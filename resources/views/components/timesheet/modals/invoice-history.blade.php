{{-- Invoice History Modal Component --}}
{{-- Modal for viewing invoice activity history --}}
<div class="modal" id="invoice-history-modal">
    <div class="modal-content" style="max-width: 700px;">
        <div class="modal-header">
            <h3><i class="fas fa-history"></i> Invoice History</h3>
            <button class="modal-close" onclick="hideInvoiceHistoryModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <div class="modal-body" id="invoice-history-content">
            <div id="invoice-history-loading" style="text-align: center; padding: 40px;">
                <i class="fas fa-spinner fa-spin" style="font-size: 24px; color: var(--primary-color);"></i>
                <p style="margin-top: 10px; color: var(--text-secondary);">Loading history...</p>
            </div>

            <div id="invoice-history-list" style="display: none;">
                <!-- History items will be populated here -->
            </div>

            <div id="invoice-history-empty" style="display: none; text-align: center; padding: 40px; color: var(--text-secondary);">
                <i class="fas fa-history" style="font-size: 48px; opacity: 0.3; margin-bottom: 15px;"></i>
                <p>No history available for this invoice</p>
            </div>
        </div>

        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" onclick="hideInvoiceHistoryModal()">
                Close
            </button>
        </div>
    </div>
</div>
