/**
 * Dashboard Module
 *
 * Handles dashboard statistics display and active session management.
 * Includes functions for loading stats, updating displays, and managing active sessions.
 */

import * as State from './state.js';
import * as Utils from './utils.js';

/**
 * Load dashboard statistics from the API
 * Filters by selected project if one is set
 */
let dashboardRequest = 0;

export async function loadDashboardStats() {
    const requestId = ++dashboardRequest;
    const feedback = document.getElementById('dashboard-feedback');
    if (feedback) feedback.hidden = true;
    updateProjectHint();
    loadRecentWork();
    try {
        const url = State.selectedProjectId
            ? `/api/timesheet/dashboard-stats?project_id=${State.selectedProjectId}`
            : '/api/timesheet/dashboard-stats';
        const response = await window.api.request(url);
        if (requestId !== dashboardRequest) return;
        State.setDashboardStats(response.stats);
        updateDashboardDisplay();

        // Update active session state and UI
        if (response.stats.active_session) {
            State.setCurrentActiveSession(response.stats.active_session);
            showActiveSessionUI();
            startSessionTimer();
        } else {
            State.setCurrentActiveSession(null);
            hideActiveSessionUI();
        }
    } catch (error) {
        if (requestId !== dashboardRequest) return;
        if (feedback) {
            feedback.textContent =
                'Could not load your time summary. Check your connection and reopen Overview to retry.';
            feedback.hidden = false;
        }
        console.error('Failed to load dashboard stats:', error);
    }
}

/**
 * Update the dashboard display with current statistics
 */
export function updateDashboardDisplay() {
    if (State.dashboardStats.today) {
        document.getElementById('today-hours').textContent =
            window.utils.formatTime(
                Math.round(State.dashboardStats.today.hours * 60),
            );
    }
    if (State.dashboardStats.this_week) {
        document.getElementById('week-hours').textContent =
            window.utils.formatTime(
                Math.round(State.dashboardStats.this_week.hours * 60),
            );
    }
    if (State.dashboardStats.this_month) {
        document.getElementById('month-hours').textContent =
            window.utils.formatTime(
                Math.round(State.dashboardStats.this_month.hours * 60),
            );
        document.getElementById('total-sessions').textContent =
            State.dashboardStats.this_month.sessions;
    }
}

/**
 * Check for an active session and update UI accordingly
 */
export async function checkActiveSession() {
    try {
        const response = await fetch('/api/timesheet/active-session', {
            headers: {
                'Content-Type': 'application/json',
                Accept: 'application/json',
                'X-CSRF-TOKEN': window.csrfToken,
                'X-Requested-With': 'XMLHttpRequest',
            },
        });

        if (response.ok) {
            const data = await response.json();
            if (data.success) {
                State.setCurrentActiveSession(data.session);
                showActiveSessionUI();
                startSessionTimer();
                return;
            }
        }

        // No active session (404 or any other status)
        State.setCurrentActiveSession(null);
        // Only try to update UI if the dashboard tab exists
        if (document.getElementById('active-session-card')) {
            hideActiveSessionUI();
        }
    } catch (error) {
        // Network error or parsing error - just clear the state silently
        State.setCurrentActiveSession(null);
        if (document.getElementById('active-session-card')) {
            hideActiveSessionUI();
        }
    }
}

/**
 * Show the active session UI elements
 */
export function showActiveSessionUI() {
    // Update dashboard
    const sessionCard = document.getElementById('active-session-card');
    if (sessionCard) {
        sessionCard.classList.remove('hidden');
    }

    document.getElementById('idle-session-card')?.classList.add('hidden');

    // Update tracker tab elements (if they exist)
    const clockInSection = document.getElementById('clock-in-section');
    const activeSessionDisplay = document.getElementById(
        'active-session-display',
    );

    if (clockInSection) {
        clockInSection.classList.add('hidden');
    }
    if (activeSessionDisplay) {
        activeSessionDisplay.classList.remove('hidden');
    }

    updateActiveSessionDisplay();
}

/**
 * Hide the active session UI elements
 */
export function hideActiveSessionUI() {
    // Safely get elements - they might not exist if dashboard isn't loaded yet
    const sessionCard = document.getElementById('active-session-card');
    const clockInSection = document.getElementById('clock-in-section');
    const activeSessionDisplay = document.getElementById(
        'active-session-display',
    );

    // Only manipulate if element exists
    if (sessionCard) {
        sessionCard.classList.add('hidden');
    }
    if (clockInSection) {
        clockInSection.classList.remove('hidden');
    }
    if (activeSessionDisplay) {
        activeSessionDisplay.classList.add('hidden');
    }

    document.getElementById('idle-session-card')?.classList.remove('hidden');
    updateProjectHint();
    stopSessionTimer();
}

/**
 * Update the active session display with current session data
 */
