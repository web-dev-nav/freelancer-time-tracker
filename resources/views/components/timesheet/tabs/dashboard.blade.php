<div class="tab-content active" id="dashboard-tab">
    <div id="dashboard-feedback" class="dashboard-feedback" role="status" hidden></div>
    <div class="dashboard-grid">
        <div class="stats-container" aria-label="Time summary">
            @foreach([['today-hours', 'Today', 'clock', 'Hours tracked today'], ['week-hours', 'This week', 'calendar-week', 'Hours tracked this week'], ['month-hours', 'This month', 'calendar-alt', 'Hours tracked this month'], ['total-sessions', 'Sessions this month', 'list', 'Recorded work sessions']] as [$id, $label, $icon, $caption])
                <div class="stat-card"><div class="stat-content"><div class="stat-label">{{ $label }}</div><div class="stat-value" id="{{ $id }}">—</div><div class="metric-caption">{{ $caption }}</div></div><div class="stat-icon"><i class="fas fa-{{ $icon }}" aria-hidden="true"></i></div></div>
            @endforeach
        </div>
        @if(auth()->user()?->isAuthor())
            <section class="focus-card" id="idle-session-card">
                <div class="panel-heading"><span class="eyebrow">TIME TRACKER</span><span class="neutral-badge">Ready when you are</span></div>
                <div class="timer-face" aria-hidden="true">0:00<span>:00</span></div>
                <h2>Make time for good work.</h2><p>Choose your project above, then start your timer. Add a work summary when you finish.</p>
                <button class="btn btn-primary" id="start-timer-btn" disabled onclick="quickClockIn()"><i class="fas fa-play" aria-hidden="true"></i> Start timer</button>
                <button class="btn btn-secondary" onclick="createNewEntry()"><i class="fas fa-plus" aria-hidden="true"></i> Add time manually</button>
                <p class="timer-hint" id="timer-project-hint">Loading projects…</p>
            </section>
            <section class="active-session-card hidden focus-card" id="active-session-card">
                <div class="panel-heading"><span class="eyebrow">TIME TRACKER</span><span class="session-status"><span class="status-indicator active"></span> Timer running</span></div>
                <div class="timer-face" id="session-duration-display" aria-label="Session duration">0:00</div>
                <h2>You're on the clock.</h2><p>Started <span id="session-start-display">—</span>. Your session stays active when you leave this page.</p>
                <div class="session-actions"><button class="btn btn-primary" onclick="showClockOutModal()"><i class="fas fa-stop" aria-hidden="true"></i> Finish &amp; save time</button><button class="btn btn-secondary" onclick="cancelActiveSession()">Discard session</button></div>
            </section>
        @endif
        <section class="quick-actions"><div class="panel-heading"><h2>Keep work moving</h2><i class="fas fa-arrow-right" aria-hidden="true"></i></div>
            <div class="action-buttons">
                <button class="action-btn" data-dashboard-destination="history" onclick="showTab('history')"><span class="action-icon"><i class="fas fa-history" aria-hidden="true"></i></span><span><strong>Review your time</strong><small>Check entries and work descriptions</small></span><i class="fas fa-chevron-right" aria-hidden="true"></i></button>
                <button class="action-btn" data-dashboard-destination="reports" onclick="showTab('reports')"><span class="action-icon"><i class="fas fa-chart-bar" aria-hidden="true"></i></span><span><strong>Build a timesheet</strong><small>Choose a period and export your hours</small></span><i class="fas fa-chevron-right" aria-hidden="true"></i></button>
                <button class="action-btn" data-dashboard-destination="invoices" onclick="showTab('invoices')"><span class="action-icon"><i class="fas fa-file-invoice-dollar" aria-hidden="true"></i></span><span><strong>Stay on top of billing</strong><small>View invoices and payment status</small></span><i class="fas fa-chevron-right" aria-hidden="true"></i></button>
            </div>
        </section>
        <section class="recent-work" id="recent-work-panel"><div class="panel-heading"><div><h2>Recent work</h2><p>Your latest entries for this project</p></div><button class="btn btn-secondary btn-sm" onclick="showTab('history')">View all <i class="fas fa-arrow-right" aria-hidden="true"></i></button></div><div id="recent-work-list" aria-live="polite"><p class="empty-state">Loading recent work…</p></div></section>
    </div>
</div>
