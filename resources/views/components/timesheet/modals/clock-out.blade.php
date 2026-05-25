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
                <div id="work-description-toolbar" class="quill-toolbar">
                    <span class="ql-formats">
                        <button type="button" class="ql-bold"></button>
                        <button type="button" class="ql-italic"></button>
                        <button type="button" class="ql-underline"></button>
                    </span>
                    <span class="ql-formats">
                        <button type="button" class="ql-list" value="ordered"></button>
                        <button type="button" class="ql-list" value="bullet"></button>
                    </span>
                    <span class="ql-formats">
                        <button type="button" class="ql-align" value=""></button>
                        <button type="button" class="ql-align" value="center"></button>
                        <button type="button" class="ql-align" value="right"></button>
                    </span>
                    <span class="ql-formats">
                        <button type="button" class="ql-clean"></button>
                    </span>
                </div>
                <div id="work-description-editor" class="quill-editor"></div>
                <textarea id="work-description" class="form-control"
                    placeholder="Describe what you accomplished during this work session..."></textarea>
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
