<?php ob_start()?>
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/admin/common.css">   
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/admin/dashboard-common.css">  
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/admin/users.css"> 
<style>
    .admin-table-wrapper{
        border-color: #3a3a3a;
    } 
    .user-action-group {
        display: inline-flex;
        flex-wrap: wrap;
        gap: 0.35rem;
        align-items: center;
    }
    .admin-btn-warning {
        background: #b86a00;
        color: #fff;
    }
    .admin-btn-warning:hover {
        background: #cc7600;
    }
    .special-toggle-form {
        display: inline-flex;
        justify-content: center;
    }
    .special-toggle-btn {
        border: 0;
        border-radius: 999px;
        padding: 8px 14px;
        font-size: 12px;
        font-weight: 700;
        cursor: pointer;
        transition: transform 0.15s ease, opacity 0.15s ease, background-color 0.15s ease;
    }
    .special-toggle-btn:hover {
        transform: translateY(-1px);
        opacity: 0.95;
    }
    .special-toggle-btn.is-on {
        background: #1f7a4d;
        color: #fff;
    }
    .special-toggle-btn.is-off {
        background: #444;
        color: #fff;
    }
</style>
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
        ['label'=>'Alumni Verifications', 'url'=>'/admin/verifications','active'=>false, 'icon' => 'check-circle'],
        ['label'=>'Suspended Users', 'url'=>'/admin/suspendedUsers','active'=>false, 'icon' => 'user-slash'],
        ['label'=>'Help & Support', 'url'=>'/admin/support','active'=>false, 'icon' => 'circle-question']
    ]
?>

