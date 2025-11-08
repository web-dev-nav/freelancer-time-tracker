{{-- Automation Tab - Cron Job Setup --}}

@php
    $cronToken = substr(md5(config('app.key')), 0, 16);
    $cronUrl = url("/cron/run/{$cronToken}");
    $testUrl = url("/cron/test-reminders/{$cronToken}");
@endphp

{{-- What This Cron Does --}}
<div class="settings-section" style="background: #f0f9ff; border: 2px solid #0284c7;">
    <div class="section-header">
        <h2><i class="fas fa-info-circle" style="color: #0284c7;"></i> What This Automation Does</h2>
        <p>Understand how automated invoice reminders work</p>
    </div>

    <div style="display: grid; gap: 12px;">
        <div style="background: white; padding: 12px; border-radius: 6px; border: 1px solid #e5e7eb;">
            <strong style="color: #1f2937; display: block; margin-bottom: 4px;">⏰ Schedule:</strong>
            <p style="margin: 0; color: #4b5563; font-size: 14px;">
                Cron runs <strong>every minute</strong>, but only executes at <strong>9:00 AM daily</strong>
            </p>
        </div>

        <div style="background: white; padding: 12px; border-radius: 6px; border: 1px solid #e5e7eb;">
            <strong style="color: #1f2937; display: block; margin-bottom: 4px;">✅ Sends Reminders For:</strong>
            <ul style="margin: 4px 0 0 20px; padding: 0; color: #4b5563; font-size: 14px; line-height: 1.6;">
                <li>Invoices with status <code style="background: #e0f2fe; padding: 2px 6px; border-radius: 3px;">sent</code> (not draft or paid)</li>
                <li>Due date is <strong>today, tomorrow, within 3 days</strong></li>
                <li>OR due date is <strong>in the past (overdue)</strong></li>
            </ul>
        </div>

        <div style="background: white; padding: 12px; border-radius: 6px; border: 1px solid #e5e7eb;">
            <strong style="color: #1f2937; display: block; margin-bottom: 4px;">❌ Does NOT Send For:</strong>
            <ul style="margin: 4px 0 0 20px; padding: 0; color: #4b5563; font-size: 14px; line-height: 1.6;">
                <li>Draft invoices (not sent yet)</li>
                <li>Paid invoices</li>
                <li>Invoices due more than 3 days from now</li>
                <li>Invoices without client email</li>
            </ul>
        </div>

        <div style="background: #fef3c7; padding: 12px; border-radius: 6px; border: 1px solid #fbbf24;">
            <strong style="color: #92400e; display: block; margin-bottom: 4px;">
                <i class="fas fa-exclamation-triangle" style="color: #f59e0b;"></i> Important:
            </strong>
            <p style="margin: 0; color: #92400e; font-size: 13px;">
                Same invoice will receive reminders <strong>every day at 9 AM</strong> until you mark it as paid. This helps ensure clients don't forget to pay.
            </p>
        </div>
    </div>
</div>

{{-- Test URLs --}}
<div class="settings-section" style="background: #d1fae5; border: 2px solid #10b981;">
    <div class="section-header">
        <h2><i class="fas fa-vial" style="color: #10b981;"></i> Test Before Setup</h2>
        <p>Click these links to test that the automation URLs work correctly</p>
    </div>

    <div style="margin-bottom: 15px;">
        <strong style="color: #065f46; display: block; margin-bottom: 8px;">1. Test Invoice Reminders:</strong>
        <a href="{{ $testUrl }}" target="_blank" class="btn btn-success" style="display: inline-block; margin-bottom: 8px;">
            <i class="fas fa-play-circle"></i> Test Invoice Reminders Now
        </a>
        <code style="display: block; background: #f0fdf4; padding: 8px; border-radius: 4px; font-family: monospace; font-size: 11px; color: #065f46; word-break: break-all; border: 1px solid #86efac;">
            {{ $testUrl }}
        </code>
    </div>

    <div>
        <strong style="color: #065f46; display: block; margin-bottom: 8px;">2. Test Full Scheduler:</strong>
        <a href="{{ $cronUrl }}" target="_blank" class="btn" style="display: inline-block; margin-bottom: 8px; background: #059669; color: white;">
            <i class="fas fa-play-circle"></i> Test Full Scheduler Now
        </a>
        <code style="display: block; background: #f0fdf4; padding: 8px; border-radius: 4px; font-family: monospace; font-size: 11px; color: #065f46; word-break: break-all; border: 1px solid #86efac;">
            {{ $cronUrl }}
        </code>
    </div>

    <p style="margin: 12px 0 0 0; color: #065f46; font-size: 13px;">
        <i class="fas fa-check-circle"></i> You should see JSON response with "status": "success"
    </p>
