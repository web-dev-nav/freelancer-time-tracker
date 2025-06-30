@extends('layouts.app')

@section('title', 'Professional Timesheet')

@section('content')
<div id="timesheet-app">
    <!-- Navigation Tabs -->
    <div class="nav-tabs">
        <button class="nav-tab active" data-tab="dashboard">
            <i class="fas fa-tachometer-alt"></i>
            Dashboard
        </button>
        <button class="nav-tab" data-tab="tracker">
            <i class="fas fa-play"></i>
            Time Tracker
        </button>
        <button class="nav-tab" data-tab="history">
            <i class="fas fa-history"></i>
            History
        </button>
        <button class="nav-tab" data-tab="reports">
            <i class="fas fa-chart-bar"></i>
            Reports
        </button>
    </div>

    <!-- Dashboard Tab -->
    <div class="tab-content active" id="dashboard-tab">
        <div class="dashboard-grid">
            <!-- Stats Cards -->
            <div class="stats-container">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-value" id="today-hours">0:00</div>
                        <div class="stat-label">Today</div>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-calendar-week"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-value" id="week-hours">0:00</div>
                        <div class="stat-label">This Week</div>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-value" id="month-hours">0:00</div>
                        <div class="stat-label">This Month</div>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-list"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-value" id="total-sessions">0</div>
                        <div class="stat-label">Total Sessions</div>
                    </div>
                </div>
            </div>

            <!-- Active Session Card -->
            <div class="active-session-card" id="active-session-card">
                <div class="session-header">
                    <h3>
                        <i class="fas fa-play-circle text-success"></i>
                        Active Session
                    </h3>
                    <div class="session-status">
                        <span class="status-indicator active"></span>
                        Working
                    </div>
                </div>
                <div class="session-details">
                    <div class="session-time">
                        <div class="session-start">
                            <label>Started:</label>
                            <span id="session-start-display">--</span>
                        </div>
                        <div class="session-duration">
                            <label>Duration:</label>
                            <span id="session-duration-display">0:00</span>
                        </div>
                    </div>
                </div>
                <div class="session-actions">
                    <button class="btn btn-warning" onclick="showClockOutModal()">
                        <i class="fas fa-stop"></i>
                        Clock Out
                    </button>
                    <button class="btn btn-danger" onclick="cancelActiveSession()">
                        <i class="fas fa-times"></i>
                        Cancel
                    </button>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="quick-actions">
                <h3 class="section-title">Quick Actions</h3>
                <div class="action-buttons">
                    <button class="action-btn" onclick="quickClockIn()">
                        <i class="fas fa-play"></i>
                        Quick Clock In
                    </button>
                    <button class="action-btn" onclick="showTab('reports')">
                        <i class="fas fa-download"></i>
                        Export Latest
                    </button>
                    <button class="action-btn" onclick="showTab('history')">
                        <i class="fas fa-eye"></i>
                        View History
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Time Tracker Tab -->
    <div class="tab-content" id="tracker-tab">
        <div class="tracker-container">
            <!-- Clock In Section -->
            <div class="clock-section" id="clock-in-section">
                <div class="clock-header">
                    <h2>
                        <i class="fas fa-clock"></i>
                        Start Your Work Session
                    </h2>
                    <p>Enter your start time and begin tracking your work</p>
                </div>
                
                <form id="clock-in-form" class="clock-form">
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label" for="clock-in-date">
                                <i class="fas fa-calendar"></i>
                                Date
                            </label>
                            <input type="date" id="clock-in-date" class="form-control" required>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label" for="clock-in-time">
                                <i class="fas fa-clock"></i>
                                Start Time
                            </label>
                            <input type="time" id="clock-in-time" class="form-control" required>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-primary btn-large">
                        <i class="fas fa-play"></i>
                        Clock In
                    </button>
                </form>
            </div>

            <!-- Active Session Display -->
            <div class="active-session-display" id="active-session-display">
                <div class="session-visual">
                    <div class="session-avatar">
                        <i class="fas fa-user-clock"></i>
                    </div>
                    <div class="session-info">
                        <h3>You're Currently Working</h3>
                        <div class="session-meta">
                            <span id="current-session-start">Started at --</span>
                            <span class="session-separator">•</span>
                            <span id="current-session-duration">Duration: 0:00</span>
                        </div>
                    </div>
                </div>
                
                <div class="session-actions">
                    <button class="btn btn-success btn-large" onclick="showClockOutModal()">
                        <i class="fas fa-stop"></i>
                        Clock Out
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- History Tab -->
    <div class="tab-content" id="history-tab">
        <div class="history-container">
            <div class="history-header">
                <h2>Work History</h2>
                <div class="history-actions">
                    <button class="btn btn-secondary" onclick="loadHistory()">
                        <i class="fas fa-sync"></i>
                        Refresh
                    </button>
                </div>
            </div>
            
            <div class="history-table-container">
                <table class="history-table" id="history-table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Clock In</th>
                            <th>Clock Out</th>
                            <th>Duration</th>
                            <th>Description</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="history-tbody">
                        <tr>
                            <td colspan="6" class="text-center">
                                <div class="loading">
                                    <div class="spinner"></div>
                                    Loading history...
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            
            <div class="pagination-container" id="pagination-container">
                <!-- Pagination will be inserted here -->
            </div>
        </div>
    </div>

    <!-- Reports Tab -->
    <div class="tab-content" id="reports-tab">
        <div class="reports-container">
            <div class="report-generator">
                <h2>Generate Timesheet Report</h2>
                <p>Generate professional timesheets for client billing</p>
                
                <form id="report-form" class="report-form">
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label" for="week-start-day">
                                <i class="fas fa-cog"></i>
                                Week Starts On
                            </label>
                            <select id="week-start-day" class="form-control">
                                <option value="monday">Monday</option>
                                <option value="sunday">Sunday</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">
                                <i class="fas fa-calendar-week"></i>
                                Quick Select
                            </label>
                            <div class="quick-select-buttons">
                                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="selectThisWeek()">This Week</button>
                                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="selectLastWeek()">Last Week</button>
                                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="selectThisMonth()">This Month</button>
                                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="selectLastMonth()">Last Month</button>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label" for="report-start-date">
                                <i class="fas fa-calendar-alt"></i>
                                Report Start Date
                            </label>
                            <input type="date" id="report-start-date" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="report-end-date">
                                <i class="fas fa-calendar-alt"></i>
                                Report End Date
                            </label>
                            <input type="date" id="report-end-date" class="form-control" required>
                        </div>
                    </div>
                    
                    <div class="report-actions">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-chart-bar"></i>
                            Generate Report
                        </button>
                        <button type="button" class="btn btn-success" onclick="exportExcel()" id="export-btn" disabled>
                            <i class="fas fa-file-excel"></i>
                            Export Excel
                        </button>
                    </div>
                </form>
            </div>

            <div class="report-results" id="report-results">
                <div class="report-summary" id="report-summary">
                    <div class="summary-card">
                        <div class="summary-value" id="report-total-hours">0:00</div>
                        <div class="summary-label">Total Hours</div>
                    </div>
                    <div class="summary-card">
                        <div class="summary-value" id="report-total-sessions">0</div>
                        <div class="summary-label">Total Sessions</div>
                    </div>
                    <div class="summary-card">
                        <div class="summary-value" id="report-days-worked">0</div>
                        <div class="summary-label">Days Worked</div>
                    </div>
                    <div class="summary-card">
                        <div class="summary-value" id="report-avg-hours">0:00</div>
                        <div class="summary-label">Avg Hours/Day</div>
                    </div>
                </div>
                
                <div class="report-period" id="report-period">
                    <!-- Period info will be inserted here -->
                </div>
                
                <div class="report-table-container">
                    <table class="report-table" id="report-table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Clock In</th>
                                <th>Clock Out</th>
                                <th>Duration</th>
                                <th>Description</th>
                            </tr>
                        </thead>
                        <tbody id="report-tbody">
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

