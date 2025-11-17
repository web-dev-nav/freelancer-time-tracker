{{-- Automation Tab - Scheduler Setup --}}

@php
    $cronToken = substr(md5(config('app.key')), 0, 16);
    $cronUrl = url("/cron/run/{$cronToken}");
    $testUrl = url("/cron/test-reminders/{$cronToken}");
    $backupUrl = url("/cron/backup/{$cronToken}");
@endphp

{{-- Overview --}}
<div class="settings-section" style="background: #eef2ff; border: 2px solid #6366f1;">
    <div class="section-header">
        <h2><i class="fas fa-robot" style="color: #4f46e5;"></i> What the Scheduler Handles</h2>
        <p>One cron job powers both automated EMAIL reminders and DATABASE backups.</p>
    </div>

    <div style="display: grid; gap: 10px;">
        <div style="background: white; padding: 12px; border-radius: 8px; border: 1px solid #e0e7ff;">
            <strong style="color: #312e81;">Daily email reminders (9:00 AM)</strong>
            <ul style="margin: 6px 0 0 18px; color: #4b5563; font-size: 13px; line-height: 1.5;">
                <li>Runs for invoices in <code style="background:#e0e7ff; padding: 2px 6px; border-radius: 3px;">sent</code> status.</li>
                <li>Targets invoices due today, tomorrow, within 3 days, or already overdue.</li>
                <li>Stops automatically after you mark the invoice as paid.</li>
            </ul>
        </div>

        <div style="background: white; padding: 12px; border-radius: 8px; border: 1px solid #fcd34d;">
            <strong style="color: #92400e;">Automated DB backups (2:00 AM)</strong>
            <ul style="margin: 6px 0 0 18px; color: #7c2d12; font-size: 13px; line-height: 1.5;">
                <li>Creates a full SQL dump every Sunday, Tuesday, Thursday, Saturday.</li>
                <li>Files land in <code>storage/app/backups</code> with timestamped names.</li>
                <li>Same scheduler command triggers both backups and reminders.</li>
            </ul>
        </div>
    </div>
</div>

{{-- Quick Install --}}
<div class="settings-section" style="background: #ecfccb; border: 2px solid #65a30d;">
    <div class="section-header">
        <h2><i class="fas fa-plug" style="color: #4d7c0f;"></i> 3-Step Scheduler Install</h2>
        <p>This is the only cron you need. Laravel handles timing internally.</p>
    </div>

    <ol style="margin: 0; padding-left: 18px; color: #1a2e05; line-height: 1.8; font-size: 14px;">
        <li>Open your hosting control panel and find the Cron Jobs screen.</li>
        <li>Choose the preset or custom schedule that runs <strong>every minute</strong>.</li>
        <li>Use this command (update absolute paths for your server):</li>
    </ol>

<pre style="background:#0f172a;color:#c7f9cc;padding:12px;border-radius:6px;font-size:12px;margin-top:12px;">
/usr/bin/php /path/to/project/artisan schedule:run >> /dev/null 2>&1
</pre>

    <p style="margin-top: 12px; color: #1a2e05; font-size: 13px;">
        That’s it! Once saved, Laravel fires reminders at 9 AM and backups at 2 AM automatically.
    </p>
</div>

{{-- Need wget-based commands? --}}
<div class="settings-section" style="background:#fff7ed;border:2px solid #f97316;">
    <div class="section-header">
        <h2><i class="fas fa-terminal" style="color:#c2410c;"></i> HTTP / wget commands</h2>
        <p>Use these if your host only allows URL calls instead of running PHP directly.</p>
    </div>

    <div style="display:grid;gap:12px;">
        <div style="background:white;padding:12px;border-radius:8px;border:1px solid #fed7aa;">
            <strong style="color:#c2410c;">Email reminders only</strong>
            <p style="margin:6px 0 8px;color:#7c2d12;font-size:13px;">Triggers just the invoice-reminder routine (safe to test anytime).</p>
<pre style="background:#0f172a;color:#fcd34d;padding:10px;border-radius:6px;font-size:12px;margin:0;">wget -q -O - "{{ $testUrl }}" > /dev/null 2>&1</pre>
        </div>

        <div style="background:white;padding:12px;border-radius:8px;border:1px solid #fed7aa;">
            <strong style="color:#c2410c;">Database backup only</strong>
            <p style="margin:6px 0 8px;color:#7c2d12;font-size:13px;">Calls the backup endpoint and runs <code>php artisan backup:database</code>.</p>
<pre style="background:#0f172a;color:#fcd34d;padding:10px;border-radius:6px;font-size:12px;margin:0;">wget -q -O - "{{ $backupUrl }}" > /dev/null 2>&1</pre>
        </div>
    </div>

    <p style="margin-top:10px;color:#7c2d12;font-size:13px;">
        <i class="fas fa-lightbulb"></i> If your host supports PHP CLI, prefer the <code>php artisan schedule:run</code> command above and let Laravel orchestrate both jobs together.
    </p>
</div>

