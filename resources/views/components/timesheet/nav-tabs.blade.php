<div class="nav-bar">
    <div class="nav-tabs">
        <button class="nav-tab active" data-tab="dashboard">
            <i class="fas fa-tachometer-alt"></i>
            Dashboard
        </button>
        <button class="nav-tab" data-tab="history">
            <i class="fas fa-history"></i>
            Work History
        </button>
        <button class="nav-tab" data-tab="reports">
            <i class="fas fa-chart-bar"></i>
            Reports
        </button>
        @if(auth()->user()?->isAuthor())
            <button class="nav-tab" data-tab="projects">
                <i class="fas fa-folder"></i>
                Projects
            </button>
        @endif
        <button class="nav-tab" data-tab="invoices">
            <i class="fas fa-file-invoice-dollar"></i>
            Invoices
        </button>
        @if(auth()->user()?->isAuthor())
            <button class="nav-tab" data-tab="backups">
                <i class="fas fa-database"></i>
                Backups
            </button>
        @endif
    </div>
</div>