<!-- Clock Out Modal -->
<div class="modal" id="clock-out-modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Clock Out</h3>
            <button class="modal-close" onclick="hideClockOutModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <form id="clock-out-form" class="modal-body">
            <div class="form-group">
                <label class="form-label" for="clock-out-time">
                    <i class="fas fa-clock"></i>
                    End Time
                </label>
                <input type="time" id="clock-out-time" class="form-control" required>
            </div>
            
            <div class="form-group">
                <label class="form-label" for="work-description">
                    <i class="fas fa-edit"></i>
                    What did you work on?
                </label>
                <textarea id="work-description" class="form-control" rows="4" 
                         placeholder="Describe what you accomplished during this work session..." required></textarea>
            </div>
        </form>
        
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" onclick="hideClockOutModal()">
                Cancel
            </button>
            <button type="submit" form="clock-out-form" class="btn btn-success">
                <i class="fas fa-check"></i>
                Complete Session
            </button>
        </div>
    </div>
</div>

<!-- Edit Log Modal -->
<div class="modal" id="edit-log-modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Edit Time Log</h3>
            <button class="modal-close" onclick="hideEditLogModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <form id="edit-log-form" class="modal-body">
            <input type="hidden" id="edit-log-id">
            
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="edit-clock-in-date">
                        <i class="fas fa-calendar"></i>
                        Date
                    </label>
                    <input type="date" id="edit-clock-in-date" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="edit-clock-in-time">
                        <i class="fas fa-clock"></i>
                        Clock In Time
                    </label>
                    <input type="time" id="edit-clock-in-time" class="form-control" required>
                </div>
            </div>
            
            <div class="form-group">
                <label class="form-label" for="edit-clock-out-time">
                    <i class="fas fa-clock"></i>
                    Clock Out Time
                </label>
                <input type="time" id="edit-clock-out-time" class="form-control" required>
            </div>
            
            <div class="form-group">
                <label class="form-label" for="edit-work-description">
                    <i class="fas fa-edit"></i>
                    Work Description
                </label>
                <textarea id="edit-work-description" class="form-control" rows="4" 
                         placeholder="Describe what you accomplished during this work session..." required></textarea>
            </div>
        </form>
        
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" onclick="hideEditLogModal()">
                Cancel
            </button>
            <button type="submit" form="edit-log-form" class="btn btn-primary">
                <i class="fas fa-save"></i>
                Save Changes
            </button>
        </div>
    </div>
</div>

<!-- Overlay -->
<div class="modal-overlay" id="modal-overlay"></div>

