<?php
// Check if URLROOT is defined, otherwise define it
if (!defined('URLROOT')) {
    // This is a fallback - ideally URLROOT should be defined in your config
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
    define('URLROOT', $protocol . $_SERVER['HTTP_HOST'] . '/GRADLINK');
}
?>

<!-- Left Sidebar Component -->
<div class="sidebar">
    <div class="logo">
        <img src="<?php echo URLROOT; ?>/img/logo_white.png" alt="GRADLINK">
    </div>
    <ul class="sidebar-menu">
        <li><i class="fas fa-home"></i> <span>Home</span></li>
        <li><i class="fas fa-search"></i> <span>Explore</span></li>
        <li id="notifications-btn">
            <i class="fas fa-bell"></i>
            <span>Notifications</span>
            <span class="notification-badge">19</span>
        </li>
        <li><i class="fas fa-envelope"></i> <span>Messages</span></li>
        <li><i class="fas fa-bookmark"></i> <span>Bookmarks</span></li>
        <li><i class="fas fa-user"></i> <span>Profile</span></li>
        <li id="logout-btn" onclick="window.location.href='<?php echo URLROOT; ?>/logout'">
            <i class="fas fa-sign-out-alt"></i> <span>Logout</span>
        </li>
    </ul>
    <button class="post-button">Post</button>
</div>

<!-- Post Modal -->
<div class="post-modal" id="postModal">
    <div class="post-modal-content">
        <div class="post-modal-header">
            <button class="modal-close-btn"><i class="fas fa-times"></i></button>
        </div>
        <div class="post-modal-body">
            <img src="<?php echo URLROOT; ?>/img/default-profile.jpg" alt="Profile" class="profile-photo">
            <div class="post-input-container">
                <textarea placeholder="What's happening?"></textarea>
                <div class="divider"></div>
                <div class="post-modal-actions">
                    <div class="post-modal-tools">
                        <button class="attach-btn">Attach</button>
                    </div>
                    <button class="modal-post-btn">Post</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Notification Modal -->
<div class="notification-modal" id="notificationModal">
    <div class="notification-modal-content">
        <div class="notification-modal-header">
            <h3>Notifications</h3>
            <button class="modal-close-btn" id="closeNotificationModal"><i class="fas fa-times"></i></button>
        </div>

        <div class="notification-list" id="notificationList">
            <!-- Notifications will be loaded here -->
        </div>
    </div>
</div>

