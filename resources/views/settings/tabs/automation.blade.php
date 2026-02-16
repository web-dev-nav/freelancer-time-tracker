{{-- Automation Tab - Hostinger Steps Only --}}

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
