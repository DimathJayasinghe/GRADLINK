<?php ob_start(); ?>
<!-- Additional styles for the dashboard layout -->
 <style>
    .cards-container{display: grid;grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));gap: 20px;padding-bottom: 20px;}
    .cards-container{display: grid;grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));gap: 20px;padding-bottom: 20px;}
    .card {background: var(--card);border: 1px solid var(--border);border-radius: 5px;padding: 1.5rem;transition: transform 0.2s ease, box-shadow 0.2s ease, background 0.2s ease;text-decoration: none;color: var(--text);display: flex;flex-direction: column;justify-content: space-between;}
    .card:hover {box-shadow: 0 8px 18px rgba(0, 0, 0, 0.12);background: rgba(15, 21, 24, 0.5);}
    .card h3 {margin: 0 0 0.75rem;color: var(--link);font-size: 1.2rem;font-weight: 600;}
    .card p {margin: 0.25rem 0;font-size: 0.95rem;color: var(--muted);}
    .card .amount {font-weight: 600;}
    .card .progress-container {position: relative;background: var(--border);border-radius: 6px;height: 10px;margin: 0.5rem 0 1rem 0;overflow: hidden;}
    .card .progress-fill {display: block;background: linear-gradient(90deg, #4caf50, #2e7d32);height: 100%;border-radius: 6px;transition: width 0.4s ease;}
    .card .progress-text {margin-bottom: 0.25rem;font-size: 0.9rem;font-weight: 600;color: var(--muted);}
    .card a {margin-top: auto;padding: 0.5rem 1rem;background: var(--link);color: #fff;border-radius: var(--radius-sm);text-align: center;text-decoration: none;font-weight: 600;transition: background 0.2s ease;}
    .card a:hover {background: #2563eb;}
    .expired-card {opacity: 0.6;}
    .rejected {color: var(--danger);}
    .pending {color: var(--warning);}
    .approved {color: var(--success);}
    .expired {color: var(--muted); font-weight: 600;}
</style>
<?php $styles = ob_get_clean(); ?>

<?php
    $sidebar_left = [
        ['label'=>'View All Fundraise Requests', 'url'=>'/fundraiser/all','active'=>true, 'icon'=>'list'],
        ['label'=>'Create Fundraise Request', 'url'=>'/fundraiser/request','active'=>false, 'icon' => 'plus-circle'],
    ]
?>


<?php ob_start(); ?>
<div>
<!-- Main content goes here -->
<h2>All Fundraise Requests</h2>
<?php if($data['fundraise_reqs']): ?>
    <div class="cards-container">
        <?php 
        foreach($data['fundraise_reqs'] as $req): 
            $timeleft = (new DateTime())->diff(new DateTime($req->deadline));
            $expired = $timeleft->invert;
            $percentage = ($req->raised_amount / $req->target_amount) * 100;
        ?>
            <div class="card <?php 
            if($expired) {
                echo 'expired-card';
            }; 
            ?>">
                <h3><?php echo htmlspecialchars($req->title); 
                    if($expired){
                        echo '<span class="expired">[EXPIRED]</span>';
                    }else{
                        switch ($req->status) {
                            case 'Pending':
                                echo '<span class="pending">[PENDING]</span>';
                                break;
                            case 'Approved':
                                echo '<span class="approved">[APPROVED]</span>';
                                break;
                            case 'Rejected':
                                echo '<span class="rejected">[REJECTED]</span>';
                                break;
                            default:
                                echo '';
                        }
                    };?>
                </h3>
                <p class="description"><?php echo htmlspecialchars($req->description); ?></p>
                <p class="club-name">Club: <?php echo htmlspecialchars($req->club_name); ?></p>
                <p class="amount target">Target Amount: Rs.<?php echo number_format($req->target_amount, 2); ?></p>
                <p class="amount raised">Raised Amount: Rs.<?php echo number_format($req->raised_amount, 2); ?></p>
                <p class="progress-text">Progress: <?php echo number_format($percentage, 2); ?>%</p>
                <div class="progress-container">
                    <div class="progress-fill" style="width: <?php echo $percentage; ?>%"></div>
                </div>
                <p>Deadline: <?php echo htmlspecialchars($req->deadline); ?></p>
                <?php if (!$expired){echo '<p>Time Left: '.$timeleft->days.' days</p>';}?>
                <p class="status">Status: <?php echo htmlspecialchars($req->status); ?></p>
                <a href="<?php echo URLROOT; ?>/fundraiser/show/<?php echo $req->req_id; ?>">View Details</a>
            </div>
        <?php endforeach; ?>
    </div>
<?php else: ?>
    <p>No fundraise requests found.</p>
<?php endif; ?>
</div>

<?php $content = ob_get_clean(); ?>
<?php require APPROOT . '/views/request_dashboards/dashboard_layout.php';?>