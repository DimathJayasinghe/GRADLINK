<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/admin/common.css">
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/admin/dashboard-common.css">
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/admin/dashboard-overview.css">
<?php ob_start(); ?>
<style>
    /* Smaller stat cards */
    .stats-overview {
        grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
        gap: 0.5rem;
    }
    .stat-card {
        padding: 0.6rem 0.75rem;
    }
    .stat-card .stat-icon {
        width: 32px;
        height: 32px;
        font-size: 0.8rem;
    }
    .stat-card .stat-info h3 {
        font-size: 1.2rem;
        margin-bottom: 0.1rem;
    }
    .stat-card .stat-info p {
        font-size: 0.7rem;
        margin-bottom: 0;
    }
    /* Reports card highlight */
    .stat-card.reports-card {
        border-left: 3px solid #dc3545;
    }
    .stat-card.reports-card .stat-icon {
        background: rgba(220, 53, 69, 0.1);
        color: #dc3545;
    }
    /* Online users card */
    .stat-card.online-card {
        border-left: 3px solid #28a745;
    }
    .stat-card.online-card .stat-icon {
        background: rgba(40, 167, 69, 0.1);
        color: #28a745;
    }
    .online-count-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 0.2rem 0.6rem;
        border-radius: 999px;
        background: rgba(40, 167, 69, 0.12);
        color: #28a745;
        font-size: 0.75rem;
        font-weight: 700;
        white-space: nowrap;
    }
    .online-users-preview {
        display: flex;
        flex-wrap: wrap;
        gap: 0.55rem;
        padding: 0.75rem 0.9rem 0.15rem;
    }
    .online-user-chip {
        display: inline-flex;
        align-items: center;
        gap: 0.55rem;
        padding: 0.38rem 0.6rem;
        border-radius: 999px;
        border: 1px solid var(--border, #e9ecef);
        background: var(--bg-secondary, #f8f9fa);
        min-width: 0;
        max-width: 100%;
    }
    .online-user-avatar {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        object-fit: cover;
        background: #ddd;
        flex: 0 0 auto;
    }
    .online-user-chip-name {
        font-weight: 500;
        font-size: 0.82rem;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        min-width: 0;
    }
    .online-user-more {
        font-weight: 700;
        color: #28a745;
    }
    .online-users-footer {
        display: flex;
        justify-content: flex-end;
        padding: 0 0.9rem 0.9rem;
    }
    .online-users-view-btn {
        border: 1px solid rgba(40, 167, 69, 0.25);
        background: rgba(40, 167, 69, 0.08);
        color: #28a745;
        border-radius: 999px;
        padding: 0.42rem 0.8rem;
        font-weight: 700;
        font-size: 0.78rem;
        cursor: pointer;
    }
    .online-users-view-btn:hover {
        background: rgba(40, 167, 69, 0.14);
    }
    .online-users-modal-content {
        max-width: 720px;
    }
    .admin-modal {
        display: none;
        position: fixed;
        z-index: 1000;
        inset: 0;
        overflow: auto;
        background: rgba(0, 0, 0, 0.45);
    }
    .admin-modal-content {
        background: var(--card);
        margin: 6% auto;
        padding: 1.5rem;
        border-radius: 1rem;
        width: 92%;
        max-width: 500px;
        position: relative;
        box-shadow: var(--shadow-4);
    }
    .admin-modal-close {
        position: absolute;
        top: 1rem;
        right: 1rem;
        font-size: 1.5rem;
        cursor: pointer;
        line-height: 1;
    }
    .online-users-modal-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 0.75rem;
        margin-bottom: 1rem;
    }
    .online-users-modal-list {
        display: grid;
        gap: 0.65rem;
        max-height: 60vh;
        overflow-y: auto;
        padding-right: 0.25rem;
    }
    .online-user-modal-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 0.75rem;
        padding: 0.7rem 0.85rem;
        border: 1px solid var(--border, #e9ecef);
        border-radius: 14px;
        background: var(--bg-secondary, #f8f9fa);
    }
    .online-user-modal-meta {
        font-size: 0.72rem;
        color: #666;
        margin-top: 0.15rem;
    }
    .online-users-empty {
        padding: 0.9rem 0.9rem 1rem;
        color: #666;
    }
    .online-indicator {
        width: 8px;
        height: 8px;
        background: #28a745;
        border-radius: 50%;
        animation: pulse 2s infinite;
    }
    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.5; }
    }
    /* System updates table */
    .activity-log-table {
        width: 100%;
        font-size: 0.75rem;
    }
    .activity-log-table th, .activity-log-table td {
        padding: 0.4rem 0.5rem;
        text-align: left;
        border-bottom: 1px solid var(--border, #eee);
    }
    .activity-log-table th {
        font-weight: 600;
    }
    .commit-hash {
        display: inline-block;
        padding: 0.15rem 0.45rem;
        border-radius: 999px;
        font-size: 0.68rem;
        font-weight: 700;
        background: #eef3ff;
        color: #2f4f9d;
        letter-spacing: 0.02em;
    }
    .admin-grid-3 {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 1rem;
        margin-top: 1rem;
    }
    /* System Health Styles */
    .health-metrics {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
        gap: 0.75rem;
        padding: 0.75rem;
    }
    .health-metrics .metric {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.5rem 0.75rem;
        background: var(--bg-secondary, #f8f9fa);
        border-radius: 6px;
        font-size: 0.8rem;
    }
    .health-metrics .metric-label {
        color: #666;
        font-weight: 500;
    }
    .health-metrics .metric-value {
        font-weight: 600;
    }
    .health-metrics .metric-value.healthy {
        color: #28a745;
    }
    .health-metrics .metric-value.warning {
        color: #ffc107;
    }
    .health-metrics .metric-value.error {
        color: #dc3545;
    }
    .status-indicator {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        padding: 0.25rem 0.75rem;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 500;
    }
    .status-indicator.healthy {
        background: rgba(40, 167, 69, 0.1);
        color: #28a745;
    }
    .status-indicator.healthy::before {
        content: '';
        width: 8px;
        height: 8px;
        background: #28a745;
        border-radius: 50%;
        animation: pulse 2s infinite;
    }
    .status-indicator.warning {
        background: rgba(255, 193, 7, 0.1);
        color: #856404;
    }
    .status-indicator.error {
        background: rgba(220, 53, 69, 0.1);
        color: #dc3545;
    }
</style>
<?php $styles = ob_get_clean(); ?>

<?php
    $sidebar_left = [
        ['label'=>'Overview', 'url'=>'/admin','active'=>true, 'icon'=>'tachometer-alt'],
        ['label'=>'User Management', 'url'=>'/admin/users','active'=>false, 'icon' => 'users'],
        ['label'=>'Engagement Metrics', 'url'=>'/admin/engagement','active'=>false, 'icon' => 'chart-bar'],
        ['label'=>'Reports', 'url'=>'/admin/reports','active'=>false, 'icon' => 'fas fa-exclamation-triangle'],
        ['label'=>'Event Moderation', 'url'=>'/admin/eventrequests','active'=>false, 'icon' => 'clipboard-list'],
        ['label'=>'Content Management', 'url'=>'/admin/posts','active'=>false, 'icon' => 'pencil-alt'],
        ['label'=>'Fundraisers', 'url'=>'/admin/fundraisers','active'=>false, 'icon' => 'donate'],
        ['label'=>'Alumni Verifications', 'url'=>'/admin/verifications','active'=>false, 'icon' => 'check-circle'],
        ['label'=>'Suspended Users', 'url'=>'/admin/suspendedUsers','active'=>false, 'icon' => 'user-slash'],
        ['label'=>'Help & Support', 'url'=>'/admin/support','active'=>false, 'icon' => 'circle-question']
    ]
?>

<?php
    $onlineUsers = $data['online_users'] ?? [];
    $onlineCount = (int)($data['online_count'] ?? count($onlineUsers));
    $onlinePreview = array_slice($onlineUsers, 0, 5);
    $onlineExtra = max(0, $onlineCount - count($onlinePreview));
?>

<?php ob_start();?>
<div class="admin-header">
    <h1>System Overview</h1>
</div>

<div class="stats-overview">
    <div class="stat-card online-card">
        <div class="stat-icon"><i class="fas fa-circle"></i></div>
        <div class="stat-info">
            <h3><?php echo number_format($data['online_count'] ?? 0); ?></h3>
            <p>Online Now</p>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon"><i class="fas fa-users"></i></div>
        <div class="stat-info">
            <h3><?php echo number_format($data['metrics']['total_users'] ?? 0); ?></h3>
            <p>Total Users</p>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon"><i class="fas fa-user-plus"></i></div>
        <div class="stat-info">
            <h3><?php echo number_format($data['detailed']['new_last_7_days'] ?? 0); ?></h3>
            <p>New Users (7d)</p>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon"><i class="fas fa-chart-line"></i></div>
        <div class="stat-info">
            <h3><?php echo number_format($data['metrics']['active_30_days'] ?? 0); ?></h3>
            <p>Active Users (30d)</p>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon"><i class="fas fa-pencil-alt"></i></div>
        <div class="stat-info">
            <h3><?php echo number_format($data['detailed']['posts'] ?? 0); ?></h3>
            <p>Total Posts</p>
        </div>
    </div>
    <div class="stat-card reports-card">
        <div class="stat-icon"><i class="fas fa-exclamation-triangle"></i></div>
        <div class="stat-info">
            <h3><?php echo number_format($data['reports']['pending'] ?? 7); ?></h3>
            <p>Content Reports</p>
        </div>
    </div>
</div>

<div class="admin-grid-3">
    <!-- Online Users Panel -->
    <div class="admin-card">
        <div class="card-header">
            <h3><i class="fas fa-circle" style="color: #28a745; font-size: 0.6rem;"></i> Online Users</h3>
            <span class="online-count-badge"><?php echo number_format($onlineCount); ?> online</span>
        </div>
        <div class="online-users-preview">
            <?php if (!empty($onlinePreview)): ?>
                <?php foreach ($onlinePreview as $user): ?>
                    <?php
                        $displayName = trim((string)($user->display_name ?? '')) !== '' ? $user->display_name : ('User #' . (int)($user->id ?? 0));
                        $profileImage = !empty($user->profile_image) ? basename($user->profile_image) : 'default.png';
                        $profileImageEncoded = rawurlencode($profileImage);
                    ?>
                    <div class="online-user-chip" title="<?php echo htmlspecialchars($displayName); ?>">
                        <img src="<?php echo URLROOT; ?>/media/profile/<?php echo $profileImageEncoded; ?>"
                             alt="<?php echo htmlspecialchars($displayName); ?>"
                             class="online-user-avatar"
                             onerror="this.src='<?php echo URLROOT; ?>/media/profile/default.png';">
                        <span class="online-user-chip-name"><?php echo htmlspecialchars($displayName); ?></span>
                    </div>
                <?php endforeach; ?>
                <?php if ($onlineExtra > 0): ?>
                    <div class="online-user-chip online-user-more">+<?php echo number_format($onlineExtra); ?> more</div>
                <?php endif; ?>
            <?php else: ?>
                <p class="online-users-empty">No users currently online</p>
            <?php endif; ?>
        </div>
        <?php if ($onlineCount > 5): ?>
            <div class="online-users-footer">
                <button type="button" class="online-users-view-btn" id="openOnlineUsersModal">View All</button>
            </div>
        <?php endif; ?>
    </div>

    <!-- Recent System Updates -->
    <div class="admin-card" style="grid-column: span 2;">
        <div class="card-header" style="flex-wrap: wrap; gap: 0.5rem;">
            <h3><i class="fas fa-code-branch"></i> Recent System Updates</h3>
            <span class="online-count-badge">
                Source: <?php echo htmlspecialchars($data['system_updates_ref'] ?? 'dev'); ?>
            </span>
        </div>

        <div style="max-height: 300px; overflow-y: auto;">
            <table class="activity-log-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Commit</th>
                        <th>Author</th>
                        <th>Message</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($data['system_updates'])): ?>
                        <?php foreach ($data['system_updates'] as $update): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($update['date'] ?? ''); ?></td>
                                <td><span class="commit-hash"><?php echo htmlspecialchars($update['hash'] ?? ''); ?></span></td>
                                <td><?php echo htmlspecialchars($update['author'] ?? ''); ?></td>
                                <td style="max-width: 320px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                    <?php echo htmlspecialchars($update['message'] ?? ''); ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" style="text-align: center; padding: 1rem; color: #666;">
                                <?php echo htmlspecialchars($data['system_updates_error'] ?? 'No recent system updates available.'); ?>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div id="onlineUsersModal" class="admin-modal">
    <div class="admin-modal-content online-users-modal-content">
        <span class="admin-modal-close" id="closeOnlineUsersModal">&times;</span>
        <div class="online-users-modal-header">
            <h3 style="margin: 0;">All Online Users</h3>
            <span class="online-count-badge"><?php echo number_format($onlineCount); ?> online</span>
        </div>
        <div class="online-users-modal-list">
            <?php if (!empty($onlineUsers)): ?>
                <?php foreach ($onlineUsers as $user): ?>
                    <?php
                        $displayName = trim((string)($user->display_name ?? '')) !== '' ? $user->display_name : ('User #' . (int)($user->id ?? 0));
                        $profileImage = !empty($user->profile_image) ? basename($user->profile_image) : 'default.png';
                        $profileImageEncoded = rawurlencode($profileImage);
                        $lastActivity = !empty($user->last_activity) ? date('d M Y, H:i', strtotime($user->last_activity)) : 'Just now';
                    ?>
                    <div class="online-user-modal-item">
                        <div style="display:flex;align-items:center;gap:0.75rem;min-width:0;">
                            <img src="<?php echo URLROOT; ?>/media/profile/<?php echo $profileImageEncoded; ?>"
                                 alt="<?php echo htmlspecialchars($displayName); ?>"
                                 class="online-user-avatar"
                                 onerror="this.src='<?php echo URLROOT; ?>/media/profile/default.png';">
                            <div style="min-width:0;">
                                <div class="online-user-chip-name"><?php echo htmlspecialchars($displayName); ?></div>
                                <div class="online-user-modal-meta">Last activity: <?php echo htmlspecialchars($lastActivity); ?></div>
                            </div>
                        </div>
                        <span class="online-count-badge">Online</span>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="online-users-empty" style="margin:0;">No users currently online</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const onlineModal = document.getElementById('onlineUsersModal');
    const openOnlineButton = document.getElementById('openOnlineUsersModal');
    const closeOnlineButton = document.getElementById('closeOnlineUsersModal');

    if (openOnlineButton && onlineModal) {
        openOnlineButton.addEventListener('click', function() {
            onlineModal.style.display = 'block';
        });
    }

    if (closeOnlineButton && onlineModal) {
        closeOnlineButton.addEventListener('click', function() {
            onlineModal.style.display = 'none';
        });
    }

    window.addEventListener('click', function(event) {
        if (event.target === onlineModal) {
            onlineModal.style.display = 'none';
        }
    });
});
</script>
<?php $content = ob_get_clean(); ?>
<?php require APPROOT . '/views/admin/dashboard_layout.php'; ?>