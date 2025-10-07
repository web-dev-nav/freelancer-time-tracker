{{-- Reports Tab Component --}}
{{-- Timesheet report generator with date range selection and export functionality --}}
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

        <!-- Report Results -->
        <div class="report-results" id="report-results">
            <!-- Summary Cards -->
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

            <!-- Report Period Info -->
            <div class="report-period" id="report-period">
                <!-- Period info will be inserted here dynamically by JavaScript -->
            </div>

            <!-- Report Table -->
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
                        <!-- Report data will be inserted here dynamically by JavaScript -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
