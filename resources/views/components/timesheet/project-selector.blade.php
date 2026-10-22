<div class="project-selector-container">
    <div class="project-selector-header">
        <label class="project-selector-label" for="project-selector">
            <i class="fas fa-briefcase"></i>
            Project
        </label>
        <div class="project-selector-controls">
            <select id="project-selector" class="project-select">
                <option value="">Loading projects...</option>
            </select>
            @if(auth()->user()?->isAuthor())
                <button class="btn btn-sm btn-primary" onclick="showAddProjectModal()" title="Add new project" aria-label="Add new project">
                    <i class="fas fa-plus"></i>
                </button>
                <a href="{{ route('settings.index') }}" class="btn btn-sm btn-secondary" title="Application settings" aria-label="Application settings">
                    <i class="fas fa-cog"></i>
                </a>
            @endif
        </div>
    </div>
</div>
