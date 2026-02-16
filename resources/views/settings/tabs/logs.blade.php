{{-- Logs Tab --}}

<div class="settings-section">
    <div class="section-header">
        <h2><i class="fas fa-file-lines"></i> Application Logs</h2>
        <p>View recent Laravel logs directly here for quick debugging.</p>
    </div>

    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:10px;align-items:end;margin-bottom:12px;">
        <div class="form-group" style="margin-bottom:0;">
            <label class="form-label" for="logs-file-select">Log File</label>
            <select id="logs-file-select" class="form-control">
                <option value="">Loading files...</option>
            </select>
        </div>

        <div class="form-group" style="margin-bottom:0;">
            <label class="form-label" for="logs-lines-select">Lines</label>
            <select id="logs-lines-select" class="form-control">
                <option value="200">200</option>
                <option value="400" selected>400</option>
                <option value="800">800</option>
                <option value="1500">1500</option>
                <option value="3000">3000</option>
            </select>
        </div>

        <div class="form-group" style="margin-bottom:0;">
            <label class="form-label" for="logs-level-select">Level</label>
            <select id="logs-level-select" class="form-control">
                <option value="all" selected>All</option>
                <option value="error">Error</option>
                <option value="warning">Warning</option>
                <option value="info">Info</option>
                <option value="debug">Debug</option>
            </select>
        </div>

        <div style="display:flex;gap:8px;align-items:center;">
            <button type="button" id="refresh-logs-btn" class="btn btn-secondary">
                <i class="fas fa-rotate"></i>
                Refresh
            </button>
        </div>
    </div>

    <div id="logs-status" style="display:none;margin-bottom:10px;padding:8px 10px;border-radius:8px;font-size:12px;"></div>
    <div id="logs-meta" style="margin-bottom:8px;color:var(--text-secondary);font-size:12px;"></div>

    <pre id="logs-output" style="margin:0;background:#0f172a;color:#e2e8f0;border:1px solid #1e293b;border-radius:10px;max-height:58vh;overflow:auto;padding:12px;font-size:12px;line-height:1.45;white-space:pre-wrap;word-break:break-word;">Open this tab and click Refresh to load logs.</pre>
</div>
