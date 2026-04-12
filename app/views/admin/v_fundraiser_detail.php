<?php ob_start()?>
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/css/admin/common.css">
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/css/admin/dashboard-common.css">
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/css/admin/fundraiser.css">
<?php $styles = ob_get_clean()?>
<?php
    $sidebar_left = [
        ['label'=>'Overview', 'url'=>'/admin','active'=>false, 'icon'=>'tachometer-alt'],
        ['label'=>'User Management', 'url'=>'/admin/users','active'=>false, 'icon' => 'users'],
        ['label'=>'Engagement Metrics', 'url'=>'/admin/engagement','active'=>false, 'icon' => 'chart-bar'],
        ['label'=>'Reports', 'url'=>'/admin/reports','active'=>false, 'icon' => 'fas fa-exclamation-triangle'],
        ['label'=>'Event Moderation', 'url'=>'/admin/eventrequests','active'=>false, 'icon' => 'clipboard-list'],
        ['label'=>'Content Management', 'url'=>'/admin/posts','active'=>false, 'icon' => 'pencil-alt'],
        ['label'=>'Fundraisers', 'url'=>'/admin/fundraisers','active'=>true, 'icon' => 'donate'],
        ['label'=>'Alumni Verifications', 'url'=>'/admin/verifications','active'=>false, 'icon' => 'check-circle'],
        ['label'=>'Help & Support', 'url'=>'/admin/support','active'=>false, 'icon' => 'life-ring']
    ];

    $fund = $data['fundraiser'] ?? null;
    if (!$fund) {
        header('Location: ' . URLROOT . '/admin/fundraisers');
        exit;
    }
    
    $percentage = $fund->target_amount > 0 ? round(($fund->raised_amount / $fund->target_amount) * 100, 1) : 0;
    $remaining = max(0, $fund->target_amount - $fund->raised_amount);
    $statusClass = 'status-' . strtolower($fund->status);
    
    // Calculate days left
    $daysLeft = null;
    $now = new DateTime();
    $deadline = new DateTime($fund->deadline ?? $fund->end_date);
    if ($deadline > $now) {
        $interval = $now->diff($deadline);
        $daysLeft = $interval->days;
    }
?>
<?php ob_start();?>

