<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/components/notification_popup.css">
<div class="notification-modal hidden" id="notificationModal">
    <div class="notification-modal-content">
        <div class="notification-modal-header">
            <h3>Notifications</h3>
            <div class="notification-header-actions">
                <button class="mark-all-read-btn" id="markAllReadBtn" title="Mark all as read">
                    <i class="fas fa-check-double"></i>
                </button>
                <button class="modal-close-btn" id="closeNotificationModal">
                    <i class="fas fa-times" style="margin-right: 0px;"></i>
                </button>
            </div>
        </div>

        <div class="notification-list" id="notificationList">
            <!-- Notifications will be dynamically loaded here by JavaScript -->
            <div class="notification-loading">
                <i class="fas fa-spinner fa-spin"></i>
                <p>Loading notifications...</p>
            </div>
        </div>
    </div>
</div>

<script src="<?php echo URLROOT; ?>/js/notificationManager.js"></script>
<script>
    // Initialize notification manager when DOM is ready
    let notificationManager;
    
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initNotifications);
    } else {
        initNotifications();
    }
    
    function initNotifications() {
        notificationManager = new NotificationManager({
            urlRoot: '<?php echo URLROOT; ?>',
            pollInterval: 20000 // Poll every 20 seconds
        });
    }
    
    // Global function for sidebar button
    function NotificationModal() {
        if (notificationManager) {
            notificationManager.toggleModal();
        }
    }
</script>