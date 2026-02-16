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
                    <input type="time" id="edit-clock-in-time" class="form-control" required>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label" for="edit-clock-out-time">
                    <i class="fas fa-clock"></i>
                    Clock Out Time
                </label>
                <input type="time" id="edit-clock-out-time" class="form-control" required>
            </div>

            <div class="form-group">
                <label class="form-label" for="edit-work-description">
                    <i class="fas fa-edit"></i>
                    Work Description
                </label>
                <div class="rich-editor" id="edit-work-description-editor-wrapper">
                    <div class="rich-editor-toolbar" role="toolbar" aria-label="Work description formatting">
                        <button type="button" class="editor-btn" onclick="applyRichTextCommand('bold')" title="Bold (Ctrl+B)">
                            <i class="fas fa-bold"></i>
                        </button>
                        <button type="button" class="editor-btn" onclick="applyRichTextCommand('italic')" title="Italic (Ctrl+I)">
                            <i class="fas fa-italic"></i>
                        </button>
                        <button type="button" class="editor-btn" onclick="applyRichTextCommand('underline')" title="Underline (Ctrl+U)">
                            <i class="fas fa-underline"></i>
                        </button>
                        <span class="editor-separator"></span>
                        <button type="button" class="editor-btn" onclick="applyRichTextCommand('insertUnorderedList')" title="Bullet List">
                            <i class="fas fa-list-ul"></i>
                        </button>
                        <button type="button" class="editor-btn" onclick="applyRichTextCommand('insertOrderedList')" title="Numbered List">
                            <i class="fas fa-list-ol"></i>
                        </button>
                        <span class="editor-separator"></span>
                        <button type="button" class="editor-btn" onclick="applyRichTextCommand('formatBlock', 'blockquote')" title="Quote">
                            <i class="fas fa-quote-right"></i>
                        </button>
                    </div>
                    <div id="edit-work-description-editor"
                         class="rich-editor-content"
                         contenteditable="true"
                         data-placeholder="Describe what you accomplished during this work session..."
                         aria-label="Work Description Editor"></div>
                </div>
                <textarea id="edit-work-description" class="form-control" rows="4" style="display:none;" required></textarea>
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
