<div class="settings-section">
    <div class="automation-card">
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

        <div class="automation-row" style="margin-bottom:12px; gap:10px;">
            <div style="flex:1;">
                <label class="automation-label" for="scheduler-log-source">Source</label>
                <select id="scheduler-log-source" class="automation-select automation-input">
                    <option value="">All</option>
                </select>
            </div>
            <div style="flex:1;">
                <label class="automation-label" for="scheduler-log-type">Type</label>
                <select id="scheduler-log-type" class="automation-select automation-input">
                    <option value="">All</option>
                </select>
            </div>
            <div style="flex:1;">
                <label class="automation-label" for="scheduler-log-status">Status</label>
                <select id="scheduler-log-status" class="automation-select automation-input">
                    <option value="">All</option>
                    <option value="scheduled">Scheduled</option>
                    <option value="sent">Sent</option>
                    <option value="skipped">Skipped</option>
                    <option value="error">Error</option>
                </select>
            </div>
            <div style="flex:2;">
                <label class="automation-label" for="scheduler-log-search">Search</label>
                <input type="text" id="scheduler-log-search" class="automation-input" placeholder="Filter by name, detail, message">
            </div>
        </div>

        <div id="scheduler-log-list" class="scheduler-table-wrap">
            <div class="automation-empty-row">Loading scheduler log entries...</div>
        </div>
        <div id="scheduler-log-pagination" style="margin-top:10px; display:flex; justify-content:flex-end; gap:6px;"></div>
    </div>
</div>
