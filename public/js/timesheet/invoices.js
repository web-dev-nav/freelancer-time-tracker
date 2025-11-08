/**
 * Invoices Module
 *
 * Handles invoice management including creating, viewing, sending,
 * and managing invoices with PDF generation and email functionality.
 */

import * as State from './state.js';

// Module state
let currentInvoice = null;
let currentEditingInvoice = null;
let currentEditingItem = null;
let createInvoiceProjects = [];
let createInvoiceItems = []; // Temporary items for create mode
let createItemIdCounter = 1; // Temporary ID counter for create mode items

/**
 * Format a date string so it can be used in an <input type="date">
 * @param {string|null} value
 * @returns {string}
 */
function formatDateForInput(value) {
    if (!value) {
        return '';
    }

    if (typeof value === 'string') {
        if (value.includes('T')) {
            return value.split('T')[0];
        }
        return value;
    }

    const date = new Date(value);
    if (!Number.isNaN(date.getTime())) {
        return date.toISOString().split('T')[0];
    }

    return '';
}

/**
 * Load invoices for the invoices tab
 */
export async function loadInvoices() {
    try {
        const status = document.getElementById('invoice-status-filter')?.value || 'all';
        const projectId = State.selectedProjectId || 'all';

        const response = await window.api.request(
            `/api/invoices?project_id=${projectId}&status=${status}&per_page=50`
        );

        if (response.data) {
            displayInvoices(response.data.data || response.data);
            loadInvoiceStats();
        }
    } catch (error) {
        window.notify.error('Failed to load invoices: ' + error.message);
    }
}

/**
 * Load invoice statistics
 */
export async function loadInvoiceStats() {
    try {
        const projectId = State.selectedProjectId || 'all';
        const response = await window.api.request(`/api/invoices/stats?project_id=${projectId}`);

        if (response) {
            document.getElementById('total-invoices-count').textContent = response.total_invoices || 0;
            document.getElementById('pending-invoices-count').textContent =
                (response.draft_count || 0) + (response.sent_count || 0);
            document.getElementById('paid-invoices-count').textContent = response.paid_count || 0;
            document.getElementById('total-revenue').textContent =
                '$' + (response.total_revenue || 0).toFixed(2);
        }
    } catch (error) {
        console.error('Failed to load invoice stats:', error);
    }
}

/**
 * Display invoices in the list
 */
export function displayInvoices(invoices) {
    const list = document.getElementById('invoices-list');

    if (!invoices || invoices.length === 0) {
        list.innerHTML = `
            <div class="empty-state">
                <i class="fas fa-file-invoice-dollar"></i>
                <h3>No Invoices Found</h3>
                <p>Create your first invoice to get started</p>
            </div>
        `;
        return;
    }

    list.innerHTML = invoices.map(invoice => {
        const statusClass = invoice.status === 'paid' ? 'success' :
                          invoice.status === 'sent' ? 'warning' :
                          invoice.status === 'draft' ? 'secondary' : 'danger';

        const isOverdue = invoice.is_overdue;

        return `
            <div class="invoice-card">
                <div class="invoice-card-header">
                    <div>
                        <h3>${invoice.invoice_number}</h3>
                        <p class="invoice-client">${invoice.client_name}</p>
                    </div>
                    <div class="invoice-card-status">
                        <span class="badge badge-${statusClass}">
                            ${isOverdue ? 'Overdue' : invoice.status.toUpperCase()}
                        </span>
                        <div class="invoice-amount">$${parseFloat(invoice.total).toFixed(2)}</div>
                    </div>
                </div>

                <div class="invoice-card-body">
                    <div class="invoice-detail">
                        <i class="fas fa-calendar"></i>
                        <span>Invoice Date: ${invoice.formatted_invoice_date || invoice.invoice_date}</span>
                    </div>
                    <div class="invoice-detail">
                        <i class="fas fa-calendar-check"></i>
                        <span>Due Date: ${invoice.formatted_due_date || invoice.due_date}</span>
                    </div>
                    <div class="invoice-detail">
                        <i class="fas fa-folder"></i>
                        <span>Project: ${invoice.project?.name || 'N/A'}</span>
                    </div>
                    ${invoice.sent_at ? `
                        <div class="invoice-detail">
                            <i class="fas fa-paper-plane"></i>
                            <span>Sent: ${new Date(invoice.sent_at).toLocaleDateString()}</span>
                        </div>
                    ` : ''}
                </div>

                <div class="invoice-card-actions">
                    ${invoice.status === 'draft' ? `
                        <button class="btn btn-sm btn-primary" onclick="showEditInvoiceModal(${invoice.id})" title="Edit Invoice">
                            <i class="fas fa-edit"></i>
                        </button>
                    ` : ''}
                    <button class="btn btn-sm btn-secondary" onclick="downloadInvoicePDF(${invoice.id})" title="Download PDF">
                        <i class="fas fa-download"></i>
                    </button>
                    <button class="btn btn-sm btn-secondary" onclick="previewInvoicePDF(${invoice.id})" title="Preview PDF">
                        <i class="fas fa-eye"></i>
                    </button>
                    ${invoice.status !== 'paid' ? `
                        <button class="btn btn-sm btn-primary" onclick="showSendInvoiceModal(${invoice.id})" title="Send via Email">
                            <i class="fas fa-paper-plane"></i>
                        </button>
                    ` : ''}
                    ${invoice.status !== 'paid' ? `
                        <button class="btn btn-sm btn-success" onclick="markInvoiceAsPaid(${invoice.id})" title="Mark as Paid">
                            <i class="fas fa-check"></i>
                        </button>
                    ` : ''}
                    ${invoice.status === 'draft' ? `
                        <button class="btn btn-sm btn-danger" onclick="deleteInvoice(${invoice.id})" title="Delete">
                            <i class="fas fa-trash"></i>
                        </button>
                    ` : ''}
                    ${(invoice.status === 'sent' || invoice.status === 'draft') ? `
                        <button class="btn btn-sm btn-warning" onclick="cancelInvoice(${invoice.id})" title="Cancel Invoice">
                            <i class="fas fa-ban"></i>
                        </button>
                    ` : ''}
                </div>
            </div>
        `;
    }).join('');
}

