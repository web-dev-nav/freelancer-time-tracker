{{-- Add/Edit Invoice Item Modal Component --}}
{{-- Modal for adding or editing invoice line items --}}
<div class="modal" id="add-item-modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 id="add-item-modal-title">Add Invoice Item</h3>
            <button class="modal-close" onclick="hideAddItemModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <form id="add-item-form" class="modal-body">
            <input type="hidden" id="item-id">
            <input type="hidden" id="item-invoice-id">

            <div class="form-group">
                <label class="form-label" for="item-description">
                    <i class="fas fa-align-left"></i>
                    Description *
                </label>
                <textarea id="item-description" class="form-control" rows="3" required
                         placeholder="e.g., Website Development - Homepage Design"></textarea>
            </div>

            <div class="form-group">
                <label class="form-label" for="item-work-date">
                    <i class="fas fa-calendar"></i>
                    Work Date *
                </label>
                <input type="date" id="item-work-date" class="form-control" required>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="item-hours">
                        <i class="fas fa-clock"></i>
                        Hours/Quantity *
                    </label>
                    <input type="number" id="item-hours" class="form-control" step="0.01" min="0" required
                           placeholder="8.50" onchange="calculateItemAmount()">
                </div>

                <div class="form-group">
                    <label class="form-label" for="item-rate">
                        <i class="fas fa-dollar-sign"></i>
                        Rate/Unit Price *
                    </label>
                    <input type="number" id="item-rate" class="form-control" step="0.01" min="0" required
                           placeholder="50.00" onchange="calculateItemAmount()">
                </div>

                <div class="form-group">
                    <label class="form-label" for="item-amount">
                        <i class="fas fa-calculator"></i>
                        Amount
                    </label>
                    <input type="number" id="item-amount" class="form-control" step="0.01" min="0" readonly
                           style="background-color: var(--darker-color);">
                </div>
            </div>

            <div class="alert alert-warning" style="margin: 0;">
                <i class="fas fa-info-circle"></i>
                <strong>Note:</strong> Amount is automatically calculated (Hours × Rate)
            </div>
        </form>

        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" onclick="hideAddItemModal()">
                Cancel
            </button>
            <button type="submit" form="add-item-form" class="btn btn-primary">
                <i class="fas fa-save"></i>
                Save Item
            </button>
        </div>
    </div>
</div>
