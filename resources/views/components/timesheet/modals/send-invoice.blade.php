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
                <label class="form-label">
                    <i class="fas fa-clock"></i>
                    Delivery Options
                </label>
                <div style="display:flex; gap:12px; flex-wrap:wrap;">
                    <label class="checkbox-label" style="margin:0;">
                        <input type="radio" name="invoice-schedule-mode" value="now" checked>
                        <span>Send Now</span>
                    </label>
                    <label class="checkbox-label" style="margin:0;">
                        <input type="radio" name="invoice-schedule-mode" value="specific">
                        <span>Schedule Specific Date</span>
                    </label>
                </div>
                <small style="color: var(--text-secondary); display: block; margin-top: 6px;">
                    <i class="fas fa-info-circle"></i> Cron runs every 5 minutes. Emails send on the next interval.
                </small>
            </div>

            <div id="invoice-schedule-specific" class="form-group" style="display: none;">
                <label class="form-label">
                    <i class="fas fa-calendar-alt"></i>
                    Schedule Date & Time
                </label>
                <div style="display:grid; grid-template-columns: 1fr 1fr; gap:10px;">
                    <div>
                        <label class="form-label" for="invoice-schedule-date" style="font-size:12px;">
                            Date
                        </label>
                        <input type="date" id="invoice-schedule-date" class="form-control">
                    </div>
                    <div>
                        <label class="form-label" for="invoice-schedule-time" style="font-size:12px;">
                            Time
                        </label>
                        <input type="time" id="invoice-schedule-time" class="form-control" value="09:00">
                    </div>
                </div>
            </div>

            <div id="invoice-schedule-due" class="form-group">
                <label class="form-label">
                    <i class="fas fa-bell"></i>
                    Due Date Reminder (Optional)
                </label>
                <div style="display:grid; grid-template-columns: 1fr 1fr; gap:10px;">
                    <div>
                        <label class="form-label" for="invoice-reminder-date" style="font-size:12px;">
                            Date
                        </label>
                        <input type="date" id="invoice-reminder-date" class="form-control">
                    </div>
                    <div>
                        <label class="form-label" for="invoice-reminder-time" style="font-size:12px;">
                            Time
                        </label>
                        <input type="time" id="invoice-reminder-time" class="form-control" value="09:00">
                    </div>
                </div>
                <small style="color: var(--text-secondary); display: block; margin-top: 6px;">
                    Leave blank to skip the reminder.
                </small>
            </div>

            <input type="hidden" id="invoice-due-date">

            <div id="invoice-schedule-preview" class="alert alert-info" style="display: none; margin: 10px 0 0 0; padding: 12px 14px; border-radius: 8px; font-size: 13px;">
                <i class="fas fa-paper-plane"></i>
                <span id="invoice-schedule-preview-text"></span>
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
