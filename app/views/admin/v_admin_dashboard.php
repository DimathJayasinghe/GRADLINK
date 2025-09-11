<?php require APPROOT . '/views/inc/header.php'; ?>
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/admin/admin.css">
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/admin/dashboard.css">

<nav class="dashboard-nav">
    <div class="nav-brand">
        <a href="<?php echo URLROOT; ?>/admin/dashboard" style="text-decoration: none; color: inherit; display: flex; align-items: center; gap: 0.75rem;">
            <div class="logo">
                <img src="<?php echo URLROOT; ?>/img/logo_white.png" alt="GRADLINK">
            </div>
            <h2>GRADLINK</h2>
            <span class="admin-badge">ADMIN</span>
        </a>
    </div>
    <div class="nav-center">
        <div class="search-bar">
            <input type="text" placeholder="Search users, posts, events..." id="adminSearch">
            <button class="search-btn"><i class="fas fa-search"></i></button>
        </div>
    </div>
    <div class="nav-right">
        <button class="notification-btn">
            <i class="fas fa-bell"></i>
        </button>
        <span class="notification-badge">5</span>
        <div class="user-menu">
            <div class="user-avatar">AD</div>
            <span><?php echo htmlspecialchars(SessionManager::getUser()['name'] ?? 'Admin'); ?></span>
            <div class="user-dropdown">
                <a href="<?php echo URLROOT; ?>/adminlogin/logout" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </div>
        </div>
    </div>
    
</nav>

<div class="dashboard-container">
    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="sidebar-menu">
            <div class="menu-item active" data-section="overview"><i class="fa fa-chart-line"></i> Overview</div>
            <div class="menu-item" data-section="users"><i class="fa fa-users"></i> Users Management</div>
            <a class="menu-item" data-section="analytics"><i class="fa fa-chart-bar"></i> Analytics</a>
            <div class="menu-item" data-section="verifications"><i class="fa fa-check"></i> Alumni Verifications</div>
            <div class="menu-item" data-section="posts"><i class="fa fa-pencil-alt"></i> Content Management</div>
            <div class="menu-item" data-section="events"><i class="fa fa-calendar-alt"></i> Event Management</div>
            <div class="menu-item" data-section="fundraiser"><i class="fa fa-dollar-sign"></i> Fundraiser Management</div>
            <div class="menu-item" data-section="reports"><i class="fa fa-file-alt"></i> Reports</div>
            <div class="menu-item" data-section="settings"><i class="fa fa-cog"></i> System Settings</div>
        </div>
    </aside>

    <!-- Main content -->
    <main class="main-content">
        <!-- Flash Messages -->
        <?php 
        $flashMessages = SessionManager::getFlash();
        if (!empty($flashMessages)): ?>
            <div class="flash-messages">
                <?php foreach ($flashMessages as $message): ?>
                    <div class="flash-message <?php echo $message['type']; ?>">
                        <?php echo htmlspecialchars($message['message']); ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <!-- Overview -->
            <!-- Modular sections for each menu item -->
            <section id="overview" class="admin-section active">
                <?php 
                if (file_exists(APPROOT . '/views/admin/v_overview.php')) {
                    require APPROOT . '/views/admin/v_overview.php';
                } else {
                    echo '<div class="admin-header"><h1>Overview</h1></div><div class="admin-card"><div class="card-header"><h3>Overview Content</h3></div><div style="padding:1.5rem;color:var(--text-secondary)">Coming soon</div></div>';
                }
                ?>
            </section>
            <section id="users" class="admin-section">
                <?php 
                if (file_exists(APPROOT . '/views/admin/v_users.php')) {
                    require APPROOT . '/views/admin/v_users.php';
                } else {
                    echo '<div class="admin-header"><h1>Users Management</h1></div><div class="admin-card"><div class="card-header"><h3>Users</h3></div><div style="padding:1.5rem;color:var(--text-secondary)">Coming soon</div></div>';
                }
                ?>
            </section>
            <section id="analytics" class="admin-section">
                <?php 
                if (file_exists(APPROOT . '/views/admin/v_engagement.php')) {
                    require APPROOT . '/views/admin/v_engagement.php';
                } else {
                    echo '<div class="admin-header"><h1>Analytics</h1></div><div class="admin-card"><div class="card-header"><h3>Engagement</h3></div><div style="padding:1.5rem;color:var(--text-secondary)">Coming soon</div></div>';
                }
                ?>
            </section>
            <section id="verifications" class="admin-section">
                <?php require APPROOT . '/views/admin/v_verifications.php'; ?>
            </section>
            <section id="posts" class="admin-section">
                <!-- Create v_posts.php for this section -->
                <?php if (file_exists(APPROOT . '/views/admin/v_posts.php')) require APPROOT . '/views/admin/v_posts.php'; else echo '<div class="admin-header"><h1>Content Management</h1></div><div class="admin-card"><div class="card-header"><h3>Moderation</h3></div><div style="padding:1.5rem;color:var(--text-secondary)">Coming soon</div></div>'; ?>
            </section>
            <section id="events" class="admin-section">
                <!-- Create v_events.php for this section -->
                <?php if (file_exists(APPROOT . '/views/admin/v_events.php')) require APPROOT . '/views/admin/v_events.php'; else echo '<div class="admin-header"><h1>Event Management</h1></div><div class="admin-card"><div class="card-header"><h3>Events</h3></div><div style="padding:1.5rem;color:var(--text-secondary)">Coming soon</div></div>'; ?>
            </section>
            <section id="fundraiser" class="admin-section">
                <!-- Create v_fundraiser.php for this section -->
                <?php if (file_exists(APPROOT . '/views/admin/v_fundraiser.php')) require APPROOT . '/views/admin/v_fundraiser.php'; else echo '<div class="admin-header"><h1>Fundraiser Management</h1></div><div class="admin-card"><div class="card-header"><h3>Fundraisers</h3></div><div style="padding:1.5rem;color:var(--text-secondary)">Coming soon</div></div>'; ?>
            </section>
            <section id="reports" class="admin-section">
                <?php require APPROOT . '/views/admin/v_reports.php'; ?>
            </section>
            <section id="settings" class="admin-section">
                <!-- Create v_settings.php for this section -->
                <?php if (file_exists(APPROOT . '/views/admin/v_settings.php')) require APPROOT . '/views/admin/v_settings.php'; else echo '<div class="admin-header"><h1>System Settings</h1></div><div class="admin-card"><div class="card-header"><h3>Configuration</h3></div><div style="padding:1.5rem;color:var(--text-secondary)">Coming soon</div></div>'; ?>
            </section>
    </main>
</div>

<script src="<?php echo URLROOT; ?>/js/admin/admin.js"></script>
</script>
<?php require APPROOT . '/views/inc/footer.php'; ?>