</div>

{{-- Hostinger Setup --}}
<div class="settings-section" style="background: #f0fdf4; border: 2px solid #10b981;">
    <div class="section-header">
        <h2><i class="fas fa-server" style="color: #10b981;"></i> HOSTINGER - Simple 4-Step Setup</h2>
        <p>Follow these steps to set up cron job on Hostinger hosting</p>
    </div>

    <div style="display: grid; gap: 12px;">
        <div style="background: white; padding: 12px; border-radius: 6px;">
            <strong style="color: #1f2937; display: block; margin-bottom: 6px;">📍 Step 1: Go to Cron Jobs</strong>
            <p style="margin: 0; color: #4b5563; font-size: 14px;">
                Login to <strong>Hostinger hPanel</strong> → Your Website → <strong>"Advanced"</strong> → <strong>"Cron Jobs"</strong>
            </p>
        </div>

        <div style="background: white; padding: 12px; border-radius: 6px;">
            <strong style="color: #1f2937; display: block; margin-bottom: 6px;">⚙️ Step 2: Select "Custom"</strong>
            <p style="margin: 0; color: #4b5563; font-size: 14px;">
                At the top, change from <strong>"PHP"</strong> to <strong>"Custom"</strong>
            </p>
        </div>

        <div style="background: white; padding: 12px; border-radius: 6px;">
            <strong style="color: #1f2937; display: block; margin-bottom: 6px;">⏰ Step 3: Set Schedule to Every Minute</strong>
            <p style="margin: 0 0 8px 0; color: #4b5563; font-size: 14px;">
                Set all dropdowns to show an asterisk <strong>*</strong>:
            </p>
            <code style="display: block; background: #f9fafb; padding: 8px; border-radius: 4px; border: 1px solid #e5e7eb;">
                Minute: *  |  Hour: *  |  Day: *  |  Month: *  |  Weekday: *
            </code>
        </div>

        <div style="background: white; padding: 12px; border-radius: 6px;">
            <strong style="color: #1f2937; display: block; margin-bottom: 6px;">🔗 Step 4: Paste This Command</strong>
            <code style="display: block; background: #1f2937; color: #10b981; padding: 10px; border-radius: 4px; font-family: monospace; font-size: 11px; word-break: break-all; border: 2px solid #10b981; margin-bottom: 8px;">curl -s "{{ $cronUrl }}"</code>
            <button onclick="navigator.clipboard.writeText('curl -s \"{{ $cronUrl }}\"')" class="btn btn-sm btn-success">
                <i class="fas fa-copy"></i> Copy Command
            </button>
        </div>
    </div>

    <p style="margin: 15px 0 0 0; color: #065f46; font-size: 14px; font-weight: 600; text-align: center;">
        <i class="fas fa-check-circle"></i> Click "Create" - Done! Reminders will send daily at 9:00 AM
    </p>
</div>

