/**
 * Settings Page JavaScript
 */

let isLoading = false;
let dailyActivityClientSchedules = [];
const ACTIVITY_COLUMN_OPTIONS = [
    { key: 'date', label: 'Date' },
    { key: 'project', label: 'Project' },
    { key: 'clock_in', label: 'In' },
    { key: 'clock_out', label: 'Out' },
    { key: 'duration', label: 'Duration' },
    { key: 'description', label: 'Description' },
];

const STRIPE_SECRET_INPUT_ID = 'stripe-secret-key';
const STRIPE_SECRET_ACTIONS_ID = 'stripe-secret-actions';
const CLEAR_CACHE_STATUS_ID = 'clear-cache-status';

function getStripeSecretInput() {
    return document.getElementById(STRIPE_SECRET_INPUT_ID);
}

function toggleStripeSecretActions(show) {
    const actions = document.getElementById(STRIPE_SECRET_ACTIONS_ID);
    if (actions) {
        actions.style.display = show ? 'block' : 'none';
    }
}

function updateClearCacheStatus(type, message) {
    const status = document.getElementById(CLEAR_CACHE_STATUS_ID);
    if (!status) {
        return;
    }

    if (!message) {
        status.style.display = 'none';
        return;
    }

    status.style.display = 'flex';
    status.style.alignItems = 'center';
    status.style.gap = '8px';
    status.style.padding = '8px 12px';
    status.style.borderRadius = '6px';
    status.style.fontWeight = '500';

    let background = '#e5e7eb';
    let color = '#374151';
    let border = '#d1d5db';
    let icon = 'info-circle';

    if (type === 'success') {
        background = '#d1fae5';
        color = '#065f46';
        border = '#10b981';
        icon = 'check-circle';
    } else if (type === 'error') {
        background = '#fee2e2';
        color = '#991b1b';
        border = '#ef4444';
        icon = 'exclamation-circle';
    } else if (type === 'info') {
        background = '#e0f2fe';
        color = '#0c4a6e';
        border = '#0284c7';
        icon = 'spinner fa-spin';
    }

    status.style.background = background;
    status.style.color = color;
    status.style.border = `1px solid ${border}`;
    status.innerHTML = `<i class="fas fa-${icon}"></i> ${message}`;
}

async function clearApplicationCaches() {
    const button = document.getElementById('clear-cache-btn');
    if (!button || isLoading) {
        return;
    }

    const originalLabel = button.innerHTML;
    button.disabled = true;
    button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Clearing...';

    updateClearCacheStatus('info', 'Clearing Laravel caches...');

    try {
        isLoading = true;
        const response = await window.api.request('/api/settings/flush-cache', {
            method: 'POST',
        });

        if (response && response.success) {
            const successMessage = response.message || 'Application caches cleared successfully.';
            updateClearCacheStatus('success', successMessage);
            showMessage('success', successMessage);
        } else {
            const errorMessage = response?.message || 'Failed to clear caches. Please try again.';
            updateClearCacheStatus('error', errorMessage);
            showMessage('error', errorMessage);
        }
    } catch (error) {
        console.error('Failed to clear caches:', error);
        const message = 'Failed to clear caches: ' + (error.message || 'Unknown error');
        updateClearCacheStatus('error', message);
        showMessage('error', message);
    } finally {
        isLoading = false;
        button.disabled = false;
        button.innerHTML = originalLabel;
    }
}

function setupStripeSecretField(maskedSecret) {
    const input = getStripeSecretInput();
    if (!input) {
        return;
    }

    if (!input.dataset.defaultPlaceholder) {
        input.dataset.defaultPlaceholder = input.placeholder || '';
    }

    if (input.dataset.listenerAttached !== 'true') {
        input.addEventListener('input', () => {
            if (input.value.trim() !== '') {
                input.dataset.cleared = 'false';
                input.dataset.hasStoredValue = 'false';
                toggleStripeSecretActions(false);
            }
        });
        input.dataset.listenerAttached = 'true';
    }

    input.value = '';
    input.dataset.cleared = 'false';

    if (maskedSecret) {
        input.dataset.hasStoredValue = 'true';
        const preview = maskedSecret.slice(-4);
        input.placeholder = `Secret key saved (ending ${preview}) — leave blank to keep`;
        input.title = 'A Stripe secret key is already stored. Leave blank to keep it.';
        toggleStripeSecretActions(true);
    } else {
        input.dataset.hasStoredValue = 'false';
        input.placeholder = input.dataset.defaultPlaceholder || 'sk_test_... or sk_live_...';
        input.title = '';
        toggleStripeSecretActions(false);
    }
}

