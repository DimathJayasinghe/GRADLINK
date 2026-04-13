<?php ob_start()?>
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/css/admin/common.css">
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/css/admin/dashboard-common.css">
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/css/admin/fundraiser.css">
<?php $styles = ob_get_clean()?>
<?php
    $sidebar_left = [
        ['label'=>'Overview', 'url'=>'/admin','active'=>false, 'icon'=>'tachometer-alt'],
        ['label'=>'User Management', 'url'=>'/admin/users','active'=>false, 'icon' => 'users'],
        ['label'=>'Engagement Metrics', 'url'=>'/admin/engagement','active'=>false, 'icon' => 'chart-bar'],
        ['label'=>'Reports', 'url'=>'/admin/reports','active'=>false, 'icon' => 'fas fa-exclamation-triangle'],
        ['label'=>'Event Moderation', 'url'=>'/admin/eventrequests','active'=>false, 'icon' => 'clipboard-list'],
        ['label'=>'Content Management', 'url'=>'/admin/posts','active'=>false, 'icon' => 'pencil-alt'],
        ['label'=>'Fundraisers', 'url'=>'/admin/fundraisers','active'=>true, 'icon' => 'donate'],
        ['label'=>'Alumni Verifications', 'url'=>'/admin/verifications','active'=>false, 'icon' => 'check-circle'],
        ['label'=>'Help & Support', 'url'=>'/admin/support','active'=>false, 'icon' => 'circle-question']
    ];

    // Extract data
    $stats = $data['stats'] ?? [];
    $fundraisers = $data['fundraisers'] ?? [];
    $clubs = $data['clubs'] ?? [];
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

