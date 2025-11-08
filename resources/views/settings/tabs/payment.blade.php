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