function clearStripeSecretInternal() {
    const input = getStripeSecretInput();
    if (!input) {
        return;
    }

    input.value = '';
    input.dataset.hasStoredValue = 'true';
    input.dataset.cleared = 'true';
    if (input.dataset.defaultPlaceholder) {
        input.placeholder = input.dataset.defaultPlaceholder;
    }
    input.title = '';
    toggleStripeSecretActions(false);

    if (window.notify?.info) {
        window.notify.info('Stripe secret key will be removed once you save the settings.');
    }
}

window.clearStripeSecret = clearStripeSecretInternal;

/**
 * Switch between tabs
 */
window.switchTab = function(tabName) {
    // Remove active class from all tabs and content
    document.querySelectorAll('.settings-tab').forEach(tab => {
        tab.classList.remove('active');
    });
    document.querySelectorAll('.tab-content').forEach(content => {
        content.classList.remove('active');
    });

    // Add active class to clicked tab and corresponding content
    event.target.classList.add('active');
    document.getElementById(tabName + '-tab').classList.add('active');
};

/**
 * Load settings from API
 */
async function loadSettings() {
    try {
        const response = await window.api.request('/api/settings');

        if (response && response.success) {
            const data = response.data;

            // General settings
            setValue('invoice-company-name', data.invoice_company_name);
            setValue('invoice-company-address', data.invoice_company_address);
            setValue('invoice-tax-number', data.invoice_tax_number);

            // Payment settings
            setValue('payment-etransfer-email', data.payment_etransfer_email);
            setValue('payment-bank-info', data.payment_bank_info);
            setValue('payment-instructions', data.payment_instructions);

            // Stripe settings
            const stripeEnabled = data.stripe_enabled === true || data.stripe_enabled === '1' || data.stripe_enabled === 1;
            const stripeCheckbox = document.getElementById('stripe-enabled');
            if (stripeCheckbox) {
                stripeCheckbox.checked = stripeEnabled;
            }
            setValue('stripe-publishable-key', data.stripe_publishable_key);
            // SECURITY: Never load secret key to frontend - it should only be on server
            // setValue('stripe-secret-key', data.stripe_secret_key);
            setupStripeSecretField(typeof data.stripe_secret_key === 'string' ? data.stripe_secret_key : null);

            // Toggle Stripe fields visibility
            if (typeof window.toggleStripeFields === 'function') {
                window.toggleStripeFields();
            }

            // Email settings
            setValue('email-mailer', data.email_mailer || 'default');
            setValue('email-smtp-host', data.email_smtp_host);
            setValue('email-smtp-port', data.email_smtp_port);
            setValue('email-smtp-username', data.email_smtp_username);
            setValue('email-smtp-password', data.email_smtp_password);
            setValue('email-smtp-encryption', data.email_smtp_encryption);
            setValue('email-from-address', data.email_from_address);
            setValue('email-from-name', data.email_from_name);

            // Daily activity email automation settings
            const dailyEnabled = data.daily_activity_email_enabled === true || data.daily_activity_email_enabled === '1' || data.daily_activity_email_enabled === 1;
            const dailyEnabledCheckbox = document.getElementById('daily-activity-email-enabled');
            if (dailyEnabledCheckbox) {
                dailyEnabledCheckbox.checked = dailyEnabled;
            }
            setValue('daily-activity-email-recipients', data.daily_activity_email_recipients);
            setValue('daily-activity-email-send-time', data.daily_activity_email_send_time || '18:00');
            const dailyLastSent = document.getElementById('daily-activity-email-last-sent');
            if (dailyLastSent) {
                dailyLastSent.value = data.daily_activity_email_last_sent_date || '';
            }
            dailyActivityClientSchedules = Array.isArray(data.daily_activity_client_schedules)
                ? data.daily_activity_client_schedules
                : [];
            renderDailyActivityScheduleTable();

            // Toggle SMTP fields visibility
            toggleSmtpFields();
        }
    } catch (error) {
        console.error('Failed to load settings:', error);
        showMessage('error', 'Failed to load settings: ' + error.message);
    }
}

/**
 * Save settings to API
 */
