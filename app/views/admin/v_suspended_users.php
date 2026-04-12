<?php ob_start() ?>
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/admin/common.css">
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/admin/dashboard-common.css">
<style>
    .admin-card {
        margin-bottom: 1rem;
    }
    .admin-table-wrapper {
        overflow-x: auto;
    }
    .admin-table {
        width: 100%;
        border-collapse: collapse;
    }
    .admin-table th,
    .admin-table td {
        border-bottom: 1px solid #3a3a3a;
        padding: 0.7rem;
        text-align: left;
        vertical-align: top;
    }
    .admin-table th {
        font-weight: 700;
        color: #f3f3f3;
    }
    .actions-row {
        display: flex;
        gap: 0.5rem;
        flex-wrap: wrap;
    }
    .admin-btn {
        border: none;
        border-radius: 6px;
        padding: 0.45rem 0.75rem;
        cursor: pointer;
        font-weight: 600;
        background: #2f7ef7;
        color: #fff;
    }
    .admin-btn-danger {
        background: #c92a2a;
    }
    .status-pill {
        display: inline-block;
        padding: 0.15rem 0.6rem;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 600;
    }
    .status-pill.active {
        background: rgba(220, 53, 69, 0.2);
        color: #ff7f8a;
    }
    .status-pill.lifted {
        background: rgba(40, 167, 69, 0.2);
        color: #7ee79a;
    }
    .status-pill.removed {
        background: rgba(255, 193, 7, 0.2);
        color: #ffd666;
    }
    .reason {
        max-width: 320px;
        white-space: pre-wrap;
        word-break: break-word;
    }
    .flash {
        border-radius: 8px;
        padding: 10px 12px;
        margin-bottom: 10px;
        font-size: 14px;
    }
    .flash-success { background: rgba(40, 167, 69, 0.2); color: #86efac; }
    .flash-error { background: rgba(220, 53, 69, 0.2); color: #fca5a5; }
    .flash-warning { background: rgba(255, 193, 7, 0.2); color: #fde68a; }
</style>
<?php $styles = ob_get_clean() ?>

<?php
$sidebar_left = [
    ['label'=>'Overview', 'url'=>'/admin','active'=>false, 'icon'=>'tachometer-alt'],
    ['label'=>'User Management', 'url'=>'/admin/users','active'=>false, 'icon' => 'users'],
    ['label'=>'Engagement Metrics', 'url'=>'/admin/engagement','active'=>false, 'icon' => 'chart-bar'],
    ['label'=>'Reports', 'url'=>'/admin/reports','active'=>false, 'icon' => 'fas fa-exclamation-triangle'],
    ['label'=>'Event Moderation', 'url'=>'/admin/eventrequests','active'=>false, 'icon' => 'clipboard-list'],
    ['label'=>'Content Management', 'url'=>'/admin/posts','active'=>false, 'icon' => 'pencil-alt'],
    ['label'=>'Fundraisers', 'url'=>'/admin/fundraisers','active'=>false, 'icon' => 'donate'],
    ['label'=>'Alumni Verifications', 'url'=>'/admin/verifications','active'=>false, 'icon' => 'check-circle'],
    ['label'=>'Suspended Users', 'url'=>'/admin/suspendedUsers','active'=>true, 'icon' => 'user-slash']
];

$activeRows = $data['active_suspensions'] ?? [];
$historyRows = $data['suspension_history'] ?? [];
$flashMessages = SessionManager::getFlash();
?>

<?php ob_start(); ?>
<div class="admin-header" style="border-bottom:2px solid #3a3a3a; padding-bottom:12px; margin-bottom:12px;">
    <h1>Suspended Users</h1>
</div>

<?php if (!empty($flashMessages)): ?>
    <?php foreach ($flashMessages as $msg): ?>
        <div class="flash flash-<?php echo htmlspecialchars($msg['type'] ?? 'success'); ?>">
            <?php echo htmlspecialchars($msg['message'] ?? ''); ?>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

<div class="admin-card">
    <div class="card-header" style="margin-bottom:8px;">
        <h3>Active Suspensions</h3>
    </div>
    <div class="admin-table-wrapper">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>User</th>
                    <th>Role</th>
                    <th>Reason</th>
                    <th>Suspended By</th>
                    <th>Suspended At</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($activeRows)): ?>
                    <tr><td colspan="7" style="text-align:center;color:#9ca3af;">No active suspended users.</td></tr>
                <?php else: ?>
                    <?php foreach ($activeRows as $row): ?>
                        <tr>
                            <td>
                                <div><strong><?php echo htmlspecialchars($row->name ?? 'Unknown User'); ?></strong></div>
                                <div style="color:#9ca3af;"><?php echo htmlspecialchars($row->email ?? '-'); ?> (ID: <?php echo (int)($row->user_id ?? 0); ?>)</div>
                            </td>
                            <td><?php echo htmlspecialchars($row->role ?? '-'); ?></td>
                            <td class="reason"><?php echo htmlspecialchars($row->reason ?? 'No reason provided'); ?></td>
                            <td><?php echo htmlspecialchars($row->suspended_by_name ?? 'Admin'); ?></td>
                            <td><?php echo htmlspecialchars($row->suspended_at ?? '-'); ?></td>
                            <td><span class="status-pill active">ACTIVE</span></td>
                            <td>
                                <div class="actions-row">
                                    <form method="post" action="<?php echo URLROOT; ?>/admin/liftSuspension">
                                        <input type="hidden" name="suspension_id" value="<?php echo (int)($row->suspension_id ?? 0); ?>">
                                        <button type="submit" class="admin-btn">Allow Access</button>
                                    </form>
                                    <form method="post" action="<?php echo URLROOT; ?>/admin/removeSuspendedUser" onsubmit="return confirm('Remove this user account from the system permanently?');">
                                        <input type="hidden" name="suspension_id" value="<?php echo (int)($row->suspension_id ?? 0); ?>">
                                        <button type="submit" class="admin-btn admin-btn-danger">Remove User</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="admin-card">
    <div class="card-header" style="margin-bottom:8px;">
        <h3>Suspension History</h3>
    </div>
    <div class="admin-table-wrapper">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>User</th>
                    <th>Reason</th>
                    <th>Status</th>
                    <th>Suspended At</th>
                    <th>Lifted At</th>
                    <th>Removed At</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($historyRows)): ?>
                    <tr><td colspan="6" style="text-align:center;color:#9ca3af;">No suspension history yet.</td></tr>
                <?php else: ?>
                    <?php foreach ($historyRows as $row): ?>
                        <tr>
                            <td>
                                <div><strong><?php echo htmlspecialchars($row->name ?? 'Removed User'); ?></strong></div>
                                <div style="color:#9ca3af;"><?php echo htmlspecialchars($row->email ?? '-'); ?></div>
                            </td>
                            <td class="reason"><?php echo htmlspecialchars($row->reason ?? 'No reason provided'); ?></td>
                            <td>
                                <?php $status = strtolower((string)($row->status ?? 'lifted')); ?>
                                <span class="status-pill <?php echo htmlspecialchars($status); ?>"><?php echo strtoupper(htmlspecialchars($status)); ?></span>
                            </td>
                            <td><?php echo htmlspecialchars($row->suspended_at ?? '-'); ?></td>
                            <td><?php echo htmlspecialchars($row->lifted_at ?? '-'); ?></td>
                            <td><?php echo htmlspecialchars($row->removed_at ?? '-'); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php $content = ob_get_clean(); ?>
<?php require APPROOT . '/views/admin/dashboard_layout.php'; ?>
