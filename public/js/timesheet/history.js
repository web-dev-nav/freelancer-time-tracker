/**
 * History Module
 *
 * Manages the history tab functionality including loading, editing, deleting time logs,
 * and pagination controls.
 */

import * as State from './state.js';
import * as Utils from './utils.js';
import { loadDashboardStats } from './dashboard.js';
import { getEditorHtml, getEditorPlainText, setEditorContent, clearEditorContent, getSelectedText, replaceSelectedText, onEditorChange } from './rich-text.js';

const AUTO_SAVE_DELAY_MS = 1200;
let autoSaveTimeoutId = null;
let autoSaveInFlight = false;
let autoSaveLastFingerprint = '';
let autoSaveListenersBound = false;
let improveButtonSelectionBound = false;

let timeInputsInitialized = false;

function escapeHtml(text) {
    return String(text)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#39;');
}

function formatPlainDescriptionForDetails(rawText) {
    const normalized = String(rawText || '')
        .replace(/\r\n|\r/g, '\n')
        .replace(/([a-z0-9])\.([A-Z])/g, '$1. $2')
        .trim();

    if (!normalized) {
        return '';
    }

    const withSectionBreaks = normalized.replace(/\b(Completed:|Next Steps:)\s*/gi, (match, label, offset) => {
        const safeLabel = label.endsWith(':') ? label : `${label}:`;
        if (offset === 0) {
            return `<strong>${safeLabel}</strong> `;
        }
        return `<br><br><strong>${safeLabel}</strong> `;
    });

    return escapeHtml(withSectionBreaks)
        .replace(/&lt;strong&gt;/g, '<strong>')
        .replace(/&lt;\/strong&gt;/g, '</strong>')
        .replace(/\n/g, '<br>');
}

function formatDescriptionForDetails(rawDescription) {
    const raw = String(rawDescription || '');
    if (!raw.trim()) {
        return '';
    }

    const looksLikeHtml = /<[^>]+>/.test(raw);
    if (looksLikeHtml) {
        return Utils.sanitizeRichTextHtml(raw);
    }

    return formatPlainDescriptionForDetails(raw);
}

function normalizeTimeInputValue(raw) {
    const digits = String(raw || '').replace(/\D/g, '').slice(0, 4);
    if (digits.length <= 2) return digits;
    return `${digits.slice(0, 2)}:${digits.slice(2)}`;
}

function isValidTimeValue(value) {
    return /^([01]\d|2[0-3]):[0-5]\d$/.test(String(value || '').trim());
}

function ensureTimeInputsInitialized() {
    if (timeInputsInitialized) return;

    const timeInputIds = ['edit-clock-in-time', 'edit-clock-out-time'];
    timeInputIds.forEach((id) => {
        const input = document.getElementById(id);
        if (!input) return;

        input.addEventListener('input', () => {
            const normalized = normalizeTimeInputValue(input.value);
            if (input.value !== normalized) {
                input.value = normalized;
            }
        });
    });

    timeInputsInitialized = true;
}

function addMinutesToTime(time, minutesToAdd) {
    const [hours = '00', minutes = '00'] = String(time || '00:00').split(':');
    const baseMinutes = (parseInt(hours, 10) * 60) + parseInt(minutes, 10);
    const normalizedMinutes = ((baseMinutes + minutesToAdd) % (24 * 60) + (24 * 60)) % (24 * 60);
    const resultHours = String(Math.floor(normalizedMinutes / 60)).padStart(2, '0');
    const resultMinutes = String(normalizedMinutes % 60).padStart(2, '0');

    return `${resultHours}:${resultMinutes}`;
}

/**
 * Load history with pagination
 * @param {number} page - Page number to load
 * @param {number} perPage - Items per page
 */
