<!DOCTYPE html>
<html lang="en">
    
<head>
    <meta charset="UTF-8">
    <title>GRADLINK - Main Feed</title>
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/css/style.css">
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/css/mainfeed_styles.css">
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/css/components/postCardStyles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
        // Make URLROOT available in JavaScript for AJAX calls
        window.URLROOT = "<?php echo URLROOT; ?>";
    </script>
</head>

<body>
    <div class="feed-container">
        <?php require APPROOT . '/views/inc/commponents/leftSideBar.php'; ?>

        <div class="main-content">
            <div class="tabs">
                <div class="tab active">For you</div>
                <div class="tab">Following</div>
            </div>

            <?php require APPROOT . '/views/inc/commponents/newpost_section.php'; ?>

            <div class="feed" id="feed">
            <?php 
            if(!empty($data['posts'])): foreach($data['posts'] as $p): ?>
                <post-card
                    profile-img="<?php echo htmlspecialchars($p->profile_image); ?>"
                    user-role = "<?php echo htmlspecialchars($p->role); ?>"
                    user-name="<?php echo htmlspecialchars($p->name); ?>"
                    tag="@user<?php echo $p->user_id; ?>"
                    post-time="<?php echo date('M d', strtotime($p->created_at)); ?>"
                    post-content="<?php echo htmlspecialchars($p->content); ?>"
                    post-img="<?php echo htmlspecialchars($p->image ?? ''); ?>"
                    like-count="<?php echo $p->likes; ?>"
                    cmnt-count="<?php echo $p->comments; ?>"
                    liked="<?php echo !empty($p->liked)?1:0; ?>"
                    post-id="<?php echo $p->id; ?>"
                    post-user-id="<?php echo $p->user_id; ?>"
                    current-user-id="<?php echo $_SESSION['user_id']; ?>"
                    current-user-role="<?php echo $_SESSION['user_role']; ?>"
                    >
                </post-card>
                <?php endforeach; else: ?>
                    <p>No posts yet.</p>
                    <?php endif; ?>
                </div>
                
        </div>
            <!-- Include the right sidebar component -->
            <?php
            $rightSidebarStylesIncluded = true; // Prevent duplicate styles
            require APPROOT . '/views/inc/commponents/rightSideBar.php';
            ?>

        <script src="<?php echo URLROOT?>/js/component/postCard.js"></script>
        
</body>
</html>