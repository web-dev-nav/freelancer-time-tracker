<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Time Tracking Tool</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            overflow: hidden;
        }

        .header {
            background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
            color: white;
            padding: 25px;
            text-align: center;
        }

        .header h1 {
            font-size: 2rem;
            margin-bottom: 10px;
        }

        .tabs {
            display: flex;
            background: #f8f9fa;
            border-bottom: 2px solid #e9ecef;
        }

        .tab {
            flex: 1;
            padding: 15px;
            background: none;
            border: none;
            cursor: pointer;
            font-size: 16px;
            font-weight: 600;
            color: #6c757d;
            transition: all 0.3s ease;
        }

        .tab.active {
            background: white;
            color: #2c3e50;
            border-bottom: 3px solid #667eea;
        }

        .tab:hover {
            background: #e9ecef;
        }

        .tab-content {
            padding: 30px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #2c3e50;
        }

        input, textarea, select {
            width: 100%;
            padding: 12px;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            font-size: 16px;
            transition: border-color 0.3s ease;
        }

        input:focus, textarea:focus, select:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        textarea {
            resize: vertical;
            min-height: 80px;
        }

        .btn {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 12px 25px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 600;
            transition: transform 0.2s ease;
            margin-right: 10px;
            margin-bottom: 10px;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.3);
        }

        .btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }

        .btn-secondary {
            background: linear-gradient(135deg, #6c757d 0%, #495057 100%);
        }

        .btn-danger {
            background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
        }

        .btn-success {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
        }

        .current-session {
            background: linear-gradient(135deg, #e3f2fd 0%, #f3e5f5 100%);
            border: 2px solid #667eea;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 25px;
        }

        .session-info {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 15px;
        }

        .info-item {
            background: white;
            padding: 12px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        .info-label {
            font-size: 12px;
            color: #6c757d;
            text-transform: uppercase;
            font-weight: 600;
            margin-bottom: 5px;
        }

        .info-value {
            font-size: 16px;
            font-weight: 600;
            color: #2c3e50;
        }

        .entries-table {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            margin-top: 25px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #e9ecef;
        }

        th {
            background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
            color: white;
            font-weight: 600;
            position: sticky;
            top: 0;
        }

        tr:hover {
            background: #f8f9fa;
        }

        .duration {
            font-weight: 600;
            color: #667eea;
        }

        .period-selector {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 12px;
            margin-bottom: 25px;
            display: grid;
            grid-template-columns: 1fr auto auto;
            gap: 15px;
            align-items: end;
        }

        .period-summary {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
            padding: 20px;
            border-radius: 12px;
            margin-bottom: 25px;
            text-align: center;
        }

        .summary-hours {
            font-size: 3rem;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .hidden {
            display: none;
        }

        .timer {
            font-size: 1.5rem;
            font-weight: bold;
            color: #667eea;
        }

        .loading {
            text-align: center;
            padding: 20px;
            color: #6c757d;
        }

        .error {
            background: #f8d7da;
            color: #721c24;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            border: 1px solid #f5c6cb;
        }

        .success {
            background: #d4edda;
            color: #155724;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            border: 1px solid #c3e6cb;
        }

        @media (max-width: 768px) {
            .tabs {
                flex-direction: column;
            }
            
            .period-selector {
                grid-template-columns: 1fr;
            }
            
            .session-info {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>⏰ Professional Time Tracker</h1>
            <p>Track your work sessions with Laravel backend and database storage</p>
        </div>

        <div class="tabs">
            <button class="tab active" onclick="showTab('track')">Track Time</button>
            <button class="tab" onclick="showTab('history')">History</button>
            <button class="tab" onclick="showTab('reports')">Reports</button>
        </div>

        <!-- Track Time Tab -->
        <div id="track-tab" class="tab-content">
            <div id="current-session" class="current-session hidden">
                <h3>🚀 Current Work Session</h3>
                <div class="session-info">
                    <div class="info-item">
                        <div class="info-label">Started At</div>
                        <div class="info-value" id="session-start"></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Task</div>
                        <div class="info-value">Working... (will describe when finished)</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Duration</div>
                        <div class="info-value timer" id="session-timer">00:00:00</div>
                    </div>
                </div>
                <div class="form-group">
                    <label for="end-time">End Time:</label>
                    <input type="time" id="end-time" required>
                </div>
                <div class="form-group">
                    <label for="task-description">What did you work on during this session?</label>
                    <textarea id="task-description" placeholder="Describe what you accomplished in this work session..." required></textarea>
                </div>
                <button class="btn btn-danger" onclick="stopSession()">⏹️ Stop & Save Session</button>
            </div>

            <div id="start-form">
                <h3>📝 Start New Work Session</h3>
                <div class="form-group">
                    <label for="start-date">Date:</label>
                    <input type="date" id="start-date" required>
                </div>
                <div class="form-group">
                    <label for="start-time">Start Time:</label>
                    <input type="time" id="start-time" required>
                </div>
                <button class="btn" onclick="startSession()">▶️ Start Session</button>
                <p style="color: #6c757d; font-style: italic; margin-top: 15px;">💡 You'll describe what you worked on when you finish the session</p>
            </div>
        </div>

        <!-- History Tab -->
        <div id="history-tab" class="tab-content hidden">
            <h3>📊 Work History</h3>
            <div id="history-loading" class="loading hidden">Loading entries...</div>
            <div class="entries-table">
                <table>
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Start Time</th>
                            <th>End Time</th>
                            <th>Task/Activity</th>
                            <th>Duration (hrs)</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="history-tbody">
                        <tr>
                            <td colspan="6" style="text-align: center; color: #6c757d; font-style: italic;">Loading entries...</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Reports Tab -->
        <div id="reports-tab" class="tab-content hidden">
            <h3>📈 Timesheet Reports</h3>
            
            <div class="period-selector">
                <div class="form-group">
                    <label for="report-end-date">Select Report End Date (e.g., June 23, 2025):</label>
                    <input type="date" id="report-end-date">
                </div>
                <button class="btn" onclick="generateReport()">Generate 2-Week Report</button>
                <button class="btn btn-success" onclick="exportToExcel()" id="export-btn" disabled>📊 Export to Excel</button>
            </div>

            <div id="report-summary" class="period-summary hidden">
                <div class="summary-hours" id="total-hours">0.00</div>
                <div>Total Hours for Period</div>
                <div id="period-range"></div>
            </div>

            <div id="report-table" class="entries-table hidden">
                <table>
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Start Time</th>
                            <th>End Time</th>
                            <th>Task/Activity</th>
                            <th>Duration (hrs)</th>
                        </tr>
                    </thead>
                    <tbody id="report-tbody">
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        // Get CSRF token for Laravel
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        // Data storage
        let workEntries = [];
        let currentSession = JSON.parse(localStorage.getItem('currentSession') || 'null');
        let timerInterval = null;
        let currentReportData = null;

        // Initialize the app
        function init() {
            setCurrentDateTime();
            setCurrentDate('report-end-date');
            loadHistory();
            
            if (currentSession) {
                resumeSession();
            }
        }

        // API Helper Functions using Laravel web routes
        async function makeRequest(url, options = {}) {
            try {
                const response = await fetch(url, {
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'X-Requested-With': 'XMLHttpRequest',
                        ...options.headers
                    },
                    ...options
                });

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                return await response.json();
            } catch (error) {
                console.error('Request failed:', error);
                showError('Connection error. Please check your internet connection and try again.');
                throw error;
            }
        }

        // UI Helper Functions
        function showError(message) {
            const errorDiv = document.createElement('div');
            errorDiv.className = 'error';
            errorDiv.textContent = message;
            document.querySelector('.tab-content:not(.hidden)').insertBefore(errorDiv, document.querySelector('.tab-content:not(.hidden)').firstChild);
            setTimeout(() => errorDiv.remove(), 5000);
        }

        function showSuccess(message) {
            const successDiv = document.createElement('div');
            successDiv.className = 'success';
            successDiv.textContent = message;
            document.querySelector('.tab-content:not(.hidden)').insertBefore(successDiv, document.querySelector('.tab-content:not(.hidden)').firstChild);
            setTimeout(() => successDiv.remove(), 3000);
        }

        // Tab management
        function showTab(tabName) {
            document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
            document.querySelectorAll('.tab-content').forEach(tc => tc.classList.add('hidden'));
            
            document.querySelector(`[onclick="showTab('${tabName}')"]`).classList.add('active');
            document.getElementById(`${tabName}-tab`).classList.remove('hidden');

            if (tabName === 'history') {
                loadHistory();
            }
        }

        // Set current date and time
        function setCurrentDateTime() {
            const now = new Date();
            document.getElementById('start-date').value = now.toISOString().split('T')[0];
            document.getElementById('start-time').value = now.toTimeString().slice(0, 5);
        }

        function setCurrentDate(elementId) {
            const now = new Date();
            document.getElementById(elementId).value = now.toISOString().split('T')[0];
        }

        // Session management
        function startSession() {
            const date = document.getElementById('start-date').value;
            const time = document.getElementById('start-time').value;

            if (!date || !time) {
                showError('Please fill in date and time');
                return;
            }

            currentSession = {
                date: date,
                startTime: time,
                startTimestamp: new Date(`${date}T${time}`).getTime()
            };

            localStorage.setItem('currentSession', JSON.stringify(currentSession));
            
            document.getElementById('start-form').classList.add('hidden');
            document.getElementById('current-session').classList.remove('hidden');
            
            updateSessionDisplay();
            setCurrentEndTime();
            startTimer();
        }

        async function stopSession() {
            if (!currentSession) return;

            const endTime = document.getElementById('end-time').value;
            const task = document.getElementById('task-description').value.trim();
            
            if (!endTime || !task) {
                showError('Please fill in end time and describe what you worked on');
                return;
            }
            
            const startTimestamp = new Date(`${currentSession.date}T${currentSession.startTime}`).getTime();
            const endTimestamp = new Date(`${currentSession.date}T${endTime}`).getTime();
            
            if (endTimestamp <= startTimestamp) {
                showError('End time must be after start time');
                return;
            }

            try {
                const response = await makeRequest('{{ route("timesheet.store") }}', {
                    method: 'POST',
                    body: JSON.stringify({
                        work_date: currentSession.date,
                        start_time: currentSession.startTime,
                        end_time: endTime,
                        task_description: task
                    })
                });

                if (response.success) {
                    localStorage.removeItem('currentSession');
                    currentSession = null;
                    stopTimer();
                    
                    document.getElementById('current-session').classList.add('hidden');
                    document.getElementById('start-form').classList.remove('hidden');
                    
                    // Clear form
                    document.getElementById('task-description').value = '';
                    
                    setCurrentDateTime();
                    loadHistory();
                    
                    showSuccess(`Session saved! Duration: ${response.data.duration} hours`);
                } else {
                    showError('Failed to save session');
                }
            } catch (error) {
                showError('Failed to save session. Please try again.');
            }
        }

        function resumeSession() {
            document.getElementById('start-form').classList.add('hidden');
            document.getElementById('current-session').classList.remove('hidden');
            updateSessionDisplay();
            setCurrentEndTime();
            startTimer();
        }

        function updateSessionDisplay() {
            if (!currentSession) return;
            
            document.getElementById('session-start').textContent = 
                `${currentSession.date} at ${currentSession.startTime}`;
        }

        // Set current end time
        function setCurrentEndTime() {
            const now = new Date();
            document.getElementById('end-time').value = now.toTimeString().slice(0, 5);
        }

        // Timer functions
        function startTimer() {
            if (timerInterval) clearInterval(timerInterval);
            
            timerInterval = setInterval(() => {
                if (!currentSession) return;
                
                const now = Date.now();
                const elapsed = now - currentSession.startTimestamp;
                const hours = Math.floor(elapsed / (1000 * 60 * 60));
                const minutes = Math.floor((elapsed % (1000 * 60 * 60)) / (1000 * 60));
                const seconds = Math.floor((elapsed % (1000 * 60)) / 1000);
                
                document.getElementById('session-timer').textContent = 
                    `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
            }, 1000);
        }

        function stopTimer() {
            if (timerInterval) {
                clearInterval(timerInterval);
                timerInterval = null;
            }
        }

        // History management
        async function loadHistory() {
            const tbody = document.getElementById('history-tbody');
            const loading = document.getElementById('history-loading');
            
            try {
                if (loading) loading.classList.remove('hidden');
                tbody.innerHTML = '<tr><td colspan="6" style="text-align: center; color: #6c757d;">Loading entries...</td></tr>';

                const response = await makeRequest('{{ route("timesheet.entries") }}');
                
                if (loading) loading.classList.add('hidden');

                if (response.success && response.data.length > 0) {
                    workEntries = response.data;
                    tbody.innerHTML = '';

                    workEntries.forEach(entry => {
                        const row = tbody.insertRow();
                        row.innerHTML = `
                            <td>${formatDate(entry.work_date)}</td>
                            <td>${entry.start_time}</td>
                            <td>${entry.end_time}</td>
                            <td>${entry.task_description}</td>
                            <td class="duration">${entry.duration}</td>
                            <td>
                                <button class="btn btn-danger" onclick="deleteEntry(${entry.id})" style="padding: 5px 10px; font-size: 12px;">Delete</button>
                            </td>
                        `;
                    });
                } else {
                    tbody.innerHTML = '<tr><td colspan="6" style="text-align: center; color: #6c757d; font-style: italic;">No entries yet. Start tracking your time!</td></tr>';
                }
            } catch (error) {
                if (loading) loading.classList.add('hidden');
                tbody.innerHTML = '<tr><td colspan="6" style="text-align: center; color: #dc3545;">Failed to load entries. Please refresh the page.</td></tr>';
            }
        }

        async function deleteEntry(id) {
            if (confirm('Are you sure you want to delete this entry?')) {
                try {
                    const response = await makeRequest(`/timesheet/entries/${id}`, {
                        method: 'DELETE'
                    });

                    if (response.success) {
                        showSuccess('Entry deleted successfully');
                        loadHistory();
                    } else {
                        showError('Failed to delete entry');
                    }
                } catch (error) {
                    showError('Failed to delete entry. Please try again.');
                }
            }
        }

        // Report generation
        async function generateReport() {
            const endDate = document.getElementById('report-end-date').value;
            if (!endDate) {
                showError('Please select a valid end date');
                return;
            }

            try {
                const response = await makeRequest(`{{ route('timesheet.report') }}?end_date=${endDate}`);

                if (response.success && response.data.entries.length > 0) {
                    const { entries, total_hours, start_date, end_date } = response.data;
                    
                    // Update display
                    document.getElementById('total-hours').textContent = parseFloat(total_hours).toFixed(2);
                    document.getElementById('period-range').textContent = 
                        `${formatDate(start_date)} - ${formatDate(end_date)}`;
                    
                    document.getElementById('report-summary').classList.remove('hidden');

                    // Populate report table
                    const tbody = document.getElementById('report-tbody');
                    tbody.innerHTML = '';

                    entries.forEach(entry => {
                        const row = tbody.insertRow();
                        row.innerHTML = `
                            <td>${formatDate(entry.work_date)}</td>
                            <td>${entry.start_time}</td>
                            <td>${entry.end_time}</td>
                            <td>${entry.task_description}</td>
                            <td class="duration">${entry.duration}</td>
                        `;
                    });

                    document.getElementById('report-table').classList.remove('hidden');
                    document.getElementById('export-btn').disabled = false;
                    currentReportData = response.data;
                } else {
                    showError('No entries found for the selected period');
                    document.getElementById('report-summary').classList.add('hidden');
                    document.getElementById('report-table').classList.add('hidden');
                    document.getElementById('export-btn').disabled = true;
                }
            } catch (error) {
                showError('Failed to generate report. Please try again.');
            }
        }

        // Excel export
        async function exportToExcel() {
            if (!currentReportData) {
                showError('Please generate a report first');
                return;
            }

            const endDate = document.getElementById('report-end-date').value;
            
            try {
                // Download file directly from Laravel
                const url = `{{ route('timesheet.export') }}?end_date=${endDate}`;
                const link = document.createElement('a');
                link.href = url;
                link.download = `Timesheet_${currentReportData.start_date}_to_${currentReportData.end_date}.xlsx`;
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
                
                showSuccess('Excel file downloaded successfully!');
            } catch (error) {
                showError('Failed to export Excel file. Please try again.');
            }
        }

        // Utility functions
        function formatDate(dateString) {
            const date = new Date(dateString);
            return date.toLocaleDateString('en-US', { 
                weekday: 'short',
                year: 'numeric', 
                month: 'short', 
                day: 'numeric' 
            });
        }

        // Initialize app when page loads
        init();
    </script>
</body>
</html>