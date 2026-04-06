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
    /* Online users list */
    .online-users-list {
        max-height: 300px;
        overflow-y: auto;
    }
    .online-user-item {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.5rem 0;
        border-bottom: 1px solid var(--border, #eee);
    }
    .online-user-item:last-child {
        border-bottom: none;
    }
    .online-user-avatar {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        object-fit: cover;
        background: #ddd;
    }
    .online-user-info {
        flex: 1;
        min-width: 0;
    }
    .online-user-name {
        font-weight: 500;
        font-size: 0.85rem;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .online-user-page {
        font-size: 0.7rem;
        color: #666;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
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
    /* Activity log table */
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
    .method-badge {
        display: inline-block;
        padding: 0.15rem 0.4rem;
        border-radius: 3px;
        font-size: 0.65rem;
        font-weight: 600;
    }
    .method-get { background: #d4edda; color: #155724; }
    .method-post { background: #cce5ff; color: #004085; }
    .method-put { background: #fff3cd; color: #856404; }
    .method-delete { background: #f8d7da; color: #721c24; }
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
        ['label'=>'Alumni Verifications', 'url'=>'/admin/verifications','active'=>false, 'icon' => 'check-circle']
    ]
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
        <div class="stat-icon"><i class="fas fa-eye"></i></div>
        <div class="stat-info">
            <h3><?php echo number_format($data['access_stats']['page_views_today'] ?? 0); ?></h3>
            <p>Page Views Today</p>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon"><i class="fas fa-user-check"></i></div>
        <div class="stat-info">
            <h3><?php echo number_format($data['access_stats']['unique_visitors_today'] ?? 0); ?></h3>
            <p>Visitors Today</p>
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
            <h3><i class="fas fa-circle" style="color: #28a745; font-size: 0.6rem;"></i> Online Users (<?php echo $data['online_count'] ?? 0; ?>)</h3>
        </div>
        <div class="online-users-list" style="padding: 0.5rem;">
            <?php if (!empty($data['online_users'])): ?>
                <?php foreach ($data['online_users'] as $user): ?>
                    <div class="online-user-item">
                        <div class="online-indicator"></div>
                        <?php if (!empty($user->profile_image)): ?>
                            <img src="<?php echo URLROOT; ?>/media/profile/<?php echo basename($user->profile_image); ?>" 
                                 alt="<?php echo htmlspecialchars($user->name); ?>" class="online-user-avatar"
                                 onerror="this.src='<?php echo URLROOT; ?>/media/profile/default.png';">
                        <?php else: ?>
                            <div class="online-user-avatar" style="display: flex; align-items: center; justify-content: center; background: #667eea; color: white; font-size: 0.8rem; font-weight: 600;">
                                <?php echo strtoupper(substr($user->name ?? 'U', 0, 1)); ?>
                            </div>
                        <?php endif; ?>
                        <div class="online-user-info">
                            <div class="online-user-name"><?php echo htmlspecialchars($user->display_name ?? $user->name); ?></div>
                            <div class="online-user-page"><?php echo htmlspecialchars($user->current_url ?? '/'); ?></div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p style="text-align: center; color: #666; padding: 1rem; font-size: 0.85rem;">No users currently online</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Recent Activity Log -->
    <div class="admin-card" style="grid-column: span 2;">
        <div class="card-header" style="flex-wrap: wrap; gap: 0.5rem;">
            <h3><i class="fas fa-history"></i> Recent Activity</h3>
            <div style="display: flex; gap: 0.5rem; align-items: center; flex-wrap: wrap;">
                <!-- User Search -->
                <input type="text" id="userSearchInput" placeholder="Search user..." 
                       style="padding: 0.3rem 0.5rem; font-size: 0.75rem; border: 1px solid #ddd; border-radius: 4px; width: 120px;">
                <!-- View Mode Toggle -->
                <select id="activityViewMode" style="padding: 0.3rem 0.5rem; font-size: 0.75rem; border: 1px solid #ddd; border-radius: 4px;">
                    <option value="all">Show All</option>
                    <option value="byUser">Group by User</option>
                    <option value="byTime">Group by Hour</option>
                    <option value="usersOnly">Users Only</option>
                </select>
            </div>
        </div>
        
        <!-- All Logs View (default) -->
        <div id="allLogsView" style="max-height: 300px; overflow-y: auto;">
            <table class="activity-log-table" id="activityTable">
                <thead>
                    <tr>
                        <th>Time</th>
                        <th>User</th>
                        <th>Method</th>
                        <th>URL</th>
                        <th>IP</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($data['recent_logs'])): ?>
                        <?php foreach ($data['recent_logs'] as $log): ?>
                            <tr data-user="<?php echo htmlspecialchars(strtolower($log->user_name ?? '')); ?>" 
                                data-role="<?php echo htmlspecialchars(strtolower($log->user_role ?? '')); ?>"
                                data-isguest="<?php echo empty($log->user_name) ? '1' : '0'; ?>"
                                data-hour="<?php echo date('H', strtotime($log->created_at)); ?>">
                                <td><?php echo date('H:i:s', strtotime($log->created_at)); ?></td>
                                <td>
                                    <?php if (!empty($log->user_name)): ?>
                                        <span title="<?php echo htmlspecialchars($log->user_role ?? ''); ?>">
                                            <?php echo htmlspecialchars($log->user_name); ?>
                                            <?php if (!empty($log->user_role)): ?>
                                                <small style="color: #888; font-size: 0.65rem;">(<?php echo htmlspecialchars($log->user_role); ?>)</small>
                                            <?php endif; ?>
                                        </span>
                                    <?php elseif ($log->user_id): ?>
                                        User #<?php echo $log->user_id; ?>
                                    <?php else: ?>
                                        <em style="color: #999;">Guest</em>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="method-badge method-<?php echo strtolower($log->method); ?>">
                                        <?php echo htmlspecialchars($log->method); ?>
                                    </span>
                                </td>
                                <td style="max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                    <?php echo htmlspecialchars($log->url); ?>
                                </td>
                                <td><?php echo htmlspecialchars($log->ip_address); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" style="text-align: center; padding: 1rem; color: #666;">No activity logs yet</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <!-- Grouped View (hidden by default) -->
        <div id="groupedView" style="display: none; max-height: 300px; overflow-y: auto; padding: 0.5rem;">
            <div id="groupedContent"></div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const userSearch = document.getElementById('userSearchInput');
    const viewMode = document.getElementById('activityViewMode');
    const allLogsView = document.getElementById('allLogsView');
    const groupedView = document.getElementById('groupedView');
    const groupedContent = document.getElementById('groupedContent');
    const tableRows = document.querySelectorAll('#activityTable tbody tr[data-user]');
    
    // Convert NodeList to array for processing
    const logs = Array.from(tableRows).map(row => ({
        element: row,
        user: row.dataset.user,
        role: row.dataset.role,
        isGuest: row.dataset.isguest === '1',
        hour: row.dataset.hour,
        time: row.cells[0].textContent,
        userName: row.cells[1].textContent.trim(),
        method: row.cells[2].textContent.trim(),
        url: row.cells[3].textContent.trim(),
        ip: row.cells[4].textContent.trim()
    }));
    
    function filterAndDisplay() {
        const searchTerm = userSearch.value.toLowerCase();
        const mode = viewMode.value;
        
        // Reset visibility
        tableRows.forEach(row => row.style.display = '');
        
        if (mode === 'all') {
            allLogsView.style.display = 'block';
            groupedView.style.display = 'none';
            
            // Apply search filter
            tableRows.forEach(row => {
                const user = row.dataset.user;
                const role = row.dataset.role;
                if (searchTerm && !user.includes(searchTerm) && !role.includes(searchTerm)) {
                    row.style.display = 'none';
                }
            });
            
        } else if (mode === 'usersOnly') {
            allLogsView.style.display = 'block';
            groupedView.style.display = 'none';
            
            // Show only logged-in users (not guests)
            tableRows.forEach(row => {
                const isGuest = row.dataset.isguest === '1';
                const user = row.dataset.user;
                if (isGuest || (searchTerm && !user.includes(searchTerm))) {
                    row.style.display = 'none';
                }
            });
            
        } else if (mode === 'byUser') {
            allLogsView.style.display = 'none';
            groupedView.style.display = 'block';
            
            // Group by user
            const groups = {};
            logs.forEach(log => {
                const key = log.isGuest ? 'Guests' : (log.userName || 'Unknown');
                if (!groups[key]) groups[key] = [];
                if (!searchTerm || log.user.includes(searchTerm)) {
                    groups[key].push(log);
                }
            });
            
            renderGroups(groups, 'user');
            
        } else if (mode === 'byTime') {
            allLogsView.style.display = 'none';
            groupedView.style.display = 'block';
            
            // Group by hour
            const groups = {};
            logs.forEach(log => {
                const hourLabel = log.hour + ':00 - ' + log.hour + ':59';
                if (!groups[hourLabel]) groups[hourLabel] = [];
                if (!searchTerm || log.user.includes(searchTerm)) {
                    groups[hourLabel].push(log);
                }
            });
            
            renderGroups(groups, 'time');
        }
    }
    
    function renderGroups(groups, type) {
        let html = '';
        const sortedKeys = Object.keys(groups).sort((a, b) => {
            if (type === 'time') return b.localeCompare(a);
            return groups[b].length - groups[a].length;
        });
        
        sortedKeys.forEach(key => {
            const items = groups[key];
            if (items.length === 0) return;
            
            html += `<div style="margin-bottom: 1rem; border-radius: 6px; padding: 0.5rem;">
                <div style="font-weight: 600; font-size: 0.85rem; margin-bottom: 0.5rem; display: flex; justify-content: space-between; align-items: center;">
                    <span>${key}</span>
                    <span style="background: #667eea; color: white; padding: 0.15rem 0.5rem; border-radius: 12px; font-size: 0.7rem;">${items.length} requests</span>
                </div>
                <div style="font-size: 0.75rem; color: #666;">`;
            
            items.slice(0, 5).forEach(log => {
                html += `<div style="padding: 0.2rem 0; border-bottom: 1px solid #eee;">
                    <span style="color: #888;">${log.time}</span> - 
                    <span class="method-badge method-${log.method.toLowerCase()}">${log.method}</span>
                    <span style="max-width: 150px; display: inline-block; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; vertical-align: bottom;">${log.url}</span>
                </div>`;
            });
            
            if (items.length > 5) {
                html += `<div style="color: #999; font-style: italic; padding-top: 0.25rem;">...and ${items.length - 5} more</div>`;
            }
            
            html += '</div></div>';
        });
        
        groupedContent.innerHTML = html || '<p style="text-align: center; color: #666;">No matching activity</p>';
    }
    
    userSearch.addEventListener('input', filterAndDisplay);
    viewMode.addEventListener('change', filterAndDisplay);
});
</script>
<?php $content = ob_get_clean(); ?>
<?php require APPROOT . '/views/admin/dashboard_layout.php'; ?>