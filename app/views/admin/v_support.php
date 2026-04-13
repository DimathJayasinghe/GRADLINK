<?php ob_start()?>
<?php
    $stats = $data['stats'] ?? [];
    $tickets = $data['tickets'] ?? [];
    $reports = $data['reports'] ?? [];
    $feedback = $data['feedback'] ?? [];
?>
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/css/admin/common.css">
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/css/admin/dashboard-common.css">
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/css/admin/support.css?v=2">
<?php $styles = ob_get_clean()?>

<?php
    $sidebar_left = [
        ['label'=>'Overview', 'url'=>'/admin','active'=>false, 'icon'=>'tachometer-alt'],
        ['label'=>'User Management', 'url'=>'/admin/users','active'=>false, 'icon' => 'users'],
        ['label'=>'Engagement Metrics', 'url'=>'/admin/engagement','active'=>false, 'icon' => 'chart-bar'],
        ['label'=>'Reports', 'url'=>'/admin/reports','active'=>false, 'icon' => 'exclamation-triangle'],
        ['label'=>'Event Moderation', 'url'=>'/admin/eventrequests','active'=>false, 'icon' => 'clipboard-list'],
        ['label'=>'Content Management', 'url'=>'/admin/posts','active'=>false, 'icon' => 'pencil-alt'],
        ['label'=>'Fundraisers', 'url'=>'/admin/fundraisers','active'=>false, 'icon' => 'donate'],
        ['label'=>'Alumni Verifications', 'url'=>'/admin/verifications','active'=>false, 'icon' => 'check-circle'],
        ['label'=>'Suspended Users', 'url'=>'/admin/suspendedUsers','active'=>false, 'icon' => 'user-slash'],
        ['label'=>'Help & Support', 'url'=>'/admin/support','active'=>true, 'icon' => 'circle-question']
    ]
?>

<?php ob_start();?>

<!-- ========== KPI Cards ========== -->
<div class="admin-header" style="border-bottom: 2px solid #3a3a3a; padding-bottom: 15px;margin-bottom: 2rem;">
    <h1>Help & Support</h1>
</div>

<div class="support-stats">
    <div class="support-stat-card">
        <div class="support-stat-icon tickets"><i class="fas fa-ticket-alt"></i></div>
        <div class="support-stat-info">
            <h4>Open Tickets</h4>
            <span class="stat-number"><?php echo $stats['open_tickets'] ?? 0; ?></span>
        </div>
    </div>
    <div class="support-stat-card">
        <div class="support-stat-icon reports"><i class="fas fa-bug"></i></div>
        <div class="support-stat-info">
            <h4>Pending Reports</h4>
            <span class="stat-number"><?php echo $stats['pending_reports'] ?? 0; ?></span>
        </div>
    </div>
    <div class="support-stat-card">
        <div class="support-stat-icon feedback"><i class="fas fa-comment-dots"></i></div>
        <div class="support-stat-info">
            <h4>Total Feedback</h4>
            <span class="stat-number"><?php echo $stats['total_feedback'] ?? 0; ?></span>
        </div>
    </div>
    <div class="support-stat-card">
        <div class="support-stat-icon resolved"><i class="fas fa-check-double"></i></div>
        <div class="support-stat-info">
            <h4>Resolved</h4>
            <span class="stat-number"><?php echo $stats['resolved_total'] ?? 0; ?></span>
        </div>
    </div>
</div>

<!-- ========== Tab Navigation ========== -->
<div class="support-tabs">
    <button class="support-tab active" data-tab="tickets">
        <i class="fas fa-ticket-alt"></i> Support Tickets
        <span class="tab-count"><?php echo count($tickets); ?></span>
    </button>
    <button class="support-tab" data-tab="reports">
        <i class="fas fa-exclamation-circle"></i> Problem Reports
        <span class="tab-count"><?php echo count($reports); ?></span>
    </button>
    <button class="support-tab" data-tab="feedback">
        <i class="fas fa-comment-dots"></i> Feedback
        <span class="tab-count"><?php echo count($feedback); ?></span>
    </button>
</div>

