<?php ob_start() ?>
<title>Alumni Profile</title>
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/style.css">
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/color-pallate.css">
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/components/postCardStyles.css">
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/profile_styles.css"> <!-- Import profile specific styles -->
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/mainfeed_styles.css"> <!-- Import main feed styles -->
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
    ];
    $isOwner = isset($_SESSION['user_id']) && isset($data['userDetails']->id) && $_SESSION['user_id'] == $data['userDetails']->id;
?>

<?php ob_start() ?>
    <?php
    $leftside_buttons = [
        ['icon' => 'home', 'label' => 'Home', 'onclick' => "window.location.href='" . URLROOT . "/mainfeed'"],
        ['icon' => 'search', 'label' => 'Explore', 'onclick' => "window.location.href='" . URLROOT . "/explore'"],
        ['icon' => 'bell', 'label' => 'Notifications', 'onclick' => "NotificationModal()", 'require' => APPROOT . '/views/inc/commponents/notification_pop_up.php', 'badge' => true],
        ['icon' => 'envelope', 'label' => 'Messages', 'onclick' => "window.location.href='" . URLROOT . "/messages'"],
        ['icon' => 'user', 'label' => 'Profile', 'onclick' => "window.location.href='" . URLROOT . "/profile?userid=" . $_SESSION['user_id'] . "'", 'active' => true],
        // icon for fundraiser
        ['icon' => 'hand-holding-heart', 'label' => 'Fundraisers', 'onclick' => "window.location.href='" . URLROOT . "/fundraiser'"],
        //icon for post requests
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
<?php $leftsidebar = ob_get_clean(); ?>


<?php ob_start() ?>
<div class="main-content">
    
    <!-- Profile Section -->
    <?php require APPROOT . '/views/profiles/partials/sections/profile.php'; ?>

    <!-- Navigation Buttons -->
    <div class="profile-navigation">
        <div class="nav-button active" id="postsTab" onclick="showTab('posts')">
            POSTS
        </div>
        <div class="nav-button" id="infoTab" onclick="showTab('info')">
            INFO
        </div>
    </div>

    <!-- Import newpost_section below navigation -->
    <?php
    if ($isOwner) {
        require APPROOT . '/views/inc/commponents/newpost_section.php';
    } ?>

    <!-- Posts Section -->
    <?php require APPROOT . '/views/profiles/partials/sections/post.php'; ?>

    <!-- Info Section -->
    <?php require APPROOT . '/views/profiles/partials/sections/info.php'; ?>
</div>

<?php
// Hidden form for POST-based navigation to Messages with target user id
?>
<form id="profileMessageForm" method="post" action="<?= URLROOT; ?>/messages" style="display:none;">
    <input type="hidden" name="user" id="profileMessageUserId" value="">
</form>

<!-- Profile Popup -->
<?php require APPROOT . '/views/profiles/partials/popups/profile.php'; ?>

<!-- Work Experience Popup -->
<?php require APPROOT . '/views/profiles/partials/popups/work_experience.php'; ?>

<!-- Projects Popup -->
<?php require APPROOT . '/views/profiles/partials/popups/projects.php'; ?>

<!-- Certificates Popup -->
<?php require APPROOT . '/views/profiles/partials/popups/certificates.php'; ?>

<?php $center_content = ob_get_clean(); ?>
<?php ob_start() ?>
<!-- Include the right sidebar component -->
<?php
    $rightSidebarStylesIncluded = true; // Prevent duplicate styles
    require APPROOT . '/views/inc/commponents/rightSideBar.php';
?>
<?php $rightsidebar = ob_get_clean(); ?>

<script>window.URLROOT = "<?= URLROOT; ?>";</script>
<script defer src="<?php echo URLROOT ?>/js/component/postCard.js"></script>
<script defer src="<?php echo URLROOT ?>/js/profile/index.js"></script>

<?php ob_start() ?>
<?php $scripts = ob_get_clean(); ?>

<?php require APPROOT . '/views/layouts/threeColumnLayout.php'; ?>