export async function loadHistory(page = 1, perPage = null) {
    const tbody = document.getElementById('history-tbody');
    const isAuthor = window.currentUser?.role === 'author';
    const columns = isAuthor ? 6 : 5;

    try {
        State.setCurrentPage(page);
        if (perPage) State.setCurrentPerPage(perPage);

        tbody.innerHTML = `<tr><td colspan="${columns}" class="text-center"><div class="loading"><div class="spinner"></div>Loading history...</div></td></tr>`;

        const url = State.selectedProjectId
            ? `/api/timesheet/history?page=${State.currentPage}&per_page=${State.currentPerPage}&project_id=${State.selectedProjectId}`
            : `/api/timesheet/history?page=${State.currentPage}&per_page=${State.currentPerPage}`;
        const response = await window.api.request(url);

        if (response.success && response.data.items.length > 0) {
            tbody.innerHTML = '';

            response.data.items.forEach(log => {
                const row = tbody.insertRow();

                try {
                    // Prefer backend-supplied timezone-aware fields when available
                    const clockInDisplay = log.clock_in_time || window.utils.formatTimeForDisplay(log.clock_in);
                    const clockOutDisplay = log.clock_out
                        ? (log.clock_out_time || window.utils.formatTimeForDisplay(log.clock_out))
                        : '-';
                    const formattedDuration = log.formatted_duration || (log.total_minutes ? window.utils.formatTime(log.total_minutes) : '-');
                    const workDescription = log.work_description || '-';
                    const workDescriptionText = Utils.htmlToPlainText(workDescription) || '-';
                    const truncatedDescription = Utils.truncateDescription(workDescriptionText, 80);

                    // Use server-provided clock_in_display_date to avoid timezone issues
                    const displayDate = log.clock_in_display_date || window.utils.formatDate(log.clock_in);

                    const actionButtons = renderHistoryActions(log.id, isAuthor);

                    row.innerHTML = `
                        <td>${displayDate}</td>
                        <td>${clockInDisplay}</td>
                        <td>${clockOutDisplay}</td>
                        <td>${formattedDuration}</td>
                        <td>
                            <div class="description-preview">${truncatedDescription}</div>
                            ${workDescriptionText.length > 80 ? `<a href="#" class="description-truncated" onclick="viewDetails(${log.id}); return false;">View Details</a>` : ''}
                        </td>
                        ${isAuthor ? `<td>${actionButtons}</td>` : ''}
                    `;
                } catch (error) {
                    // Fallback formatting - use server-provided formatted dates when available
                    const timezone = Utils.getAppTimezone();
                    const clockInTime = log.clock_in_time
                        || new Date(log.clock_in).toLocaleTimeString('en-US', {timeZone: timezone, hour: '2-digit', minute:'2-digit', hour12: false});
                    const clockOutTime = log.clock_out_time
                        || (log.clock_out ? new Date(log.clock_out).toLocaleTimeString('en-US', {timeZone: timezone, hour: '2-digit', minute:'2-digit', hour12: false}) : '-');
                    const formattedDuration = log.total_minutes ? window.utils.formatTime(log.total_minutes) : '-';

                    const workDesc = log.work_description || '-';
                    const workDescText = Utils.htmlToPlainText(workDesc) || '-';
                    const truncatedDesc = Utils.truncateDescription(workDescText, 80);

                    // Use server-provided clock_in_display_date to avoid timezone issues
                    const displayDate = log.clock_in_display_date || new Date(log.clock_in).toLocaleDateString('en-CA', {
                        timeZone: timezone,
                        weekday: 'short',
                        year: 'numeric',
                        month: 'short',
                        day: 'numeric'
                    });

                    const actionButtons = renderHistoryActions(log.id, isAuthor);

                    row.innerHTML = `
                        <td>${displayDate}</td>
                        <td>${clockInTime}</td>
                        <td>${clockOutTime}</td>
                        <td>${formattedDuration}</td>
                        <td>
                            <div class="description-preview">${truncatedDesc}</div>
                            ${workDescText.length > 80 ? `<a href="#" class="description-truncated" onclick="viewDetails(${log.id}); return false;">View Details</a>` : ''}
                        </td>
                        ${isAuthor ? `<td>${actionButtons}</td>` : ''}
                    `;
                }
            });

            // Update pagination
            updatePagination(response.data.pagination);
        } else {
            tbody.innerHTML = `<tr><td colspan="${columns}" class="text-center">No work history found.</td></tr>`;
            updatePagination(null);
        }
    } catch (error) {
        tbody.innerHTML = `<tr><td colspan="${columns}" class="text-center text-danger">Failed to load history. Please try again.</td></tr>`;
        window.notify.error('Failed to load history: ' + error.message);
        updatePagination(null);
    }
}

