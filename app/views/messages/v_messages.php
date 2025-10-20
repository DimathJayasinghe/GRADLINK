<?php ob_start();?>
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/messages/messages.css">
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/messages/messages_sections.css">
<?php $styles = ob_get_clean();?>

<!-- Leftside bar section -->
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
    ];
?>

<?php ob_start();
    $leftside_buttons = [
        ['icon' => 'home', 'label' => 'Home', 'onclick' => "window.location.href='" . URLROOT . "/mainfeed'"],
        ['icon' => 'search', 'label' => 'Explore', 'onclick' => "window.location.href='" . URLROOT . "/explore'"],
        ['icon' => 'bell', 'label' => 'Notifications', 'onclick' => "NotificationModal()", 'require' => APPROOT . '/views/inc/commponents/notification_pop_up.php', 'notifications' => $notifications],
        ['icon' => 'envelope', 'label' => 'Messages', 'onclick' => "window.location.href='" . URLROOT . "/messages'",'active' => true],
        // ['icon' => 'user', 'label' => 'Profile' , 'onclick' => "window.location.href='" . URLROOT . "/profile/watch/".$_SESSION['user_id'] . "'"],
        ['icon' => 'user', 'label' => 'Profile' , 'onclick' => "window.location.href='" . URLROOT . "/profile?userid=".$_SESSION['user_id'] . "'"],
        // icon for fundraiser
        ['icon' => 'hand-holding-heart', 'label' => 'Fundraisers', 'onclick' => "window.location.href='" . URLROOT . "/fundraiser'"],
        // ['icon' => 'clipboard-list', 'label' => 'Post Requests', 'onclick' => "window.location.href='" . URLROOT . "/postrequest/'"],
        ['icon' => 'clipboard-list', 'label' => 'Event Requests', 'onclick' => "window.location.href='" . URLROOT . "/eventrequest/'"],
        ['icon' => 'calendar-alt', 'label' => 'Calender', 'onclick' => "window.location.href='" . URLROOT . "/calender'"],
    ];
    //  new portal to approve new alumnis only available for special alumnis
    if ($_SESSION['special_alumni']){
        $leftside_buttons[] = [
            'icon'=>'user-check','label'=>'Approve Alumni','onclick'=>"window.location.href='".URLROOT."/alumni/approve'"
        ];
    };
    $leftside_buttons[] = ['icon' => 'cog', 'label' => 'Settings', 'onclick' => "window.location.href='" . URLROOT . "/settings'"];
    require APPROOT . '/views/inc/commponents/leftSideBar.php'; ?>
<?php $leftsidebar = ob_get_clean(); ?>







<!-- Center section Available users list -->
<?php $center_topic = "Messages";?>
<?php ob_start();?>
    <?php require APPROOT . '/views/messages/sections/users_list_section.php'; ?>
<?php $center_content = ob_get_clean(); ?>






<!-- Right side section, Opened chats -->
<?php ob_start();?>
    <div class="main_content_section" id="chatRoom" style="padding: 0px; margin:0px; height: 100%;">
        <?php require APPROOT . '/views/messages/sections/' . $data['section']. '_section.php'; ?>
    </div>
<?php $rightsidebar = ob_get_clean(); ?>



<!-- Script section -->
<?php $script = null;?>


<!-- Import the relevent layout -->
<?php require APPROOT . '/views/layouts/threeColumnMiniLayout.php'; ?>