<!-- ========== TICKETS TAB ========== -->
<div class="support-tab-panel active" id="panel-tickets">
    <div class="admin-card">
        <?php if (!empty($tickets)): ?>
        <div class="support-table-wrapper">
            <table class="support-table">
                <thead>
                    <tr>
                        <th class="col-id">#</th>
                        <th class="col-user">User</th>
                        <th class="col-topic">Topic</th>
                        <th class="col-message">Message</th>
                        <th class="col-status">Status</th>
                        <th class="col-date">Date</th>
                        <th class="col-actions">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($tickets as $ticket): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($ticket->id); ?></td>
                        <td>
                            <strong><?php echo htmlspecialchars($ticket->user_name); ?></strong>
                            <br><small style="color:var(--muted)"><?php echo htmlspecialchars($ticket->email); ?></small>
                        </td>
                        <td><span class="topic-badge"><?php echo htmlspecialchars($ticket->topic); ?></span></td>
                        <td><span class="message-preview"><?php echo htmlspecialchars($ticket->message); ?></span></td>
                        <td><span class="support-status status-<?php echo htmlspecialchars($ticket->status); ?>"><?php echo str_replace('_', ' ', htmlspecialchars($ticket->status)); ?></span></td>
                        <td><?php echo date('M d, Y', strtotime($ticket->created_at)); ?></td>
                        <td>
                            <div class="support-actions">
                                <button class="support-btn support-btn-view" onclick="viewTicket(<?php echo $ticket->id; ?>)">View</button>
                                <button class="support-btn support-btn-reply" onclick="openReplyModal('ticket', <?php echo $ticket->id; ?>)">Reply</button>
                                <form method="POST" action="<?php echo URLROOT; ?>/admin/updateTicketStatus" style="display:inline;">
                                    <input type="hidden" name="id" value="<?php echo $ticket->id; ?>">
                                    <select name="status" class="support-select" onchange="this.form.submit()">
                                        <option value="">Status</option>
                                        <option value="open" <?php echo $ticket->status === 'open' ? 'disabled' : ''; ?>>Open</option>
                                        <option value="in_progress" <?php echo $ticket->status === 'in_progress' ? 'disabled' : ''; ?>>In Progress</option>
                                        <option value="resolved" <?php echo $ticket->status === 'resolved' ? 'disabled' : ''; ?>>Resolved</option>
                                        <option value="closed" <?php echo $ticket->status === 'closed' ? 'disabled' : ''; ?>>Closed</option>
                                    </select>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
        <div class="support-empty">
            <i class="fas fa-ticket-alt"></i>
            <p>No support tickets yet.</p>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- ========== REPORTS TAB ========== -->
<div class="support-tab-panel" id="panel-reports">
    <div class="admin-card">
        <?php if (!empty($reports)): ?>
        <div class="support-table-wrapper">
            <table class="support-table">
                <thead>
                    <tr>
                        <th class="col-id">#</th>
                        <th class="col-user">Reporter</th>
                        <th class="col-topic">Type</th>
                        <th class="col-message">Details</th>
                        <th class="col-status">Status</th>
                        <th class="col-date">Date</th>
                        <th class="col-actions">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($reports as $report): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($report->id); ?></td>
                        <td>
                            <strong><?php echo htmlspecialchars($report->user_name); ?></strong>
                            <br><small style="color:var(--muted)"><?php echo htmlspecialchars($report->user_role); ?></small>
                        </td>
                        <td><span class="topic-badge"><?php echo htmlspecialchars($report->report_type); ?></span></td>
                        <td><span class="message-preview"><?php echo htmlspecialchars($report->details); ?></span></td>
                        <td><span class="support-status status-<?php echo htmlspecialchars($report->status); ?>"><?php echo ucfirst(htmlspecialchars($report->status)); ?></span></td>
                        <td><?php echo date('M d, Y', strtotime($report->created_at)); ?></td>
                        <td>
                            <div class="support-actions">
                                <button class="support-btn support-btn-view" onclick="viewReport(<?php echo $report->id; ?>)">View</button>
                                <button class="support-btn support-btn-reply" onclick="openReplyModal('report', <?php echo $report->id; ?>)">Reply</button>
                                <form method="POST" action="<?php echo URLROOT; ?>/admin/updateReportStatus" style="display:inline;">
                                    <input type="hidden" name="id" value="<?php echo $report->id; ?>">
                                    <select name="status" class="support-select" onchange="this.form.submit()">
                                        <option value="">Status</option>
                                        <option value="pending" <?php echo $report->status === 'pending' ? 'disabled' : ''; ?>>Pending</option>
                                        <option value="triaged" <?php echo $report->status === 'triaged' ? 'disabled' : ''; ?>>Triaged</option>
                                        <option value="resolved" <?php echo $report->status === 'resolved' ? 'disabled' : ''; ?>>Resolved</option>
                                        <option value="rejected" <?php echo $report->status === 'rejected' ? 'disabled' : ''; ?>>Rejected</option>
                                    </select>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
        <div class="support-empty">
            <i class="fas fa-bug"></i>
            <p>No problem reports yet.</p>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- ========== FEEDBACK TAB ========== -->
