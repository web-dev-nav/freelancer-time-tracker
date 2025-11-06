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
