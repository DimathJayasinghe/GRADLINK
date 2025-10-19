<?php ob_start(); ?>
<!-- Additional styles for the dashboard layout -->
<style>
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
    font-size: 0.9rem;
    line-height: 1.5;
    font-weight: normal;
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
        ['label' => 'View Calendar', 'url'=>'/calender', 'active'=>false, 'icon'=>'calendar'],
        ['label'=>'Bookmarked Events', 'url'=>'/calender/bookmarks', 'active'=>false, 'icon'=>'bookmark'],
        ['label' => 'Details', 'url'=>'/calender/show/'.$data['event']->event_id, 'active'=>true, 'icon'=>'info-circle']
    ]
?>

<?php ob_start(); ?>
<div class="details-container">
    <?php
        $request = $data['event'];
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
                <span class="info-label">Venue</span>
                <span class="info-value"><?php echo htmlspecialchars($request->event_venue); ?></span>
            </div>
            
            <div class="details-info-item">
                <span class="info-label">Date</span>
                <span class="info-value"><?php echo date('M d, Y', strtotime($request->event_date)); ?></span>
            </div>
            <div class="details-info-item">
                <span class="info-label">Time</span>
                <span class="info-value"><?php echo date('h:i A', strtotime($request->event_time)); ?></span>
            </div>
            
            <div class="details-info-item" style="padding: 0px;display: flex; align-items: center; justify-content: center;">
                <?php if($request->bookmarked): ?>
                <button style="background-color:#4caf50" style="margin: 0px; ">
                    <span class="btn" style="color:#ffffff">Add to Bookmark !</span>
                </button>
                <?php else: ?>
                    <button style="background-color:#ec2424ff" style="margin: 0px; ">
                        <span class="btn" style="color:#ffffff">Remove Bookmark</span>
                    </button>
                <?php endif; ?>
                </div>
        </div>
        
        <div class="event-info">
            <!-- <h3>Event Details</h3> -->
            <?php if($request->attachment_image): ?>
                <div class="image-container">
                    <img src="<?php echo URLROOT; ?>/Media/event/<?php echo htmlspecialchars($request->attachment_image); ?>" alt="Event Image" class="request-image">
                </div>
            <?php else: ?>
                <div class="image-container">
                    <div class="no-image">No image attached to this request</div>
                </div>
            <?php endif; ?>
        </div>


        <div class="action-buttons">
            <a href="<?php echo URLROOT; ?>/calender/" class="btn btn-back">Back to All Event Requests</a>
        </div>
    <?php else: ?>
        <p>Event request not found.</p>
        <div class="action-buttons">
            <a href="<?php echo URLROOT; ?>/calender/" class="btn btn-back">Back to All Event Requests</a>
        </div>
    <?php endif; ?>
</div>

<?php $content = ob_get_clean(); ?>
<?php require APPROOT . '/views/calender/v_layout_adapter.php';?>