<div class="support-tab-panel" id="panel-feedback">
    <div class="admin-card">
        <?php if (!empty($feedback)): ?>
        <div class="support-table-wrapper">
            <table class="support-table">
                <thead>
                    <tr>
                        <th class="col-id">#</th>
                        <th class="col-user">User</th>
                        <th class="col-topic">Type</th>
                        <th class="col-message">Message</th>
                        <th class="col-date">Date</th>
                        <th class="col-actions">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($feedback as $fb): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($fb->id); ?></td>
                        <td>
                            <strong><?php echo htmlspecialchars($fb->user_name); ?></strong>
                            <br><small style="color:var(--muted)"><?php echo htmlspecialchars($fb->user_role); ?></small>
                        </td>
                        <td><span class="topic-badge"><?php echo htmlspecialchars($fb->feedback_type); ?></span></td>
                        <td><span class="message-preview"><?php echo htmlspecialchars($fb->message); ?></span></td>
                        <td><?php echo date('M d, Y', strtotime($fb->created_at)); ?></td>
                        <td>
                            <div class="support-actions">
                                <button class="support-btn support-btn-view" onclick="viewFeedback(<?php echo $fb->id; ?>)">View</button>
                                <form method="POST" action="<?php echo URLROOT; ?>/admin/deleteFeedbackEntry" style="display:inline;" onsubmit="return confirm('Delete this feedback?')">
                                    <input type="hidden" name="id" value="<?php echo $fb->id; ?>">
                                    <button type="submit" class="support-btn support-btn-delete">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
        <div class="support-empty">
            <i class="fas fa-comment-dots"></i>
            <p>No feedback yet.</p>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- ========== View Ticket Modal ========== -->
<div id="viewTicketModal" class="support-modal">
    <div class="support-modal-content">
        <div class="support-modal-header">
            <h3>Ticket Details</h3>
            <button class="support-modal-close" onclick="closeModal('viewTicketModal')">&times;</button>
        </div>
        <div class="support-modal-body" id="viewTicketBody">
            <!-- Filled by JS -->
        </div>
        <div class="support-modal-footer">
            <button class="modal-btn modal-btn-cancel" onclick="closeModal('viewTicketModal')">Close</button>
        </div>
    </div>
</div>

<!-- ========== View Report Modal ========== -->
<div id="viewReportModal" class="support-modal">
    <div class="support-modal-content">
        <div class="support-modal-header">
            <h3>Report Details</h3>
            <button class="support-modal-close" onclick="closeModal('viewReportModal')">&times;</button>
        </div>
        <div class="support-modal-body" id="viewReportBody">
            <!-- Filled by JS -->
        </div>
        <div class="support-modal-footer">
            <button class="modal-btn modal-btn-cancel" onclick="closeModal('viewReportModal')">Close</button>
        </div>
    </div>
</div>

<!-- ========== View Feedback Modal ========== -->
<div id="viewFeedbackModal" class="support-modal">
    <div class="support-modal-content">
        <div class="support-modal-header">
            <h3>Feedback Details</h3>
            <button class="support-modal-close" onclick="closeModal('viewFeedbackModal')">&times;</button>
        </div>
        <div class="support-modal-body" id="viewFeedbackBody">
            <!-- Filled by JS -->
        </div>
        <div class="support-modal-footer">
            <button class="modal-btn modal-btn-cancel" onclick="closeModal('viewFeedbackModal')">Close</button>
        </div>
    </div>
</div>

