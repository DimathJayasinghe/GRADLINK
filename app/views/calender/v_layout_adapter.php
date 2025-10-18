<?php
// This is a template adapter for request dashboards to use threeColumnMiniLayout
// It helps maintain the original sidebar structure while integrating with the three-column layout

// Start capturing leftsidebar content for standard sidebar
ob_start();
?>
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

    // Define which sidebar item should be active based on current page
    $is_fundraiser = strpos($_SERVER['REQUEST_URI'], 'fundraiser') !== false;
    $is_eventrequest = strpos($_SERVER['REQUEST_URI'], 'eventrequest') !== false;

    $leftside_buttons = [
        ['icon' => 'home', 'label' => 'Home', 'onclick' => "window.location.href='" . URLROOT . "/mainfeed'" ],
        ['icon' => 'search', 'label' => 'Explore', 'onclick' => "window.location.href='" . URLROOT . "/explore'"],
        ['icon' => 'bell', 'label' => 'Notifications', 'onclick' => "NotificationModal()", 'require' => APPROOT . '/views/inc/commponents/notification_pop_up.php', 'notifications' => $notifications],
        ['icon' => 'envelope', 'label' => 'Messages', 'onclick' => "window.location.href='" . URLROOT . "/messages'"],
        ['icon' => 'user', 'label' => 'Profile' , 'onclick' => "window.location.href='" . URLROOT . "/profile?userid=".$_SESSION['user_id'] . "'"],
        // icon for fundraiser
        ['icon' => 'hand-holding-heart', 'label' => 'Fundraisers', 'onclick' => "window.location.href='" . URLROOT . "/fundraiser'", 'active' => $is_fundraiser],
        //icon for event requests
        // ['icon' => 'clipboard-list', 'label' => 'event Requests', 'onclick' => "window.location.href='" . URLROOT . "/eventrequest/'", 'active' => $is_eventrequest],
        ['icon' => 'clipboard-list', 'label' => 'Event Requests', 'onclick' => "window.location.href='" . URLROOT . "/eventrequest/'", 'active' => $is_eventrequest],
        ['icon' => 'calendar-alt', 'label' => 'Calender', 'onclick' => "window.location.href='" . URLROOT . "/calender'",'active' => true],
    ];
    //  new portal to approve new alumnis only available for special alumnis
    if ($_SESSION['special_alumni']){
        $leftside_buttons[] = ['icon'=>'user-check','label'=>'Approve Alumni','onclick'=>"window.location.href='".URLROOT."/alumni/approve'"];
    };
    $leftside_buttons[] = ['icon' => 'cog', 'label' => 'Settings', 'onclick' => "window.location.href='" . URLROOT . "/settings'"];
    require APPROOT . '/views/inc/commponents/leftSideBar.php'; ?>
<?php $leftsidebar = ob_get_clean();
$center_topic= "Events";
$rightsidebar = $content;
require APPROOT . '/views/layouts/threeColumnMiniLayout.php';
?>