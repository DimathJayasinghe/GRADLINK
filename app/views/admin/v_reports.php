<?php ob_start()?>
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/css/admin/common.css">   
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/css/admin/dashboard-common.css">   
    <style>
    .admin-table-wrapper { overflow-x: auto; }
    .admin-table { width: 100%; border-collapse: collapse; margin-top: 0rem; }
    .admin-table th, .admin-table td { padding: 0.75rem; border-bottom: 1px solid #eee; text-align: left; }
    .admin-table th { background: var(--bg); }
    .status-badge { padding: 0.25em 0.75em; border-radius: 1em; font-size: 0.9em; }
    .status-pending { background: #ffeeba; color: #856404; }
    .status-resolved { background: #d4edda; color: #155724; }
    .status-rejected { background: #f8d7da; color: #721c24; }
    .admin-btn { padding: 0.4em 1em; margin: 0 0.2em; border: none; border-radius: 3px; background: #007bff; color: #fff; cursor: pointer; font-size: 0.95em; }
    .admin-btn-danger { background: #dc3545; }
    .admin-btn:disabled { opacity: 0.6; cursor: not-allowed; }
    .admin-select{background-color: #3a3a3a; color: #fff; border: none; padding: 0.4em 0.8em; border-radius: 4px; font-size: 0.95em; cursor: pointer;}
    .admin-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; }
    .admin-actions { display: flex; gap: 0.5rem; }
    .card-header { display: flex; justify-content: space-between; align-items: center; }
    .card-tools { display: flex; gap: 0.5rem; }
    .analytics-section { display: flex; flex-wrap: wrap; gap: 2rem; padding: 1rem 0; }
    .analytics-item { min-width: 180px; font-size: 1.1em; }
    .analytics-label { color: #888; margin-right: 0.5em; }
    .analytics-value { font-weight: bold; }
    .admin-modal { display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; background: rgba(0,0,0,0.3); }
    .admin-modal-content { background: var(--surface-4); margin: 5% auto; padding: 2rem; border-radius: 8px; width: 90%; max-width: 500px; position: relative; }
    .admin-modal-close { position: absolute; top: 1rem; right: 1rem; font-size: 1.5rem; cursor: pointer; }

    .report-id{
        width: 5%;
    }
    .report-user{
        width: 20%;
    }
    .report-type{
        width: 15%;
    }
    .report-status{
        width: 15%;
    }
    .report-date{
        width: 20%;
    }
    .report-actions{
        width: 25%;
    }

    </style> 
<?php $styles = ob_get_clean()?>



<?php
    $sidebar_left = [
        ['label'=>'Overview', 'url'=>'/admin','active'=>false, 'icon'=>'tachometer-alt'],
        ['label'=>'User Management', 'url'=>'/admin/users','active'=>false, 'icon' => 'users'],
        ['label'=>'Engagement Metrics', 'url'=>'/admin/engagement','active'=>false, 'icon' => 'chart-bar'],
        ['label'=>'Reports', 'url'=>'/admin/reports','active'=>true, 'icon' => 'fas fa-exclamation-triangle'],
        ['label'=>'Event Moderation', 'url'=>'/admin/eventrequests','active'=>false, 'icon' => 'clipboard-list'],
        ['label'=>'Content Management', 'url'=>'/admin/posts','active'=>false, 'icon' => 'pencil-alt'],
        ['label'=>'Fundraisers', 'url'=>'/admin/fundraisers','active'=>false, 'icon' => 'donate'],
        ['label'=>'Alumni Verifications', 'url'=>'/admin/verifications','active'=>false, 'icon' => 'check-circle']
    ]
?>

<?php ob_start();?>
<div class="admin-header" style="border-bottom: 2px solid #3a3a3a; padding-bottom: 15px;">
    <h1>Reports & Exports</h1>
    <!-- <div class="admin-actions">
        <button class="admin-btn" id="export-users">Export User Reports</button>
        <button class="admin-btn" id="export-content">Export Content Reports</button>
    </div> -->
</div>

<div class="admin-card" style="margin-bottom:1.5rem;">
    <div class="card-header">
        <h3>User Reports</h3>
    </div>
    <div class="reports-table-wrapper">
        <table class="admin-table" id="userReportsTable">
            <thead>
                <tr>
                    <th class="report-id">Report ID</th>
                    <th class="report-user">User</th>
                    <th class="report-type">Type</th>
                    <th class="report-status">Status</th>
                    <th class="report-date">Date</th>
                    <th class="report-actions">Actions</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>101</td>
                    <td>Jane Smith</td>
                    <td>Abuse</td>
                    <td><span class="status-badge status-pending">Pending</span></td>
                    <td>2025-09-01</td>
                    <td style="display:flex; gap:0.5rem;">
                        <button class="admin-btn view-report">View</button>
                        <select class="admin-select">
                            <option value="">Change Status</option>
                            <option value="resolved">Mark as Resolved</option>
                            <option value="rejected">Reject Report</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>102</td>
                    <td>Michael Lee</td>
                    <td>Spam</td>
                    <td><span class="status-badge status-resolved">Resolved</span></td>
                    <td>2025-09-03</td>
                    <td style="display:flex; gap:0.5rem;"><button class="admin-btn view-report">View</button>
                        <select class="admin-select">
                            <option value="">Change Status</option>
                            <option value="pending">Mark as Pending</option>
                            <option value="rejected">Reject Report</option>
                        </select>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
<div class="admin-card">
    <div class="card-header">
        <h3>Content Reports</h3>
    </div>
    <div class="reports-table-wrapper">
        <table class="admin-table" id="contentReportsTable">
            <thead>
                <tr>
                    <th class="report-id">Report ID</th>
                    <th class="report-content">Content</th>
                    <th class="report-type">Type</th>
                    <th class="report-status">Status</th>
                    <th class="report-date">Date</th>
                    <th class="report-actions">Actions</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>201</td>
                    <td>Post #1</td>
                    <td>Inappropriate</td>
                    <td><span class="status-badge status-pending">Pending</span></td>
                    <td>2025-09-02</td>
                    <td style="display:flex; gap:0.5rem;"><button class="admin-btn view-report">View</button>
                        <select class="admin-select">
                            <option value="">Change Status</option>
                            <option value="resolved">Mark as Resolved</option>
                            <option value="rejected">Reject Report</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>202</td>
                    <td>Comment #5</td>
                    <td>Spam</td>
                    <td><span class="status-badge status-rejected">Rejected</span></td>
                    <td>2025-09-04</td>
                    <td style="display:flex; gap:0.5rem;"><button class="admin-btn view-report">View</button>
                        <select class="admin-select">
                            <option value="">Change Status</option>
                            <option value="pending">Mark as Pending</option>
                            <option value="resolved">Mark as Resolved</option>
                            <option value="rejected">Reject Report</option>
                        </select>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
<!-- Modal for viewing report details -->
<div id="reportModal" class="admin-modal" style="display:none;">
    <div class="admin-modal-content">
        <span class="admin-modal-close">&times;</span>
        <h2>Report Details</h2>
        <div id="modalReportContent">
            <!-- Filled by JS -->
        </div>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Modal logic
    const modal = document.getElementById('reportModal');
    const modalClose = document.querySelector('.admin-modal-close');
    modalClose.onclick = () => modal.style.display = 'none';
    window.onclick = (e) => { if (e.target == modal) modal.style.display = 'none'; };
    // View report
    document.querySelectorAll('.view-report').forEach(btn => {
        btn.onclick = function() {
            const row = this.closest('tr');
            let details = '';
            row.querySelectorAll('td').forEach((td, i) => {
                if (i < 5) details += `<b>${document.querySelectorAll('thead th')[i].textContent}:</b> ${td.textContent}<br>`;
            });
            document.getElementById('modalReportContent').innerHTML = details;
            modal.style.display = 'block';
        };
    });
    // Export buttons (placeholders)
    document.getElementById('export-analytics').onclick = function() { alert('Export analytics (CSV/Excel) - backend needed'); };
    document.getElementById('export-users').onclick = function() { alert('Export user reports (CSV/Excel) - backend needed'); };
    document.getElementById('export-content').onclick = function() { alert('Export content reports (CSV/Excel) - backend needed'); };
});
</script>
<?php $content = ob_get_clean(); ?>
<?php require APPROOT . '/views/admin/dashboard_layout.php'; ?>



