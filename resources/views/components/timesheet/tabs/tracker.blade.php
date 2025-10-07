{{-- Time Tracker Tab Component --}}
{{-- Clock in form and active session display --}}
<div class="tab-content" id="tracker-tab">
    <div class="tracker-container">
        <!-- Clock In Section -->
        <div class="clock-section" id="clock-in-section">
            <div class="clock-header">
                <h2>
                    <i class="fas fa-clock"></i>
                    Start Your Work Session
                </h2>
                <p>Enter your start time and begin tracking your work</p>
            </div>

            <form id="clock-in-form" class="clock-form">
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label" for="clock-in-date">
                            <i class="fas fa-calendar"></i>
                            Date
                        </label>
                        <input type="date" id="clock-in-date" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="clock-in-time">
                            <i class="fas fa-clock"></i>
                            Start Time
                        </label>
                        <input type="time" id="clock-in-time" class="form-control" required>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary btn-large">
                    <i class="fas fa-play"></i>
                    Clock In
                </button>
            </form>
        </div>

        <!-- Active Session Display -->
        <div class="active-session-display" id="active-session-display">
            <div class="session-visual">
                <div class="session-avatar">
                    <i class="fas fa-user-clock"></i>
                </div>
                <div class="session-info">
                    <h3>You're Currently Working</h3>
                    <div class="session-meta">
                        <span id="current-session-start">Started at --</span>
                        <span class="session-separator">•</span>
                        <span id="current-session-duration">Duration: 0:00</span>
                    </div>
                </div>
            </div>

            <div class="session-actions">
                <button class="btn btn-success btn-large" onclick="showClockOutModal()">
                    <i class="fas fa-stop"></i>
                    Clock Out
                </button>
            </div>
        </div>
    </div>
</div>
