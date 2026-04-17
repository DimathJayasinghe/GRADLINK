<?php ob_start()?>
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/admin/common.css">
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/admin/dashboard-common.css">
<style>
.admin-table-wrapper { overflow-x: auto; }
.admin-table { width: 100%; border-collapse: collapse; margin-top: 1rem; }
.admin-table th, .admin-table td { padding: 0.75rem; border-bottom: 1px solid #eee; text-align: left; }
.admin-table th { background: #f8f8f8; background-color: var(--bg);}
.status-badge { padding: 0.25em 0.75em; border-radius: 1em; font-size: 0.9em; }
.status-pending { background: #ffeeba; color: #856404; }
.status-verified { background: #d4edda; color: #155724; }
.status-rejected { background: #f8d7da; color: #721c24; }
.admin-btn { padding: 0.4em 1em; margin: 0 0.2em; border: none; border-radius: 3px; background: #007bff; color: #fff; cursor: pointer; font-size: 0.95em; }
.admin-btn-danger { background: #dc3545; }
.admin-btn:disabled { opacity: 0.6; cursor: not-allowed; }
.admin-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; }
.admin-actions { display: flex; gap: 0.5rem; }
.card-header { display: flex; justify-content: space-between; align-items: center; }
.card-tools { display: flex; gap: 0.5rem; }
.admin-modal { display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; background: rgba(6, 10, 16, 0.58); backdrop-filter: blur(2px); }
.admin-modal-content { background: #0e1b28; margin: 5% auto; padding: 1.5rem 1.6rem; border-radius: 10px; width: 92%; max-width: 520px; position: relative; border: 1px solid rgba(255, 255, 255, 0.08); box-shadow: 0 14px 36px rgba(0, 0, 0, 0.35); color: #d9e3ee; }
.admin-modal-content h2 { margin: 0 0 0.9rem; color: #ffffff; font-size: 1.25rem; }
#modalAlumniContent { line-height: 1.55; }
#modalAlumniContent b { color: #f2f7ff; }
#modalAlumniContent img { margin-top: 0.45rem; max-width: 100%; border: 1px solid rgba(255, 255, 255, 0.16); border-radius: 8px; background: #0b1118; }
.admin-modal-close { position: absolute; top: 0.75rem; right: 0.95rem; font-size: 1.45rem; color: #c9d5e2; cursor: pointer; line-height: 1; }
.admin-modal-close:hover { color: #ffffff; }
.alumni-modal-grid { display: grid; grid-template-columns: 92px 1fr; gap: 8px 12px; align-items: start; font-size: 0.95rem; }
.alumni-modal-grid dt { margin: 0; color: #b9c7d6; font-weight: 600; }
.alumni-modal-grid dd { margin: 0; color: #eef4fb; word-break: break-word; }
.alumni-modal-media { margin-top: 0.9rem; padding-top: 0.75rem; border-top: 1px solid rgba(255, 255, 255, 0.12); }
.alumni-modal-media-title { margin: 0 0 0.4rem; color: #b9c7d6; font-size: 0.85rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.04em; }
.alumni-modal-media img { display: block; width: 100%; }
.flash-message.success { background: rgba(40, 167, 69, 0.15); color: #28a745; border-left: 4px solid #28a745; }
.flash-message.error { background: rgba(220, 53, 69, 0.15); color: #dc3545; border-left: 4px solid #dc3545; }
.flash-message.warning { background: rgba(255, 193, 7, 0.15); color: #ffc107; border-left: 4px solid #ffc107; }
.flash-message.info { background: rgba(23, 162, 184, 0.15); color: #17a2b8; border-left: 4px solid #17a2b8; }
</style>
<?php $styles = ob_get_clean()?>

<?php
    $sidebar_left = [
        ['label'=>'Overview', 'url'=>'/admin','active'=>false, 'icon'=>'tachometer-alt'],
        ['label'=>'User Management', 'url'=>'/admin/users','active'=>false, 'icon' => 'users'],
        ['label'=>'Engagement Metrics', 'url'=>'/admin/engagement','active'=>false, 'icon' => 'chart-bar'],
        ['label'=>'Reports', 'url'=>'/admin/reports','active'=>false, 'icon' => 'fas fa-exclamation-triangle'],
        ['label'=>'Event Moderation', 'url'=>'/admin/eventrequests','active'=>false, 'icon' => 'clipboard-list'],
        ['label'=>'Content Management', 'url'=>'/admin/posts','active'=>false, 'icon' => 'pencil-alt'],
        ['label'=>'Fundraisers', 'url'=>'/admin/fundraisers','active'=>false, 'icon' => 'donate'],
        ['label'=>'Alumni Verifications', 'url'=>'/admin/verifications','active'=>true, 'icon' => 'check-circle'],
        ['label'=>'Suspended Users', 'url'=>'/admin/suspendedUsers','active'=>false, 'icon' => 'user-slash'],
        ['label'=>'Help & Support', 'url'=>'/admin/support','active'=>false, 'icon' => 'circle-question']
    ];

    $requests = $data['requests'] ?? [];
?>

<?php ob_start();?>

<!-- Flash Messages -->
<?php 
$flashMessages = SessionManager::getFlash();
if (!empty($flashMessages)): ?>
    <div class="flash-messages" style="margin-bottom: 1.5rem;">
        <?php foreach ($flashMessages as $message): ?>
            <div class="flash-message <?php echo htmlspecialchars($message['type']); ?>" style="padding: 12px 16px; border-radius: 4px; margin-bottom: 8px; font-weight: 500;">
                <?php echo htmlspecialchars($message['message']); ?>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<div class="admin-header" style="border-bottom: 2px solid #3a3a3a; padding-bottom: 15px;">
    <h1>Alumni Verifications</h1>
    <div class="admin-actions">
        <button id="bulk-verify" class="admin-btn">Verify Selected</button>
        <button id="bulk-reject" class="admin-btn admin-btn-danger">Reject Selected</button>
    </div>
</div>
<div class="admin-card">
    <div class="card-header">
        <h3>Verification Queue</h3>
        <div class="card-tools">
            <input type="text" id="alumniSearch" style="background-color:#3a3a3a; color:aliceblue; padding:4px 8px; border:none; border-radius:4px;" placeholder="Search by name, email, batch, NIC...">
            <select id="alumniStatusFilter" style="background-color:#3a3a3a; color:aliceblue; padding:4px 8px; border:none; border-radius:4px;">
                <option value="pending">Pending</option>
                <option value="verified">Verified</option>
                <option value="rejected">Rejected</option>
            </select>
        </div>
    </div>
    <div class="admin-table-wrapper">
        <?php if (!empty($requests)): ?>
        <table class="admin-table" id="alumniTable">
            <thead>
                <tr>
                    <th><input type="checkbox" id="selectAllAlumni"></th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Batch</th>
                    <th>NIC</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <!-- Alumni will be loaded here by JS or backend -->
                <?php foreach ($requests as $alumni): ?>
                <tr data-req-id="<?= htmlspecialchars($alumni->req_id ?? '') ?>">
                    <td><input type="checkbox" class="selectAlumni"></td>
                    <td><?= htmlspecialchars($alumni->Name ?? '') ?></td>
                    <td><?= htmlspecialchars($alumni->email ?? '') ?></td>
                    <td><?= htmlspecialchars($alumni->Batch ?? '') ?></td>
                    <td><?= htmlspecialchars($alumni->nic ?? '') ?></td>
                    <td><span class="status-badge status-<?= strtolower($alumni->status ?? 'pending') ?>"><?= htmlspecialchars($alumni->status ?? 'Pending') ?></span></td>
                    <td>
                        <button class="admin-btn view-alumni" style="background-color: #525253ff; color: white;">View</button>
                        <button class="admin-btn verify-alumni">Verify</button>
                        <button class="admin-btn admin-btn-danger reject-alumni">Reject</button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php else: ?>
            <div class="empty-state">No pending requests.</div>
        <?php endif; ?>
    </div>
</div>

<!-- Modal for viewing alumni details -->
<div id="alumniModal" class="admin-modal" style="display:none;">
    <div class="admin-modal-content">
        <span class="admin-modal-close">&times;</span>
        <h2>Alumni Details</h2>
        <div id="modalAlumniContent">
            <!-- Filled by JS -->
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Select all alumni
    const selectAll = document.getElementById('selectAllAlumni');
    if (selectAll) {
        selectAll.addEventListener('change', function() {
            document.querySelectorAll('.selectAlumni').forEach(cb => cb.checked = this.checked);
        });
    }

    // Modal logic
    const modal = document.getElementById('alumniModal');
    const modalClose = document.querySelector('.admin-modal-close');
    if (modalClose) {
        modalClose.onclick = () => modal.style.display = 'none';
    }
    window.onclick = (e) => { if (e.target == modal) modal.style.display = 'none'; };

    // View alumni
    document.querySelectorAll('.view-alumni').forEach(btn => {
        btn.onclick = function() {
            const row = this.closest('tr');
            const name = row.children[1].textContent;
            const email = row.children[2].textContent;
            const batch = row.children[3].textContent;
            const nic = row.children[4].textContent;
            const status = row.children[5].textContent;
            document.getElementById('modalAlumniContent').innerHTML =
                `<dl class="alumni-modal-grid">
                    <dt>Name</dt><dd>${name}</dd>
                    <dt>Email</dt><dd>${email}</dd>
                    <dt>Batch</dt><dd>${batch}</dd>
                    <dt>NIC</dt><dd>${nic}</dd>
                    <dt>Status</dt><dd>${status}</dd>
                </dl>
                <div class="alumni-modal-media">
                    <div class="alumni-modal-media-title">Profile Image</div>
                    <img src="${"<?php echo URLROOT; ?>"}/media/profile/${encodeURIComponent(row.getAttribute('data-req-id') + '_nic.jpg')}" alt="Profile Image">
                </div>`;
            modal.style.display = 'block';
        };
    });

    const goTo = (url) => { window.location.href = url; };

    // Verify
    document.querySelectorAll('.verify-alumni').forEach(btn => {
        btn.onclick = async function() {
            const row = this.closest('tr');
            const id = row ? row.getAttribute('data-req-id') : null;
            if (!id) {
                await AdminPopup.alert('Missing request ID', { title: 'Verify Alumni', danger: true });
                return;
            }
            goTo(`${"<?php echo URLROOT; ?>"}/signup/alumni?id=${encodeURIComponent(id)}`);
        };
    });

    // Reject
    document.querySelectorAll('.reject-alumni').forEach(btn => {
        btn.onclick = async function() {
            const row = this.closest('tr');
            const id = row ? row.getAttribute('data-req-id') : null;
            if (!id) {
                await AdminPopup.alert('Missing request ID', { title: 'Reject Alumni', danger: true });
                return;
            }
            const confirmed = await AdminPopup.confirm('Are you sure you want to reject this request?', {
                title: 'Reject Alumni',
                confirmText: 'Reject',
                danger: true
            });
            if (!confirmed) {
                return;
            }
            goTo(`${"<?php echo URLROOT; ?>"}/signup/alumni?reject_id=${encodeURIComponent(id)}`);
        };
    });

    // Bulk actions
    const bulkVerify = document.getElementById('bulk-verify');
    if (bulkVerify) {
        bulkVerify.onclick = async function() {
            const checked = document.querySelectorAll('.selectAlumni:checked');
            if (checked.length === 0) {
                await AdminPopup.alert('Please select at least one alumni.', { title: 'Bulk Verify' });
                return;
            }
            const confirmed = await AdminPopup.confirm(`Verify ${checked.length} alumni?`, {
                title: 'Bulk Verify',
                confirmText: 'Verify'
            });
            if (!confirmed) {
                return;
            }
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `${"<?php echo URLROOT; ?>"}/admin/bulkVerifyAlumni`;
            checked.forEach((cb, i) => {
                const id = cb.closest('tr').getAttribute('data-req-id');
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = `ids[${i}]`;
                input.value = id;
                form.appendChild(input);
            });
            document.body.appendChild(form);
            form.submit();
        };
    }

    const bulkReject = document.getElementById('bulk-reject');
    if (bulkReject) {
        bulkReject.onclick = async function() {
            const checked = document.querySelectorAll('.selectAlumni:checked');
            if (checked.length === 0) {
                await AdminPopup.alert('Please select at least one alumni.', { title: 'Bulk Reject' });
                return;
            }
            const confirmed = await AdminPopup.confirm(`Reject ${checked.length} alumni?`, {
                title: 'Bulk Reject',
                confirmText: 'Reject',
                danger: true
            });
            if (!confirmed) {
                return;
            }
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `${"<?php echo URLROOT; ?>"}/admin/bulkRejectAlumni`;
            checked.forEach((cb, i) => {
                const id = cb.closest('tr').getAttribute('data-req-id');
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = `ids[${i}]`;
                input.value = id;
                form.appendChild(input);
            });
            document.body.appendChild(form);
            form.submit();
        };
    }

    // Search/filter functionality
    const searchInput = document.getElementById('alumniSearch');
    const statusFilter = document.getElementById('alumniStatusFilter');
    const alumniTable = document.getElementById('alumniTable');

    const filterTable = () => {
        if (!alumniTable) return;

        const searchTerm = (searchInput?.value || '').toLowerCase();
        const selectedStatus = (statusFilter?.value || 'pending').toLowerCase();
        const rows = alumniTable.querySelectorAll('tbody tr');

        rows.forEach(row => {
            const name = row.children[1].textContent.toLowerCase();
            const email = row.children[2].textContent.toLowerCase();
            const batch = row.children[3].textContent.toLowerCase();
            const nic = row.children[4].textContent.toLowerCase();
            const statusCell = row.children[5].textContent.toLowerCase();

            // Check if row matches search term
            const matchesSearch = !searchTerm || 
                name.includes(searchTerm) || 
                email.includes(searchTerm) || 
                batch.includes(searchTerm) || 
                nic.includes(searchTerm);

            // Check if row matches status filter
            const matchesStatus = statusCell.includes(selectedStatus);

            // Show row if it matches both criteria
            row.style.display = (matchesSearch && matchesStatus) ? '' : 'none';
        });
    };

    if (searchInput) {
        searchInput.oninput = filterTable;
    }
    if (statusFilter) {
        statusFilter.onchange = filterTable;
    }
});
</script>


<?php $content = ob_get_clean(); ?>
<?php require APPROOT . '/views/admin/dashboard_layout.php'; ?>
