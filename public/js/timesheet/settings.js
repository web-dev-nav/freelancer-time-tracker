/**
 * Settings Module
 *
 * Manages application-wide settings such as invoice/company information
 * and email delivery preferences.
 */

let isFormInitialized = false;
let isLoading = false;

/**
 * Load settings values and populate the form.
 */
async function loadSettings() {
    if (isLoading) return;

    try {
        isLoading = true;
        setLoadingState(true);

        const response = await window.api.request('/api/settings');

        if (response.success) {
            applySettingsToForm(response.data || {});
        } else {
            window.notify.warning(response.message || 'Using default settings.');
            applyDefaultValues();
        }
    } catch (error) {
        console.warn('Settings request failed, falling back to defaults.', error);
        applyDefaultValues();
    } finally {
        isLoading = false;
        setLoadingState(false);
    }
}

/**
 * Persist settings changes.
 * @param {Event} e
 */
async function saveSettings(e) {
    e.preventDefault();

    if (isLoading) return;

    const payload = collectFormValues();

    try {
        isLoading = true;
        setLoadingState(true);
        hideMessage();

        const response = await window.api.request('/api/settings', {
            method: 'POST',
            body: JSON.stringify(payload),
        });

        if (response.success) {
            applySettingsToForm(response.data || {});
            showMessage('success', 'Settings saved successfully!');
        } else {
            showMessage('error', response.message || 'Unable to save settings.');
        }
    } catch (error) {
        console.error('Failed to save settings:', error);
        showMessage('error', 'Failed to save settings. Please try again.');
    } finally {
        isLoading = false;
        setLoadingState(false);
    }
}

/**
 * Show settings modal.
 */
export async function showSettingsModal() {
    const modal = document.getElementById('settings-modal');
    const overlay = document.getElementById('modal-overlay');

    if (!modal || !overlay) return;

    ensureFormInitialized();
    toggleSmtpFields();

    modal.classList.add('show');
    overlay.classList.add('show');
    document.body.style.overflow = 'hidden';

    await loadSettings();
}

/**
 * Hide settings modal.
 */
export function hideSettingsModal() {
    const modal = document.getElementById('settings-modal');
    const overlay = document.getElementById('modal-overlay');

    if (!modal || !overlay) return;

    setLoadingState(false);
    hideMessage();

    if (modal.classList.contains('show')) {
        modal.classList.remove('show');
        overlay.classList.remove('show');
        document.body.style.overflow = 'auto';
    }
}

/**
 * Toggle loading indicator and button state.
 * @param {boolean} loading
 */
function setLoadingState(loading) {
    const submitButton = document.getElementById('settings-save-btn');
    const loadingIndicator = document.getElementById('settings-loading');

    if (submitButton) submitButton.disabled = loading;
    if (loadingIndicator) loadingIndicator.style.display = loading ? 'flex' : 'none';
}

function ensureFormInitialized() {
    if (isFormInitialized) return;

    const form = document.getElementById('settings-form');
    if (!form) return;

    form.addEventListener('submit', saveSettings);

    const mailerSelect = document.getElementById('email-mailer');
    if (mailerSelect) {
        mailerSelect.addEventListener('change', toggleSmtpFields);
    }

    isFormInitialized = true;
}

function collectFormValues() {
    const getValue = (id) => {
        const el = document.getElementById(id);
        if (!el) return null;
        const value = el.value.trim();
        return value.length > 0 ? value : null;
    };

    return {
        invoice_company_name: getValue('invoice-company-name'),
        invoice_company_address: getValue('invoice-company-address'),
        invoice_tax_number: getValue('invoice-tax-number'),
        payment_etransfer_email: getValue('payment-etransfer-email'),
        payment_bank_info: getValue('payment-bank-info'),
        payment_instructions: getValue('payment-instructions'),
        email_mailer: document.getElementById('email-mailer')?.value || 'default',
        email_smtp_host: getValue('email-smtp-host'),
        email_smtp_port: getValue('email-smtp-port'),
        email_smtp_username: getValue('email-smtp-username'),
        email_smtp_password: getValue('email-smtp-password'),
        email_smtp_encryption: document.getElementById('email-smtp-encryption')?.value || null,
        email_from_address: getValue('email-from-address'),
        email_from_name: getValue('email-from-name'),
    };
}