@push('styles')
<style>
    /* Navigation Tabs */
    .nav-tabs {
        display: flex;
        background: rgba(31, 41, 55, 0.3);
        border-radius: 12px;
        padding: 4px;
        margin-bottom: 24px;
        backdrop-filter: blur(10px);
        border: 1px solid rgba(75, 85, 99, 0.5);
    }

    .nav-tab {
        flex: 1;
        padding: 12px 16px;
        background: none;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        color: var(--text-secondary);
        font-weight: 500;
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        font-size: 14px;
    }

    .nav-tab:hover {
        color: var(--text-primary);
        background: rgba(75, 85, 99, 0.3);
        transform: translateY(-1px);
    }

    .nav-tab.active {
        background: var(--light-color);
        color: var(--primary-color);
        box-shadow: var(--shadow);
        font-weight: 600;
    }

    .nav-tab i {
        font-size: 16px;
    }

    /* Tab Content */
    .tab-content {
        display: none;
        padding: 24px;
    }

    .tab-content.active {
        display: block;
    }

    /* Dashboard Styles */
    .dashboard-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        grid-template-rows: auto auto;
        gap: 24px;
    }

    .stats-container {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 16px;
    }

    .stat-card {
        background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
        color: white;
        padding: 20px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        gap: 16px;
        box-shadow: var(--shadow);
    }

    .stat-icon {
        font-size: 2rem;
        opacity: 0.8;
    }

    .stat-value {
        font-size: 1.5rem;
        font-weight: 700;
        margin-bottom: 4px;
    }

    .stat-label {
        font-size: 0.875rem;
        opacity: 0.9;
    }

    .active-session-card {
        background: var(--light-color);
        padding: 24px;
        border-radius: 12px;
        box-shadow: var(--shadow);
        border: 1px solid var(--border-color);
    }

    .session-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }

    .session-header h3 {
        display: flex;
        align-items: center;
        gap: 8px;
        margin: 0;
    }

    .session-status {
        display: flex;
        align-items: center;
        gap: 8px;
        color: var(--success-color);
        font-weight: 500;
    }

    .status-indicator {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: var(--success-color);
        animation: pulse 2s infinite;
    }

    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.5; }
    }

    .session-time {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px;
        margin-bottom: 20px;
    }

    .session-time label {
        display: block;
        font-size: 0.875rem;
        color: var(--text-secondary);
        margin-bottom: 4px;
    }

    .session-time span {
        font-weight: 600;
        color: var(--text-primary);
    }

    .session-actions {
        display: flex;
        gap: 12px;
    }

    .quick-actions {
        background: var(--light-color);
        padding: 24px;
        border-radius: 12px;
        box-shadow: var(--shadow);
        border: 1px solid var(--border-color);
    }

    .section-title {
        font-size: 1.125rem;
        font-weight: 600;
        margin-bottom: 16px;
        color: var(--text-primary);
    }

    .action-buttons {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .action-btn {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px 16px;
        background: var(--secondary-color);
        border: 1px solid var(--border-color);
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.2s ease;
        text-align: left;
        font-weight: 500;
        color: var(--text-primary);
    }

    .action-btn:hover {
        background: var(--lighter-color);
        transform: translateY(-1px);
    }

    /* Tracker Styles */
    .tracker-container {
        max-width: 600px;
        margin: 0 auto;
    }

    .clock-section {
        background: var(--light-color);
        padding: 32px;
        border-radius: 16px;
        box-shadow: var(--shadow-lg);
        text-align: center;
    }

    .clock-header h2 {
        font-size: 1.5rem;
        font-weight: 600;
        margin-bottom: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 12px;
    }

    .clock-header p {
        color: var(--text-secondary);
        margin-bottom: 32px;
    }

    .clock-form {
        text-align: left;
    }

    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
        margin-bottom: 32px;
    }

    .btn-large {
        padding: 16px 32px;
        font-size: 1rem;
        font-weight: 600;
    }

    .active-session-display {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: white;
        padding: 32px;
        border-radius: 16px;
        text-align: center;
        box-shadow: var(--shadow-lg);
    }

    .session-visual {
        margin-bottom: 24px;
    }

    .session-avatar {
        width: 80px;
        height: 80px;
        background: rgba(255, 255, 255, 0.2);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 16px;
        font-size: 2rem;
    }

    .session-info h3 {
        font-size: 1.25rem;
        margin-bottom: 8px;
    }

    .session-meta {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        opacity: 0.9;
    }

    .session-separator {
        opacity: 0.6;
    }

    /* History Styles */
    .history-container {
        background: var(--light-color);
        border-radius: 12px;
        overflow: hidden;
    }

    .history-header {
        padding: 24px;
        border-bottom: 1px solid var(--border-color);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .history-header h2 {
        margin: 0;
        font-size: 1.25rem;
    }

    .history-table-container {
        overflow-x: auto;
    }

    .history-table {
        width: 100%;
        border-collapse: collapse;
    }

    .history-table th,
    .history-table td {
        padding: 16px;
        text-align: left;
        border-bottom: 1px solid var(--border-color);
    }

    .history-table th {
        background: var(--secondary-color);
        font-weight: 600;
        color: var(--text-primary);
        position: sticky;
        top: 0;
    }

    .history-table tbody tr:hover {
        background: var(--secondary-color);
    }

    /* Pagination Styles */
    .pagination-container {
        padding: 20px 24px;
        border-top: 1px solid var(--border-color);
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 16px;
    }

    .pagination-info {
        color: var(--text-secondary);
        font-size: 0.875rem;
    }

    .pagination-controls {
        display: flex;
        gap: 8px;
        align-items: center;
    }

    .pagination-btn {
        padding: 8px 12px;
        border: 1px solid var(--border-color);
        background: var(--secondary-color);
        color: var(--text-primary);
        border-radius: 6px;
        cursor: pointer;
        transition: all 0.2s ease;
        font-size: 0.875rem;
    }

    .pagination-btn:hover:not(:disabled) {
        background: var(--lighter-color);
        transform: translateY(-1px);
    }

    .pagination-btn:disabled {
        opacity: 0.5;
        cursor: not-allowed;
        transform: none;
    }

    .pagination-btn.active {
        background: var(--primary-color);
        color: white;
        border-color: var(--primary-color);
    }

    .page-size-selector {
        display: flex;
        align-items: center;
        gap: 8px;
        color: var(--text-secondary);
        font-size: 0.875rem;
    }

    .page-size-selector select {
        padding: 4px 8px;
        border: 1px solid var(--border-color);
        background: var(--secondary-color);
        color: var(--text-primary);
        border-radius: 4px;
        font-size: 0.875rem;
    }

    /* Reports Styles */
    .reports-container {
        background: var(--light-color);
        border-radius: 12px;
        overflow: hidden;
    }

    .report-generator {
        padding: 24px;
        border-bottom: 1px solid var(--border-color);
    }

    .report-generator h2 {
        margin-bottom: 8px;
        font-size: 1.25rem;
    }

    .report-generator p {
        color: var(--text-secondary);
        margin-bottom: 24px;
    }

    .report-form .form-row {
        margin-bottom: 24px;
    }

    .quick-select-buttons {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
        margin-top: 8px;
    }

    .quick-select-buttons .btn-sm {
        padding: 6px 12px;
        font-size: 0.875rem;
        border-radius: 6px;
        transition: all 0.2s ease;
    }

    .quick-select-buttons .btn-outline-secondary {
        border-color: var(--border-color);
        color: var(--text-secondary);
        background: transparent;
    }

    .quick-select-buttons .btn-outline-secondary:hover {
        background: var(--primary-color);
        border-color: var(--primary-color);
        color: white;
        transform: translateY(-1px);
    }

    .report-actions {
        display: flex;
        gap: 12px;
    }

    .report-results {
        padding: 24px;
    }

    .report-summary {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 16px;
        margin-bottom: 24px;
    }

    .summary-card {
        background: var(--secondary-color);
        padding: 20px;
        border-radius: 8px;
        text-align: center;
    }

    .summary-value {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--primary-color);
        margin-bottom: 4px;
    }

    .summary-label {
        font-size: 0.875rem;
        color: var(--text-secondary);
    }

    .report-period {
        background: var(--lighter-color);
        padding: 16px 20px;
        border-radius: 8px;
        margin-bottom: 24px;
        font-weight: 500;
        color: var(--text-primary);
    }

    .report-table-container {
        overflow-x: auto;
    }

    .report-table {
        width: 100%;
        border-collapse: collapse;
    }

    .report-table th,
    .report-table td {
        padding: 12px 16px;
        text-align: left;
        border-bottom: 1px solid var(--border-color);
    }

    .report-table th {
        background: var(--secondary-color);
        font-weight: 600;
        color: var(--text-primary);
    }

    /* Modal Styles */
    .modal {
        display: none;
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        z-index: 1001;
        max-width: 500px;
        width: 90%;
        max-height: 90vh;
        overflow-y: auto;
    }

    .modal.show {
        display: block;
    }

    .modal-content {
        background: var(--light-color);
        border-radius: 12px;
        box-shadow: var(--shadow-lg);
        overflow: hidden;
    }

    .modal-header {
        padding: 20px 24px;
        border-bottom: 1px solid var(--border-color);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .modal-header h3 {
        margin: 0;
        font-size: 1.125rem;
    }

    .modal-close {
        background: none;
        border: none;
        font-size: 1.25rem;
        cursor: pointer;
        color: var(--text-secondary);
        padding: 4px;
    }

    .modal-body {
        padding: 24px;
    }

    .modal-footer {
        padding: 20px 24px;
        border-top: 1px solid var(--border-color);
        display: flex;
        justify-content: flex-end;
        gap: 12px;
    }

    .modal-overlay {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.7);
        z-index: 1000;
    }

    .modal-overlay.show {
        display: block;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .dashboard-grid {
            grid-template-columns: 1fr;
        }

        .stats-container {
            grid-template-columns: 1fr;
        }

        .form-row {
            grid-template-columns: 1fr;
        }

        .report-summary {
            grid-template-columns: repeat(2, 1fr);
        }

        .nav-tabs {
            flex-wrap: wrap;
        }

        .nav-tab {
            flex: 0 0 calc(50% - 2px);
        }

        .session-actions,
        .report-actions {
            flex-direction: column;
        }

        .pagination-container {
            flex-direction: column;
            text-align: center;
        }

        .pagination-controls {
            flex-wrap: wrap;
            justify-content: center;
        }
    }

    /* Utility Classes */
    .text-success { color: var(--success-color); }
    .text-warning { color: var(--warning-color); }
    .text-danger { color: var(--danger-color); }

    .hidden { display: none !important; }
