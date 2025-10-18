<?php ob_start(); ?>
<style>
    /* Center column: event / request list */
    .event-card,
    .event-card { /* new event-friendly class */
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px 14px;
        border-bottom: 1px solid var(--border);
        cursor: pointer;
        transition: background 0.2s ease;
        text-decoration: none;
        color: inherit;
    }

    .event-card:hover,
    .event-card:hover {
        background: rgba(255, 255, 255, 0.04);
    }

    .event-card.active-selected-event,
    .event-card.active-selected-event {
        background: rgba(158, 212, 220, 0.1);
    }

    .profile-pic-new-event img,
    .profile-pic-event img {
        width: 44px;
        height: 44px;
        border-radius: 50%;
        object-fit: cover;
    }

    .event-details h3,
    .event-details h3 {
        margin: 0;
        font-size: 15px;
        color: var(--text);
    }

    .event-details p,
    .event-details p {
        margin: 2px 0 6px;
        font-size: 12px;
        color: var(--muted);
    }

    .status {
        display: inline-block;
        padding: 2px 8px;
        border-radius: var(--radius-full);
        font-size: 11px;
        font-weight: 600;
    }

    .status-pending,
    .status-event-pending {
        background: rgba(255, 193, 7, 0.15);
        color: #ffc107;
    }
    .status-approved,
    .status-event-approved {
        background: rgba(40, 167, 69, 0.15);
        color: #28a745;
    }
    .status-rejected,
    .status-event-rejected {
        background: rgba(220, 53, 69, 0.15);
        color: #dc3545;
    }

    /* Make the center column a flex container so the list can scroll */
    .template-center {
        display: flex;
        flex-direction: column;
    }
    .template-center .center-topic { flex: 0 0 auto; }
    .event-list {
        flex: 1 1 auto;
        overflow-y: auto;
        min-height: 0; /* allow flex item to shrink for overflow */
    }

    /* Right column: detail panel (event / request details) */
    .rightsidebar_content,
    .rightsidebar_event_content {
        padding: 20px 30px 30px;
    }


    .name-of-the-request-holder .description {
        color: var(--muted);
        font-size: 13px;
    }

    .profile-image img,
    .event-image img {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        object-fit: cover;
        margin: 16px 0;
    }

    .detail-grid {
        display: grid;
        grid-template-columns: 160px 1fr;
        gap: 8px 12px;
        margin: 16px 0 20px;
    }

    .detail-label {
        color: var(--muted);
        font-size: 13px;
    }

    .detail-value {
        color: var(--text);
        font-weight: 500;
        font-size: 14px;
    }

    .action-row {
        display: flex;
        gap: 10px;
        margin-top: 8px;
    }

    .btn-approve,
    .btn-reject {
        border: none;
        padding: 10px 14px;
        border-radius: var(--radius-sm);
        cursor: pointer;
        font-weight: 600;
    }

    .btn-approve {
        background: #28a745;
        color: #fff;
    }

    .btn-reject {
        background: #dc3545;
        color: #fff;
    }

    .empty-state {
        color: var(--muted);
        margin-top: 20px;
    }
    .Empty-section{
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-align: center;
        min-height: 77vh;
    }
    .Empty-section h2{
        margin: 0 0 0px;
        font-size: 20px;
        font-weight: 600;
        /* color: var(--text); */
        /* color: rgba(237, 237, 237, 0.74); */
        color: rgba(255, 255, 255, 0.12);
    }
    .Empty-section .empty-state{
        margin: 0px;
        margin-top: 5px;
        font-size: 15px;
        /* color: var(--muted); */
        color: rgba(255, 255, 255, 0.12);
        max-width: 520px;
    }
    .section-topic{
        margin-top: 8px;
        margin-bottom: 10px;
        font-size: 24px;
        font-weight: 600;
        color: var(--text);
    }
</style>
<?php $styles = ob_get_clean(); ?>

<?php ob_start(); ?>
<!-- Center column: list all events / pending join events -->
<?php if (!empty($data['events'])): ?>
<div class="event-list" style="overflow-y: auto;">
    <?php foreach ($data['events'] as $request): ?>
        <a class="event-card<?php echo (isset($data['selected_event_id']) && (string)$request->req_id === (string)$data['selected_event_id']) ? 'active-selected-event' : ''; ?>" href="<?php echo URLROOT; ?>/alumni/approve?req_id=<?php echo urlencode($request->req_id); ?>">
            <div class="profile-pic-new-event" data-req-id="<?php echo htmlspecialchars($request->req_id); ?>">
                <img src="<?php echo URLROOT; ?>/media/profile/<?php echo htmlspecialchars($request->profile); ?>" alt="profile">
            </div>
            <div class="event-details">
                <h3><?php echo htmlspecialchars($request->Name); ?></h3>
                <p><?php echo htmlspecialchars($request->Batch); ?></p>
                <span class="status status-<?php echo strtolower($request->status); ?> status-event-<?php echo strtolower($request->status); ?>"><?php echo htmlspecialchars($request->status); ?></span>
            </div>
        </a>
    <?php endforeach; ?>

</div>
<?php else: ?>
    <div class="empty-state" style="text-align: center;">No upcomming events.</div>
<?php endif; ?>
<?php $center_content = ob_get_clean(); ?>

<?php ob_start(); ?>
<!-- Right column: user-selected details -->
<div class="rightsidebar_content">




<?php $content = ob_get_clean(); ?>

<?php ob_start(); ?>
// Placeholder actions; integrate with backend endpoints when ready
document.addEventListener('click', (e) => {
    // Support both legacy approve/reject buttons and new add/remove calendar actions
    const approveBtn = e.target.closest('.btn-approve');
    const rejectBtn = e.target.closest('.btn-reject');
    const addCalBtn = e.target.closest('.btn-add-calendar');
    const removeEvtBtn = e.target.closest('.btn-remove-event');

    if (addCalBtn) {
        const id = addCalBtn.getAttribute('data-req-id');
        // TODO: POST to backend to add to calendar
        alert('Added request/event #' + id + ' to calendar (wire up backend)');
        return;
    }

    if (removeEvtBtn) {
        const id = removeEvtBtn.getAttribute('data-req-id');
        // TODO: POST to backend to remove from calendar
        alert('Removed request/event #' + id + ' from calendar (wire up backend)');
        return;
    }

    if (approveBtn) {
        const id = approveBtn.getAttribute('data-req-id');
        // TODO: POST to backend to approve
        alert('Approved request #' + id + ' (wire up backend)');
    }

    if (rejectBtn) {
        const id = rejectBtn.getAttribute('data-req-id');
        // TODO: POST to backend to reject
        alert('Rejected request #' + id + ' (wire up backend)');
    }
});
<?php $scripts = ob_get_clean(); ?>

<?php require APPROOT."/views/calender/v_layout_adapter.php"?>