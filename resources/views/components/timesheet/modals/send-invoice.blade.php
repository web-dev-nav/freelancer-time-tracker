{{-- Send Invoice Modal Component --}}
{{-- Modal for sending invoice via email --}}
<div class="modal" id="send-invoice-modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Send Invoice</h3>
            <button class="modal-close" onclick="hideSendInvoiceModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <form id="send-invoice-form" class="modal-body">
            <input type="hidden" id="send-invoice-id">

            <div class="form-group">
                <label class="form-label" for="send-invoice-email">
                    <i class="fas fa-envelope"></i>
                    Recipient Email *
                </label>
                <input type="email" id="send-invoice-email" class="form-control" required
                       placeholder="client@example.com">
                <small style="color: var(--text-secondary); display: block; margin-top: 4px;">
                    Invoice PDF will be attached to this email
                </small>
            </div>

            <div class="form-group">
                <label class="form-label" for="send-invoice-subject">
                    <i class="fas fa-heading"></i>
                    Email Subject
                </label>
                <input type="text" id="send-invoice-subject" class="form-control"
                       placeholder="Invoice #INV-2025-01-0001 from Freelancer Time Tracker">
            </div>

            <div class="form-group">
                <label class="form-label" for="send-invoice-message">
                    <i class="fas fa-comment"></i>
                    Email Message
                </label>
                <textarea id="send-invoice-message" class="form-control" rows="8"
                         placeholder="Loading..."></textarea>
                <small style="color: var(--text-secondary); display: block; margin-top: 4px;">
                    <i class="fas fa-edit"></i> The message is auto-generated based on current month and payment instructions from Settings. You can edit it before sending.
                </small>
            </div>

            <div class="form-group">
                <label class="checkbox-label">
                    <input type="checkbox" id="schedule-send-checkbox" onchange="toggleScheduleSend()">
                    <span><i class="fas fa-clock"></i> Schedule Send Later</span>
                </label>
            </div>

            <div id="schedule-send-group" class="form-group" style="display: none;">
                <label class="form-label" for="scheduled-send-at">
                    <i class="fas fa-calendar-alt"></i>
                    Schedule Date & Time
                </label>
                <input type="datetime-local" id="scheduled-send-at" class="form-control">
                <small style="color: var(--text-secondary); display: block; margin-top: 4px;">
                    <i class="fas fa-info-circle"></i> Cron runs every 5 minutes. Email will be sent at the next 5-minute interval.
                </small>

                <!-- Trigger Time Preview -->
                <div id="trigger-time-preview" style="display: none; margin-top: 10px;"></div>
            </div>

            <div class="alert alert-warning" style="margin: 0;">
                <i class="fas fa-info-circle"></i>
                <strong>Note:</strong> The invoice will be automatically marked as "Sent" after sending the email.
            </div>

            <!-- Success/Error Message Alert -->
            <div id="send-invoice-message-alert" style="display: none; margin: 20px 0 0 0; padding: 15px 20px; border-radius: 8px; font-size: 14px; font-weight: 500; align-items: center; gap: 12px;">
                <i id="send-invoice-message-icon" class="fas" style="font-size: 18px;"></i>
                <span id="send-invoice-message-text" style="flex: 1;"></span>
            </div>
        </form>

        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" onclick="hideSendInvoiceModal()">
                Cancel
            </button>
            <button type="submit" form="send-invoice-form" class="btn btn-success">
                <i class="fas fa-paper-plane"></i>
                Send Invoice
            </button>
        </div>
    </div>
</div>
