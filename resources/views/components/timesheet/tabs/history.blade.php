{{-- History Tab Component --}}
{{-- Time logs history table with pagination and filters --}}
<div class="tab-content" id="history-tab">
    <div class="history-container">
        <div class="history-header">
            <h2>Work History</h2>
            <div class="history-actions">
                <button class="btn btn-primary" id="create-new-entry-btn" style="margin-right: 8px;">
                    <i class="fas fa-plus"></i>
                    Create New Entry
                </button>
                <button class="btn btn-secondary" id="refresh-history-btn">
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
            <!-- Pagination will be inserted here dynamically by JavaScript -->
        </div>
    </div>
</div>