async function saveSettings(e) {
    e.preventDefault();

    if (isLoading) return;

    const formData = collectFormData();

    try {
        isLoading = true;
        showMessage('', 'Saving settings...', 'info');

        const response = await window.api.request('/api/settings', {
            method: 'POST',
            body: JSON.stringify(formData),
        });

        if (response && response.success) {
            showMessage('success', 'Settings saved successfully!');
            // Reload settings to ensure we have the latest data
            await loadSettings();
        } else {
            showMessage('error', response.message || 'Failed to save settings');
        }
    } catch (error) {
        console.error('Failed to save settings:', error);
        showMessage('error', 'Failed to save settings: ' + error.message);
    } finally {
        isLoading = false;
    }
}

/**
 * Collect form data
 */
function collectFormData() {
    const payload = {
        // General
        invoice_company_name: getValue('invoice-company-name'),
        invoice_company_address: getValue('invoice-company-address'),
        invoice_tax_number: getValue('invoice-tax-number'),

        // Payment
        payment_etransfer_email: getValue('payment-etransfer-email'),
        payment_bank_info: getValue('payment-bank-info'),
        payment_instructions: getValue('payment-instructions'),

        // Stripe
        stripe_enabled: document.getElementById('stripe-enabled')?.checked || false,
        stripe_publishable_key: getValue('stripe-publishable-key'),

        // Email
        email_mailer: getValue('email-mailer'),
        email_smtp_host: getValue('email-smtp-host'),
        email_smtp_port: getValue('email-smtp-port'),
        email_smtp_username: getValue('email-smtp-username'),
        email_smtp_password: getValue('email-smtp-password'),
        email_smtp_encryption: getValue('email-smtp-encryption'),
        email_from_address: getValue('email-from-address'),
        email_from_name: getValue('email-from-name'),

        // Daily activity report automation
        daily_activity_email_enabled: document.getElementById('daily-activity-email-enabled')?.checked || false,
        daily_activity_email_recipients: getValue('daily-activity-email-recipients'),
        daily_activity_email_send_time: getValue('daily-activity-email-send-time'),
        daily_activity_client_schedules: collectDailyActivityClientSchedules(),
    };

    const secretInput = getStripeSecretInput();
    if (secretInput) {
        const secretValue = secretInput.value.trim();

        if (secretValue !== '') {
            payload.stripe_secret_key = secretValue;
        } else if (secretInput.dataset.hasStoredValue === 'true') {
            if (secretInput.dataset.cleared === 'true') {
                payload.stripe_secret_key = null;
            }
        } else {
            payload.stripe_secret_key = null;
        }
    }

    return payload;
}

/**
 * Get value from form field
 */
function getValue(id) {
    const element = document.getElementById(id);
    return element ? element.value.trim() : null;
}

/**
 * Set value to form field
 */
function setValue(id, value) {
    const element = document.getElementById(id);
    if (element) {
        element.value = value || '';
    }
}

