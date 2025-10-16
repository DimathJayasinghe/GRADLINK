<?php ob_start() ?>
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/css/components/postCardStyles.css">
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/css/mainfeed_styles.css">
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
        ['icon' => 'home', 'label' => 'Home', 'onclick' => "window.location.href='" . URLROOT . "/mainfeed'" , 'active' => true],
        ['icon' => 'search', 'label' => 'Explore', 'onclick' => "window.location.href='" . URLROOT . "/explore'"],
        ['icon' => 'bell', 'label' => 'Notifications', 'onclick' => "NotificationModal()", 'require' => APPROOT . '/views/inc/commponents/notification_pop_up.php', 'notifications' => $notifications],
        ['icon' => 'envelope', 'label' => 'Messages', 'onclick' => "window.location.href='" . URLROOT . "/messages'"],
        // ['icon' => 'user', 'label' => 'Profile' , 'onclick' => "window.location.href='" . URLROOT . "/profile/watch/".$_SESSION['user_id'] . "'"],
        ['icon' => 'user', 'label' => 'Profile' , 'onclick' => "window.location.href='" . URLROOT . "/profile?userid=".$_SESSION['user_id'] . "'"],
        // icon for fundraiser
        ['icon' => 'hand-holding-heart', 'label' => 'Fundraisers', 'onclick' => "window.location.href='" . URLROOT . "/fundraiser'"],
        //icon for post requests
        ['icon' => 'clipboard-list', 'label' => 'Post Requests', 'onclick' => "window.location.href='" . URLROOT . "/postrequest/'"],
        ['icon' => 'calendar-alt', 'label' => 'Calender', 'onclick' => "window.location.href='" . URLROOT . "/calender'"],
        ['icon' => 'cog', 'label' => 'Settings', 'onclick' => "window.location.href='" . URLROOT . "/settings'"]
    ];
    require APPROOT . '/views/inc/commponents/leftSideBar.php'; ?>
    <?php $leftsidebar = ob_get_clean(); ?>

    <?php ob_start() ?>
    <div class="main-content">
        <div class="tabs">
            <div class="tab active">For you</div>
            <div class="tab">Following</div>
        </div>

        <?php require APPROOT . '/views/inc/commponents/newpost_section.php'; ?>

        <div class="feed" id="feed">
            <?php
            if (!empty($data['posts'])): foreach ($data['posts'] as $p): ?>
                    <post-card
                        profile-img="<?php echo htmlspecialchars($p->profile_image); ?>"
                        user-role="<?php echo htmlspecialchars($p->role); ?>"
                        user-name="<?php echo htmlspecialchars($p->name); ?>"
                        tag="@user<?php echo $p->user_id; ?>"
                        post-time="<?php echo date('M d', strtotime($p->created_at)); ?>"
                        post-content="<?php echo htmlspecialchars($p->content); ?>"
                        post-img="<?php echo htmlspecialchars($p->image ?? ''); ?>"
                        like-count="<?php echo $p->likes; ?>"
                        cmnt-count="<?php echo $p->comments; ?>"
                        liked="<?php echo !empty($p->liked) ? 1 : 0; ?>"
                        post-id="<?php echo $p->id; ?>"
                        post-user-id="<?php echo $p->user_id; ?>"
                        current-user-id="<?php echo $_SESSION['user_id']; ?>"
                        current-user-role="<?php echo $_SESSION['user_role']; ?>">
                    </post-card>
                <?php endforeach;
            else: ?>
                <p>No posts yet.</p>
            <?php endif; ?>
        </div>

    </div>
    <?php $center_content = ob_get_clean(); ?>
    <?php ob_start() ?>
    <!-- Include the right sidebar component -->
        <?php
        $rightSidebarStylesIncluded = true; // Prevent duplicate styles
        require APPROOT . '/views/inc/commponents/rightSideBar.php';
        ?>
    <?php $rightsidebar = ob_get_clean(); ?>

    <?php ob_start() ?>
    window.URLROOT = "<?php echo URLROOT; ?>";
    <script src="<?php echo URLROOT; ?>/js/mainfeed_script.js"></script>
    <script src="<?php echo URLROOT ?>/js/component/postCard.js"></script>
    <?php $scripts = ob_get_clean(); ?>
    <?php require APPROOT . '\views\layouts\threeColumnLayout.php'; ?>