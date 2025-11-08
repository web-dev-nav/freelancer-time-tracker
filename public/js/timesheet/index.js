/**
 * Global Namespace Index
 *
 * This file imports all modules and exports their functions to the global window object
 * for compatibility with existing HTML onclick attributes and inline event handlers.
 *
 * Note: Cache busting is handled by timestamp in blade template (index.js?v={{ time() }})
 * This forces browser to reload index.js and all its module dependencies including invoices.js
 */

// Import all modules
import * as State from './state.js';
import * as Utils from './utils.js';
import * as Dashboard from './dashboard.js';
import * as Tracker from './tracker.js';
import * as History from './history.js';
import * as Reports from './reports.js';
import * as Projects from './projects.js';
import * as Invoices from './invoices.js';
import * as Backups from './backups.js';
import { showSettingsModal, hideSettingsModal } from './settings.js';
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

// Invoice functions (called from HTML)
window.loadInvoices = Invoices.loadInvoices;
window.showCreateInvoiceModal = Invoices.showCreateInvoiceModal;
window.hideCreateInvoiceModal = Invoices.hideCreateInvoiceModal;
window.createInvoice = Invoices.createInvoice;
window.handleCreateInvoiceProjectChange = Invoices.handleCreateInvoiceProjectChange;
window.showSendInvoiceModal = Invoices.showSendInvoiceModal;
window.hideSendInvoiceModal = Invoices.hideSendInvoiceModal;
window.sendInvoice = Invoices.sendInvoice;
window.toggleScheduleSend = Invoices.toggleScheduleSend;
window.downloadInvoicePDF = Invoices.downloadInvoicePDF;
window.previewInvoicePDF = Invoices.previewInvoicePDF;
window.markInvoiceAsPaid = Invoices.markInvoiceAsPaid;
window.cancelInvoice = Invoices.cancelInvoice;
window.deleteInvoice = Invoices.deleteInvoice;
window.showEditInvoiceModal = Invoices.showEditInvoiceModal;
window.hideEditInvoiceModal = Invoices.hideEditInvoiceModal;
window.saveInvoiceChanges = Invoices.saveInvoiceChanges;
window.showAddItemModal = Invoices.showAddItemModal;
window.hideAddItemModal = Invoices.hideAddItemModal;
window.calculateItemAmount = Invoices.calculateItemAmount;
window.deleteInvoiceItem = Invoices.deleteInvoiceItem;
window.showAddItemModalForCreate = Invoices.showAddItemModalForCreate;
window.editCreateInvoiceItem = Invoices.editCreateInvoiceItem;
window.deleteCreateInvoiceItem = Invoices.deleteCreateInvoiceItem;

