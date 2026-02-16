/**
 * History Module
 *
 * Manages the history tab functionality including loading, editing, deleting time logs,
 * and pagination controls.
 */

import * as State from './state.js';
import * as Utils from './utils.js';
import { loadDashboardStats } from './dashboard.js';

let timeInputsInitialized = false;

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

                    const actionButtons = isAuthor
                        ? `
                        <button class="btn btn-info" onclick="viewDetails(${log.id})" style="padding: 6px 12px; font-size: 12px; margin-right: 8px;" title="View Details">
                            <i class="fas fa-eye"></i>
                        </button>
                        <button class="btn btn-primary" onclick="editLog(${log.id})" style="padding: 6px 12px; font-size: 12px; margin-right: 8px;" title="Edit">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-danger" onclick="deleteLog(${log.id})" style="padding: 6px 12px; font-size: 12px;" title="Delete">
                            <i class="fas fa-trash"></i>
                        </button>
                    `
                        : `
                        <button class="btn btn-info" onclick="viewDetails(${log.id})" style="padding: 6px 12px; font-size: 12px;" title="View Details">
                            <i class="fas fa-eye"></i>
                        </button>
                    `;

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

                    const actionButtons = isAuthor
                        ? `
                        <button class="btn btn-info" onclick="viewDetails(${log.id})" style="padding: 6px 12px; font-size: 12px; margin-right: 8px;" title="View Details">
                            <i class="fas fa-eye"></i>
                        </button>
                        <button class="btn btn-primary" onclick="editLog(${log.id})" style="padding: 6px 12px; font-size: 12px; margin-right: 8px;" title="Edit">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-danger" onclick="deleteLog(${log.id})" style="padding: 6px 12px; font-size: 12px;" title="Delete">
                            <i class="fas fa-trash"></i>
                        </button>
                    `
                        : `
                        <button class="btn btn-info" onclick="viewDetails(${log.id})" style="padding: 6px 12px; font-size: 12px;" title="View Details">
                            <i class="fas fa-eye"></i>
                        </button>
                    `;

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
    const descField = document.getElementById('edit-work-description');

    if (dateField) dateField.value = currentDateTime.date;
    if (clockInField) clockInField.value = currentDateTime.time;
    if (clockOutField) clockOutField.value = currentDateTime.time;
    if (descField) descField.value = '';

    // Store the currently selected project for the new entry
    editLogId.setAttribute('data-create-project-id', State.selectedProjectId || '');

    // Show the modal
    showEditLogModal();
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

            const normalizedDescription = Utils.htmlToPlainText(log.work_description || '');
            document.getElementById('edit-work-description').value = normalizedDescription || '';

            // Show the modal
            showEditLogModal();
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

    modal.classList.add('show');
    overlay.classList.add('show');
    document.body.style.overflow = 'hidden';
    document.body.style.position = 'fixed'; // Prevent scroll on mobile
    document.body.style.width = '100%';
}

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
}

/**
 * Update or create a log entry
 */
export async function updateLog() {
    const logId = document.getElementById('edit-log-id').value;
    const date = document.getElementById('edit-clock-in-date').value;
    const clockInTime = document.getElementById('edit-clock-in-time').value;
    const clockOutTime = document.getElementById('edit-clock-out-time').value;
    const description = document.getElementById('edit-work-description').value.trim();

    if (!date || !clockInTime || !clockOutTime || !description) {
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
            work_description: description
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
            const plainDescription = Utils.htmlToPlainText(log.work_description || '');
            detailDescription.textContent = plainDescription || 'No description provided';

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
