<?php ob_start(); ?>
<!-- Additional styles for the dashboard layout -->
<style>
.details-container {
    padding: 1rem;
}

.details-header {
    margin-bottom: 1.5rem;
}

.details-container h2 {
    font-size: 1.6rem;
    font-weight: 700;
    margin-bottom: 0.75rem;
    color: var(--text);
}

.details-container .description {
    margin: 0.5rem 0 1rem;
    color: var(--muted);
    font-size: 1rem;
    line-height: 1.5;
}

.details-info {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: 1rem;
    margin-bottom: 1.5rem;
}

.details-info-item {
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

.details-container p {
    margin: 0.35rem 0;
    font-size: 0.95rem;
    color: var(--text);
}

.event-info {
    margin-top: 1.5rem;
    background: rgba(255, 255, 255, 0.03);
    border-radius: var(--radius-md);
    padding: 1.5rem;
    border: 1px solid var(--border);
}

.event-info h3 {
    margin-top: 0;
    font-size: 1.2rem;
    font-weight: 600;
    color: var(--text);
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

.image-container {
    margin: 1.5rem 0;
    text-align: center;
}

.request-image {
    max-width: 100%;
    max-height: 400px;
    border-radius: var(--radius-md);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.no-image {
    background: rgba(255, 255, 255, 0.05);
    padding: 2rem;
    border-radius: var(--radius-md);
    text-align: center;
    color: var(--muted);
}

.action-buttons {
    display: flex;
    gap: 1rem;
    margin-top: 2rem;
}

.btn {
    padding: 0.5rem 1.5rem;
    border-radius: var(--radius-sm);
    font-weight: 600;
    cursor: pointer;
    transition: opacity 0.2s ease;
    text-decoration: none;
    text-align: center;
}

.btn:hover {
    opacity: 0.9;
}

.btn-primary {
    background: var(--link);
    color: #fff;
}

.btn-danger {
    background: var(--danger);
    color: #fff;
}

.btn-back {
    background: var(--card);
    color: var(--text);
    border: 1px solid var(--border);
}

@media (max-width: 768px) {
    .details-info {
        grid-template-columns: 1fr;
    }
    
    .details-container {
        padding: 1rem 0.5rem;
    }
    
    .action-buttons {
        flex-direction: column;
        gap: 0.5rem;
    }
}
</style>
<?php $styles = ob_get_clean(); ?>

<?php
    $sidebar_left = [
        ['label'=>'My Event Requests', 'url'=>'/postrequest/all', 'active'=>false, 'icon'=>'user'],
        ['label'=>'Create Event Request', 'url'=>'/postrequest', 'active'=>false, 'icon'=>'plus-circle'],
        ['label' => 'Details', 'url'=>'/postrequest/show/'.$data['request']->req_id, 'active'=>true, 'icon'=>'info-circle']
    ]
?>

<?php ob_start(); ?>
<div class="details-container">
    <?php
        $request = $data['request'];
    ?>

    <?php if($request): ?>
        <div class="details-header">
            <h2><?php echo htmlspecialchars($request->title); ?></h2>
            <p class="description"><?php echo htmlspecialchars($request->description); ?></p>
        </div>

        <div class="details-info">
            <div class="details-info-item">
                <span class="info-label">Club</span>
                <span class="info-value"><?php echo htmlspecialchars($request->club_name); ?></span>
            </div>
            
            <div class="details-info-item">
                <span class="info-label">Requested By</span>
                <span class="info-value"><?php echo htmlspecialchars($request->user_name); ?></span>
            </div>
            
            <div class="details-info-item">
                <span class="info-label">Created At</span>
                <span class="info-value"><?php echo date('M d, Y', strtotime($request->created_at)); ?></span>
            </div>
            
            <div class="details-info-item">
                <span class="info-label">Status</span>
                <span class="status-badge status-<?php echo strtolower($request->status); ?>">
                    <?php echo htmlspecialchars($request->status); ?>
                </span>
            </div>
        </div>
        
        <div class="event-info">
            <h3>Event Details</h3>
            <p><strong>Event Date:</strong> <?php echo date('M d, Y', strtotime($request->event_date)); ?></p>
            <p><strong>Event Time:</strong> <?php echo date('h:i A', strtotime($request->event_time)); ?></p>
            <p><strong>Venue:</strong> <?php echo htmlspecialchars($request->event_venue); ?></p>
        </div>

        <?php if($request->attachment_image): ?>
            <div class="image-container">
                <img src="<?php echo URLROOT; ?>/Media/post/<?php echo htmlspecialchars($request->attachment_image); ?>" alt="Event Image" class="request-image">
            </div>
        <?php else: ?>
            <div class="image-container">
                <div class="no-image">No image attached to this request</div>
            </div>
        <?php endif; ?>

        <div class="action-buttons">
            <a href="<?php echo URLROOT; ?>/postrequest/all" class="btn btn-back">Back to All Event Requests</a>
            
            <?php if($request->status === 'Pending' && isset($_SESSION['user_id']) && $_SESSION['user_id'] === $request->user_id): ?>
                <a href="<?php echo URLROOT; ?>/postrequest/edit/<?php echo $request->req_id; ?>" class="btn btn-primary">Edit Event Request</a>
                <a href="<?php echo URLROOT; ?>/postrequest/delete/<?php echo $request->req_id; ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this event request?')">Delete Event Request</a>
            <?php endif; ?>
        </div>
    <?php else: ?>
        <p>Event request not found.</p>
        <div class="action-buttons">
            <a href="<?php echo URLROOT; ?>/postrequest/all" class="btn btn-back">Back to All Event Requests</a>
        </div>
    <?php endif; ?>
</div>

<?php $content = ob_get_clean(); ?>
<?php require APPROOT . '/views/request_dashboards/request_dashboard_layout_adapter.php';?>