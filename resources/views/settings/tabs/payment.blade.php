{{-- Payment Instructions Tab --}}

<div class="settings-section">
    <div class="section-header">
        <h2><i class="fas fa-money-check-alt"></i> Payment Instructions</h2>
        <p>Configure payment methods that will be shown on invoices</p>
    </div>

    <div class="form-group">
        <label class="form-label" for="payment-etransfer-email">
            <i class="fas fa-envelope"></i>
            E-Transfer Email
        </label>
        <input type="email" id="payment-etransfer-email" name="payment_etransfer_email" class="form-control"
               placeholder="payments@yourcompany.com">
        <small class="form-hint">
            Email address where clients should send Interac e-Transfers
        </small>
    </div>

    <div class="form-group">
        <label class="form-label" for="payment-bank-info">
            <i class="fas fa-university"></i>
            Direct Deposit / Bank Info
        </label>
        <textarea id="payment-bank-info" name="payment_bank_info" class="form-control" rows="4"
                  placeholder="Bank Name&#10;Transit: 12345&#10;Institution: 001&#10;Account: 1234567"></textarea>
        <small class="form-hint">
            Bank details for direct deposits (optional)
        </small>
    </div>

    <div class="form-group">
        <label class="form-label" for="payment-instructions">
            <i class="fas fa-info-circle"></i>
            Additional Payment Instructions
        </label>
        <textarea id="payment-instructions" name="payment_instructions" class="form-control" rows="5"
                  placeholder="Other payment methods or instructions..."></textarea>
        <small class="form-hint">
            Any other payment details you want to include in invoice emails
        </small>
    </div>
</div>

{{-- Stripe Payment Integration --}}
<div class="settings-section">
    <div class="section-header">
        <h2><i class="fab fa-stripe"></i> Stripe Payment Integration</h2>
        <p>Accept online credit card payments via Stripe</p>
    </div>

    <div class="form-group">
        <label class="form-check">
            {{-- SECURITY: Removed inline onchange handler for CSP compliance - handled by external JS --}}
            <input type="checkbox" id="stripe-enabled" name="stripe_enabled" class="form-check-input">
            <span class="form-check-label">
                <strong>Enable Stripe Payments</strong>
            </span>
        </label>
        <small class="form-hint">
            When enabled, invoices will include a "Pay with Stripe" button
        </small>
    </div>

    <div id="stripe-fields" style="display: none;">
        <div class="alert alert-info">
            <i class="fas fa-info-circle"></i>
            <strong>Get your API keys:</strong>
            <a href="https://dashboard.stripe.com/test/apikeys" target="_blank">Test Mode</a> |
            <a href="https://dashboard.stripe.com/apikeys" target="_blank">Live Mode</a>
        </div>

        <div class="form-group">
            <label class="form-label" for="stripe-publishable-key">
                <i class="fas fa-key"></i>
                Stripe Publishable Key
            </label>
            <input type="text"
                   id="stripe-publishable-key"
                   name="stripe_publishable_key"
                   class="form-control font-monospace"
                   placeholder="pk_test_... or pk_live_...">
            <small class="form-hint">
                Starts with "pk_test_" (test mode) or "pk_live_" (live mode)
            </small>
        </div>

        <div class="form-group">
            <label class="form-label" for="stripe-secret-key">
                <i class="fas fa-lock"></i>
                Stripe Secret Key
            </label>
            <input type="password"
                   id="stripe-secret-key"
                   name="stripe_secret_key"
                   class="form-control font-monospace"
                   placeholder="sk_test_... or sk_live_...">
            <small class="form-hint">
                Starts with "sk_test_" (test mode) or "sk_live_" (live mode). Keep this secret and leave the field blank to keep your existing key.
            </small>
            <div id="stripe-secret-actions" style="display: none; margin-top: 6px;">
                <button type="button"
                        class="btn btn-link btn-sm"
                        onclick="clearStripeSecret()"
                        style="padding: 0; text-decoration: none;">
                    <i class="fas fa-eraser"></i> Remove saved key
                </button>
            </div>
        </div>

        <div class="alert alert-warning">
            <i class="fas fa-exclamation-triangle"></i>
            <strong>Important:</strong>
            <ul class="mb-0 mt-2">
                <li>Use <strong>test keys</strong> (pk_test_/sk_test_) while testing</li>
                <li>Switch to <strong>live keys</strong> (pk_live_/sk_live_) for production</li>
                <li>Test payments with card: <code>4242 4242 4242 4242</code></li>
            </ul>
        </div>
    </div>
</div>

{{-- SECURITY: Inline script removed - function moved to /public/js/settings/index.js for CSP compliance --}}
