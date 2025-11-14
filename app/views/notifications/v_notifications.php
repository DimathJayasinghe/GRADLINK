<?php ob_start() ?>
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/style.css">
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/components/postCardStyles.css">
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/components/postCardShowMore.css">
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/mainfeed_styles.css">
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/explore_styles.css">
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/components/rightSidebarStyles.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<?php $styles = ob_get_clean(); ?>

<?php
$notifications = [
    (object)[
        'type' => 'like',
        'user' => 'Alice',
        'content' => ' liked your post.',
        'time' => '2h ago',
        'userImg' => URLROOT . '/media/profile/alice.jpg'
    ],
    (object)[
        'type' => 'follow',
        'user' => 'Bob',
        'content' => ' started following you.',
        'time' => '3h ago',
        'userImg' => URLROOT . '/media/profile/bob.jpg'
    ]
]
?>

<?php ob_start() ?>
<?php
$leftside_buttons = [
    ['icon' => 'home', 'label' => 'Home', 'onclick' => "window.location.href='" . URLROOT . "/mainfeed'"],
    ['icon' => 'search', 'label' => 'Explore', 'onclick' => "window.location.href='" . URLROOT . "/explore'"],
    ['icon' => 'bell', 'label' => 'Notifications', 'onclick' => "NotificationModal()", 'require' => APPROOT . '/views/inc/commponents/notification_pop_up.php', 'badge' => true, 'active' => true],
    ['icon' => 'envelope', 'label' => 'Messages', 'onclick' => "window.location.href='" . URLROOT . "/messages'"],
    ['icon' => 'user', 'label' => 'Profile', 'onclick' => "window.location.href='" . URLROOT . "/profile?userid=" . $_SESSION['user_id'] . "'"],
    ['icon' => 'hand-holding-heart', 'label' => 'Fundraisers', 'onclick' => "window.location.href='" . URLROOT . "/fundraiser'"],
    ['icon' => 'clipboard-list', 'label' => 'Event Requests', 'onclick' => "window.location.href='" . URLROOT . "/eventrequest/'"],
    ['icon' => 'calendar-alt', 'label' => 'Calender', 'onclick' => "window.location.href='" . URLROOT . "/calender'"],
];
//  new portal to approve new alumnis only available for special alumnis
if ($_SESSION['special_alumni']) {
    $leftside_buttons[] = [
        'icon' => 'user-check',
        'label' => 'Approve Alumni',
        'onclick' => "window.location.href='" . URLROOT . "/alumni/approve'"
    ];
};
$leftside_buttons[] = ['icon' => 'cog', 'label' => 'Settings', 'onclick' => "window.location.href='" . URLROOT . "/settings'"];
require APPROOT . '/views/inc/commponents/leftSideBar.php'; ?>
require APPROOT . '/views/inc/commponents/leftSideBar.php';
?>
<?php $leftsidebar = ob_get_clean(); ?>

<?php ob_start() ?>
<div class="main-content">
    <h2 class="Notification-headline">Notifications</h2>
    <div class="notification-container">
        <!-- Notifications will be rendered here -->
         <div class="notification" data-notification-id="">
            <div class="icon">
                <!-- Notification icon goes here, can differe with the type of the notifiation -->
            </div>
            <div class="notification-content">
                <div class="text">
                    <p><strong>User Name</strong> <!-- notification content goes here.--></p>
                    <span class="time">Time ago</span>
                </div>
                <div class="expandable-part">
                    <!-- this part is dynamic , can differe with the type of the notification -->
                </div>
            </div>
         </div>
    </div>
    
</div>

<?php $center_content = ob_get_clean(); ?>

<?php ob_start() ?>
<!-- Include the right sidebar component -->
<?php
require APPROOT . '/views/inc/commponents/rightSideBar.php';
?>
<?php $rightsidebar = ob_get_clean(); ?>

<script src="<?php echo URLROOT; ?>/js/notifications/page.js"></script>
<?php ob_start() ?>
    let notificationPageManager;
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initNotificationPage);
    } else {
        initNotificationPage();
    }

    function initNotificationPage() {
        notificationPageManager = new NotificationPageManager({
            urlRoot: '<?php echo URLROOT; ?>'
            pollInterval: 20000 // Poll every 20 seconds
        });
    }
    
<?php $scripts = ob_get_clean(); ?>

<?php require APPROOT . '/views/layouts/threeColumnLayout.php'; ?>