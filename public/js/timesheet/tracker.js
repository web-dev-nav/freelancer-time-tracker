/**
 * Time Tracker Module
 *
 * Handles time tracking operations including clock in/out, active session management,
 * and session cancellation.
 */

import * as State from './state.js';
import * as Utils from './utils.js';
import { loadDashboardStats, showActiveSessionUI, hideActiveSessionUI, startSessionTimer } from './dashboard.js';

/**
 * Clock in with specified date and time
 */
export async function clockIn() {
    const date = document.getElementById('clock-in-date').value;
    const time = document.getElementById('clock-in-time').value;

    if (!date || !time) {
        window.notify.error('Please fill in both date and time');
        return;
    }

    if (!State.selectedProjectId) {
        window.notify.error('Please select a project first');
        return;
    }

    try {
        const response = await window.api.request('/api/timesheet/clock-in', {
            method: 'POST',
            body: JSON.stringify({
                date: date,
                time: time,
                project_id: State.selectedProjectId
            })
        });

        if (response.success) {
            State.setCurrentActiveSession(response.session);
            showActiveSessionUI();
            startSessionTimer();
            loadDashboardStats();
            window.notify.success('Successfully clocked in!');
        } else {
            if (response.active_session) {
                State.setCurrentActiveSession(response.active_session);
                showActiveSessionUI();
                startSessionTimer();
            }
            window.notify.error(response.message);
        }
    } catch (error) {
        window.notify.error('Failed to clock in: ' + error.message);
    }
}

/**
 * Quick clock in with current date and time
 */
export async function quickClockIn() {
    const now = Utils.getCurrentDateTime();
    document.getElementById('clock-in-date').value = now.date;
    document.getElementById('clock-in-time').value = now.time;
    await clockIn();
}

/**
 * Show the clock out modal
 */
export function showClockOutModal() {
    if (!State.currentActiveSession) {
        window.notify.error('No active session found');
        return;
    }

    const now = Utils.getCurrentDateTime();
    document.getElementById('clock-out-time').value = now.time;

    document.getElementById('clock-out-modal').classList.add('show');
    document.getElementById('modal-overlay').classList.add('show');
    document.body.style.overflow = 'hidden';
}

/**
 * Hide the clock out modal
 */
export function hideClockOutModal() {
    document.getElementById('clock-out-modal').classList.remove('show');
    document.getElementById('modal-overlay').classList.remove('show');
    document.body.style.overflow = 'auto';

    // Clear form
    document.getElementById('clock-out-form').reset();
}

/**
 * Clock out the current active session
 */
export async function clockOut() {
    const time = document.getElementById('clock-out-time').value;
    const description = document.getElementById('work-description').value.trim();

    if (!time || !description) {
        window.notify.error('Please fill in end time and work description');
        return;
    }

    try {
        const response = await window.api.request('/api/timesheet/clock-out', {
            method: 'POST',
            body: JSON.stringify({
                session_id: State.currentActiveSession.session_id,
                time: time,
                work_description: description
            })
        });

        if (response.success) {
            State.setCurrentActiveSession(null);
            hideActiveSessionUI();
            hideClockOutModal();
            loadDashboardStats();
            window.notify.success(`Successfully clocked out! Duration: ${window.utils.formatTime(response.session.total_minutes)}`);
        } else {
            window.notify.error(response.message);
        }
    } catch (error) {
        window.notify.error('Failed to clock out: ' + error.message);
    }
}

/**
 * Cancel the current active session
 */
export async function cancelActiveSession() {
    if (!confirm('Are you sure you want to cancel the current session? This will delete the session data.')) {
        return;
    }

    try {
        const response = await window.api.request('/api/timesheet/cancel-session', {
            method: 'DELETE'
        });

        if (response.success) {
            State.setCurrentActiveSession(null);
            hideActiveSessionUI();
            loadDashboardStats();
            window.notify.success('Session cancelled successfully');
        } else {
            window.notify.error(response.message);
        }
    } catch (error) {
        // If there's no active session found, still clear the UI state
        if (error.message.includes('No active session found')) {
            State.setCurrentActiveSession(null);
            hideActiveSessionUI();
            loadDashboardStats();
            window.notify.info('No active session to cancel. Refreshing display...');
        } else {
            window.notify.error('Failed to cancel session: ' + error.message);
        }
    }
}
