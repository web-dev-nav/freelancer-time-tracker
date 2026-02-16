/**
 * Application Initialization Module
 *
 * Handles application initialization, event listeners setup, and tab navigation.
 * This is the main entry point for the timesheet application.
 */

import * as State from './state.js';
import * as Utils from './utils.js';
import { loadDashboardStats, checkActiveSession } from './dashboard.js';
import { clockIn, clockOut } from './tracker.js';
import { loadHistory, updateLog, hideEditLogModal, createNewEntry } from './history.js';
import { generateReport, selectThisWeek } from './reports.js';
import { loadProjectsForSelector, onProjectChange, loadProjects, saveProject, backupProject, backupDatabase } from './projects.js';
import { loadInvoices } from './invoices.js';
import { loadBackups } from './backups.js';
import { hideClockOutModal } from './tracker.js';

/**
 * Initialize the application on DOM content loaded
 */
export function initializeApp() {
    // Set current date and time
    const now = Utils.getCurrentDateTime();
    const clockInDate = document.getElementById('clock-in-date');
    const clockInTime = document.getElementById('clock-in-time');
    const reportStartDate = document.getElementById('report-start-date');
    const reportEndDate = document.getElementById('report-end-date');

    if (clockInDate) clockInDate.value = now.date;
    if (clockInTime) clockInTime.value = now.time;
    if (reportStartDate && reportEndDate) {
        selectThisWeek();
    }
}

/**
 * Setup all event listeners for the application
 */
export function setupEventListeners() {
    // Project selector change
    const projectSelector = document.getElementById('project-selector');
    if (projectSelector) {
        projectSelector.addEventListener('change', function() {
            State.setSelectedProjectId(this.value || null);
            localStorage.setItem('selectedProjectId', State.selectedProjectId || '');
            onProjectChange();
        });
    }

    // Tab navigation
    document.querySelectorAll('.nav-tab').forEach(tab => {
        tab.addEventListener('click', function() {
            const tabName = this.getAttribute('data-tab');
            showTab(tabName);
        });
    });

    // Clock in form
    const clockInForm = document.getElementById('clock-in-form');
    if (clockInForm) {
        clockInForm.addEventListener('submit', function(e) {
            e.preventDefault();
            clockIn();
        });
    }

    // Clock out form
    const clockOutForm = document.getElementById('clock-out-form');
    if (clockOutForm) {
        clockOutForm.addEventListener('submit', function(e) {
            e.preventDefault();
            clockOut();
        });
    }

    // Edit log form
    const editLogForm = document.getElementById('edit-log-form');
    if (editLogForm) {
        editLogForm.addEventListener('submit', function(e) {
            e.preventDefault();
            updateLog();
        });
    }

    // Report form
    const reportForm = document.getElementById('report-form');
    if (reportForm) {
        reportForm.addEventListener('submit', function(e) {
            e.preventDefault();
            generateReport();
        });
    }

    // Modal overlay
    const modalOverlay = document.getElementById('modal-overlay');
    if (modalOverlay) {
        modalOverlay.addEventListener('click', function() {
            hideClockOutModal();
            hideEditLogModal();
            if (typeof window.hideSettingsModal === 'function') {
                window.hideSettingsModal();
            }
        });
    }

    // Project form
    const projectForm = document.getElementById('project-form');
    if (projectForm) {
        projectForm.addEventListener('submit', saveProject);
    }

    // Note: History button event listeners are now set up in index.js for better reliability
}

/**
 * Show a specific tab and load its data
 * @param {string} tabName - Name of the tab to show
 */
export function showTab(tabName) {
    // Update nav tabs
    document.querySelectorAll('.nav-tab').forEach(tab => {
        tab.classList.remove('active');
    });
    const navTab = document.querySelector(`[data-tab="${tabName}"]`);
    if (!navTab) {
        return;
    }
    navTab.classList.add('active');

    // Update tab content
    document.querySelectorAll('.tab-content').forEach(content => {
        content.classList.remove('active');
    });
    const tabContent = document.getElementById(`${tabName}-tab`);
    if (!tabContent) {
        return;
    }
    tabContent.classList.add('active');

    // Load data for specific tabs
    if (tabName === 'history') {
        State.setCurrentPage(1); // Reset to first page
        loadHistory();
    } else if (tabName === 'dashboard') {
        loadDashboardStats(); // This already handles active session
    } else if (tabName === 'reports') {
        const reportStartDate = document.getElementById('report-start-date');
        const reportEndDate = document.getElementById('report-end-date');
        if (!reportStartDate?.value || !reportEndDate?.value) {
            selectThisWeek();
        }
        generateReport();
    } else if (tabName === 'projects') {
        loadProjects();
    } else if (tabName === 'invoices') {
        loadInvoices();
    } else if (tabName === 'backups') {
        loadBackups();
    }
}

/**
 * Main application entry point
 */
document.addEventListener('DOMContentLoaded', function() {
    // Initialize utility functions
    Utils.initializeUtils();

    // Load week start preference
    const savedWeekStart = localStorage.getItem('weekStartDay') || 'monday';
    const weekStartDay = document.getElementById('week-start-day');
    if (weekStartDay) {
        weekStartDay.value = savedWeekStart;
    }

    // Save week start preference when changed
    if (weekStartDay) {
        weekStartDay.addEventListener('change', function() {
            localStorage.setItem('weekStartDay', this.value);
        });
    }

    initializeApp();
    setupEventListeners();
    loadProjectsForSelector();  // Load projects first
});
