/**
 * Global Namespace Index
 *
 * This file imports all modules and exports their functions to the global window object
 * for compatibility with existing HTML onclick attributes and inline event handlers.
 */

// Import all modules
import * as State from './state.js';
import * as Utils from './utils.js';
import * as Dashboard from './dashboard.js';
import * as Tracker from './tracker.js';
import * as History from './history.js';
import * as Reports from './reports.js';
import * as Projects from './projects.js';
import * as Backups from './backups.js';
import * as App from './app.js';

// ====================
// Export to Global Window Namespace
// ====================

// Tracker functions (called from HTML)
window.clockIn = Tracker.clockIn;
window.quickClockIn = Tracker.quickClockIn;
window.showClockOutModal = Tracker.showClockOutModal;
window.hideClockOutModal = Tracker.hideClockOutModal;
window.clockOut = Tracker.clockOut;
window.cancelActiveSession = Tracker.cancelActiveSession;

// History functions (called from HTML)
window.loadHistory = History.loadHistory;
window.createNewEntry = History.createNewEntry;
window.editLog = History.editLog;
window.deleteLog = History.deleteLog;
window.viewDetails = History.viewDetails;
window.changePageSize = History.changePageSize;
window.hideViewDetailsModal = History.hideViewDetailsModal;
window.hideEditLogModal = History.hideEditLogModal;
window.showEditLogModal = History.showEditLogModal;
window.updateLog = History.updateLog;


// Reports functions (called from HTML)
window.selectThisWeek = Reports.selectThisWeek;
window.selectLastWeek = Reports.selectLastWeek;
window.selectThisMonth = Reports.selectThisMonth;
window.selectLastMonth = Reports.selectLastMonth;
window.generateReport = Reports.generateReport;
window.exportExcel = Reports.exportExcel;

// Projects functions (called from HTML)
window.showAddProjectModal = Projects.showAddProjectModal;
window.editProject = Projects.editProject;
window.hideProjectModal = Projects.hideProjectModal;
window.archiveProject = Projects.archiveProject;
window.activateProject = Projects.activateProject;
window.deleteProject = Projects.deleteProject;
window.toggleArchivedProjects = Projects.toggleArchivedProjects;

// Backup functions (called from HTML)
window.loadBackups = Backups.loadBackups;
window.createFullBackup = Backups.createFullBackup;
window.downloadBackup = Backups.downloadBackup;
window.deleteBackup = Backups.deleteBackup;
window.refreshBackupList = Backups.refreshBackupList;
window.showProjectBackupSelector = Backups.showProjectBackupSelector;
window.hideProjectBackupSelector = Backups.hideProjectBackupSelector;
window.createProjectBackup = Backups.createProjectBackup;

// App functions (called from HTML)
window.showTab = App.showTab;

// ====================
// Setup Event Listeners for History Buttons
// ====================

// Wait for DOM to be ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', setupHistoryButtonListeners);
} else {
    setupHistoryButtonListeners();
}

function setupHistoryButtonListeners() {
    // Use event delegation on document body to catch all clicks
    document.body.addEventListener('click', function(e) {
        const target = e.target;

        // Check if clicked element or any parent is the create-new-entry button
        const createBtn = target.closest('#create-new-entry-btn');
        if (createBtn) {
            e.preventDefault();
            e.stopPropagation();
            if (typeof window.createNewEntry === 'function') {
                window.createNewEntry();
            }
            return;
        }

        // Check if clicked element or any parent is the refresh button
        const refreshBtn = target.closest('#refresh-history-btn');
        if (refreshBtn) {
            e.preventDefault();
            e.stopPropagation();
            if (typeof window.loadHistory === 'function') {
                window.loadHistory();
            }
            return;
        }

        // Check if clicked on modal close button (X button)
        const modalClose = target.closest('.modal-close');
        if (modalClose) {
            e.preventDefault();
            e.stopPropagation();

            // Check which modal it belongs to
            const editModal = target.closest('#edit-log-modal');
            const viewModal = target.closest('#view-details-modal');
            const clockOutModal = target.closest('#clock-out-modal');

            if (editModal && typeof window.hideEditLogModal === 'function') {
                window.hideEditLogModal();
            } else if (viewModal && typeof window.hideViewDetailsModal === 'function') {
                window.hideViewDetailsModal();
            } else if (clockOutModal && typeof window.hideClockOutModal === 'function') {
                window.hideClockOutModal();
            }
            return;
        }

        // Check if clicked on Cancel button in edit modal footer
        if (target.matches('.btn-secondary') || target.closest('.btn-secondary')) {
            const modalFooter = target.closest('.modal-footer');
            const editModal = target.closest('#edit-log-modal');

            if (modalFooter && editModal) {
                e.preventDefault();
                e.stopPropagation();
                if (typeof window.hideEditLogModal === 'function') {
                    window.hideEditLogModal();
                }
                return;
            }
        }
    }, true); // Use capture phase
}

// ====================
// Module Exports (for ES6 imports)
// ====================

export {
    State,
    Utils,
    Dashboard,
    Tracker,
    History,
    Reports,
    Projects,
    Backups,
    App
};