function renderHistoryActions(logId, isAuthor) {
    const desktopButtons = isAuthor
        ? `
            <button class="btn btn-info history-action-btn" onclick="viewDetails(${logId})" title="View Details">
                <i class="fas fa-eye"></i>
            </button>
            <button class="btn btn-secondary history-action-btn" onclick="duplicateLog(${logId})" title="Duplicate">
                <i class="fas fa-copy"></i>
            </button>
            <button class="btn btn-primary history-action-btn" onclick="editLog(${logId})" title="Edit">
                <i class="fas fa-edit"></i>
            </button>
            <button class="btn btn-danger history-action-btn" onclick="deleteLog(${logId})" title="Delete">
                <i class="fas fa-trash"></i>
            </button>
        `
        : `
            <button class="btn btn-info history-action-btn" onclick="viewDetails(${logId})" title="View Details">
                <i class="fas fa-eye"></i>
            </button>
        `;

    const mobileOptions = isAuthor
        ? `
            <option value="">Actions</option>
            <option value="view">View Details</option>
            <option value="duplicate">Duplicate</option>
            <option value="edit">Edit</option>
            <option value="delete">Delete</option>
        `
        : `
            <option value="">Actions</option>
            <option value="view">View Details</option>
        `;

    return `
        <div class="history-row-actions">
            <div class="history-actions-desktop">
                ${desktopButtons}
            </div>
            <select class="history-actions-mobile form-control" onchange="handleHistoryAction(${logId}, this.value, this)">
                ${mobileOptions}
            </select>
        </div>
    `;
}

export function handleHistoryAction(logId, action, selectElement = null) {
    if (!action) return;

    if (action === 'view') {
        viewDetails(logId);
    } else if (action === 'duplicate') {
        duplicateLog(logId);
    } else if (action === 'edit') {
        editLog(logId);
    } else if (action === 'delete') {
        deleteLog(logId);
    }

    if (selectElement) {
        selectElement.value = '';
    }
}

/**
 * Update pagination controls
 * @param {Object} pagination - Pagination data from API
 */
export function updatePagination(pagination) {
    const container = document.getElementById('pagination-container');

    if (!pagination || pagination.total === 0) {
        container.innerHTML = '';
        return;
    }

    State.setTotalPages(pagination.last_page);
    State.setCurrentPage(pagination.current_page);

    const startItem = pagination.from || 0;
    const endItem = pagination.to || 0;
    const total = pagination.total;

    container.innerHTML = `
        <div class="pagination-info">
            Showing ${startItem} to ${endItem} of ${total} entries
        </div>
        <div class="pagination-controls">
            <button class="pagination-btn" onclick="loadHistory(1)" ${State.currentPage === 1 ? 'disabled' : ''}>
                <i class="fas fa-angle-double-left"></i>
            </button>
            <button class="pagination-btn" onclick="loadHistory(${State.currentPage - 1})" ${State.currentPage === 1 ? 'disabled' : ''}>
                <i class="fas fa-angle-left"></i>
            </button>
            ${generatePageNumbers()}
            <button class="pagination-btn" onclick="loadHistory(${State.currentPage + 1})" ${State.currentPage === State.totalPages ? 'disabled' : ''}>
                <i class="fas fa-angle-right"></i>
            </button>
            <button class="pagination-btn" onclick="loadHistory(${State.totalPages})" ${State.currentPage === State.totalPages ? 'disabled' : ''}>
                <i class="fas fa-angle-double-right"></i>
            </button>
        </div>
        <div class="page-size-selector">
            <span>Show:</span>
            <select onchange="changePageSize(this.value)">
                <option value="10" ${State.currentPerPage === 10 ? 'selected' : ''}>10</option>
                <option value="15" ${State.currentPerPage === 15 ? 'selected' : ''}>15</option>
                <option value="25" ${State.currentPerPage === 25 ? 'selected' : ''}>25</option>
                <option value="50" ${State.currentPerPage === 50 ? 'selected' : ''}>50</option>
            </select>
            <span>entries</span>
        </div>
    `;
}

/**
 * Generate page number buttons for pagination
 * @returns {string} HTML for page number buttons
 */
