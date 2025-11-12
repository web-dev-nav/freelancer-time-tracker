/**
 * Utility Functions Module
 *
 * Provides utility functions for formatting dates, times, and other common operations.
 * All time-related functions respect the app timezone configured on the server.
 */

const DEFAULT_TIMEZONE = 'UTC';

/**
 * Resolve the application timezone provided by the backend
 * @returns {string} IANA timezone identifier
 */
export function getAppTimezone() {
    if (typeof window !== 'undefined' && window.appTimezone) {
        return window.appTimezone;
    }
    return DEFAULT_TIMEZONE;
}

/**
 * Format a datetime string to display time in HH:MM format (app timezone)
 * @param {string} dateString - ISO datetime string
 * @returns {string} Formatted time string
 */
export function formatTimeForDisplay(dateString) {
    const timezone = getAppTimezone();
    return new Date(dateString).toLocaleTimeString('en-CA', {
        timeZone: timezone,
        hour: '2-digit',
        minute: '2-digit',
        hour12: false
    });
}

/**
 * Format a datetime string to display both date and time (app timezone)
 * @param {string} dateString - ISO datetime string
 * @returns {Object} Object with date and time properties
 */
export function formatDateTimeForDisplay(dateString) {
    const timezone = getAppTimezone();
    const date = new Date(dateString);
    return {
        date: date.toLocaleDateString('en-CA', {
            timeZone: timezone,
            weekday: 'short',
            year: 'numeric',
            month: 'short',
            day: 'numeric'
        }),
        time: date.toLocaleTimeString('en-CA', {
            timeZone: timezone,
            hour: '2-digit',
            minute: '2-digit',
            hour12: false
        })
    };
}

/**
 * Format a date string for display (app timezone)
 * @param {string} dateString - ISO date string
 * @returns {string} Formatted date string
 */
export function formatDate(dateString) {
    const timezone = getAppTimezone();
    return new Date(dateString).toLocaleDateString('en-CA', {
        timeZone: timezone,
        weekday: 'short',
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    });
}

/**
 * Get current date and time in application timezone
 * @returns {Object} Object with date (YYYY-MM-DD) and time (HH:MM) properties
 */
export function getCurrentDateTime() {
    const timezone = getAppTimezone();
    const formatter = new Intl.DateTimeFormat('en-CA', {
        timeZone: timezone,
        year: 'numeric',
        month: '2-digit',
        day: '2-digit',
        hour: '2-digit',
        minute: '2-digit',
        hour12: false
    });

    const parts = formatter.formatToParts(new Date());
    let year = '0000';
    let month = '00';
    let day = '00';
    let hour = '00';
    let minute = '00';

    for (const { type, value } of parts) {
        switch (type) {
            case 'year':
                year = value;
                break;
            case 'month':
                month = value;
                break;
            case 'day':
                day = value;
                break;
            case 'hour':
                hour = value;
                break;
            case 'minute':
                minute = value;
                break;
            default:
                // Ignore literals and other parts
                break;
        }
    }

    return {
        date: `${year}-${month}-${day}`,
        time: `${hour}:${minute}`
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

    window.utils.getAppTimezone = getAppTimezone;
    window.utils.formatTimeForDisplay = formatTimeForDisplay;
    window.utils.formatDateTimeForDisplay = formatDateTimeForDisplay;
    window.utils.formatDate = formatDate;
    window.utils.getCurrentDateTime = getCurrentDateTime;
}
