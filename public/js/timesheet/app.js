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
import { generateReport } from './reports.js';
import { loadProjectsForSelector, onProjectChange, loadProjects, saveProject, backupProject, backupDatabase } from './projects.js';
import { hideClockOutModal } from './tracker.js';

/**
 * Initialize the application on DOM content loaded
 */
export function initializeApp() {
    // Set current date and time
    const now = Utils.getCurrentDateTime();
    document.getElementById('clock-in-date').value = now.date;
    document.getElementById('clock-in-time').value = now.time;
    document.getElementById('report-end-date').value = now.date;
}

/**
 * Setup all event listeners for the application
 */
export function setupEventListeners() {
    // Project selector change
    document.getElementById('project-selector').addEventListener('change', function() {
        State.setSelectedProjectId(this.value || null);
        localStorage.setItem('selectedProjectId', State.selectedProjectId || '');
        onProjectChange();
    });

    // Tab navigation
    document.querySelectorAll('.nav-tab').forEach(tab => {
        tab.addEventListener('click', function() {
            const tabName = this.getAttribute('data-tab');
            showTab(tabName);
        });
    });

    // Clock in form
    document.getElementById('clock-in-form').addEventListener('submit', function(e) {
        e.preventDefault();
        clockIn();
    });

    // Clock out form
    document.getElementById('clock-out-form').addEventListener('submit', function(e) {
        e.preventDefault();
        clockOut();
    });

    // Edit log form
    document.getElementById('edit-log-form').addEventListener('submit', function(e) {
        e.preventDefault();
        updateLog();
    });

    // Report form
    document.getElementById('report-form').addEventListener('submit', function(e) {
        e.preventDefault();
        generateReport();
    });

    // Modal overlay
    document.getElementById('modal-overlay').addEventListener('click', function(e) {
        hideClockOutModal();
        hideEditLogModal();
    });

    // Project form
    document.getElementById('project-form').addEventListener('submit', saveProject);

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
    document.querySelector(`[data-tab="${tabName}"]`).classList.add('active');

    // Update tab content
    document.querySelectorAll('.tab-content').forEach(content => {
        content.classList.remove('active');
    });
    document.getElementById(`${tabName}-tab`).classList.add('active');

    // Load data for specific tabs
    if (tabName === 'history') {
        State.setCurrentPage(1); // Reset to first page
        loadHistory();
    } else if (tabName === 'dashboard') {
        loadDashboardStats(); // This already handles active session
    } else if (tabName === 'projects') {
        loadProjects();
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
    document.getElementById('week-start-day').value = savedWeekStart;

    // Save week start preference when changed
    document.getElementById('week-start-day').addEventListener('change', function() {
        localStorage.setItem('weekStartDay', this.value);
    });

    initializeApp();
    setupEventListeners();
    loadProjectsForSelector();  // Load projects first
});
