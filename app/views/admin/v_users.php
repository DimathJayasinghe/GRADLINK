<?php require APPROOT . '/views/inc/header.php'; ?>
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/admin/dashboard.css">

<div class="admin-dashboard">
    <div class="admin-header">
        <h1>Users</h1>
        <nav class="tabs">
            <a class="tab" href="<?php echo URLROOT; ?>/admin">Overview</a>
            <a class="tab active" href="<?php echo URLROOT; ?>/admin/users">Users</a>
            <a class="tab" href="<?php echo URLROOT; ?>/admin/engagement">Engagement</a>
        </nav>
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

<?php require APPROOT . '/views/inc/footer.php'; ?>


