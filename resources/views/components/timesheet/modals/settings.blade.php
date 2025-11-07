{{-- Settings Modal Component --}}
{{-- Manage application-wide configuration such as invoice branding --}}
<div class="modal" id="settings-modal">
    <div class="modal-content">
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

                <h4 style="margin: 20px 0 15px 0; color: #1f2937; font-size: 16px;">
                    <i class="fas fa-money-check-alt"></i> Payment Instructions
                </h4>

                <div class="form-group">
                    <label class="form-label" for="payment-etransfer-email">
                        <i class="fas fa-envelope"></i>
                        E-Transfer Email
                    </label>
                    <input type="email" id="payment-etransfer-email" class="form-control"
                           placeholder="payments@yourcompany.com">
                    <small class="form-hint">
                        Email address where clients should send Interac e-Transfers.
                    </small>
                </div>

                <div class="form-group">
                    <label class="form-label" for="payment-bank-info">
                        <i class="fas fa-university"></i>
                        Direct Deposit / Bank Info
                    </label>
                    <textarea id="payment-bank-info" class="form-control" rows="3"
                              placeholder="Bank Name&#10;Transit: 12345&#10;Institution: 001&#10;Account: 1234567"></textarea>
                    <small class="form-hint">
                        Bank details for direct deposits (optional).
                    </small>
                </div>

                <div class="form-group">
                    <label class="form-label" for="payment-instructions">
                        <i class="fas fa-info-circle"></i>
                        Additional Payment Instructions
                    </label>
                    <textarea id="payment-instructions" class="form-control" rows="4"
                              placeholder="Other payment methods or instructions..."></textarea>
                    <small class="form-hint">
                        Any other payment details you want to include in invoice emails.
                    </small>
                </div>

                <hr class="settings-divider">

                <h4 style="margin: 20px 0 15px 0; color: #1f2937; font-size: 16px;">
                    <i class="fas fa-clock"></i> Scheduled Invoice Reminders
                </h4>

                <div class="alert alert-info" style="margin-bottom: 20px; background-color: #eff6ff; border-left: 4px solid #3b82f6; padding: 15px; border-radius: 4px;">
                    <div style="display: flex; align-items: start;">
                        <i class="fas fa-info-circle" style="color: #3b82f6; margin-right: 12px; margin-top: 2px; font-size: 18px;"></i>
                        <div style="flex: 1;">
                            <strong style="color: #1f2937; display: block; margin-bottom: 8px;">Automatic Payment Reminders</strong>
                            <p style="margin: 0 0 10px 0; color: #4b5563; font-size: 13px; line-height: 1.5;">
                                Set up a cron job to automatically send payment reminders for unpaid invoices that are due within 3 days or overdue. <strong>Runs daily at 9:00 AM.</strong>
                            </p>

                            @php
                                $cronToken = substr(md5(config('app.key')), 0, 16);
                                $cronUrl = url("/cron/run/{$cronToken}");
                                $testUrl = url("/cron/test-reminders/{$cronToken}");
                            @endphp

                            {{-- Test URLs First --}}
                            <div style="background: #d1fae5; padding: 12px; border-radius: 4px; border: 1px solid #10b981; margin-top: 10px;">
                                <strong style="color: #065f46; font-size: 14px; display: block; margin-bottom: 8px;">
                                    <i class="fas fa-vial" style="color: #10b981;"></i> 🧪 Test URLs (Click to Test Now!)
                                </strong>
                                <p style="margin: 8px 0; color: #065f46; font-size: 13px;">
                                    <strong>1. Test Invoice Reminders:</strong><br>
                                    Click this link to test the reminder system right now:
                                </p>
                                <a href="{{ $testUrl }}" target="_blank" style="display: block; background: #10b981; color: white; padding: 10px; border-radius: 4px; text-align: center; text-decoration: none; font-weight: 500; margin: 8px 0;">
                                    <i class="fas fa-play-circle"></i> Test Invoice Reminders Now
                                </a>
                                <code style="display: block; background: #f0fdf4; padding: 8px; border-radius: 4px; font-family: monospace; font-size: 10px; color: #065f46; word-break: break-all; border: 1px solid #86efac; margin-top: 5px;">
                                    {{ $testUrl }}
                                </code>
                                <p style="margin: 12px 0 8px 0; color: #065f46; font-size: 13px;">
                                    <strong>2. Test Full Scheduler:</strong><br>
                                    This runs all scheduled tasks (backups + reminders):
                                </p>
                                <a href="{{ $cronUrl }}" target="_blank" style="display: block; background: #059669; color: white; padding: 10px; border-radius: 4px; text-align: center; text-decoration: none; font-weight: 500; margin: 8px 0;">
                                    <i class="fas fa-play-circle"></i> Test Full Scheduler Now
                                </a>
                                <code style="display: block; background: #f0fdf4; padding: 8px; border-radius: 4px; font-family: monospace; font-size: 10px; color: #065f46; word-break: break-all; border: 1px solid #86efac; margin-top: 5px;">
                                    {{ $cronUrl }}
                                </code>
                                <p style="margin: 10px 0 0 0; color: #065f46; font-size: 12px;">
                                    ✓ You should see JSON response with "status": "success"
                                </p>
                            </div>

                            {{-- Hostinger Instructions --}}
                            <div style="background: #ffffff; padding: 12px; border-radius: 4px; border: 1px solid #d1d5db; margin-top: 10px;">
                                <strong style="color: #374151; font-size: 14px; display: block; margin-bottom: 8px;">
                                    <i class="fas fa-server" style="color: #7c3aed;"></i> Hostinger (Recommended)
                                </strong>
                                <ol style="margin: 8px 0; padding-left: 20px; color: #4b5563; font-size: 13px; line-height: 1.6;">
                                    <li style="margin-bottom: 8px;">Log into your <strong>Hostinger hPanel</strong></li>
                                    <li style="margin-bottom: 8px;">Go to <strong>"Advanced"</strong> → <strong>"Cron Jobs"</strong></li>
                                    <li style="margin-bottom: 8px;">Click <strong>"Create Cron Job"</strong></li>
                                    <li style="margin-bottom: 8px;">Set schedule: <strong>Every minute (* * * * *)</strong> or Custom</li>
                                    <li style="margin-bottom: 8px;">Select <strong>"Run via URL"</strong></li>
                                    <li style="margin-bottom: 8px;"><strong>Paste this URL:</strong></li>
                                </ol>
                                <code style="display: block; background: #f9fafb; padding: 10px; border-radius: 4px; font-family: monospace; font-size: 11px; color: #1f2937; word-break: break-all; border: 1px solid #e5e7eb; margin-top: 5px;">
                                    {{ $cronUrl }}
                                </code>
                                <p style="margin: 8px 0 0 0; color: #059669; font-size: 12px;">
                                    <i class="fas fa-check-circle"></i> That's it! Hostinger will call this URL every minute, and Laravel handles the rest.
                                </p>
                            </div>

                            {{-- cPanel Instructions --}}
                            <div style="background: #ffffff; padding: 12px; border-radius: 4px; border: 1px solid #d1d5db; margin-top: 10px;">
                                <strong style="color: #374151; font-size: 14px; display: block; margin-bottom: 8px;">
                                    <i class="fas fa-server" style="color: #3b82f6;"></i> cPanel / Other Shared Hosting
                                </strong>
                                <p style="margin: 8px 0; color: #4b5563; font-size: 13px;"><strong>Option 1: Use URL (Easier):</strong></p>
                                <ol style="margin: 8px 0; padding-left: 20px; color: #4b5563; font-size: 13px; line-height: 1.6;">
                                    <li style="margin-bottom: 8px;">Log into <strong>cPanel</strong></li>
                                    <li style="margin-bottom: 8px;">Find <strong>"Cron Jobs"</strong></li>
                                    <li style="margin-bottom: 8px;">Add cron job with: <code style="background: #f3f4f6; padding: 2px 6px; border-radius: 3px;">* * * * *</code></li>
                                    <li style="margin-bottom: 8px;">Use <strong>wget</strong> or <strong>curl</strong> command:</li>
                                </ol>
                                <code style="display: block; background: #f9fafb; padding: 10px; border-radius: 4px; font-family: monospace; font-size: 11px; color: #1f2937; word-break: break-all; border: 1px solid #e5e7eb; margin-top: 5px;">
                                    wget -q -O - "{{ $cronUrl }}" > /dev/null 2>&1
                                </code>
                                <p style="margin: 8px 0; color: #4b5563; font-size: 13px;"><strong>Or use curl:</strong></p>
                                <code style="display: block; background: #f9fafb; padding: 10px; border-radius: 4px; font-family: monospace; font-size: 11px; color: #1f2937; word-break: break-all; border: 1px solid #e5e7eb; margin-top: 5px;">
                                    curl -s "{{ $cronUrl }}" > /dev/null 2>&1
                                </code>
                                <p style="margin: 8px 0 0 0; color: #059669; font-size: 12px;">
                                    <i class="fas fa-check-circle"></i> This calls your URL every minute automatically.
                                </p>
                            </div>

                            {{-- Plesk Instructions --}}
                            <div style="background: #ffffff; padding: 12px; border-radius: 4px; border: 1px solid #d1d5db; margin-top: 10px;">
                                <strong style="color: #374151; font-size: 14px; display: block; margin-bottom: 8px;">
                                    <i class="fas fa-server" style="color: #3b82f6;"></i> Plesk
                                </strong>
                                <ol style="margin: 8px 0; padding-left: 20px; color: #4b5563; font-size: 13px; line-height: 1.6;">
                                    <li style="margin-bottom: 8px;">Log into <strong>Plesk</strong></li>
                                    <li style="margin-bottom: 8px;">Go to <strong>"Websites & Domains"</strong> → Your domain</li>
                                    <li style="margin-bottom: 8px;">Click <strong>"Scheduled Tasks"</strong> or <strong>"Cron Jobs"</strong></li>
                                    <li style="margin-bottom: 8px;">Click <strong>"Add Task"</strong></li>
                                    <li style="margin-bottom: 8px;">Set schedule: <strong>Every minute (* * * * *)</strong></li>
                                    <li style="margin-bottom: 8px;"><strong>Command:</strong></li>
                                </ol>
                                <code style="display: block; background: #f9fafb; padding: 10px; border-radius: 4px; font-family: monospace; font-size: 11px; color: #1f2937; word-break: break-all; border: 1px solid #e5e7eb; margin-top: 5px;">
                                    cd /var/www/vhosts/yourdomain.com/httpdocs && php artisan schedule:run >> /dev/null 2>&1
                                </code>
                            </div>

                            {{-- VPS/Dedicated Server Instructions --}}
                            <div style="background: #ffffff; padding: 12px; border-radius: 4px; border: 1px solid #d1d5db; margin-top: 10px;">
                                <strong style="color: #374151; font-size: 14px; display: block; margin-bottom: 8px;">
                                    <i class="fas fa-server" style="color: #3b82f6;"></i> VPS / Dedicated Server (SSH Access)
                                </strong>
                                <ol style="margin: 8px 0; padding-left: 20px; color: #4b5563; font-size: 13px; line-height: 1.6;">
                                    <li style="margin-bottom: 8px;">SSH into your server</li>
                                    <li style="margin-bottom: 8px;">Run: <code style="background: #f3f4f6; padding: 2px 6px; border-radius: 3px;">crontab -e</code></li>
                                    <li style="margin-bottom: 8px;">Add this line at the bottom:</li>
                                </ol>
                                <code style="display: block; background: #f9fafb; padding: 10px; border-radius: 4px; font-family: monospace; font-size: 11px; color: #1f2937; word-break: break-all; border: 1px solid #e5e7eb; margin-top: 5px;">
                                    * * * * * cd {{ base_path() }} && php artisan schedule:run >> /dev/null 2>&1
                                </code>
                                <p style="margin: 8px 0 0 0; color: #6b7280; font-size: 12px;">
                                    Save and exit. The cron job will start automatically.
                                </p>
                            </div>


                            {{-- Important Notes --}}
                            <div style="background: #fef3c7; padding: 12px; border-radius: 4px; border: 1px solid #fbbf24; margin-top: 10px;">
                                <p style="margin: 0; color: #92400e; font-size: 12px; line-height: 1.6;">
                                    <i class="fas fa-lightbulb" style="color: #f59e0b;"></i>
                                    <strong>Important Notes for Hostinger Users:</strong>
                                </p>
                                <ul style="margin: 8px 0 0 20px; color: #92400e; font-size: 12px; line-height: 1.6;">
                                    <li><strong>First, test the URLs above</strong> by clicking the green buttons - you should see JSON with "status": "success"</li>
                                    <li>In Hostinger cron jobs, select <strong>"Run via URL"</strong> method (not command line)</li>
                                    <li>The cron runs <strong>every minute</strong>, but Laravel only sends reminders at <strong>9:00 AM</strong></li>
                                    <li>Your cron URL includes a security token - <strong>keep it private!</strong></li>
                                    <li>The URL works on localhost too - test it now before deploying to Hostinger</li>
                                    <li>Check Laravel logs at <code style="background: #fde68a; padding: 2px 4px; border-radius: 3px;">storage/logs/laravel.log</code> for any errors</li>
                                </ul>
                            </div>

                            <p style="margin: 12px 0 0 0; color: #059669; font-size: 13px; font-weight: 500;">
                                <i class="fas fa-check-circle"></i>
                                Once set up, the system automatically checks for unpaid invoices (status: sent) due within 3 days or overdue.
                            </p>
                        </div>
                    </div>
                </div>

                <hr class="settings-divider">

                <h4 style="margin: 20px 0 15px 0; color: #1f2937; font-size: 16px;">
                    <i class="fas fa-envelope"></i> Email Configuration
                </h4>

                <div class="form-group">
                    <label class="form-label" for="email-mailer">
                        <i class="fas fa-cog"></i>
                        Email Delivery Method
                    </label>
                    <select id="email-mailer" class="form-control">
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

                <hr class="settings-divider">

                <h4 style="margin: 20px 0 15px 0; color: #1f2937; font-size: 16px;">
                    <i class="fas fa-vial"></i> Test Email Configuration
                </h4>

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

                <button type="button" class="btn btn-secondary" id="test-email-btn" onclick="sendTestEmail()" style="width: 100%; margin-bottom: 20px;">
                    <i class="fas fa-paper-plane"></i>
                    Send Test Email
                </button>

                <div id="settings-loading" class="settings-loading">
                    <i class="fas fa-spinner fa-spin"></i>
                    <span>Working on it...</span>
                </div>

                <!-- Success/Error Message Alert -->
                <div id="settings-message" style="display: none; margin: 25px 0 20px 0; padding: 15px 20px; border-radius: 8px; font-size: 14px; font-weight: 500; align-items: center; gap: 12px;">
                    <i id="settings-message-icon" class="fas" style="font-size: 18px;"></i>
                    <span id="settings-message-text" style="flex: 1;"></span>
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
