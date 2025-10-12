/**
 * Projects Module
 *
 * Handles project management including loading, creating, editing, archiving,
 * and deleting projects. Also manages the project selector dropdown.
 */

import * as State from './state.js';
import { loadDashboardStats } from './dashboard.js';
import { loadHistory } from './history.js';

/**
 * Load active projects for the selector dropdown
 */
export async function loadProjectsForSelector() {
    try {
        const response = await window.api.request('/api/projects/active');

        if (response.success) {
            State.setAllProjects(response.data);
            updateProjectSelector();

            // Restore last selected project or select first one
            const savedProjectId = localStorage.getItem('selectedProjectId');
            if (savedProjectId && State.allProjects.find(p => p.id == savedProjectId)) {
                State.setSelectedProjectId(savedProjectId);
            } else if (State.allProjects.length > 0) {
                State.setSelectedProjectId(State.allProjects[0].id.toString());
            }

            document.getElementById('project-selector').value = State.selectedProjectId || '';

            // Load initial data for selected project
            if (State.selectedProjectId) {
                onProjectChange();
            }
        }
    } catch (error) {
        console.error('Failed to load projects:', error);
        window.notify.error('Failed to load projects. Please refresh the page.');
    }
}

/**
 * Update the project selector dropdown with current projects
 */
export function updateProjectSelector() {
    const selector = document.getElementById('project-selector');

    if (State.allProjects.length === 0) {
        selector.innerHTML = '<option value="">No projects - Create one!</option>';
        return;
    }

    selector.innerHTML = State.allProjects.map(project => `
        <option value="${project.id}">${project.name}${project.client_name ? ' (' + project.client_name + ')' : ''}</option>
    `).join('');
}

/**
 * Called when project selection changes
 * Reloads all data for the newly selected project
 */
export function onProjectChange() {
    // Reload current tab data
    const activeTab = document.querySelector('.nav-tab.active');
    if (activeTab) {
        const tabName = activeTab.getAttribute('data-tab');
        if (tabName === 'dashboard') {
            // Load stats which includes active session check
            loadDashboardStats();
        } else if (tabName === 'history') {
            State.setCurrentPage(1);
            loadHistory();
        }
    }
}

/**
 * Load projects for the projects tab
 * Loads either active or archived projects based on toggle
 */
export async function loadProjects() {
    try {
        const status = State.showArchived ? 'archived' : 'active';
        const response = await window.api.request(`/api/projects?status=${status}`);

        if (response.success) {
            State.setCurrentProjects(response.data);
            displayProjects();
        }
    } catch (error) {
        window.notify.error('Failed to load projects: ' + error.message);
    }
}

/**
 * Display projects in the projects tab grid
 */