<!-- ========== Reply Modal (shared for tickets & reports) ========== -->
<div id="replyModal" class="support-modal">
    <div class="support-modal-content">
        <div class="support-modal-header">
            <h3 id="replyModalTitle">Reply</h3>
            <button class="support-modal-close" onclick="closeModal('replyModal')">&times;</button>
        </div>
        <form id="replyForm" method="POST" action="">
            <div class="support-modal-body">
                <input type="hidden" name="id" id="replyItemId">
                <div style="margin-bottom: 1rem;">
                    <label style="display:block; margin-bottom: 0.5rem; color: var(--muted); font-size: 0.9rem;">Your reply</label>
                    <textarea name="reply" id="replyText" placeholder="Type your reply to the user..." required></textarea>
                </div>
            </div>
            <div class="support-modal-footer">
                <button type="button" class="modal-btn modal-btn-cancel" onclick="closeModal('replyModal')">Cancel</button>
                <button type="submit" class="modal-btn modal-btn-success">Send Reply</button>
            </div>
        </form>
    </div>
</div>

<!-- ========== Toast ========== -->
<div id="supportToast" class="support-toast"></div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const URLROOT = '<?= URLROOT ?>';

    // ---- Tab switching ----
    document.querySelectorAll('.support-tab').forEach(tab => {
        tab.addEventListener('click', function() {
            document.querySelectorAll('.support-tab').forEach(t => t.classList.remove('active'));
            document.querySelectorAll('.support-tab-panel').forEach(p => p.classList.remove('active'));
            this.classList.add('active');
            document.getElementById('panel-' + this.dataset.tab).classList.add('active');
        });
    });

    // ---- Close modal on backdrop click ----
    document.querySelectorAll('.support-modal').forEach(modal => {
        modal.addEventListener('click', function(e) {
            if (e.target === this) this.style.display = 'none';
        });
    });

    // ---- Show flash messages as toast ----
    <?php $flashMessages = SessionManager::getFlash(); ?>
    <?php foreach ($flashMessages as $flash): ?>
        showToast('<?php echo addslashes($flash['message']); ?>', '<?php echo addslashes($flash['type']); ?>');
    <?php endforeach; ?>
});

// ---- Data objects for JS modals ----
const ticketsData = <?php echo json_encode($tickets); ?>;
const reportsData = <?php echo json_encode($reports); ?>;
const feedbackData = <?php echo json_encode($feedback); ?>;

function findById(arr, id) {
    return arr.find(item => parseInt(item.id) === parseInt(id));
}

// ---- View Ticket ----
function viewTicket(id) {
    const t = findById(ticketsData, id);
    if (!t) return;
    let html = `
        <div class="support-detail-row"><span class="support-detail-label">Ticket #</span><span class="support-detail-value">${t.id}</span></div>
        <div class="support-detail-row"><span class="support-detail-label">User</span><span class="support-detail-value">${esc(t.user_name)} (${esc(t.user_role)})</span></div>
        <div class="support-detail-row"><span class="support-detail-label">Email</span><span class="support-detail-value">${esc(t.email)}</span></div>
        <div class="support-detail-row"><span class="support-detail-label">Topic</span><span class="support-detail-value"><span class="topic-badge">${esc(t.topic)}</span></span></div>
        <div class="support-detail-row"><span class="support-detail-label">Status</span><span class="support-detail-value"><span class="support-status status-${esc(t.status)}">${esc(t.status).replace('_',' ')}</span></span></div>
        <div class="support-detail-row"><span class="support-detail-label">Submitted</span><span class="support-detail-value">${formatDate(t.created_at)}</span></div>
        <div class="support-detail-row"><span class="support-detail-label">Message</span><span class="support-detail-value">${esc(t.message)}</span></div>
    `;
    if (t.admin_reply) {
        html += `
            <div class="support-reply-box">
                <h4><i class="fas fa-reply"></i> Admin Reply</h4>
                <p>${esc(t.admin_reply)}</p>
                <small style="color:var(--muted)">Replied: ${formatDate(t.admin_replied_at)}</small>
            </div>
        `;
    }
    document.getElementById('viewTicketBody').innerHTML = html;
    openModal('viewTicketModal');
}

