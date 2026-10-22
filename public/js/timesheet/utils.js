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
 * Strip HTML tags and return readable text
 * @param {string} html - HTML string
 * @returns {string} Plain text
 */
export function escapeHtml(value) {
    return String(value ?? '').replace(/[&<>"']/g, char => ({'&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;'}[char]));
}
export function htmlToPlainText(html) {
    if (!html) return '';
    const documentText = new DOMParser().parseFromString(String(html), 'text/html');
    documentText.querySelectorAll('script, style').forEach(node => node.remove());
    documentText.querySelectorAll('br').forEach(node => node.replaceWith('\n'));
    documentText.querySelectorAll('li').forEach(node => node.prepend('• '));
    documentText.querySelectorAll('p, div, li, ul, ol, blockquote').forEach(node => node.append('\n'));
    return (documentText.body.textContent || '').replace(/\u00a0/g, ' ').replace(/[ \t]+/g, ' ').replace(/ *\n */g, '\n').replace(/\n{3,}/g, '\n\n').trim();
}

/**
 * Sanitize rich text by allowing a small subset of tags and removing attributes.
 * @param {string} html - Raw HTML
 * @returns {string} Sanitized HTML
 */
export function sanitizeRichTextHtml(html) {
    if (!html) return '';

    const allowedTags = new Set([
        'P', 'DIV', 'BR', 'STRONG', 'B', 'EM', 'I', 'U', 'UL', 'OL', 'LI', 'BLOCKQUOTE'
    ]);

    const template = document.createElement('template');
    template.innerHTML = String(html);

    const sanitizeNode = (node) => {
        if (node.nodeType === Node.ELEMENT_NODE) {
            const tagName = node.tagName.toUpperCase();
            if (!allowedTags.has(tagName)) {
                const parent = node.parentNode;
                if (!parent) return;
                while (node.firstChild) {
                    parent.insertBefore(node.firstChild, node);
                }
                parent.removeChild(node);
                return;
            }

            const attributes = Array.from(node.attributes);
            for (const attr of attributes) {
                node.removeAttribute(attr.name);
            }
        }

        const children = Array.from(node.childNodes);
        for (const child of children) {
            sanitizeNode(child);
        }
    };

    const rootChildren = Array.from(template.content.childNodes);
    for (const child of rootChildren) {
        sanitizeNode(child);
    }

    return template.innerHTML.trim();
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
 * Copy text to clipboard and show notification
 * @param {string} text - Text to copy to clipboard
 * @param {string} message - Success message to display
 */
export function copyToClipboard(text, message = 'Copied to clipboard!') {
    if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(text).then(() => {
            if (window.notify && window.notify.success) {
                window.notify.success(message);
            } else {
                alert(message);
            }
        }).catch(err => {
            console.error('Failed to copy text:', err);
            fallbackCopyToClipboard(text, message);
        });
    } else {
        fallbackCopyToClipboard(text, message);
    }
}

/**
 * Fallback copy method for older browsers
 * @param {string} text - Text to copy
 * @param {string} message - Success message
 */
function fallbackCopyToClipboard(text, message) {
    const textArea = document.createElement('textarea');
    textArea.value = text;
    textArea.style.position = 'fixed';
    textArea.style.left = '-999999px';
    textArea.style.top = '-999999px';
    document.body.appendChild(textArea);
    textArea.focus();
    textArea.select();

    try {
        const successful = document.execCommand('copy');
        if (successful && window.notify && window.notify.success) {
            window.notify.success(message);
        } else if (successful) {
            alert(message);
        }
    } catch (err) {
        console.error('Fallback: Failed to copy', err);
    }

    document.body.removeChild(textArea);
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
    window.utils.copyToClipboard = copyToClipboard;
    window.utils.htmlToPlainText = htmlToPlainText;
    window.utils.sanitizeRichTextHtml = sanitizeRichTextHtml;
}


// Confirm the server created a file before showing success or downloading an error page.
export async function downloadFile(url, fallbackName) {
    const controller = new AbortController();
    const timeout = setTimeout(() => controller.abort(), 60000);
    try {
        const response = await fetch(url, {signal: controller.signal, headers: {'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest'}});
        const type = response.headers.get('content-type') || '';
        if (!response.ok || type.includes('application/json') || type.includes('text/html')) {
            const data = await response.json().catch(() => ({}));
            throw new Error(data.message || 'The file could not be generated. Please try again.');
        }
        const blob = await response.blob();
        const objectUrl = URL.createObjectURL(blob);
        const link = document.createElement('a');
        const name = /filename="?([^";]+)"?/i.exec(response.headers.get('content-disposition') || '')?.[1];
        link.href = objectUrl;
        link.download = name || fallbackName;
        document.body.append(link);
        link.click();
        link.remove();
        setTimeout(() => URL.revokeObjectURL(objectUrl), 1000);
    } catch (error) {
        if (controller.signal.aborted) throw new Error('File generation timed out. Please try again.');
        throw error;
    } finally { clearTimeout(timeout); }
}
