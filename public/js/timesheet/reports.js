/**
 * Reports Module
 *
 * Handles report generation, display, and Excel export functionality.
 * Includes date range selection helpers for weeks and months.
 */

import * as State from './state.js';
import * as Utils from './utils.js';

/**
 * Select this week's date range
 */
export function selectThisWeek() {
    const weekStartDay = document.getElementById('week-start-day').value;
    const today = new Date();
    const weekStart = Utils.getWeekStart(today, weekStartDay);
    const weekEnd = Utils.getWeekEnd(today, weekStartDay);

    document.getElementById('report-start-date').value = Utils.formatDateForInput(weekStart);
    document.getElementById('report-end-date').value = Utils.formatDateForInput(weekEnd);
}

/**
 * Select last week's date range
 */
export function selectLastWeek() {
    const weekStartDay = document.getElementById('week-start-day').value;
    const today = new Date();
    const lastWeek = new Date(today);
    lastWeek.setDate(today.getDate() - 7);
    const weekStart = Utils.getWeekStart(lastWeek, weekStartDay);
    const weekEnd = Utils.getWeekEnd(lastWeek, weekStartDay);

    document.getElementById('report-start-date').value = Utils.formatDateForInput(weekStart);
    document.getElementById('report-end-date').value = Utils.formatDateForInput(weekEnd);
}

/**
 * Select this month's date range
 */
export function selectThisMonth() {
    const today = new Date();
    const monthStart = new Date(today.getFullYear(), today.getMonth(), 1);
    const monthEnd = new Date(today.getFullYear(), today.getMonth() + 1, 0);

    document.getElementById('report-start-date').value = Utils.formatDateForInput(monthStart);
    document.getElementById('report-end-date').value = Utils.formatDateForInput(monthEnd);
}

/**
 * Select last month's date range
 */
export function selectLastMonth() {
    const today = new Date();
    const lastMonth = new Date(today.getFullYear(), today.getMonth() - 1, 1);
    const lastMonthEnd = new Date(today.getFullYear(), today.getMonth(), 0);

    document.getElementById('report-start-date').value = Utils.formatDateForInput(lastMonth);
    document.getElementById('report-end-date').value = Utils.formatDateForInput(lastMonthEnd);
}

/**
 * Generate a report for the selected date range
 */
export async function generateReport() {
    const startDate = document.getElementById('report-start-date').value;
    const endDate = document.getElementById('report-end-date').value;

    if (!startDate || !endDate) {
        window.notify.error('Please select both start and end dates');
        return;
    }

    if (new Date(startDate) > new Date(endDate)) {
        window.notify.error('Start date cannot be after end date');
        return;
    }

    try {
        const url = State.selectedProjectId
            ? `/api/timesheet/report?start_date=${startDate}&end_date=${endDate}&project_id=${State.selectedProjectId}`
            : `/api/timesheet/report?start_date=${startDate}&end_date=${endDate}`;
        const response = await window.api.request(url);

        if (response.success && response.data.logs.length > 0) {
            State.setCurrentReportData(response.data);
            displayReport();
            document.getElementById('export-btn').disabled = false;
            window.notify.success('Report generated successfully');
        } else {
            document.getElementById('report-results').classList.add('hidden');
            document.getElementById('export-btn').disabled = true;
            window.notify.error('No data found for the selected period');
        }
    } catch (error) {
        window.notify.error('Failed to generate report: ' + error.message);
    }
}

/**
 * Display the generated report
 */