function applySettingsToForm(data) {
    const setValue = (id, value) => {
        const el = document.getElementById(id);
        if (!el) return;
        el.value = value ?? '';
    };

    setValue('invoice-company-name', data.invoice_company_name ?? window.utils?.appName ?? '');
    setValue('invoice-company-address', data.invoice_company_address ?? '');
    setValue('invoice-tax-number', data.invoice_tax_number ?? '');
    setValue('payment-etransfer-email', data.payment_etransfer_email ?? '');
    setValue('payment-bank-info', data.payment_bank_info ?? '');
    setValue('payment-instructions', data.payment_instructions ?? '');
    setValue('email-mailer', data.email_mailer ?? 'default');
    setValue('email-smtp-host', data.email_smtp_host ?? '');
    setValue('email-smtp-port', data.email_smtp_port ?? '');
    setValue('email-smtp-username', data.email_smtp_username ?? '');
    setValue('email-smtp-password', data.email_smtp_password ?? '');
    setValue('email-smtp-encryption', data.email_smtp_encryption ?? '');
    setValue('email-from-address', data.email_from_address ?? '');
    setValue('email-from-name', data.email_from_name ?? (window.utils?.appName ?? ''));

    toggleSmtpFields();
}

function applyDefaultValues() {
    applySettingsToForm({
        invoice_company_name: window.utils?.appName || 'Freelancer',
        invoice_company_address: '',
        invoice_tax_number: '',
        email_mailer: 'default',
        email_smtp_host: '',
        email_smtp_port: '',
        email_smtp_username: '',
        email_smtp_password: '',
        email_smtp_encryption: '',
        email_from_address: '',
        email_from_name: window.utils?.appName || 'Freelancer',
    });
}

function toggleSmtpFields() {
    const mailerSelect = document.getElementById('email-mailer');
    const smtpSection = document.getElementById('smtp-settings');

    if (!mailerSelect || !smtpSection) return;

    const isSmtp = mailerSelect.value === 'smtp';
    smtpSection.style.display = isSmtp ? 'grid' : 'none';
}

/**
 * Show message in the modal.
 * @param {string} type - 'success' or 'error'
 * @param {string} message - The message text
 */
function showMessage(type, message) {
    const messageEl = document.getElementById('settings-message');
    const iconEl = document.getElementById('settings-message-icon');
    const textEl = document.getElementById('settings-message-text');

    if (!messageEl || !iconEl || !textEl) return;

    // Set message text
    textEl.textContent = message;

    // Set icon and colors based on type
    if (type === 'success') {
        messageEl.style.backgroundColor = '#d1fae5';
        messageEl.style.border = '2px solid #10b981';
        messageEl.style.color = '#065f46';
        iconEl.className = 'fas fa-check-circle';
        iconEl.style.color = '#10b981';
    } else if (type === 'error') {
        messageEl.style.backgroundColor = '#fee2e2';
        messageEl.style.border = '2px solid #ef4444';
        messageEl.style.color = '#991b1b';
        iconEl.className = 'fas fa-exclamation-circle';
        iconEl.style.color = '#ef4444';
    }

    // Show the message with flex display
    messageEl.style.display = 'flex';

    // Scroll the message into view
    messageEl.scrollIntoView({ behavior: 'smooth', block: 'nearest' });

    // Auto-hide success messages after 5 seconds
    if (type === 'success') {
        setTimeout(() => {
            hideMessage();
        }, 5000);
    }
}

/**
 * Hide the message in the modal.
 */
function hideMessage() {
    const messageEl = document.getElementById('settings-message');
    if (messageEl) {
        messageEl.style.display = 'none';
    }
}

/**
 * Send a test email to verify email configuration.
 */
async function sendTestEmail() {
    if (isLoading) return;

    const testEmailAddress = document.getElementById('test-email-address')?.value?.trim();

    if (!testEmailAddress) {
        showMessage('error', 'Please enter an email address to send the test email to.');
        return;
    }

    // Validate email format
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(testEmailAddress)) {
        showMessage('error', 'Please enter a valid email address.');
        return;
    }

    const payload = {
        test_email: testEmailAddress,
        email_mailer: document.getElementById('email-mailer')?.value || 'default',
        email_smtp_host: document.getElementById('email-smtp-host')?.value?.trim() || null,
        email_smtp_port: document.getElementById('email-smtp-port')?.value?.trim() || null,
        email_smtp_username: document.getElementById('email-smtp-username')?.value?.trim() || null,
        email_smtp_password: document.getElementById('email-smtp-password')?.value?.trim() || null,
        email_smtp_encryption: document.getElementById('email-smtp-encryption')?.value || null,
        email_from_address: document.getElementById('email-from-address')?.value?.trim() || null,
        email_from_name: document.getElementById('email-from-name')?.value?.trim() || null,
    };

    try {
        isLoading = true;
        setLoadingState(true);
        hideMessage();

        const response = await window.api.request('/api/settings/test-email', {
            method: 'POST',
            body: JSON.stringify(payload),
        });

        if (response.success) {
            showMessage('success', response.message || 'Test email sent! Check your inbox and spam folder.');
        } else {
            showMessage('error', response.message || 'Failed to send test email.');
        }
    } catch (error) {
        console.error('Test email failed:', error);
        showMessage('error', 'Failed to send test email: ' + (error.message || 'Unknown error'));
    } finally {
        isLoading = false;
        setLoadingState(false);
    }
}

// Make sendTestEmail available globally for onclick handler
window.sendTestEmail = sendTestEmail;
