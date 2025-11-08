{{-- General Settings Tab - Invoice & Company Information --}}

<div class="settings-section">
    <div class="section-header">
        <h2><i class="fas fa-building"></i> Company & Invoice Information</h2>
        <p>Customize the details that appear on generated invoices</p>
    </div>

    <div class="form-group">
        <label class="form-label" for="invoice-company-name">
            <i class="fas fa-building"></i>
            Invoice / Company Name *
        </label>
        <input type="text" id="invoice-company-name" name="invoice_company_name" class="form-control"
               placeholder="Enter the company or freelancer name to show on invoices">
        <small class="form-hint">
            This name will appear at the top of all invoices
        </small>
    </div>

    <div class="form-group">
        <label class="form-label" for="invoice-company-address">
            <i class="fas fa-map-marked-alt"></i>
            Company Address
        </label>
        <textarea id="invoice-company-address" name="invoice_company_address" class="form-control" rows="3"
                  placeholder="Street, City, Province/State, Postal Code"></textarea>
        <small class="form-hint">
            Line breaks will automatically format the address on generated PDFs
        </small>
    </div>

    <div class="form-group">
        <label class="form-label" for="invoice-tax-number">
            <i class="fas fa-receipt"></i>
            GST/HST/Tax Number
        </label>
        <input type="text" id="invoice-tax-number" name="invoice_tax_number" class="form-control"
               placeholder="e.g., 123456789RT0001">
        <small class="form-hint">
            Your tax registration number (optional)
        </small>
    </div>
</div>