</style>
@endpush

@push('scripts')
<script>
    // Application State
    let currentActiveSession = null;
    let dashboardStats = {};
    let currentReportData = null;
    let currentPage = 1;
    let currentPerPage = 15;
    let totalPages = 1;

    // Initialize Application
    document.addEventListener('DOMContentLoaded', function() {
        // Ensure utils functions are available
        if (!window.utils.formatTimeForDisplay) {
            window.utils.formatTimeForDisplay = function(dateString) {
                return new Date(dateString).toLocaleTimeString('en-CA', {
                    timeZone: 'America/Toronto',
                    hour: '2-digit',
                    minute: '2-digit',
                    hour12: false
                });
            };
        }
        
        if (!window.utils.formatDateTimeForDisplay) {
            window.utils.formatDateTimeForDisplay = function(dateString) {
                const date = new Date(dateString);
                return {
                    date: date.toLocaleDateString('en-CA', {
                        timeZone: 'America/Toronto',
                        weekday: 'short',
                        year: 'numeric',
                        month: 'short',
                        day: 'numeric'
                    }),
                    time: date.toLocaleTimeString('en-CA', {
                        timeZone: 'America/Toronto',
                        hour: '2-digit',
                        minute: '2-digit',
                        hour12: false
                    })
                };
            };
        }
        
        // Update utils.formatDate to use Toronto timezone
        window.utils.formatDate = function(dateString) {
            return new Date(dateString).toLocaleDateString('en-CA', {
                timeZone: 'America/Toronto',
                weekday: 'short',
                year: 'numeric',
                month: 'short',
                day: 'numeric'
            });
        };
        
        // Update utils.getCurrentDateTime to use Toronto timezone
        window.utils.getCurrentDateTime = function() {
            const now = new Date();
            // Convert to Toronto timezone
            const torontoTime = new Date(now.toLocaleString("en-US", {timeZone: "America/Toronto"}));
            
            return {
                date: torontoTime.toISOString().split('T')[0],
                time: torontoTime.toTimeString().slice(0, 5)
            };
        };
        
        // Load week start preference
        const savedWeekStart = localStorage.getItem('weekStartDay') || 'monday';
        document.getElementById('week-start-day').value = savedWeekStart;
        
        // Save week start preference when changed
        document.getElementById('week-start-day').addEventListener('change', function() {
            localStorage.setItem('weekStartDay', this.value);
        });
        
        initializeApp();
        setupEventListeners();
        loadDashboardStats();
        checkActiveSession();
    });

    function initializeApp() {
        // Set current date and time
        const now = utils.getCurrentDateTime();
        document.getElementById('clock-in-date').value = now.date;
        document.getElementById('clock-in-time').value = now.time;
        document.getElementById('report-end-date').value = now.date;
    }

    function setupEventListeners() {
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
    }

    // Tab Management
    function showTab(tabName) {
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
            currentPage = 1; // Reset to first page
            loadHistory();
        } else if (tabName === 'dashboard') {
            loadDashboardStats();
        }
    }

    // Dashboard Functions
    async function loadDashboardStats() {
        try {
            const response = await window.api.request('/api/timesheet/dashboard-stats');
            dashboardStats = response.stats;
            updateDashboardDisplay();
        } catch (error) {
            console.error('Failed to load dashboard stats:', error);
        }
    }

    function updateDashboardDisplay() {
        if (dashboardStats.today) {
            document.getElementById('today-hours').textContent = utils.formatTime(Math.round(dashboardStats.today.hours * 60));
        }
        if (dashboardStats.this_week) {
            document.getElementById('week-hours').textContent = utils.formatTime(Math.round(dashboardStats.this_week.hours * 60));
        }
        if (dashboardStats.this_month) {
            document.getElementById('month-hours').textContent = utils.formatTime(Math.round(dashboardStats.this_month.hours * 60));
            document.getElementById('total-sessions').textContent = dashboardStats.this_month.sessions;
        }
    }

    // Active Session Management
    async function checkActiveSession() {
        try {
            const response = await window.api.request('/api/timesheet/active-session');
            if (response.success) {
                currentActiveSession = response.session;
                showActiveSessionUI();
                startSessionTimer();
            } else {
                hideActiveSessionUI();
            }
        } catch (error) {
            hideActiveSessionUI();
        }
    }

    function showActiveSessionUI() {
        // Update dashboard
        const sessionCard = document.getElementById('active-session-card');
        sessionCard.classList.remove('hidden');
        
        // Update tracker
        document.getElementById('clock-in-section').classList.add('hidden');
        document.getElementById('active-session-display').classList.remove('hidden');
        
        updateActiveSessionDisplay();
    }

    function hideActiveSessionUI() {
        document.getElementById('active-session-card').classList.add('hidden');
        document.getElementById('clock-in-section').classList.remove('hidden');
        document.getElementById('active-session-display').classList.add('hidden');
        stopSessionTimer();
    }

    function updateActiveSessionDisplay() {
        if (!currentActiveSession) return;

        const startTime = new Date(currentActiveSession.clock_in);
        const startDisplay = utils.formatDateTimeForDisplay(currentActiveSession.clock_in);
        
        document.getElementById('session-start-display').textContent = `${startDisplay.date} at ${startDisplay.time}`;
        document.getElementById('current-session-start').textContent = `Started at ${startDisplay.time}`;
    }

    // Session Timer
    let timerInterval = null;

    function startSessionTimer() {
        if (timerInterval) clearInterval(timerInterval);
        
        timerInterval = setInterval(() => {
            if (!currentActiveSession) return;
            
            const startTime = new Date(currentActiveSession.clock_in);
            const now = new Date();
            const diffMinutes = Math.floor((now - startTime) / (1000 * 60));
            
            const formattedTime = utils.formatTime(diffMinutes);
            document.getElementById('session-duration-display').textContent = formattedTime;
            document.getElementById('current-session-duration').textContent = 'Duration: ' + formattedTime;
        }, 1000);
    }

    function stopSessionTimer() {
        if (timerInterval) {
            clearInterval(timerInterval);
            timerInterval = null;
        }
    }

    // Clock In/Out Functions
    async function clockIn() {
        const date = document.getElementById('clock-in-date').value;
        const time = document.getElementById('clock-in-time').value;

        if (!date || !time) {
            window.notify.error('Please fill in both date and time');
            return;
        }

        try {
            const response = await window.api.request('/api/timesheet/clock-in', {
                method: 'POST',
                body: JSON.stringify({
                    date: date,
                    time: time
                })
            });

            if (response.success) {
                currentActiveSession = response.session;
                showActiveSessionUI();
                startSessionTimer();
                loadDashboardStats();
                window.notify.success('Successfully clocked in!');
            } else {
                if (response.active_session) {
                    currentActiveSession = response.active_session;
                    showActiveSessionUI();
                    startSessionTimer();
                }
                window.notify.error(response.message);
            }
        } catch (error) {
            window.notify.error('Failed to clock in: ' + error.message);
        }
    }

    async function quickClockIn() {
        const now = utils.getCurrentDateTime();
        document.getElementById('clock-in-date').value = now.date;
        document.getElementById('clock-in-time').value = now.time;
        await clockIn();
    }

    function showClockOutModal() {
        if (!currentActiveSession) {
            window.notify.error('No active session found');
            return;
        }

        const now = utils.getCurrentDateTime();
        document.getElementById('clock-out-time').value = now.time;
        
        document.getElementById('clock-out-modal').classList.add('show');
        document.getElementById('modal-overlay').classList.add('show');
        document.body.style.overflow = 'hidden';
    }

    function hideClockOutModal() {
        document.getElementById('clock-out-modal').classList.remove('show');
        document.getElementById('modal-overlay').classList.remove('show');
        document.body.style.overflow = 'auto';
        
        // Clear form
        document.getElementById('clock-out-form').reset();
    }

    async function clockOut() {
        const time = document.getElementById('clock-out-time').value;
        const description = document.getElementById('work-description').value.trim();

        if (!time || !description) {
            window.notify.error('Please fill in end time and work description');
            return;
        }

        try {
            const response = await window.api.request('/api/timesheet/clock-out', {
                method: 'POST',
                body: JSON.stringify({
                    session_id: currentActiveSession.session_id,
                    time: time,
                    work_description: description
                })
            });

            if (response.success) {
                currentActiveSession = null;
                hideActiveSessionUI();
                hideClockOutModal();
                loadDashboardStats();
                window.notify.success(`Successfully clocked out! Duration: ${utils.formatTime(response.session.total_minutes)}`);
            } else {
                window.notify.error(response.message);
            }
        } catch (error) {
            window.notify.error('Failed to clock out: ' + error.message);
        }
    }

    async function cancelActiveSession() {
        if (!confirm('Are you sure you want to cancel the current session? This will delete the session data.')) {
            return;
        }

        try {
            const response = await window.api.request('/api/timesheet/cancel-session', {
                method: 'DELETE'
            });

            if (response.success) {
                currentActiveSession = null;
                hideActiveSessionUI();
                loadDashboardStats();
                window.notify.success('Session cancelled successfully');
            } else {
                window.notify.error(response.message);
            }
        } catch (error) {
            window.notify.error('Failed to cancel session: ' + error.message);
        }
    }

    // History Functions with Pagination
    async function loadHistory(page = 1, perPage = null) {
        const tbody = document.getElementById('history-tbody');
        
        try {
            currentPage = page;
            if (perPage) currentPerPage = perPage;
            
            tbody.innerHTML = '<tr><td colspan="7" class="text-center"><div class="loading"><div class="spinner"></div>Loading history...</div></td></tr>';
            
            const response = await window.api.request(`/api/timesheet/history?page=${currentPage}&per_page=${currentPerPage}`);
            
            if (response.success && response.data.items.length > 0) {
                tbody.innerHTML = '';
                
                response.data.items.forEach(log => {
                    const row = tbody.insertRow();
                    
                    try {
                        // Format the data properly using Toronto timezone
                        const clockInDisplay = window.utils.formatTimeForDisplay(log.clock_in);
                        const clockOutDisplay = log.clock_out ? window.utils.formatTimeForDisplay(log.clock_out) : '-';
                        const formattedDuration = log.formatted_duration || (log.total_minutes ? window.utils.formatTime(log.total_minutes) : '-');
                        const workDescription = log.work_description || '-';
                        
                        row.innerHTML = `
                            <td>${window.utils.formatDate(log.clock_in)}</td>
                            <td>${clockInDisplay}</td>
                            <td>${clockOutDisplay}</td>
                            <td>${formattedDuration}</td>
                            <td>${workDescription}</td>
                            <td>
                                <button class="btn btn-primary" onclick="editLog(${log.id})" style="padding: 6px 12px; font-size: 12px; margin-right: 8px;">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-danger" onclick="deleteLog(${log.id})" style="padding: 6px 12px; font-size: 12px;">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        `;
                    } catch (error) {
                        // Fallback formatting
                        const clockInTime = new Date(log.clock_in).toLocaleTimeString('en-US', {hour: '2-digit', minute:'2-digit', hour12: false});
                        const clockOutTime = log.clock_out ? new Date(log.clock_out).toLocaleTimeString('en-US', {hour: '2-digit', minute:'2-digit', hour12: false}) : '-';
                        const formattedDuration = log.total_minutes ? window.utils.formatTime(log.total_minutes) : '-';
                        
                        row.innerHTML = `
                            <td>${new Date(log.clock_in).toLocaleDateString()}</td>
                            <td>${clockInTime}</td>
                            <td>${clockOutTime}</td>
                            <td>${formattedDuration}</td>
                            <td>${log.work_description || '-'}</td>
                            <td>${log.project_name || '-'}</td>
                            <td>
                                <button class="btn btn-primary" onclick="editLog(${log.id})" style="padding: 6px 12px; font-size: 12px; margin-right: 8px;">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-danger" onclick="deleteLog(${log.id})" style="padding: 6px 12px; font-size: 12px;">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        `;
                    }
                });
                
                // Update pagination
                updatePagination(response.data.pagination);
            } else {
                tbody.innerHTML = '<tr><td colspan="7" class="text-center">No work history found. Start tracking your time!</td></tr>';
                updatePagination(null);
            }
        } catch (error) {
            tbody.innerHTML = '<tr><td colspan="7" class="text-center text-danger">Failed to load history. Please try again.</td></tr>';
            window.notify.error('Failed to load history: ' + error.message);
            updatePagination(null);
        }
    }

    function updatePagination(pagination) {
        const container = document.getElementById('pagination-container');
        
        if (!pagination || pagination.total === 0) {
            container.innerHTML = '';
            return;
        }
        
        totalPages = pagination.last_page;
        currentPage = pagination.current_page;
        
        const startItem = pagination.from || 0;
        const endItem = pagination.to || 0;
        const total = pagination.total;
        
        container.innerHTML = `
            <div class="pagination-info">
                Showing ${startItem} to ${endItem} of ${total} entries
            </div>
            <div class="pagination-controls">
                <button class="pagination-btn" onclick="loadHistory(1)" ${currentPage === 1 ? 'disabled' : ''}>
                    <i class="fas fa-angle-double-left"></i>
                </button>
                <button class="pagination-btn" onclick="loadHistory(${currentPage - 1})" ${currentPage === 1 ? 'disabled' : ''}>
                    <i class="fas fa-angle-left"></i>
                </button>
                ${generatePageNumbers()}
                <button class="pagination-btn" onclick="loadHistory(${currentPage + 1})" ${currentPage === totalPages ? 'disabled' : ''}>
                    <i class="fas fa-angle-right"></i>
                </button>
                <button class="pagination-btn" onclick="loadHistory(${totalPages})" ${currentPage === totalPages ? 'disabled' : ''}>
                    <i class="fas fa-angle-double-right"></i>
                </button>
            </div>
            <div class="page-size-selector">
                <span>Show:</span>
                <select onchange="changePageSize(this.value)">
                    <option value="10" ${currentPerPage === 10 ? 'selected' : ''}>10</option>
                    <option value="15" ${currentPerPage === 15 ? 'selected' : ''}>15</option>
                    <option value="25" ${currentPerPage === 25 ? 'selected' : ''}>25</option>
                    <option value="50" ${currentPerPage === 50 ? 'selected' : ''}>50</option>
                </select>
                <span>entries</span>
            </div>
        `;
    }

    function generatePageNumbers() {
        let pages = '';
        const maxVisible = 5;
        let start = Math.max(1, currentPage - Math.floor(maxVisible / 2));
        let end = Math.min(totalPages, start + maxVisible - 1);
        
        if (end - start + 1 < maxVisible) {
            start = Math.max(1, end - maxVisible + 1);
        }
        
        for (let i = start; i <= end; i++) {
            pages += `<button class="pagination-btn ${i === currentPage ? 'active' : ''}" onclick="loadHistory(${i})">${i}</button>`;
        }
        
        return pages;
    }

    function changePageSize(newSize) {
        currentPerPage = parseInt(newSize);
        currentPage = 1; // Reset to first page
        loadHistory(1, currentPerPage);
    }

    // Edit Log Functions
    async function editLog(id) {
        try {
            const response = await window.api.request(`/api/timesheet/logs/${id}`);
            
            if (response.success) {
                const log = response.data;
                
                // Populate the edit form
                document.getElementById('edit-log-id').value = log.id;
                
                // Parse the clock_in datetime
                const clockInDate = new Date(log.clock_in);
                document.getElementById('edit-clock-in-date').value = clockInDate.toISOString().split('T')[0];
                document.getElementById('edit-clock-in-time').value = clockInDate.toTimeString().slice(0, 5);
                
                // Parse the clock_out datetime if it exists
                if (log.clock_out) {
                    const clockOutDate = new Date(log.clock_out);
                    document.getElementById('edit-clock-out-time').value = clockOutDate.toTimeString().slice(0, 5);
                } else {
                    document.getElementById('edit-clock-out-time').value = '';
                }
                
                document.getElementById('edit-work-description').value = log.work_description || '';
                
                // Show the modal
                showEditLogModal();
            } else {
                window.notify.error(response.message);
            }
        } catch (error) {
            window.notify.error('Failed to load entry details: ' + error.message);
        }
    }

    function showEditLogModal() {
        document.getElementById('edit-log-modal').classList.add('show');
        document.getElementById('modal-overlay').classList.add('show');
        document.body.style.overflow = 'hidden';
    }

    function hideEditLogModal() {
        document.getElementById('edit-log-modal').classList.remove('show');
        document.getElementById('modal-overlay').classList.remove('show');
        document.body.style.overflow = 'auto';
        
        // Clear form
        document.getElementById('edit-log-form').reset();
    }

    async function updateLog() {
        const logId = document.getElementById('edit-log-id').value;
        const date = document.getElementById('edit-clock-in-date').value;
        const clockInTime = document.getElementById('edit-clock-in-time').value;
        const clockOutTime = document.getElementById('edit-clock-out-time').value;
        const description = document.getElementById('edit-work-description').value.trim();

        if (!date || !clockInTime || !clockOutTime || !description) {
            window.notify.error('Please fill in all required fields');
            return;
        }

        try {
            const response = await window.api.request(`/api/timesheet/logs/${logId}`, {
                method: 'PUT',
                body: JSON.stringify({
                    date: date,
                    clock_in_time: clockInTime,
                    clock_out_time: clockOutTime,
                    work_description: description
                })
            });

            if (response.success) {
                hideEditLogModal();
                loadHistory(currentPage); // Reload current page
                loadDashboardStats(); // Refresh dashboard stats
                window.notify.success('Entry updated successfully');
            } else {
                window.notify.error(response.message);
            }
        } catch (error) {
            window.notify.error('Failed to update entry: ' + error.message);
        }
    }

    async function deleteLog(id) {
        if (!confirm('Are you sure you want to delete this entry?')) {
            return;
        }

        try {
            const response = await window.api.request(`/api/timesheet/logs/${id}`, {
                method: 'DELETE'
            });

            if (response.success) {
                loadHistory(currentPage); // Reload current page
                loadDashboardStats(); // Refresh dashboard stats
                window.notify.success('Entry deleted successfully');
            } else {
                window.notify.error(response.message);
            }
        } catch (error) {
            window.notify.error('Failed to delete entry: ' + error.message);
        }
    }

    // Report Functions
    function getWeekStart(date, weekStartDay) {
        const d = new Date(date);
        const day = d.getDay();
        const diff = weekStartDay === 'sunday' ? day : (day === 0 ? 6 : day - 1);
        d.setDate(d.getDate() - diff);
        return d;
    }

    function getWeekEnd(date, weekStartDay) {
        const weekStart = getWeekStart(date, weekStartDay);
        const weekEnd = new Date(weekStart);
        weekEnd.setDate(weekStart.getDate() + 6);
        return weekEnd;
    }

    function formatDateForInput(date) {
        return date.toISOString().split('T')[0];
    }

    function selectThisWeek() {
        const weekStartDay = document.getElementById('week-start-day').value;
        const today = new Date();
        const weekStart = getWeekStart(today, weekStartDay);
        const weekEnd = getWeekEnd(today, weekStartDay);
        
        document.getElementById('report-start-date').value = formatDateForInput(weekStart);
        document.getElementById('report-end-date').value = formatDateForInput(weekEnd);
    }

    function selectLastWeek() {
        const weekStartDay = document.getElementById('week-start-day').value;
        const today = new Date();
        const lastWeek = new Date(today);
        lastWeek.setDate(today.getDate() - 7);
        const weekStart = getWeekStart(lastWeek, weekStartDay);
        const weekEnd = getWeekEnd(lastWeek, weekStartDay);
        
        document.getElementById('report-start-date').value = formatDateForInput(weekStart);
        document.getElementById('report-end-date').value = formatDateForInput(weekEnd);
    }

    function selectThisMonth() {
        const today = new Date();
        const monthStart = new Date(today.getFullYear(), today.getMonth(), 1);
        const monthEnd = new Date(today.getFullYear(), today.getMonth() + 1, 0);
        
        document.getElementById('report-start-date').value = formatDateForInput(monthStart);
        document.getElementById('report-end-date').value = formatDateForInput(monthEnd);
    }

    function selectLastMonth() {
        const today = new Date();
        const lastMonth = new Date(today.getFullYear(), today.getMonth() - 1, 1);
        const lastMonthEnd = new Date(today.getFullYear(), today.getMonth(), 0);
        
        document.getElementById('report-start-date').value = formatDateForInput(lastMonth);
        document.getElementById('report-end-date').value = formatDateForInput(lastMonthEnd);
    }

    async function generateReport() {
        const startDate = document.getElementById('report-start-date').value;
        const endDate = document.getElementById('report-end-date').value;
        
        if (!startDate || !endDate) {
            window.notify.error('Please select both start and end dates');
            return;
        }

        if (new Date(startDate) > new Date(endDate)) {
            window.notify.error('Start date cannot be after end date');
            return;
        }

        try {
            const response = await window.api.request(`/api/timesheet/report?start_date=${startDate}&end_date=${endDate}`);

            if (response.success && response.data.logs.length > 0) {
                currentReportData = response.data;
                displayReport();
                document.getElementById('export-btn').disabled = false;
                window.notify.success('Report generated successfully');
            } else {
                document.getElementById('report-results').classList.add('hidden');
                document.getElementById('export-btn').disabled = true;
                window.notify.error('No data found for the selected period');
            }
        } catch (error) {
            window.notify.error('Failed to generate report: ' + error.message);
        }
    }

    function displayReport() {
        if (!currentReportData) return;

        const { summary, period, logs } = currentReportData;

        // Update summary
        document.getElementById('report-total-hours').textContent = utils.formatTime(Math.round(summary.total_hours * 60));
        document.getElementById('report-total-sessions').textContent = summary.total_sessions;
        document.getElementById('report-days-worked').textContent = summary.days_worked;
        document.getElementById('report-avg-hours').textContent = utils.formatTime(Math.round(summary.average_hours_per_day * 60));

        // Update period with week context
        const startDate = new Date(period.start_date);
        const endDate = new Date(period.end_date);
        const weekStartDay = document.getElementById('week-start-day').value;
        
        let periodContext = '';
        const daysDiff = Math.ceil((endDate - startDate) / (1000 * 60 * 60 * 24)) + 1;
        
        if (daysDiff === 7) {
            const weekStart = getWeekStart(startDate, weekStartDay);
            const weekEnd = getWeekEnd(startDate, weekStartDay);
            
            if (startDate.getTime() === weekStart.getTime() && endDate.getTime() === weekEnd.getTime()) {
                const today = new Date();
                const thisWeekStart = getWeekStart(today, weekStartDay);
                const lastWeekStart = new Date(thisWeekStart);
                lastWeekStart.setDate(thisWeekStart.getDate() - 7);
                
                if (startDate.getTime() === thisWeekStart.getTime()) {
                    periodContext = ' (This Week)';
                } else if (startDate.getTime() === lastWeekStart.getTime()) {
                    periodContext = ' (Last Week)';
                } else {
                    periodContext = ` (Week starting ${weekStartDay === 'sunday' ? 'Sunday' : 'Monday'})`;
                }
            }
        }
        
        document.getElementById('report-period').innerHTML = `
            <strong>Report Period:</strong> ${utils.formatDate(period.start_date)} - ${utils.formatDate(period.end_date)}${periodContext}
            <br><small class="text-muted">Week starts on ${weekStartDay.charAt(0).toUpperCase() + weekStartDay.slice(1)}</small>
        `;

        // Update table
        const tbody = document.getElementById('report-tbody');
        tbody.innerHTML = '';

        logs.forEach(log => {
            const row = tbody.insertRow();
            
            try {
                // Format the data properly using Toronto timezone
                const clockInDisplay = window.utils.formatTimeForDisplay(log.clock_in);
                const clockOutDisplay = log.clock_out ? window.utils.formatTimeForDisplay(log.clock_out) : '-';
                const formattedDuration = log.formatted_duration || (log.total_minutes ? window.utils.formatTime(log.total_minutes) : '-');
                const workDescription = log.work_description || '-';
                
                row.innerHTML = `
                    <td>${window.utils.formatDate(log.clock_in)}</td>
                    <td>${clockInDisplay}</td>
                    <td>${clockOutDisplay}</td>
                    <td>${formattedDuration}</td>
                    <td>${workDescription}</td>
                `;
            } catch (error) {
                // Fallback formatting
                const clockInTime = new Date(log.clock_in).toLocaleTimeString('en-US', {hour: '2-digit', minute:'2-digit', hour12: false});
                const clockOutTime = log.clock_out ? new Date(log.clock_out).toLocaleTimeString('en-US', {hour: '2-digit', minute:'2-digit', hour12: false}) : '-';
                const formattedDuration = log.total_minutes ? window.utils.formatTime(log.total_minutes) : '-';
                
                row.innerHTML = `
                    <td>${new Date(log.clock_in).toLocaleDateString()}</td>
                    <td>${clockInTime}</td>
                    <td>${clockOutTime}</td>
                    <td>${formattedDuration}</td>
                    <td>${log.work_description || '-'}</td>
                `;
            }
        });

        document.getElementById('report-results').classList.remove('hidden');
    }

    async function exportExcel() {
        if (!currentReportData) {
            window.notify.error('Please generate a report first');
            return;
        }

        const startDate = document.getElementById('report-start-date').value;
        const endDate = document.getElementById('report-end-date').value;

        try {
            const url = `/api/timesheet/export-excel?start_date=${startDate}&end_date=${endDate}`;
            const link = document.createElement('a');
            link.href = url;
            link.download = `Timesheet_${currentReportData.period.start_date}_to_${currentReportData.period.end_date}.xlsx`;
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            
            window.notify.success('Excel file downloaded successfully!');
        } catch (error) {
            window.notify.error('Failed to export Excel: ' + error.message);
        }
    }
</script>
@endpush