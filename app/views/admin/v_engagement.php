<?php ob_start()?>
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/admin/dashboard.css">
<?php $styles = ob_get_clean()?>


<?php
    $sidebar_left = [
        ['label'=>'Overview', 'url'=>'/admin','active'=>false, 'icon'=>'tachometer-alt'],
        ['label'=>'User Management', 'url'=>'/admin/users','active'=>false, 'icon' => 'users'],
        ['label'=>'Engagement Metrics', 'url'=>'/admin/engagement','active'=>true, 'icon' => 'chart-bar'],
        ['label'=>'Reports', 'url'=>'/admin/reports','active'=>false, 'icon' => 'file-alt'],
        ['label'=>'Content Management', 'url'=>'/admin/posts','active'=>false, 'icon' => 'pencil-alt'],
        ['label'=>'Fundraisers', 'url'=>'/admin/fundraisers','active'=>false, 'icon' => 'donate'],
        ['label'=>'Alumni Verifications', 'url'=>'/admin/verifications','active'=>false, 'icon' => 'check-circle']
    ]
?>


<?php ob_start();?>
<div class="admin-dashboard">
    <div class="admin-header">
        <h1>Analytics Dashboard</h1>
        <p>Track user activity, engagement, and platform usage.</p>
        <nav class="tabs">
            <a class="tab <?php echo ($data['activeTab'] ?? '') === 'overview' ? 'active' : ''; ?>" href="<?php echo URLROOT; ?>/admin">Overview</a>
            <a class="tab <?php echo ($data['activeTab'] ?? '') === 'users' ? 'active' : ''; ?>" href="<?php echo URLROOT; ?>/admin/users">Users</a>
            <a class="tab <?php echo ($data['activeTab'] ?? '') === 'engagement' ? 'active' : ''; ?>" href="<?php echo URLROOT; ?>/admin/engagement">Engagement</a>
        </nav>
    </div>

    <section class="kpis">
        <div class="kpi">
            <span class="kpi-label">Total Users</span>
            <span class="kpi-value"><?php echo number_format($data['metrics']['total_users'] ?? 0); ?></span>
        </div>
        <div class="kpi">
            <span class="kpi-label">Active Users (Last 30 Days)</span>
            <span class="kpi-value"><?php echo number_format($data['metrics']['active_30_days'] ?? 0); ?></span>
        </div>
        <div class="kpi">
            <span class="kpi-label">User Growth (Last 3 Months)</span>
            <span class="kpi-value">+<?php echo (int)($data['metrics']['growth_3_months_pct'] ?? 0); ?>%</span>
        </div>
    </section>

    <section class="charts">
        <div class="card">
            <h3>User Distribution by Graduation/Batch</h3>
            <canvas id="batchChart" height="100"></canvas>
        </div>
        <div class="card">
            <h3>Distribution by Role</h3>
            <canvas id="roleChart" height="100"></canvas>
        </div>
    </section>

    <?php $e = $data['engagement'] ?? ['posts'=>0,'comments'=>0,'reactions'=>0,'active_over_time'=>[]]; ?>

    <section class="kpis">
        <div class="kpi"><span class="kpi-label">Total Posts</span><span class="kpi-value"><?php echo (int)$e['posts']; ?></span></div>
        <div class="kpi"><span class="kpi-label">Total Comments</span><span class="kpi-value"><?php echo (int)$e['comments']; ?></span></div>
        <div class="kpi"><span class="kpi-label">Total Reactions</span><span class="kpi-value"><?php echo (int)$e['reactions']; ?></span></div>
    </section>

    <section class="grid-2">
        <div class="card map-placeholder">
            <h3>Alumni Locations</h3>
            <div class="map-box">Map placeholder</div>
        </div>
        <div class="card">
            <h3>Active Users Over Time</h3>
            <canvas id="activeOverTime" height="100"></canvas>
        </div>
    </section>

    <section class="filters card">
        <h3>Filter Options</h3>
        <form class="filters-form" method="get" action="<?php echo URLROOT; ?>/admin">
            <input type="text" name="date_range" placeholder="Date Range">
            <input type="text" name="user_type" placeholder="User Type">
            <button class="btn">Apply</button>
        </form>
    </section>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const roleData = <?php echo json_encode($data['charts']['roles'] ?? []); ?>;
    const batchData = <?php echo json_encode($data['charts']['batches'] ?? []); ?>;
    const engagement = <?php echo json_encode((new M_admin())->getEngagementMetrics()); ?>;

    // Populate simple metrics
    document.addEventListener('DOMContentLoaded', () => {
        document.getElementById('metric-posts').textContent = (engagement.posts ?? 0).toLocaleString();
        document.getElementById('metric-comments').textContent = (engagement.comments ?? 0).toLocaleString();
        document.getElementById('metric-reactions').textContent = (engagement.reactions ?? 0).toLocaleString();
    });

    function toLabelsCounts(arr, labelKey, valueKey){
        const labels = [];
        const values = [];
        (arr || []).forEach(r => { labels.push(r[labelKey]); values.push(parseInt(r[valueKey] || 0)); });
        return {labels, values};
    }

    const role = toLabelsCounts(roleData, 'role', 'count');
    new Chart(document.getElementById('roleChart'), {
        type: 'doughnut',
        data: { labels: role.labels, datasets: [{ data: role.values, backgroundColor: ['#60a5fa','#34d399','#fbbf24'] }] },
        options: { plugins: { legend: { position: 'bottom' } } }
    });

    const batch = toLabelsCounts(batchData, 'batch', 'count');
    new Chart(document.getElementById('batchChart'), {
        type: 'bar',
        data: { labels: batch.labels, datasets: [{ data: batch.values, backgroundColor: '#93c5fd' }] },
        options: { scales: { y: { beginAtZero: true } } }
    });

    const over = <?php echo json_encode((new M_admin())->getEngagementMetrics()['active_over_time'] ?? []); ?>;
    const overLabels = over.map(r => r.ym);
    const overValues = over.map(r => parseInt(r.c || 0));
    new Chart(document.getElementById('activeOverTime'), {
        type: 'line',
        data: { labels: overLabels, datasets: [{ data: overValues, borderColor: '#4ade80', fill: false, tension: 0.3 }] },
        options: { scales: { y: { beginAtZero: true } } }
    });
</script>

<?php $content = ob_get_clean(); ?>
<?php require APPROOT . '/views/admin/dashboard_layout.php'; ?>