<?php ob_start();?>
<div class="admin-dashboard">
    <div class="admin-header" style="border-bottom: 2px solid #3a3a3a; padding-bottom: 10px;">
        <h1>Users</h1>
    </div>

    <?php
        $flashMessages = SessionManager::getFlash();
        if (!empty($flashMessages)):
    ?>
        <div class="flash-messages" style="margin-bottom: 1.25rem;">
            <?php foreach ($flashMessages as $message): ?>
                <div class="flash-message <?php echo htmlspecialchars($message['type']); ?>" style="padding: 12px 16px; border-radius: 4px; margin-bottom: 8px; font-weight: 500;">
                    <?php echo htmlspecialchars($message['message']); ?>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

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
            <select id="role-filter" style="color: #736f68ff;" aria-label="Filter by role">
                <option value="">All roles</option>
                <?php foreach ($roles as $r): ?>
                    <option value="<?php echo htmlspecialchars($r); ?>"><?php echo htmlspecialchars(ucfirst($r)); ?></option>
                <?php endforeach; ?>
            </select>

            <button id="clear-filters" class="clear-btn" type="button">Clear</button>
        </div>

        <?php
            // Partition users by role
            $allUsers = $data['users'] ?? [];
            $undergrads = [];
            $alumni = [];
            $admins = [];
            foreach($allUsers as $u){
                $r = strtolower(trim((string)($u->role ?? '')));
                if($r === 'undergrad' || $r === 'student' || $r === 'undergraduate') $undergrads[] = $u;
                else if($r === 'alumni' || $r === 'alumnus') $alumni[] = $u;
                else if($r === 'admin' || $r === 'administrator') $admins[] = $u;
                else $undergrads[] = $u; // default bucket
            }
        ?>

            <div class="admin-table-wrapper">
            <h2>Undergraduates</h2>
            <table class="admin-table" role="table">
                <thead>
                    <tr>
                        <th class="uid">ID</th>
                        <th class="name">Name</th>
                        <th class="email">Email</th>
                        <th class="role">Role</th>
                        <th class="batch">Batch</th>
                        <th class="actions">Actions</th>
                    </tr>
                </thead>
                <tbody id="users-tbody-undergrad">
                    <?php foreach ($undergrads as $u): ?>
                        <tr>
                            <td data-label="ID"><?php echo (int)$u->id; ?></td>
                            <td data-label="Name"><?php echo htmlspecialchars($u->name ?? ''); ?></td>
                            <td data-label="Email"><?php echo htmlspecialchars($u->email ?? ''); ?></td>
                            <td data-label="Role"><?php echo htmlspecialchars($u->role ?? ''); ?></td>
                            <td data-label="Batch"><?php echo htmlspecialchars($u->batch_no ?? ($u->batch_no ?? '')); ?></td>
                            <td data-label="Actions">
                                <div class="user-action-group">
                                    <button class="admin-btn view-user" type="button" onclick="location.href='<?php echo URLROOT; ?>/profile?userid=<?php echo (int)$u->id; ?>';">View</button>
                                    <button
                                        class="admin-btn admin-btn-warning suspend-user-btn"
                                        type="button"
                                        data-user-id="<?php echo (int)$u->id; ?>"
                                        data-user-name="<?php echo htmlspecialchars($u->name ?? 'User', ENT_QUOTES, 'UTF-8'); ?>"
                                    >Suspend</button>
                                    <button class="admin-btn admin-btn-danger delete-user" type="button">Delete</button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="admin-table-wrapper">
            <h2>Alumni</h2>
            <table class="admin-table" role="table">
                <thead>
                    <tr>
                        <th class="uid">ID</th>
                        <th class="name">Name</th>
                        <th class="email">Email</th>
                        <!-- <th class="role">Role</th> -->
                        <th class="batch">Batch</th>
                        <th class="actions">Actions</th>
                        <th class="special-alumni">Special Alumni</th>
                    </tr>
                </thead>
                <tbody id="users-tbody-alumni">
                    <?php foreach ($alumni as $u): ?>
                        <tr>
                            <td data-label="ID"><?php echo (int)$u->id; ?></td>
                            <td data-label="Name"><?php echo htmlspecialchars($u->name ?? ''); ?></td>
                            <td data-label="Email"><?php echo htmlspecialchars($u->email ?? ''); ?></td>
                            <!-- <td data-label="Role"><?php echo htmlspecialchars($u->role ?? ''); ?></td> -->
                            <td data-label="Batch"><?php echo htmlspecialchars($u->batch_no ?? ($u->batch_no ?? '01')); ?></td>
                            <td data-label="Actions">
                                <div class="user-action-group">
                                    <button class="admin-btn view-user" type="button" onclick="location.href='<?php echo URLROOT; ?>/profile?userid=<?php echo (int)$u->id; ?>';">View</button>
                                    <button
                                        class="admin-btn admin-btn-warning suspend-user-btn"
                                        type="button"
                                        data-user-id="<?php echo (int)$u->id; ?>"
                                        data-user-name="<?php echo htmlspecialchars($u->name ?? 'User', ENT_QUOTES, 'UTF-8'); ?>"
                                    >Suspend</button>
                                    <button class="admin-btn admin-btn-danger delete-user" type="button">Delete</button>
                                </div>
                            </td>
                            <td style="text-align: center;">
                                <?php $isSpecial = !empty($u->special_alumni); ?>
                                <form class="special-toggle-form" method="post" action="<?php echo URLROOT; ?>/admin/toggleSpecialAlumni">
                                    <input type="hidden" name="user_id" value="<?php echo (int)$u->id; ?>">
                                    <input type="hidden" name="special_alumni" value="<?php echo $isSpecial ? '0' : '1'; ?>">
                                    <button type="submit" class="special-toggle-btn <?php echo $isSpecial ? 'is-on' : 'is-off'; ?>">
                                        <?php echo $isSpecial ? 'Remove Special' : 'Make Special'; ?>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="admin-table-wrapper">
            <h2>Admins</h2>
            <table class="admin-table" role="table">
                <thead>
                    <tr>
                        <th class="uid">ID</th>
                        <th class="name">Name</th>
                        <th class="email">Email</th>
                        <th class="role">Role</th>
                        <th class="batch">Batch</th>
                        <th class="actions">Actions</th>
                    </tr>
                </thead>
                <tbody id="users-tbody-admins">
                    <?php foreach ($admins as $u): ?>
                        <tr>
                            <td data-label="ID"><?php echo (int)$u->id; ?></td>
                            <td data-label="Name"><?php echo htmlspecialchars($u->name ?? ''); ?></td>
                            <td data-label="Email"><?php echo htmlspecialchars($u->email ?? ''); ?></td>
                            <td data-label="Role"><?php echo htmlspecialchars($u->role ?? ''); ?></td>
                            <td data-label="Batch"><?php echo htmlspecialchars($u->batch_no ?? ($u->graduation_year ?? '')); ?></td>
                            <td data-label="Actions">
                                <button class="admin-btn view-user" onclick="location.href='<?php echo URLROOT; ?>/profile?userid=<?php echo (int)$u->id; ?>';">View</button>
                            </td>
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
            const tbodyUnder = document.getElementById('users-tbody-undergrad');
            const tbodyAlumni = document.getElementById('users-tbody-alumni');
            const tbodyAdmins = document.getElementById('users-tbody-admins');
            const allTBodies = [tbodyUnder, tbodyAlumni, tbodyAdmins].filter(Boolean);

            function normalize(s){ return (s||'').toString().toLowerCase(); }

            function filterRows(){
                const q = normalize(searchEl.value);
                const role = normalize(roleEl.value);
                allTBodies.forEach(tb=>{
                    Array.from(tb.rows).forEach(row=>{
                        const name = normalize(row.cells[1].textContent);
                        const email = normalize(row.cells[2].textContent);
                        const r = normalize(row.cells[3].textContent);
                        const matchesQuery = q === '' || name.includes(q) || email.includes(q);
                        const matchesRole = role === '' || r === role;
                        row.style.display = (matchesQuery && matchesRole) ? '' : 'none';
                    });
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

        (function() {
            const suspendButtons = document.querySelectorAll('.suspend-user-btn');
            if (!suspendButtons.length) return;

            const apiBase = '<?php echo URLROOT; ?>';

            suspendButtons.forEach((btn) => {
                btn.addEventListener('click', async function() {
                    const userId = Number(this.dataset.userId || 0);
                    const userName = this.dataset.userName || `User #${userId}`;

                    if (!userId) {
                        alert('Invalid user id for suspension');
                        return;
                    }

                    const reasonInput = prompt(`Suspend ${userName}?\nProvide a reason (optional):`, 'Suspended by admin from User Management');
                    if (reasonInput === null) {
                        return;
                    }

                    const previousText = this.textContent;
                    this.disabled = true;
                    this.textContent = 'Suspending...';

                    try {
                        const response = await fetch(`${apiBase}/admin/suspendUser`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            body: JSON.stringify({
                                user_id: userId,
                                reason: (reasonInput || '').trim()
                            })
                        });

                        const payload = await response.json().catch(() => null);
                        if (!response.ok || !payload || !payload.ok) {
                            throw new Error((payload && payload.error) ? payload.error : 'Failed to suspend user');
                        }

                        alert((payload && payload.message) ? payload.message : `${userName} suspended successfully`);
                        this.textContent = 'Suspended';
                    } catch (err) {
                        alert(err && err.message ? err.message : 'Failed to suspend user');
                        this.disabled = false;
                        this.textContent = previousText;
                    }
                });
            });
        })();
    </script>
</div>
<?php $content = ob_get_clean(); ?>
<?php require APPROOT . '/views/admin/dashboard_layout.php'; ?>