export function displayProjects() {
    const grid = document.getElementById('projects-grid');

    if (State.currentProjects.length === 0) {
        grid.innerHTML = `
            <div class="empty-projects">
                <i class="fas fa-folder-open"></i>
                <h3>No ${State.showArchived ? 'Archived ' : ''}Projects</h3>
                <p>${State.showArchived ? 'You have no archived projects.' : 'Get started by creating your first project!'}</p>
            </div>
        `;
        return;
    }

    grid.innerHTML = State.currentProjects.map(project => `
        <div class="project-card" style="border-left-color: ${project.color};">
            <div class="project-card-header">
                <div class="project-info">
                    <h3>${project.name}</h3>
                    ${project.client_name ? `
                        <div class="project-client">
                            <i class="fas fa-building"></i>
                            ${project.client_name}
                        </div>
                    ` : ''}
                </div>
                <div class="project-actions">
                    ${project.status === 'active' ? `
                        <button class="btn btn-sm btn-secondary" onclick="event.stopPropagation(); editProject(${project.id})" title="Edit">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-sm btn-warning" onclick="event.stopPropagation(); archiveProject(${project.id})" title="Archive">
                            <i class="fas fa-archive"></i>
                        </button>
                    ` : `
                        <button class="btn btn-sm btn-success" onclick="event.stopPropagation(); activateProject(${project.id})" title="Activate">
                            <i class="fas fa-check"></i>
                        </button>
                    `}
                    ${!project.has_time_logs ? `
                        <button class="btn btn-sm btn-danger" onclick="event.stopPropagation(); deleteProject(${project.id})" title="Delete">
                            <i class="fas fa-trash"></i>
                        </button>
                    ` : ''}
                </div>
            </div>

            ${project.description ? `
                <div class="project-description">${project.description}</div>
            ` : ''}

            ${project.status === 'archived' ? `
                <div style="margin-top: 12px;">
                    <span class="project-badge archived">
                        <i class="fas fa-archive"></i>
                        Archived
                    </span>
                </div>
            ` : ''}

            <div class="project-stats">
                <div class="project-stat">
                    <div class="project-stat-value">${window.utils.formatTime(Math.round((project.total_hours || 0) * 60))}</div>
                    <div class="project-stat-label">Total Hours</div>
                </div>
                <div class="project-stat">
                    <div class="project-stat-value">${project.total_sessions || 0}</div>
                    <div class="project-stat-label">Sessions</div>
                </div>
                ${project.hourly_rate ? `
                    <div class="project-stat">
                        <div class="project-stat-value">$${parseFloat(project.hourly_rate).toFixed(2)}/hr</div>
                        <div class="project-stat-label">Rate</div>
                    </div>
                    <div class="project-stat">
                        <div class="project-stat-value">$${((project.total_hours || 0) * project.hourly_rate).toFixed(2)}</div>
                        <div class="project-stat-label">Earnings</div>
                    </div>
                ` : ''}
            </div>
        </div>
    `).join('');
}

/**
 * Show the add project modal
 */
export function showAddProjectModal() {
    document.getElementById('project-modal-title').textContent = 'Add New Project';
    document.getElementById('project-id').value = '';
    document.getElementById('project-form').reset();
    document.getElementById('project-color').value = '#8b5cf6';
    document.getElementById('project-modal').classList.add('show');
    document.getElementById('modal-overlay').classList.add('show');
    document.body.style.overflow = 'hidden';
}

/**
 * Edit a project
 * @param {number} projectId - Project ID
 */
export async function editProject(projectId) {
    try {
        const response = await window.api.request(`/api/projects/${projectId}`);

        if (response.success) {
            const project = response.data;
            document.getElementById('project-modal-title').textContent = 'Edit Project';
            document.getElementById('project-id').value = project.id;
            document.getElementById('project-name').value = project.name;
            document.getElementById('client-name').value = project.client_name || '';
            document.getElementById('project-color').value = project.color;
            document.getElementById('hourly-rate').value = project.hourly_rate || '';
            document.getElementById('has-tax').checked = project.has_tax || false;
            document.getElementById('project-description').value = project.description || '';
            document.getElementById('project-modal').classList.add('show');
            document.getElementById('modal-overlay').classList.add('show');
            document.body.style.overflow = 'hidden';
        }
    } catch (error) {
        window.notify.error('Failed to load project: ' + error.message);
    }
}

/**
 * Hide the project modal
 */
export function hideProjectModal() {
    document.getElementById('project-modal').classList.remove('show');
    document.getElementById('modal-overlay').classList.remove('show');
    document.body.style.overflow = 'auto';
}

/**
 * Save a project (create or update)
 * @param {Event} e - Form submit event
 */
