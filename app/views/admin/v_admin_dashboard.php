<?php require APPROOT . '/views/inc/header.php'; ?>
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/admin/admin.css">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<!-- Top navigation -->
<nav class="dashboard-nav">
    <div class="nav-brand">
        <a href="<?php echo URLROOT; ?>/admin/dashboard" style="text-decoration: none; color: inherit; display: flex; align-items: center; gap: 0.5rem;">
            <div class="nav-logo">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M12 2L2 7L12 12L22 7L12 2Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M2 17L12 22L22 17" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M2 12L12 17L22 12" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
            <span>GRADLINK</span>
            <span class="admin-badge">ADMIN</span>
        </a>
    </div>
    <div class="nav-center">
        <div class="search-bar">
            <input type="text" placeholder="Search users, posts, events..." id="adminSearch">
            <button class="search-btn">🔍</button>
        </div>
    </div>
    <div class="nav-right">
        <button class="notification-btn">🔔 <span class="badge">5</span></button>
        <div class="user-menu">
            <div class="user-avatar">AD</div>
            <span><?php echo htmlspecialchars(SessionManager::getUser()['name'] ?? 'Admin'); ?></span>
            <div class="user-dropdown">
                <a href="<?php echo URLROOT; ?>/adminlogin/logout" class="logout-btn">🚪 Logout</a>
            </div>
        </div>
    </div>
    
</nav>

<div class="dashboard-container">
    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="sidebar-menu">
            <a class="menu-item active" href="<?php echo URLROOT; ?>/admin/dashboard">📊 Overview</a>
            <a class="menu-item" href="<?php echo URLROOT; ?>/admin/users">👥 User Management</a>
            <a class="menu-item" href="<?php echo URLROOT; ?>/admin/engagement">📈 Analytics</a>
            <div class="menu-item" data-section="verifications">✅ Alumni Verifications</div>
            <div class="menu-item" data-section="posts">📝 Content Management</div>
            <div class="menu-item" data-section="events">📅 Event Management</div>
            <div class="menu-item" data-section="reports">📋 Reports</div>
            <div class="menu-item" data-section="settings">⚙️ System Settings</div>
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
                    <button class="btn btn-primary">📊 Export Data</button>
                    <button class="btn btn-secondary">💾 Backup</button>
                    <button class="btn btn-warning">🔧 Maintenance</button>
                </div>
            </div>

            <div class="stats-overview">
                <div class="stat-card">
                    <div class="stat-icon">👥</div>
                    <div class="stat-info">
                        <h3><?php echo number_format($data['metrics']['total_users'] ?? 0); ?></h3>
                        <p>Total Users</p>
                        <span class="stat-change positive">+<?php echo (int)($data['metrics']['growth_3_months_pct'] ?? 0); ?>% last 3 months</span>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">🎓</div>
                    <div class="stat-info">
                        <h3><?php echo number_format($data['detailed']['alumni'] ?? 0); ?></h3>
                        <p>Alumni</p>
                        <span class="stat-change positive">Active 30d: <?php echo number_format($data['metrics']['active_30_days'] ?? 0); ?></span>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">📚</div>
                    <div class="stat-info">
                        <h3><?php echo number_format($data['detailed']['students'] ?? 0); ?></h3>
                        <p>Students</p>
                        <span class="stat-change positive">+<?php echo number_format($data['detailed']['new_last_7_days'] ?? 0); ?> this week</span>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">📝</div>
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
                                    <div class="activity-icon">📝</div>
                                    <div class="activity-content">
                                        <p><strong>New post:</strong> <?php echo htmlspecialchars($p->title ?? ''); ?></p>
                                        <span class="activity-time"><?php echo htmlspecialchars($p->created_at ?? ''); ?></span>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="activity-item">
                                <div class="activity-icon">ℹ️</div>
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
                            <span class="action-icon">✅</span>
                            <span class="action-text">Review Verifications</span>
                            <span class="action-badge">—</span>
                        </button>
                        <button class="quick-action-btn" data-section="reports">
                            <span class="action-icon">⚠️</span>
                            <span class="action-text">Review Reports</span>
                            <span class="action-badge">—</span>
                        </button>
                        <button class="quick-action-btn" data-section="posts">
                            <span class="action-icon">📢</span>
                            <span class="action-text">Create Announcement</span>
                        </button>
                        <button class="quick-action-btn" data-section="analytics">
                            <span class="action-icon">📊</span>
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


