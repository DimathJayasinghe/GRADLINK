<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/components/notification_popup.css">
<div class="notification-modal hidden" id="notificationModal">
    <div class="notification-modal-content">
        <div class="notification-modal-header">
            <h3>Notifications</h3>
            <button class="modal-close-btn"  id="closeNotificationModal"><i class="fas fa-times" style="margin-right: 0px;" ></i></button>
        </div>

        <div class="notification-list" id="notificationList">
            <!-- Notifications will be loaded here -->
            <?php if(!empty($notifications)): ?>
                <?php foreach ($notifications as $notification): ?>
                    <div class="notification-item">
                        <div class="notification-icon <?php echo $notification->type; ?>">
                            <i class="fas fa-<?php 
                               switch ($notification->type) {
                                    case 'like':
                                        echo 'heart';
                                        break;
                                    case 'follow':
                                        echo 'user-plus';
                                        break;
                                    case 'mention':
                                        echo 'at';
                                        break;
                                    case 'comment':
                                        echo 'comment';
                                        break;
                                    case 'event':
                                        echo 'calendar-day';
                                        break;
                                    default:
                                        echo 'bell';
                                }
                            ?>"></i>
                        </div>
                        <img src="<?php echo $notification->userImg; ?>" alt="" class="profile-photo" style="width:36px;height:36px;border-radius:50%;margin-right:12px;">
                        <div class="notification-content">
                            <div class="notification-text">
                                <span class="notification-user"><?php echo $notification->user; ?></span><?php echo $notification->content; ?>
                            </div>
                            <div class="notification-time"><?php echo $notification->time; ?></div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="no-notifications">No notifications to display</div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
    function NotificationModal() {
        const modal = document.getElementById('notificationModal');
        if (modal.classList.contains('hidden')) {
            modal.classList.remove('hidden');
            modal.style.display = 'flex';
        } else {
            modal.classList.add('hidden');
            modal.style.display = 'none';
        }
    }


    // Close the modal when clicking outside of it
    window.addEventListener('click', function(event) {
        const modal = document.getElementById('notificationModal');
        if (event.target === modal) {
            modal.classList.add('hidden');
            modal.style.display = 'none';
        }
    });
</script>