export function displayReport() {
    if (!State.currentReportData) return;

    const { summary, period, logs } = State.currentReportData;

    // Update summary
    document.getElementById('report-total-hours').textContent =
        window.utils.formatTime(Math.round(summary.total_hours * 60));
    document.getElementById('report-total-sessions').textContent = summary.total_sessions;
    document.getElementById('report-days-worked').textContent = summary.days_worked;
    document.getElementById('report-avg-hours').textContent =
        window.utils.formatTime(Math.round(summary.average_hours_per_day * 60));

    // Update period with week context
    const startDate = new Date(period.start_date);
    const endDate = new Date(period.end_date);
    const weekStartDay = document.getElementById('week-start-day').value;

    let periodContext = '';
    const daysDiff = Math.ceil((endDate - startDate) / (1000 * 60 * 60 * 24)) + 1;

    if (daysDiff === 7) {
        const weekStart = Utils.getWeekStart(startDate, weekStartDay);
        const weekEnd = Utils.getWeekEnd(startDate, weekStartDay);

        if (startDate.getTime() === weekStart.getTime() && endDate.getTime() === weekEnd.getTime()) {
            const today = new Date();
            const thisWeekStart = Utils.getWeekStart(today, weekStartDay);
            const lastWeekStart = new Date(thisWeekStart);
            lastWeekStart.setDate(thisWeekStart.getDate() - 7);

            if (startDate.getTime() === thisWeekStart.getTime()) {
                periodContext = ' (This Week)';
            } else if (startDate.getTime() === lastWeekStart.getTime()) {
                periodContext = ' (Last Week)';
            } else {
                periodContext = ` (Week starting ${weekStartDay === 'sunday' ? 'Sunday' : 'Monday'})`;
            }
        }
    }

    document.getElementById('report-period').innerHTML = `
        <strong>Report Period:</strong> ${window.utils.formatDate(period.start_date)} - ${window.utils.formatDate(period.end_date)}${periodContext}
        <br><small class="text-muted">Week starts on ${weekStartDay.charAt(0).toUpperCase() + weekStartDay.slice(1)}</small>
    `;

    // Update table
    const tbody = document.getElementById('report-tbody');
    tbody.innerHTML = '';

    logs.forEach(log => {
        const row = tbody.insertRow();

        try {
            // Format the data properly using Toronto timezone
            const clockInDisplay = window.utils.formatTimeForDisplay(log.clock_in);
            const clockOutDisplay = log.clock_out ? window.utils.formatTimeForDisplay(log.clock_out) : '-';
            const formattedDuration = log.formatted_duration || (log.total_minutes ? window.utils.formatTime(log.total_minutes) : '-');
            const workDescription = log.work_description || '-';
            const truncatedDescription = Utils.truncateDescription(workDescription, 100);

            row.innerHTML = `
                <td>${window.utils.formatDate(log.clock_in)}</td>
                <td>${clockInDisplay}</td>
                <td>${clockOutDisplay}</td>
                <td>${formattedDuration}</td>
                <td>
                    <div class="description-preview">${truncatedDescription}</div>
                    ${workDescription.length > 100 ? `<a href="#" class="description-truncated" onclick="viewDetails(${log.id}); return false;">View Details</a>` : ''}
                </td>
            `;
        } catch (error) {
            // Fallback formatting
            const clockInTime = new Date(log.clock_in).toLocaleTimeString('en-US', {hour: '2-digit', minute:'2-digit', hour12: false});
            const clockOutTime = log.clock_out ? new Date(log.clock_out).toLocaleTimeString('en-US', {hour: '2-digit', minute:'2-digit', hour12: false}) : '-';
            const formattedDuration = log.total_minutes ? window.utils.formatTime(log.total_minutes) : '-';

            const workDesc = log.work_description || '-';
            const truncatedDesc = Utils.truncateDescription(workDesc, 100);

            row.innerHTML = `
                <td>${new Date(log.clock_in).toLocaleDateString()}</td>
                <td>${clockInTime}</td>
                <td>${clockOutTime}</td>
                <td>${formattedDuration}</td>
                <td>
                    <div class="description-preview">${truncatedDesc}</div>
                    ${workDesc.length > 100 ? `<a href="#" class="description-truncated" onclick="viewDetails(${log.id}); return false;">View Details</a>` : ''}
                </td>
            `;
        }
    });

    document.getElementById('report-results').classList.remove('hidden');
}

/**
 * Export the current report to Excel
 */
export async function exportExcel() {
    if (!State.currentReportData) {
        window.notify.error('Please generate a report first');
        return;
    }

    const startDate = document.getElementById('report-start-date').value;
    const endDate = document.getElementById('report-end-date').value;

    try {
        const url = State.selectedProjectId
            ? `/api/timesheet/export-excel?start_date=${startDate}&end_date=${endDate}&project_id=${State.selectedProjectId}`
            : `/api/timesheet/export-excel?start_date=${startDate}&end_date=${endDate}`;
        const link = document.createElement('a');
        link.href = url;
        link.download = `Timesheet_${State.currentReportData.period.start_date}_to_${State.currentReportData.period.end_date}.xlsx`;
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);

        window.notify.success('Excel file downloaded successfully!');
    } catch (error) {
        window.notify.error('Failed to export Excel: ' + error.message);
    }
}