<!-- Flash Messages -->
<?php 
$flashMessages = SessionManager::getFlash();
if (!empty($flashMessages)): ?>
    <div class="flash-messages" style="margin-bottom: 1.5rem;">
        <?php foreach ($flashMessages as $message): ?>
            <div class="flash-message <?php echo htmlspecialchars($message['type']); ?>" style="padding: 12px 16px; border-radius: 4px; margin-bottom: 8px; font-weight: 500;">
                <?php echo htmlspecialchars($message['message']); ?>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<div class="admin-fundraiser">
    <div class="admin-header" style="display:flex; justify-content:space-between; align-items:flex-start; flex-wrap:wrap; gap:1rem; border-bottom: 2px solid #3a3a3a; padding-bottom: 15px;">
        <div style="flex:1; min-width:200px;">
            <a href="<?php echo URLROOT; ?>/admin/fundraisers" style="color:#4a90e2; text-decoration:none; font-size:0.9rem;">
                <i class="fas fa-arrow-left"></i> Back to Fundraisers
            </a>
            <h1 style="margin-top:0.5rem;"><?php echo htmlspecialchars($fund->title); ?></h1>
            <span class="status-badge <?php echo $statusClass; ?>" style="font-size:1rem; padding:0.5em 1em;">
                <?php echo $fund->status; ?>
            </span>
        </div>
        <div class="admin-actions" style="display:flex; gap:0.5rem; flex-wrap:wrap; align-items:center;">
            <?php if($fund->status === 'Pending'): ?>
                <button class="admin-btn" style="background:#28a745;" onclick="approveFund()">
                    <i class="fas fa-check"></i> Approve
                </button>
                <button class="admin-btn admin-btn-danger" onclick="openRejectModal()">
                    <i class="fas fa-times"></i> Reject
                </button>
            <?php elseif($fund->status === 'Approved' || $fund->status === 'Active'): ?>
                <?php if($percentage >= 100): ?>
                    <button class="admin-btn" style="background:#17a2b8;" onclick="completeFund()" title="Mark as Completed">
                        <i class="fas fa-check-circle"></i> Mark Completed
                    </button>
                <?php endif; ?>
                <button class="admin-btn" style="background:#ffc107; color:#000;" onclick="holdFund()">
                    <i class="fas fa-pause"></i> Hold
                </button>
            <?php endif; ?>
            <button class="admin-btn admin-btn-danger" onclick="removeFund()">
                <i class="fas fa-trash"></i> Remove
            </button>
        </div>
    </div>

    <!-- KPI Stats -->
    <section class="fund-kpis" style="margin-top:1.5rem;">
        <div class="fund-kpi">
            <span class="label">Raised</span>
            <span class="value">Rs.<?php echo number_format($fund->raised_amount, 2); ?></span>
        </div>
        <div class="fund-kpi">
            <span class="label">Target</span>
            <span class="value">Rs.<?php echo number_format($fund->target_amount, 2); ?></span>
        </div>
        <div class="fund-kpi">
            <span class="label">Progress</span>
            <span class="value"><?php echo $percentage; ?>%</span>
        </div>
        <div class="fund-kpi">
            <span class="label">Donors</span>
            <span class="value"><?php echo $fund->donor_count ?? 0; ?></span>
        </div>
        <div class="fund-kpi">
            <span class="label">Avg. Donation</span>
            <span class="value">Rs.<?php echo number_format($fund->avg_donation ?? 0, 2); ?></span>
        </div>
        <div class="fund-kpi">
            <span class="label">Days Left</span>
            <span class="value"><?php echo $daysLeft !== null ? $daysLeft : 'Ended'; ?></span>
        </div>
    </section>

    <!-- Progress Bar -->
    <div style="margin:1.5rem 0;">
        <div class="progress" style="height:20px; border-radius:10px;">
            <i style="width:<?php echo min($percentage, 100); ?>%"></i>
        </div>
        <div style="display:flex; justify-content:space-between; margin-top:0.5rem; font-size:0.9rem; color:#888;">
            <span>Rs.0</span>
            <span>Remaining: Rs.<?php echo number_format($remaining, 2); ?></span>
            <span>Rs.<?php echo number_format($fund->target_amount, 2); ?></span>
        </div>
    </div>

    <div class="detail-grid">
        <!-- Left Column: Details -->
        <div class="detail-section">
            <div class="admin-card">
                <div class="card-header"><h3>Campaign Details</h3></div>
                <div class="card-body">
                    <div class="info-row">
                        <strong>Headline:</strong>
                        <span><?php echo htmlspecialchars($fund->headline ?? '-'); ?></span>
                    </div>
                    <div class="info-row">
                        <strong>Description:</strong>
                        <p style="margin:0.5rem 0; line-height:1.6;"><?php echo nl2br(htmlspecialchars($fund->description ?? '')); ?></p>
                    </div>
                    <div class="info-row">
                        <strong>Objective:</strong>
                        <p style="margin:0.5rem 0; line-height:1.6;"><?php echo nl2br(htmlspecialchars($fund->objective ?? '')); ?></p>
                    </div>
                    <div class="info-row">
                        <strong>Period:</strong>
                        <span><?php echo date('M d, Y', strtotime($fund->start_date)); ?> - <?php echo date('M d, Y', strtotime($fund->deadline ?? $fund->end_date)); ?></span>
                    </div>
                    <?php if($fund->project_poster): ?>
                    <div class="info-row">
                        <strong>Poster:</strong>
                        <img src="<?php echo URLROOT; ?>/media/fundraiser/<?php echo basename($fund->project_poster); ?>" 
                            alt="Poster" style="max-width:100%; max-height:200px; border-radius:8px; margin-top:0.5rem;"
                            onerror="this.style.display='none';">
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="admin-card" style="margin-top:1rem;">
                <div class="card-header"><h3>Requester Information</h3></div>
                <div class="card-body">
                    <div class="info-row"><strong>Club/Organization:</strong> <span><?php echo htmlspecialchars($fund->club_name); ?></span></div>
                    <div class="info-row">
                        <strong>Requester:</strong> 
                        <a href="<?php echo URLROOT; ?>/profile?userid=<?php echo $fund->user_id; ?>" class="profile-link" target="_blank">
                            <i class="fas fa-user"></i> <?php echo htmlspecialchars($fund->display_name ?? $fund->user_name); ?>
                        </a>
                    </div>
                    <div class="info-row"><strong>Email:</strong> <span><?php echo htmlspecialchars($fund->user_email ?? ''); ?></span></div>
                    <div class="info-row"><strong>Position:</strong> <span><?php echo htmlspecialchars($fund->requester_position ?? '-'); ?></span></div>
                    <div class="info-row"><strong>Phone:</strong> <span><?php echo htmlspecialchars($fund->requester_phone ?? '-'); ?></span></div>
                    <div class="info-row"><strong>Fund Manager:</strong> <span><?php echo htmlspecialchars($fund->fund_manager_name ?? '-'); ?></span></div>
                    <div class="info-row"><strong>Manager Contact:</strong> <span><?php echo htmlspecialchars($fund->fund_manager_contact ?? '-'); ?></span></div>
                    <?php if($fund->advisor_id): ?>
                    <div class="info-row">
                        <strong>Advisor:</strong> 
                        <a href="<?php echo URLROOT; ?>/profile?userid=<?php echo $fund->advisor_id; ?>" class="profile-link" target="_blank">
                            <i class="fas fa-user"></i> <?php echo htmlspecialchars($fund->advisor_name); ?>
                        </a>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <?php if($fund->status === 'Rejected' && $fund->rejection_reason): ?>
            <div class="admin-card" style="margin-top:1rem; border-color:#dc3545;">
                <div class="card-header" style="background:rgba(220,53,69,0.1);"><h3 style="color:#dc3545;">Rejection Reason</h3></div>
                <div class="card-body">
                    <p style="margin:0; color:#dc3545;"><?php echo nl2br(htmlspecialchars($fund->rejection_reason)); ?></p>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <!-- Right Column: Bank & Donations -->
        <div class="detail-section">
            <div class="admin-card">
                <div class="card-header">
                    <h3>Bank Details</h3>
                    <button class="admin-btn" style="background:#6c757d; font-size:0.8rem;" onclick="toggleBankDetails()">
                        <i class="fas fa-eye" id="bank-toggle-icon"></i> <span id="bank-toggle-text">Show</span>
                    </button>
                </div>
                <div class="card-body" id="bank-details" style="display:none;">
                    <?php if($fund->bank_details): ?>
                    <div class="info-row"><strong>Bank:</strong> <span><?php echo htmlspecialchars($fund->bank_details->bank_name); ?></span></div>
                    <div class="info-row"><strong>Branch:</strong> <span><?php echo htmlspecialchars($fund->bank_details->branch); ?></span></div>
                    <div class="info-row"><strong>Account Number:</strong> <span class="sensitive"><?php echo htmlspecialchars($fund->bank_details->account_number); ?></span></div>
                    <div class="info-row"><strong>Account Holder:</strong> <span><?php echo htmlspecialchars($fund->bank_details->account_holder); ?></span></div>
                    <?php else: ?>
                    <p style="color:#888;">No bank details available.</p>
                    <?php endif; ?>
                </div>
                <div class="card-body" id="bank-placeholder">
                    <p style="color:#888; font-style:italic;">Bank details hidden for security. Click "Show" to reveal.</p>
                </div>
            </div>

            <?php if(!empty($fund->team_members)): ?>
            <div class="admin-card" style="margin-top:1rem;">
                <div class="card-header"><h3>Team Members</h3></div>
                <div class="card-body">
                    <ul style="list-style:none; padding:0; margin:0;">
                    <?php foreach($fund->team_members as $member): ?>
                        <li style="padding:0.5rem 0; border-bottom:1px solid var(--border); display:flex; align-items:center; gap:0.75rem;">
                            <img src="<?php echo URLROOT; ?>/media/profile/<?php echo htmlspecialchars($member->profile_image ?? 'default.jpg'); ?>" 
                                alt="" style="width:36px; height:36px; border-radius:50%; object-fit:cover;"
                                onerror="this.src='<?php echo URLROOT; ?>/media/profile/default.jpg';">
                            <div>
                                <a href="<?php echo URLROOT; ?>/profile?userid=<?php echo $member->user_id; ?>" class="profile-link" target="_blank">
                                    <strong><?php echo htmlspecialchars($member->display_name ?? $member->name); ?></strong>
                                </a>
                                <br><small style="color:#888;"><?php echo htmlspecialchars($member->email); ?></small>
                            </div>
                        </li>
                    <?php endforeach; ?>
                    </ul>
                </div>
            </div>
            <?php endif; ?>

            <div class="admin-card" style="margin-top:1rem;">
                <div class="card-header"><h3>Donation History (<?php echo count($fund->donations ?? []); ?>)</h3></div>
                <div class="card-body" style="max-height:400px; overflow-y:auto;">
                    <?php if(empty($fund->donations)): ?>
                        <p style="color:#888; text-align:center;">No donations yet.</p>
                    <?php else: ?>
                        <table class="admin-table" style="margin:0;">
                            <thead>
                                <tr>
                                    <th>Donor</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php foreach($fund->donations as $donation): ?>
                                <tr>
                                    <td>
                                        <?php if($donation->is_anonymous): ?>
                                            <em style="color:#888;">Anonymous</em>
                                        <?php else: ?>
                                            <?php echo htmlspecialchars($donation->donor_display_name ?? $donation->donor_user_name ?? $donation->donor_name ?? 'Unknown'); ?>
                                        <?php endif; ?>
                                    </td>
                                    <td>Rs.<?php echo number_format($donation->amount, 2); ?></td>
                                    <td>
                                        <span class="status-badge status-<?php echo strtolower($donation->status); ?>">
                                            <?php echo $donation->status; ?>
                                        </span>
                                    </td>
                                    <td><?php echo date('M d, Y', strtotime($donation->created_at)); ?></td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Reject Modal -->
    <div class="admin-modal" id="reject-modal">
        <div class="admin-modal-content" style="background:var(--card-bg, #1a2634);">
            <span class="admin-modal-close" onclick="closeRejectModal()">&times;</span>
            <h2>Reject Fundraiser</h2>
            <form method="POST" action="<?php echo URLROOT; ?>/admin/rejectFundraiser">
                <input type="hidden" name="id" value="<?php echo $fund->id; ?>">
                <input type="hidden" name="return_to" value="detail">
                <div style="margin:1rem 0;">
                    <label style="display:block; margin-bottom:0.5rem;">Rejection Reason:</label>
                    <textarea name="reason" required rows="4" 
                        style="width:100%; padding:0.75rem; border-radius:6px; border:1px solid var(--border); background:var(--surface); color:inherit;"
                        placeholder="Please explain why this fundraiser is being rejected..."></textarea>
                </div>
                <button type="submit" class="admin-btn admin-btn-danger">Reject Fundraiser</button>
            </form>
        </div>
    </div>
