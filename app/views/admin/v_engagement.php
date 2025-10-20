<?php ob_start()?>
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/admin/common.css">
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/admin/dashboard-common.css">
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/admin/engagement.css">
<?php $styles = ob_get_clean()?>


<?php
    $sidebar_left = [
        ['label'=>'Overview', 'url'=>'/admin','active'=>false, 'icon'=>'tachometer-alt'],
        ['label'=>'User Management', 'url'=>'/admin/users','active'=>false, 'icon' => 'users'],
        ['label'=>'Engagement Metrics', 'url'=>'/admin/engagement','active'=>true, 'icon' => 'chart-bar'],
        ['label'=>'Reports', 'url'=>'/admin/reports','active'=>false, 'icon' => 'fas fa-exclamation-triangle'],
        ['label'=>'Content Management', 'url'=>'/admin/posts','active'=>false, 'icon' => 'pencil-alt'],
        ['label'=>'Fundraisers', 'url'=>'/admin/fundraisers','active'=>false, 'icon' => 'donate'],
        ['label'=>'Alumni Verifications', 'url'=>'/admin/verifications','active'=>false, 'icon' => 'check-circle']
    ]
?>


<?php ob_start();?>
<div class="admin-dashboard">
    <div class="admin-header">
        <h1>Analytics Dashboard</h1>
        <p style="color: var(--muted); margin: 1rem;">Track user activity, engagement, and platform usage.</p>
    </div>

    <section class="filters">
        <h3>Filter Options</h3>
        <form class="filters-form" method="get" action="<?php echo URLROOT; ?>/admin">
            <input type="text" name="date_range" placeholder="Date Range">
            <input type="text" name="user_type" placeholder="User Type">
            <button class="btn">Apply</button>
        </form>
    </section>

    <section class="kpis">
        <div class="kpi">
            <span class="kpi-label">Total Users</span>
            <span id="analytics-users" class="kpi-value"><?php echo number_format($data['metrics']['total_users'] ?? 0); ?></span>
        </div>
        <div class="kpi">
            <span class="kpi-label">Active Users (Last 30 Days)</span>
            <span id="analytics-active" class="kpi-value"><?php echo number_format($data['metrics']['active_30_days'] ?? 0); ?></span>
        </div>
        <div class="kpi">
            <span class="kpi-label">User Growth (Last 3 Months)</span>
            <span id="analytics-growth" class="kpi-value">+<?php echo (int)($data['metrics']['growth_3_months_pct'] ?? 0); ?>%</span>
        </div>
    </section>

    <section class="charts">
        <div class="card">
            <h3>User Distribution by Graduation/Batch</h3>
            <div class="chart-wrap-batch"><canvas id="batchChart"></canvas></div>
        </div>
        <div class="card">
            <h3>Distribution by Role</h3>
            <div class="chart-wrap"><canvas id="roleChart"></canvas></div>
        </div>
    </section>

    <?php $e = $data['engagement'] ?? ['posts'=>0,'comments'=>0,'reactions'=>0,'active_over_time'=>[]]; ?>

    <section class="kpis">
        <div class="kpi"><span class="kpi-label">Total Posts</span><span id="metric-posts" class="kpi-value"><?php echo (int)$e['posts']; ?></span></div>
        <div class="kpi"><span class="kpi-label">Total Comments</span><span id="metric-comments" class="kpi-value"><?php echo (int)$e['comments']; ?></span></div>
        <div class="kpi"><span class="kpi-label">Total Reactions</span><span id="metric-reactions" class="kpi-value"><?php echo (int)$e['reactions']; ?></span></div>
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
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const roleData = <?php echo json_encode($data['charts']['roles'] ?? []); ?>;
    const batchData = <?php echo json_encode($data['charts']['batches'] ?? []); ?>;
    const engagement = <?php echo json_encode((new M_admin())->getEngagementMetrics()); ?>;

    // Populate simple metrics
    document.addEventListener('DOMContentLoaded', () => {
        // Populate numeric metrics (already seeded server-side but keep JS-safe updates)
        const postsEl = document.getElementById('metric-posts');
        const commentsEl = document.getElementById('metric-comments');
        const reactionsEl = document.getElementById('metric-reactions');
        const usersEl = document.getElementById('analytics-users');
        const activeEl = document.getElementById('analytics-active');
        const growthEl = document.getElementById('analytics-growth');

        if(postsEl) postsEl.textContent = (engagement.posts ?? 0).toLocaleString();
        if(commentsEl) commentsEl.textContent = (engagement.comments ?? 0).toLocaleString();
        if(reactionsEl) reactionsEl.textContent = (engagement.reactions ?? 0).toLocaleString();
        if(usersEl) usersEl.textContent = (<?php echo json_encode($data['metrics']['total_users'] ?? 0); ?>).toLocaleString();
        if(activeEl) activeEl.textContent = (<?php echo json_encode($data['metrics']['active_30_days'] ?? 0); ?>).toLocaleString();
        if(growthEl) growthEl.textContent = ('+' + (<?php echo json_encode((int)($data['metrics']['growth_3_months_pct'] ?? 0)); ?>) + '%');

        // Export helpers (simple CSV export of current charts/metrics)
        function downloadCSV(filename, rows){
            const csv = rows.map(r => r.map(c => '"' + String(c).replace(/"/g,'""') + '"').join(',')).join('\n');
            const blob = new Blob([csv], { type: 'text/csv' });
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url; a.download = filename; document.body.appendChild(a); a.click(); a.remove();
            URL.revokeObjectURL(url);
        }

        document.getElementById('export-analytics').addEventListener('click', function(){
            const rows = [
                ['Metric','Value'],
                ['Total Users', usersEl ? usersEl.textContent : '0'],
                ['Active 30d', activeEl ? activeEl.textContent : '0'],
                ['Growth 3mo', growthEl ? growthEl.textContent : '0'],
                ['Posts', postsEl ? postsEl.textContent : '0'],
                ['Comments', commentsEl ? commentsEl.textContent : '0'],
                ['Reactions', reactionsEl ? reactionsEl.textContent : '0']
            ];
            downloadCSV('analytics_summary.csv', rows);
        });

        document.getElementById('export-users').addEventListener('click', function(){
            // Placeholder: in real app, this should call server for a full export. Here export empty header.
            downloadCSV('users_export.csv', [['id','name','email','role','batch']]);
        });

        document.getElementById('export-content').addEventListener('click', function(){
            downloadCSV('content_export.csv', [['id','title','type','status','date']]);
        });
    });

    function toLabelsCounts(arr, labelKey, valueKey){
        const labels = [];
        const values = [];
        (arr || []).forEach(r => { labels.push(r[labelKey]); values.push(parseInt(r[valueKey] || 0)); });
        return {labels, values};
    }

    // Build charts from server-provided data (guard DOM presence)
    (function(){
        const roleArr = <?php echo json_encode($data['charts']['roles'] ?? []); ?>;
        const batchArr = <?php echo json_encode($data['charts']['batches'] ?? []); ?>;
        const overArr = <?php echo json_encode($data['engagement']['active_over_time'] ?? []); ?>;

        function buildLabelsValues(arr, labelKey, valueKey){
            const labels = [], values = [];
            (arr||[]).forEach(r=>{ labels.push(r[labelKey]); values.push(parseInt(r[valueKey] || 0)); });
            return {labels, values};
        }

        const roleDataObj = buildLabelsValues(roleArr, 'role', 'count');
        const roleCtx = document.getElementById('roleChart');
        if(roleCtx && roleDataObj.labels.length){
            new Chart(roleCtx.getContext('2d'), { type:'doughnut', data: { labels: roleDataObj.labels, datasets:[{ data: roleDataObj.values, backgroundColor: ['#60a5fa','#34d399','#fbbf24','#f87171','#a78bfa'] }] }, options:{responsive:true, plugins:{legend:{position:'bottom'}}} });
        }

        const batchDataObj = buildLabelsValues(batchArr, 'batch', 'count');
        const batchCtx = document.getElementById('batchChart');
        if(batchCtx && batchDataObj.labels.length){
            new Chart(batchCtx.getContext('2d'), { type:'bar', data:{ labels: batchDataObj.labels, datasets:[{ data: batchDataObj.values, backgroundColor:'#93c5fd' }] }, options:{responsive:true, scales:{y:{beginAtZero:true}}} });
        }

        const overCtx = document.getElementById('activeOverTime');
        if(overCtx && (overArr||[]).length){
            const labels = overArr.map(r=>r.ym);
            const values = overArr.map(r=>parseInt(r.c||0));
            new Chart(overCtx.getContext('2d'), { type:'line', data:{ labels, datasets:[{ data: values, borderColor:'#4ade80', fill:false, tension:0.3 }] }, options:{responsive:true, scales:{y:{beginAtZero:true}}} });
        }
    })();
</script>

<?php $content = ob_get_clean(); ?>
<?php require APPROOT . '/views/admin/dashboard_layout.php'; ?>


