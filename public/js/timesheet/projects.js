/**
 * Projects Module
 *
 * Handles project management including loading, creating, editing, archiving,
 * and deleting projects. Also manages the project selector dropdown.
 */

import * as State from './state.js';
import { loadDashboardStats } from './dashboard.js';
import { loadHistory } from './history.js';
import { loadInvoices } from './invoices.js';

let clientEmailLookupTimer = null;
let clientEmailLookupRequest = 0;
let clientProfiles = [];
let clientProfilesLoaded = false;
const collapsedOrganizationGroups = new Set();

function setClientPasswordVisibility(visible) {
    const passwordGroup = document.getElementById('client-password-group');
    const passwordInput = document.getElementById('client-login-password');

    if (!passwordGroup || !passwordInput) {
        return;
    }

    passwordGroup.style.display = visible ? '' : 'none';
    if (!visible) {
        passwordInput.value = '';
    }
}

function setClientAccountStatus(message = '', tone = 'muted') {
    const statusEl = document.getElementById('client-account-status');
    if (!statusEl) {
        return;
    }

    statusEl.textContent = message;
    statusEl.classList.remove('text-muted', 'text-success', 'text-warning', 'text-danger');
    statusEl.classList.add(tone === 'success' ? 'text-success' : tone === 'warning' ? 'text-warning' : tone === 'danger' ? 'text-danger' : 'text-muted');
}

export async function checkClientAccountByEmail() {
    const emailInput = document.getElementById('client-email');
    if (!emailInput) {
        return;
    }

    const email = emailInput.value.trim();
    if (!email) {
        setClientAccountStatus('');
        setClientPasswordVisibility(true);
        return;
    }

    const requestId = ++clientEmailLookupRequest;

    try {
        const response = await window.api.request(`/api/projects/client-account?email=${encodeURIComponent(email)}`);

        if (requestId !== clientEmailLookupRequest) {
            return;
        }

        const exists = !!response.exists;

        if (exists) {
            const clientName = response.data?.name || 'Client';
            setClientAccountStatus(`${clientName} already exists. Password is not needed.`, 'success');
            setClientPasswordVisibility(false);
        } else {
            setClientAccountStatus('New client account will be created. Set a password.', 'warning');
            setClientPasswordVisibility(true);
        }
    } catch (error) {
        if (requestId !== clientEmailLookupRequest) {
            return;
        }
        setClientAccountStatus('Unable to verify client account right now.', 'danger');
        setClientPasswordVisibility(true);
    }
}

export function setupClientEmailWatcher() {
    const emailInput = document.getElementById('client-email');
    if (!emailInput || emailInput.dataset.lookupBound === '1') {
        return;
    }

    emailInput.dataset.lookupBound = '1';
    emailInput.addEventListener('input', () => {
        if (clientEmailLookupTimer) {
            clearTimeout(clientEmailLookupTimer);
        }
        clientEmailLookupTimer = setTimeout(() => {
            checkClientAccountByEmail();
        }, 300);
    });

    emailInput.addEventListener('blur', () => {
        checkClientAccountByEmail();
    });
}

function normalizeClientName(name) {
    return (name || '').trim().toLowerCase();
}

function escapeHtml(value = '') {
    return String(value)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#39;');
}

function findClientProfileByName(name) {
    const normalized = normalizeClientName(name);
    if (!normalized) {
        return null;
    }

    return clientProfiles.find(profile => normalizeClientName(profile.client_name) === normalized) || null;
}

function prefillFromClientProfile(profile) {
    if (!profile) {
        return;
    }

    const emailInput = document.getElementById('client-email');
    const addressInput = document.getElementById('client-address');
    const accessDashboard = document.getElementById('client-can-access-dashboard');
    const accessHistory = document.getElementById('client-can-access-history');
    const accessReports = document.getElementById('client-can-access-reports');
    const accessInvoices = document.getElementById('client-can-access-invoices');

    if (emailInput) {
        emailInput.value = profile.client_email || '';
    }
    if (addressInput) {
        addressInput.value = profile.client_address || '';
    }
    if (accessDashboard) {
        accessDashboard.checked = profile.client_can_access_dashboard !== false;
    }
    if (accessHistory) {
        accessHistory.checked = profile.client_can_access_history !== false;
    }
    if (accessReports) {
        accessReports.checked = profile.client_can_access_reports !== false;
    }
    if (accessInvoices) {
        accessInvoices.checked = profile.client_can_access_invoices !== false;
    }
}

async function loadClientProfiles() {
    if (clientProfilesLoaded) {
        return;
    }

    try {
        const response = await window.api.request('/api/projects/client-profiles');
        clientProfiles = response?.data || [];
        clientProfilesLoaded = true;
    } catch (error) {
        console.error('Failed to load client profiles:', error);
    }
}

