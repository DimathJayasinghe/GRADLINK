<?php ob_start();?>
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/settings/settings.css">
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
        ['icon' => 'clipboard-list', 'label' => 'Event Requests', 'onclick' => "window.location.href='" . URLROOT . "/eventrequest/'"],
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
            // ['icon' => 'paint-brush', 'label' => 'Appearance', 'link' => URLROOT . '/settings/appearance','active' => $data['section'] === 'appearance'? true : false],
            ['icon' => 'question-circle', 'label'=>'Help', 'link' => URLROOT . '/settings/helpandsupport','active' => $data['section'] === 'helpandsupport'? true : false],
        ];
        require APPROOT . '/views/inc/commponents/settings_categories.php';
        $center_topic = "Settings";
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