<?php ob_start(); ?>
<style>
.analytics-container {
    padding: 1rem;
}

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
    font-size: 1rem;
    line-height: 1.5;
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

.analytics-container .amount {
    font-weight: 600;
    color: var(--link);
}

.analytics-container .progress-text {
    font-weight: 600;
    margin-top: 1rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.progress-container {
    position: relative;
    width: 100%;
    background: var(--border);
    border-radius: 10px;
    height: 14px;
    overflow: hidden;
    margin: 0.5rem 0 1.5rem;
}

.progress-fill {
    height: 100%;
    background: linear-gradient(90deg, #4caf50, #2e7d32);
    border-radius: 10px 0 0 10px;
    transition: width 0.4s ease;
}

.analytics-container .expired {
    color: var(--danger);
    font-weight: bold;
    display: inline-block;
    margin-left: 0.5rem;
    padding: 0.2rem 0.5rem;
    border-radius: var(--radius-sm);
    background: rgba(220, 53, 69, 0.1);
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

#analytics-charts {
    margin-top: 2rem;
    padding: 1.5rem;
    background: rgba(255,255,255,0.03);
    border: 1px dashed var(--border);
    border-radius: var(--radius-md);
    text-align: left;
    color: var(--muted);
}

#analytics-charts h3 {
    margin-bottom: 1rem;
    font-weight: 600;
    font-size: 1.2rem;
}

.chart-placeholder {
    height: 190px;
    background: linear-gradient(180deg, 
                rgba(255, 255, 255, 0.02) 0%, 
                rgba(255, 255, 255, 0.05) 100%);
    border-radius: var(--radius-md);
    display: flex;
    align-items: center;
    justify-content: center;
}

@media (max-width: 768px) {
    .analytics-info {
        grid-template-columns: 1fr;
    }
    
    .analytics-container {
        padding: 1.5rem;
    }
}
</style>

<?php $styles = ob_get_clean(); ?>

<?php
    $sidebar_left = [
        ['label'=>'View All Fundraise Requests', 'url'=>'/fundraiser/all', 'active'=>false, 'icon'=>'list'],
        ['label'=>'Create Fundraise Request', 'url'=>'/fundraiser/request', 'active'=>false, 'icon'=>'plus-circle'],
        ['label'=>'Analytics', 'url'=>'#', 'active'=>true, 'icon'=>'chart-line']
    ]
?>


<?php ob_start(); ?>
<div class="analytics-container">
    <?php
        $target_post = null;
        foreach ($data['fundraise_reqs'] as $req){
            if($req->req_id == $data['req_id']){
                $target_post = $req;
                break;
            }
        }
    ?>

    <?php if($target_post): ?>
        <div class="analytics-header">
            <h2>Analytics for: <?php echo htmlspecialchars($target_post->title); ?></h2>
            <p class="description"><?php echo htmlspecialchars($target_post->description); ?></p>
        </div>

        <div class="analytics-info">
            <div class="analytics-info-item">
                <span class="info-label">Club</span>
                <span class="info-value"><?php echo htmlspecialchars($target_post->club_name); ?></span>
            </div>
            
            <div class="analytics-info-item">
                <span class="info-label">Target Amount</span>
                <span class="info-value amount">Rs.<?php echo number_format($target_post->target_amount, 2); ?></span>
            </div>
            
            <div class="analytics-info-item">
                <span class="info-label">Raised Amount</span>
                <span class="info-value amount">Rs.<?php echo number_format($target_post->raised_amount, 2); ?></span>
            </div>

            <?php 
                $percentage = ($target_post->raised_amount / $target_post->target_amount) * 100;
                $timeleft = (new DateTime())->diff(new DateTime($target_post->deadline));
                $expired = $timeleft->invert;
            ?>
            
            <div class="analytics-info-item">
                <span class="info-label">Deadline</span>
                <span class="info-value">
                    <?php echo htmlspecialchars($target_post->deadline); ?>
                    <?php if ($expired): ?>
                        <span class="expired">EXPIRED</span>
                    <?php endif; ?>
                </span>
            </div>
        </div>

        <p class="progress-text">
            <span>Progress</span>
            <span><?php echo number_format($percentage, 2); ?>%</span>
        </p>
        <div class="progress-container">
            <div class="progress-fill" style="width: <?php echo $percentage; ?>%"></div>
        </div>
        
        <?php if (!$expired): ?>
            <p>Time Left: <?php echo $timeleft->days; ?> days</p>
        <?php endif; ?>
        
        <p class="status">Status: 
            <span class="status-badge status-<?php echo strtolower($target_post->status); ?>">
                <?php echo htmlspecialchars($target_post->status); ?>
            </span>
        </p>

        <!-- Analytics charts with placeholders -->
        <div id="analytics-charts">
            <h3>Fundraising Analytics</h3>
            <div class="chart-placeholder">
                <p>charts will be displayed here</p>
            </div>
        </div>
    <?php else: ?>
        <p>Fundraise request not found.</p>
    <?php endif; ?>
</div>

<?php $content = ob_get_clean(); ?>
<?php require APPROOT . '/views/request_dashboards/dashboard_layout.php';?>