<?php ob_start(); ?>
<style>
    /* Center column: request list */
    .request-card {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px 14px;
        border-bottom: 1px solid var(--border);
        cursor: pointer;
        transition: background 0.2s ease;
    }

    .request-card {
        text-decoration: none;
        color: inherit;
    }

    .request-card:hover {
        background: rgba(255, 255, 255, 0.04);
    }

    .request-card.active-selected-request {
        background: rgba(158, 212, 220, 0.1);
    }

    .profile-pic-new-alumni img {
        width: 44px;
        height: 44px;
        border-radius: 50%;
        object-fit: cover;
    }

    .new-commer-details h3 {
        margin: 0;
        font-size: 15px;
        color: var(--text);
    }

    .new-commer-details p {
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

    .status-pending {
        background: rgba(255, 193, 7, 0.15);
        color: #ffc107;
    }

    .status-approved {
        background: rgba(40, 167, 69, 0.15);
        color: #28a745;
    }

    .status-rejected {
        background: rgba(220, 53, 69, 0.15);
        color: #dc3545;
    }

    /* Make the center column a flex container so the list can scroll */
    .template-center {
        display: flex;
        flex-direction: column;
    }
    .template-center .center-topic { flex: 0 0 auto; }
    .request-list {
        flex: 1 1 auto;
        overflow-y: auto;
        min-height: 0; /* allow flex item to shrink for overflow */
    }

    /* Right column: detail panel */
    .rightsidebar_content {
        padding: 20px 30px 30px;
    }


    .name-of-the-request-holder .description {
        color: var(--muted);
        font-size: 13px;
    }

    .profile-image img {
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
<!-- Center column: list all pending join requests -->
<?php if (!empty($data['requests'])): ?>
<div class="request-list" style="overflow-y: auto;">
    <?php foreach ($data['requests'] as $request): ?>
        <a class="request-card <?php echo (isset($data['selected_req_id']) && (string)$request->req_id === (string)$data['selected_req_id']) ? 'active-selected-request' : ''; ?>" href="<?php echo URLROOT; ?>/alumni/approve?req_id=<?php echo urlencode($request->req_id); ?>">
            <div class="profile-pic-new-alumni" data-req-id="<?php echo htmlspecialchars($request->req_id); ?>">
                <img src="<?php echo URLROOT; ?>/media/profile/<?php echo htmlspecialchars($request->profile); ?>" alt="profile">
            </div>
            <div class="new-commer-details">
                <h3><?php echo htmlspecialchars($request->Name); ?></h3>
                <p><?php echo htmlspecialchars($request->Batch); ?></p>
                <span class="status status-<?php echo strtolower($request->status); ?>"><?php echo htmlspecialchars($request->status); ?></span>
            </div>
        </a>
    <?php endforeach; ?>

</div>
<?php else: ?>
    <div class="empty-state">No pending requests.</div>
<?php endif; ?>
<?php $center_content = ob_get_clean(); ?>

<?php ob_start(); ?>
<!-- Right column: user-selected details -->
<div class="rightsidebar_content">
    <?php
    $selected = null;
    if (isset($data['selected_req_id']) && isset($data['requestsById'][$data['selected_req_id']])) {
        $selected = $data['requestsById'][$data['selected_req_id']];
    }
    ?>
    <?php if ($selected): ?>
        <!-- <h2 class="section-topic">Details about the Alumni</h2> -->
        <div class="name-of-the-request-holder">
            <h2 class="section-topic"><?php echo htmlspecialchars($selected->Name); ?></h2>
            <p class="description">Review the details and approve or reject this alumni request.</p>
        </div>
        <div class="profile-image">
            <img src="<?php echo URLROOT; ?>/media/profile/<?php echo htmlspecialchars($selected->profile); ?>" alt="profile">
        </div>
        <div class="detail-grid">
            <div class="detail-label">Email</div>
            <div class="detail-value"><?php echo htmlspecialchars($selected->email); ?></div>
            <div class="detail-label">Batch</div>
            <div class="detail-value"><?php echo htmlspecialchars($selected->Batch); ?></div>
            <div class="detail-label">NIC</div>
            <div class="detail-value"><?php echo htmlspecialchars($selected->nic); ?></div>
            <div class="detail-label">Student No</div>
            <div class="detail-value"><?php echo htmlspecialchars($selected->student_no); ?></div>
            <div class="detail-label">Display Name</div>
            <div class="detail-value"><?php echo htmlspecialchars($selected->display_name); ?></div>
            <div class="detail-label">Bio</div>
            <div class="detail-value"><?php echo htmlspecialchars($selected->bio); ?></div>
            <div class="detail-label">Status</div>
            <div class="detail-value"><span class="status status-<?php echo strtolower($selected->status); ?>"><?php echo htmlspecialchars($selected->status); ?></span></div>
        </div>
        <div class="action-row">
            <button class="btn-approve" data-req-id="<?php echo htmlspecialchars($selected->req_id); ?>">Approve</button>
            <button class="btn-reject" data-req-id="<?php echo htmlspecialchars($selected->req_id); ?>">Reject</button>
        </div>
    <?php else: ?>
        <div class="Empty-section">
            <h2 class="">Select a request to view details</h2>
            <!-- <p class="empty-state">Click on a request card on the left to see more information.</p> -->
        </div>
    <?php endif; ?>
</div>
<?php $content = ob_get_clean(); ?>

<?php ob_start(); ?>
// Placeholder actions; integrate with backend endpoints when ready
document.addEventListener('click', (e) => {
const approveBtn = e.target.closest('.btn-approve');
const rejectBtn = e.target.closest('.btn-reject');
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

<?php require APPROOT . '/views/alumni_approval/layout_adapter.php'; ?>