{{-- Email Settings Tab --}}

<div class="settings-section">
    <div class="section-header">
        <h2><i class="fas fa-envelope"></i> Email Configuration</h2>
        <p>Configure how the application sends emails for invoices and reminders</p>
    </div>

    <div class="form-group">
        <label class="form-label" for="email-mailer">
            <i class="fas fa-cog"></i>
            Email Delivery Method
        </label>
        <select id="email-mailer" name="email_mailer" class="form-control" onchange="toggleSmtpFields()">
            <option value="default">Testing/Log Only (No Email Sent)</option>
            <option value="mail">PHP mail() Function</option>
            <option value="smtp">Custom SMTP</option>
        </select>
        <small class="form-hint">
            Choose "PHP mail() Function" for most shared hosting (Hostinger, cPanel, etc.) or "Custom SMTP" for a specific mail server.
        </small>
    </div>

    <div class="alert alert-warning" style="margin: 15px 0; background-color: #fef3c7; border-left: 4px solid #f59e0b; padding: 12px; border-radius: 4px;">
        <div style="display: flex; align-items: start;">
            <i class="fas fa-exclamation-triangle" style="color: #f59e0b; margin-right: 10px; margin-top: 2px;"></i>
            <div style="flex: 1;">
                <strong style="color: #92400e; display: block; margin-bottom: 8px;">⚠️ Common Issue: mail() Says "Sent" But Email Not Received</strong>
                <p style="margin: 0 0 8px 0; color: #92400e; font-size: 13px;">
                    PHP mail() function often returns "success" even when emails fail to deliver. This is because it only confirms the email was passed to the server, not that it was actually sent.
                </p>
                <strong style="color: #92400e; display: block; margin-top: 10px; margin-bottom: 5px;">Solutions:</strong>
                <ol style="margin: 5px 0 0 15px; color: #92400e; font-size: 13px; line-height: 1.6;">
                    <li><strong>Critical:</strong> "From Email Address" MUST match your domain (e.g., noreply@yourdomain.com, NOT gmail.com)</li>
                    <li>Use the <strong>"Send Test Email"</strong> button below to verify</li>
                    <li>Check spam/junk folder (mark as "Not Spam" if found)</li>
                    <li>Check server logs at <code style="background: #fde68a; padding: 2px 4px; border-radius: 3px;">storage/logs/laravel.log</code></li>
                    <li>If mail() doesn't work, switch to <strong>"Custom SMTP"</strong> (recommended: Gmail, SendGrid, Mailgun)</li>
                </ol>
            </div>
        </div>
    </div>

    {{-- SMTP Settings (hidden by default) --}}
    <div id="smtp-settings" style="display: none;">
        <h4 style="margin: 20px 0 15px 0; color: #1f2937; font-size: 15px;">
            <i class="fas fa-server"></i> SMTP Server Settings
        </h4>

        <div class="form-row">
            <div class="form-group" style="flex: 2;">
                <label class="form-label" for="email-smtp-host">SMTP Host</label>
                <input type="text" id="email-smtp-host" name="email_smtp_host" class="form-control" placeholder="smtp.gmail.com">
            </div>

            <div class="form-group" style="flex: 1;">
                <label class="form-label" for="email-smtp-port">Port</label>
                <input type="number" id="email-smtp-port" name="email_smtp_port" class="form-control" placeholder="587">
            </div>
        </div>

        <div class="form-group">
            <label class="form-label" for="email-smtp-encryption">Encryption</label>
            <select id="email-smtp-encryption" name="email_smtp_encryption" class="form-control">
                <option value="">None</option>
                <option value="tls">TLS</option>
                <option value="ssl">SSL</option>
            </select>
        </div>

        <div class="form-group">
            <label class="form-label" for="email-smtp-username">SMTP Username</label>
            <input type="text" id="email-smtp-username" name="email_smtp_username" class="form-control" placeholder="your-email@gmail.com">
        </div>

        <div class="form-group">
            <label class="form-label" for="email-smtp-password">SMTP Password</label>
            <input type="password" id="email-smtp-password" name="email_smtp_password" class="form-control" placeholder="Your password or app-specific password">
            <small class="form-hint">
                For Gmail, use an <a href="https://support.google.com/accounts/answer/185833" target="_blank">App Password</a>
            </small>
        </div>
    </div>

    <hr style="margin: 25px 0; border: none; border-top: 1px solid #e5e7eb;">

    <h4 style="margin: 20px 0 15px 0; color: #1f2937; font-size: 15px;">
        <i class="fas fa-user"></i> Email Sender Information
    </h4>

    <div class="form-group">
        <label class="form-label" for="email-from-address">
            <i class="fas fa-envelope"></i>
            From Email Address
        </label>
        <input type="email" id="email-from-address" name="email_from_address" class="form-control" placeholder="invoices@example.com">
        <small class="form-hint">
            <strong>Important:</strong> For mail() function, this MUST match your domain name
        </small>
    </div>

    <div class="form-group">
        <label class="form-label" for="email-from-name">
            <i class="fas fa-id-badge"></i>
            From Name
        </label>
        <input type="text" id="email-from-name" name="email_from_name" class="form-control" placeholder="My Company Inc.">
    </div>
</div>

{{-- Test Email Section --}}
<div class="settings-section">
    <div class="section-header">
        <h2><i class="fas fa-vial"></i> Test Email Configuration</h2>
        <p>Send a test email to verify your configuration is working correctly</p>
    </div>

    <div class="form-group">
        <label class="form-label" for="test-email-address">
            <i class="fas fa-envelope"></i>
            Send Test Email To
        </label>
        <input type="email" id="test-email-address" class="form-control" placeholder="your@email.com">
        <small class="form-hint">
            Enter your email address to receive a test email. This will verify your configuration is working.
        </small>
    </div>

    <button type="button" class="btn btn-secondary" id="test-email-btn" onclick="sendTestEmail()">
        <i class="fas fa-paper-plane"></i>
        Send Test Email
    </button>

    <div id="test-email-message" style="display: none; margin-top: 15px; padding: 12px; border-radius: 6px; font-size: 14px;"></div>
</div>

<script>
function toggleSmtpFields() {
    const mailerSelect = document.getElementById('email-mailer');
    const smtpSection = document.getElementById('smtp-settings');

    if (mailerSelect && smtpSection) {
        smtpSection.style.display = mailerSelect.value === 'smtp' ? 'block' : 'none';
    }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    toggleSmtpFields();
});
</script>