function renderDailyActivityScheduleTable() {
    const tbody = document.getElementById('daily-activity-schedules-body');
    if (!tbody) {
        return;
    }

    if (!Array.isArray(dailyActivityClientSchedules) || dailyActivityClientSchedules.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="7" class="automation-empty-row">No client emails found in projects yet.</td>
            </tr>
        `;
        return;
    }

    tbody.innerHTML = dailyActivityClientSchedules.map((row, index) => {
        const originalClientName = row.client_name || null;
        const originalClientEmail = (row.client_email || '').toLowerCase();
        const clientName = escapeHtml(originalClientName || 'Unnamed Client');
        const clientEmail = escapeHtml(originalClientEmail);
        const enabledChecked = row.enabled ? 'checked' : '';
        const sendTime = escapeHtml(row.send_time || '18:00');
        const subject = escapeHtml(row.subject || '');
        const selectedColumns = new Set(normalizeActivityColumns(row.activity_columns));
        const activityTags = ACTIVITY_COLUMN_OPTIONS.map((option) => {
            const activeClass = selectedColumns.has(option.key) ? 'active' : '';
            return `<button type="button" class="automation-tag ${activeClass}" data-column-tag="${option.key}" aria-pressed="${selectedColumns.has(option.key) ? 'true' : 'false'}">${escapeHtml(option.label)}</button>`;
        }).join('');
        const lastSent = escapeHtml(row.last_sent_date || '-');

        return `
            <tr data-schedule-index="${index}">
                <td><span class="automation-client-name">${clientName}</span></td>
                <td><span class="automation-client-email">${clientEmail}</span></td>
                <td class="automation-cell-center">
                    <input type="checkbox" data-schedule-enabled data-index="${index}" ${enabledChecked}>
                </td>
                <td class="automation-cell-center">
                    <input type="time" class="automation-input automation-time-input" data-schedule-send-time data-index="${index}" value="${sendTime}">
                </td>
                <td>
                    <input
                        class="automation-input"
                        type="text"
                        data-schedule-subject
                        data-index="${index}"
                        value="${subject}"
                        placeholder="Daily Activity Report - {date} ({client_name})"
                    >
                </td>
                <td>
                    <div class="automation-tags" data-schedule-columns data-index="${index}">
                        ${activityTags}
                    </div>
                </td>
                <td class="automation-cell-center" style="color:#64748b;">
                    ${lastSent}
                </td>
            </tr>
        `;
    }).join('');
}

function collectDailyActivityClientSchedules() {
    const rows = [];
    const tableBody = document.getElementById('daily-activity-schedules-body');
    if (!tableBody) {
        return rows;
    }

    const trElements = Array.from(tableBody.querySelectorAll('tr'));
    trElements.forEach((tr) => {
        const index = Number(tr.getAttribute('data-schedule-index'));
        const baseRow = Number.isInteger(index) && index >= 0 ? dailyActivityClientSchedules[index] : null;
        const enabledEl = tr.querySelector('[data-schedule-enabled]');
        const sendTimeEl = tr.querySelector('[data-schedule-send-time]');
        const subjectEl = tr.querySelector('[data-schedule-subject]');
        const columnsEl = tr.querySelector('[data-schedule-columns]');

        if (!baseRow || !enabledEl || !sendTimeEl) {
            return;
        }

        const clientEmail = String(baseRow.client_email || '').trim().toLowerCase();
        if (!clientEmail) {
            return;
        }

        rows.push({
            client_email: clientEmail,
            client_name: String(baseRow.client_name || '').trim() || null,
            enabled: Boolean(enabledEl.checked),
            send_time: (sendTimeEl.value || '18:00').trim() || '18:00',
            subject: (subjectEl?.value || '').trim() || null,
            activity_columns: columnsEl
                ? normalizeActivityColumns(
                    Array.from(columnsEl.querySelectorAll('[data-column-tag].active'))
                        .map((tag) => String(tag.getAttribute('data-column-tag') || '').trim())
                        .join(',')
                ).join(',')
                : 'date,project,clock_in,clock_out,duration,description',
        });
    });

    return rows;
}

function escapeHtml(value) {
    return String(value)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#39;');
}

function normalizeActivityColumns(raw) {
    const allowed = ACTIVITY_COLUMN_OPTIONS.map((option) => option.key);
    const values = String(raw || '')
        .split(/[,\s;]+/)
        .map((item) => item.trim().toLowerCase())
        .filter((item) => item !== '' && allowed.includes(item));

    const unique = Array.from(new Set(values));
    return unique.length > 0 ? unique : allowed;
}

/**
 * Show message in save bar
 */
function showMessage(type, message, customType = null) {
    const messageEl = document.getElementById('save-message');
    if (!messageEl) return;

    messageEl.textContent = message;
    messageEl.className = 'save-bar-message';

    if (type === 'success') {
        messageEl.classList.add('success');
        messageEl.innerHTML = '<i class="fas fa-check-circle"></i> ' + message;
        // Auto-hide after 5 seconds
        setTimeout(() => {
            messageEl.style.display = 'none';
        }, 5000);
    } else if (type === 'error') {
        messageEl.classList.add('error');
        messageEl.innerHTML = '<i class="fas fa-exclamation-circle"></i> ' + message;
    } else if (customType === 'info') {
        messageEl.style.display = 'flex';
        messageEl.style.alignItems = 'center';
        messageEl.style.gap = '8px';
        messageEl.style.background = '#e0f2fe';
        messageEl.style.color = '#0c4a6e';
        messageEl.style.border = '1px solid #0284c7';
        messageEl.innerHTML = '<i class="fas fa-spinner fa-spin"></i> ' + message;
    }
}

/**
 * Toggle SMTP fields based on email mailer selection
 */
window.toggleSmtpFields = function() {
    const mailerSelect = document.getElementById('email-mailer');
    const smtpSection = document.getElementById('smtp-settings');

    if (mailerSelect && smtpSection) {
        smtpSection.style.display = mailerSelect.value === 'smtp' ? 'block' : 'none';
    }
};

/**
 * Toggle Stripe fields based on enable checkbox
 * SECURITY: Moved from inline script to external JS file for CSP compliance
 */
window.toggleStripeFields = function() {
    const enabled = document.getElementById('stripe-enabled')?.checked;
    const stripeFields = document.getElementById('stripe-fields');
    if (stripeFields) {
        stripeFields.style.display = enabled ? 'block' : 'none';
    }
};

/**
 * Send test email
 */
window.sendTestEmail = async function() {
    if (isLoading) return;

    const testEmailAddress = document.getElementById('test-email-address')?.value?.trim();

    if (!testEmailAddress) {
        showTestEmailMessage('error', 'Please enter an email address to send the test email to.');
        return;
    }

    // Validate email format
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(testEmailAddress)) {
        showTestEmailMessage('error', 'Please enter a valid email address.');
        return;
    }

    const payload = {
        test_email: testEmailAddress,
        email_mailer: getValue('email-mailer') || 'default',
        email_smtp_host: getValue('email-smtp-host'),
        email_smtp_port: getValue('email-smtp-port'),
        email_smtp_username: getValue('email-smtp-username'),
        email_smtp_password: getValue('email-smtp-password'),
        email_smtp_encryption: getValue('email-smtp-encryption'),
        email_from_address: getValue('email-from-address'),
        email_from_name: getValue('email-from-name'),
    };

    try {
        isLoading = true;
        showTestEmailMessage('info', 'Sending test email...');

        const response = await window.api.request('/api/settings/test-email', {
            method: 'POST',
            body: JSON.stringify(payload),
        });

        if (response && response.success) {
            showTestEmailMessage('success', response.message || 'Test email sent! Check your inbox and spam folder.');
        } else {
            showTestEmailMessage('error', response.message || 'Failed to send test email.');
        }
    } catch (error) {
        console.error('Test email failed:', error);
        showTestEmailMessage('error', 'Failed to send test email: ' + (error.message || 'Unknown error'));
    } finally {
        isLoading = false;
    }
};

/**
 * Show test email message
 */
function showTestEmailMessage(type, message) {
    const messageEl = document.getElementById('test-email-message');
    if (!messageEl) return;

    messageEl.style.display = 'flex';
    messageEl.style.alignItems = 'center';
    messageEl.style.gap = '8px';
    messageEl.textContent = message;

    if (type === 'success') {
        messageEl.style.background = '#d1fae5';
        messageEl.style.color = '#065f46';
        messageEl.style.border = '1px solid #10b981';
        messageEl.innerHTML = '<i class="fas fa-check-circle"></i> ' + message;
    } else if (type === 'error') {
        messageEl.style.background = '#fee2e2';
        messageEl.style.color = '#991b1b';
        messageEl.style.border = '1px solid #ef4444';
        messageEl.innerHTML = '<i class="fas fa-exclamation-circle"></i> ' + message;
    } else if (type === 'info') {
        messageEl.style.background = '#e0f2fe';
        messageEl.style.color = '#0c4a6e';
        messageEl.style.border = '1px solid #0284c7';
        messageEl.innerHTML = '<i class="fas fa-spinner fa-spin"></i> ' + message;
    }
}

/**
 * Initialize on page load
 */
document.addEventListener('DOMContentLoaded', function() {
    // Load settings
    loadSettings();

    // Attach form submit handler
    const form = document.getElementById('settings-form');
    if (form) {
        form.addEventListener('submit', saveSettings);
    }

    // Initialize SMTP toggle
    const emailMailer = document.getElementById('email-mailer');
    if (emailMailer) {
        emailMailer.addEventListener('change', toggleSmtpFields);
    }

    // Initialize Stripe toggle
    const stripeEnabled = document.getElementById('stripe-enabled');
    if (stripeEnabled) {
        stripeEnabled.addEventListener('change', toggleStripeFields);
    }

    const clearCacheBtn = document.getElementById('clear-cache-btn');
    if (clearCacheBtn) {
        clearCacheBtn.addEventListener('click', clearApplicationCaches);
    }

    const scheduleBody = document.getElementById('daily-activity-schedules-body');
    if (scheduleBody) {
        scheduleBody.addEventListener('click', (event) => {
            const tag = event.target.closest('[data-column-tag]');
            if (!tag) {
                return;
            }

            tag.classList.toggle('active');
            tag.setAttribute('aria-pressed', tag.classList.contains('active') ? 'true' : 'false');
        });
    }
});