/**
 * Show create invoice modal
 */
export async function showCreateInvoiceModal() {
    try {
        // Attach form submit handler (only once)
        const form = document.getElementById('create-invoice-form');
        if (form && !form.dataset.listenerAttached) {
            form.addEventListener('submit', createInvoice);
            form.dataset.listenerAttached = 'true';
            console.log('Invoice form submit handler attached');
        }
        if (form) {
            form.reset();
        }

        resetInvoiceClientFields();
        createInvoiceProjects = [];
        createInvoiceItems = [];
        createItemIdCounter = 1;

        const selector = document.getElementById('invoice-project-id');
        if (selector) {
            selector.innerHTML = '<option value="">Select a project...</option>';
        }

        // Reset items table and totals
        displayCreateInvoiceItems();
        updateCreateInvoiceTotals();

        // Load active projects for selector
        const response = await window.api.request('/api/projects/active');

        if (response.success) {
            createInvoiceProjects = response.data || [];
            if (selector) {
                selector.innerHTML = '<option value="">Select a project...</option>' +
                    createInvoiceProjects.map(p => `
                        <option value="${p.id}" data-rate="${p.hourly_rate || 0}" data-tax="${p.has_tax ? 13 : 0}">
                            ${p.name}${p.client_name ? ' (' + p.client_name + ')' : ''}
                        </option>
                    `).join('');
            }
        }

        // Set default dates
        const today = new Date().toISOString().split('T')[0];
        const dueDate = new Date();
        dueDate.setDate(dueDate.getDate() + 30);

        document.getElementById('invoice-date').value = today;
        document.getElementById('due-date').value = dueDate.toISOString().split('T')[0];

        // Show modal
        document.getElementById('create-invoice-modal').classList.add('show');
        document.getElementById('modal-overlay').classList.add('show');
        document.body.style.overflow = 'hidden';
    } catch (error) {
        window.notify.error('Failed to open invoice modal: ' + error.message);
    }
}

/**
 * Hide create invoice modal
 */
export function hideCreateInvoiceModal() {
    document.getElementById('create-invoice-modal').classList.remove('show');
    document.getElementById('modal-overlay').classList.remove('show');
    document.body.style.overflow = 'auto';
    const form = document.getElementById('create-invoice-form');
    if (form) {
        form.reset();
    }
    resetInvoiceClientFields();
}

/**
 * Handle project selection changes in the create invoice modal
 */
export function handleCreateInvoiceProjectChange() {
    const projectSelect = document.getElementById('invoice-project-id');
    if (!projectSelect) {
        return;
    }

    const projectId = projectSelect.value;
    const project = createInvoiceProjects.find(p => String(p.id) === projectId) || null;

    if (project) {
        populateInvoiceClientFields(project);
        // Update tax rate display based on project
        const taxRate = project.has_tax ? 13 : 0;
        const taxRateElem = document.getElementById('create-invoice-tax-rate');
        if (taxRateElem) {
            taxRateElem.textContent = taxRate;
        }
        updateCreateInvoiceTotals();
    } else {
        resetInvoiceClientFields();
        const taxRateElem = document.getElementById('create-invoice-tax-rate');
        if (taxRateElem) {
            taxRateElem.textContent = '0';
        }
        updateCreateInvoiceTotals();
    }
}

