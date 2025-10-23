/**
 * Backups Module
 *
 * Handles backup management including listing, creating, downloading,
 * and deleting backup files with pagination support.
 */

// Pagination state
let currentPage = 1;
let perPage = 10;
let totalPages = 1;

/**
 * Load backups from the server with pagination
 */
export async function loadBackups(page = 1) {
    try {
        currentPage = page;
        const response = await window.api.request(`/api/backups?page=${page}&per_page=${perPage}`);

        if (response.success) {
            displayBackups(response.data, response.pagination);
        }

        // Load projects for the backup selector (only on first load)
        if (page === 1) {
            await loadProjectsForBackup();
        }
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
 * Display backups in the backups list with pagination
 */
function displayBackups(backups, pagination) {
    const list = document.getElementById('backups-list');

    if (backups.length === 0 && pagination.current_page === 1) {
        list.innerHTML = `
            <div class="empty-backups">
                <i class="fas fa-database"></i>
                <h3>No Backups Found</h3>
                <p>Create your first backup to get started!</p>
            </div>
        `;
        return;
    }

    // Store pagination info
    totalPages = pagination.total_pages;
    currentPage = pagination.current_page;

    // Build backup items HTML
    const backupsHtml = backups.map(backup => `
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

    // Build pagination HTML
    const paginationHtml = buildPaginationHtml(pagination);

    list.innerHTML = backupsHtml + paginationHtml;
}

/**
 * Build pagination controls HTML
 */
function buildPaginationHtml(pagination) {
    if (pagination.total_pages <= 1) {
        return '';
    }

    const { current_page, total_pages, total } = pagination;

    let paginationHtml = '<div class="backup-pagination">';

    // Pagination info
    paginationHtml += `<div class="pagination-info">Showing page ${current_page} of ${total_pages} (${total} total backups)</div>`;

    // Pagination buttons
    paginationHtml += '<div class="pagination-controls">';

    // First page button
    if (current_page > 1) {
        paginationHtml += `<button class="btn btn-sm btn-secondary" onclick="loadBackups(1)" title="First Page">
            <i class="fas fa-angle-double-left"></i>
        </button>`;
    }

    // Previous page button
    if (current_page > 1) {
        paginationHtml += `<button class="btn btn-sm btn-secondary" onclick="loadBackups(${current_page - 1})" title="Previous Page">
            <i class="fas fa-angle-left"></i> Previous
        </button>`;
    }

    // Page numbers
    const pageNumbers = getPageNumbers(current_page, total_pages);
    pageNumbers.forEach(pageNum => {
        if (pageNum === '...') {
            paginationHtml += `<span class="pagination-ellipsis">...</span>`;
        } else {
            const activeClass = pageNum === current_page ? 'btn-primary' : 'btn-secondary';
            paginationHtml += `<button class="btn btn-sm ${activeClass}" onclick="loadBackups(${pageNum})">${pageNum}</button>`;
        }
    });

    // Next page button
    if (current_page < total_pages) {
        paginationHtml += `<button class="btn btn-sm btn-secondary" onclick="loadBackups(${current_page + 1})" title="Next Page">
            Next <i class="fas fa-angle-right"></i>
        </button>`;
    }

    // Last page button
    if (current_page < total_pages) {
        paginationHtml += `<button class="btn btn-sm btn-secondary" onclick="loadBackups(${total_pages})" title="Last Page">
            <i class="fas fa-angle-double-right"></i>
        </button>`;
    }

    paginationHtml += '</div></div>';

    return paginationHtml;
}

/**
 * Get page numbers to display in pagination
 */
function getPageNumbers(currentPage, totalPages) {
    const pages = [];
    const maxVisible = 5; // Maximum number of page buttons to show

    if (totalPages <= maxVisible) {
        // Show all pages if total is small
        for (let i = 1; i <= totalPages; i++) {
            pages.push(i);
        }
    } else {
        // Always show first page
        pages.push(1);

        // Calculate range around current page
        let startPage = Math.max(2, currentPage - 1);
        let endPage = Math.min(totalPages - 1, currentPage + 1);

        // Add ellipsis if needed
        if (startPage > 2) {
            pages.push('...');
        }

        // Add pages around current page
        for (let i = startPage; i <= endPage; i++) {
            pages.push(i);
        }

        // Add ellipsis if needed
        if (endPage < totalPages - 1) {
            pages.push('...');
        }

        // Always show last page
        pages.push(totalPages);
    }

    return pages;
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
