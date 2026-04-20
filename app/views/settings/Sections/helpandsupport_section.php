<?php
    $myTickets = $data['myTickets'] ?? [];
    $myReports = $data['myReports'] ?? [];
?>

<div class="account_content">
	<h2>Help & Support</h2>
	<p class="settings-description">Find answers, report issues, or contact our support team.</p>

	<div class="settings-section">
		<h3>Quick Help</h3>
		<div class="section-divider"></div>
		<ul class="simple-list">
			<li>
				<span>How do I reset my password?</span>
				<a href="#" class="view-more-link" data-guide="reset_password">View guide</a>
			</li>
			<li>
				<span>How to edit my profile?</span>
				<a href="#" class="view-more-link" data-guide="edit_profile">View guide</a>
			</li>
			<li>
				<span>Managing notifications</span>
				<a href="#" class="view-more-link" data-guide="notifications">View guide</a>
			</li>
		</ul>
	</div>

	<!-- Guide Modal (Quick Help) -->
	<div id="guideModal" class="settings-modal">
		<div class="settings-modal-content">
			<div class="settings-modal-header">
				<h3 id="guideTitle">Guide</h3>
				<span class="settings-close-modal">&times;</span>
			</div>
			<div class="settings-modal-body">
				<div id="guideBody" style="color: var(--muted); line-height: 1.7;"></div>
				<div class="form-actions" style="margin-top: 14px;">
					<button type="button" class="settings-btn-secondary cancel-modal">Close</button>
					<a id="guideCta" href="#" class="settings-btn" style="text-decoration:none; display:inline-block;">Go</a>
				</div>
			</div>
		</div>
	</div>

	<div class="settings-section">
		<h3>Support</h3>
		<div class="section-divider"></div>
		<div class="settings-option">
			<div class="settings-option-details">
				<h4>Contact Support</h4>
				<p>Reach our team for account or technical help</p>
			</div>
			<button class="settings-btn" id="openSupport">Contact</button>
		</div>
		<div class="settings-option">
			<div class="settings-option-details">
				<h4>Report a Problem</h4>
				<p>Tell us about a bug or policy violation</p>
			</div>
			<button class="settings-btn settings-btn-danger" id="openReport">Report</button>
		</div>
	</div>

	<div class="settings-section">
		<h3>My Requests</h3>
		<div class="section-divider"></div>

		<div style="margin-bottom: 18px;">
			<h4 style="margin: 0 0 10px; color: var(--text); font-weight: 600;">Support Tickets</h4>
			<?php if (!empty($myTickets)): ?>
				<ul class="simple-list">
					<?php foreach ($myTickets as $t): ?>
						<?php $canEditTicket = (($t->status ?? '') === 'open') && (($t->admin_reply ?? '') === '' || ($t->admin_reply ?? null) === null); ?>
						<li>
							<span>
								Ticket #<?php echo htmlspecialchars($t->id); ?> — <?php echo htmlspecialchars($t->topic); ?>
								(<?php echo str_replace('_', ' ', htmlspecialchars($t->status)); ?>)
							</span>
							<?php if ($canEditTicket): ?>
								<a href="#" class="view-more-link edit-ticket-link" data-ticket-id="<?php echo (int)$t->id; ?>">Edit</a>
							<?php else: ?>
								<span style="color: var(--muted); font-size: 0.9rem;">Locked</span>
							<?php endif; ?>
						</li>
					<?php endforeach; ?>
				</ul>
			<?php else: ?>
				<p style="color: var(--muted); margin: 0;">No support tickets yet.</p>
			<?php endif; ?>
		</div>

		<div>
			<h4 style="margin: 0 0 10px; color: var(--text); font-weight: 600;">Problem Reports</h4>
			<?php if (!empty($myReports)): ?>
				<ul class="simple-list">
					<?php foreach ($myReports as $r): ?>
						<?php $canEditReport = (($r->status ?? '') === 'pending') && (($r->admin_reply ?? '') === '' || ($r->admin_reply ?? null) === null); ?>
						<li>
							<span>
								Report #<?php echo htmlspecialchars($r->id); ?> — <?php echo htmlspecialchars($r->report_type); ?>
								(<?php echo str_replace('_', ' ', htmlspecialchars($r->status)); ?>)
							</span>
							<?php if ($canEditReport): ?>
								<a href="#" class="view-more-link edit-report-link" data-report-id="<?php echo (int)$r->id; ?>">Edit</a>
							<?php else: ?>
								<span style="color: var(--muted); font-size: 0.9rem;">Locked</span>
							<?php endif; ?>
						</li>
					<?php endforeach; ?>
				</ul>
			<?php else: ?>
				<p style="color: var(--muted); margin: 0;">No problem reports yet.</p>
			<?php endif; ?>
		</div>
	</div>

	<div class="settings-section">
		<h3>Feedback</h3>
		<div class="section-divider"></div>
		<form id="feedbackForm">
			<div class="form-group">
				<label for="feedbackType">Type</label>
				<select id="feedbackType">
					<option value="feature">Feature request</option>
					<option value="ux">Usability/UX</option>
					<option value="other">Other</option>
				</select>
			</div>
			<div class="form-group">
				<label for="feedbackMessage">Your feedback</label>
				<textarea id="feedbackMessage" rows="4" placeholder="Share your ideas or concerns..."></textarea>
			</div>
			<div class="form-actions">
				<button type="submit" class="settings-btn">Submit</button>
			</div>
		</form>
	</div>

	<!-- Contact Support Modal -->
	<div id="supportModal" class="settings-modal">
		<div class="settings-modal-content">
			<div class="settings-modal-header">
				<h3>Contact Support</h3>
				<span class="settings-close-modal">&times;</span>
			</div>
			<div class="settings-modal-body">
				<form id="supportForm">
					<div class="form-group">
						<label for="supportEmail">Your email</label>
						<input type="email" id="supportEmail" placeholder="you@example.com" required>
					</div>
					<div class="form-group">
						<label for="supportTopic">Topic</label>
						<select id="supportTopic">
							<option value="account">Account</option>
							<option value="technical" selected>Technical issue</option>
							<option value="billing">Billing</option>
						</select>
					</div>
					<div class="form-group">
						<label for="supportMessage">Message</label>
						<textarea id="supportMessage" rows="4" required></textarea>
					</div>
					<div class="form-actions">
						<button type="button" class="settings-btn-secondary cancel-modal">Cancel</button>
						<button type="submit" class="settings-btn">Send</button>
					</div>
				</form>
			</div>
		</div>
	</div>

	<!-- Report Problem Modal -->
	<div id="reportModal" class="settings-modal">
		<div class="settings-modal-content">
			<div class="settings-modal-header">
				<h3>Report a Problem</h3>
				<span class="settings-close-modal">&times;</span>
			</div>
			<div class="settings-modal-body">
				<form id="reportForm">
					<div class="form-group">
						<label for="reportType">What are you reporting?</label>
						<select id="reportType">
							<option value="bug">Bug</option>
							<option value="abuse">Abuse or harassment</option>
							<option value="policy">Policy violation</option>
						</select>
					</div>
					<div class="form-group">
						<label for="reportDetails">Details</label>
						<textarea id="reportDetails" rows="4" required></textarea>
					</div>
					<div class="form-actions">
						<button type="button" class="settings-btn-secondary cancel-modal">Cancel</button>
						<button type="submit" class="settings-btn-danger">Submit report</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function(){
	const URLROOT = '<?= URLROOT ?>';
	const currentUserId = <?= (int)($_SESSION['user_id'] ?? 0) ?>;
	const myTicketsData = <?php echo json_encode($myTickets, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;
	const myReportsData = <?php echo json_encode($myReports, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;
	const guideModal = document.getElementById('guideModal');
	const guideTitle = document.getElementById('guideTitle');
	const guideBody = document.getElementById('guideBody');
	const guideCta = document.getElementById('guideCta');

	const supportModal = document.getElementById('supportModal');
	const reportModal = document.getElementById('reportModal');
	const openSupport = document.getElementById('openSupport');
	const openReport = document.getElementById('openReport');

	if (openSupport) openSupport.addEventListener('click', () => {
		if (typeof window.__resetSupportEditMode === 'function') window.__resetSupportEditMode();
		openModal(supportModal);
	});
	if (openReport) openReport.addEventListener('click', () => {
		if (typeof window.__resetReportEditMode === 'function') window.__resetReportEditMode();
		openModal(reportModal);
	});

	document.querySelectorAll('.settings-close-modal, .cancel-modal').forEach(el => {
		el.addEventListener('click', function(){ closeModal(this.closest('.settings-modal')); });
	});

	window.addEventListener('click', function(event){
		[supportModal, reportModal, guideModal].forEach(modal => { if (event.target === modal) closeModal(modal); });
	});

	// Quick Help guides
	document.querySelectorAll('.view-more-link[data-guide]').forEach(link => {
		link.addEventListener('click', function(e){
			e.preventDefault();
			openGuide(this.getAttribute('data-guide'));
		});
	});

	// Support form
	const supportForm = document.getElementById('supportForm');
	if (supportForm) {
		const supportSubmitBtn = supportForm.querySelector('button[type="submit"]');
		const supportModalTitle = supportModal ? supportModal.querySelector('.settings-modal-header h3') : null;

		function resetSupportEditMode() {
			delete supportForm.dataset.editId;
			supportForm.reset();
			if (supportModalTitle) supportModalTitle.textContent = 'Contact Support';
			if (supportSubmitBtn) supportSubmitBtn.textContent = 'Send';
		}

		function setSupportEditMode(ticket) {
			supportForm.dataset.editId = String(ticket.id);
			if (supportModalTitle) supportModalTitle.textContent = 'Edit Support Ticket #' + ticket.id;
			if (supportSubmitBtn) supportSubmitBtn.textContent = 'Update';
			const emailEl = document.getElementById('supportEmail');
			const topicEl = document.getElementById('supportTopic');
			const msgEl = document.getElementById('supportMessage');
			if (emailEl) emailEl.value = ticket.email || '';
			if (topicEl) topicEl.value = ticket.topic || 'technical';
			if (msgEl) msgEl.value = ticket.message || '';
		}

		// Expose for click handlers
		window.__setSupportEditMode = setSupportEditMode;
		window.__resetSupportEditMode = resetSupportEditMode;

		supportForm.addEventListener('submit', function(e){
			e.preventDefault();
			const editId = parseInt(supportForm.dataset.editId || '0', 10);
			const payload = {
				id: editId || undefined,
				email: document.getElementById('supportEmail').value,
				topic: document.getElementById('supportTopic').value,
				message: document.getElementById('supportMessage').value
			};
			if (!payload.id) {
				delete payload.id;
			}

			fetch(URLROOT + (editId ? '/settings/updateSupportTicket' : '/settings/submitSupportRequest'), {
				method: 'POST',
				headers: {'Content-Type': 'application/json'},
				body: JSON.stringify(payload)
			})
			.then(r => r.json())
			.then(res => {
				if (res.success) {
					//alert(editId ? 'Support ticket updated successfully!' : ('Support ticket submitted successfully! Ticket #' + res.ticket_id));
					supportForm.reset();
					resetSupportEditMode();
					closeModal(supportModal);
					window.location.reload();
				} else {
					alert(res.error || 'Failed to submit support request.');
				}
			})
			.catch(() => alert('Network error. Please try again.'));
		});

		// Allow other handlers to call this within DOM scope
		window.resetSupportEditMode = resetSupportEditMode;
	}

	// Report form
	const reportForm = document.getElementById('reportForm');
	if (reportForm) {
		const reportSubmitBtn = reportForm.querySelector('button[type="submit"]');
		const reportModalTitle = reportModal ? reportModal.querySelector('.settings-modal-header h3') : null;

		function resetReportEditMode() {
			delete reportForm.dataset.editId;
			reportForm.reset();
			if (reportModalTitle) reportModalTitle.textContent = 'Report a Problem';
			if (reportSubmitBtn) reportSubmitBtn.textContent = 'Submit report';
		}

		function setReportEditMode(report) {
			reportForm.dataset.editId = String(report.id);
			if (reportModalTitle) reportModalTitle.textContent = 'Edit Problem Report #' + report.id;
			if (reportSubmitBtn) reportSubmitBtn.textContent = 'Update';
			const typeEl = document.getElementById('reportType');
			const detailsEl = document.getElementById('reportDetails');
			if (typeEl) typeEl.value = report.report_type || 'bug';
			if (detailsEl) detailsEl.value = report.details || '';
		}

		window.__setReportEditMode = setReportEditMode;
		window.__resetReportEditMode = resetReportEditMode;

		reportForm.addEventListener('submit', function(e){
			e.preventDefault();
			const editId = parseInt(reportForm.dataset.editId || '0', 10);
			const payload = {
				id: editId || undefined,
				report_type: document.getElementById('reportType').value,
				details: document.getElementById('reportDetails').value
			};
			if (!payload.id) {
				delete payload.id;
			}

			fetch(URLROOT + (editId ? '/settings/updateProblemReport' : '/settings/submitProblemReport'), {
				method: 'POST',
				headers: {'Content-Type': 'application/json'},
				body: JSON.stringify(payload)
			})
			.then(r => r.json())
			.then(res => {
				if (res.success) {
					//alert(editId ? 'Problem report updated successfully!' : ('Problem report submitted successfully! Report #' + res.report_id));
					reportForm.reset();
					resetReportEditMode();
					closeModal(reportModal);
					window.location.reload();
				} else {
					alert(res.error || 'Failed to submit report.');
				}
			})
			.catch(() => alert('Network error. Please try again.'));
		});

		window.resetReportEditMode = resetReportEditMode;
	}

	// ---- Edit links ----
	document.querySelectorAll('.edit-ticket-link[data-ticket-id]').forEach(link => {
		link.addEventListener('click', function(e){
			e.preventDefault();
			const id = parseInt(this.getAttribute('data-ticket-id') || '0', 10);
			const ticket = (myTicketsData || []).find(t => parseInt(t.id, 10) === id);
			if (!ticket) return;
			if (typeof window.__setSupportEditMode === 'function') {
				window.__setSupportEditMode(ticket);
				openModal(supportModal);
			}
		});
	});

	document.querySelectorAll('.edit-report-link[data-report-id]').forEach(link => {
		link.addEventListener('click', function(e){
			e.preventDefault();
			const id = parseInt(this.getAttribute('data-report-id') || '0', 10);
			const report = (myReportsData || []).find(r => parseInt(r.id, 10) === id);
			if (!report) return;
			if (typeof window.__setReportEditMode === 'function') {
				window.__setReportEditMode(report);
				openModal(reportModal);
			}
		});
	});

	// Feedback form
	const feedbackForm = document.getElementById('feedbackForm');
	if (feedbackForm) {
		feedbackForm.addEventListener('submit', function(e){
			e.preventDefault();
			const payload = {
				feedback_type: document.getElementById('feedbackType').value,
				message: document.getElementById('feedbackMessage').value
			};
			fetch(URLROOT + '/settings/submitFeedback', {
				method: 'POST',
				headers: {'Content-Type': 'application/json'},
				body: JSON.stringify(payload)
			})
			.then(r => r.json())
			.then(res => {
				if (res.success) {
					alert('Feedback submitted successfully! Thank you.');
					feedbackForm.reset();
				} else {
					alert(res.error || 'Failed to submit feedback.');
				}
			})
			.catch(() => alert('Network error. Please try again.'));
		});
	}

	function openModal(modal){ if (modal) modal.style.display = 'block'; }
	function closeModal(modal){ if (modal) modal.style.display = 'none'; }

	function openGuide(key){
		if (!guideModal || !guideTitle || !guideBody || !guideCta) return;

		const guides = {
			reset_password: {
				title: 'Reset Password',
				body: `
					<p><strong>If you are already logged in</strong>, you can change your password from Settings.</p>
					<ol style="margin: 8px 0 0 18px;">
						<li>Go to <strong>Settings → Account</strong>.</li>
						<li>Find <strong>Password</strong> and click <strong>Change</strong>.</li>
						<li>Enter your <strong>current password</strong>, then a <strong>new password</strong>, and confirm it.</li>
						<li>Click <strong>Update Password</strong>.</li>
					</ol>
					<p style="margin-top:10px;"><strong>If you cannot log in</strong>, use the Help & Support contact form to request assistance.</p>
				`,
				ctaText: 'Open Account Settings',
				ctaHref: `${URLROOT}/settings/account`
			},
			edit_profile: {
				title: 'Edit Profile',
				body: `
					<p>You can update your profile details from your Profile page and from Settings.</p>
					<ol style="margin: 8px 0 0 18px;">
						<li>Open <strong>Profile</strong> from the sidebar.</li>
						<li>Click <strong>Edit Profile</strong>.</li>
						<li>Update the fields you want (name, bio, skills, profile photo) and save.</li>
					</ol>
					<p style="margin-top:10px;">For changing <strong>Name</strong>, <strong>Bio</strong>, <strong>Email</strong>, or <strong>Password</strong>, go to <strong>Settings → Account</strong>.</p>
				`,
				ctaText: 'Open My Profile',
				ctaHref: currentUserId ? `${URLROOT}/profile?userid=${currentUserId}` : `${URLROOT}/profile`
			},
			notifications: {
				title: 'Managing Notifications',
				body: `
					<p>Control what notifications you receive from the Notifications settings page.</p>
					<ol style="margin: 8px 0 0 18px;">
						<li>Go to <strong>Settings → Notifications</strong>.</li>
						<li>Use <strong>Delivery Channels</strong> to enable/disable <strong>Email</strong> and <strong>In-App Sounds</strong>.</li>
						<li>Use <strong>Categories</strong> to turn on/off:</li>
					</ol>
					<ul style="margin: 8px 0 0 18px;">
						<li><strong>Mentions and Replies</strong></li>
						<li><strong>New Followers</strong> (follow requests / started following)</li>
						<li><strong>Post Engagement</strong> (likes / comments / shares)</li>
					</ul>
					<p style="margin-top:10px;">When a category is OFF, those notifications will not show in your in-app notification list.</p>
				`,
				ctaText: 'Open Notification Settings',
				ctaHref: `${URLROOT}/settings/notifications`
			}
		};

		const g = guides[key] || guides.notifications;
		guideTitle.textContent = g.title;
		guideBody.innerHTML = g.body;
		guideCta.textContent = g.ctaText;
		guideCta.setAttribute('href', g.ctaHref);
		openModal(guideModal);
	}
});
</script>
