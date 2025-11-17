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

<div class="settings-section">
    <div class="section-header">
        <h2><i class="fas fa-tools"></i> Maintenance Utilities</h2>
        <p>Clear Laravel cache, config, and routes after deployments or .env changes</p>
    </div>

    <p style="color: var(--text-secondary); font-size: 14px; margin-bottom: 14px;">
        Use this tool only when something appears out of sync after updating code or environment variables. It runs
        <code>config:clear</code>, <code>cache:clear</code>, <code>route:clear</code>, <code>view:clear</code>, and <code>optimize:clear</code> together.
    </p>

    <div style="display: flex; flex-wrap: wrap; align-items: center; gap: 12px;">
        <button type="button" id="clear-cache-btn" class="btn btn-warning" style="display: inline-flex; align-items: center; gap: 8px;">
            <i class="fas fa-broom"></i>
            Clear Cache / Config / Routes
        </button>
        <div id="clear-cache-status" style="display: none; font-size: 14px;"></div>
    </div>

    <small class="form-hint" style="margin-top: 10px;">
        Tip: Run this after editing routes, config files, or environment variables on the server.
    </small>
</div>
