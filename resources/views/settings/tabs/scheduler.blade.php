{{-- Scheduler Tab --}}

<style>
    .scheduler-shell {
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        background: linear-gradient(155deg, #ffffff 0%, #f8fafc 100%);
        box-shadow: 0 10px 28px rgba(15, 23, 42, 0.06);
        overflow: hidden;
    }

    .scheduler-header {
        padding: 20px 22px 16px;
        border-bottom: 1px solid #e2e8f0;
        background: linear-gradient(90deg, rgba(15, 118, 110, 0.08), rgba(59, 130, 246, 0.02));
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
        margin-bottom: 8px;
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

    .automation-compact-form {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
        gap: 10px;
        margin-top: 10px;
    }

    .automation-compact-form .full {
        grid-column: 1 / -1;
    }

    .automation-details summary {
        cursor: pointer;
        font-weight: 700;
        font-size: 13px;
        color: #0f172a;
        list-style: none;
    }

    .automation-details summary::-webkit-details-marker {
        display: none;
    }

    .automation-details summary::before {
        content: "▸";
        display: inline-block;
        margin-right: 6px;
        color: #64748b;
        transition: transform 0.2s ease;
    }

    .automation-details[open] summary::before {
        transform: rotate(90deg);
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

    .automation-schedule-list {
        display: grid;
        gap: 10px;
    }

    .automation-schedule-row {
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 10px 12px;
        background: #ffffff;
        display: grid;
        gap: 6px;
    }

    .automation-schedule-title {
        font-weight: 700;
        font-size: 13px;
        color: #0f172a;
    }

    .automation-schedule-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        font-size: 12px;
        color: #64748b;
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
        gap: 8px;
        flex-wrap: wrap;
    }

    .automation-empty-row {
        text-align: center;
        color: #64748b;
        padding: 24px 12px;
    }

    @media (max-width: 900px) {
        .automation-row {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="settings-section scheduler-shell">
    <div class="scheduler-header">
        <h2 class="scheduler-title">
            <i class="fas fa-calendar-check" style="color:#0f766e;"></i>
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

            <details class="automation-details" open>
                <summary>Compose</summary>
                <form id="custom-email-schedule-form" class="automation-compact-form">
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
                    <div class="automation-field full">
                        <label class="automation-label" for="custom-email-body">Message</label>
                        <textarea class="automation-input automation-textarea" id="custom-email-body" placeholder="Add the email body here..."></textarea>
                    </div>

                    <div class="automation-field">
                        <label class="automation-label" for="custom-email-type">Mode</label>
                        <select class="automation-select automation-input" id="custom-email-type">
                            <option value="date">Date</option>
                            <option value="daily">Daily</option>
                        </select>
                    </div>
                    <div class="automation-field">
                        <label class="automation-label" for="custom-email-time">Send Time</label>
                        <input class="automation-input automation-time-input" id="custom-email-time" type="time" value="09:00">
                    </div>
                    <div class="automation-field">
                        <label class="automation-label" for="custom-email-date">Date</label>
                        <input class="automation-input" id="custom-email-date" type="date">
                    </div>
                    <div class="automation-field">
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
                    <div class="automation-field">
                        <label class="automation-enabled-wrap">
                            <input type="checkbox" id="custom-email-enabled" checked>
                            Enabled
                        </label>
                    </div>
                    <div class="automation-field" style="display:flex;gap:8px;align-items:center;">
                        <button type="submit" class="btn btn-primary" id="custom-email-submit">
                            <i class="fas fa-calendar-check"></i>
                            Schedule Email
                        </button>
                        <button type="button" class="btn btn-secondary" id="custom-email-reset">
                            <i class="fas fa-undo"></i>
                            Clear
                        </button>
                    </div>
                </form>
            </details>

            <div id="custom-email-message" class="automation-test-message"></div>
        </div>

        <div class="automation-card">
            <div class="automation-card-head">
                <div>
                    <div class="automation-client-name">Scheduled Emails</div>
                    <div class="automation-client-email">Cancel or reschedule anytime.</div>
                </div>
                <button type="button" class="btn btn-secondary" id="custom-email-refresh">
                    <i class="fas fa-rotate"></i>
                    Refresh
                </button>
            </div>
            <div id="custom-email-schedule-list" class="automation-schedule-list">
                <div class="automation-empty-row">Loading scheduled emails...</div>
            </div>
        </div>
    </div>
</div>
