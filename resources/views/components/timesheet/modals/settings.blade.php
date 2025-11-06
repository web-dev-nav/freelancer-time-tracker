{{-- Settings Modal Component --}}
{{-- Manage application-wide configuration such as invoice branding --}}
<div class="modal" id="settings-modal">
    <div class="modal-content" style="max-width: 720px; width: 95%;">
        <div class="modal-header">
            <h3><i class="fas fa-cog"></i> Application Settings</h3>
            <button class="modal-close" onclick="hideSettingsModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <div class="modal-body">
            <p class="settings-intro">
                Customize the details that appear on generated invoices and emails. These values apply to every client.
            </p>

            <form id="settings-form" class="settings-form">
                <div class="form-group">
                    <label class="form-label" for="invoice-company-name">
                        <i class="fas fa-building"></i>
                        Invoice / Company Name
                    </label>
                    <input type="text" id="invoice-company-name" class="form-control"
                           placeholder="Enter the company or freelancer name to show on invoices">
                </div>

                <div class="form-group">
                    <label class="form-label" for="invoice-company-address">
                        <i class="fas fa-map-marked-alt"></i>
                        Company Address
                    </label>
                    <textarea id="invoice-company-address" class="form-control" rows="3"
                              placeholder="Street, City, Province/State, Postal Code"></textarea>
                    <small class="form-hint">
                        Line breaks will automatically format the address on generated PDFs.
                    </small>
                </div>

                <div class="form-group">
                    <label class="form-label" for="invoice-tax-number">
                        <i class="fas fa-receipt"></i>
                        GST/HST Number
                    </label>
                    <input type="text" id="invoice-tax-number" class="form-control"
                           placeholder="e.g., 123456789RT0001">
                </div>

                <hr class="settings-divider">

                <div class="form-group">
                    <label class="form-label" for="email-mailer">
                        <i class="fas fa-envelope"></i>
                        Email Delivery Method
                    </label>
                    <select id="email-mailer" class="form-control">
                        <option value="default">Use Application Defaults</option>
                        <option value="smtp">Custom SMTP</option>
                    </select>
                    <small class="form-hint">
                        Choose “Custom SMTP” to send invoices through a specific mail server.
                    </small>
                </div>

                <div id="smtp-settings" class="smtp-settings-grid">
                    <div class="form-group">
                        <label class="form-label" for="email-smtp-host">SMTP Host</label>
                        <input type="text" id="email-smtp-host" class="form-control" placeholder="smtp.example.com">
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="email-smtp-port">SMTP Port</label>
                        <input type="number" id="email-smtp-port" class="form-control" placeholder="587" min="1" max="65535">
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="email-smtp-username">SMTP Username</label>
                        <input type="text" id="email-smtp-username" class="form-control" placeholder="user@example.com">
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="email-smtp-password">SMTP Password</label>
                        <input type="password" id="email-smtp-password" class="form-control" placeholder="••••••••">
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="email-smtp-encryption">Encryption</label>
                        <select id="email-smtp-encryption" class="form-control">
                            <option value="">None</option>
                            <option value="ssl">SSL</option>
                            <option value="tls">TLS</option>
                            <option value="starttls">STARTTLS</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="email-from-address">
                        <i class="fas fa-paper-plane"></i>
                        From Email Address
                    </label>
                    <input type="email" id="email-from-address" class="form-control" placeholder="invoices@example.com">
                </div>

                <div class="form-group">
                    <label class="form-label" for="email-from-name">
                        <i class="fas fa-id-badge"></i>
                        From Name
                    </label>
                    <input type="text" id="email-from-name" class="form-control" placeholder="My Company Inc.">
                </div>

                <div id="settings-loading" class="settings-loading">
                    <i class="fas fa-spinner fa-spin"></i>
                    <span>Working on it...</span>
                </div>

                <div class="settings-actions">
                    <button type="button" class="btn btn-secondary" onclick="hideSettingsModal()">
                        Cancel
                    </button>
                    <button type="submit" class="btn btn-primary" id="settings-save-btn">
                        <i class="fas fa-save"></i>
                        Save Settings
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
