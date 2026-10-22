{{-- Organizations Tab Component --}}
{{-- Organization management page with grid layout for project cards --}}
<div class="tab-content" id="projects-tab">
    <div class="projects-page">
        <div class="projects-page-header">
            <div>
                <h2>Manage Organizations</h2>
                <p class="projects-subtitle">Organize your freelance work by organizations</p>
            </div>
            <div class="projects-page-actions">
                <button class="btn btn-sm btn-secondary" onclick="toggleArchivedProjects()">
                    <i class="fas fa-archive"></i>
                    <span id="archive-toggle-text">Show Archived</span>
                </button>
                <button class="btn btn-primary" onclick="showAddProjectModal()">
                    <i class="fas fa-plus"></i>
                    Add New Organization
                </button>
            </div>
        </div>

        <div class="projects-grid" id="projects-grid">
            <!-- Organization cards will be loaded here dynamically by JavaScript -->
        </div>
    </div>
</div>
