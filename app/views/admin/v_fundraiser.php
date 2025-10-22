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
        ['label'=>'Alumni Verifications', 'url'=>'/admin/verifications','active'=>false, 'icon' => 'check-circle']
    ]
?>
<?php ob_start();?>
<div class="admin-fundraiser">
    <div class="admin-header">
        <h1>Fundraisers</h1>
        <div class="admin-actions">
            <button class="admin-btn" id="create-fund">Create Fundraiser</button>
            <button class="admin-btn" id="export-funds">Export CSV</button>
        </div>
    </div>

    <section class="fund-kpis">
        <div class="fund-kpi"><span class="label">Open Campaigns</span><span class="value" id="kpi-open">--</span></div>
        <div class="fund-kpi"><span class="label">Total Raised</span><span class="value" id="kpi-raised">--</span></div>
        <div class="fund-kpi"><span class="label">Active Clubs</span><span class="value" id="kpi-clubs">--</span></div>
        <div class="fund-kpi"><span class="label">Expiring Soon</span><span class="value" id="kpi-expire">--</span></div>
    </section>

    <div class="fund-controls">
        <input type="search" id="fund-search" placeholder="Search by title, club or organizer">
        <select id="fund-status">
            <option value="">All status</option>
            <option>Pending</option>
            <option>Approved</option>
            <option>Rejected</option>
            <option>Expired</option>
        </select>
        <select id="fund-club">
            <option value="">All clubs</option>
        </select>
        <button class="btn" id="fund-filter">Apply</button>
        <div style="flex:1"></div>
        <div class="card-compact"><strong>Sort</strong>
            <select id="fund-sort"><option value="new">Newest</option><option value="raised">Raised %</option></select>
        </div>
    </div>

    <!-- <div class="fund-grid"> -->
        <div class="fund-list-card">
            <table class="fund-list-table" id="fund-list">
                <thead>
                    <tr><th>Title</th><th>Club</th><th>Raised</th><th>Target</th><th>Progress</th><th>Deadline</th><th>Status</th><th>Actions</th></tr>
                </thead>
                <tbody>
                    <!-- Dummy rows for demo -->
                    <tr>
                        <td>Library Renovation</td>
                        <td>ACM Student Chapter</td>
                        <td>Rs.120,000.00</td>
                        <td>Rs.200,000.00</td>
                        <td><div class="progress"><i style="width:60%"></i></div><small>60%</small></td>
                        <td>2025-11-15</td>
                        <td>Pending</td>
                        <td><button class="admin-btn">View</button></td>
                    </tr>
                    <tr>
                        <td>Robotics Lab Upgrade</td>
                        <td>Robotics Club</td>
                        <td>Rs.75,000.00</td>
                        <td>Rs.100,000.00</td>
                        <td><div class="progress"><i style="width:75%"></i></div><small>75%</small></td>
                        <td>2025-10-30</td>
                        <td>Approved</td>
                        <td><button class="admin-btn">View</button></td>
                    </tr>
                    <tr>
                        <td>Drama Festival</td>
                        <td>Drama Club</td>
                        <td>Rs.20,000.00</td>
                        <td>Rs.50,000.00</td>
                        <td><div class="progress"><i style="width:40%"></i></div><small>40%</small></td>
                        <td>2025-12-05</td>
                        <td>Rejected</td>
                        <td><button class="admin-btn">View</button></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <!-- <aside class="fund-sidebar">
            <h4>Summary</h4>
            <p id="sidebar-summary">3 campaigns, Rs.215,000 raised, 3 clubs</p>
            <h4>Quick Actions</h4>
            <div style="display:flex;flex-direction:column;gap:.5rem">
                <button class="btn" id="approve-selected">Approve Selected</button>
                <button class="btn" id="reject-selected">Reject Selected</button>
            </div>
            <h4 style="margin-top:1rem">Charts</h4>
            <div class="card" style="margin-top:.5rem">
                <img src="https://dummyimage.com/400x140/60a5fa/fff&text=Funds+by+Club" alt="Funds by Club Chart" style="width:100%;height:140px;object-fit:cover;border-radius:8px;">
            </div>
        </aside> -->
    <!-- </div> -->

    <!-- Modal scaffold -->
    <div class="modal" id="fund-modal">
        <div class="modal-inner">
            <button onclick="document.getElementById('fund-modal').style.display='none'">Close</button>
            <div id="fund-modal-body">Loading...</div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    (function(){
        // JS is disabled for demo; all data is hardcoded above for now
    </script>
<?php $content = ob_get_clean(); ?>
<?php require APPROOT . '/views/admin/dashboard_layout.php'; ?>