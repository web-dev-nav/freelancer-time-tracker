/**
 * Application State Module
 *
 * Manages all global application state variables including:
 * - Active session tracking
 * - Dashboard statistics
 * - Report data
 * - Pagination state
 * - Project selection state
 */

// Active session and dashboard state
export let currentActiveSession = null;
export let dashboardStats = {};

// Report state
export let currentReportData = null;

// Pagination state
export let currentPage = 1;
export let currentPerPage = 15;
export let totalPages = 1;

// Project state
export let selectedProjectId = null;  // Currently selected project
export let allProjects = [];          // All active projects
export let currentProjects = [];
export let showArchived = false;

// Session timer state
export let timerInterval = null;

// State setters
export function setCurrentActiveSession(session) {
    currentActiveSession = session;
}

export function setDashboardStats(stats) {
    dashboardStats = stats;
}

export function setCurrentReportData(data) {
    currentReportData = data;
}

export function setCurrentPage(page) {
    currentPage = page;
}

export function setCurrentPerPage(perPage) {
    currentPerPage = perPage;
}

export function setTotalPages(pages) {
    totalPages = pages;
}

export function setSelectedProjectId(id) {
    selectedProjectId = id;
}

export function setAllProjects(projects) {
    allProjects = projects;
}

export function setCurrentProjects(projects) {
    currentProjects = projects;
}

export function setShowArchived(archived) {
    showArchived = archived;
}

export function setTimerInterval(interval) {
    timerInterval = interval;
}
