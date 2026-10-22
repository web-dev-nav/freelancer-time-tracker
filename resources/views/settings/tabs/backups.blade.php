<div class="settings-section">
    <div class="section-header"><h2><i class="fas fa-database" aria-hidden="true"></i> Automatic database backups</h2><p>Keep a daily copy of every organization, time entry, invoice, and setting.</p></div>
    <div id="backup-schedule-status" class="alert alert-info" role="status">Loading backup schedule…</div>
    <div class="form-group"><label class="checkbox-label"><input type="checkbox" id="backup-enabled"><span>Enable daily database backup</span></label></div>
    <div class="form-row">
        <div class="form-group"><label class="form-label" for="backup-time">Daily backup time</label><input type="time" id="backup-time" class="form-control" value="00:00"><small class="text-muted">Time zone: {{ config('app.timezone') }}. A missed run is picked up when the scheduler resumes.</small></div>
        <div class="form-group"><label class="form-label" for="backup-retention">Keep backups for (days)</label><input type="number" id="backup-retention" class="form-control" min="7" max="365" value="30"><small class="text-muted">Keep 7–365 days. Old full backups are removed only after a successful new backup.</small></div>
    </div>
    <dl class="backup-health"><dt>Last successful backup</dt><dd id="backup-last-success">Not recorded yet</dd><dt>Next scheduled backup</dt><dd id="backup-next-run">—</dd><dt>Scheduler last checked in</dt><dd id="backup-heartbeat">Not recorded yet</dd></dl>
    <p id="backup-last-error" class="text-danger" role="status" hidden></p>
    <div class="backup-actions"><button type="button" class="btn btn-primary" id="save-backup-schedule">Save backup schedule</button><button type="button" class="btn btn-secondary" id="refresh-backup-schedule">Refresh status</button><a href="{{ route('timesheet.index') }}" class="btn btn-secondary" id="open-backups-link">View backups / create one now</a></div>
    <div class="backup-cron-help"><h3>Keep the scheduler running</h3><p>Invoice delivery, reports, and backups need the server scheduler to run every minute. Saving a schedule does not install the host cron job.</p><code>* * * * * cd {{ base_path() }} &amp;&amp; php artisan schedule:run &gt;&gt; /dev/null 2&gt;&amp;1</code><p>Separate organization exports continue on the existing two-day schedule. The full database backup already includes all organizations.</p></div>
</div>
