<?php ob_start()?>
<?php
$postReports = $data['postReports'] ?? ($data['reports'] ?? []);
$profileReports = $data['profileReports'] ?? [];
?>
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/admin/common.css">
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/admin/dashboard-common.css">
<style>
.admin-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
    border-bottom: 2px solid #3a3a3a;
    padding-bottom: 0.95rem;
}

.admin-header h1 {
    margin: 0;
    color: var(--text, #ffffff);
    font-size: clamp(1.35rem, 1.2rem + 0.7vw, 1.85rem);
    letter-spacing: 0.2px;
}

.admin-card {
    margin-bottom: 1.25rem;
}

.admin-card:last-of-type {
    margin-bottom: 0;
}

.report-card-header {
    margin-bottom: 0;
    padding: 1rem 1.25rem;
}

.report-card-header h3 {
    margin: 0;
    font-size: 1.02rem;
}

.table-subtitle {
    color: #a8b2c4;
    font-size: 0.84rem;
    margin-top: 0.3rem;
    line-height: 1.35;
}

.reports-table-wrapper {
    overflow-x: auto;
    border-top: 1px solid var(--border, #3a3a3a);
}

.admin-table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
    margin-top: 0;
    min-width: 980px;
    table-layout: fixed;
    background: var(--card, #1f1f1f);
}

.admin-table th,
.admin-table td {
    padding: 0.78rem 0.75rem;
    border-bottom: 1px solid var(--border, #3a3a3a);
    text-align: left;
    vertical-align: top;
    color: var(--text, #ffffff);
}

.admin-table th {
    position: sticky;
    top: 0;
    z-index: 1;
    background: var(--surface-2, #232323);
    color: #d7deea;
    font-weight: 600;
    font-size: 0.84rem;
}

.admin-table tbody tr:hover {
    background: rgba(74, 144, 226, 0.09);
}

.admin-table tbody tr:last-child td {
    border-bottom: none;
}

.status-badge {
    display: inline-flex;
    align-items: center;
    padding: 0.28rem 0.7rem;
    border-radius: 999px;
    font-size: 0.78rem;
    font-weight: 600;
    line-height: 1.2;
}

.status-pending {
    background: rgba(245, 158, 11, 0.2);
    color: #fbbf24;
    border: 1px solid rgba(245, 158, 11, 0.45);
}

.status-resolved {
    background: rgba(16, 185, 129, 0.2);
    color: #34d399;
    border: 1px solid rgba(16, 185, 129, 0.45);
}

.status-rejected {
    background: rgba(239, 68, 68, 0.2);
    color: #f87171;
    border: 1px solid rgba(239, 68, 68, 0.45);
}

.report-actions-wrap {
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
    align-items: center;
    min-width: 240px;
}

.admin-status-form {
    display: flex;
    gap: 0.45rem;
    align-items: center;
    border: 1px solid var(--border, #3a3a3a);
    border-radius: 0.5rem;
    padding: 0.28rem;
    background: rgba(255, 255, 255, 0.03);
}

.admin-btn {
    height: 34px;
    padding: 0 0.85rem;
    border: none;
    border-radius: 0.45rem;
    background: #2f7edc;
    color: #ffffff;
    cursor: pointer;
    font-size: 0.84rem;
    font-weight: 600;
    line-height: 1;
    white-space: nowrap;
    transition: transform 0.15s ease, filter 0.15s ease, opacity 0.2s ease;
}

.admin-btn:hover {
    filter: brightness(1.08);
}

.admin-btn:active {
    transform: translateY(1px);
}

.admin-btn-danger {
    background: #c0394c;
}

.admin-btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

.admin-select {
    height: 34px;
    min-width: 110px;
    background-color: #262a32;
    color: #f0f4ff;
    border: 1px solid #3f4654;
    padding: 0 0.55rem;
    border-radius: 0.4rem;
    font-size: 0.82rem;
    cursor: pointer;
}

.admin-select:focus {
    outline: none;
    border-color: #4a90e2;
    box-shadow: 0 0 0 2px rgba(74, 144, 226, 0.2);
}

.report-snippet {
    max-width: 380px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    color: #e7ecf8;
}

.admin-modal {
    display: none;
    position: fixed;
    z-index: 1200;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    overflow: auto;
    background: rgba(0, 0, 0, 0.58);
}

.admin-modal-content {
    background: var(--surface-4, #1d2330);
    color: var(--text, #ffffff);
    margin: 5% auto;
    padding: 1.6rem;
    border-radius: 0.75rem;
    width: 92%;
    max-width: 620px;
    position: relative;
    border: 1px solid var(--border, #3a3a3a);
    box-shadow: 0 20px 45px rgba(0, 0, 0, 0.45);
}

.admin-modal-close {
    position: absolute;
    top: 0.85rem;
    right: 0.95rem;
    font-size: 1.5rem;
    cursor: pointer;
    color: #9aa5bc;
}

.admin-modal-close:hover {
    color: #ffffff;
}

#modalReportContent {
    color: #dce3f2;
    line-height: 1.45;
    word-break: break-word;
}

#modalReportContent a {
    color: #78b6ff;
}

.report-id { width: 8%; }
.report-reporter { width: 16%; }
.report-content { width: 24%; }
.report-type { width: 12%; }
.report-status { width: 12%; }
.report-date { width: 13%; }
.report-actions { width: 26%; }

@media (max-width: 1200px) {
    .admin-table {
        min-width: 860px;
    }

    .report-actions-wrap {
        min-width: 220px;
    }
}

@media (max-width: 768px) {
    .admin-header {
        padding-bottom: 0.75rem;
        margin-bottom: 0.75rem;
    }

    .card-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 0.45rem;
    }

    .admin-table {
        min-width: 720px;
    }

    .report-actions-wrap {
        flex-direction: column;
        align-items: stretch;
        min-width: 180px;
    }

    .admin-status-form {
        width: 100%;
        justify-content: space-between;
    }

    .admin-status-form .admin-select {
        flex: 1;
        min-width: 0;
    }

    .admin-status-form .admin-btn {
        min-width: 72px;
    }

    .admin-btn {
        width: 100%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }
}
</style>
<?php $styles = ob_get_clean()?>



<?php
    $sidebar_left = [
        ['label'=>'Overview', 'url'=>'/admin','active'=>false, 'icon'=>'tachometer-alt'],
        ['label'=>'User Management', 'url'=>'/admin/users','active'=>false, 'icon' => 'users'],
        ['label'=>'Engagement Metrics', 'url'=>'/admin/engagement','active'=>false, 'icon' => 'chart-bar'],
        ['label'=>'Reports', 'url'=>'/admin/reports','active'=>true, 'icon' => 'fas fa-exclamation-triangle'],
        ['label'=>'Event Moderation', 'url'=>'/admin/eventrequests','active'=>false, 'icon' => 'clipboard-list'],
        ['label'=>'Content Management', 'url'=>'/admin/posts','active'=>false, 'icon' => 'pencil-alt'],
        ['label'=>'Fundraisers', 'url'=>'/admin/fundraisers','active'=>false, 'icon' => 'donate'],
        ['label'=>'Alumni Verifications', 'url'=>'/admin/verifications','active'=>false, 'icon' => 'check-circle'],
        ['label'=>'Suspended Users', 'url'=>'/admin/suspendedUsers','active'=>false, 'icon' => 'user-slash'],
        ['label'=>'Help & Support', 'url'=>'/admin/support','active'=>false, 'icon' => 'circle-question']
    ]
?>

<?php ob_start();?>
<div class="admin-header">
    <h1>Reports Moderation</h1>

</div>

<div class="admin-card">
    <div class="card-header report-card-header">
        <div>
            <h3 style="margin:0;">Post Reports</h3>
            <div class="table-subtitle">Reports submitted against posts</div>
        </div>
        <span class="status-badge status-pending"><?php echo count($postReports); ?> total</span>
    </div>

    <div class="reports-table-wrapper">
        <table class="admin-table" id="contentReportsTable">
            <thead>
                <tr>
                    <th class="report-id">Report ID</th>
                    <th class="report-reporter">Reporter</th>
                    <th class="report-content">Content</th>
                    <th class="report-type">Type</th>
                    <th class="report-status">Status</th>
                    <th class="report-date">Date</th>
                    <th class="report-actions">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($postReports)): ?>
                    <?php foreach ($postReports as $report): ?>
                        <?php
                        $reportId = (int)($report->id ?? 0);
                        $reporterName = trim((string)($report->reporter_name ?? 'Unknown'));
                        $reporterRole = trim((string)($report->reporter_role ?? ''));
                        $postContent = trim((string)($report->post_content ?? ''));
                        if ($postContent === '') {
                            $postContent = 'Post #' . (int)($report->post_id ?? 0);
                        }
                        $postSnippet = strlen($postContent) > 120 ? substr($postContent, 0, 120) . '...' : $postContent;
                        $status = trim((string)($report->status ?? 'pending'));
                        $statusClass = in_array($status, ['pending', 'resolved', 'rejected'], true) ? $status : 'pending';
                        $category = trim((string)($report->category ?? ''));
                        $modalDetails = trim((string)($report->details ?? ''));
                        $modalLink = trim((string)($report->reference_link ?? ''));
                        $source = trim((string)($report->source ?? 'reports'));
                        $suspendTargetId = (int)($report->post_owner_id ?? 0);
                        $suspendTargetName = trim((string)($report->owner_name ?? 'User #' . $suspendTargetId));
                        $ownerRoleRaw = trim((string)($report->owner_role ?? ''));
                        $ownerRole = strtolower($ownerRoleRaw);
                        $ownerProtected = $ownerRole !== '' && strpos($ownerRole, 'admin') !== false;
                        ?>
                        <tr>
                            <td><?php echo $reportId; ?></td>
                            <td data-modal-value="<?php echo htmlspecialchars($reporterName . ($reporterRole !== '' ? ' (' . $reporterRole . ')' : '')); ?>">
                                <?php echo htmlspecialchars($reporterName); ?>
                                <?php if ($reporterRole !== ''): ?>
                                    <div class="table-subtitle"><?php echo htmlspecialchars($reporterRole); ?></div>
                                <?php endif; ?>
                            </td>
                            <td class="report-snippet" data-modal-value="<?php echo htmlspecialchars($postContent); ?>"><?php echo htmlspecialchars($postSnippet); ?></td>
                            <td><?php echo htmlspecialchars($category !== '' ? $category : 'Other'); ?></td>
                            <td><span class="status-badge status-<?php echo htmlspecialchars($statusClass); ?>"><?php echo ucfirst(htmlspecialchars($statusClass)); ?></span></td>
                            <td><?php echo htmlspecialchars((string)($report->created_at ?? '')); ?></td>
                            <td>
                                <div class="report-actions-wrap">
                                    <button
                                        class="admin-btn view-report"
                                        data-report-kind="Post Report"
                                        data-details="<?php echo htmlspecialchars($modalDetails); ?>"
                                        data-link="<?php echo htmlspecialchars($modalLink); ?>"
                                    >View</button>
                                    <form class="admin-status-form" method="post" action="<?php echo URLROOT; ?>/admin/updateContentReportStatus">
                                        <input type="hidden" name="report_id" value="<?php echo $reportId; ?>">
                                        <input type="hidden" name="source" value="<?php echo htmlspecialchars($source); ?>">
                                        <select class="admin-select" name="status" required>
                                            <option value="pending" <?php echo $statusClass === 'pending' ? 'selected' : ''; ?>>Pending</option>
                                            <option value="resolved" <?php echo $statusClass === 'resolved' ? 'selected' : ''; ?>>Resolved</option>
                                            <option value="rejected" <?php echo $statusClass === 'rejected' ? 'selected' : ''; ?>>Rejected</option>
                                        </select>
                                        <button class="admin-btn" type="submit">Save</button>
                                    </form>
                                    <?php if ($suspendTargetId > 0 && !$ownerProtected): ?>
                                        <button
                                            type="button"
                                            class="admin-btn admin-btn-danger suspend-user-btn"
                                            data-user-id="<?php echo $suspendTargetId; ?>"
                                            data-user-name="<?php echo htmlspecialchars($suspendTargetName); ?>"
                                        >Suspend User</button>
                                    <?php elseif ($ownerProtected): ?>
                                        <span class="table-subtitle">Protected admin account</span>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7">No post reports yet.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="admin-card" style="margin-top: 1.25rem;">
    <div class="card-header report-card-header">
        <div>
            <h3 style="margin:0;">User Profile Reports</h3>
            <div class="table-subtitle">Reports submitted against user profiles</div>
        </div>
        <span class="status-badge status-pending"><?php echo count($profileReports); ?> total</span>
    </div>

    <div class="reports-table-wrapper">
        <table class="admin-table" id="userReportsTable">
            <thead>
                <tr>
                    <th class="report-id">Report ID</th>
                    <th class="report-reporter">Reporter</th>
                    <th class="report-content">Reported User</th>
                    <th class="report-type">Type</th>
                    <th class="report-status">Status</th>
                    <th class="report-date">Date</th>
                    <th class="report-actions">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($profileReports)): ?>
                    <?php foreach ($profileReports as $report): ?>
                        <?php
                        $reportId = (int)($report->id ?? 0);
                        $reporterName = trim((string)($report->reporter_name ?? 'Unknown'));
                        $reporterRole = trim((string)($report->reporter_role ?? ''));
                        $targetName = trim((string)($report->target_name ?? ''));
                        if ($targetName === '') {
                            $targetName = 'User #' . (int)($report->profile_id ?? 0);
                        }
                        $targetRole = trim((string)($report->target_role ?? ''));
                        $status = trim((string)($report->status ?? 'pending'));
                        $statusClass = in_array($status, ['pending', 'resolved', 'rejected'], true) ? $status : 'pending';
                        $category = trim((string)($report->category ?? ''));
                        $modalDetails = trim((string)($report->details ?? ''));
                        $modalLink = trim((string)($report->reference_link ?? ''));
                        $source = trim((string)($report->source ?? 'reports'));
                        $suspendTargetId = (int)($report->profile_id ?? 0);
                        $suspendTargetName = trim((string)($targetName ?: ('User #' . $suspendTargetId)));
                        $targetRoleLower = strtolower($targetRole);
                        $targetProtected = $targetRoleLower !== '' && strpos($targetRoleLower, 'admin') !== false;
                        ?>
                        <tr>
                            <td><?php echo $reportId; ?></td>
                            <td data-modal-value="<?php echo htmlspecialchars($reporterName . ($reporterRole !== '' ? ' (' . $reporterRole . ')' : '')); ?>">
                                <?php echo htmlspecialchars($reporterName); ?>
                                <?php if ($reporterRole !== ''): ?>
                                    <div class="table-subtitle"><?php echo htmlspecialchars($reporterRole); ?></div>
                                <?php endif; ?>
                            </td>
                            <td data-modal-value="<?php echo htmlspecialchars($targetName . ($targetRole !== '' ? ' (' . $targetRole . ')' : '')); ?>">
                                <?php echo htmlspecialchars($targetName); ?>
                                <?php if ($targetRole !== ''): ?>
                                    <div class="table-subtitle"><?php echo htmlspecialchars($targetRole); ?></div>
                                <?php endif; ?>
                            </td>
                            <td><?php echo htmlspecialchars($category !== '' ? $category : 'Other'); ?></td>
                            <td><span class="status-badge status-<?php echo htmlspecialchars($statusClass); ?>"><?php echo ucfirst(htmlspecialchars($statusClass)); ?></span></td>
                            <td><?php echo htmlspecialchars((string)($report->created_at ?? '')); ?></td>
                            <td>
                                <div class="report-actions-wrap">
                                    <button
                                        class="admin-btn view-report"
                                        data-report-kind="Profile Report"
                                        data-details="<?php echo htmlspecialchars($modalDetails); ?>"
                                        data-link="<?php echo htmlspecialchars($modalLink); ?>"
                                    >View</button>
                                    <form class="admin-status-form" method="post" action="<?php echo URLROOT; ?>/admin/updateContentReportStatus">
                                        <input type="hidden" name="report_id" value="<?php echo $reportId; ?>">
                                        <input type="hidden" name="source" value="<?php echo htmlspecialchars($source); ?>">
                                        <select class="admin-select" name="status" required>
                                            <option value="pending" <?php echo $statusClass === 'pending' ? 'selected' : ''; ?>>Pending</option>
                                            <option value="resolved" <?php echo $statusClass === 'resolved' ? 'selected' : ''; ?>>Resolved</option>
                                            <option value="rejected" <?php echo $statusClass === 'rejected' ? 'selected' : ''; ?>>Rejected</option>
                                        </select>
                                        <button class="admin-btn" type="submit">Save</button>
                                    </form>
                                    <?php if ($suspendTargetId > 0 && !$targetProtected): ?>
                                        <button
                                            type="button"
                                            class="admin-btn admin-btn-danger suspend-user-btn"
                                            data-user-id="<?php echo $suspendTargetId; ?>"
                                            data-user-name="<?php echo htmlspecialchars($suspendTargetName); ?>"
                                        >Suspend User</button>
                                    <?php elseif ($targetProtected): ?>
                                        <span class="table-subtitle">Protected admin account</span>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7">No profile reports yet.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal for viewing report details -->
<div id="reportModal" class="admin-modal" style="display:none;">
    <div class="admin-modal-content">
        <span class="admin-modal-close">&times;</span>
        <h2>Report Details</h2>
        <div id="modalReportContent">
            <!-- Filled by JS -->
        </div>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Modal logic
    const modal = document.getElementById('reportModal');
    const modalClose = document.querySelector('.admin-modal-close');
    if (modalClose) {
        modalClose.onclick = () => modal.style.display = 'none';
    }
    window.onclick = (e) => { if (e.target === modal) modal.style.display = 'none'; };

    const esc = (str) => {
        const map = { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' };
        return String(str ?? '').replace(/[&<>"']/g, (m) => map[m]);
    };

    // View report
    document.querySelectorAll('.view-report').forEach(btn => {
        btn.onclick = function() {
            const row = this.closest('tr');
            const table = row ? row.closest('table') : null;
            let details = `<div style="margin-bottom:0.75rem;"><strong>Report Group:</strong> ${esc(this.dataset.reportKind || 'Report')}</div>`;
            const headers = row.closest('table')?.querySelectorAll('thead th') || [];
            row.querySelectorAll('td').forEach((td, i) => {
                if (!table || !headers[i]) return;
                if (i === headers.length - 1) return;
                const label = headers[i].textContent.trim();
                const value = (td.getAttribute('data-modal-value') || td.textContent || '').trim();
                details += `<div style="margin-bottom:0.45rem;"><b>${esc(label)}:</b> ${esc(value)}</div>`;
            });

            const reportDetails = (this.dataset.details || '').trim();
            const reportLink = (this.dataset.link || '').trim();
            if (reportDetails) {
                details += `<div style="margin-top:0.5rem;"><b>Submitted Details:</b><br>${esc(reportDetails)}</div>`;
            }
            if (reportLink) {
                details += `<div style="margin-top:0.5rem;"><b>Reference Link:</b> <a href="${esc(reportLink)}" target="_blank" rel="noopener">${esc(reportLink)}</a></div>`;
            }

            document.getElementById('modalReportContent').innerHTML = details;
            modal.style.display = 'block';
        };
    });

    // Persist status updates via AJAX to ensure action feedback is immediate.
    document.querySelectorAll('.admin-status-form').forEach(form => {
        form.addEventListener('submit', async function(e) {
            e.preventDefault();
            const submitBtn = form.querySelector('button[type="submit"]');
            const statusSelect = form.querySelector('select[name="status"]');
            if (!statusSelect) return;

            const selectedStatus = statusSelect.value;
            const previousText = submitBtn ? submitBtn.textContent : '';
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.textContent = 'Saving...';
            }

            try {
                const fd = new FormData(form);
                const res = await fetch(form.action, {
                    method: 'POST',
                    body: fd,
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                const json = await res.json().catch(() => null);
                if (!res.ok || !json || !json.ok) {
                    throw new Error((json && json.error) ? json.error : 'Failed to save report status');
                }

                const row = form.closest('tr');
                const badge = row ? row.querySelector('.status-badge') : null;
                if (badge) {
                    badge.className = `status-badge status-${selectedStatus}`;
                    badge.textContent = selectedStatus.charAt(0).toUpperCase() + selectedStatus.slice(1);
                }
            } catch (err) {
                console.error('Status update failed', err);
                await AdminPopup.alert(err && err.message ? err.message : 'Failed to save report status', {
                    title: 'Report Status',
                    danger: true
                });
            } finally {
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.textContent = previousText || 'Save';
                }
            }
        });
    });

    // Suspend user directly from a report row when moderation requires escalation.
    document.querySelectorAll('.suspend-user-btn').forEach(btn => {
        btn.addEventListener('click', async function() {
            const userId = Number(this.dataset.userId || 0);
            const userName = this.dataset.userName || `User #${userId}`;
            if (!userId) {
                await AdminPopup.alert('Invalid user id for suspension', { title: 'Suspend User' });
                return;
            }

            const reasonInput = await AdminPopup.prompt(
                `Suspend ${userName}?\nProvide a reason (optional):`,
                'Suspended due to repeated reports',
                { title: 'Suspend User', confirmText: 'Suspend', danger: true }
            );
            if (reasonInput === null) {
                return;
            }

            const previousText = this.textContent;
            this.disabled = true;
            this.textContent = 'Suspending...';

            try {
                const res = await fetch(`<?php echo URLROOT; ?>/admin/suspendUser`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({
                        user_id: userId,
                        reason: (reasonInput || '').trim()
                    })
                });

                const json = await res.json().catch(() => null);
                if (!res.ok || !json || !json.ok) {
                    throw new Error((json && json.error) ? json.error : 'Failed to suspend user');
                }

                await AdminPopup.alert((json && json.message) ? json.message : `${userName} suspended successfully`, {
                    title: 'Suspend User'
                });
                this.textContent = 'Suspended';
            } catch (err) {
                console.error('Suspension failed', err);
                await AdminPopup.alert(err && err.message ? err.message : 'Failed to suspend user', {
                    title: 'Suspend User',
                    danger: true
                });
                this.disabled = false;
                this.textContent = previousText;
            }
        });
    });
});
</script>
<?php $content = ob_get_clean(); ?>
<?php require APPROOT . '/views/admin/dashboard_layout.php'; ?>