export function generatePageNumbers() {
    let pages = '';
    const maxVisible = 5;
    let start = Math.max(1, State.currentPage - Math.floor(maxVisible / 2));
    let end = Math.min(State.totalPages, start + maxVisible - 1);

    if (end - start + 1 < maxVisible) {
        start = Math.max(1, end - maxVisible + 1);
    }

    for (let i = start; i <= end; i++) {
        pages += `<button class="pagination-btn ${i === State.currentPage ? 'active' : ''}" onclick="loadHistory(${i})">${i}</button>`;
    }

    return pages;
}

/**
 * Change the number of items per page
 * @param {string} newSize - New page size
 */
export function changePageSize(newSize) {
    State.setCurrentPerPage(parseInt(newSize));
    State.setCurrentPage(1); // Reset to first page
    loadHistory(1, State.currentPerPage);
}

/**
 * Create a new entry
 */
export function createNewEntry() {
    // Clear the form and set to create mode
    const editLogId = document.getElementById('edit-log-id');
    if (!editLogId) return;

    editLogId.value = '';

    // Set default values - current date and current time in application timezone
    const currentDateTime = Utils.getCurrentDateTime();

    const dateField = document.getElementById('edit-clock-in-date');
    const clockInField = document.getElementById('edit-clock-in-time');
    const clockOutField = document.getElementById('edit-clock-out-time');

    if (dateField) dateField.value = currentDateTime.date;
    if (clockInField) clockInField.value = currentDateTime.time;
    if (clockOutField) clockOutField.value = currentDateTime.time;
    clearEditorContent('edit-work-description');

    // Store the currently selected project for the new entry
    editLogId.setAttribute('data-create-project-id', State.selectedProjectId || '');

    // Show the modal
    showEditLogModal();
    setAutoSaveStatus('Auto-save starts after the entry is created.');
    autoSaveLastFingerprint = buildAutoSaveFingerprint();
}

/**
 * Edit a log entry
 * @param {number} id - Log entry ID
 */
export async function editLog(id) {
    try {
        const response = await window.api.request(`/api/timesheet/logs/${id}`);

        if (response.success) {
            const timezone = Utils.getAppTimezone();
            const log = response.data;

            // Populate the edit form
            document.getElementById('edit-log-id').value = log.id;

            const dateForInput = log.clock_in_date || new Date(log.clock_in).toLocaleDateString('en-CA', {
                timeZone: timezone,
                year: 'numeric',
                month: '2-digit',
                day: '2-digit'
            });

            const timeForInput = log.clock_in_time || new Date(log.clock_in).toLocaleTimeString('en-CA', {
                timeZone: timezone,
                hour: '2-digit',
                minute: '2-digit',
                hour12: false
            });

            document.getElementById('edit-clock-in-date').value = dateForInput;
            document.getElementById('edit-clock-in-time').value = timeForInput;

            // Parse the clock_out datetime if it exists
            if (log.clock_out) {
                const clockOutTimeForInput = log.clock_out_time || new Date(log.clock_out).toLocaleTimeString('en-CA', {
                    timeZone: timezone,
                    hour: '2-digit',
                    minute: '2-digit',
                    hour12: false
                });
                document.getElementById('edit-clock-out-time').value = clockOutTimeForInput;
            } else {
                document.getElementById('edit-clock-out-time').value = '';
            }

            setEditorContent('edit-work-description', log.work_description || '');

            // Show the modal
            showEditLogModal();
            autoSaveLastFingerprint = buildAutoSaveFingerprint();
            setAutoSaveStatus('All changes saved.');
        } else {
            window.notify.error(response.message);
        }
    } catch (error) {
        window.notify.error('Failed to load entry details: ' + error.message);
    }
}

/**
 * Show the edit log modal
 */
export function showEditLogModal() {
    const modal = document.getElementById('edit-log-modal');
    const overlay = document.getElementById('modal-overlay');

    if (!modal || !overlay) return;

    ensureTimeInputsInitialized();
    bindAutoSaveListeners();

    modal.classList.add('show');
    overlay.classList.add('show');
    document.body.style.overflow = 'hidden';
    document.body.style.position = 'fixed'; // Prevent scroll on mobile
    document.body.style.width = '100%';
}

/**
 * Improve work description using OpenAI
 */
