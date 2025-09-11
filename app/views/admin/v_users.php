<?php ob_start(); ?>
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/admin/dashboard.css">
<?php $styles = ob_get_clean(); ?>


<?php $sidebar_left = [
    ['url' => '/admin', 'label' => 'Overview', 'icon' => 'chart-bar', 'active' => false],
    ['url' => '/admin/users', 'label' => 'Users', 'icon' => 'users', 'active' => true],
    ['url' => '/admin/engagement', 'label' => 'Engagement', 'icon' => 'comments', 'active' => false],
    ['url' => '/admin/reports', 'label' => 'Reports', 'icon' => 'flag', 'active' => false],
    ['url' => '/admin/settings', 'label' => 'Settings', 'icon' => 'cog', 'active' => false],
]?>
<?php $content = ob_start(); ?>
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

<?php require APPROOT . '/views/admin/dashboard_layout.php';?>