<div class="admin-fundraiser">
    <div class="admin-header">
        <h1>Fundraisers</h1>
        <div class="admin-actions">
            <button class="admin-btn" id="create-fund" onclick="openCreateModal()">
                <i class="fas fa-plus"></i> Create Fundraiser
            </button>
            <button class="admin-btn" id="export-funds" onclick="exportCSV()" style="background:#28a745;">
                <i class="fas fa-file-csv"></i> Export CSV
            </button>
        </div>
    </div>

    <section class="fund-kpis">
        <div class="fund-kpi">
            <span class="label">Open Campaigns</span>
            <span class="value" id="kpi-open"><?php echo $stats['open_campaigns'] ?? 0; ?></span>
        </div>
        <div class="fund-kpi">
            <span class="label">Total Raised</span>
            <span class="value" id="kpi-raised">Rs.<?php echo number_format($stats['total_raised'] ?? 0, 2); ?></span>
        </div>
        <div class="fund-kpi">
            <span class="label">Active Clubs</span>
            <span class="value" id="kpi-clubs"><?php echo $stats['active_clubs'] ?? 0; ?></span>
        </div>
        <div class="fund-kpi">
            <span class="label">Expiring Soon</span>
            <span class="value" id="kpi-expire"><?php echo $stats['expiring_soon'] ?? 0; ?></span>
        </div>
        <div class="fund-kpi" style="background: #ffeeba;">
            <span class="label" style="color:#856404;">Pending Review</span>
            <span class="value" style="color:#856404;"><?php echo $stats['pending_count'] ?? 0; ?></span>
        </div>
    </section>

    <div class="fund-controls">
        <input type="search" id="fund-search" placeholder="Search by title, club or organizer">
        <select id="fund-status">
            <option value="">All status</option>
            <option value="Pending">Pending</option>
            <option value="Approved">Approved</option>
            <option value="Rejected">Rejected</option>
            <option value="Completed">Completed</option>
            <option value="Cancelled">Cancelled/Held</option>
        </select>
        <select id="fund-club">
            <option value="">All clubs</option>
            <?php foreach($clubs as $club): ?>
                <option value="<?php echo htmlspecialchars($club->club_name); ?>">
                    <?php echo htmlspecialchars($club->club_name); ?>
                </option>
            <?php endforeach; ?>
        </select>
        <button class="btn" id="fund-filter" onclick="applyFilter()">Apply</button>
        <div style="flex:1"></div>
        <div class="card-compact"><strong>Sort</strong>
            <select id="fund-sort" onchange="sortTable()">
                <option value="new">Newest</option>
                <option value="raised">Raised %</option>
                <option value="deadline">Deadline</option>
            </select>
        </div>
    </div>

    <!-- Bulk Actions -->
    <div class="bulk-actions" style="margin-bottom:1rem; display:flex; gap:0.5rem;">
        <button class="admin-btn" onclick="bulkApprove()" style="background:#28a745;">
            <i class="fas fa-check"></i> Approve Selected
        </button>
        <button class="admin-btn admin-btn-danger" onclick="bulkReject()">
            <i class="fas fa-times"></i> Reject Selected
        </button>
    </div>

    <div class="fund-list-card">
        <table class="fund-list-table" id="fund-list">
            <thead>
                <tr>
                    <th><input type="checkbox" id="selectAll" onchange="toggleSelectAll()"></th>
                    <th>Title</th>
                    <th>Club</th>
                    <th>Raised</th>
                    <th>Target</th>
                    <th>Progress</th>
                    <th>Deadline</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="fund-tbody">
                <?php if(empty($fundraisers)): ?>
                    <tr><td colspan="9" style="text-align:center; padding:2rem; color:#888;">No fundraisers found.</td></tr>
                <?php else: ?>
                    <?php foreach($fundraisers as $fund): 
                        $percentage = $fund->target_amount > 0 ? round(($fund->raised_amount / $fund->target_amount) * 100, 1) : 0;
                        $statusClass = 'status-' . strtolower($fund->status);
                    ?>
                    <tr data-id="<?php echo $fund->req_id; ?>" 
                        data-title="<?php echo htmlspecialchars($fund->title); ?>"
                        data-club="<?php echo htmlspecialchars($fund->club_name); ?>"
                        data-status="<?php echo $fund->status; ?>"
                        data-percentage="<?php echo $percentage; ?>"
                        data-deadline="<?php echo $fund->deadline; ?>"
                        data-created="<?php echo $fund->created_at; ?>">
                        <td><input type="checkbox" class="fund-select" value="<?php echo $fund->req_id; ?>"></td>
                        <td>
                            <strong><?php echo htmlspecialchars($fund->title); ?></strong>
                            <br><small style="color:#888;"><?php echo htmlspecialchars($fund->headline ?? ''); ?></small>
                        </td>
                        <td><?php echo htmlspecialchars($fund->club_name); ?></td>
                        <td>Rs.<?php echo number_format($fund->raised_amount, 2); ?></td>
                        <td>Rs.<?php echo number_format($fund->target_amount, 2); ?></td>
                        <td>
                            <div class="progress"><i style="width:<?php echo min($percentage, 100); ?>%"></i></div>
                            <small><?php echo $percentage; ?>%</small>
                        </td>
                        <td><?php echo date('Y-m-d', strtotime($fund->deadline)); ?></td>
                        <td><span class="status-badge <?php echo $statusClass; ?>"><?php echo $fund->status; ?></span></td>
                        <td class="action-btns">
                            <a href="<?php echo URLROOT; ?>/admin/fundraiserDetails/<?php echo $fund->req_id; ?>" class="admin-btn" style="background:#6c757d;">View</a>
                            <?php if($fund->status === 'Pending'): ?>
                                <button class="admin-btn" style="background:#28a745;" onclick="quickApprove(<?php echo $fund->req_id; ?>)">Approve</button>
                                <button class="admin-btn admin-btn-danger" onclick="openRejectModal(<?php echo $fund->req_id; ?>)">Reject</button>
                            <?php elseif($fund->status === 'Approved' || $fund->status === 'Active'): ?>
                                <?php if($percentage >= 100): ?>
                                    <button class="admin-btn" style="background:#17a2b8;" onclick="completeFund(<?php echo $fund->req_id; ?>)" title="Mark as Completed"><i class="fas fa-check-circle"></i> Complete</button>
                                <?php endif; ?>
                                <button class="admin-btn" style="background:#ffc107; color:#000;" onclick="holdFund(<?php echo $fund->req_id; ?>)">Hold</button>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Reject Modal -->
    <div class="admin-modal" id="reject-modal">
        <div class="admin-modal-content" style="background:var(--card-bg, #1a2634);">
            <span class="admin-modal-close" onclick="closeRejectModal()">&times;</span>
            <h2>Reject Fundraiser</h2>
            <form id="reject-form" method="POST" action="<?php echo URLROOT; ?>/admin/rejectFundraiser">
                <input type="hidden" name="id" id="reject-id">
                <input type="hidden" name="return_to" value="list">
                <div style="margin:1rem 0;">
                    <label for="reject-reason" style="display:block; margin-bottom:0.5rem;">Rejection Reason:</label>
                    <textarea name="reason" id="reject-reason" required rows="4" 
                        style="width:100%; padding:0.75rem; border-radius:6px; border:1px solid var(--border); background:var(--surface); color:inherit;"
                        placeholder="Please provide a reason for rejecting this fundraiser..."></textarea>
                </div>
                <button type="submit" class="admin-btn admin-btn-danger">Reject Fundraiser</button>
            </form>
        </div>
    </div>

    <!-- Create Fundraiser Modal -->
    <div class="admin-modal" id="create-modal">
        <div class="admin-modal-content" style="background:var(--card-bg, #1a2634); max-width:700px; max-height:90vh; overflow-y:auto;">
            <span class="admin-modal-close" onclick="closeCreateModal()">&times;</span>
            <h2><i class="fas fa-plus"></i> Create New Fundraiser</h2>
            <form id="create-form" method="POST" action="<?php echo URLROOT; ?>/admin/createFundraiser" enctype="multipart/form-data">
                
                <div class="form-section">
                    <h4>Campaign Details</h4>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Title *</label>
                            <input type="text" name="title" required placeholder="Campaign title">
                        </div>
                        <div class="form-group">
                            <label>Club/Organization *</label>
                            <input type="text" name="club_name" required placeholder="Organization name">
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Headline</label>
                        <input type="text" name="headline" placeholder="Short tagline">
                    </div>
                    <div class="form-group">
                        <label>Description *</label>
                        <textarea name="description" required rows="3" placeholder="Detailed description"></textarea>
                    </div>
                </div>

                <div class="form-section">
                    <h4>Financial Details</h4>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Target Amount (LKR) *</label>
                            <input type="number" name="target_amount" required min="1000" placeholder="100000">
                        </div>
                        <div class="form-group">
                            <label>Start Date *</label>
                            <input type="date" name="start_date" required value="<?php echo date('Y-m-d'); ?>">
                        </div>
                        <div class="form-group">
                            <label>End Date *</label>
                            <input type="date" name="end_date" required>
                        </div>
                    </div>
                </div>

                <div class="form-section">
                    <h4>Bank Details</h4>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Bank Name *</label>
                            <input type="text" name="bank_name" required placeholder="e.g., Commercial Bank">
                        </div>
                        <div class="form-group">
                            <label>Branch *</label>
                            <input type="text" name="branch" required placeholder="Branch name">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Account Number *</label>
                            <input type="text" name="account_number" required placeholder="Account number">
                        </div>
                        <div class="form-group">
                            <label>Account Holder *</label>
                            <input type="text" name="account_holder" required placeholder="Account holder name">
                        </div>
                    </div>
                </div>

                <div class="form-section">
                    <h4>Optional Details</h4>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Fund Manager</label>
                            <input type="text" name="fund_manager" placeholder="Manager name">
                        </div>
                        <div class="form-group">
                            <label>Contact Phone</label>
                            <input type="text" name="fund_manager_contact" placeholder="Phone number">
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Project Poster</label>
                        <input type="file" name="project_poster" accept="image/*">
                        <small style="color: #666; display: block; margin-top: 0.25rem;">Recommended: 1200 × 400 px (3:1 ratio, banner style), PNG or JPG, max 5MB</small>
                    </div>
                </div>

                <div style="margin-top:1.5rem; text-align:right;">
                    <button type="button" class="btn btn-secondary" onclick="closeCreateModal()">Cancel</button>
                    <button type="submit" class="admin-btn" style="background:#28a745;">Create Fundraiser</button>
                </div>
            </form>
        </div>
    </div>

