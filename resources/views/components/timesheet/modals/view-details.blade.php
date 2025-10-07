{{-- View Details Modal Component --}}
{{-- Modal for viewing detailed information about a work session --}}
<div class="modal" id="view-details-modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Work Session Details</h3>
            <button class="modal-close" onclick="hideViewDetailsModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <div class="modal-body" id="view-details-content">
            <div class="detail-section">
                <h4><i class="fas fa-calendar-alt"></i> Session Information</h4>
                <div class="detail-grid">
                    <div class="detail-item">
                        <label>Date:</label>
                        <span id="detail-date">-</span>
                    </div>
                    <div class="detail-item">
                        <label>Start Time:</label>
                        <span id="detail-start-time">-</span>
                    </div>
                    <div class="detail-item">
                        <label>End Time:</label>
                        <span id="detail-end-time">-</span>
                    </div>
                    <div class="detail-item">
                        <label>Duration:</label>
                        <span id="detail-duration">-</span>
                    </div>
                </div>
            </div>

            <div class="detail-section">
                <h4><i class="fas fa-tasks"></i> Work Description</h4>
                <div class="work-description-content" id="detail-work-description">
                    -
                </div>
            </div>
        </div>

        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" onclick="hideViewDetailsModal()">
                Close
            </button>
        </div>
    </div>
</div>
