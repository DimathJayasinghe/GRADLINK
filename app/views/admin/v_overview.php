<!-- <link rel="stylesheet" href="<?php echo URLROOT; ?>/css/admin/admin.css">    -->
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/admin/dashboard.css">
<?php ob_start(); ?>
<?php $styles = ob_get_clean(); ?>

<?php
    $sidebar_left = [
        ['label'=>'Overview', 'url'=>'/admin','active'=>true, 'icon'=>'tachometer-alt'],
        ['label'=>'User Management', 'url'=>'/admin/users','active'=>false, 'icon' => 'users'],
        ['label'=>'Engagement Metrics', 'url'=>'/admin/engagement','active'=>false, 'icon' => 'chart-bar'],
        ['label'=>'Reports', 'url'=>'/admin/reports','active'=>false, 'icon' => 'file-alt'],
        ['label'=>'Content Management', 'url'=>'/admin/posts','active'=>false, 'icon' => 'pencil-alt'],
        ['label'=>'Fundraisers', 'url'=>'/admin/fundraisers','active'=>false, 'icon' => 'donate'],
        ['label'=>'Alumni Verifications', 'url'=>'/admin/verifications','active'=>false, 'icon' => 'check-circle']
    ]
?>

<?php ob_start();?>
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
<?php $content = ob_get_clean(); ?>
<?php require APPROOT . '/views/admin/dashboard_layout.php'; ?>