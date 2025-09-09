<?php
// Reports Management Page for Admin
?>
<div class="admin-header">
    <h1>Reports & Exports</h1>
    <div class="admin-actions">
        <button class="admin-btn" id="export-analytics">Export Analytics</button>
        <button class="admin-btn" id="export-users">Export User Reports</button>
        <button class="admin-btn" id="export-content">Export Content Reports</button>
    </div>
</div>
<div class="admin-card" style="margin-bottom:1.5rem;">
    <div class="card-header">
        <h3>Analytics Overview</h3>
    </div>
    <div class="analytics-section">
        <div class="analytics-item">
            <span class="analytics-label">Total Users:</span>
            <span class="analytics-value" id="analytics-users">—</span>
        </div>
        <div class="analytics-item">
            <span class="analytics-label">Total Posts:</span>
            <span class="analytics-value" id="analytics-posts">—</span>
        </div>
        <div class="analytics-item">
            <span class="analytics-label">Active Users (30d):</span>
            <span class="analytics-value" id="analytics-active">—</span>
        </div>
        <div class="analytics-item">
            <span class="analytics-label">Growth (3mo):</span>
            <span class="analytics-value" id="analytics-growth">—</span>
        </div>
    </div>
</div>
<div class="admin-card" style="margin-bottom:1.5rem;">
    <div class="card-header">
        <h3>User Reports</h3>
    </div>
    <div class="reports-table-wrapper">
        <table class="admin-table" id="userReportsTable">
            <thead>
                <tr>
                    <th>Report ID</th>
                    <th>User</th>
                    <th>Type</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>101</td>
                    <td>Jane Smith</td>
                    <td>Abuse</td>
                    <td><span class="status-badge status-pending">Pending</span></td>
                    <td>2025-09-01</td>
                    <td><button class="admin-btn view-report">View</button></td>
                </tr>
                <tr>
                    <td>102</td>
                    <td>Michael Lee</td>
                    <td>Spam</td>
                    <td><span class="status-badge status-resolved">Resolved</span></td>
                    <td>2025-09-03</td>
                    <td><button class="admin-btn view-report">View</button></td>
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
                    <th>Report ID</th>
                    <th>Content</th>
                    <th>Type</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>201</td>
                    <td>Post #1</td>
                    <td>Inappropriate</td>
                    <td><span class="status-badge status-pending">Pending</span></td>
                    <td>2025-09-02</td>
                    <td><button class="admin-btn view-report">View</button></td>
                </tr>
                <tr>
                    <td>202</td>
                    <td>Comment #5</td>
                    <td>Spam</td>
                    <td><span class="status-badge status-rejected">Rejected</span></td>
                    <td>2025-09-04</td>
                    <td><button class="admin-btn view-report">View</button></td>
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
</style>
