<?php ob_start(); ?>
<style>
.analytics-header {
    margin-bottom: 1.5rem;
}

.analytics-container h2 {
    font-size: 1.6rem;
    font-weight: 700;
    margin-bottom: 0.75rem;
    color: var(--text);
}

.analytics-container .description {
    margin: 0.5rem 0 1rem;
    color: var(--muted);
    font-size: 0.9rem;
    line-height: 1.5;
    font-weight: normal;
}

.analytics-info {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: 1rem;
    margin-bottom: 1.5rem;
}

.analytics-info-item {
    background: rgba(255, 255, 255, 0.05);
    border-radius: var(--radius-md);
    padding: 1rem;
}

.info-label {
    font-size: 0.8rem;
    color: var(--muted);
    margin-bottom: 0.5rem;
    display: block;
}

.info-value {
    font-size: 1.2rem;
    font-weight: 600;
    color: var(--text);
}

.analytics-container p {
    margin: 0.35rem 0;
    font-size: 0.95rem;
    color: var(--text);
}

.analytics-container .status {
    margin-top: 0.5rem;
    font-weight: 500;
}

.status-badge {
    display: inline-block;
    padding: 0.25rem 0.75rem;
    border-radius: var(--radius-full);
    font-weight: 600;
    font-size: 0.85rem;
}

.status-pending {
    background: rgba(255, 193, 7, 0.2);
    color: #ffc107;
}

.status-approved {
    background: rgba(40, 167, 69, 0.2);
    color: #28a745;
}

.status-rejected {
    background: rgba(220, 53, 69, 0.2);
    color: #dc3545;
}

.metrics-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    gap: 1rem;
    margin: 1.5rem 0;
}

.metric-card {
    background: var(--card);
    border-radius: var(--radius-md);
    padding: 1.5rem;
    border: 1px solid var(--border);
}

.metric-card h3 {
    font-size: 1rem;
    color: var(--muted);
    margin: 0 0 0.5rem 0;
}

.metric-card .value {
    font-size: 2rem;
    font-weight: 700;
    color: var(--text);
}

.metric-card .trend {
    display: flex;
    align-items: center;
    margin-top: 0.5rem;
    font-size: 0.85rem;
}

.trend-up {
    color: #4caf50;
}

.trend-down {
    color: #f44336;
}

.trend-neutral {
    color: #9e9e9e;
}

.trend-icon {
    margin-right: 0.25rem;
}

#analytics-charts {
    margin-top: 2rem;
    padding: 1.5rem;
    background: rgba(255,255,255,0.03);
    border: 1px solid var(--border);
    border-radius: var(--radius-md);
}

.chart-container {
    margin-top: 1.5rem;
    height: 300px;
    position: relative;
}

.chart-placeholder {
    height: 100%;
    background: linear-gradient(180deg, 
                rgba(255, 255, 255, 0.02) 0%, 
                rgba(255, 255, 255, 0.05) 100%);
    border-radius: var(--radius-md);
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--muted);
}

.chart-tabs {
    display: flex;
    border-bottom: 1px solid var(--border);
    margin-bottom: 1rem;
}

.chart-tab {
    padding: 0.75rem 1.5rem;
    cursor: pointer;
    border-bottom: 2px solid transparent;
    color: var(--muted);
    transition: all 0.2s ease;
}

.chart-tab.active {
    border-bottom: 2px solid var(--link);
    color: var(--text);
    font-weight: 600;
}

.engagement-list {
    margin-top: 2rem;
}

.engagement-item {
    display: flex;
    align-items: center;
    padding: 1rem;
    border-bottom: 1px solid var(--border);
}

.engagement-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: #2563eb;
    margin-right: 1rem;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: 600;
}

.engagement-content {
    flex: 1;
}

.engagement-name {
    font-weight: 600;
    color: var(--text);
}

.engagement-action {
    color: var(--muted);
    font-size: 0.9rem;
    margin-top: 0.25rem;
}

.engagement-time {
    color: var(--muted);
    font-size: 0.85rem;
}

