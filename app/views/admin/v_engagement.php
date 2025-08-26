<?php require APPROOT . '/views/inc/header.php'; ?>
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/admin/dashboard.css">

<div class="admin-dashboard">
    <div class="admin-header">
        <h1>Engagement</h1>
        <nav class="tabs">
            <a class="tab" href="<?php echo URLROOT; ?>/admin">Overview</a>
            <a class="tab" href="<?php echo URLROOT; ?>/admin/users">Users</a>
            <a class="tab active" href="<?php echo URLROOT; ?>/admin/engagement">Engagement</a>
        </nav>
    </div>

    <?php $e = $data['engagement'] ?? ['posts'=>0,'comments'=>0,'reactions'=>0,'active_over_time'=>[]]; ?>

    <section class="kpis">
        <div class="kpi"><span class="kpi-label">Total Posts</span><span class="kpi-value"><?php echo (int)$e['posts']; ?></span></div>
        <div class="kpi"><span class="kpi-label">Total Comments</span><span class="kpi-value"><?php echo (int)$e['comments']; ?></span></div>
        <div class="kpi"><span class="kpi-label">Total Reactions</span><span class="kpi-value"><?php echo (int)$e['reactions']; ?></span></div>
    </section>

    <div class="card">
        <h3>Active Users Over Time</h3>
        <canvas id="activeOverTime" height="100"></canvas>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const over = <?php echo json_encode($e['active_over_time']); ?>;
    const labels = over.map(r => r.ym);
    const values = over.map(r => parseInt(r.c || 0));
    new Chart(document.getElementById('activeOverTime'), {
        type: 'line',
        data: { labels, datasets: [{ data: values, borderColor: '#60a5fa', fill: false, tension: 0.3 }] },
        options: { scales: { y: { beginAtZero: true } } }
    });
</script>

<?php require APPROOT . '/views/inc/footer.php'; ?>


