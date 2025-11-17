# Stripe Payment Integration - Implementation Guide

## Overview
This document describes the Stripe payment link integration for the invoice system.

## What Has Been Done

### 1. Database Migration ✅
**File:** `database/migrations/2025_11_15_060110_add_stripe_settings_to_invoices.php`

Adds two fields to the `invoices` table:
- `stripe_payment_link` (string, 500) - Stores the Stripe payment link URL
- `stripe_payment_intent_id` (string, 100) - Stores the Stripe payment intent ID for tracking

**To run:**
```bash
php artisan migrate
```

### 2. Stripe PHP SDK ✅
**Status:** Already installed via Composer
```json
"stripe/stripe-php": "*"
```

### 3. Settings Configuration ✅
**File:** `app/Http/Controllers/SettingController.php`

Added three new settings:
- `stripe_enabled` (boolean) - Enable/disable Stripe payments
- `stripe_publishable_key` (string) - Stripe publishable key (for future frontend use)
- `stripe_secret_key` (string) - Stripe secret key (for API calls)

### 4. Stripe Payment Service ✅
**File:** `app/Services/StripePaymentService.php`

Created a service to:
- Check if Stripe is enabled
- Create payment links for invoices
- Auto-create Stripe Products and Prices
- Handle errors gracefully

**Key Methods:**
- `isEnabled()` - Check if Stripe is configured
- `createPaymentLink($invoice)` - Create a new payment link
- `getPaymentLink($invoice)` - Get existing or create new payment link

## What Needs To Be Completed

### 5. Update Invoice Controller

**File:** `app/Http/Controllers/InvoiceController.php`

Add Stripe payment link generation when sending emails:

```php
use App\Services\StripePaymentService;

// In the sendEmail() method, after validation:
$stripeService = new StripePaymentService();
if ($stripeService->isEnabled()) {
    $paymentLink = $stripeService->getPaymentLink($invoice);
    // Payment link is now stored in $invoice->stripe_payment_link
}
```

### 6. Update Email Template

**File:** `app/Services/InvoiceMailer.php` (or create `resources/views/emails/invoice.blade.php`)

Add Stripe payment link to the email body:

```html
@if($invoice->stripe_payment_link)
<div style="margin: 30px 0; text-align: center;">
    <a href="{{ $invoice->stripe_payment_link }}"
       style="display: inline-block; padding: 15px 30px; background: #635BFF; color: white; text-decoration: none; border-radius: 5px; font-weight: bold;">
        Pay with Stripe
    </a>
    <p style="margin-top: 10px; color: #666; font-size: 14px;">
        Secure payment powered by Stripe
    </p>
</div>
@endif
```

### 7. Update Invoice Model

**File:** `app/Models/Invoice.php`

Add Stripe fields to `$fillable`:

```php
protected $fillable = [
    // ... existing fields ...
    'stripe_payment_link',
    'stripe_payment_intent_id',
];
```

### 8. Add Stripe Settings UI

**File:** `resources/views/settings/tabs/payment.blade.php` (or create new tab)

Add Stripe configuration section:

```html
<div class="settings-section">
    <div class="section-header">
        <h2><i class="fab fa-stripe"></i> Stripe Payment Integration</h2>
        <p>Accept online payments via Stripe</p>
    </div>

    <div class="form-group">
        <label class="form-check">
            <input type="checkbox" id="stripe-enabled" name="stripe_enabled" class="form-check-input">
            <span class="form-check-label">Enable Stripe Payments</span>
        </label>
        <small class="form-text text-muted">
            When enabled, invoices will include a Stripe payment link
        </small>
    </div>

    <div id="stripe-settings" style="display: none;">
        <div class="form-group">
            <label for="stripe-publishable-key">Stripe Publishable Key</label>
            <input type="text"
                   id="stripe-publishable-key"
                   name="stripe_publishable_key"
                   class="form-control"
                   placeholder="pk_live_...">
            <small class="form-text text-muted">
                Starts with "pk_live_" or "pk_test_" - <a href="https://dashboard.stripe.com/apikeys" target="_blank">Get your keys</a>
            </small>
        </div>

        <div class="form-group">
            <label for="stripe-secret-key">Stripe Secret Key</label>
            <input type="password"
                   id="stripe-secret-key"
                   name="stripe_secret_key"
                   class="form-control"
                   placeholder="sk_live_...">
            <small class="form-text text-muted">
                Starts with "sk_live_" or "sk_test_" - Keep this secret!
            </small>
        </div>

        <div class="alert alert-info">
            <i class="fas fa-info-circle"></i>
            <strong>Important:</strong> Use test keys (pk_test_/sk_test_) for testing.
            Switch to live keys (pk_live_/sk_live_) for production.
        </div>
    </div>
</div>

<script>
document.getElementById('stripe-enabled').addEventListener('change', function() {
    document.getElementById('stripe-settings').style.display =
        this.checked ? 'block' : 'none';
});
</script>
```

