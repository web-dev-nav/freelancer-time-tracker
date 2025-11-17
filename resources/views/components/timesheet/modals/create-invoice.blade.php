{{-- Create Invoice Modal Component --}}
{{-- Modal for creating a new invoice from time logs --}}
<div class="modal" id="create-invoice-modal">
    <div class="modal-content" style="max-width: 1100px; width: 95%;">
        <div class="modal-header">
            <h3>Create New Invoice</h3>
            <button class="modal-close" onclick="hideCreateInvoiceModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <form id="create-invoice-form" class="modal-body">
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="invoice-project-id">
                        <i class="fas fa-folder"></i>
                        Project *
                    </label>
                    <select id="invoice-project-id" class="form-control" required onchange="handleCreateInvoiceProjectChange()">
                        <option value="">Select a project...</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label" for="invoice-date">
                        <i class="fas fa-calendar"></i>
                        Invoice Date *
                    </label>
                    <input type="date" id="invoice-date" class="form-control" required>
                </div>

                <div class="form-group">
                    <label class="form-label" for="due-date">
                        <i class="fas fa-calendar-check"></i>
                        Due Date *
                    </label>
                    <input type="date" id="due-date" class="form-control" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="invoice-client-name">
                        <i class="fas fa-user"></i>
                        Client Name *
                    </label>
                    <input type="text" id="invoice-client-name" class="form-control" required
                           placeholder="Client or company name">
                </div>

                <div class="form-group">
                    <label class="form-label" for="invoice-client-email">
                        <i class="fas fa-envelope"></i>
                        Client Email
                    </label>
                    <input type="email" id="invoice-client-email" class="form-control"
                           placeholder="client@example.com">
                </div>
            </div>

            <div class="form-group">
                <label class="form-label" for="invoice-client-address">
                    <i class="fas fa-map-marker-alt"></i>
                    Client Address
                </label>
                <textarea id="invoice-client-address" class="form-control" rows="2"
                          placeholder="Billing address, if needed"></textarea>
            </div>

            {{-- Invoice Items Section --}}
            <div class="invoice-items-section">
                <div class="section-header">
                    <h4><i class="fas fa-list"></i> Invoice Items</h4>
                    <button type="button" class="btn btn-sm btn-primary" onclick="showAddItemModalForCreate()">
                        <i class="fas fa-plus"></i> Add Item
                    </button>
                </div>

                <div class="invoice-items-table" id="create-invoice-items-table">
                    <div class="empty-state-small">
                        <i class="fas fa-info-circle"></i>
                        <p>No items added yet</p>
                        <p style="font-size: 12px; margin-top: 8px;">Click "Add Item" to add line items to this invoice</p>
                    </div>
                </div>
            </div>

            {{-- Totals Display --}}
            <div class="invoice-totals-display">
                <div class="totals-row">
                    <span>Subtotal:</span>
                    <strong id="create-invoice-subtotal">$0.00</strong>
                </div>
                <div class="totals-row">
                    <span>Tax (<span id="create-invoice-tax-rate">0</span>%):</span>
                    <strong id="create-invoice-tax-amount">$0.00</strong>
                </div>
                <div class="totals-row" id="stripe-fee-row" style="border-top: 1px solid #e5e7eb; padding-top: 8px; margin-top: 8px;">
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <label style="display: flex; align-items: center; gap: 6px; margin: 0; cursor: pointer;">
                            <input type="checkbox" id="include-stripe-fees" onchange="calculateCreateInvoiceTotals()" style="cursor: pointer;">
                            <span style="font-size: 13px; color: #374151;">
                                <i class="fab fa-stripe" style="color: #635BFF;"></i>
                                Charge clients a Stripe processing fee (2.9% + $0.30) when they pay via Stripe
                            </span>
                        </label>
                        <span style="font-size: 11px; color: #9ca3af;" title="Based on Stripe's standard pricing for Canadian transactions">
                            <i class="fas fa-info-circle"></i>
                        </span>
                    </div>
                    <strong id="create-invoice-stripe-fee">+$0.00</strong>
                </div>
                <div class="totals-row totals-total">
                    <span>Total:</span>
                    <strong id="create-invoice-total">$0.00</strong>
                </div>
                <div class="totals-row" id="stripe-payment-total-row" style="display: none; background: #eef2ff; border-radius: 6px; padding: 8px 12px; margin-top: 8px;">
                    <span style="font-size: 13px; color: #312e81;">Stripe payment total:</span>
                    <strong id="create-invoice-stripe-total" style="color: #312e81;">$0.00</strong>
                </div>
                <p id="stripe-payment-note" style="display: none; font-size: 12px; color: #6b7280; margin: 6px 0 0;">
                    A Stripe processing fee (2.9% + $0.30 CAD) is only added when the client pays using the "Pay with Stripe" button.
                </p>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="invoice-notes">
                        <i class="fas fa-sticky-note"></i>
                        Notes / Terms (optional)
                    </label>
                    <textarea id="invoice-notes" class="form-control" rows="3"
                             placeholder="Payment terms, additional notes..."></textarea>
                </div>

                <div class="form-group">
                    <label class="form-label" for="invoice-description">
                        <i class="fas fa-align-left"></i>
                        Description (optional)
                    </label>
                    <textarea id="invoice-description" class="form-control" rows="3"
                             placeholder="Brief description of work..."></textarea>
                </div>
            </div>
        </form>

        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" onclick="hideCreateInvoiceModal()">
                Cancel
            </button>
            <button type="submit" form="create-invoice-form" class="btn btn-primary">
                <i class="fas fa-plus"></i>
                Create Invoice
            </button>
        </div>
    </div>
</div>