</div>

<style>
.detail-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-top: 1.5rem; }
@media (max-width: 900px) { .detail-grid { grid-template-columns: 1fr; } }
.detail-section .admin-card { background: var(--card, #1a2634); border: 1px solid var(--border); border-radius: 8px; }
.card-body { padding: 1rem; }
.info-row { padding: 0.75rem 0; border-bottom: 1px solid var(--border); display: flex; flex-wrap: wrap; gap: 0.5rem; align-items: center; }
.info-row:last-child { border-bottom: none; }
.info-row strong { min-width: 140px; color: #888; }
.sensitive { font-family: monospace; background: rgba(255,255,255,0.1); padding: 0.2rem 0.5rem; border-radius: 4px; }
.status-successful { background: #d4edda; color: #155724; }
.status-pending { background: #ffeeba; color: #856404; }
.status-failed { background: #f8d7da; color: #721c24; }
.status-completed { background: #d4edda; color: #155724; }
.status-cancelled { background: #f8d7da; color: #721c24; }
.status-active { background: #cce5ff; color: #004085; }

/* Profile Links */
.profile-link {
    color: #4a90e2;
    text-decoration: none;
    transition: color 0.2s, background 0.2s;
    padding: 0.25rem 0.5rem;
    border-radius: 4px;
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
}
.profile-link:hover {
    color: #fff;
    background: rgba(74, 144, 226, 0.2);
    text-decoration: underline;
}
.profile-link i {
    font-size: 0.85em;
}
</style>

<script>
const URLROOT = '<?php echo URLROOT; ?>';
const fundId = <?php echo $fund->id; ?>;

function approveFund() {
    if (!confirm('Are you sure you want to approve this fundraiser?')) return;
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = URLROOT + '/admin/approveFundraiser';
    form.innerHTML = `<input type="hidden" name="id" value="${fundId}"><input type="hidden" name="return_to" value="detail">`;
    document.body.appendChild(form);
    form.submit();
}

function holdFund() {
    if (!confirm('Are you sure you want to put this fundraiser on hold?')) return;
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = URLROOT + '/admin/holdFundraiser';
    form.innerHTML = `<input type="hidden" name="id" value="${fundId}"><input type="hidden" name="return_to" value="detail">`;
    document.body.appendChild(form);
    form.submit();
}

function completeFund() {
    if (!confirm('Are you sure you want to mark this fundraiser as completed?')) return;
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = URLROOT + '/admin/completeFundraiser';
    form.innerHTML = `<input type="hidden" name="id" value="${fundId}"><input type="hidden" name="return_to" value="detail">`;
    document.body.appendChild(form);
    form.submit();
}

function removeFund() {
    if (!confirm('WARNING: This will permanently delete this fundraiser and all associated data. Are you sure?')) return;
    if (!confirm('This action cannot be undone. Type "DELETE" to confirm.')) return;
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = URLROOT + '/admin/removeFundraiser';
    form.innerHTML = `<input type="hidden" name="id" value="${fundId}">`;
    document.body.appendChild(form);
    form.submit();
}

function openRejectModal() {
    document.getElementById('reject-modal').style.display = 'flex';
}
function closeRejectModal() {
    document.getElementById('reject-modal').style.display = 'none';
}

function toggleBankDetails() {
    const details = document.getElementById('bank-details');
    const placeholder = document.getElementById('bank-placeholder');
    const icon = document.getElementById('bank-toggle-icon');
    const text = document.getElementById('bank-toggle-text');
    
    if (details.style.display === 'none') {
        details.style.display = 'block';
        placeholder.style.display = 'none';
        icon.className = 'fas fa-eye-slash';
        text.textContent = 'Hide';
    } else {
        details.style.display = 'none';
        placeholder.style.display = 'block';
        icon.className = 'fas fa-eye';
        text.textContent = 'Show';
    }
}

window.onclick = function(e) {
    if (e.target.classList.contains('admin-modal')) {
        e.target.style.display = 'none';
    }
};
</script>

<?php $content = ob_get_clean(); ?>
<?php require APPROOT . '/views/admin/dashboard_layout.php'; ?>