</div>

<style>
.form-section { margin-bottom: 1.5rem; padding: 1rem; background: rgba(255,255,255,0.05); border-radius: 8px; }
.form-section h4 { margin: 0 0 1rem 0; color: var(--accent, #4a90e2); border-bottom: 1px solid var(--border); padding-bottom: 0.5rem; }
.form-row { display: flex; gap: 1rem; flex-wrap: wrap; }
.form-group { flex: 1; min-width: 200px; margin-bottom: 1rem; }
.form-group label { display: block; margin-bottom: 0.5rem; font-weight: 500; font-size: 0.9rem; }
.form-group input, .form-group textarea, .form-group select { 
    width: 100%; padding: 0.65rem; border-radius: 6px; 
    border: 1px solid var(--border); background: var(--surface); color: inherit; 
}
.form-group input:focus, .form-group textarea:focus { outline: none; border-color: var(--accent, #4a90e2); }

/* Action Buttons - Hidden by default, shown on hover or when row selected */
.action-btns { 
    display: flex; 
    gap: 0.25rem; 
    flex-wrap: wrap; 
    opacity: 0;
    visibility: hidden;
    transition: opacity 0.2s, visibility 0.2s;
}
#fund-tbody tr:hover .action-btns,
#fund-tbody tr.selected .action-btns {
    opacity: 1;
    visibility: visible;
}
#fund-tbody tr {
    cursor: pointer;
    transition: background 0.15s;
}
#fund-tbody tr:hover {
    background: rgba(74, 144, 226, 0.08);
}
#fund-tbody tr.selected {
    background: rgba(74, 144, 226, 0.15);
    border-left: 3px solid #4a90e2;
}

