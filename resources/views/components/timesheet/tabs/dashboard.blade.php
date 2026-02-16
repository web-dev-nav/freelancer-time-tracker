{{-- Dashboard Tab Component --}}
{{-- Displays stats cards, active session card, and quick actions --}}
<div class="tab-content active" id="dashboard-tab">
    <div class="dashboard-grid">
        <!-- Stats Cards -->
        <div class="stats-container">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-value" id="today-hours">0:00</div>
                    <div class="stat-label">Today</div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-calendar-week"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-value" id="week-hours">0:00</div>
                    <div class="stat-label">This Week</div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-calendar-alt"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-value" id="month-hours">0:00</div>
                    <div class="stat-label">This Month</div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-list"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-value" id="total-sessions">0</div>
                    <div class="stat-label">Total Sessions</div>
                </div>
            </div>
        </div>

        <!-- Active Session Card -->
        @if(auth()->user()?->isAuthor())
            <div class="active-session-card hidden" id="active-session-card">
                <div class="session-header">
                    <h3>
                        <i class="fas fa-play-circle text-success"></i>
                        Active Session
                    </h3>
                    <div class="session-status">
                        <span class="status-indicator active"></span>
                        Working
                    </div>
                </div>
                <div class="session-details">
                    <div class="session-time">
                        <div class="session-start">
                            <label>Started:</label>
                            <span id="session-start-display">--</span>
                        </div>
                        <div class="session-duration">
                            <label>Duration:</label>
                            <span id="session-duration-display">0:00</span>
                        </div>
                    </div>
                </div>
                <div class="session-actions">
                    <button class="btn btn-warning" onclick="showClockOutModal()">
                        <i class="fas fa-stop"></i>
                        Clock Out
                    </button>
                    <button class="btn btn-danger" onclick="cancelActiveSession()">
                        <i class="fas fa-times"></i>
                        Cancel
                    </button>
                </div>
            </div>
        @endif

        <!-- Quick Actions -->
        @if(auth()->user()?->isAuthor())
            <div class="quick-actions">
                <h3 class="section-title">Quick Actions</h3>
                <div class="action-buttons">
                    <button class="action-btn" onclick="quickClockIn()">
                        <i class="fas fa-play"></i>
                        Quick Clock In
                    </button>
                    <button class="action-btn" onclick="showTab('reports')">
                        <i class="fas fa-download"></i>
                        Export Latest
                    </button>
                    <button class="action-btn" onclick="showTab('history')">
                        <i class="fas fa-eye"></i>
                        View Work History
                    </button>
                </div>
            </div>
        @endif
    </div>
</div>