export async function saveProject(e) {
    e.preventDefault();

    const projectId = document.getElementById('project-id').value;
    const data = {
        name: document.getElementById('project-name').value,
        client_name: document.getElementById('client-name').value || null,
        color: document.getElementById('project-color').value,
        hourly_rate: document.getElementById('hourly-rate').value || null,
        has_tax: document.getElementById('has-tax').checked,
        description: document.getElementById('project-description').value || null
    };

    try {
        const url = projectId ? `/api/projects/${projectId}` : '/api/projects';
        const method = projectId ? 'PUT' : 'POST';

        const response = await window.api.request(url, {
            method,
            body: JSON.stringify(data)
        });

        if (response.success) {
            window.notify.success(projectId ? 'Project updated successfully!' : 'Project created successfully!');
            hideProjectModal();
            loadProjectsForSelector();  // Refresh the selector

            // If this was a new project and we had no projects before, select it
            if (!projectId && !State.selectedProjectId) {
                State.setSelectedProjectId(response.data.id.toString());
                localStorage.setItem('selectedProjectId', State.selectedProjectId);
            }

            // If we're on the projects tab, reload it
            const activeTab = document.querySelector('.nav-tab.active');
            if (activeTab && activeTab.getAttribute('data-tab') === 'projects') {
                loadProjects();
            }
        }
    } catch (error) {
        window.notify.error('Failed to save project: ' + error.message);
    }
}

/**
 * Archive a project
 * @param {number} projectId - Project ID
 */
export async function archiveProject(projectId) {
    if (!confirm('Are you sure you want to archive this project? You can always activate it later.')) {
        return;
    }

    try {
        const response = await window.api.request(`/api/projects/${projectId}/archive`, {
            method: 'POST'
        });

        if (response.success) {
            window.notify.success('Project archived successfully!');
            loadProjectsForSelector();
            const activeTab = document.querySelector('.nav-tab.active');
            if (activeTab && activeTab.getAttribute('data-tab') === 'projects') {
                loadProjects();
            }
        }
    } catch (error) {
        window.notify.error('Failed to archive project: ' + error.message);
    }
}

/**
 * Activate an archived project
 * @param {number} projectId - Project ID
 */
export async function activateProject(projectId) {
    try {
        const response = await window.api.request(`/api/projects/${projectId}/activate`, {
            method: 'POST'
        });

        if (response.success) {
            window.notify.success('Project activated successfully!');
            loadProjectsForSelector();
            const activeTab = document.querySelector('.nav-tab.active');
            if (activeTab && activeTab.getAttribute('data-tab') === 'projects') {
                loadProjects();
            }
        }
    } catch (error) {
        window.notify.error('Failed to activate project: ' + error.message);
    }
}

/**
 * Delete a project
 * @param {number} projectId - Project ID
 */
export async function deleteProject(projectId) {
    if (!confirm('Are you sure you want to delete this project? This action cannot be undone. (Projects with time logs cannot be deleted)')) {
        return;
    }

    try {
        const response = await window.api.request(`/api/projects/${projectId}`, {
            method: 'DELETE'
        });

        if (response.success) {
            window.notify.success('Project deleted successfully!');
            loadProjectsForSelector();
            const activeTab = document.querySelector('.nav-tab.active');
            if (activeTab && activeTab.getAttribute('data-tab') === 'projects') {
                loadProjects();
            }
        }
    } catch (error) {
        window.notify.error(error.message || 'Failed to delete project');
    }
}

/**
 * Toggle between showing active and archived projects
 */
export function toggleArchivedProjects() {
    State.setShowArchived(!State.showArchived);
    document.getElementById('archive-toggle-text').textContent = State.showArchived ? 'Show Active' : 'Show Archived';
    loadProjects();
}

/**
 * Backup a project's database (project + time logs)
 * @param {number} projectId - Project ID
 */
export async function backupProject(projectId) {
    try {
        // Create a download link and trigger it
        const link = document.createElement('a');
        link.href = `/api/projects/${projectId}/backup`;
        link.download = `project-${projectId}-backup-${new Date().toISOString().split('T')[0]}.sql`;
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);

        window.notify.success('Project backup downloaded successfully!');
    } catch (error) {
        window.notify.error('Failed to backup project: ' + error.message);
    }
}

/**
 * Backup entire database (all tables and data)
 */
export async function backupDatabase() {
    try {
        // Create a download link and trigger it
        const link = document.createElement('a');
        link.href = `/api/database/backup`;
        link.download = `database-full-${new Date().toISOString().split('T')[0]}.sql`;
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);

        window.notify.success('Full database backup downloaded successfully!');
    } catch (error) {
        window.notify.error('Failed to backup database: ' + error.message);
    }
}