/* Status badges */
.status-completed { background: #d4edda; color: #155724; }
.status-cancelled { background: #f8d7da; color: #721c24; }
.status-active { background: #cce5ff; color: #004085; }

/* Table improvements */
.fund-list-table {
    width: 100%;
    border-collapse: collapse;
}
.fund-list-table th,
.fund-list-table td {
    padding: 0.75rem 0.5rem;
    text-align: left;
    vertical-align: middle;
}
.fund-list-table thead th {
    background: rgba(255,255,255,0.05);
    font-weight: 600;
    border-bottom: 2px solid var(--border);
}
.fund-list-table tbody tr {
    border-bottom: 1px solid var(--border);
}
</style>

<script>
const URLROOT = '<?php echo URLROOT; ?>';

// Row selection - click to select/deselect and show actions
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('#fund-tbody tr[data-id]').forEach(row => {
        row.addEventListener('click', function(e) {
            // Don't trigger if clicking on a button, checkbox, or link
            if (e.target.closest('button') || e.target.closest('a') || e.target.type === 'checkbox') {
                return;
            }
            
            // Toggle selection
            const wasSelected = this.classList.contains('selected');
            
            // Deselect all other rows (single selection mode)
            document.querySelectorAll('#fund-tbody tr.selected').forEach(r => {
                r.classList.remove('selected');
            });
            
            // Toggle this row
            if (!wasSelected) {
                this.classList.add('selected');
            }
        });
    });
});

// Toggle select all
function toggleSelectAll() {
    const selectAll = document.getElementById('selectAll');
    document.querySelectorAll('.fund-select').forEach(cb => cb.checked = selectAll.checked);
}

// Open/close modals
function openRejectModal(id) {
    document.getElementById('reject-id').value = id;
    document.getElementById('reject-modal').style.display = 'flex';
}
function closeRejectModal() {
    document.getElementById('reject-modal').style.display = 'none';
}
function openCreateModal() {
    document.getElementById('create-modal').style.display = 'flex';
}
function closeCreateModal() {
    document.getElementById('create-modal').style.display = 'none';
}

// Quick approve
function quickApprove(id) {
    if (!confirm('Are you sure you want to approve this fundraiser?')) return;
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = URLROOT + '/admin/approveFundraiser';
    form.innerHTML = `<input type="hidden" name="id" value="${id}"><input type="hidden" name="return_to" value="list">`;
    document.body.appendChild(form);
    form.submit();
}

// Hold fundraiser
function holdFund(id) {
    if (!confirm('Are you sure you want to put this fundraiser on hold?')) return;
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = URLROOT + '/admin/holdFundraiser';
    form.innerHTML = `<input type="hidden" name="id" value="${id}"><input type="hidden" name="return_to" value="list">`;
    document.body.appendChild(form);
    form.submit();
}

// Complete fundraiser (mark as completed)
function completeFund(id) {
    if (!confirm('Are you sure you want to mark this fundraiser as completed?')) return;
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = URLROOT + '/admin/completeFundraiser';
    form.innerHTML = `<input type="hidden" name="id" value="${id}"><input type="hidden" name="return_to" value="list">`;
    document.body.appendChild(form);
    form.submit();
}

// Bulk approve
function bulkApprove() {
    const checked = document.querySelectorAll('.fund-select:checked');
    if (checked.length === 0) { alert('Select at least one fundraiser.'); return; }
    if (!confirm(`Approve ${checked.length} fundraiser(s)?`)) return;
    
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = URLROOT + '/admin/bulkApproveFundraisers';
    checked.forEach((cb, i) => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = `ids[${i}]`;
        input.value = cb.value;
        form.appendChild(input);
    });
    document.body.appendChild(form);
    form.submit();
}