function renderClientNameDropdown(search = '') {
    const dropdown = document.getElementById('client-name-dropdown');
    if (!dropdown) {
        return;
    }

    const query = normalizeClientName(search);
    const matches = clientProfiles.filter(profile => {
        if (!profile?.client_name) {
            return false;
        }
        if (!query) {
            return true;
        }
        return normalizeClientName(profile.client_name).includes(query);
    });

    if (matches.length === 0) {
        dropdown.innerHTML = '<div class="client-name-empty">No existing client found</div>';
        return;
    }

    dropdown.innerHTML = matches.map(profile => `
        <button type="button" class="client-name-option" data-client-name="${encodeURIComponent(profile.client_name)}">
            ${escapeHtml(profile.client_name)}${profile.client_email ? ` (${escapeHtml(profile.client_email)})` : ''}
        </button>
    `).join('');
}

function setClientNameDropdownVisible(visible) {
    const dropdown = document.getElementById('client-name-dropdown');
    if (!dropdown) {
        return;
    }
    dropdown.style.display = visible ? 'block' : 'none';
}

function setupClientNameWatcher() {
    const nameInput = document.getElementById('client-name');
    const dropdown = document.getElementById('client-name-dropdown');
    const toggle = document.getElementById('client-name-toggle');

    if (!nameInput || !dropdown || !toggle || nameInput.dataset.lookupBound === '1') {
        return;
    }

    nameInput.dataset.lookupBound = '1';

    const applyIfKnown = async (name = null) => {
        await loadClientProfiles();
        const profile = findClientProfileByName(name ?? nameInput.value);
        if (!profile) {
            return;
        }
        nameInput.value = profile.client_name || nameInput.value;
        prefillFromClientProfile(profile);
        await checkClientAccountByEmail();
    };

    nameInput.addEventListener('input', async () => {
        await loadClientProfiles();
        renderClientNameDropdown(nameInput.value);
        setClientNameDropdownVisible(true);
    });

    nameInput.addEventListener('focus', async () => {
        await loadClientProfiles();
        renderClientNameDropdown(nameInput.value);
        setClientNameDropdownVisible(true);
    });

    nameInput.addEventListener('blur', async () => {
        setTimeout(async () => {
            setClientNameDropdownVisible(false);
            await applyIfKnown();
        }, 220);
    });

    toggle.addEventListener('click', async () => {
        await loadClientProfiles();
        const isOpen = dropdown.style.display === 'block';
        if (isOpen) {
            setClientNameDropdownVisible(false);
            return;
        }
        renderClientNameDropdown(nameInput.value);
        setClientNameDropdownVisible(true);
        nameInput.focus();
    });

    dropdown.addEventListener('mousedown', async (event) => {
        const option = event.target.closest('.client-name-option');
        if (!option) {
            return;
        }
        event.preventDefault();
        const selectedName = decodeURIComponent(option.getAttribute('data-client-name') || '');
        nameInput.value = selectedName;
        await applyIfKnown(selectedName);
        setClientNameDropdownVisible(false);
    });

    document.addEventListener('click', (event) => {
        const combobox = event.target.closest('.client-name-combobox');
        if (!combobox) {
            setClientNameDropdownVisible(false);
        }
    });
}

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
            applyClientTabPermissions();

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
        const message = window.currentUser?.role === 'author'
            ? 'No projects - Create one!'
            : 'No assigned projects';
        selector.innerHTML = `<option value="">${message}</option>`;
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
    applyClientTabPermissions();

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
        } else if (tabName === 'invoices') {
            loadInvoices();
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

    const groupedProjects = State.currentProjects.reduce((groups, project) => {
        const orgName = (project.client_name || '').trim() || 'Unassigned Organization';
        if (!groups[orgName]) {
            groups[orgName] = [];
        }
        groups[orgName].push(project);
        return groups;
    }, {});

    const sortedOrganizations = Object.keys(groupedProjects).sort((a, b) => a.localeCompare(b));

    grid.innerHTML = sortedOrganizations.map(orgName => {
        const orgProjects = groupedProjects[orgName];
        const orgKey = encodeURIComponent(orgName.toLowerCase());
        const isCollapsed = collapsedOrganizationGroups.has(orgKey);
        const totalOrgHours = orgProjects.reduce((sum, p) => sum + Number(p.total_hours || 0), 0);
        const totalOrgSessions = orgProjects.reduce((sum, p) => sum + Number(p.total_sessions || 0), 0);

        return `
        <section class="organization-group ${isCollapsed ? 'collapsed' : ''}" data-org-key="${orgKey}">
            <button type="button" class="organization-header" data-org-key="${orgKey}">
                <div class="organization-title-wrap">
                    <i class="fas fa-building"></i>
                    <span class="organization-title">${orgName}</span>
                </div>
                <div class="organization-meta">
                    <span class="organization-badge">${orgProjects.length} project${orgProjects.length === 1 ? '' : 's'}</span>
                    <span class="organization-badge">${window.utils.formatTime(Math.round(totalOrgHours * 60))}</span>
                    <span class="organization-badge">${totalOrgSessions} sessions</span>
                    <i class="fas fa-chevron-down organization-chevron"></i>
                </div>
            </button>
            <div class="organization-projects">
                <div class="organization-projects-grid">
                    ${orgProjects.map(project => `
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
                    `).join('')}
                </div>
            </div>
        </section>
    `;
    }).join('');

    grid.querySelectorAll('.organization-header').forEach(button => {
        button.addEventListener('click', () => {
            const orgKey = button.getAttribute('data-org-key');
            toggleOrganizationGroup(orgKey);
        });
    });
}

