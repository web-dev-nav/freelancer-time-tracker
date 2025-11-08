{{-- Invoices Tab Component --}}
{{-- Invoice management page for creating and managing invoices --}}
<div class="tab-content" id="invoices-tab">
    <div class="invoices-page">
        {{-- Header with Stats --}}
        <div class="invoices-stats-row">
            <div class="invoice-stat-card">
                <div class="invoice-stat-icon" style="background: rgba(59, 130, 246, 0.1); color: #60a5fa;">
                    <i class="fas fa-file-invoice"></i>
                </div>
                <div class="invoice-stat-content">
                    <div class="invoice-stat-value" id="total-invoices-count">0</div>
                    <div class="invoice-stat-label">Total Invoices</div>
                </div>
            </div>
            <div class="invoice-stat-card">
                <div class="invoice-stat-icon" style="background: rgba(245, 158, 11, 0.1); color: #fbbf24;">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="invoice-stat-content">
                    <div class="invoice-stat-value" id="pending-invoices-count">0</div>
                    <div class="invoice-stat-label">Pending</div>
                </div>
            </div>
            <div class="invoice-stat-card">
                <div class="invoice-stat-icon" style="background: rgba(16, 185, 129, 0.1); color: #34d399;">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="invoice-stat-content">
                    <div class="invoice-stat-value" id="paid-invoices-count">0</div>
                    <div class="invoice-stat-label">Paid</div>
                </div>
            </div>
            <div class="invoice-stat-card">
                <div class="invoice-stat-icon" style="background: rgba(139, 92, 246, 0.1); color: #a78bfa;">
                    <i class="fas fa-dollar-sign"></i>
                </div>
                <div class="invoice-stat-content">
                    <div class="invoice-stat-value" id="total-revenue">$0.00</div>
                    <div class="invoice-stat-label">Total Revenue</div>
                </div>
            </div>
        </div>

        {{-- Action Bar --}}
        <div class="invoices-page-header">
            <div>
                <h2>Invoices</h2>
                <p class="invoices-subtitle">Create and manage client invoices</p>
            </div>
            <div class="invoices-page-actions">
                <select class="form-control" id="invoice-status-filter" onchange="loadInvoices()" style="width: auto; display: inline-block;">
                    <option value="all">All Statuses</option>
                    <option value="draft">Draft</option>
                    <option value="sent">Sent</option>
                    <option value="paid">Paid</option>
                    <option value="cancelled">Cancelled</option>
                </select>
                <button class="btn btn-primary" onclick="showCreateInvoiceModal()">
                    <i class="fas fa-plus"></i>
                    Create Invoice
                </button>
            </div>
        </div>

        {{-- Invoices List --}}
        <div class="invoices-list" id="invoices-list">
            <!-- Invoices will be loaded here dynamically by JavaScript -->
            <div class="empty-state">
                <i class="fas fa-file-invoice-dollar"></i>
                <h3>No Invoices Yet</h3>
                <p>Create your first invoice to get started</p>
            </div>
        </div>

        {{-- Pagination --}}
        <div class="pagination-container" id="invoice-pagination" style="display: none;">
            <!-- Pagination will be inserted here -->
        </div>
    </div>
</div>