// Bulk reject
function bulkReject() {
    const checked = document.querySelectorAll('.fund-select:checked');
    if (checked.length === 0) { alert('Select at least one fundraiser.'); return; }
    const reason = prompt('Enter rejection reason for all selected:');
    if (!reason) return;
    
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = URLROOT + '/admin/bulkRejectFundraisers';
    checked.forEach((cb, i) => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = `ids[${i}]`;
        input.value = cb.value;
        form.appendChild(input);
    });
    const reasonInput = document.createElement('input');
    reasonInput.type = 'hidden';
    reasonInput.name = 'reason';
    reasonInput.value = reason;
    form.appendChild(reasonInput);
    document.body.appendChild(form);
    form.submit();
}

// Filter table
function applyFilter() {
    const search = document.getElementById('fund-search').value.toLowerCase();
    const status = document.getElementById('fund-status').value;
    const club = document.getElementById('fund-club').value;
    
    document.querySelectorAll('#fund-tbody tr').forEach(row => {
        if (!row.dataset.id) return; // Skip empty row
        const title = row.dataset.title?.toLowerCase() || '';
        const rowClub = row.dataset.club || '';
        const rowStatus = row.dataset.status || '';
        
        const matchSearch = !search || title.includes(search) || rowClub.toLowerCase().includes(search);
        const matchStatus = !status || rowStatus === status;
        const matchClub = !club || rowClub === club;
        
        row.style.display = (matchSearch && matchStatus && matchClub) ? '' : 'none';
    });
}

// Sort table
function sortTable() {
    const sortBy = document.getElementById('fund-sort').value;
    const tbody = document.getElementById('fund-tbody');
    const rows = Array.from(tbody.querySelectorAll('tr[data-id]'));
    
    rows.sort((a, b) => {
        if (sortBy === 'new') {
            return new Date(b.dataset.created) - new Date(a.dataset.created);
        } else if (sortBy === 'raised') {
            return parseFloat(b.dataset.percentage) - parseFloat(a.dataset.percentage);
        } else if (sortBy === 'deadline') {
            return new Date(a.dataset.deadline) - new Date(b.dataset.deadline);
        }
        return 0;
    });
    
    rows.forEach(row => tbody.appendChild(row));
}

// Search on input
document.getElementById('fund-search').addEventListener('input', applyFilter);

// Export CSV
function exportCSV() {
    const rows = document.querySelectorAll('#fund-tbody tr[data-id]');
    let csv = 'Title,Club,Raised,Target,Progress,Deadline,Status\n';
    
    rows.forEach(row => {
        const cells = row.querySelectorAll('td');
        const title = row.dataset.title.replace(/"/g, '""');
        const club = row.dataset.club.replace(/"/g, '""');
        const raised = cells[3]?.textContent.trim() || '';
        const target = cells[4]?.textContent.trim() || '';
        const progress = row.dataset.percentage + '%';
        const deadline = row.dataset.deadline;
        const status = row.dataset.status;
        csv += `"${title}","${club}","${raised}","${target}","${progress}","${deadline}","${status}"\n`;
    });
    
    const blob = new Blob([csv], { type: 'text/csv' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = `fundraisers_${new Date().toISOString().split('T')[0]}.csv`;
    a.click();
    window.URL.revokeObjectURL(url);
}

// Close modals on outside click
window.onclick = function(e) {
    if (e.target.classList.contains('admin-modal')) {
        e.target.style.display = 'none';
    }
};
</script>

<?php $content = ob_get_clean(); ?>
<?php require APPROOT . '/views/admin/dashboard_layout.php'; ?>