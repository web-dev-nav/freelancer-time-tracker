/**
 * Global Namespace Index
 *
 * This file imports all modules and exports their functions to the global window object
 * for compatibility with existing HTML onclick attributes and inline event handlers.
 */

console.log('INDEX.JS LOADING - START');

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

// Verify functions are properly exported
console.log('Timesheet modules loaded successfully');
console.log('History functions available:', {
    createNewEntry: typeof window.createNewEntry,
    editLog: typeof window.editLog,
    loadHistory: typeof window.loadHistory
});

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

console.log('Setting up history button event listeners...');

// Wait for DOM to be ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', setupHistoryButtonListeners);
} else {
    setupHistoryButtonListeners();
}

function setupHistoryButtonListeners() {
    console.log('setupHistoryButtonListeners called, readyState:', document.readyState);

    // Use event delegation on document body to catch all clicks
    document.body.addEventListener('click', function(e) {
        const target = e.target;

        // Check if clicked element or any parent is the create-new-entry button
        const createBtn = target.closest('#create-new-entry-btn');
        if (createBtn) {
            console.log('Create New Entry button clicked via delegation!');
            e.preventDefault();
            e.stopPropagation();
            if (typeof window.createNewEntry === 'function') {
                window.createNewEntry();
            } else {
                console.error('window.createNewEntry is not a function!', typeof window.createNewEntry);
            }
            return;
        }

        // Check if clicked element or any parent is the refresh button
        const refreshBtn = target.closest('#refresh-history-btn');
        if (refreshBtn) {
            console.log('Refresh History button clicked via delegation!');
            e.preventDefault();
            e.stopPropagation();
            if (typeof window.loadHistory === 'function') {
                window.loadHistory();
            } else {
                console.error('window.loadHistory is not a function!', typeof window.loadHistory);
            }
            return;
        }
    }, true); // Use capture phase

    console.log('History button listeners set up successfully');
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