// ---- View Report ----
function viewReport(id) {
    const r = findById(reportsData, id);
    if (!r) return;
    let html = `
        <div class="support-detail-row"><span class="support-detail-label">Report #</span><span class="support-detail-value">${r.id}</span></div>
        <div class="support-detail-row"><span class="support-detail-label">Reporter</span><span class="support-detail-value">${esc(r.user_name)} (${esc(r.user_role)})</span></div>
        <div class="support-detail-row"><span class="support-detail-label">Email</span><span class="support-detail-value">${esc(r.user_email)}</span></div>
        <div class="support-detail-row"><span class="support-detail-label">Type</span><span class="support-detail-value"><span class="topic-badge">${esc(r.report_type)}</span></span></div>
        <div class="support-detail-row"><span class="support-detail-label">Status</span><span class="support-detail-value"><span class="support-status status-${esc(r.status)}">${esc(r.status)}</span></span></div>
        <div class="support-detail-row"><span class="support-detail-label">Submitted</span><span class="support-detail-value">${formatDate(r.created_at)}</span></div>
        <div class="support-detail-row"><span class="support-detail-label">Details</span><span class="support-detail-value">${esc(r.details)}</span></div>
    `;
    if (r.admin_reply) {
        html += `
            <div class="support-reply-box">
                <h4><i class="fas fa-reply"></i> Admin Reply</h4>
                <p>${esc(r.admin_reply)}</p>
                <small style="color:var(--muted)">Replied: ${formatDate(r.admin_replied_at)}</small>
            </div>
        `;
    }
    document.getElementById('viewReportBody').innerHTML = html;
    openModal('viewReportModal');
}

// ---- View Feedback ----
function viewFeedback(id) {
    const f = findById(feedbackData, id);
    if (!f) return;
    let html = `
        <div class="support-detail-row"><span class="support-detail-label">Feedback #</span><span class="support-detail-value">${f.id}</span></div>
        <div class="support-detail-row"><span class="support-detail-label">User</span><span class="support-detail-value">${esc(f.user_name)} (${esc(f.user_role)})</span></div>
        <div class="support-detail-row"><span class="support-detail-label">Email</span><span class="support-detail-value">${esc(f.user_email)}</span></div>
        <div class="support-detail-row"><span class="support-detail-label">Type</span><span class="support-detail-value"><span class="topic-badge">${esc(f.feedback_type)}</span></span></div>
        <div class="support-detail-row"><span class="support-detail-label">Submitted</span><span class="support-detail-value">${formatDate(f.created_at)}</span></div>
        <div class="support-detail-row"><span class="support-detail-label">Message</span><span class="support-detail-value">${esc(f.message)}</span></div>
    `;
    document.getElementById('viewFeedbackBody').innerHTML = html;
    openModal('viewFeedbackModal');
}

// ---- Reply Modal ----
function openReplyModal(type, id) {
    const URLROOT = '<?= URLROOT ?>';
    const form = document.getElementById('replyForm');
    const title = document.getElementById('replyModalTitle');
    const idInput = document.getElementById('replyItemId');
    const textarea = document.getElementById('replyText');

    textarea.value = '';
    idInput.value = id;

    if (type === 'ticket') {
        title.textContent = 'Reply to Ticket #' + id;
        form.action = URLROOT + '/admin/replyTicket';
    } else {
        title.textContent = 'Reply to Report #' + id;
        form.action = URLROOT + '/admin/replyReport';
    }

    openModal('replyModal');
}

// ---- Modal helpers ----
function openModal(id) {
    document.getElementById(id).style.display = 'block';
}
function closeModal(id) {
    document.getElementById(id).style.display = 'none';
}

// ---- Utility ----
function esc(str) {
    if (!str) return '';
    const div = document.createElement('div');
    div.textContent = str;
    return div.innerHTML;
}

function formatDate(dateStr) {
    if (!dateStr) return '—';
    const d = new Date(dateStr);
    return d.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric', hour: '2-digit', minute: '2-digit' });
}

function showToast(msg, type) {
    const toast = document.getElementById('supportToast');
    toast.textContent = msg;
    toast.className = 'support-toast ' + type + ' show';
    setTimeout(() => { toast.classList.remove('show'); }, 4000);
}
</script>

<?php $content = ob_get_clean(); ?>
<?php require APPROOT . '/views/admin/dashboard_layout.php'; ?>