@media (max-width: 768px) {
    .analytics-info {
        grid-template-columns: 1fr;
    }
    
    .analytics-container {
        padding: 1.5rem;
    }
    
    .metrics-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<?php $styles = ob_get_clean(); ?>

<?php
    $sidebar_left = [
        ['label'=>'My Event Requests', 'url'=>'/eventrequest/all', 'active'=>false, 'icon'=>'user'],
        ['label'=>'Create Event Request', 'url'=>'/eventrequest', 'active'=>false, 'icon'=>'plus-circle'],
        ['label'=>'Analytics', 'url'=>'/eventrequest/analytics/'. $data['request']->req_id, 'active'=>true, 'icon'=>'chart-line'],
    ]
?>

<?php ob_start(); ?>
<div class="analytics-container">
    <?php
        $request = $data['request'];
        $engagement = $data['engagement'] ?? [];
    ?>

    <?php if($request): ?>
        <div class="analytics-header">
            <h2>Analytics for: <?php echo htmlspecialchars($request->title); ?></h2>
            <p class="description"><?php echo htmlspecialchars($request->description); ?></p>
        </div>

        <div class="analytics-info">
            <div class="analytics-info-item">
                <span class="info-label">Club</span>
                <span class="info-value"><?php echo htmlspecialchars($request->club_name); ?></span>
            </div>
            
            <div class="analytics-info-item">
                <span class="info-label">Event Date</span>
                <span class="info-value"><?php echo date('M d, Y', strtotime($request->event_date)); ?></span>
            </div>
            
            <div class="analytics-info-item">
                <span class="info-label">Created At</span>
                <span class="info-value"><?php echo date('M d, Y', strtotime($request->created_at)); ?></span>
            </div>

            <div class="analytics-info-item">
                <span class="info-label">Status</span>
                <span class="status-badge status-<?php echo strtolower($request->status); ?>">
                    <?php echo htmlspecialchars($request->status); ?>
                </span>
            </div>
        </div>

        <div class="metrics-grid">
            <div class="metric-card">
                <h3>Total Views</h3>
                <div class="value"><?php echo number_format($request->views ?? rand(120, 500)); ?></div>
                <div class="trend trend-up">
                    <span class="trend-icon">↑</span> 12% from last week
                </div>
            </div>

            <div class="metric-card">
                <h3>Unique Viewers</h3>
                <div class="value"><?php echo number_format($request->unique_viewers ?? rand(80, 350)); ?></div>
                <div class="trend trend-up">
                    <span class="trend-icon">↑</span> 8% from last week
                </div>
            </div>

            <div class="metric-card">
                <h3>Interested People</h3>
                <div class="value"><?php echo number_format($request->interested_count ?? rand(15, 75)); ?></div>
                <div class="trend trend-up">
                    <span class="trend-icon">↑</span> 5% from yesterday
                </div>
            </div>

            <div class="metric-card">
                <h3>Going People</h3>
                <div class="value"><?php echo number_format($request->going_count ?? rand(5, 40)); ?></div>
                <div class="trend trend-neutral">
                    <span class="trend-icon">→</span> Same as yesterday
                </div>
            </div>
        </div>

        <div id="analytics-charts">
            <div class="chart-tabs">
                <div class="chart-tab active" data-tab="views">Views Over Time</div>
                <div class="chart-tab" data-tab="engagement">Engagement Rate</div>
                <div class="chart-tab" data-tab="demographics">Demographics</div>
            </div>
            
            <div class="chart-container">
                <div class="chart-placeholder" id="chart-views">
                    <p>Views and engagement data visualization will be displayed here</p>
                </div>
            </div>
        </div>

        <div class="engagement-list">
            <h3>Recent Engagement</h3>
            
            <?php
            // Sample engagement data
            $engagements = [
                [
                    'user' => 'John D.',
                    'action' => 'marked as interested',
                    'time' => '2 hours ago'
                ],
                [
                    'user' => 'Sarah M.',
                    'action' => 'marked as going',
                    'time' => '3 hours ago'
                ],
                [
                    'user' => 'Arun K.',
                    'action' => 'shared the event',
                    'time' => '5 hours ago'
                ],
                [
                    'user' => 'Maria L.',
                    'action' => 'commented: "Looking forward to this!"',
                    'time' => '1 day ago'
                ],
                [
                    'user' => 'David T.',
                    'action' => 'marked as going',
                    'time' => '1 day ago'
                ]
            ];
            
            foreach ($engagements as $e):
                $initials = strtoupper(substr($e['user'], 0, 1));
            ?>
            <div class="engagement-item">
                <div class="engagement-avatar"><?php echo $initials; ?></div>
                <div class="engagement-content">
                    <div class="engagement-name"><?php echo htmlspecialchars($e['user']); ?></div>
                    <div class="engagement-action"><?php echo htmlspecialchars($e['action']); ?></div>
                </div>
                <div class="engagement-time"><?php echo htmlspecialchars($e['time']); ?></div>
            </div>
            <?php endforeach; ?>
        </div>

    <?php else: ?>
        <p>Event request not found.</p>
    <?php endif; ?>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const tabs = document.querySelectorAll('.chart-tab');
        
        tabs.forEach(tab => {
            tab.addEventListener('click', function() {
                // Remove active class from all tabs
                tabs.forEach(t => t.classList.remove('active'));
                
                // Add active class to clicked tab
                this.classList.add('active');
                
                // Here you would typically update the chart based on the selected tab
                // For now we'll just update the placeholder text
                const tabName = this.dataset.tab;
                document.getElementById('chart-views').innerHTML = `<p>${tabName.charAt(0).toUpperCase() + tabName.slice(1)} data visualization will be displayed here</p>`;
            });
        });
    });
</script>

<?php $content = ob_get_clean(); ?>
<?php require APPROOT . '/views/request_dashboards/request_dashboard_layout_adapter.php';?>