function toggleOrganizationGroup(orgKey) {
    if (!orgKey) {
        return;
    }

    if (collapsedOrganizationGroups.has(orgKey)) {
        collapsedOrganizationGroups.delete(orgKey);
    } else {
        collapsedOrganizationGroups.add(orgKey);
    }

    displayProjects();
}

/**
 * Show the add project modal
 */
export function showAddProjectModal() {
    setupClientEmailWatcher();
    setupClientNameWatcher();
    loadClientProfiles();
    document.getElementById('project-modal-title').textContent = 'Add New Project';
    document.getElementById('project-id').value = '';
    document.getElementById('project-form').reset();
    document.getElementById('client-login-password').value = '';
    document.getElementById('client-can-access-dashboard').checked = true;
    document.getElementById('client-can-access-history').checked = true;
    document.getElementById('client-can-access-reports').checked = true;
    document.getElementById('client-can-access-invoices').checked = true;
    setClientAccountStatus('');
    setClientPasswordVisibility(true);
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
        setupClientEmailWatcher();
        setupClientNameWatcher();
        await loadClientProfiles();
        const response = await window.api.request(`/api/projects/${projectId}`);

        if (response.success) {
            const project = response.data;
            document.getElementById('project-modal-title').textContent = 'Edit Project';
            document.getElementById('project-id').value = project.id;
            document.getElementById('project-name').value = project.name;
            document.getElementById('client-name').value = project.client_name || '';
            document.getElementById('client-email').value = project.client_email || '';
            document.getElementById('client-address').value = project.client_address || '';
            document.getElementById('client-login-password').value = '';
            document.getElementById('client-can-access-dashboard').checked = project.client_can_access_dashboard !== false;
            document.getElementById('client-can-access-history').checked = project.client_can_access_history !== false;
            document.getElementById('client-can-access-reports').checked = project.client_can_access_reports !== false;
            document.getElementById('client-can-access-invoices').checked = project.client_can_access_invoices !== false;
            document.getElementById('project-color').value = project.color;
            document.getElementById('hourly-rate').value = project.hourly_rate || '';
            document.getElementById('has-tax').checked = project.has_tax || false;
            document.getElementById('project-description').value = project.description || '';
            await checkClientAccountByEmail();
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
        client_email: document.getElementById('client-email').value || null,
        client_login_password: document.getElementById('client-login-password').value || null,
        client_address: document.getElementById('client-address').value || null,
        client_can_access_dashboard: document.getElementById('client-can-access-dashboard').checked,
        client_can_access_history: document.getElementById('client-can-access-history').checked,
        client_can_access_reports: document.getElementById('client-can-access-reports').checked,
        client_can_access_invoices: document.getElementById('client-can-access-invoices').checked,
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

export function applyClientTabPermissions() {
    const currentUser = window.currentUser;
    if (!currentUser || currentUser.role !== 'client') {
        return;
    }

    const selectedProject = State.allProjects.find(p => String(p.id) === String(State.selectedProjectId));
    if (!selectedProject) {
        return;
    }

    const permissions = {
        dashboard: selectedProject.client_can_access_dashboard !== false,
        history: selectedProject.client_can_access_history !== false,
        reports: selectedProject.client_can_access_reports !== false,
        invoices: selectedProject.client_can_access_invoices !== false,
    };

    const clientTabs = ['dashboard', 'history', 'reports', 'invoices'];

    clientTabs.forEach(tabName => {
        const nav = document.querySelector(`.nav-tab[data-tab="${tabName}"]`);
        const content = document.getElementById(`${tabName}-tab`);
        const canAccess = permissions[tabName];

        if (nav) {
            nav.style.display = canAccess ? '' : 'none';
        }

        if (content) {
            content.style.display = canAccess ? '' : 'none';
        }
    });

    const activeTab = document.querySelector('.nav-tab.active');
    const activeTabName = activeTab?.getAttribute('data-tab');
    if (activeTabName && !permissions[activeTabName]) {
        const fallback = clientTabs.find(tabName => permissions[tabName]);
        if (fallback && typeof window.showTab === 'function') {
            window.showTab(fallback);
        }
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
