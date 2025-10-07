{{-- Clock Out Modal Component --}}
{{-- Modal for clocking out and adding work description --}}
<div class="modal" id="clock-out-modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Clock Out</h3>
            <button class="modal-close" onclick="hideClockOutModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <form id="clock-out-form" class="modal-body">
            <div class="form-group">
                <label class="form-label" for="clock-out-time">
                    <i class="fas fa-clock"></i>
                    End Time
                </label>
                <input type="time" id="clock-out-time" class="form-control" required>
            </div>

            <div class="form-group">
                <label class="form-label" for="work-description">
                    <i class="fas fa-edit"></i>
                    What did you work on?
                </label>
                <textarea id="work-description" class="form-control" rows="4"
                         placeholder="Describe what you accomplished during this work session..." required></textarea>
            </div>
        </form>

        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" onclick="hideClockOutModal()">
                Cancel
            </button>
            <button type="submit" form="clock-out-form" class="btn btn-success">
                <i class="fas fa-check"></i>
                Complete Session
            </button>
        </div>
    </div>
</div>
