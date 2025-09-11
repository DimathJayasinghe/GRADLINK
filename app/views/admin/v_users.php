<?php ob_start()?>
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/admin/admin.css">   
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/admin/dashboard.css">
<?php $styles = ob_get_clean()?>

<?php
    $sidebar_left = [
        ['label'=>'Overview', 'url'=>'/admin','active'=>false, 'icon'=>'tachometer-alt'],
        ['label'=>'User Management', 'url'=>'/admin/users','active'=>true, 'icon' => 'users'],
        ['label'=>'Engagement Metrics', 'url'=>'/admin/engagement','active'=>false, 'icon' => 'chart-bar'],
        ['label'=>'Reports', 'url'=>'/admin/reports','active'=>false, 'icon' => 'file-alt'],
        ['label'=>'Content Management', 'url'=>'/admin/posts','active'=>false, 'icon' => 'pencil-alt'],
        ['label'=>'Fundraisers', 'url'=>'/admin/fundraisers','active'=>false, 'icon' => 'donate'],
        ['label'=>'Alumni Verifications', 'url'=>'/admin/verifications','active'=>false, 'icon' => 'check-circle']
    ]
?>

<?php ob_start();?>
<div class="admin-dashboard">
    <div class="admin-header">
        <h1>Users</h1>
        
    </div>

    <div class="card">
        <table style="width:100%; border-collapse:collapse">
            <thead>
                <tr>
                    <th style="text-align:left; padding:8px; border-bottom:1px solid var(--border)">ID</th>
                    <th style="text-align:left; padding:8px; border-bottom:1px solid var(--border)">Name</th>
                    <th style="text-align:left; padding:8px; border-bottom:1px solid var(--border)">Email</th>
                    <th style="text-align:left; padding:8px; border-bottom:1px solid var(--border)">Role</th>
                    <th style="text-align:left; padding:8px; border-bottom:1px solid var(--border)">Batch</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach (($data['users'] ?? []) as $u): ?>
                    <tr>
                        <td style="padding:8px; border-bottom:1px solid var(--border)"><?php echo (int)$u->id; ?></td>
                        <td style="padding:8px; border-bottom:1px solid var(--border)"><?php echo htmlspecialchars($u->name ?? ''); ?></td>
                        <td style="padding:8px; border-bottom:1px solid var(--border)"><?php echo htmlspecialchars($u->email ?? ''); ?></td>
                        <td style="padding:8px; border-bottom:1px solid var(--border)"><?php echo htmlspecialchars($u->role ?? ''); ?></td>
                        <td style="padding:8px; border-bottom:1px solid var(--border)"><?php echo htmlspecialchars($u->batch_no ?? ($u->graduation_year ?? '')); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php $content = ob_get_clean(); ?>
<?php require APPROOT . '/views/admin/dashboard_layout.php'; ?>



