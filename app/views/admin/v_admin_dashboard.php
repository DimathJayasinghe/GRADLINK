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
            <a class="menu-item active" href="<?php echo URLROOT; ?>/admin/dashboard"><i class="fa fa-chart-line"></i> Overview</a>
            <a class="menu-item" href="<?php echo URLROOT; ?>/admin/users"><i class="fa fa-users"></i> User Management</a>
            <a class="menu-item" href="<?php echo URLROOT; ?>/admin/engagement"><i class="fa fa-chart-bar"></i> Analytics</a>
            <!-- <div class="menu-item" data-section="users"><i class="fa fa-users"></i> Users</div> -->
            <div class="menu-item" data-section="verifications"><i class="fa fa-check"></i> Alumni Verifications</div>
            <div class="menu-item" data-section="posts"><i class="fa fa-pencil-alt"></i> Content Management</div>
            <div class="menu-item" data-section="events"><i class="fa fa-calendar-alt"></i> Event Management</div>
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
        <section id="overview" class="admin-section active">
            <div class="admin-header">
                <h1>System Overview</h1>
                <div class="admin-actions">
                    <button class="btn btn-primary"><i class="fas fa-file-export"></i> Export Data</button>
                    <button class="btn btn-secondary"><i class="fas fa-database"></i> Backup</button>
                    <button class="btn btn-warning"><i class="fas fa-tools"></i> Maintenance</button>
                </div>
            </div>

            <div class="stats-overview">
                <div class="stat-card">
                    <div class="stat-icon"><i class="fas fa-users"></i></div>
                    <div class="stat-info">
                        <h3><?php echo number_format($data['metrics']['total_users'] ?? 0); ?></h3>
                        <p>Total Users</p>
                        <span class="stat-change positive">+<?php echo (int)($data['metrics']['growth_3_months_pct'] ?? 0); ?>% last 3 months</span>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon"><i class="fas fa-user-graduate"></i></div>
                    <div class="stat-info">
                        <h3><?php echo number_format($data['detailed']['alumni'] ?? 0); ?></h3>
                        <p>Alumni</p>
                        <span class="stat-change positive">Active 30d: <?php echo number_format($data['metrics']['active_30_days'] ?? 0); ?></span>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon"><i class="fas fa-book"></i></div>
                    <div class="stat-info">
                        <h3><?php echo number_format($data['detailed']['students'] ?? 0); ?></h3>
                        <p>Students</p>
                        <span class="stat-change positive">+<?php echo number_format($data['detailed']['new_last_7_days'] ?? 0); ?> this week</span>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon"><i class="fas fa-pencil-alt"></i></div>
                    <div class="stat-info">
                        <h3><?php echo number_format($data['detailed']['posts'] ?? 0); ?></h3>
                        <p>Total Posts</p>
                        <span class="stat-change warning">Moderation required</span>
                    </div>
                </div>
            </div>

            <div class="admin-grid">
                <div class="admin-card">
                    <div class="card-header">
                        <h3>Recent System Activity</h3>
                        <button class="btn-small btn-secondary" id="refreshActivity">Refresh</button>
                    </div>
                    <div class="activity-feed">
                        <?php if (!empty($data['activity']['posts'])): ?>
                            <?php foreach ($data['activity']['posts'] as $p): ?>
                                <div class="activity-item">
                                    <div class="activity-icon"><i class="fas fa-pencil-alt"></i></div>
                                    <div class="activity-content">
                                        <p><strong>New post:</strong> <?php echo htmlspecialchars($p->title ?? ''); ?></p>
                                        <span class="activity-time"><?php echo htmlspecialchars($p->created_at ?? ''); ?></span>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="activity-item">
                                <div class="activity-icon"><i class="fas fa-info-circle"></i></div>
                                <div class="activity-content">
                                    <p><em>No recent activity to display</em></p>
                                    <span class="activity-time">System is ready for data</span>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="admin-card">
                    <div class="card-header">
                        <h3>Quick Actions</h3>
                    </div>
                    <div class="quick-actions-grid">
                        <button class="quick-action-btn" data-section="verifications">
                            <span class="action-icon"><i class="fas fa-check-circle"></i></span>
                            <span class="action-text">Review Verifications</span>
                            <span class="action-badge">—</span>
                        </button>
                        <button class="quick-action-btn" data-section="reports">
                            <span class="action-icon"><i class="fas fa-exclamation-triangle"></i></span>
                            <span class="action-text">Review Reports</span>
                            <span class="action-badge">—</span>
                        </button>
                        <button class="quick-action-btn" data-section="posts">
                            <span class="action-icon"><i class="fas fa-bullhorn"></i></span>
                            <span class="action-text">Create Announcement</span>
                        </button>
                        <button class="quick-action-btn" data-section="analytics">
                            <span class="action-icon"><i class="fas fa-chart-line"></i></span>
                            <span class="action-text">View Analytics</span>
                        </button>
                    </div>
                </div>
            </div>

            <div class="admin-card">
                <div class="card-header">
                    <h3>System Health</h3>
                    <span class="status-indicator healthy">All Systems Operational</span>
                </div>
                <div class="health-metrics">
                    <div class="metric"><span class="metric-label">Server Status</span><span class="metric-value healthy">Online</span></div>
                    <div class="metric"><span class="metric-label">Database</span><span class="metric-value healthy">Connected</span></div>
                    <div class="metric"><span class="metric-label">Storage</span><span class="metric-value warning">—</span></div>
                    <div class="metric"><span class="metric-label">Active Sessions</span><span class="metric-value">—</span></div>
                </div>
            </div>
        </section>

        <!-- Placeholder sections to align with preview (non-functional yet) -->
        <!-- <section id="users" class="admin-section">
            <div class="admin-header"><h1>Users</h1></div>
            <div class="admin-card"><div class="card-header"><h3>Queue</h3></div><div style="padding:1.5rem;color:var(--text-secondary)">Coming soon</div></div>
            <?php require APPROOT . '/views/admin/v_users.php'; ?>
        </section> -->
        <section id="verifications" class="admin-section">
            <div class="admin-header"><h1>Alumni Verifications</h1></div>
            <div class="admin-card"><div class="card-header"><h3>Queue</h3></div><div style="padding:1.5rem;color:var(--text-secondary)">Coming soon</div></div>
        </section>
        <section id="posts" class="admin-section">
            <div class="admin-header"><h1>Content Management</h1></div>
            <div class="admin-card"><div class="card-header"><h3>Moderation</h3></div><div style="padding:1.5rem;color:var(--text-secondary)">Coming soon</div></div>
        </section>
        <section id="events" class="admin-section">
            <div class="admin-header"><h1>Event Management</h1></div>
            <div class="admin-card"><div class="card-header"><h3>Events</h3></div><div style="padding:1.5rem;color:var(--text-secondary)">Coming soon</div></div>
        </section>
        <section id="reports" class="admin-section">
            <div class="admin-header"><h1>Reports</h1></div>
            <div class="admin-card"><div class="card-header"><h3>Reporting</h3></div><div style="padding:1.5rem;color:var(--text-secondary)">Coming soon</div></div>
        </section>
        <section id="settings" class="admin-section">
            <div class="admin-header"><h1>System Settings</h1></div>
            <div class="admin-card"><div class="card-header"><h3>Configuration</h3></div><div style="padding:1.5rem;color:var(--text-secondary)">Coming soon</div></div>
        </section>
    </main>
</div>

<script src="<?php echo URLROOT; ?>/js/admin/admin.js"></script>
<?php require APPROOT . '/views/inc/footer.php'; ?>