export function updateActiveSessionDisplay() {
    if (!State.currentActiveSession) return;

    const session = State.currentActiveSession;
    const startDisplay = Utils.formatDateTimeForDisplay(session.clock_in);
    const formattedDate = session.clock_in_display_date || startDisplay.date;
    const formattedTime = session.clock_in_time || startDisplay.time;

    const sessionStartDisplay = document.getElementById(
        'session-start-display',
    );
    const currentSessionStart = document.getElementById(
        'current-session-start',
    );

    if (sessionStartDisplay) {
        sessionStartDisplay.textContent = `${formattedDate} at ${formattedTime}`;
    }
    if (currentSessionStart) {
        currentSessionStart.textContent = `Started at ${formattedTime}`;
    }
}

/**
 * Start the session timer that updates the duration display
 */
export function startSessionTimer() {
    if (State.timerInterval) clearInterval(State.timerInterval);

    const updateTimer = () => {
        if (!State.currentActiveSession) return;

        // Parse the start time properly (it's stored in UTC)
        const startTime = new Date(State.currentActiveSession.clock_in);
        const now = new Date();

        // Calculate difference in minutes
        const diffMinutes = Math.floor((now - startTime) / (1000 * 60));

        // Ensure we don't show negative durations (in case of clock sync issues)
        const safeDiffMinutes = Math.max(0, diffMinutes);

        const formattedTime = window.utils.formatTime(safeDiffMinutes);

        const sessionDurationDisplay = document.getElementById(
            'session-duration-display',
        );
        const currentSessionDuration = document.getElementById(
            'current-session-duration',
        );

        if (sessionDurationDisplay) {
            sessionDurationDisplay.textContent = `${formattedTime}:${String(Math.max(0, Math.floor((now - startTime) / 1000)) % 60).padStart(2, '0')}`;
        }
        if (currentSessionDuration) {
            currentSessionDuration.textContent = 'Duration: ' + formattedTime;
        }
    };
    updateTimer();
    State.setTimerInterval(setInterval(updateTimer, 1000));
}

/**
 * Stop the session timer
 */
export function stopSessionTimer() {
    if (State.timerInterval) {
        clearInterval(State.timerInterval);
        State.setTimerInterval(null);
    }
}

function updateProjectHint() {
    const hint = document.getElementById('timer-project-hint');
    const button = document.getElementById('start-timer-btn');
    const project = State.allProjects.find(
        (item) => String(item.id) === String(State.selectedProjectId),
    );
    if (hint)
        hint.textContent = project
            ? `Tracking for ${project.name}`
            : 'Choose or create a project to start tracking.';
    if (button) button.disabled = !project;
}

let recentRequest = 0;
async function loadRecentWork() {
    const list = document.getElementById('recent-work-list');
    if (!list) return;
    const requestId = ++recentRequest;
    const project = State.allProjects.find(
        (item) => String(item.id) === String(State.selectedProjectId),
    );
    if (
        window.currentUser?.role === 'client' &&
        (!project || project.client_can_access_history === false)
    ) {
        list.replaceChildren();
        return;
    }
    const message = (text) => {
        list.replaceChildren();
        const paragraph = document.createElement('p');
        paragraph.className = 'empty-state';
        paragraph.textContent = text;
        list.append(paragraph);
    };
    message('Loading recent work…');
    try {
        const params = new URLSearchParams({ per_page: 4, page: 1 });
        if (State.selectedProjectId)
            params.set('project_id', State.selectedProjectId);
        const response = await window.api.request(
            `/api/timesheet/history?${params}`,
        );
        if (requestId !== recentRequest) return;
        if (!response.success) throw new Error('History unavailable');
        const entries = response.data.items;
        if (!entries.length) {
            message('No entries yet. Your tracked work will appear here.');
            return;
        }
        list.replaceChildren();
        entries.forEach((entry) => {
            const row = document.createElement('div');
            row.className = 'recent-work-row';
            const details = document.createElement('div');
            const title = document.createElement('strong');
            title.textContent =
                Utils.htmlToPlainText(entry.work_description || '') ||
                (entry.clock_out ? 'Work session' : 'Session in progress');
            const date = document.createElement('small');
            date.textContent = `${entry.clock_in_display_date || window.utils.formatDate(entry.clock_in)} · ${entry.clock_in_time || window.utils.formatTimeForDisplay(entry.clock_in)}`;
            const duration = document.createElement('span');
            duration.textContent = entry.clock_out
                ? entry.formatted_duration ||
                  window.utils.formatTime(entry.total_minutes || 0)
                : 'Running';
            details.append(title, date);
            row.append(details, duration);
            list.append(row);
        });
    } catch (_) {
        if (requestId === recentRequest)
            message(
                'Recent work is unavailable. Open Work History to try again.',
            );
    }
}
