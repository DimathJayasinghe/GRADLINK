<?php ob_start(); ?>
<!-- Additional styles for the dashboard layout -->
<style>
    .cards-container {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 20px;
        padding-bottom: 20px;
    }
    
    .card {
        background: var(--card);
        border: 1px solid var(--border);
        border-radius: 5px;
        padding: 1.5rem;
        transition: transform 0.2s ease, box-shadow 0.2s ease, background 0.2s ease;
        text-decoration: none;
        color: var(--text);
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }
    
    .card:hover {
        box-shadow: 0 8px 18px rgba(0, 0, 0, 0.12);
        background: rgba(15, 21, 24, 0.5);
    }
    
    .card h3 {
        margin: 0 0 0.75rem;
        color: var(--link);
        font-size: 1.2rem;
        font-weight: 600;
    }
    
    .card p {
        margin: 0.25rem 0;
        font-size: 0.95rem;
        color: var(--muted);
    }
    
    .card .event-details {
        margin-top: 10px;
        padding-top: 10px;
        border-top: 1px solid var(--border);
    }
    
    .card .event-date-time {
        display: flex;
        justify-content: space-between;
        margin: 8px 0;
    }
    
    .card a {
        margin-top: auto;
        padding: 0.5rem 1rem;
        background: var(--link);
        color: #fff;
        border-radius: var(--radius-sm);
        text-align: center;
        text-decoration: none;
        font-weight: 600;
        transition: background 0.2s ease;
    }
    
    .card a:hover {
        background: #2563eb;
    }
    
    .rejected {
        color: var(--danger);
    }
    
    .pending {
        color: var(--warning);
    }
    
    .approved {
        color: var(--success);
    }
    
    .status-badge {
        display: inline-block;
        padding: 0.25rem 0.75rem;
        border-radius: var(--radius-full);
        font-weight: 600;
        font-size: 0.85rem;
        margin-left: 8px;
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
    
    .event-icon {
        background: rgba(79, 70, 229, 0.1);
        color: #4f46e5;
        padding: 4px 8px;
        border-radius: 4px;
        font-size: 0.8rem;
        font-weight: 600;
        display: inline-block;
        margin-right: 5px;
    }
    
    .empty-state {
        text-align: center;
        padding: 3rem;
        background: rgba(255, 255, 255, 0.03);
        border-radius: var(--radius-md);
        margin: 2rem auto;
        max-width: 600px;
    }
    
    .empty-state p {
        color: var(--muted);
        margin-bottom: 1.5rem;
    }
    
    .empty-state .btn {
        display: inline-block;
        padding: 0.75rem 1.5rem;
        background: var(--link);
        color: #fff;
        border-radius: var(--radius-md);
        text-decoration: none;
        font-weight: 600;
    }
</style>
<?php $styles = ob_get_clean(); ?>

<?php
    $sidebar_left = [
        ['label'=>'My Event Requests', 'url'=>'/eventrequest/all','active'=>true, 'icon'=>'user'],
        ['label'=>'Create Event Request', 'url'=>'/eventrequest','active'=>false, 'icon'=>'plus-circle']
    ]
?>

<?php ob_start(); ?>
<!-- Main content goes here -->
<div>
    <h2>My Event Requests</h2>
    <?php if(!empty($data['myrequests'])): ?>
        <div class="cards-container">
            <?php foreach($data['myrequests'] as $request): ?>
                <div class="card">
                    <h3>
                        <?php echo htmlspecialchars($request->title); ?>
                        <span class="status-badge status-<?php echo strtolower($request->status); ?>">
                            <?php echo htmlspecialchars($request->status); ?>
                        </span>
                    </h3>
                    <p class="description"><?php echo htmlspecialchars(substr($request->description, 0, 100)) . (strlen($request->description) > 100 ? '...' : ''); ?></p>
                    <p class="club-name">Club: <?php echo htmlspecialchars($request->club_name); ?></p>
                    <p class="created-at">Created: <?php echo date('M d, Y', strtotime($request->created_at)); ?></p>
                    
                    <div class="event-details">
                        <p><span class="event-icon">EVENT</span> Details:</p>
                        <p class="event-date-time">
                            <span>Date: <?php echo date('M d, Y', strtotime($request->event_date)); ?></span>
                            <span>Time: <?php echo date('h:i A', strtotime($request->event_time)); ?></span>
                        </p>
                        <p class="event-venue">Venue: <?php echo htmlspecialchars($request->event_venue); ?></p>
                    </div>
                    
                    <div style="display: flex; gap: 10px; margin-top: 15px;">
                        <a href="<?php echo URLROOT; ?>/eventrequest/show/<?php echo $request->req_id; ?>" style="flex: 1;">View Details</a>
                        <?php if($request->status === 'Approved'): ?>
                            <a href="<?php echo URLROOT; ?>/eventrequest/analytics/<?php echo $request->req_id; ?>" style="flex: 1; background: #6c5ce7;">Analytics</a>
                        <?php else: ?>
                            <a href="<?php echo URLROOT; ?>/eventrequest/edit/<?php echo $request->req_id; ?>" style="flex: 1; background: #1ec38fff;">Edit</a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="empty-state">
            <h3>You haven't created any event requests yet</h3>
            <p>Create your first event request to promote campus activities and events.</p>
            <a href="<?php echo URLROOT; ?>/eventrequest" class="btn">Create Event Request</a>
        </div>
    <?php endif; ?>
</div>

<?php $content = ob_get_clean(); ?>
<?php require APPROOT . '/views/request_dashboards/request_dashboard_layout_adapter.php';?>