export async function improveWorkDescription() {
    const button = document.getElementById('improve-work-description-btn');

    if (!button) return;

    const selectedText = getSelectedText('edit-work-description') || window.getSelection()?.toString() || '';
    const isSelectionMode = selectedText.length > 0;
    const originalText = isSelectionMode ? selectedText.trim() : '';

    if (!originalText) {
        window.notify.error('Please highlight the text you want AI to improve.');
        return;
    }

    const originalButtonHtml = button.innerHTML;
    button.disabled = true;
    button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Improving...';

    try {
        const response = await window.api.request('/api/timesheet/improve-description', {
            method: 'POST',
            body: JSON.stringify({
                description: originalText,
                is_partial: true
            })
        });

        if (response.success && response.improved_text) {
            const improvedText = String(response.improved_text || '').trim();
            const isSuspiciousRewrite = improvedText.length < Math.max(25, Math.floor(originalText.length * 0.55));

            if (isSuspiciousRewrite) {
                window.notify.error('AI suggestion looked incomplete, so your original text was kept.');
                return;
            }

            const replaced = replaceSelectedText('edit-work-description', improvedText);
            if (!replaced) {
                window.notify.error('Could not replace the selected text. Please highlight it again and retry.');
                return;
            }
            window.notify.success('Selected text improved.');
        } else {
            window.notify.error(response.message || 'Failed to improve description.');
        }
    } catch (error) {
        window.notify.error('Failed to improve description: ' + error.message);
    } finally {
        button.disabled = false;
        button.innerHTML = originalButtonHtml;
    }
}

window.improveWorkDescription = improveWorkDescription;

/**
 * Hide the edit log modal
 */
export function hideEditLogModal() {
    const modal = document.getElementById('edit-log-modal');
    const overlay = document.getElementById('modal-overlay');

    if (modal) modal.classList.remove('show');
    if (overlay) overlay.classList.remove('show');

    document.body.style.overflow = '';
    document.body.style.position = '';
    document.body.style.width = '';

    // Clear form
    const form = document.getElementById('edit-log-form');
    if (form) form.reset();
    clearEditorContent('edit-work-description');
    clearAutoSaveTimer();
    autoSaveInFlight = false;
    autoSaveLastFingerprint = '';
    setAutoSaveStatus('');
}

/**
 * Update or create a log entry
 */
export async function updateLog() {
    const logId = document.getElementById('edit-log-id').value;
    const date = document.getElementById('edit-clock-in-date').value;
    const clockInTime = document.getElementById('edit-clock-in-time').value;
    const clockOutTime = document.getElementById('edit-clock-out-time').value;
    const descriptionPlainText = getEditorPlainText('edit-work-description');
    const descriptionHtml = getEditorHtml('edit-work-description');

    if (!date || !clockInTime || !clockOutTime || !descriptionPlainText) {
        window.notify.error('Please fill in all required fields');
        return;
    }

    if (!isValidTimeValue(clockInTime) || !isValidTimeValue(clockOutTime)) {
        window.notify.error('Please enter valid time in HH:MM format (24-hour).');
        return;
    }

    try {
        // If logId is empty, create new entry; otherwise update existing
        const url = logId ? `/api/timesheet/logs/${logId}` : '/api/timesheet/logs';
        const method = logId ? 'PUT' : 'POST';

        // Get project ID for new entries
        const projectId = logId ? null : (document.getElementById('edit-log-id').getAttribute('data-create-project-id') || null);

        console.log('Submitting log:', { url, method, logId, date, clockInTime, clockOutTime, projectId });

        const requestBody = {
            date: date,
            clock_in_time: clockInTime,
            clock_out_time: clockOutTime,
            work_description: descriptionHtml
        };

        // Add project_id only for new entries
        if (!logId && projectId) {
            requestBody.project_id = projectId;
        }

        const response = await window.api.request(url, {
            method: method,
            body: JSON.stringify(requestBody)
        });

        console.log('Response:', response);

        if (response.success) {
            autoSaveLastFingerprint = buildFingerprintFromValues(date, clockInTime, clockOutTime, descriptionHtml);
            hideEditLogModal();
            loadHistory(State.currentPage); // Reload current page
            loadDashboardStats(); // Refresh dashboard stats
            window.notify.success(logId ? 'Entry updated successfully' : 'Entry created successfully');
        } else {
            window.notify.error(response.message);
        }
    } catch (error) {
        console.error('Error submitting log:', error);
        window.notify.error(logId ? 'Failed to update entry: ' + error.message : 'Failed to create entry: ' + error.message);
    }
}

