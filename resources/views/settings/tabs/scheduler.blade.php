{{-- Scheduler Tab --}}

<style>
    .scheduler-shell {
        border: 1px solid #bfdbfe;
        border-radius: 16px;
        background: linear-gradient(155deg, #f8fbff 0%, #f0f7ff 100%);
        box-shadow: 0 10px 28px rgba(15, 23, 42, 0.08);
        overflow: hidden;
    }

    .scheduler-header {
        padding: 20px 22px 18px;
        border-bottom: 1px solid #dbeafe;
        background: linear-gradient(90deg, rgba(2, 132, 199, 0.08), rgba(59, 130, 246, 0.03));
    }

    .scheduler-title {
        margin: 0;
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 18px;
        color: #0f172a;
    }

    .scheduler-subtitle {
        margin: 8px 0 0;
        color: #475569;
        font-size: 14px;
        line-height: 1.45;
    }

    .scheduler-body {
        padding: 16px;
        display: grid;
        gap: 12px;
    }

    .automation-card {
        background: #ffffff;
        border: 1px solid #dbeafe;
        border-radius: 14px;
        padding: 14px;
        box-shadow: 0 6px 18px rgba(15, 23, 42, 0.06);
    }

    .automation-card-head {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 10px;
        margin-bottom: 12px;
    }

    .automation-card-status {
        font-size: 12px;
        color: #64748b;
        white-space: nowrap;
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

    .automation-field {
        margin-bottom: 10px;
    }

    .automation-field:last-child {
        margin-bottom: 0;
    }

    .automation-label {
        display: block;
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: .05em;
        color: #64748b;
        margin-bottom: 6px;
        font-weight: 700;
    }

    .automation-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 10px;
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
        box-shadow: 0 0 0 2px rgba(56, 189, 248, 0.2);
    }

    .automation-select {
        appearance: none;
        background-image: linear-gradient(45deg, transparent 50%, #64748b 50%),
            linear-gradient(135deg, #64748b 50%, transparent 50%);
        background-position: calc(100% - 16px) 50%, calc(100% - 11px) 50%;
        background-size: 5px 5px, 5px 5px;
        background-repeat: no-repeat;
        padding-right: 28px;
    }

    .automation-time-input {
        padding-right: 10px;
    }

    .automation-enabled-wrap {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        color: #334155;
        font-size: 13px;
        font-weight: 600;
    }

    .automation-textarea {
        min-height: 110px;
        resize: vertical;
    }

    .automation-day-tags {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
    }

    .automation-day-tag {
        border: 1px solid #cbd5e1;
        border-radius: 999px;
        padding: 4px 10px;
        font-size: 12px;
        color: #475569;
        background: #ffffff;
        cursor: pointer;
    }

    .automation-day-tag.active {
        border-color: #16a34a;
        background: #dcfce7;
        color: #166534;
    }

    .scheduler-range {
        position: relative;
        display: flex;
        gap: 8px;
        align-items: center;
    }

    .scheduler-range-input {
        flex: 1;
        cursor: pointer;
        background: #f8fafc;
    }

    .scheduler-range-panel {
        position: absolute;
        top: calc(100% + 8px);
        left: 0;
        z-index: 5;
        background: #ffffff;
        border: 1px solid #cbd5e1;
        border-radius: 12px;
        padding: 12px;
        box-shadow: 0 10px 28px rgba(15, 23, 42, 0.08);
        width: min(360px, 90vw);
        display: none;
    }

    .scheduler-range-panel.active {
        display: block;
    }

    .scheduler-range-actions {
        display: flex;
        gap: 6px;
        justify-content: flex-end;
        margin-top: 8px;
    }

    .scheduler-template-row {
        display: flex;
        gap: 8px;
        align-items: center;
        margin-top: 8px;
        flex-wrap: wrap;
    }

    .scheduler-form-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 12px;
    }

    .scheduler-form-grid .full {
        grid-column: 1 / -1;
    }

    .automation-inline-help {
        font-size: 12px;
        color: #64748b;
        margin-top: 6px;
    }

    .automation-test-message {
        display: none;
        margin-top: 10px;
        padding: 10px 12px;
        border-radius: 8px;
        font-size: 13px;
    }

    .scheduler-table-wrap {
        overflow: auto;
        border: 1px solid #dbeafe;
        border-radius: 12px;
        background: #ffffff;
    }

    .scheduler-table {
        width: 100%;
        border-collapse: collapse;
        min-width: 820px;
    }

    .scheduler-table thead th {
        text-align: left;
        padding: 10px 12px;
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: .06em;
        color: #475569;
        background: #f8fafc;
        border-bottom: 1px solid #e2e8f0;
        position: sticky;
        top: 0;
        z-index: 1;
    }

    .scheduler-table tbody td {
        padding: 10px 12px;
        border-bottom: 1px solid #f1f5f9;
        font-size: 13px;
        color: #0f172a;
        vertical-align: top;
    }

    .scheduler-table tbody tr:hover {
        background: #f8fafc;
    }

    .scheduler-muted {
        color: #64748b;
        font-size: 12px;
    }

    .automation-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 2px 8px;
        border-radius: 999px;
        background: #f1f5f9;
        color: #0f172a;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .04em;
    }

    .automation-pill.status-scheduled {
        background: #dbeafe;
        color: #1e3a8a;
    }

    .automation-pill.status-sent {
        background: #dcfce7;
        color: #166534;
    }

    .automation-pill.status-cancelled {
        background: #fee2e2;
        color: #991b1b;
    }

    .automation-schedule-actions {
        display: flex;
        gap: 6px;
        flex-wrap: wrap;
    }

    .automation-schedule-actions .btn {
        padding: 5px 8px;
        font-size: 12px;
    }

    .scheduler-icon-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 30px;
        height: 30px;
        padding: 0;
    }

    .scheduler-preview {
        margin-top: 8px;
        padding: 10px 12px;
        border-radius: 10px;
        border: 1px dashed #cbd5e1;
        background: #f8fafc;
        color: #0f172a;
        font-size: 13px;
        line-height: 1.5;
        display: none;
        white-space: pre-wrap;
    }

    .scheduler-preview.active {
        display: block;
    }

    .scheduler-actions {
        display: flex;
        gap: 6px;
        align-items: center;
        flex-wrap: wrap;
    }

    .scheduler-actions-right {
        display: flex;
        justify-content: flex-end;
        gap: 6px;
        align-items: center;
    }

    .btn-sm {
        padding: 6px 10px;
        font-size: 12px;
        line-height: 1.2;
    }

    .automation-empty-row {
        text-align: center;
        color: #64748b;
        padding: 24px 12px;
    }

    @media (max-width: 900px) {
        .automation-row,
        .scheduler-form-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="settings-section scheduler-shell">
    <div class="scheduler-header">
        <h2 class="scheduler-title">
            <i class="fas fa-calendar-check" style="color:#0369a1;"></i>
            Custom Email Scheduler
        </h2>
        <p class="scheduler-subtitle">
            Schedule one-off or recurring emails to multiple recipients. View, edit, or cancel scheduled sends anytime.
        </p>
    </div>

    <div class="scheduler-body">
        <div class="automation-card">
            <div class="automation-card-head">
                <div>
                    <div class="automation-client-name">New Scheduled Email</div>
                    <div class="automation-client-email">Runs every 5 minutes via scheduler.</div>
                </div>
                <div class="automation-card-status">Status: Draft</div>
            </div>

            <div id="custom-email-schedule-form" class="scheduler-form-grid">
                <input type="hidden" id="custom-email-schedule-id">

                <div class="automation-field">
                    <label class="automation-label" for="custom-email-name">Name</label>
                    <input class="automation-input" id="custom-email-name" type="text" placeholder="Invoice Reminder - Acme">
                </div>
                <div class="automation-field">
                    <label class="automation-label" for="custom-email-recipients">Send To</label>
                    <input class="automation-input" id="custom-email-recipients" type="text" list="custom-email-recipient-list" placeholder="client@acme.com, another@acme.com">
                    <datalist id="custom-email-recipient-list"></datalist>
                    <div class="automation-inline-help">Comma-separated emails. You can use existing client emails or new ones.</div>
                </div>
                <div class="automation-field">
                    <label class="automation-label" for="custom-email-subject">Subject</label>
                    <input class="automation-input" id="custom-email-subject" type="text" placeholder="Quick check-in before Friday">
                </div>
                <div class="automation-field">
                    <label class="automation-label" for="custom-email-type">Mode</label>
                    <select class="automation-select automation-input" id="custom-email-type">
                        <option value="date">Date</option>
                        <option value="daily">Daily</option>
                    </select>
                </div>
                <div class="automation-field full">
                    <label class="automation-label" for="custom-email-range-display">Reporting Period</label>
                    <div class="scheduler-range">
                        <input
                            class="automation-input scheduler-range-input"
                            id="custom-email-range-display"
                            type="text"
                            placeholder="Select start and end date"
                            readonly
                        >
                        <button type="button" class="btn btn-secondary btn-sm" id="custom-email-range-toggle">
                            <i class="fas fa-calendar-alt"></i>
                            Pick
                        </button>
                        <div class="scheduler-range-panel" id="custom-email-range-panel">
                            <div class="automation-row">
                                <div class="automation-field">
                                    <label class="automation-label" for="custom-email-range-start">Start Date</label>
                                    <input class="automation-input" id="custom-email-range-start" type="date">
                                </div>
                                <div class="automation-field">
                                    <label class="automation-label" for="custom-email-range-end">End Date</label>
                                    <input class="automation-input" id="custom-email-range-end" type="date">
                                </div>
                            </div>
                            <div class="scheduler-range-actions">
                                <button type="button" class="btn btn-secondary btn-sm" id="custom-email-range-clear">Clear</button>
                                <button type="button" class="btn btn-primary btn-sm" id="custom-email-range-apply">Apply</button>
                            </div>
                        </div>
                    </div>
                    <div class="scheduler-template-row">
                        <select class="automation-select automation-input" id="custom-email-range-template" style="max-width:280px;">
                            <option value="reporting">Reporting period: {start} – {end}</option>
                            <option value="window">Report window ({start} to {end})</option>
                            <option value="plain">{start} - {end}</option>
                        </select>
                        <button type="button" class="btn btn-secondary btn-sm" id="custom-email-range-insert">
                            <i class="fas fa-plus"></i>
                            Insert Into Message
                        </button>
                    </div>
                </div>
                <div class="automation-field full">
                    <label class="automation-label" for="custom-email-body">Message</label>
                    <textarea class="automation-input automation-textarea" id="custom-email-body" placeholder="Add the email body here..."></textarea>
                </div>
                <div class="automation-field">
                    <label class="automation-label" for="custom-email-date">Date</label>
                    <input class="automation-input" id="custom-email-date" type="date">
                </div>
                <div class="automation-field">
                    <label class="automation-label" for="custom-email-time">Send Time</label>
                    <input class="automation-input automation-time-input" id="custom-email-time" type="time" value="09:00">
                </div>
                <div class="automation-field full">
                    <label class="automation-label">Working Days</label>
                    <div class="automation-day-tags" id="custom-email-working-days">
                        <button type="button" class="automation-day-tag active" data-working-day="mon">Mon</button>
                        <button type="button" class="automation-day-tag active" data-working-day="tue">Tue</button>
                        <button type="button" class="automation-day-tag active" data-working-day="wed">Wed</button>
                        <button type="button" class="automation-day-tag active" data-working-day="thu">Thu</button>
                        <button type="button" class="automation-day-tag active" data-working-day="fri">Fri</button>
                        <button type="button" class="automation-day-tag" data-working-day="sat">Sat</button>
                        <button type="button" class="automation-day-tag" data-working-day="sun">Sun</button>
                    </div>
                </div>
                <div class="automation-field full scheduler-actions-right">
                    <label class="automation-enabled-wrap">
                        <input type="checkbox" id="custom-email-enabled" checked>
                        Enabled
                    </label>
                    <button type="button" class="btn btn-primary btn-sm" id="custom-email-submit">
                        <i class="fas fa-calendar-check"></i>
                        Schedule
                    </button>
                    <button type="button" class="btn btn-secondary btn-sm" id="custom-email-reset">
                        <i class="fas fa-undo"></i>
                        Clear
                    </button>
                </div>
            </div>

            <div id="custom-email-message" class="automation-test-message"></div>
        </div>

        <div class="automation-card">
            <div class="automation-card-head">
                <div>
                    <div class="automation-client-name">Scheduled Emails</div>
                    <div class="automation-client-email">Cancel or reschedule anytime.</div>
                </div>
                <button type="button" class="btn btn-secondary btn-sm" id="custom-email-refresh">
                    <i class="fas fa-rotate"></i>
                    Refresh
                </button>
            </div>
            <div id="custom-email-schedule-list" class="scheduler-table-wrap">
                <div class="automation-empty-row">Loading scheduled emails...</div>
            </div>
        </div>
    </div>
</div>
