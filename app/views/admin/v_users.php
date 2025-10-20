<?php ob_start()?>
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/admin/common.css">   
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/admin/dashboard-common.css">  
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/admin/users.css">  
<?php $styles = ob_get_clean()?>

<?php
    $sidebar_left = [
        ['label'=>'Overview', 'url'=>'/admin','active'=>false, 'icon'=>'tachometer-alt'],
        ['label'=>'User Management', 'url'=>'/admin/users','active'=>true, 'icon' => 'users'],
        ['label'=>'Engagement Metrics', 'url'=>'/admin/engagement','active'=>false, 'icon' => 'chart-bar'],
        ['label'=>'Reports', 'url'=>'/admin/reports','active'=>false, 'icon' => 'fas fa-exclamation-triangle'],
        ['label'=>'Event Moderation', 'url'=>'/admin/eventrequests','active'=>false, 'icon' => 'clipboard-list'],
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

        <?php
            // Build role list for filter (unique, sorted)
            $roles = [];
            foreach ($data['users'] ?? [] as $u) {
                $r = trim((string)($u->role ?? ''));
                if ($r !== '') $roles[$r] = true;
            }
            $roles = array_keys($roles);
            sort($roles);
        ?>

        <div class="admin-users-toolbar">
            <label for="user-search" class="sr-only">Search users</label>
            <input id="user-search" type="search" placeholder="Search name or email..." aria-label="Search users">

            <label for="role-filter" class="sr-only">Filter by role</label>
            <select id="role-filter" aria-label="Filter by role">
                <option value="">All roles</option>
                <?php foreach ($roles as $r): ?>
                    <option value="<?php echo htmlspecialchars($r); ?>"><?php echo htmlspecialchars(ucfirst($r)); ?></option>
                <?php endforeach; ?>
            </select>

            <button id="clear-filters" class="clear-btn" type="button">Clear</button>
        </div>

        <div class="admin-table-wrapper">
            <table class="admin-table" role="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Batch</th>
                    </tr>
                </thead>
                <tbody id="users-tbody">
                    <?php foreach (($data['users'] ?? []) as $u): ?>
                        <tr>
                            <td data-label="ID"><?php echo (int)$u->id; ?></td>
                            <td data-label="Name"><?php echo htmlspecialchars($u->name ?? ''); ?></td>
                            <td data-label="Email"><?php echo htmlspecialchars($u->email ?? ''); ?></td>
                            <td data-label="Role"><?php echo htmlspecialchars($u->role ?? ''); ?></td>
                            <td data-label="Batch"><?php echo htmlspecialchars($u->batch_no ?? ($u->graduation_year ?? '')); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        (function(){
            const searchEl = document.getElementById('user-search');
            const roleEl = document.getElementById('role-filter');
            const clearBtn = document.getElementById('clear-filters');
            const tbody = document.getElementById('users-tbody');

            function normalize(s){ return (s||'').toString().toLowerCase(); }

            function filterRows(){
                const q = normalize(searchEl.value);
                const role = normalize(roleEl.value);
                Array.from(tbody.rows).forEach(row=>{
                    const name = normalize(row.cells[1].textContent);
                    const email = normalize(row.cells[2].textContent);
                    const r = normalize(row.cells[3].textContent);
                    const matchesQuery = q === '' || name.includes(q) || email.includes(q);
                    const matchesRole = role === '' || r === role;
                    row.style.display = (matchesQuery && matchesRole) ? '' : 'none';
                });
            }

            searchEl && searchEl.addEventListener('input', filterRows);
            roleEl && roleEl.addEventListener('change', filterRows);
            clearBtn && clearBtn.addEventListener('click', ()=>{
                searchEl.value = '';
                roleEl.selectedIndex = 0;
                filterRows();
                searchEl.focus();
            });

            // small accessibility: allow Escape to clear search
            searchEl && searchEl.addEventListener('keydown', (e)=>{ if (e.key === 'Escape') { searchEl.value=''; filterRows(); } });
        })();
    </script>
</div>
<?php $content = ob_get_clean(); ?>
<?php require APPROOT . '/views/admin/dashboard_layout.php'; ?>