<!-- Include required CSS if not already included -->
<?php if (!isset($sidebarStylesIncluded)): ?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/mainfeed_styles.css">
<style>
    /* Essential sidebar styles */
    .sidebar {
        width: 275px;
        padding: 20px;
        border-right: 1px solid var(--border);
    }

    .sidebar-menu {
        list-style: none;
        margin-top: 20px;
    }

    .sidebar-menu li {
        margin-bottom: 25px;
        font-size: 18px;
        display: flex;
        align-items: center;
        font-weight: 500;
        cursor: pointer;
        transition: var(--transition);
    }

    .sidebar-menu li:hover {
        color: var(--link);
    }

    .sidebar-menu li i, .sidebar-menu li img {
        margin-right: 15px;
        font-size: 22px;
    }

    .sidebar-menu .notification-badge {
        background-color: var(--link);
        color: white;
        border-radius: 50%;
        width: 20px;
        height: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        position: relative;
        top: -10px;
        left: -10px;
    }

    .post-button {
        background-color: var(--btn);
        color: var(--btn-text);
        border: none;
        border-radius: 30px;
        padding: 15px 0;
        width: 100%;
        font-size: 16px;
        font-weight: 600;
        cursor: pointer;
        margin-top: 10px;
        transition: var(--transition);
    }

    .post-button:hover {
        background-color: var(--link);
        transform: translateY(-2px);
    }

    /* Logo styling */
    .logo {
        display: flex;
        align-items: center;
        margin-bottom: 10px;
    }

    .logo img {
        height: 40px;
        filter: drop-shadow(0 0 5px rgba(158, 212, 220, 0.3));
    }
    
    /* Post Modal Styles */
    .post-modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: rgba(0, 0, 0, 0.7);
        z-index: 1000;
        align-items: flex-start;
        justify-content: center;
        padding-top: 50px;
        animation: fadeIn 0.3s;
    }

    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }

    .post-modal-content {
        background-color: var(--bg);
        width: 100%;
        max-width: 600px;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 0 20px rgba(0, 0, 0, 0.3);
        animation: slideDown 0.3s;
    }

    @keyframes slideDown {
        from { transform: translateY(-20px); opacity: 0; }
        to { transform: translateY(0); opacity: 1; }
    }

    .post-modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 10px 15px;
        border-bottom: 1px solid var(--border);
    }

    .modal-close-btn {
        background: transparent;
        border: none;
        color: var(--text);
        font-size: 20px;
        cursor: pointer;
        width: 34px;
        height: 34px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: background-color 0.2s;
    }

    .modal-close-btn:hover {
        background-color: rgba(158, 212, 220, 0.1);
    }

    .post-modal-body {
        padding: 15px;
        display: flex;
        gap: 15px;
    }

    .post-input-container {
        flex-grow: 1;
    }

    .post-modal-body textarea {
        width: 100%;
        min-height: 120px;
        background: transparent;
        border: none;
        color: var(--text);
        font-size: 20px;
        resize: none;
        font-family: 'Poppins', sans-serif;
        outline: none;
        margin-bottom: 15px;
    }

    .divider {
        height: 1px;
        background-color: var(--border);
        margin: 15px 0;
    }

    .post-modal-actions {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .post-modal-tools {
        display: flex;
        gap: 10px;
    }

    .modal-post-btn {
        background-color: var(--btn);
        color: var(--btn-text);
        border: none;
        border-radius: 20px;
        padding: 8px 20px;
        font-weight: 600;
        cursor: pointer;
        transition: var(--transition);
    }

    .modal-post-btn:hover {
        background-color: var(--link);
        transform: translateY(-2px);
    }

    /* Notification Modal Styles */
    .notification-modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.8);
        z-index: 1000;
        justify-content: center;
        align-items: center;
    }

    .notification-modal-content {
        background-color: var(--card);
        border-radius: 15px;
        width: 90%;
        max-width: 450px;
        max-height: 90vh;
        display: flex;
        flex-direction: column;
        overflow: hidden;
    }

    .notification-modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 15px 20px;
        border-bottom: 1px solid var(--border);
    }

    .notification-modal-header h3 {
        color: var(--text);
        font-weight: 600;
        font-size: 18px;
    }

    .notification-list {
        padding: 10px 0;
        overflow-y: auto;
        max-height: 70vh;
    }

    .no-notifications {
        color: var(--muted);
        text-align: center;
        padding: 30px;
        font-style: italic;
    }

    .notification-item {
        display: flex;
        padding: 15px 20px;
        border-bottom: 1px solid var(--border);
        position: relative;
        transition: var(--transition);
    }

    .notification-item:hover {
        background-color: rgba(15, 21, 24, 0.5);
    }

    .notification-item:last-child {
        border-bottom: none;
    }

    .notification-icon {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 10px;
        color: white;
    }

    .notification-icon.like {
        background-color: #f91880;
    }

    .notification-icon.follow {
        background-color: #1d9bf0;
    }

    .notification-icon.mention {
        background-color: #7856ff;
    }

    .notification-icon.comment {
        background-color: #00ba7c;
    }

    .notification-icon.event {
        background-color: #ff7a00;
    }

    .notification-item .profile-photo {
        width: 36px;
        height: 36px;
        margin-right: 15px;
    }

    .notification-content {
        flex: 1;
    }

    .notification-text {
        font-size: 14px;
        line-height: 1.4;
        color: var(--text);
    }

    .notification-user {
        font-weight: 600;
    }

    .notification-time {
        font-size: 12px;
        color: var(--muted);
        margin-top: 4px;
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .sidebar {
            width: 70px;
            padding: 20px 10px;
        }
        
        .sidebar-menu li span {
            display: none;
        }
        
        .sidebar-menu li i {
            margin-right: 0;
            font-size: 24px;
        }
        
        .post-button {
            font-size: 0;
            padding: 15px;
        }
        
        .post-button::after {
            content: "+";
            font-size: 24px;
            font-weight: 300;
        }
    }
