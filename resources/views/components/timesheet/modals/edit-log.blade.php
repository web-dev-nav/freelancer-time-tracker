{{-- Edit Log Modal Component --}}
{{-- Modal for editing existing time log entries --}}
<div class="modal" id="edit-log-modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Edit Time Log</h3>
            <button class="modal-close" onclick="hideEditLogModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <form id="edit-log-form" class="modal-body">
            <input type="hidden" id="edit-log-id">

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="edit-clock-in-date">
                        <i class="fas fa-calendar"></i>
                        Date
                    </label>
                    <input type="date" id="edit-clock-in-date" class="form-control" required>
                </div>

                <div class="form-group">
                    <label class="form-label" for="edit-clock-in-time">
                        <i class="fas fa-clock"></i>
                        Clock In Time
                    </label>
                    <input type="text" id="edit-clock-in-time" class="form-control" placeholder="HH:MM" pattern="^([01]\d|2[0-3]):[0-5]\d$" inputmode="numeric" autocomplete="off" required>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label" for="edit-clock-out-time">
                    <i class="fas fa-clock"></i>
                    Clock Out Time
                </label>
                <input type="text" id="edit-clock-out-time" class="form-control" placeholder="HH:MM" pattern="^([01]\d|2[0-3]):[0-5]\d$" inputmode="numeric" autocomplete="off" required>
            </div>

            <div class="form-group">
                <label class="form-label" for="edit-work-description">
                    <i class="fas fa-edit"></i>
                    Work Description
                </label>
                <textarea id="edit-work-description" class="form-control" rows="6"
                         placeholder="Describe what you accomplished during this work session..." required></textarea>
            </div>
        </form>

        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" onclick="hideEditLogModal()">
                Cancel
            </button>
            <button type="submit" form="edit-log-form" class="btn btn-primary">
                <i class="fas fa-save"></i>
                Save Changes
            </button>
        </div>
    </div>
</div>
