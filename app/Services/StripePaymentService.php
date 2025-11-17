<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\Setting;
use Stripe\Stripe;
use Stripe\PaymentLink;
use Stripe\Price;
use Stripe\Product;
use Exception;

class StripePaymentService
{
    protected ?string $secretKey;
    protected bool $enabled;
    protected const STRIPE_API_VERSION = '2024-06-20';

    public function __construct()
    {
        $this->enabled = Setting::getValue('stripe_enabled', false) === '1';
        $encryptedKey = Setting::getValue('stripe_secret_key');

        // SECURITY: Decrypt the secret key
        if ($encryptedKey) {
            try {
                $this->secretKey = decrypt($encryptedKey);
            } catch (Exception $e) {
                // If decryption fails, try using it as-is (legacy compatibility)
                $this->secretKey = $encryptedKey;
                \Log::warning('Failed to decrypt Stripe secret key, using as-is', [
                    'error' => $e->getMessage()
                ]);
            }
        } else {
            $this->secretKey = null;
        }

        if ($this->enabled && $this->secretKey) {
            Stripe::setApiKey($this->secretKey);
            // SECURITY: Pin Stripe API version to prevent breaking changes
            Stripe::setApiVersion(self::STRIPE_API_VERSION);
        }
    }

    /**
     * Check if Stripe is enabled and configured
     */
    public function isEnabled(): bool
    {
        return $this->enabled && !empty($this->secretKey);
    }

    /**
     * Create a payment link for an invoice
     *
     * @param Invoice $invoice
     * @return string|null Payment link URL
     */
    public function createPaymentLink(Invoice $invoice): ?string
    {
        if (!$this->isEnabled()) {
            return null;
        }

        try {
            // SECURITY: Use idempotency key to prevent duplicate resources
            $idempotencyKey = $this->buildIdempotencyKey($invoice);

            // OPTIMIZATION: Use a single reusable product instead of creating one per invoice
            // Try to get existing product or create one
            $product = $this->getOrCreateGenericProduct();

            // Create a price for the product
            // Convert amount to cents (Stripe uses smallest currency unit)
            $amountCents = (int) round($this->resolveStripeChargeAmount($invoice) * 100);

            $price = Price::create([
                'product' => $product->id,
                'unit_amount' => $amountCents,
                'currency' => 'cad', // Canadian dollars
            ], [
                'idempotency_key' => $idempotencyKey . '_price'
            ]);

            // Create a payment link
            $paymentLink = PaymentLink::create([
                'line_items' => [[
                    'price' => $price->id,
                    'quantity' => 1,
                ]],
                'after_completion' => [
                    'type' => 'redirect',
                    'redirect' => [
                        // SECURITY: Use dedicated confirmation route for post-payment messaging
                        'url' => route('payment.stripe.result', [
                            'status' => 'success',
                            'invoice' => $invoice->id,
                        ], true),
                    ],
                ],
                'metadata' => [
                    'invoice_id' => (string) $invoice->id,
                    'invoice_number' => $invoice->invoice_number,
                    'client_name' => $invoice->client_name,
                ],
                'invoice_creation' => [
                    'enabled' => true,
                    'invoice_data' => [
                        'description' => "Invoice #{$invoice->invoice_number}",
                        'metadata' => [
                            'invoice_id' => (string) $invoice->id,
                            'invoice_number' => $invoice->invoice_number,
                        ],
                    ],
                ],
            ], [
                'idempotency_key' => $idempotencyKey . '_link'
            ]);

            // SECURITY: Set stripe fields directly to bypass mass assignment protection
            $invoice->stripe_payment_link = $paymentLink->url;
            $invoice->save();

            return $paymentLink->url;
        } catch (Exception $e) {
            // SECURITY: Log error but don't expose internal details to user
            \Log::error('Stripe payment link creation failed', [
                'invoice_id' => $invoice->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return null;
        }
    }

    /**
     * Determine the amount that should be charged through Stripe.
     * This keeps invoice totals (for non-Stripe payments) separate from
     * any processing fees that only apply when using Stripe.
     */
    protected function resolveStripeChargeAmount(Invoice $invoice): float
    {
        // Ensure we have up-to-date invoice figures
        $baseAmount = (float) ($invoice->subtotal ?? 0) + (float) ($invoice->tax_amount ?? 0);
        if ($baseAmount <= 0) {
            $invoice->calculateTotals();
            $invoice->refresh();
            $baseAmount = (float) ($invoice->subtotal ?? 0) + (float) ($invoice->tax_amount ?? 0);
        }

        if (!$invoice->stripe_fees_included) {
            return $baseAmount;
        }

        // Always recompute so the Stripe checkout reflects the latest totals and fee formula
        $invoice->calculateStripeFees();
        $invoice->save();

        $updatedBaseAmount = (float) ($invoice->subtotal ?? 0) + (float) ($invoice->tax_amount ?? 0);

        return $updatedBaseAmount + (float) $invoice->stripe_fee_amount;
    }

    /**
     * Get or create a generic product for all invoices
     * This prevents cluttering the Stripe dashboard with one product per invoice
     */
    protected function getOrCreateGenericProduct(): Product
    {
        // Check if we have a stored product ID
        $productId = Setting::getValue('stripe_product_id');

        if ($productId) {
            try {
                return Product::retrieve($productId);
            } catch (Exception $e) {
                \Log::warning('Failed to retrieve stored Stripe product, creating new one', [
                    'product_id' => $productId,
                    'error' => $e->getMessage()
                ]);
            }
        }

        // Create a new generic product
        $product = Product::create([
            'name' => 'Invoice Payment',
            'description' => 'Payment for freelancer invoice',
        ], [
            'idempotency_key' => 'generic_product_v1'
        ]);

        // Store the product ID for reuse
        Setting::setValue('stripe_product_id', $product->id);

        return $product;
    }

    /**
     * Get or create a payment link for an invoice
     *
     * @param Invoice $invoice
     * @return string|null
     */
    public function getPaymentLink(Invoice $invoice): ?string
    {
        // If invoice already has a payment link, return it
        if ($invoice->stripe_payment_link) {
            return $invoice->stripe_payment_link;
        }

        // Create a new payment link
        return $this->createPaymentLink($invoice);
    }

    /**
     * Build an idempotency key that evolves when invoice totals change.
     */
    protected function buildIdempotencyKey(Invoice $invoice, string $suffix = 'v1'): string
    {
        $signatureParts = [
            $invoice->id,
            number_format((float) ($invoice->subtotal ?? 0), 2, '.', ''),
            number_format((float) ($invoice->tax_amount ?? 0), 2, '.', ''),
            number_format((float) ($invoice->stripe_fee_amount ?? 0), 2, '.', ''),
            $invoice->updated_at?->getTimestamp() ?? $invoice->created_at?->getTimestamp() ?? time(),
        ];

        $hash = substr(hash('sha256', implode('|', $signatureParts)), 0, 16);

        return "invoice_{$invoice->id}_{$suffix}_{$hash}";
    }

    /**
     * Get the application URL with proper protocol
     * SECURITY: Always use HTTPS in production, respect APP_URL config
     *
     * @return string
     */
    protected function getAppUrl(): string
    {
        $url = config('app.url', url('/'));

        // Force HTTPS in production environment
        if (config('app.env') === 'production') {
            $url = preg_replace('/^http:/', 'https:', $url);
        }

        // Remove trailing slash
        return rtrim($url, '/');
    }
}
