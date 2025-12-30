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

.status-completed {
    background: rgba(33, 150, 243, 0.2);
    color: #2196f3;
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
        ['label'=>'View my Fundraise Requests', 'url'=>'/fundraiser/myrequests','active'=>false, 'icon'=>'user'],
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
        
        <?php 
        // Only show donation button for Approved or Active campaigns
        if (in_array($target_post->status, ['Approved', 'Active']) && !$expired): 
        ?>
            <button style="background-color:#4caf50" onclick="GL_openDonationModal()">
                <span class="btn" style="color:#ffffff">Make a Donation</span>
            </button>
        <?php else: ?>
            <?php if ($target_post->status === 'Pending'): ?>
                <p style="color: var(--warning); margin-top: 1rem; padding: 0.75rem; background: rgba(255, 193, 7, 0.1); border-radius: var(--radius-md);">
                    ⏳ This campaign is pending approval. Donations will be enabled once approved.
                </p>
            <?php elseif ($target_post->status === 'Rejected'): ?>
                <p style="color: var(--danger); margin-top: 1rem; padding: 0.75rem; background: rgba(220, 53, 69, 0.1); border-radius: var(--radius-md);">
                    ❌ This campaign has been rejected and cannot accept donations.
                    <?php if (!empty($target_post->rejection_reason)): ?>
                        <br><strong>Reason:</strong> <?php echo htmlspecialchars($target_post->rejection_reason); ?>
                    <?php endif; ?>
                </p>
            <?php elseif ($expired): ?>
                <p style="color: var(--muted); margin-top: 1rem; padding: 0.75rem; background: rgba(255, 255, 255, 0.05); border-radius: var(--radius-md);">
                    ⏰ This campaign has expired and is no longer accepting donations.
                </p>
            <?php endif; ?>
        <?php endif; ?>

        
        <!-- Donation Timeline & Impact Visualization -->
        <div id="donation-analytics" style="margin-top: 2rem;">
            <h3 style="margin-bottom: 1.5rem; font-weight: 600; font-size: 1.3rem; color: var(--text);">Donation Analytics</h3>
            
            <?php if (!empty($data['donations']) && count($data['donations']) > 0): ?>
                <?php 
                // Check if current user is the campaign owner
                $isOwner = isset($_SESSION['user_id']) && $_SESSION['user_id'] == $target_post->user_id;
                ?>
                
                <!-- Donation Stats Summary (Owner Only) -->
                <?php if ($isOwner): ?>
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 1rem; margin-bottom: 2rem;">
                        <div style="background: rgba(76, 175, 80, 0.1); padding: 1rem; border-radius: var(--radius-md); border-left: 4px solid #4caf50;">
                            <p style="margin: 0; font-size: 0.85rem; color: var(--muted);">Total Donors</p>
                            <p style="margin: 0.25rem 0 0 0; font-size: 1.5rem; font-weight: 700; color: #4caf50;"><?php echo count($data['donations']); ?></p>
                        </div>
                        <div style="background: rgba(33, 150, 243, 0.1); padding: 1rem; border-radius: var(--radius-md); border-left: 4px solid #2196f3;">
                            <p style="margin: 0; font-size: 0.85rem; color: var(--muted);">Average Donation</p>
                            <p style="margin: 0.25rem 0 0 0; font-size: 1.5rem; font-weight: 700; color: #2196f3;">
                                Rs.<?php echo number_format($target_post->raised_amount / count($data['donations']), 2); ?>
                            </p>
                        </div>
                        <div style="background: rgba(255, 152, 0, 0.1); padding: 1rem; border-radius: var(--radius-md); border-left: 4px solid #ff9800;">
                            <p style="margin: 0; font-size: 0.85rem; color: var(--muted);">Remaining</p>
                            <p style="margin: 0.25rem 0 0 0; font-size: 1.5rem; font-weight: 700; color: #ff9800;">
                                Rs.<?php echo number_format($target_post->target_amount - $target_post->raised_amount, 2); ?>
                            </p>
                        </div>
                    </div>
                <?php endif; ?>
                
                <!-- Donations Over Time Chart (For Everyone) -->
                <div style="background: rgba(255,255,255,0.03); border: 1px solid var(--border); border-radius: var(--radius-md); padding: 1.5rem; margin-bottom: 2rem;">
                    <h4 style="margin: 0 0 1.5rem 0; font-weight: 600; font-size: 1.1rem; color: var(--text);">Donations Over Time</h4>
                    <canvas id="donationsChart" style="max-height: 300px;"></canvas>
                </div>
                
                <!-- Only show detailed contributors list to campaign owner -->
                <?php if ($isOwner): ?>
                    <!-- Recent Contributions (Owner Only) -->
                    <div style="background: rgba(255,255,255,0.03); border: 1px solid var(--border); border-radius: var(--radius-md); padding: 1.5rem;">
                        <h4 style="margin: 0 0 1.5rem 0; font-weight: 600; font-size: 1.1rem; color: var(--text);">Recent Contributions</h4>
                        
                        <?php 
                        foreach ($data['donations'] as $index => $donation): 
                            $donorName = $donation->is_anonymous ? 'Anonymous Donor' : ($donation->display_name ?? $donation->donor_name ?? 'Anonymous');
                        ?>
                            <div style="margin-bottom: 1.5rem; padding-bottom: 1.5rem; border-bottom: 1px solid rgba(255,255,255,0.05);">
                                <div style="display: flex; justify-content: space-between; align-items: start;">
                                    <div style="flex: 1;">
                                        <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.5rem;">
                                            <?php if (!$donation->is_anonymous && !empty($donation->profile_image)): ?>
                                                <img src="<?php echo URLROOT . '/public/img/profiles/' . $donation->profile_image; ?>" 
                                                     alt="<?php echo htmlspecialchars($donorName); ?>" 
                                                     style="width: 32px; height: 32px; border-radius: 50%; object-fit: cover;">
                                            <?php else: ?>
                                                <div style="width: 32px; height: 32px; border-radius: 50%; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display: flex; align-items: center; justify-content: center; color: white; font-weight: 600; font-size: 0.9rem;">
                                                    <?php echo substr($donorName, 0, 1); ?>
                                                </div>
                                            <?php endif; ?>
                                            <div>
                                                <p style="margin: 0; font-weight: 600; color: var(--text); font-size: 1rem;">
                                                    <?php echo htmlspecialchars($donorName); ?>
                                                </p>
                                                <p style="margin: 0.25rem 0 0 0; font-size: 0.85rem; color: var(--muted);">
                                                    <?php echo date('M j, Y g:i A', strtotime($donation->created_at)); ?>
                                                </p>
                                            </div>
                                        </div>
                                        <?php if (!empty($donation->message)): ?>
                                            <p style="margin: 0.5rem 0 0 2.75rem; padding: 0.5rem 0.75rem; background: rgba(255,255,255,0.03); border-left: 2px solid var(--link); border-radius: var(--radius-sm); font-size: 0.9rem; color: var(--muted); font-style: italic;">
                                                "<?php echo htmlspecialchars($donation->message); ?>"
                                            </p>
                                        <?php endif; ?>
                                    </div>
                                    <div style="text-align: right; margin-left: 1rem;">
                                        <p style="margin: 0; font-size: 1.3rem; font-weight: 700; color: #4caf50;">
                                            +Rs.<?php echo number_format($donation->amount, 2); ?>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
                
                <!-- Chart.js Script -->
                <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
                <script>
                    // Prepare data for chart
                    const donations = <?php echo json_encode(array_reverse($data['donations'])); ?>;
                    
                    // Calculate cumulative amounts over time
                    let cumulativeAmount = 0;
                    const chartData = donations.map(donation => {
                        cumulativeAmount += parseFloat(donation.amount);
                        return {
                            date: new Date(donation.created_at),
                            amount: cumulativeAmount
                        };
                    });
                    
                    const ctx = document.getElementById('donationsChart').getContext('2d');
                    new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: chartData.map(d => d.date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' })),
                            datasets: [{
                                label: 'Total Raised (Rs.)',
                                data: chartData.map(d => d.amount),
                                borderColor: '#4caf50',
                                backgroundColor: 'rgba(76, 175, 80, 0.1)',
                                borderWidth: 2,
                                fill: true,
                                tension: 0.4,
                                pointRadius: 4,
                                pointHoverRadius: 6,
                                pointBackgroundColor: '#4caf50',
                                pointBorderColor: '#fff',
                                pointBorderWidth: 2
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: true,
                            plugins: {
                                legend: {
                                    display: true,
                                    labels: {
                                        color: '#fff',
                                        font: {
                                            size: 12
                                        }
                                    }
                                },
                                tooltip: {
                                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                                    titleColor: '#fff',
                                    bodyColor: '#fff',
                                    borderColor: '#4caf50',
                                    borderWidth: 1,
                                    callbacks: {
                                        label: function(context) {
                                            return 'Total: Rs.' + context.parsed.y.toLocaleString('en-LK', {minimumFractionDigits: 2, maximumFractionDigits: 2});
                                        }
                                    }
                                }
                            },
                            scales: {
                                x: {
                                    grid: {
                                        color: 'rgba(255, 255, 255, 0.05)'
                                    },
                                    ticks: {
                                        color: '#aaa',
                                        font: {
                                            size: 11
                                        }
                                    }
                                },
                                y: {
                                    beginAtZero: true,
                                    grid: {
                                        color: 'rgba(255, 255, 255, 0.05)'
                                    },
                                    ticks: {
                                        color: '#aaa',
                                        font: {
                                            size: 11
                                        },
                                        callback: function(value) {
                                            return 'Rs.' + value.toLocaleString();
                                        }
                                    }
                                }
                            }
                        }
                    });
                </script>
            <?php else: ?>
                <div style="background: rgba(255,255,255,0.03); border: 1px solid var(--border); border-radius: var(--radius-md); padding: 2rem; text-align: center;">
                    <p style="margin: 0; color: var(--muted); font-size: 1rem;">
                        📊 No donations yet. Be the first to support this campaign!
                    </p>
                </div>
            <?php endif; ?>
        </div>
    <?php else: ?>
        <p>Fundraise request not found.</p>
    <?php endif; ?>
</div>

<?php 
    // Provide $campaign for the modal summary; use the same selected request
    if(isset($target_post)) { $campaign = $target_post; }
    require APPROOT . '/views/request_dashboards/fundraise/_donation_modal.php';
?>

<?php $content = ob_get_clean(); ?>
<?php require APPROOT . '/views/request_dashboards/request_dashboard_layout_adapter.php';?>