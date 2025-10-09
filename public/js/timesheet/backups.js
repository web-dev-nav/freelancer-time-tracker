/**
 * Backups Module
 *
 * Handles backup management including listing, creating, downloading,
 * and deleting backup files.
 */

/**
 * Load all backups from the server
 */
export async function loadBackups() {
    try {
        const response = await window.api.request('/api/backups');

        if (response.success) {
            displayBackups(response.data);
        }

        // Load projects for the backup selector
        await loadProjectsForBackup();
    } catch (error) {
        window.notify.error('Failed to load backups: ' + error.message);
    }
}

/**
 * Load projects for the backup selector dropdown
 */
async function loadProjectsForBackup() {
    try {
        const response = await window.api.request('/api/projects');

        if (response.success) {
            const select = document.getElementById('backup-project-select');
            const projects = response.data;

            // Clear existing options except the first one
            select.innerHTML = '<option value="">-- Select a Project --</option>';

            // Add project options
            projects.forEach(project => {
                const option = document.createElement('option');
                option.value = project.id;
                option.textContent = `${project.name}${project.client_name ? ' (' + project.client_name + ')' : ''}`;
                select.appendChild(option);
            });
        }
    } catch (error) {
        console.error('Failed to load projects for backup:', error);
    }
}

/**
 * Display backups in the backups list
 */
function displayBackups(backups) {
    const list = document.getElementById('backups-list');

    if (backups.length === 0) {
        list.innerHTML = `
            <div class="empty-backups">
                <i class="fas fa-database"></i>
                <h3>No Backups Found</h3>
                <p>Create your first backup to get started!</p>
            </div>
        `;
        return;
    }

    list.innerHTML = backups.map(backup => `
        <div class="backup-item">
            <div class="backup-info">
                <div class="backup-filename">
                    <i class="fas fa-file-archive"></i>
                    ${backup.filename}
                </div>
                <div class="backup-meta">
                    <span class="backup-size">
                        <i class="fas fa-hdd"></i>
                        ${backup.formatted_size}
                    </span>
                    <span class="backup-date">
                        <i class="fas fa-calendar"></i>
                        ${backup.formatted_date}
                    </span>
                </div>
            </div>
            <div class="backup-actions">
                <button class="btn btn-sm btn-primary" onclick="downloadBackup('${backup.filename}')" title="Download">
                    <i class="fas fa-download"></i>
                    Download
                </button>
                <button class="btn btn-sm btn-danger" onclick="deleteBackup('${backup.filename}')" title="Delete">
                    <i class="fas fa-trash"></i>
                    Delete
                </button>
            </div>
        </div>
    `).join('');
}

/**
 * Create a new full database backup
 */
export async function createFullBackup() {
    try {
        // Show loading notification
        window.notify.info('Creating backup... This may take a moment.');

        // Create a download link and trigger it
        const link = document.createElement('a');
        link.href = '/api/backups/create';
        link.download = `database-full-${new Date().toISOString().split('T')[0]}.sql`;
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);

        // Wait a bit then reload the list
        setTimeout(() => {
            window.notify.success('Full database backup created successfully!');
            loadBackups();
        }, 2000);
    } catch (error) {
        window.notify.error('Failed to create backup: ' + error.message);
    }
}

/**
 * Download a specific backup file
 * @param {string} filename - Backup filename
 */
export async function downloadBackup(filename) {
    try {
        const link = document.createElement('a');
        link.href = `/api/backups/${filename}`;
        link.download = filename;
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);

        window.notify.success('Backup download started!');
    } catch (error) {
        window.notify.error('Failed to download backup: ' + error.message);
    }
}

/**
 * Delete a backup file
 * @param {string} filename - Backup filename
 */
export async function deleteBackup(filename) {
    if (!confirm(`Are you sure you want to delete this backup?\n\n${filename}\n\nThis action cannot be undone.`)) {
        return;
    }

    try {
        const response = await window.api.request(`/api/backups/${filename}`, {
            method: 'DELETE'
        });

        if (response.success) {
            window.notify.success('Backup deleted successfully!');
            loadBackups();
        }
    } catch (error) {
        window.notify.error('Failed to delete backup: ' + error.message);
    }
}

/**
 * Refresh the backup list
 */
export function refreshBackupList() {
    loadBackups();
}

/**
 * Show the project backup selector
 */
export function showProjectBackupSelector() {
    document.getElementById('project-backup-selector').style.display = 'block';
}

/**
 * Hide the project backup selector
 */
export function hideProjectBackupSelector() {
    document.getElementById('project-backup-selector').style.display = 'none';
    document.getElementById('backup-project-select').value = '';
}

/**
 * Create a backup for a specific project
 */
export async function createProjectBackup() {
    const projectId = document.getElementById('backup-project-select').value;

    if (!projectId) {
        window.notify.error('Please select a project to backup');
        return;
    }

    try {
        window.notify.info('Creating project backup...');

        const response = await window.api.request(`/api/projects/${projectId}/backup`);

        if (response.success) {
            window.notify.success('Project backup created successfully!');
            hideProjectBackupSelector();
            loadBackups();
        }
    } catch (error) {
        window.notify.error('Failed to create project backup: ' + error.message);
    }
}
