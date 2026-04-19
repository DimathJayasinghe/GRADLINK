<link rel="shortcut icon" href="<?php echo URLROOT ?>/img/favicon_white.png" type="image/x-icon">
<?php

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


    $is_fundraiser = strpos($_SERVER['REQUEST_URI'], 'fundraiser') !== false;
    $is_eventrequest = strpos($_SERVER['REQUEST_URI'], 'eventrequest') !== false;

    $leftside_buttons = [
        ['icon' => 'home', 'label' => 'Home', 'onclick' => "window.location.href='" . URLROOT . "/mainfeed'" ],
        ['icon' => 'search', 'label' => 'Explore', 'onclick' => "window.location.href='" . URLROOT . "/explore'"],
        ['icon' => 'bell', 'label' => 'Notifications', 'onclick' => "NotificationModal()", 'require' => APPROOT . '/views/inc/commponents/notification_pop_up.php', 'notifications' => $notifications],
        ['icon' => 'envelope', 'label' => 'Messages', 'onclick' => "window.location.href='" . URLROOT . "/messages'"],
        ['icon' => 'user', 'label' => 'Profile' , 'onclick' => "window.location.href='" . URLROOT . "/profile?userid=".$_SESSION['user_id'] . "'"],
        
        ['icon' => 'hand-holding-heart', 'label' => 'Fundraisers', 'onclick' => "window.location.href='" . URLROOT . "/fundraiser'"],
        ['icon' => 'clipboard-list', 'label' => 'Event Requests', 'onclick' => "window.location.href='" . URLROOT . "/eventrequest/'"],
        ['icon' => 'calendar-alt', 'label' => 'Calender', 'onclick' => "window.location.href='" . URLROOT . "/calender'",'active' => true],
    ];
    if ($_SESSION['special_alumni']){
        $leftside_buttons[] = [
            'icon'=>'user-check','label'=>'Approve Alumni','onclick'=>"window.location.href='".URLROOT."/alumni/approve'"
        ];
    };
    $leftside_buttons[] = ['icon' => 'cog', 'label' => 'Settings', 'onclick' => "window.location.href='" . URLROOT . "/settings'"];
    require APPROOT . '/views/inc/commponents/leftSideBar.php'; ?>
    
?>
<?php

$leftsidebar = ob_get_clean();


ob_start();
?>
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/req_dashboard/dashboard_layout.css">
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/req_dashboard/adapter_layout.css">

<style>
    /* Style the dashboard navigation similar to settings categories */
    .dashboard-categories {
        display: flex;
        flex-direction: column;
        height: 100%;
        /* border-right: 1px solid var(--border); */
        background-color: var(--bg-alt);
    }
    
    .dashboard-categories .category-title {
        padding: 20px 16px 10px;
        font-weight: 600;
        color: var(--text);
        font-size: 18px;
    }
    
    .dashboard-categories ul {
        list-style: none;
        /* padding: 20px 0 0 0; Added top padding for headroom */
        margin: 0;
    }
    
    .dashboard-categories li {
        padding: 10px 16px;
        display: flex;
        align-items: center;
        cursor: pointer;
        color: var(--muted);
        transition: all 0.2s ease;
    }
    
    .dashboard-categories li:hover {
        background: rgba(255, 255, 255, 0.05);
        color: var(--text);
    }
    
    .dashboard-categories li.active {
        background: rgba(158, 212, 220, 0.1);
        color: var(--link);
        border-left: 3px solid var(--link);
    }
    
    .dashboard-categories li i {
        margin-right: 10px;
        width: 20px;
        text-align: center;
    }
    
    .dashboard-categories li span {
        font-size: 14px;
    }
</style>

<div class="dashboard-categories">
    <ul>
        <?php foreach($sidebar_left as $link): ?>
        <li class="<?php if(isset($link['active']) && $link['active']){echo "active";}?>">
            <a href="<?php echo URLROOT.$link['url'] ?>">
                <i class="fas fa-<?php echo isset($link['icon']) ? $link['icon'] : 'layer-group'; ?>"></i>
                <span><?php echo $link['label'] ?></span>
            </a>
        </li>
        <?php endforeach; ?>
    </ul>
</div>
<?php
$center_topic = "Events";

$center_content = ob_get_clean();


if (!isset($scripts)) {
    $scripts = '';
}

$rightsidebar = $content;
SessionManager::ensureStarted();
echo "<script>window.GL_CURRENT_USER_ID = " . (isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : 'null') . ";</script>\n";

require APPROOT . '/views/layouts/threeColumnMiniLayout.php';
?>