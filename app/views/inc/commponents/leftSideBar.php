<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/components/leftSidebar.css">

<!-- Left Sidebar Component -->
<div class="sidebar">
    <div class="logo">
        <img src="<?php echo URLROOT; ?>/img/logo_white.png" alt="GRADLINK">
        <p>GRADLINK</p>
    </div>
    <ul class="sidebar-menu">
        <?php foreach ($leftside_buttons as $button):?>
            <li onclick="<?php echo $button['onclick']?>" class="<?php echo isset($button['active']) && $button['active'] ? 'active' : '' ?>">
                <div class="sidebar-menu-item-wrapper">
                    <i class="fas fa-<?php echo $button['icon']?>"></i>
                    <span><?php echo $button['label']?></span>
                    <?php if (isset($button['badge']) && $button['badge']): ?>
                        <span class="notification-badge" id="notificationBadge" style="display: none;">0</span>
                    <?php endif; ?>
                </div>
                <?php 
                    if (isset($button['require']) && !empty($button['require'])) {
                        // Pass notifications if they exist in this button
                        if (isset($button['notifications'])) {
                            $notifications = $button['notifications'];
                        }
                        require $button['require'];
                    } 
                ?>
            </li>
        <?php endforeach; ?>
        <li id="logout-btn" onclick="window.location.href='<?php echo URLROOT; ?>/logout'">
            <i class="fas fa-sign-out-alt"></i> <span>Logout</span>
        </li>
        <button class="post-button" style="font-family: 'Poppins', sans-serif;">Post</button>
    </ul>
</div>

<!-- Post Modal -->
<?php require APPROOT . '/views/inc/commponents/newpost_popup.php' ?>