// Settings functions
window.showSettingsModal = showSettingsModal;
window.hideSettingsModal = hideSettingsModal;

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
    // Note: Form submit handlers for invoices are now attached when modals open
    // to prevent duplicate submissions

    // Add ESC key handler to close modals
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' || e.keyCode === 27) {
            // Find which modal is currently open and close it
            const openEditModal = document.querySelector('#edit-log-modal.show');
            const openViewModal = document.querySelector('#view-details-modal.show');
            const openClockOutModal = document.querySelector('#clock-out-modal.show');
            const openProjectModal = document.querySelector('#project-modal.show');
            const openCreateInvoiceModal = document.querySelector('#create-invoice-modal.show');
            const openSendInvoiceModal = document.querySelector('#send-invoice-modal.show');
            const openEditInvoiceModal = document.querySelector('#edit-invoice-modal.show');
            const openAddItemModal = document.querySelector('#add-item-modal.show');
            const openSettingsModal = document.querySelector('#settings-modal.show');

            if (openEditModal && typeof window.hideEditLogModal === 'function') {
                e.preventDefault();
                window.hideEditLogModal();
            } else if (openViewModal && typeof window.hideViewDetailsModal === 'function') {
                e.preventDefault();
                window.hideViewDetailsModal();
            } else if (openClockOutModal && typeof window.hideClockOutModal === 'function') {
                e.preventDefault();
                window.hideClockOutModal();
            } else if (openProjectModal && typeof window.hideProjectModal === 'function') {
                e.preventDefault();
                window.hideProjectModal();
            } else if (openAddItemModal && typeof window.hideAddItemModal === 'function') {
                e.preventDefault();
                window.hideAddItemModal();
            } else if (openEditInvoiceModal && typeof window.hideEditInvoiceModal === 'function') {
                e.preventDefault();
                window.hideEditInvoiceModal();
            } else if (openCreateInvoiceModal && typeof window.hideCreateInvoiceModal === 'function') {
                e.preventDefault();
                window.hideCreateInvoiceModal();
            } else if (openSendInvoiceModal && typeof window.hideSendInvoiceModal === 'function') {
                e.preventDefault();
                window.hideSendInvoiceModal();
            } else if (openSettingsModal && typeof window.hideSettingsModal === 'function') {
                e.preventDefault();
                window.hideSettingsModal();
            }
        }
    });

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
            const projectModal = target.closest('#project-modal');
            const createInvoiceModal = target.closest('#create-invoice-modal');
            const sendInvoiceModal = target.closest('#send-invoice-modal');
            const editInvoiceModal = target.closest('#edit-invoice-modal');
            const addItemModal = target.closest('#add-item-modal');
            const settingsModal = target.closest('#settings-modal');

            if (editModal && typeof window.hideEditLogModal === 'function') {
                window.hideEditLogModal();
            } else if (viewModal && typeof window.hideViewDetailsModal === 'function') {
                window.hideViewDetailsModal();
            } else if (clockOutModal && typeof window.hideClockOutModal === 'function') {
                window.hideClockOutModal();
            } else if (projectModal && typeof window.hideProjectModal === 'function') {
                window.hideProjectModal();
            } else if (addItemModal && typeof window.hideAddItemModal === 'function') {
                window.hideAddItemModal();
            } else if (editInvoiceModal && typeof window.hideEditInvoiceModal === 'function') {
                window.hideEditInvoiceModal();
            } else if (createInvoiceModal && typeof window.hideCreateInvoiceModal === 'function') {
                window.hideCreateInvoiceModal();
            } else if (sendInvoiceModal && typeof window.hideSendInvoiceModal === 'function') {
                window.hideSendInvoiceModal();
            } else if (settingsModal && typeof window.hideSettingsModal === 'function') {
                window.hideSettingsModal();
            }
            return;
        }

        // Check if clicked on Cancel button in modal footer
        if (target.matches('.btn-secondary') || target.closest('.btn-secondary')) {
            const modalFooter = target.closest('.modal-footer');

            if (modalFooter) {
                const editModal = target.closest('#edit-log-modal');
                const viewModal = target.closest('#view-details-modal');
                const clockOutModal = target.closest('#clock-out-modal');
                const projectModal = target.closest('#project-modal');
                const createInvoiceModal = target.closest('#create-invoice-modal');
                const sendInvoiceModal = target.closest('#send-invoice-modal');
                const editInvoiceModal = target.closest('#edit-invoice-modal');
                const addItemModal = target.closest('#add-item-modal');

                if (editModal && typeof window.hideEditLogModal === 'function') {
                    e.preventDefault();
                    e.stopPropagation();
                    window.hideEditLogModal();
                    return;
                } else if (viewModal && typeof window.hideViewDetailsModal === 'function') {
                    e.preventDefault();
                    e.stopPropagation();
                    window.hideViewDetailsModal();
                    return;
                } else if (clockOutModal && typeof window.hideClockOutModal === 'function') {
                    e.preventDefault();
                    e.stopPropagation();
                    window.hideClockOutModal();
                    return;
                } else if (projectModal && typeof window.hideProjectModal === 'function') {
                    e.preventDefault();
                    e.stopPropagation();
                    window.hideProjectModal();
                    return;
                } else if (addItemModal && typeof window.hideAddItemModal === 'function') {
                    e.preventDefault();
                    e.stopPropagation();
                    window.hideAddItemModal();
                    return;
                } else if (editInvoiceModal && typeof window.hideEditInvoiceModal === 'function') {
                    e.preventDefault();
                    e.stopPropagation();
                    window.hideEditInvoiceModal();
                    return;
                } else if (createInvoiceModal && typeof window.hideCreateInvoiceModal === 'function') {
                    e.preventDefault();
                    e.stopPropagation();
                    window.hideCreateInvoiceModal();
                    return;
                } else if (sendInvoiceModal && typeof window.hideSendInvoiceModal === 'function') {
                    e.preventDefault();
                    e.stopPropagation();
                    window.hideSendInvoiceModal();
                    return;
                }
            }
        }

        // Check if clicked on modal overlay (background)
        const modalOverlay = target.closest('#modal-overlay');
        if (modalOverlay && target.id === 'modal-overlay') {
            e.preventDefault();
            e.stopPropagation();

            // Find which modal is currently open and close it
            const openEditModal = document.querySelector('#edit-log-modal.show');
            const openViewModal = document.querySelector('#view-details-modal.show');
            const openClockOutModal = document.querySelector('#clock-out-modal.show');
            const openProjectModal = document.querySelector('#project-modal.show');
            const openCreateInvoiceModal = document.querySelector('#create-invoice-modal.show');
            const openSendInvoiceModal = document.querySelector('#send-invoice-modal.show');
            const openEditInvoiceModal = document.querySelector('#edit-invoice-modal.show');
            const openAddItemModal = document.querySelector('#add-item-modal.show');
            const openSettingsModal = document.querySelector('#settings-modal.show');

            if (openEditModal && typeof window.hideEditLogModal === 'function') {
                window.hideEditLogModal();
            } else if (openViewModal && typeof window.hideViewDetailsModal === 'function') {
                window.hideViewDetailsModal();
            } else if (openClockOutModal && typeof window.hideClockOutModal === 'function') {
                window.hideClockOutModal();
            } else if (openProjectModal && typeof window.hideProjectModal === 'function') {
                window.hideProjectModal();
            } else if (openAddItemModal && typeof window.hideAddItemModal === 'function') {
                window.hideAddItemModal();
            } else if (openEditInvoiceModal && typeof window.hideEditInvoiceModal === 'function') {
                window.hideEditInvoiceModal();
            } else if (openCreateInvoiceModal && typeof window.hideCreateInvoiceModal === 'function') {
                window.hideCreateInvoiceModal();
            } else if (openSendInvoiceModal && typeof window.hideSendInvoiceModal === 'function') {
                window.hideSendInvoiceModal();
            } else if (openSettingsModal && typeof window.hideSettingsModal === 'function') {
                window.hideSettingsModal();
            }
            return;
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
    Invoices,
    Backups,
    App
};