</style>
<?php endif; ?>

<!-- Include required JavaScript for the sidebar functionality -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // ----- FIX: define JS URL root -----
    const URLROOT_JS = "<?php echo URLROOT; ?>";

    // Post modal functionality (unchanged)
    const postButton = document.querySelector('.post-button');
    const postModal = document.getElementById('postModal');
    const postModalCloseBtn = document.querySelector('#postModal .modal-close-btn');
    const modalPostBtn = document.querySelector('.modal-post-btn');

    if (postButton) {
        postButton.addEventListener('click', () => {
            postModal.style.display = 'flex';
            postModal.querySelector('textarea').focus();
        });
    }
    if (postModalCloseBtn) postModalCloseBtn.addEventListener('click', () => postModal.style.display = 'none');
    window.addEventListener('click', e => { if (e.target === postModal) postModal.style.display = 'none'; });
    if (modalPostBtn) {
        modalPostBtn.addEventListener('click', () => {
            const txt = postModal.querySelector('textarea').value.trim();
            if (!txt) return;
            postModal.querySelector('textarea').value = '';
            postModal.style.display = 'none';
        });
    }

    // ----- Notification functionality FIX -----
    const notificationBtn        = document.getElementById('notifications-btn');
    const notificationModal      = document.getElementById('notificationModal');
    const closeNotificationModal = document.getElementById('closeNotificationModal');
    const notificationList       = document.getElementById('notificationList');

    // FIX: removed undefined URLROOT references; now use URLROOT_JS
    const notificationsData = [
        {type:'like',   user:'Tech Careers',        userImg: URLROOT_JS + '/img/follow-1.jpg',        content:'liked your post about job interview tips.', time:'2 hours ago'},
        {type:'follow', user:'Dr. Sarah Johnson',   userImg: URLROOT_JS + '/img/professor-profile.jpg', content:'started following you.',                      time:'1 day ago'},
        {type:'mention',user:'Grad Network',        userImg: URLROOT_JS + '/img/follow-2.jpg',        content:'mentioned you in a post.',                   time:'2 days ago'},
        {type:'comment',user:'Jane Smith',          userImg: URLROOT_JS + '/img/default-profile.jpg', content:'commented: "Great insights!"',               time:'3 days ago'},
        {type:'event',  user:'IEEE Student Branch', userImg: URLROOT_JS + '/img/ieee-logo.jpg',       content:'posted an event: "IEEE INTRODUCTORY SESSION"', time:'1 week ago'}
    ];

    function iconFor(type){
        switch(type){
            case 'like': return 'fas fa-heart';
            case 'follow': return 'fas fa-user-plus';
            case 'mention': return 'fas fa-at';
            case 'comment': return 'fas fa-comment';
            case 'event': return 'fas fa-calendar-day';
            default: return 'fas fa-bell';
        }
    }

    function loadNotifications(){
        if (!notificationList) return;
        if (!notificationsData.length){
            notificationList.innerHTML = '<div class="no-notifications">No notifications yet.</div>';
            return;
        }
        notificationList.innerHTML = notificationsData.map(n => `
            <div class="notification-item">
                <div class="notification-icon ${n.type}"><i class="${iconFor(n.type)}"></i></div>
                <img src="${n.userImg}" alt="" class="profile-photo" style="width:36px;height:36px;border-radius:50%;margin-right:12px;">
                <div class="notification-content">
                    <div class="notification-text">
                        <span class="notification-user">${n.user}</span>${n.content}
                    </div>
                    <div class="notification-time">${n.time}</div>
                </div>
            </div>
        `).join('');
    }

    if (notificationBtn) {
        notificationBtn.addEventListener('click', () => {
            loadNotifications();
            notificationModal.style.display = 'flex';
            const badge = notificationBtn.querySelector('.notification-badge');
            if (badge) badge.style.display = 'none';
        });
    }

    if (closeNotificationModal) {
        closeNotificationModal.addEventListener('click', () => {
            notificationModal.style.display = 'none';
        });
    }

    window.addEventListener('click', e => {
        if (e.target === notificationModal) notificationModal.style.display = 'none';
    });
});
</script>