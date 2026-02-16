<div class="project-selector-container">
    <div class="project-selector-header">
        <div class="project-selector-label">
            <i class="fas fa-briefcase"></i>
            Current Project:
        </div>
        <div class="project-selector-controls">
            <select id="project-selector" class="project-select">
                <option value="">Loading projects...</option>
            </select>
            @if(auth()->user()?->isAuthor())
                <button class="btn btn-sm btn-primary" onclick="showAddProjectModal()" title="Add New Project">
                    <i class="fas fa-plus"></i>
                </button>
                <a href="{{ route('settings.index') }}" target="_blank" class="btn btn-sm btn-secondary" title="Application Settings">
                    <i class="fas fa-cog"></i>
                </a>
            @endif
        </div>
    </div>
</div>