{{-- cPanel Setup --}}
<div class="settings-section" style="background: #eff6ff; border: 2px solid #3b82f6;">
    <div class="section-header">
        <h2><i class="fas fa-server" style="color: #3b82f6;"></i> cPANEL - Simple 3-Step Setup</h2>
        <p>Follow these steps to set up cron job on cPanel hosting</p>
    </div>

    <div style="display: grid; gap: 12px;">
        <div style="background: white; padding: 12px; border-radius: 6px;">
            <strong style="color: #1f2937; display: block; margin-bottom: 6px;">📍 Step 1: Go to Cron Jobs</strong>
            <p style="margin: 0; color: #4b5563; font-size: 14px;">
                Login to <strong>cPanel</strong> → Search for <strong>"Cron Jobs"</strong> → Click to open
            </p>
        </div>

        <div style="background: white; padding: 12px; border-radius: 6px;">
            <strong style="color: #1f2937; display: block; margin-bottom: 6px;">⚙️ Step 2: Set Schedule</strong>
            <p style="margin: 0 0 8px 0; color: #4b5563; font-size: 14px;">
                Under <strong>"Add New Cron Job"</strong>, set the schedule to <strong>Every Minute</strong>:
            </p>
            <code style="display: block; background: #f9fafb; padding: 8px; border-radius: 4px; border: 1px solid #e5e7eb; margin-bottom: 6px;">
                Minute: *  |  Hour: *  |  Day: *  |  Month: *  |  Weekday: *
            </code>
            <p style="margin: 0; color: #6b7280; font-size: 13px;">
                Or select "Common Settings" → "Once Per Minute (* * * * *)"
            </p>
        </div>

        <div style="background: white; padding: 12px; border-radius: 6px;">
            <strong style="color: #1f2937; display: block; margin-bottom: 6px;">🔗 Step 3: Paste This Command</strong>
            <p style="margin: 0 0 8px 0; color: #4b5563; font-size: 14px;">
                In the <strong>"Command"</strong> field, paste this (choose wget OR curl):
            </p>

            <p style="margin: 8px 0 4px 0; font-size: 13px; font-weight: 600;">Option 1: wget (recommended)</p>
            <code style="display: block; background: #1f2937; color: #60a5fa; padding: 10px; border-radius: 4px; font-family: monospace; font-size: 10px; word-break: break-all; border: 2px solid #3b82f6; margin-bottom: 8px;">wget -q -O - "{{ $cronUrl }}" > /dev/null 2>&1</code>
            <button onclick="navigator.clipboard.writeText('wget -q -O - \"{{ $cronUrl }}\" > /dev/null 2>&1')" class="btn btn-sm" style="background: #3b82f6; color: white; margin-bottom: 12px;">
                <i class="fas fa-copy"></i> Copy wget Command
            </button>

            <p style="margin: 8px 0 4px 0; font-size: 13px; font-weight: 600;">Option 2: curl</p>
            <code style="display: block; background: #1f2937; color: #60a5fa; padding: 10px; border-radius: 4px; font-family: monospace; font-size: 10px; word-break: break-all; border: 2px solid #3b82f6; margin-bottom: 8px;">curl -s "{{ $cronUrl }}" > /dev/null 2>&1</code>
            <button onclick="navigator.clipboard.writeText('curl -s \"{{ $cronUrl }}\" > /dev/null 2>&1')" class="btn btn-sm" style="background: #3b82f6; color: white;">
                <i class="fas fa-copy"></i> Copy curl Command
            </button>
        </div>
    </div>

    <p style="margin: 15px 0 0 0; color: #1e40af; font-size: 14px; font-weight: 600; text-align: center;">
        <i class="fas fa-check-circle"></i> Click "Add New Cron Job" - Done! Reminders will send daily at 9:00 AM
    </p>
</div>

{{-- Quick Tips --}}
<div class="settings-section" style="background: #fef3c7; border: 2px solid #f59e0b;">
    <div class="section-header">
        <h2><i class="fas fa-lightbulb" style="color: #f59e0b;"></i> Quick Tips</h2>
        <p>Important information about cron job setup</p>
    </div>

    <div style="display: grid; gap: 10px;">
        <div style="background: white; padding: 10px; border-radius: 4px;">
            <strong style="color: #92400e; font-size: 13px;">✅ ALWAYS TEST FIRST!</strong>
            <p style="margin: 4px 0 0 0; color: #92400e; font-size: 13px;">
                Click the green "Test" buttons above to verify the URL works before setting up cron
            </p>
        </div>
        <div style="background: white; padding: 10px; border-radius: 4px;">
            <strong style="color: #92400e; font-size: 13px;">⏰ How It Works</strong>
            <p style="margin: 4px 0 0 0; color: #92400e; font-size: 13px;">
                Cron runs every minute, but reminders only send at <strong>9:00 AM daily</strong> for invoices due within 3 days or overdue
            </p>
        </div>
        <div style="background: white; padding: 10px; border-radius: 4px;">
            <strong style="color: #92400e; font-size: 13px;">🔒 Security</strong>
            <p style="margin: 4px 0 0 0; color: #92400e; font-size: 13px;">
                Your cron URL includes a security token - <strong>keep it private!</strong> Check logs at <code style="background: #fde68a; padding: 2px 4px; border-radius: 3px;">storage/logs/laravel.log</code>
            </p>
        </div>
    </div>
</div>
