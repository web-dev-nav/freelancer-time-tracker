{{-- Automation Tab --}}

<style>
    .automation-shell {
        border: 1px solid #bfdbfe;
        border-radius: 16px;
        background: linear-gradient(155deg, #f8fbff 0%, #f0f7ff 100%);
        box-shadow: 0 10px 28px rgba(15, 23, 42, 0.08);
        overflow: hidden;
    }

    .automation-header {
        padding: 20px 22px 18px;
        border-bottom: 1px solid #dbeafe;
        background: linear-gradient(90deg, rgba(2, 132, 199, 0.08), rgba(59, 130, 246, 0.03));
    }

    .automation-title {
        margin: 0;
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 18px;
        color: #0f172a;
    }

    .automation-subtitle {
        margin: 8px 0 0;
        color: #475569;
        font-size: 14px;
        line-height: 1.45;
    }

    .automation-chip-row {
        margin-top: 12px;
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
    }

    .automation-chip {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        border-radius: 999px;
        padding: 4px 10px;
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: .04em;
        background: #e0f2fe;
        color: #075985;
        border: 1px solid #bae6fd;
    }

    .automation-body {
        padding: 16px;
    }

    .automation-table-wrap {
        overflow: auto;
        border: 1px solid #dbeafe;
        border-radius: 12px;
        background: #ffffff;
    }

    .automation-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        min-width: 1160px;
    }

    .automation-table thead th {
        position: sticky;
        top: 0;
        z-index: 1;
        text-align: left;
        padding: 11px 12px;
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: .06em;
        color: #475569;
        background: #f8fafc;
        border-bottom: 1px solid #e2e8f0;
    }

    .automation-table tbody td {
        padding: 10px 12px;
        border-bottom: 1px solid #f1f5f9;
        vertical-align: middle;
        color: #0f172a;
        font-size: 13px;
    }

    .automation-table tbody tr:hover {
        background: #f8fafc;
    }

    .automation-cell-center {
        text-align: center;
    }

    .automation-client-name {
        font-weight: 600;
        color: #0f172a;
    }

    .automation-client-email {
        color: #64748b;
        font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
        font-size: 12px;
    }

    .automation-input {
        width: 100%;
        min-height: 36px;
        border: 1px solid #cbd5e1;
        border-radius: 10px;
        padding: 7px 10px;
        font-size: 13px;
        color: #0f172a;
        background: #ffffff;
    }

    .automation-input:focus {
        outline: none;
        border-color: #38bdf8;
        box-shadow: 0 0 0 3px rgba(56, 189, 248, 0.2);
    }

    .automation-tags {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
    }

    .automation-tag {
        border: 1px solid #cbd5e1;
        border-radius: 999px;
        padding: 4px 10px;
        font-size: 11px;
        font-weight: 600;
        letter-spacing: .02em;
        background: #ffffff;
        color: #334155;
        cursor: pointer;
        user-select: none;
        transition: all .15s ease;
    }

    .automation-tag:hover {
        border-color: #7dd3fc;
        background: #f0f9ff;
        color: #0c4a6e;
    }

    .automation-tag.active {
        border-color: #0ea5e9;
        background: #e0f2fe;
        color: #075985;
        box-shadow: 0 0 0 1px rgba(14, 165, 233, 0.15) inset;
    }

    .automation-time-input {
        max-width: 140px;
        text-align: center;
    }

    .automation-select {
        min-height: 36px;
        border: 1px solid #cbd5e1;
        border-radius: 10px;
        padding: 6px 10px;
        font-size: 13px;
        background: #fff;
        color: #0f172a;
    }

    .automation-day-tags {
        display: flex;
        gap: 6px;
        flex-wrap: wrap;
    }

    .automation-day-tag {
        border: 1px solid #cbd5e1;
        border-radius: 999px;
        padding: 3px 8px;
        font-size: 10px;
        font-weight: 700;
        letter-spacing: .03em;
        background: #ffffff;
        color: #334155;
        cursor: pointer;
        user-select: none;
        text-transform: uppercase;
    }

    .automation-day-tag.active {
        border-color: #16a34a;
        background: #dcfce7;
        color: #166534;
    }

    .automation-empty-row {
        text-align: center;
        color: #64748b;
        padding: 24px 12px;
    }

    .automation-note {
        margin-top: 12px;
        background: #f8fafc;
        border: 1px dashed #cbd5e1;
        border-radius: 10px;
        padding: 10px 12px;
        color: #475569;
        font-size: 12px;
        line-height: 1.5;
    }

    .cron-shell {
        border: 1px solid #bbf7d0;
        border-radius: 16px;
        background: linear-gradient(155deg, #f0fdf4 0%, #ecfdf5 100%);
        box-shadow: 0 10px 28px rgba(15, 23, 42, 0.07);
        overflow: hidden;
        margin-top: 14px;
    }

    .cron-header {
        padding: 18px 22px;
        border-bottom: 1px solid #bbf7d0;
    }

    .cron-grid {
        padding: 14px 16px 16px;
        display: grid;
        gap: 12px;
    }

    .cron-card {
        background: white;
        padding: 14px;
        border-radius: 12px;
        border: 1px solid #d1fae5;
    }

    .cron-cmd {
        background: #052e16;
        color: #86efac;
        border-radius: 8px;
        padding: 10px;
        font-size: 12px;
        margin-top: 10px;
        overflow: auto;
    }

    @media (max-width: 900px) {
        .automation-header,
        .cron-header {
            padding: 16px;
        }

        .automation-body,
        .cron-grid {
            padding: 12px;
        }
    }
</style>

<div class="settings-section automation-shell">
    <div class="automation-header">
        <h2 class="automation-title">
            <i class="fas fa-paper-plane" style="color:#0369a1;"></i>
            Per-Client Daily Activity Schedules
        </h2>
        <p class="automation-subtitle">Set different send times, subjects, and activity columns for each client. Each client receives only their project data.</p>
        <div class="automation-chip-row">
            <span class="automation-chip"><i class="fas fa-clock"></i> Checks Every 5 Min</span>
            <span class="automation-chip"><i class="fas fa-envelope-open-text"></i> Per-Client Delivery</span>
            <span class="automation-chip"><i class="fas fa-filter"></i> Custom Columns</span>
        </div>
    </div>

    <div class="automation-body">
        <div class="automation-table-wrap">
            <table class="automation-table">
                <thead>
                <tr>
                    <th>Client</th>
                    <th>Email</th>
                    <th class="automation-cell-center">Enabled</th>
                    <th class="automation-cell-center">Mode</th>
                    <th class="automation-cell-center">Send Time</th>
                    <th class="automation-cell-center">Date</th>
                    <th>Working Days</th>
                    <th>Subject</th>
                    <th>Activity Columns</th>
                    <th class="automation-cell-center">Last Sent</th>
                </tr>
                </thead>
                <tbody id="daily-activity-schedules-body">
                <tr>
                    <td colspan="10" class="automation-empty-row">Loading client schedules...</td>
                </tr>
                </tbody>
            </table>
        </div>

        <div class="automation-note">
            Daily mode ignores Date and uses only selected Working Days + Time. Date mode sends only on that specific date and time.
        </div>
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

<div class="settings-section cron-shell">
    <div class="cron-header">
        <h2 class="automation-title"><i class="fas fa-server" style="color:#10b981;"></i> Hostinger Cron Setup</h2>
        <p class="automation-subtitle">Use this exact command in hPanel and run it every minute.</p>
    </div>

    <div class="cron-grid">
        <div class="cron-card">
            <strong style="color:#064e3b;">Create a New Cron Job</strong>
            <ul style="margin:8px 0 0 18px;color:#065f46;font-size:13px;line-height:1.6;">
                <li>Type: <code>PHP</code></li>
                <li>Mode: <code>Custom</code></li>
                <li>Command to run:</li>
            </ul>
<pre class="cron-cmd">/usr/bin/php /home/u849062718/domains/brainandbolt.com/public_html/sub-domains/timetrack/artisan schedule:run >> /dev/null 2>&1</pre>
            <ul style="margin:8px 0 0 18px;color:#065f46;font-size:13px;line-height:1.6;">
                <li>Minute: <code>*</code></li>
                <li>Hour: <code>*</code></li>
                <li>Day: <code>*</code></li>
                <li>Month: <code>*</code></li>
                <li>Weekday: <code>*</code></li>
            </ul>
            <p style="margin:10px 0 0;color:#065f46;font-size:13px;">Click <strong>Save</strong>.</p>
        </div>

        <div class="cron-card">
            <strong style="color:#064e3b;">Or (same result) create another Custom cron with the same schedule</strong>
<pre class="cron-cmd">php /home/u849062718/domains/brainandbolt.com/public_html/sub-domains/timetrack/artisan schedule:run >> /dev/null 2>&1</pre>
        </div>
    </div>
</div>
