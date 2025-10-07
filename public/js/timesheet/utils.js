/**
 * Utility Functions Module
 *
 * Provides utility functions for formatting dates, times, and other common operations.
 * All time-related functions use America/Toronto timezone.
 */

/**
 * Format a datetime string to display time in HH:MM format (Toronto timezone)
 * @param {string} dateString - ISO datetime string
 * @returns {string} Formatted time string
 */
export function formatTimeForDisplay(dateString) {
    return new Date(dateString).toLocaleTimeString('en-CA', {
        timeZone: 'America/Toronto',
        hour: '2-digit',
        minute: '2-digit',
        hour12: false
    });
}

/**
 * Format a datetime string to display both date and time (Toronto timezone)
 * @param {string} dateString - ISO datetime string
 * @returns {Object} Object with date and time properties
 */
export function formatDateTimeForDisplay(dateString) {
    const date = new Date(dateString);
    return {
        date: date.toLocaleDateString('en-CA', {
            timeZone: 'America/Toronto',
            weekday: 'short',
            year: 'numeric',
            month: 'short',
            day: 'numeric'
        }),
        time: date.toLocaleTimeString('en-CA', {
            timeZone: 'America/Toronto',
            hour: '2-digit',
            minute: '2-digit',
            hour12: false
        })
    };
}

/**
 * Format a date string for display (Toronto timezone)
 * @param {string} dateString - ISO date string
 * @returns {string} Formatted date string
 */
export function formatDate(dateString) {
    return new Date(dateString).toLocaleDateString('en-CA', {
        timeZone: 'America/Toronto',
        weekday: 'short',
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    });
}

/**
 * Get current date and time in Toronto timezone
 * @returns {Object} Object with date (YYYY-MM-DD) and time (HH:MM) properties
 */
export function getCurrentDateTime() {
    const now = new Date();
    // Convert to Toronto timezone and format properly
    const torontoTime = new Date(now.toLocaleString("en-US", {timeZone: "America/Toronto"}));

    // Format date as YYYY-MM-DD
    const year = torontoTime.getFullYear();
    const month = String(torontoTime.getMonth() + 1).padStart(2, '0');
    const day = String(torontoTime.getDate()).padStart(2, '0');

    // Format time as HH:MM
    const hours = String(torontoTime.getHours()).padStart(2, '0');
    const minutes = String(torontoTime.getMinutes()).padStart(2, '0');

    return {
        date: `${year}-${month}-${day}`,
        time: `${hours}:${minutes}`
    };
}

/**
 * Truncate description text to a maximum length
 * @param {string} text - Text to truncate
 * @param {number} maxLength - Maximum length before truncation
 * @returns {string} Truncated text with ellipsis if needed
 */
export function truncateDescription(text, maxLength) {
    if (!text || text === '-' || text.length <= maxLength) {
        return text;
    }
    return text.substring(0, maxLength) + '...';
}

/**
 * Get the start of the week for a given date
 * @param {Date} date - Reference date
 * @param {string} weekStartDay - 'sunday' or 'monday'
 * @returns {Date} Start of the week
 */
export function getWeekStart(date, weekStartDay) {
    const d = new Date(date);
    const day = d.getDay();
    const diff = weekStartDay === 'sunday' ? day : (day === 0 ? 6 : day - 1);
    d.setDate(d.getDate() - diff);
    return d;
}

/**
 * Get the end of the week for a given date
 * @param {Date} date - Reference date
 * @param {string} weekStartDay - 'sunday' or 'monday'
 * @returns {Date} End of the week
 */
export function getWeekEnd(date, weekStartDay) {
    const weekStart = getWeekStart(date, weekStartDay);
    const weekEnd = new Date(weekStart);
    weekEnd.setDate(weekStart.getDate() + 6);
    return weekEnd;
}

/**
 * Format a date for input field (YYYY-MM-DD)
 * @param {Date} date - Date to format
 * @returns {string} Formatted date string
 */
export function formatDateForInput(date) {
    return date.toISOString().split('T')[0];
}

/**
 * Initialize utility functions on the window.utils namespace for backwards compatibility
 */
export function initializeUtils() {
    if (!window.utils) {
        window.utils = {};
    }

    window.utils.formatTimeForDisplay = formatTimeForDisplay;
    window.utils.formatDateTimeForDisplay = formatDateTimeForDisplay;
    window.utils.formatDate = formatDate;
    window.utils.getCurrentDateTime = getCurrentDateTime;
}
