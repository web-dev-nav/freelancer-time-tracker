{{-- Project Modal Component --}}
{{-- Modal for adding or editing projects --}}
<div class="modal" id="project-modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 id="project-modal-title">Add New Project</h3>
            <button class="modal-close" onclick="hideProjectModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <form id="project-form" class="modal-body">
            <input type="hidden" id="project-id">

            <div class="form-group">
                <label class="form-label" for="project-name">
                    <i class="fas fa-briefcase"></i>
                    Project Name *
                </label>
                <input type="text" id="project-name" class="form-control" required
                       placeholder="e.g., Upwork Client - Website Development">
            </div>

            <div class="form-group">
                <label class="form-label" for="client-name">
                    <i class="fas fa-user"></i>
                    Client/Organization Name
                </label>
                <input type="text" id="client-name" class="form-control"
                       placeholder="e.g., ABC Corporation">
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="project-color">
                        <i class="fas fa-palette"></i>
                        Project Color
                    </label>
                    <input type="color" id="project-color" class="form-control" value="#8b5cf6">
                </div>

                <div class="form-group">
                    <label class="form-label" for="hourly-rate">
                        <i class="fas fa-dollar-sign"></i>
                        Hourly Rate (optional)
                    </label>
                    <input type="number" id="hourly-rate" class="form-control" step="0.01" min="0"
                           placeholder="25.00">
                </div>
            </div>

            <div class="form-group">
                <label class="form-label checkbox-label">
                    <input type="checkbox" id="has-tax" class="form-checkbox">
                    <span>
                        <i class="fas fa-receipt"></i>
                        Include 13% Tax
                    </span>
                </label>
            </div>

            <div class="form-group">
                <label class="form-label" for="project-description">
                    <i class="fas fa-align-left"></i>
                    Description/Notes
                </label>
                <textarea id="project-description" class="form-control" rows="3"
                         placeholder="Add any notes about this project..."></textarea>
            </div>
        </form>

        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" onclick="hideProjectModal()">
                Cancel
            </button>
            <button type="submit" form="project-form" class="btn btn-primary">
                <i class="fas fa-save"></i>
                Save Project
            </button>
        </div>
    </div>
</div>
