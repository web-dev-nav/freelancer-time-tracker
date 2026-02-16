{{-- Automation Tab --}}

<div class="settings-section" style="background:#f8fafc;border:2px solid #0ea5e9;">
    <div class="section-header">
        <h2><i class="fas fa-paper-plane" style="color:#0369a1;"></i> Daily Activity Email Schedule</h2>
        <p>Control when daily activity summary email is sent to client/company recipients.</p>
    </div>

    <div style="display:grid;gap:14px;">
        <label class="form-label checkbox-label" style="margin-bottom:0;">
            <input type="checkbox" id="daily-activity-email-enabled" class="form-checkbox">
            <span>
                <i class="fas fa-toggle-on"></i>
                Enable Daily Activity Email
            </span>
        </label>

        <div class="form-group" style="margin-bottom:0;">
            <label class="form-label" for="daily-activity-email-recipients">
                <i class="fas fa-envelope"></i>
                Recipient Email(s)
            </label>
            <input type="text" id="daily-activity-email-recipients" class="form-control"
                   placeholder="billing@client.com, manager@company.com">
            <small class="text-muted">Separate multiple emails using comma or semicolon.</small>
        </div>

        <div class="form-group" style="margin-bottom:0;max-width:280px;">
            <label class="form-label" for="daily-activity-email-send-time">
                <i class="fas fa-clock"></i>
                Send Time (Daily)
            </label>
            <input type="time" id="daily-activity-email-send-time" class="form-control" value="18:00">
        </div>

        <div class="form-group" style="margin-bottom:0;">
            <label class="form-label" for="daily-activity-email-last-sent">
                <i class="fas fa-calendar-check"></i>
                Last Sent Date
            </label>
            <input type="text" id="daily-activity-email-last-sent" class="form-control" readonly placeholder="Not sent yet">
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