/**
 * Reset client fields in the create invoice modal
 */
function resetInvoiceClientFields() {
    const nameInput = document.getElementById('invoice-client-name');
    const emailInput = document.getElementById('invoice-client-email');
    const addressInput = document.getElementById('invoice-client-address');

    if (nameInput) nameInput.value = '';
    if (emailInput) emailInput.value = '';
    if (addressInput) addressInput.value = '';
}

/**
 * Populate client fields using project defaults
 * @param {Object|null} project
 */
function populateInvoiceClientFields(project) {
    if (!project) {
        resetInvoiceClientFields();
        return;
    }

    const nameInput = document.getElementById('invoice-client-name');
    const emailInput = document.getElementById('invoice-client-email');
    const addressInput = document.getElementById('invoice-client-address');

    if (nameInput) {
        nameInput.value = project.client_name || project.name || '';
    }

    if (emailInput) {
        emailInput.value = project.client_email || '';
    }

    if (addressInput) {
        addressInput.value = project.client_address || '';
    }
}

/**
 * Create invoice
 */
export async function createInvoice(e) {
    if (e) e.preventDefault();

    console.log('Creating invoice...');

    const projectId = document.getElementById('invoice-project-id').value;
    const invoiceDate = document.getElementById('invoice-date').value;
    const dueDate = document.getElementById('due-date').value;
    const clientName = document.getElementById('invoice-client-name').value.trim();
    const clientEmail = document.getElementById('invoice-client-email').value.trim();
    const clientAddress = document.getElementById('invoice-client-address').value.trim();
    const notes = document.getElementById('invoice-notes').value;
    const description = document.getElementById('invoice-description').value;

    console.log('Project ID:', projectId);
    if (!projectId) {
        window.notify.error('Please select a project');
        return;
    }

    if (!clientName) {
        window.notify.error('Client name is required');
        return;
    }

    // Include invoice items
    const data = {
        project_id: projectId,
        invoice_date: invoiceDate,
        due_date: dueDate,
        time_log_ids: [],
        client_name: clientName,
        client_email: clientEmail || null,
        client_address: clientAddress || null,
        notes: notes || null,
        description: description || null,
        items: createInvoiceItems.map(item => ({
            description: item.description,
            work_date: item.work_date,
            hours: item.hours,
            rate: item.rate
        }))
    };

    console.log('Invoice data:', data);

    try {
        const response = await window.api.request('/api/invoices', {
            method: 'POST',
            body: JSON.stringify(data)
        });

        if (response.invoice || response.data) {
            window.notify.success('Invoice created successfully!');
            hideCreateInvoiceModal();
            loadInvoices();
        }
    } catch (error) {
        window.notify.error('Failed to create invoice: ' + error.message);
    }
}

/**
 * Display items in the create invoice modal
 */
