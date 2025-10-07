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
export async function loadDashboardStats() {
    try {
        const url = State.selectedProjectId
            ? `/api/timesheet/dashboard-stats?project_id=${State.selectedProjectId}`
            : '/api/timesheet/dashboard-stats';
        const response = await window.api.request(url);
        State.setDashboardStats(response.stats);
        updateDashboardDisplay();
    } catch (error) {
        console.error('Failed to load dashboard stats:', error);
    }
}

/**
 * Update the dashboard display with current statistics
 */
export function updateDashboardDisplay() {
    if (State.dashboardStats.today) {
        document.getElementById('today-hours').textContent =
            window.utils.formatTime(Math.round(State.dashboardStats.today.hours * 60));
    }
    if (State.dashboardStats.this_week) {
        document.getElementById('week-hours').textContent =
            window.utils.formatTime(Math.round(State.dashboardStats.this_week.hours * 60));
    }
    if (State.dashboardStats.this_month) {
        document.getElementById('month-hours').textContent =
            window.utils.formatTime(Math.round(State.dashboardStats.this_month.hours * 60));
        document.getElementById('total-sessions').textContent = State.dashboardStats.this_month.sessions;
    }
}

/**
 * Check for an active session and update UI accordingly
 */
export async function checkActiveSession() {
    try {
        const response = await window.api.request('/api/timesheet/active-session');
        if (response.success) {
            State.setCurrentActiveSession(response.session);
            showActiveSessionUI();
            startSessionTimer();
        } else {
            hideActiveSessionUI();
        }
    } catch (error) {
        hideActiveSessionUI();
    }
}

/**
 * Show the active session UI elements
 */
export function showActiveSessionUI() {
    // Update dashboard
    const sessionCard = document.getElementById('active-session-card');
    sessionCard.classList.remove('hidden');

    // Update tracker
    document.getElementById('clock-in-section').classList.add('hidden');
    document.getElementById('active-session-display').classList.remove('hidden');

    updateActiveSessionDisplay();
}

/**
 * Hide the active session UI elements
 */
export function hideActiveSessionUI() {
    document.getElementById('active-session-card').classList.add('hidden');
    document.getElementById('clock-in-section').classList.remove('hidden');
    document.getElementById('active-session-display').classList.add('hidden');
    stopSessionTimer();
}

/**
 * Update the active session display with current session data
 */
export function updateActiveSessionDisplay() {
    if (!State.currentActiveSession) return;

    const startTime = new Date(State.currentActiveSession.clock_in);
    const startDisplay = Utils.formatDateTimeForDisplay(State.currentActiveSession.clock_in);

    document.getElementById('session-start-display').textContent =
        `${startDisplay.date} at ${startDisplay.time}`;
    document.getElementById('current-session-start').textContent =
        `Started at ${startDisplay.time}`;
}

/**
 * Start the session timer that updates the duration display
 */
export function startSessionTimer() {
    if (State.timerInterval) clearInterval(State.timerInterval);

    const interval = setInterval(() => {
        if (!State.currentActiveSession) return;

        // Parse the start time properly (it's stored in UTC)
        const startTime = new Date(State.currentActiveSession.clock_in);
        const now = new Date();

        // Calculate difference in minutes
        const diffMinutes = Math.floor((now - startTime) / (1000 * 60));

        // Ensure we don't show negative durations (in case of clock sync issues)
        const safeDiffMinutes = Math.max(0, diffMinutes);

        const formattedTime = window.utils.formatTime(safeDiffMinutes);
        document.getElementById('session-duration-display').textContent = formattedTime;
        document.getElementById('current-session-duration').textContent = 'Duration: ' + formattedTime;
    }, 1000);

    State.setTimerInterval(interval);
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
