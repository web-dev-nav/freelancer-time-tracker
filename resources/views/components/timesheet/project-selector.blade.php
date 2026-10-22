<div class="project-selector-container">
    <div class="project-selector-header">
        <label class="project-selector-label" for="project-selector">
            <i class="fas fa-briefcase"></i>
            Organization
        </label>
        <div class="project-selector-controls">
            <select id="project-selector" class="project-select">
                <option value="">Loading organizations...</option>
            </select>
            @if(auth()->user()?->isAuthor())
                <button class="btn btn-sm btn-primary" onclick="showAddProjectModal()" title="Add new organization" aria-label="Add new organization">
                    <i class="fas fa-plus"></i>
                </button>
                <a href="{{ route('settings.index') }}" class="btn btn-sm btn-secondary" title="Application settings" aria-label="Application settings">
                    <i class="fas fa-cog"></i>
                </a>
            @endif
        </div>
    </div>
</div>
<div id="project-load-error" class="alert alert-warning" role="status" hidden>
    <span>Organizations could not be loaded.</span>
    <button type="button" class="btn btn-secondary btn-sm" onclick="window.reloadOrganizations ? window.reloadOrganizations() : location.reload()">Retry</button>
</div>