function displayCreateInvoiceItems() {
    const container = document.getElementById('create-invoice-items-table');

    if (!createInvoiceItems || createInvoiceItems.length === 0) {
        container.innerHTML = `
            <div class="empty-state-small">
                <i class="fas fa-info-circle"></i>
                <p>No items added yet</p>
                <p style="font-size: 12px; margin-top: 8px;">Click "Add Item" to add line items to this invoice</p>
            </div>
        `;
        return;
    }

    container.innerHTML = `
        <table class="invoice-items-table-element">
            <thead>
                <tr>
                    <th>Description</th>
                    <th>Date</th>
                    <th>Hours/Qty</th>
                    <th>Rate</th>
                    <th>Amount</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                ${createInvoiceItems.map(item => {
                    const amount = item.hours * item.rate;
                    return `
                        <tr>
                            <td>${item.description}</td>
                            <td>${new Date(item.work_date).toLocaleDateString()}</td>
                            <td>${parseFloat(item.hours).toFixed(2)}</td>
                            <td>$${parseFloat(item.rate).toFixed(2)}</td>
                            <td>$${amount.toFixed(2)}</td>
                            <td>
                                <button class="btn btn-sm btn-secondary" onclick="editCreateInvoiceItem(${item.tempId})" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-danger" onclick="deleteCreateInvoiceItem(${item.tempId})" title="Delete">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    `;
                }).join('')}
            </tbody>
        </table>
    `;
}

/**
 * Update totals in the create invoice modal
 */
function updateCreateInvoiceTotals() {
    const projectSelect = document.getElementById('invoice-project-id');
    const project = projectSelect ? createInvoiceProjects.find(p => String(p.id) === projectSelect.value) : null;
    const taxRate = project && project.has_tax ? 13 : 0;

    const subtotal = createInvoiceItems.reduce((sum, item) => sum + (item.hours * item.rate), 0);
    const taxAmount = subtotal * (taxRate / 100);
    const total = subtotal + taxAmount;

    document.getElementById('create-invoice-subtotal').textContent = '$' + subtotal.toFixed(2);
    document.getElementById('create-invoice-tax-rate').textContent = taxRate.toFixed(0);
    document.getElementById('create-invoice-tax-amount').textContent = '$' + taxAmount.toFixed(2);
    document.getElementById('create-invoice-total').textContent = '$' + total.toFixed(2);
}

/**
 * Show add item modal for create invoice mode
 */
export function showAddItemModalForCreate() {
    const projectId = document.getElementById('invoice-project-id').value;
    if (!projectId) {
        window.notify.error('Please select a project first');
        return;
    }

    const project = createInvoiceProjects.find(p => String(p.id) === projectId);

    // Attach form submit handler (only once)
    const form = document.getElementById('add-item-form');
    if (form && !form.dataset.listenerAttached) {
        form.addEventListener('submit', saveInvoiceItem);
        form.dataset.listenerAttached = 'true';
    }

    // Reset form for new item
    currentEditingItem = null;
    document.getElementById('add-item-modal-title').textContent = 'Add Invoice Item';
    document.getElementById('item-id').value = '';
    document.getElementById('item-invoice-id').value = 'create';
    document.getElementById('add-item-form').reset();

    // Set default work date to today
    const today = new Date().toISOString().split('T')[0];
    document.getElementById('item-work-date').value = today;

    // Set default rate from project
    if (project) {
        document.getElementById('item-rate').value = parseFloat(project.hourly_rate || 0).toFixed(2);
    }

    // Show modal
    document.getElementById('add-item-modal').classList.add('show');
    document.getElementById('modal-overlay').classList.add('show');
    document.body.style.overflow = 'hidden';
}

/**
 * Edit an item in create invoice mode
 */
export function editCreateInvoiceItem(tempId) {
    const item = createInvoiceItems.find(i => i.tempId === tempId);
    if (!item) return;

    currentEditingItem = item;
    document.getElementById('add-item-modal-title').textContent = 'Edit Invoice Item';
    document.getElementById('item-id').value = tempId;
    document.getElementById('item-invoice-id').value = 'create';
    document.getElementById('item-description').value = item.description;
    document.getElementById('item-work-date').value = item.work_date;
    document.getElementById('item-hours').value = parseFloat(item.hours).toFixed(2);
    document.getElementById('item-rate').value = parseFloat(item.rate).toFixed(2);
    document.getElementById('item-amount').value = (item.hours * item.rate).toFixed(2);

    // Show modal
    document.getElementById('add-item-modal').classList.add('show');
    document.getElementById('modal-overlay').classList.add('show');
    document.body.style.overflow = 'hidden';
}

/**
 * Delete an item from create invoice mode
 */
export function deleteCreateInvoiceItem(tempId) {
    if (!confirm('Are you sure you want to delete this item?')) {
        return;
    }

    createInvoiceItems = createInvoiceItems.filter(i => i.tempId !== tempId);
    displayCreateInvoiceItems();
    updateCreateInvoiceTotals();
    window.notify.success('Item deleted');
}

/**
 * Generate professional email body with payment instructions
 */
async function generateInvoiceEmailBody(invoice) {
    try {
        // Get settings with payment instructions
        const settingsResponse = await window.api.request('/api/settings');
        const settings = settingsResponse.data || {};

        const companyName = invoice.company_name || settings.invoice_company_name || window.utils?.appName || 'Your Company';

        // Calculate current month bill period
        const now = new Date();
        const startOfMonth = new Date(now.getFullYear(), now.getMonth(), 1);
        const endOfMonth = new Date(now.getFullYear(), now.getMonth() + 1, 0);

        const formatDate = (date) => {
            const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
            return `${months[date.getMonth()]} ${date.getDate()}, ${date.getFullYear()}`;
        };

        const startDate = formatDate(startOfMonth);
        const endDate = formatDate(endOfMonth);
        const dueDate = invoice.formatted_due_date || invoice.due_date;

        // Build email body
        let body = `Thank you for choosing ${companyName}. The invoice for bill period (${startDate} - ${endDate}) is attached.\n\n`;
        body += `The total amount $${parseFloat(invoice.total).toFixed(2)} will be due on ${dueDate}.\n\n`;

        // Add payment instructions
        let hasPaymentInfo = false;
        body += 'Payment Instructions:\n';
        let instructionNumber = 1;

        // E-Transfer
        if (settings.payment_etransfer_email) {
            body += `${instructionNumber}. By Interac e-Transfer\n`;
            body += `   Send to: ${settings.payment_etransfer_email}\n`;
            body += `   Reference: Invoice ${invoice.invoice_number}\n\n`;
            instructionNumber++;
            hasPaymentInfo = true;
        }

        // Direct Deposit
        if (settings.payment_bank_info) {
            body += `${instructionNumber}. By Direct Deposit\n`;
            const bankLines = settings.payment_bank_info.split('\n');
            bankLines.forEach(line => {
                if (line.trim()) {
                    body += `   ${line.trim()}\n`;
                }
            });
            body += '\n';
            instructionNumber++;
            hasPaymentInfo = true;
        }

        // Additional instructions
        if (settings.payment_instructions) {
            body += `${instructionNumber}. ${settings.payment_instructions}\n\n`;
            instructionNumber++;
            hasPaymentInfo = true;
        }

        // If no payment info is configured in settings, show a reminder
        if (!hasPaymentInfo) {
            body += 'Please configure payment instructions in Settings.\n\n';
        }

        body += "If you have any questions, please don't hesitate to contact us.\n\n";
        body += `Best regards,\n${companyName}`;

        return body;
    } catch (error) {
        console.error('Failed to generate email body:', error);
        return `Dear ${invoice.client_name || 'Client'},\n\nPlease find attached invoice ${invoice.invoice_number}.\n\nThank you for your business!`;
    }
}

/**
 * Show send invoice modal
 */
export async function showSendInvoiceModal(invoiceId) {
    try {
        // Attach form submit handler (only once)
        const form = document.getElementById('send-invoice-form');
        if (form && !form.dataset.listenerAttached) {
            form.addEventListener('submit', sendInvoice);
            form.dataset.listenerAttached = 'true';
            console.log('Send invoice form submit handler attached');
        }

        const response = await window.api.request(`/api/invoices/${invoiceId}`);

        if (response) {
            currentInvoice = response;

            const companyName =
                response.company_name ||
                response.project?.name ||
                window.utils?.appName ||
                'Invoice';

            document.getElementById('send-invoice-id').value = invoiceId;
            document.getElementById('send-invoice-email').value = response.client_email || '';
            document.getElementById('send-invoice-subject').value =
                `Invoice ${response.invoice_number} from ${companyName}`;

            // Generate and pre-fill the email message
            const emailBody = await generateInvoiceEmailBody(response);
            document.getElementById('send-invoice-message').value = emailBody;

            document.getElementById('send-invoice-modal').classList.add('show');
            document.getElementById('modal-overlay').classList.add('show');
            document.body.style.overflow = 'hidden';
        }
    } catch (error) {
        window.notify.error('Failed to load invoice: ' + error.message);
    }
}

/**
 * Hide send invoice modal
 */
export function hideSendInvoiceModal() {
    document.getElementById('send-invoice-modal').classList.remove('show');
    document.getElementById('modal-overlay').classList.remove('show');
    document.body.style.overflow = 'auto';
    document.getElementById('send-invoice-form').reset();
    hideInvoiceMessage();
    currentInvoice = null;
}

/**
 * Send invoice via email
 */
export async function sendInvoice(e) {
    e.preventDefault();

    const invoiceId = document.getElementById('send-invoice-id').value;
    const email = document.getElementById('send-invoice-email').value;
    const subject = document.getElementById('send-invoice-subject').value;
    const message = document.getElementById('send-invoice-message').value;

    const data = {
        email: email,
        subject: subject || null,
        message: message || null
    };

    try {
        hideInvoiceMessage();
        const response = await window.api.request(`/api/invoices/${invoiceId}/send-email`, {
            method: 'POST',
            body: JSON.stringify(data)
        });

        if (response && response.success) {
            showInvoiceMessage('success', 'Invoice sent successfully!');
            loadInvoices();

            // Auto-close modal after 2 seconds on success
            setTimeout(() => {
                hideSendInvoiceModal();
            }, 2000);
        } else {
            showInvoiceMessage('error', response?.message || 'Failed to send invoice');
        }
    } catch (error) {
        showInvoiceMessage('error', 'Failed to send invoice: ' + error.message);
    }
}

/**
 * Show message in the send invoice modal.
 * @param {string} type - 'success' or 'error'
 * @param {string} message - The message text
 */
function showInvoiceMessage(type, message) {
    const messageEl = document.getElementById('send-invoice-message-alert');
    const iconEl = document.getElementById('send-invoice-message-icon');
    const textEl = document.getElementById('send-invoice-message-text');

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
}

/**
 * Hide the message in the send invoice modal.
 */
function hideInvoiceMessage() {
    const messageEl = document.getElementById('send-invoice-message-alert');
    if (messageEl) {
        messageEl.style.display = 'none';
    }
}

/**
 * Download invoice PDF
 */
export function downloadInvoicePDF(invoiceId) {
    const link = document.createElement('a');
    link.href = `/api/invoices/${invoiceId}/pdf/download`;
    link.download = `invoice-${invoiceId}.pdf`;
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

/**
 * Preview invoice PDF
 */
export function previewInvoicePDF(invoiceId) {
    window.open(`/api/invoices/${invoiceId}/pdf/preview`, '_blank');
}

/**
 * Mark invoice as paid
 */
export async function markInvoiceAsPaid(invoiceId) {
    if (!confirm('Mark this invoice as paid?')) {
        return;
    }

    try {
        const response = await window.api.request(`/api/invoices/${invoiceId}/mark-as-paid`, {
            method: 'POST'
        });

        if (response) {
            window.notify.success('Invoice marked as paid!');
            loadInvoices();
        }
    } catch (error) {
        window.notify.error('Failed to update invoice: ' + error.message);
    }
}

/**
 * Cancel/Archive invoice
 */
export async function cancelInvoice(invoiceId) {
    if (!confirm('Cancel this invoice? This will archive it and stop all reminders. The invoice will be hidden from the active list.')) {
        return;
    }

    try {
        const response = await window.api.request(`/api/invoices/${invoiceId}/cancel`, {
            method: 'POST'
        });

        if (response && response.success) {
            window.notify.success('Invoice cancelled successfully!');
            loadInvoices();
        } else {
            window.notify.error(response.message || 'Failed to cancel invoice');
        }
    } catch (error) {
        window.notify.error('Failed to cancel invoice: ' + error.message);
    }
}

/**
 * Delete invoice
 */
export async function deleteInvoice(invoiceId) {
    if (!confirm('Are you sure you want to delete this invoice? This action cannot be undone.')) {
        return;
    }

    try {
        const response = await window.api.request(`/api/invoices/${invoiceId}`, {
            method: 'DELETE'
        });

        if (response) {
            window.notify.success('Invoice deleted successfully!');
            loadInvoices();
        }
    } catch (error) {
        window.notify.error('Failed to delete invoice: ' + error.message);
    }
}

/**
 * Show edit invoice modal
 */
export async function showEditInvoiceModal(invoiceId) {
    try {
        const response = await window.api.request(`/api/invoices/${invoiceId}`);

        if (response) {
            currentEditingInvoice = response;

            // Populate invoice header fields
            document.getElementById('edit-invoice-id').value = response.id;
            document.getElementById('edit-invoice-number').textContent = response.invoice_number;
            document.getElementById('edit-invoice-date').value = formatDateForInput(response.invoice_date);
            document.getElementById('edit-due-date').value = formatDateForInput(response.due_date);
            document.getElementById('edit-invoice-status').value = response.status;
            document.getElementById('edit-client-name').value = response.client_name || '';
            document.getElementById('edit-client-email').value = response.client_email || '';
            document.getElementById('edit-client-address').value = response.client_address || '';
            document.getElementById('edit-invoice-notes').value = response.notes || '';
            document.getElementById('edit-invoice-description').value = response.description || '';

            // Load invoice items
            await loadInvoiceItems(invoiceId);

            // Update totals display
            updateEditInvoiceTotals();

            // Show modal
            document.getElementById('edit-invoice-modal').classList.add('show');
            document.getElementById('modal-overlay').classList.add('show');
            document.body.style.overflow = 'hidden';
        }
    } catch (error) {
        window.notify.error('Failed to load invoice: ' + error.message);
    }
}

/**
 * Hide edit invoice modal
 */
export function hideEditInvoiceModal() {
    document.getElementById('edit-invoice-modal').classList.remove('show');
    document.getElementById('modal-overlay').classList.remove('show');
    document.body.style.overflow = 'auto';
    currentEditingInvoice = null;
}

/**
 * Save invoice changes
 */
export async function saveInvoiceChanges() {
    const invoiceId = document.getElementById('edit-invoice-id').value;
    const data = {
        invoice_date: document.getElementById('edit-invoice-date').value,
        due_date: document.getElementById('edit-due-date').value,
        client_name: document.getElementById('edit-client-name').value,
        client_email: document.getElementById('edit-client-email').value || null,
        client_address: document.getElementById('edit-client-address').value || null,
        notes: document.getElementById('edit-invoice-notes').value || null,
        description: document.getElementById('edit-invoice-description').value || null
    };

    try {
        const response = await window.api.request(`/api/invoices/${invoiceId}`, {
            method: 'PUT',
            body: JSON.stringify(data)
        });

        if (response) {
            window.notify.success('Invoice updated successfully!');
            hideEditInvoiceModal();
            loadInvoices();
        }
    } catch (error) {
        window.notify.error('Failed to update invoice: ' + error.message);
    }
}

/**
 * Load invoice items
 */
async function loadInvoiceItems(invoiceId) {
    try {
        const response = await window.api.request(`/api/invoices/${invoiceId}`);

        if (response && response.items) {
            displayInvoiceItems(response.items);
            currentEditingInvoice = response;
        }
    } catch (error) {
        console.error('Failed to load invoice items:', error);
    }
}

/**
 * Display invoice items in the table
 */
function displayInvoiceItems(items) {
    const container = document.getElementById('invoice-items-table');

    if (!items || items.length === 0) {
        container.innerHTML = `
            <div class="empty-state-small">
                <i class="fas fa-info-circle"></i>
                <p>No items added yet</p>
                <p style="font-size: 12px; margin-top: 8px;">Click "Add Item" to add line items</p>
            </div>
        `;
        return;
    }

    container.innerHTML = `
        <table class="invoice-items-table-element">
            <thead>
                <tr>
                    <th>Description</th>
                    <th>Date</th>
                    <th>Hours/Qty</th>
                    <th>Rate</th>
                    <th>Amount</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                ${items.map(item => `
                    <tr>
                        <td>${item.description}</td>
                        <td>${new Date(item.work_date).toLocaleDateString()}</td>
                        <td>${parseFloat(item.hours).toFixed(2)}</td>
                        <td>$${parseFloat(item.rate).toFixed(2)}</td>
                        <td>$${parseFloat(item.amount).toFixed(2)}</td>
                        <td>
                            <button class="btn btn-sm btn-secondary" onclick="showAddItemModal(null, ${item.id})" title="Edit">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-sm btn-danger" onclick="deleteInvoiceItem(${item.id})" title="Delete">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                `).join('')}
            </tbody>
        </table>
    `;
}

/**
 * Update totals display in edit modal
 */
function updateEditInvoiceTotals() {
    if (!currentEditingInvoice) return;

    document.getElementById('edit-invoice-subtotal').textContent =
        '$' + parseFloat(currentEditingInvoice.subtotal || 0).toFixed(2);
    document.getElementById('edit-invoice-tax-rate').textContent =
        parseFloat(currentEditingInvoice.tax_rate || 0).toFixed(0);
    document.getElementById('edit-invoice-tax-amount').textContent =
        '$' + parseFloat(currentEditingInvoice.tax_amount || 0).toFixed(2);
    document.getElementById('edit-invoice-total').textContent =
        '$' + parseFloat(currentEditingInvoice.total || 0).toFixed(2);
}

/**
 * Show add/edit item modal
 */
export async function showAddItemModal(invoiceId = null, itemId = null) {
    const editInvoiceId = invoiceId || document.getElementById('edit-invoice-id').value;

    // Attach form submit handler (only once)
    const form = document.getElementById('add-item-form');
    if (form && !form.dataset.listenerAttached) {
        form.addEventListener('submit', saveInvoiceItem);
        form.dataset.listenerAttached = 'true';
    }

    if (itemId) {
        // Editing existing item
        try {
            const item = currentEditingInvoice.items.find(i => i.id === itemId);
            if (item) {
                currentEditingItem = item;
                document.getElementById('add-item-modal-title').textContent = 'Edit Invoice Item';
                document.getElementById('item-id').value = item.id;
                document.getElementById('item-invoice-id').value = editInvoiceId;
                document.getElementById('item-description').value = item.description;
                document.getElementById('item-work-date').value = item.work_date;
                document.getElementById('item-hours').value = parseFloat(item.hours).toFixed(2);
                document.getElementById('item-rate').value = parseFloat(item.rate).toFixed(2);
                document.getElementById('item-amount').value = parseFloat(item.amount).toFixed(2);
            }
        } catch (error) {
            console.error('Failed to load item:', error);
        }
    } else {
        // Adding new item
        currentEditingItem = null;
        document.getElementById('add-item-modal-title').textContent = 'Add Invoice Item';
        document.getElementById('item-id').value = '';
        document.getElementById('item-invoice-id').value = editInvoiceId;
        document.getElementById('add-item-form').reset();

        // Set default work date to today
        const today = new Date().toISOString().split('T')[0];
        document.getElementById('item-work-date').value = today;

        // Set default rate from project if available
        if (currentEditingInvoice && currentEditingInvoice.project) {
            document.getElementById('item-rate').value =
                parseFloat(currentEditingInvoice.project.hourly_rate || 0).toFixed(2);
        }
    }

    // Show modal
    document.getElementById('add-item-modal').classList.add('show');
    document.getElementById('modal-overlay').classList.add('show');
    document.body.style.overflow = 'hidden';
}

/**
 * Hide add item modal
 */
export function hideAddItemModal() {
    document.getElementById('add-item-modal').classList.remove('show');
    document.getElementById('modal-overlay').classList.remove('show');
    document.body.style.overflow = 'auto';
    document.getElementById('add-item-form').reset();
    currentEditingItem = null;
}

/**
 * Calculate item amount
 */
export function calculateItemAmount() {
    const hours = parseFloat(document.getElementById('item-hours').value) || 0;
    const rate = parseFloat(document.getElementById('item-rate').value) || 0;
    const amount = hours * rate;

    document.getElementById('item-amount').value = amount.toFixed(2);
}

/**
 * Save invoice item (add or update)
 */
export async function saveInvoiceItem(e) {
    if (e) e.preventDefault();

    const invoiceId = document.getElementById('item-invoice-id').value;
    const itemId = document.getElementById('item-id').value;

    const data = {
        description: document.getElementById('item-description').value,
        work_date: document.getElementById('item-work-date').value,
        hours: parseFloat(document.getElementById('item-hours').value),
        rate: parseFloat(document.getElementById('item-rate').value)
    };

    // Validate
    if (!data.description) {
        window.notify.error('Description is required');
        return;
    }
    if (!data.work_date) {
        window.notify.error('Work date is required');
        return;
    }
    if (isNaN(data.hours) || data.hours <= 0) {
        window.notify.error('Hours must be greater than 0');
        return;
    }
    if (isNaN(data.rate) || data.rate < 0) {
        window.notify.error('Rate must be 0 or greater');
        return;
    }

    // Handle create mode (temporary storage)
    if (invoiceId === 'create') {
        if (itemId) {
            // Edit existing temp item
            const tempId = parseInt(itemId);
            const item = createInvoiceItems.find(i => i.tempId === tempId);
            if (item) {
                item.description = data.description;
                item.work_date = data.work_date;
                item.hours = data.hours;
                item.rate = data.rate;
                window.notify.success('Item updated');
            }
        } else {
            // Add new temp item
            createInvoiceItems.push({
                tempId: createItemIdCounter++,
                ...data
            });
            window.notify.success('Item added');
        }
        hideAddItemModal();
        displayCreateInvoiceItems();
        updateCreateInvoiceTotals();
        return;
    }

    // Handle edit mode (API calls)
    try {
        if (itemId) {
            // Update existing item (not implemented in controller yet, so treat as add)
            window.notify.error('Editing items is not yet supported. Please delete and re-add.');
            return;
        } else {
            // Add new item
            const response = await window.api.request(`/api/invoices/${invoiceId}/items`, {
                method: 'POST',
                body: JSON.stringify(data)
            });

            if (response) {
                window.notify.success('Item added successfully!');
                hideAddItemModal();

                // Reload the invoice to get updated items and totals
                await loadInvoiceItems(invoiceId);
                updateEditInvoiceTotals();
            }
        }
    } catch (error) {
        window.notify.error('Failed to save item: ' + error.message);
    }
}

/**
 * Delete invoice item
 */
export async function deleteInvoiceItem(itemId) {
    if (!confirm('Are you sure you want to delete this item?')) {
        return;
    }

    const invoiceId = document.getElementById('edit-invoice-id').value;

    try {
        const response = await window.api.request(`/api/invoices/${invoiceId}/items/${itemId}`, {
            method: 'DELETE'
        });

        if (response) {
            window.notify.success('Item deleted successfully!');

            // Reload the invoice to get updated items and totals
            await loadInvoiceItems(invoiceId);
            updateEditInvoiceTotals();
        }
    } catch (error) {
        window.notify.error('Failed to delete item: ' + error.message);
    }
}
