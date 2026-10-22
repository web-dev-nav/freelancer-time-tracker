{{-- Backups Tab Component --}}
{{-- Backup management page with list of all backups --}}
<div class="tab-content" id="backups-tab">
    <div class="backups-page">
        <div class="backups-page-header">
            <div>
                <h2>Database Backups</h2>
                <p class="backups-subtitle">Manage and download your backup files</p>
            </div>
            <div class="backups-page-actions">
                <button class="btn btn-primary" onclick="createFullBackup()" id="create-full-backup-btn">
                    <i class="fas fa-database"></i>
                    Full Database Backup
                </button>
                <button class="btn btn-success" onclick="showProjectBackupSelector()">
                    <i class="fas fa-folder"></i>
                    Organization Backup
                </button>
                <button class="btn btn-secondary" onclick="refreshBackupList()">
                    <i class="fas fa-sync-alt"></i>
                    Refresh
                </button>
            </div>
        </div>

        {{-- Organization Backup Selector (Hidden by default) --}}
        <div id="project-backup-selector" class="project-backup-selector" style="display: none;">
            <div class="backup-selector-content">
                <h3>Select Organization to Backup</h3>
                <select id="backup-project-select" class="form-control">
                    <option value="">-- Select an organization --</option>
                </select>
                <div class="backup-selector-actions">
                    <button class="btn btn-primary" onclick="createProjectBackup()">
                        <i class="fas fa-save"></i>
                        Create Backup
                    </button>
                    <button class="btn btn-secondary" onclick="hideProjectBackupSelector()">
                        <i class="fas fa-times"></i>
                        Cancel
                    </button>
                </div>
            </div>
        </div>

        <div class="backups-container">
            <div id="backups-list" class="backups-list">
                <!-- Backup items will be loaded here dynamically by JavaScript -->
            </div>
        </div>
    </div>
</div>