function bindAutoSaveListeners() {
    if (autoSaveListenersBound) return;

    const dateInput = document.getElementById('edit-clock-in-date');
    const clockInInput = document.getElementById('edit-clock-in-time');
    const clockOutInput = document.getElementById('edit-clock-out-time');

    [dateInput, clockInInput, clockOutInput].forEach((input) => {
        if (!input) return;
        input.addEventListener('input', scheduleAutoSave);
        input.addEventListener('change', scheduleAutoSave);
    });

    onEditorChange('edit-work-description', ({ source }) => {
        if (source === 'user') {
            scheduleAutoSave();
        }
    });

    bindImproveButtonSelectionPreserver();

    autoSaveListenersBound = true;
}

function bindImproveButtonSelectionPreserver() {
    if (improveButtonSelectionBound) return;

    const button = document.getElementById('improve-work-description-btn');
    if (!button) return;

    // Preserve editor selection while clicking Improve so highlighted text remains available.
    button.addEventListener('mousedown', (event) => {
        event.preventDefault();
    });

    improveButtonSelectionBound = true;
}

function getEditLogId() {
    const logId = document.getElementById('edit-log-id')?.value || '';
    return String(logId).trim();
}

function buildFingerprintFromValues(date, clockInTime, clockOutTime, descriptionHtml) {
    return [date, clockInTime, clockOutTime, descriptionHtml].join('||');
}

function buildAutoSaveFingerprint() {
    const date = document.getElementById('edit-clock-in-date')?.value || '';
    const clockInTime = document.getElementById('edit-clock-in-time')?.value || '';
    const clockOutTime = document.getElementById('edit-clock-out-time')?.value || '';
    const descriptionHtml = getEditorHtml('edit-work-description');
    return buildFingerprintFromValues(date, clockInTime, clockOutTime, descriptionHtml);
}

function clearAutoSaveTimer() {
    if (!autoSaveTimeoutId) return;
    clearTimeout(autoSaveTimeoutId);
    autoSaveTimeoutId = null;
}

function setAutoSaveStatus(message) {
    const statusEl = document.getElementById('edit-log-autosave-status');
    if (!statusEl) return;
    statusEl.textContent = message || '';
}

function scheduleAutoSave() {
    const logId = getEditLogId();
    if (!logId) return; // Only autosave existing entries

    const date = document.getElementById('edit-clock-in-date')?.value || '';
    const clockInTime = document.getElementById('edit-clock-in-time')?.value || '';
    const clockOutTime = document.getElementById('edit-clock-out-time')?.value || '';
    const descriptionPlainText = getEditorPlainText('edit-work-description');
    const descriptionHtml = getEditorHtml('edit-work-description');

    if (!date || !clockInTime || !clockOutTime || !descriptionPlainText) {
        setAutoSaveStatus('Add required fields to auto-save.');
        return;
    }

    const nextFingerprint = buildFingerprintFromValues(date, clockInTime, clockOutTime, descriptionHtml);
    if (nextFingerprint === autoSaveLastFingerprint) {
        setAutoSaveStatus('All changes saved.');
        return;
    }

    clearAutoSaveTimer();
    setAutoSaveStatus('Typing…');

    autoSaveTimeoutId = setTimeout(() => {
        performAutoSave(logId, {
            date,
            clock_in_time: clockInTime,
            clock_out_time: clockOutTime,
            work_description: descriptionHtml,
        }, nextFingerprint);
    }, AUTO_SAVE_DELAY_MS);
}

async function performAutoSave(logId, payload, fingerprint) {
    if (autoSaveInFlight) {
        // Let the in-flight save complete; next input will queue another save.
        return;
    }

    if (!document.getElementById('edit-log-modal')?.classList.contains('show')) {
        return;
    }

    autoSaveInFlight = true;
    setAutoSaveStatus('Auto-saving…');

    try {
        const response = await window.api.request(`/api/timesheet/logs/${logId}`, {
            method: 'PUT',
            body: JSON.stringify(payload)
        });

        if (response.success) {
            autoSaveLastFingerprint = fingerprint;
            setAutoSaveStatus('All changes saved.');
        } else {
            setAutoSaveStatus('Auto-save failed.');
        }
    } catch (_error) {
        setAutoSaveStatus('Auto-save failed.');
    } finally {
        autoSaveInFlight = false;
    }
}

