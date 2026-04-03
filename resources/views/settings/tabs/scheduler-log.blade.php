<div class="settings-section">
    <div class="automation-card">
        <div class="automation-card-head">
            <div>
                <div class="automation-client-name">Scheduled Emails</div>
                <div class="automation-client-email">Manage and cancel your scheduled automations.</div>
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

    <div class="automation-card" style="margin-top:12px;">
        <div class="automation-card-head">
            <div>
                <div class="automation-client-name">Scheduler Log</div>
                <div class="automation-client-email">Historical record of every automated run, send, reminder, and skip.</div>
            </div>
            <button type="button" class="btn btn-secondary btn-sm" id="scheduler-log-refresh">
                <i class="fas fa-rotate"></i>
                Refresh
            </button>
        </div>

        <div id="scheduler-log-list" class="scheduler-table-wrap">
            <div class="automation-empty-row">Loading scheduler log entries...</div>
        </div>
        <div id="scheduler-log-pagination" style="margin-top:10px; display:flex; justify-content:flex-end; gap:6px;"></div>
    </div>

    <div class="automation-card" style="margin-top:12px;">
        <div class="automation-card-head">
            <div>
                <div class="automation-client-name">Upcoming Schedule Timeline</div>
                <div class="automation-client-email">Next run times for every custom email, invoice send, and activity report.</div>
            </div>
            <button type="button" class="btn btn-secondary btn-sm" id="upcoming-schedules-refresh">
                <i class="fas fa-rotate"></i>
                Refresh Timeline
            </button>
        </div>

        <div id="upcoming-schedules-list" class="scheduler-table-wrap">
            <div class="automation-empty-row">Loading upcoming schedules...</div>
        </div>
    </div>
</div>
