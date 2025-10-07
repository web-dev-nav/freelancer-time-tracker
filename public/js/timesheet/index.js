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
import * as App from './app.js';

// ====================
// Export to Global Window Namespace
// ====================

// Tracker functions (called from HTML)
window.clockIn = Tracker.clockIn;
window.quickClockIn = Tracker.quickClockIn;
window.showClockOutModal = Tracker.showClockOutModal;
window.clockOut = Tracker.clockOut;
window.cancelActiveSession = Tracker.cancelActiveSession;

// History functions (called from HTML)
window.loadHistory = History.loadHistory;
window.editLog = History.editLog;
window.deleteLog = History.deleteLog;
window.viewDetails = History.viewDetails;
window.changePageSize = History.changePageSize;
window.hideViewDetailsModal = History.hideViewDetailsModal;

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

// App functions (called from HTML)
window.showTab = App.showTab;

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
    App
};
