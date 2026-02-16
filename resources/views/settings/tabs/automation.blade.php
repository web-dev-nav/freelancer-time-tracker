{{-- Automation Tab --}}

<div class="settings-section" style="background:#f8fafc;border:2px solid #0ea5e9;">
    <div class="section-header">
        <h2><i class="fas fa-paper-plane" style="color:#0369a1;"></i> Per-Client Daily Activity Schedules</h2>
        <p>Set different daily send times for each client. Reports include only that client's projects.</p>
    </div>

    <div style="overflow-x:auto;">
        <table style="width:100%;border-collapse:collapse;background:white;border-radius:8px;overflow:hidden;border:1px solid #e5e7eb;">
            <thead style="background:#f1f5f9;">
                <tr>
                    <th style="text-align:left;padding:10px 12px;font-size:12px;color:#475569;text-transform:uppercase;">Client</th>
                    <th style="text-align:left;padding:10px 12px;font-size:12px;color:#475569;text-transform:uppercase;">Email</th>
                    <th style="text-align:center;padding:10px 12px;font-size:12px;color:#475569;text-transform:uppercase;">Enabled</th>
                    <th style="text-align:center;padding:10px 12px;font-size:12px;color:#475569;text-transform:uppercase;">Send Time</th>
                    <th style="text-align:left;padding:10px 12px;font-size:12px;color:#475569;text-transform:uppercase;">Subject</th>
                    <th style="text-align:center;padding:10px 12px;font-size:12px;color:#475569;text-transform:uppercase;">Last Sent</th>
                </tr>
            </thead>
            <tbody id="daily-activity-schedules-body">
                <tr>
                    <td colspan="6" style="padding:14px;color:#64748b;">Loading client schedules...</td>
                </tr>
            </tbody>
        </table>
    </div>

    <div style="margin-top:10px;">
        <small class="text-muted">
            Cron runs every minute, scheduler checks every 5 minutes, and each client is sent once per day after their configured time.
        </small>
    </div>

    <div style="display:none;">
        <input type="checkbox" id="daily-activity-email-enabled" class="form-checkbox">
        <input type="text" id="daily-activity-email-recipients" class="form-control">
        <input type="time" id="daily-activity-email-send-time" class="form-control" value="18:00">
        <div class="form-group" style="margin-bottom:0;">
            <input type="text" id="daily-activity-email-last-sent" class="form-control" readonly>
        </div>
    </div>
</div>

<div class="settings-section" style="background:#f0fdf4;border:2px solid #10b981;">
    <div class="section-header">
        <h2><i class="fas fa-server" style="color:#10b981;"></i> Hostinger Cron Setup</h2>
        <p>Use only this setup in Hostinger hPanel.</p>
    </div>

    <div style="display:grid;gap:14px;">
        <div style="background:white;padding:14px;border-radius:8px;border:1px solid #d1fae5;">
            <strong style="color:#064e3b;">Create a New Cron Job</strong>
            <ul style="margin:8px 0 0 18px;color:#065f46;font-size:13px;line-height:1.6;">
                <li>Type: <code>PHP</code></li>
                <li>Mode: <code>Custom</code></li>
                <li>Command to run:</li>
            </ul>
<pre style="background:#022c22;color:#6ee7b7;padding:10px;border-radius:6px;font-size:12px;margin:10px 0 0;">/usr/bin/php /home/u849062718/domains/brainandbolt.com/public_html/sub-domains/timetrack/artisan schedule:run >> /dev/null 2>&1</pre>
            <ul style="margin:8px 0 0 18px;color:#065f46;font-size:13px;line-height:1.6;">
                <li>Minute: <code>*</code></li>
                <li>Hour: <code>*</code></li>
                <li>Day: <code>*</code></li>
                <li>Month: <code>*</code></li>
                <li>Weekday: <code>*</code></li>
            </ul>
            <p style="margin:10px 0 0;color:#065f46;font-size:13px;">Click <strong>Save</strong>.</p>
        </div>

        <div style="background:white;padding:14px;border-radius:8px;border:1px solid #d1fae5;">
            <strong style="color:#064e3b;">Or (same result) create another Custom cron with the same schedule</strong>
<pre style="background:#022c22;color:#6ee7b7;padding:10px;border-radius:6px;font-size:12px;margin:10px 0 0;">php /home/u849062718/domains/brainandbolt.com/public_html/sub-domains/timetrack/artisan schedule:run >> /dev/null 2>&1</pre>
        </div>
    </div>
</div>
