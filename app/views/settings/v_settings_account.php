<?php ob_start();?>
<style>
    /* Settings page specific styles */
    .account_content {
        padding: 10px 30px 30px;
    }
    
    .account_content h2 {
        margin-bottom: 10px;
        font-size: 24px;
        font-weight: 600;
        color: var(--text);
    }
    
    .settings-description {
        color: var(--muted);
        margin-bottom: 30px;
    }
    
    .settings-section {
        margin-bottom: 40px;
        background: var(--card);
        border-radius: 10px;
        padding: 20px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
    }
    
    .settings-section h3 {
        font-size: 18px;
        font-weight: 500;
        margin-bottom: 15px;
        color: var(--text);
        padding-bottom: 10px;
        border-bottom: 1px solid var(--border);
    }
    
    .settings-option {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 15px 0;
        border-bottom: 1px solid var(--border);
    }
    
    .settings-option:last-child {
        border-bottom: none;
    }
    
    .settings-option-details h4 {
        font-size: 16px;
        margin-bottom: 5px;
        font-weight: 500;
    }
    
    .settings-option-details p {
        font-size: 14px;
        color: var(--muted);
    }
    
    .settings-btn {
        padding: 8px 16px;
        background-color: var(--btn);
        color: var(--btn-text);
        border: none;
        border-radius: 6px;
        cursor: pointer;
        font-size: 14px;
        font-weight: 500;
        transition: all 0.2s ease;
    }
    
    .settings-btn:hover {
        background-color: var(--link);
    }
    
    .settings-btn-secondary {
        background-color: var(--input);
        color: var(--text);
    }
    
    .settings-btn-secondary:hover {
        background-color: var(--border);
    }
    
    .settings-btn-danger {
        background-color: rgba(220, 53, 69, 0.1);
        color: #dc3545;
    }
    
    .settings-btn-danger:hover {
        background-color: rgba(220, 53, 69, 0.2);
    }
</style>
<?php $styles = ob_get_clean();?>
<?php 
    $notification = [

    ]
?>

<?php ob_start();?>
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
<?php
    $leftside_buttons = [
        ['icon' => 'home', 'label' => 'Home', 'onclick' => "window.location.href='" . URLROOT . "/mainfeed'" ],
        ['icon' => 'search', 'label' => 'Explore', 'onclick' => "window.location.href='" . URLROOT . "/explore'"],
        ['icon' => 'bell', 'label' => 'Notifications', 'onclick' => "NotificationModal()", 'require' => APPROOT . '/views/inc/commponents/notification_pop_up.php', 'notifications' => $notifications],
        ['icon' => 'envelope', 'label' => 'Messages', 'onclick' => "window.location.href='" . URLROOT . "/messages'"],
        ['icon' => 'user', 'label' => 'Profile' , 'onclick' => "window.location.href='" . URLROOT . "/profile?userid=".$_SESSION['user_id'] . "'"],
        // icon for fundraiser
        ['icon' => 'hand-holding-heart', 'label' => 'Fundraisers', 'onclick' => "window.location.href='" . URLROOT . "/fundraiser'"],
        //icon for post requests
        ['icon' => 'clipboard-list', 'label' => 'Post Requests', 'onclick' => "window.location.href='" . URLROOT . "/postrequest/'"],
        ['icon' => 'calendar-alt', 'label' => 'Calender', 'onclick' => "window.location.href='" . URLROOT . "/calender'"],
        ['icon' => 'cog', 'label' => 'Settings', 'onclick' => "window.location.href='" . URLROOT . "/settings'", 'active' => true],
    ];
    require APPROOT . '/views/inc/commponents/leftSideBar.php'; ?>
<?php $leftsidebar = ob_get_clean(); ?>
<?php ob_start();?>
    <!-- Settings categories -->
    <?php 
        $settings_categories = [
            ['icon' => 'user', 'label' => 'Account', 'link' => URLROOT . '/settings/account', 'active' => $data['section'] === 'account'? true : false],
            ['icon' => 'shield-alt', 'label' => 'Privacy', 'link' => URLROOT . '/settings/privacyandsafety','active' => $data['section'] === 'privacyandsafety'? true : false],
            ['icon' => 'bell', 'label' => 'Notifications', 'link' => URLROOT . '/settings/notifications','active' => $data['section'] === 'notifications'? true : false],
            ['icon' => 'paint-brush', 'label' => 'Appearance', 'link' => URLROOT . '/settings/appearance','active' => $data['section'] === 'appearance'? true : false],
            ['icon' => 'question-circle', 'label'=>'Help', 'link' => URLROOT . '/settings/helpandsupport','active' => $data['section'] === 'helpandsupport'? true : false],
        ];
        require APPROOT . '/views/inc/commponents/settings_categories.php';
    ?>
    
<?php $center_content = ob_get_clean(); ?>
<?php ob_start();?>
       <?php require APPROOT . '/views/settings/Sections/' . $data['section']. '_section.php'; ?>
<?php $rightsidebar = ob_get_clean(); ?>
<?php ob_start();?>
<script>
    // Custom JavaScript can be added here
</script>
<?php $scripts = ob_get_clean();?>
<?php require APPROOT . '/views/layouts/threeColumnMiniLayout.php'; ?>