### 9. Update Settings JavaScript

**File:** `public/js/settings/index.js`

Add Stripe fields to form collection:

```javascript
function collectFormData() {
    return {
        // ... existing fields ...
        stripe_enabled: document.getElementById('stripe-enabled')?.checked || false,
        stripe_publishable_key: getValue('stripe-publishable-key'),
        stripe_secret_key: getValue('stripe-secret-key'),
    };
}

function loadSettings() {
    // ... existing code ...

    // Stripe settings
    const stripeEnabled = data.stripe_enabled === true || data.stripe_enabled === '1';
    document.getElementById('stripe-enabled').checked = stripeEnabled;
    setValue('stripe-publishable-key', data.stripe_publishable_key);
    setValue('stripe-secret-key', data.stripe_secret_key);

    // Show/hide Stripe settings
    document.getElementById('stripe-settings').style.display =
        stripeEnabled ? 'block' : 'none';
}
```

### 10. Update InvoiceController sendEmail Method

**File:** `app/Http/Controllers/InvoiceController.php`

Add Stripe link generation before sending email:

```php
public function sendEmail(Request $request, $id)
{
    // ... existing validation ...

    $invoice = Invoice::with('project', 'items')->findOrFail($id);

    // Generate Stripe payment link if enabled
    $stripeService = new StripePaymentService();
    if ($stripeService->isEnabled()) {
        try {
            $paymentLink = $stripeService->getPaymentLink($invoice);
            Log::info('Stripe payment link generated', [
                'invoice_id' => $invoice->id,
                'payment_link' => $paymentLink
            ]);
        } catch (\Exception $e) {
            Log::warning('Failed to create Stripe payment link', [
                'invoice_id' => $invoice->id,
                'error' => $e->getMessage()
            ]);
            // Continue sending email even if Stripe fails
        }
    }

    // ... rest of email sending code ...
}
```

## Complete Implementation Steps

1. **Run the migration:**
   ```bash
   php artisan migrate
   ```

2. **Add Stripe fields to Invoice model** (`app/Models/Invoice.php`)

3. **Update Invoice Controller** to generate payment links

4. **Update email template** to include payment button

5. **Add Settings UI** for Stripe configuration

6. **Update Settings JavaScript** to handle Stripe fields

7. **Test the integration:**
   - Get test API keys from https://dashboard.stripe.com/test/apikeys
   - Enable Stripe in settings
   - Enter test keys (pk_test_... and sk_test_...)
   - Create and send an invoice
   - Check that email includes "Pay with Stripe" button
   - Click button to test payment flow

## Stripe Dashboard Setup

1. Create a Stripe account at https://stripe.com
2. Go to Developers > API keys
3. Copy your publishable key (pk_test_...)
4. Copy your secret key (sk_test_...)
5. For production, use live keys instead of test keys

## Security Notes

- **Never commit secret keys to version control**
- Store keys in database via settings page
- Secret keys should only be used on server-side
- Use test keys for development
- Validate all webhook signatures (if implementing webhooks later)

## Future Enhancements

- [ ] Webhook handling for payment confirmation
- [ ] Auto-mark invoices as paid when Stripe payment succeeds
- [ ] Support for other currencies besides CAD
- [ ] Payment status tracking
- [ ] Refund functionality
- [ ] Stripe Customer creation for repeat clients

## Testing Checklist

- [ ] Settings page shows Stripe options
- [ ] Can enable/disable Stripe
- [ ] Can save Stripe keys
- [ ] Payment link generates when sending invoice
- [ ] Email includes "Pay with Stripe" button
- [ ] Clicking button opens Stripe payment page
- [ ] Payment page shows correct invoice amount
- [ ] Test payment with card 4242 4242 4242 4242
- [ ] Stripe dashboard shows payment