{{-- Hostinger cheat sheet --}}
<div class="settings-section" style="background:#f0fdf4;border:2px solid #10b981;">
    <div class="section-header">
        <h2><i class="fas fa-server" style="color:#10b981;"></i> Hostinger (3 steps)</h2>
        <p>Fast path for the most common deployment.</p>
    </div>

    <div style="display:grid;gap:10px;">
        <div style="background:white;padding:12px;border-radius:8px;border:1px solid #d1fae5;">
            <strong style="color:#064e3b;">1. Open Cron Jobs</strong>
            <p style="margin:4px 0 0;color:#065f46;font-size:13px;">hPanel → Advanced → Cron Jobs → “Add Cronjob”.</p>
        </div>
        <div style="background:white;padding:12px;border-radius:8px;border:1px solid #d1fae5;">
            <strong style="color:#064e3b;">2. Choose schedule</strong>
            <p style="margin:4px 0 0;color:#065f46;font-size:13px;">Select the “Every minute” preset.</p>
        </div>
        <div style="background:white;padding:12px;border-radius:8px;border:1px solid #d1fae5;">
            <strong style="color:#064e3b;">3. Command</strong>
            <p style="margin:4px 0 8px;color:#065f46;font-size:13px;">Use the absolute paths for your account. Example:</p>
<pre style="background:#022c22;color:#6ee7b7;padding:10px;border-radius:6px;font-size:12px;margin:0;">/usr/bin/php /home/USERNAME/domains/example.com/public_html/artisan schedule:run >> /dev/null 2>&1</pre>
        </div>
    </div>

    <p style="margin-top:12px;color:#065f46;font-size:13px;text-align:center;">
        <i class="fas fa-check-circle"></i> Save the cron and you’re done—no additional jobs needed.
    </p>
</div>

{{-- cPanel cheat sheet --}}
<div class="settings-section" style="background:#eff6ff;border:2px solid #3b82f6;">
    <div class="section-header">
        <h2><i class="fas fa-cloud" style="color:#2563eb;"></i> cPanel (3 steps)</h2>
        <p>Same idea, different UI.</p>
    </div>

    <div style="display:grid;gap:10px;">
        <div style="background:white;padding:12px;border-radius:8px;border:1px solid #dbeafe;">
            <strong style="color:#1d4ed8;">1. Cron Jobs → “Add New Cron Job”</strong>
        </div>
        <div style="background:white;padding:12px;border-radius:8px;border:1px solid #dbeafe;">
            <strong style="color:#1d4ed8;">2. Schedule</strong>
            <p style="margin:4px 0 0;color:#1e3a8a;font-size:13px;">Set Minute/Hour/Day/Month/Weekday to <code>*</code> or choose “Once Per Minute”.</p>
        </div>
        <div style="background:white;padding:12px;border-radius:8px;border:1px solid #dbeafe;">
            <strong style="color:#1d4ed8;">3. Command</strong>
            <p style="margin:4px 0 8px;color:#1e3a8a;font-size:13px;">Use PHP directly or call the scheduler URL:</p>
<pre style="background:#0f172a;color:#93c5fd;padding:10px;border-radius:6px;font-size:12px;margin:0;">/usr/local/bin/php /home/USERNAME/app/artisan schedule:run >> /dev/null 2>&1
# or fallback if PHP path isn’t known
wget -q -O - "{{ $cronUrl }}" > /dev/null 2>&1</pre>
        </div>
    </div>
</div>

{{-- Verification & manual triggers --}}
<div class="settings-section" style="background:#fef3c7;border:2px solid #f59e0b;">
    <div class="section-header">
        <h2><i class="fas fa-clipboard-check" style="color:#d97706;"></i> Verify it works</h2>
        <p>Quick checks to confirm the cron is alive.</p>
    </div>

    <div style="display:grid;gap:10px;">
        <div style="background:white;padding:12px;border-radius:8px;border:1px solid #fde68a;">
            <strong style="color:#b45309;">1. Test endpoints</strong>
            <p style="margin:4px 0 0;color:#92400e;font-size:13px;">Click these and expect a JSON success response:</p>
            <ul style="margin:6px 0 0 18px;color:#92400e;font-size:13px;">
                <li><a href="{{ $testUrl }}" target="_blank">Invoice reminder test</a></li>
                <li><a href="{{ $cronUrl }}" target="_blank">Full schedule run</a></li>
            </ul>
        </div>
        <div style="background:white;padding:12px;border-radius:8px;border:1px solid #fde68a;">
            <strong style="color:#b45309;">2. Artisan commands (SSH)</strong>
<pre style="background:#0f172a;color:#fcd34d;padding:10px;border-radius:6px;font-size:12px;margin:6px 0 0;">php artisan schedule:run      # run everything once
php artisan backup:database   # manual DB backup
php artisan schedule:list     # see upcoming tasks
</pre>
        </div>
    </div>

    <p style="margin-top:10px;color:#92400e;font-size:13px;">
        <i class="fas fa-lightbulb"></i> Keep the cron enabled 24/7. Pausing it stops reminders and backups together.
    </p>
</div>
