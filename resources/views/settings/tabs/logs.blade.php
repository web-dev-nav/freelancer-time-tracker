{{-- Logs Tab --}}
<style>
    .logs-toolbar {
        display: flex;
        gap: 8px;
        align-items: center;
        justify-content: flex-end;
        flex-wrap: wrap;
    }

    .logs-toolbar .btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        white-space: nowrap;
    }

    .logs-toolbar-btn {
        min-width: 110px;
    }

    .logs-toolbar-btn-sm {
        padding: 7px 10px;
        font-size: 12px;
        line-height: 1.1;
    }

    .logs-output {
        margin: 0;
        background: #0b1220;
        color: #e2e8f0;
        border: 1px solid #1e293b;
        border-radius: 10px;
        max-height: 58vh;
        overflow: auto;
        padding: 12px;
        font-size: 12px;
        line-height: 1.55;
        font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
        white-space: pre-wrap;
        word-break: break-word;
        tab-size: 4;
    }

    .log-line {
        padding: 4px 6px;
        border-radius: 6px;
        margin-bottom: 2px;
    }

    .log-line.parsed {
        display: grid;
        grid-template-columns: 190px 92px minmax(0, 1fr);
        gap: 8px;
        align-items: start;
    }

    .log-line .log-ts {
        color: #93c5fd;
        white-space: nowrap;
    }

    .log-line .log-level {
        font-weight: 700;
        white-space: nowrap;
        text-transform: uppercase;
        letter-spacing: .03em;
    }

    .log-line .log-msg {
        white-space: pre-wrap;
        overflow-wrap: anywhere;
    }

    .log-line.level-error,
    .log-line.level-critical,
    .log-line.level-alert,
    .log-line.level-emergency {
        background: rgba(239, 68, 68, 0.12);
    }

    .log-line.level-error .log-level,
    .log-line.level-critical .log-level,
    .log-line.level-alert .log-level,
    .log-line.level-emergency .log-level {
        color: #fca5a5;
    }

    .log-line.level-warning,
    .log-line.level-notice {
        background: rgba(245, 158, 11, 0.12);
    }

    .log-line.level-warning .log-level,
    .log-line.level-notice .log-level {
        color: #fcd34d;
    }

    .log-line.level-info {
        background: rgba(56, 189, 248, 0.10);
    }

    .log-line.level-info .log-level {
        color: #7dd3fc;
    }

    .log-line.level-debug {
        background: rgba(148, 163, 184, 0.10);
    }

    .log-line.level-debug .log-level {
        color: #cbd5e1;
    }

    .log-line.stack-line {
        display: block;
        color: #94a3b8;
        padding-left: 18px;
        background: rgba(15, 23, 42, 0.35);
    }

    @media (max-width: 740px) {
        .logs-toolbar {
            justify-content: stretch;
        }

        .logs-toolbar .btn {
            flex: 1 1 140px;
        }
    }
</style>

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

        <div class="logs-toolbar">
            <button type="button" id="refresh-logs-btn" class="btn btn-secondary logs-toolbar-btn">
                <i class="fas fa-rotate"></i>
                Refresh
            </button>
            <button type="button" id="copy-logs-btn" class="btn btn-secondary logs-toolbar-btn logs-toolbar-btn-sm">
                <i class="fas fa-copy"></i>
                Copy Logs
            </button>
            <button type="button" id="delete-log-file-btn" class="btn btn-danger logs-toolbar-btn logs-toolbar-btn-sm">
                <i class="fas fa-trash"></i>
                Delete File
            </button>
        </div>
    </div>

    <div id="logs-status" style="display:none;margin-bottom:10px;padding:8px 10px;border-radius:8px;font-size:12px;"></div>
    <div id="logs-meta" style="margin-bottom:8px;color:var(--text-secondary);font-size:12px;"></div>

    <div id="logs-output" class="logs-output">Open this tab and click Refresh to load logs.</div>
</div>
