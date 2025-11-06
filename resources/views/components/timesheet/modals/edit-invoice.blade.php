{{-- Edit Invoice Modal Component --}}
{{-- Modal for editing invoice details and managing line items --}}
<div class="modal" id="edit-invoice-modal">
    <div class="modal-content" style="max-width: 1200px; width: 95%;">
        <div class="modal-header">
            <h3>Edit Invoice <span id="edit-invoice-number"></span></h3>
            <button class="modal-close" onclick="hideEditInvoiceModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <div class="modal-body">
            <input type="hidden" id="edit-invoice-id">

            {{-- Invoice Header Info --}}
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="edit-invoice-date">
                        <i class="fas fa-calendar"></i>
                        Invoice Date *
                    </label>
                    <input type="date" id="edit-invoice-date" class="form-control" required>
                </div>

                <div class="form-group">
                    <label class="form-label" for="edit-due-date">
                        <i class="fas fa-calendar-check"></i>
                        Due Date *
                    </label>
                    <input type="date" id="edit-due-date" class="form-control" required>
                </div>

                <div class="form-group">
                    <label class="form-label" for="edit-invoice-status">
                        <i class="fas fa-info-circle"></i>
                        Status
                    </label>
                    <select id="edit-invoice-status" class="form-control" disabled>
                        <option value="draft">Draft</option>
                        <option value="sent">Sent</option>
                        <option value="paid">Paid</option>
                    </select>
                </div>
            </div>

            {{-- Client Info --}}
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="edit-client-name">
                        <i class="fas fa-user"></i>
                        Client Name *
                    </label>
                    <input type="text" id="edit-client-name" class="form-control" required>
                </div>

                <div class="form-group">
                    <label class="form-label" for="edit-client-email">
                        <i class="fas fa-envelope"></i>
                        Client Email
                    </label>
                    <input type="email" id="edit-client-email" class="form-control">
                </div>
            </div>

            <div class="form-group">
                <label class="form-label" for="edit-client-address">
                    <i class="fas fa-map-marker-alt"></i>
                    Client Address
                </label>
                <textarea id="edit-client-address" class="form-control" rows="2"></textarea>
            </div>

            {{-- Invoice Items Section --}}
            <div class="invoice-items-section">
                <div class="section-header">
                    <h4><i class="fas fa-list"></i> Invoice Items</h4>
                    <button type="button" class="btn btn-sm btn-primary" onclick="showAddItemModal()">
                        <i class="fas fa-plus"></i> Add Item
                    </button>
                </div>

                <div class="invoice-items-table" id="invoice-items-table">
                    <!-- Items will be loaded here -->
                </div>
            </div>

            {{-- Totals Display --}}
            <div class="invoice-totals-display">
                <div class="totals-row">
                    <span>Subtotal:</span>
                    <strong id="edit-invoice-subtotal">$0.00</strong>
                </div>
                <div class="totals-row">
                    <span>Tax (<span id="edit-invoice-tax-rate">0</span>%):</span>
                    <strong id="edit-invoice-tax-amount">$0.00</strong>
                </div>
                <div class="totals-row totals-total">
                    <span>Total:</span>
                    <strong id="edit-invoice-total">$0.00</strong>
                </div>
            </div>

            {{-- Notes --}}
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="edit-invoice-notes">
                        <i class="fas fa-sticky-note"></i>
                        Notes / Terms
                    </label>
                    <textarea id="edit-invoice-notes" class="form-control" rows="3"></textarea>
                </div>

                <div class="form-group">
                    <label class="form-label" for="edit-invoice-description">
                        <i class="fas fa-align-left"></i>
                        Description
                    </label>
                    <textarea id="edit-invoice-description" class="form-control" rows="3"></textarea>
                </div>
            </div>
        </div>

        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" onclick="hideEditInvoiceModal()">
                Cancel
            </button>
            <button type="button" class="btn btn-primary" onclick="saveInvoiceChanges()">
                <i class="fas fa-save"></i>
                Save Changes
            </button>
        </div>
    </div>
</div>