/**
 * Delete a log entry
 * @param {number} id - Log entry ID
 */
export async function deleteLog(id) {
    if (!confirm('Are you sure you want to delete this entry?')) {
        return;
    }

    try {
        const response = await window.api.request(`/api/timesheet/logs/${id}`, {
            method: 'DELETE'
        });

        if (response.success) {
            loadHistory(State.currentPage); // Reload current page
            loadDashboardStats(); // Refresh dashboard stats
            window.notify.success('Entry deleted successfully');
        } else {
            window.notify.error(response.message);
        }
    } catch (error) {
        window.notify.error('Failed to delete entry: ' + error.message);
    }
}

/**
 * Duplicate an existing log entry as a new log entry for today.
 * Uses current app date/time and copies description (and project where available).
 * @param {number} id - Log entry ID
 */
export async function duplicateLog(id) {
    if (!confirm('A duplicate row will be created for today. Continue?')) {
        return;
    }

    try {
        const logResponse = await window.api.request(`/api/timesheet/logs/${id}`);

        if (!logResponse.success || !logResponse.data) {
            window.notify.error(logResponse.message || 'Failed to load entry for duplication');
            return;
        }

        const sourceLog = logResponse.data;
        const now = Utils.getCurrentDateTime();
        const sourceDuration = parseInt(sourceLog.total_minutes, 10);
        const durationMinutes = Number.isFinite(sourceDuration) && sourceDuration > 0 ? sourceDuration : 1;
        const clockOutTime = addMinutesToTime(now.time, durationMinutes);
        const workDescription = sourceLog.work_description || '';

        if (!workDescription || !Utils.htmlToPlainText(workDescription).trim()) {
            window.notify.error('Cannot duplicate an entry without a description');
            return;
        }

        const requestBody = {
            date: now.date,
            clock_in_time: now.time,
            clock_out_time: clockOutTime,
            work_description: workDescription
        };

        if (sourceLog.project_id) {
            requestBody.project_id = sourceLog.project_id;
        } else if (State.selectedProjectId) {
            requestBody.project_id = State.selectedProjectId;
        }

        const createResponse = await window.api.request('/api/timesheet/logs', {
            method: 'POST',
            body: JSON.stringify(requestBody)
        });

        if (createResponse.success) {
            loadHistory(State.currentPage);
            loadDashboardStats();
            window.notify.success('Entry duplicated for today');
        } else {
            window.notify.error(createResponse.message || 'Failed to duplicate entry');
        }
    } catch (error) {
        window.notify.error('Failed to duplicate entry: ' + error.message);
    }
}

/**
 * View details of a log entry
 * @param {number} logId - Log entry ID
 */
export async function viewDetails(logId) {
    try {
        const response = await window.api.request(`/api/timesheet/logs/${logId}`);

        if (response.success) {
            const log = response.data;

            // Populate modal with data
            // Use server-provided clock_in_display_date for consistency
            document.getElementById('detail-date').textContent = log.clock_in_display_date || window.utils.formatDate(log.clock_in);
            document.getElementById('detail-start-time').textContent = log.clock_in_time || window.utils.formatTimeForDisplay(log.clock_in);
            document.getElementById('detail-end-time').textContent = log.clock_out
                ? (log.clock_out_time || window.utils.formatTimeForDisplay(log.clock_out))
                : '-';
            document.getElementById('detail-duration').textContent = log.formatted_duration || (log.total_minutes ? window.utils.formatTime(log.total_minutes) : '-');
            const detailDescription = document.getElementById('detail-work-description');
            const formattedDescription = formatDescriptionForDetails(log.work_description || '');
            detailDescription.innerHTML = formattedDescription || 'No description provided';

            // Show modal
            showViewDetailsModal();
        } else {
            window.notify.error('Failed to load entry details');
        }
    } catch (error) {
        window.notify.error('Failed to load entry details: ' + error.message);
    }
}

/**
 * Show the view details modal
 */
export function showViewDetailsModal() {
    document.getElementById('view-details-modal').classList.add('show');
    document.getElementById('modal-overlay').classList.add('show');
}

/**
 * Hide the view details modal
 */
export function hideViewDetailsModal() {
    document.getElementById('view-details-modal').classList.remove('show');
    document.getElementById('modal-overlay').classList.remove('show');
}
