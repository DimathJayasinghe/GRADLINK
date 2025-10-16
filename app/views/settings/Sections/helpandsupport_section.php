<div class="account_content">
	<h2>Help & Support</h2>
	<p class="settings-description">Find answers, report issues, or contact our support team.</p>

	<div class="settings-section">
		<h3>Quick Help</h3>
		<div class="section-divider"></div>
		<ul class="simple-list">
			<li>
				<span>How do I reset my password?</span>
				<a href="#" class="view-more-link">View guide</a>
			</li>
			<li>
				<span>How to edit my profile?</span>
				<a href="#" class="view-more-link">View guide</a>
			</li>
			<li>
				<span>Managing notifications</span>
				<a href="#" class="view-more-link">View guide</a>
			</li>
		</ul>
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
	const supportModal = document.getElementById('supportModal');
	const reportModal = document.getElementById('reportModal');
	const openSupport = document.getElementById('openSupport');
	const openReport = document.getElementById('openReport');

	if (openSupport) openSupport.addEventListener('click', () => openModal(supportModal));
	if (openReport) openReport.addEventListener('click', () => openModal(reportModal));

	document.querySelectorAll('.settings-close-modal, .cancel-modal').forEach(el => {
		el.addEventListener('click', function(){ closeModal(this.closest('.settings-modal')); });
	});

	window.addEventListener('click', function(event){
		[supportModal, reportModal].forEach(modal => { if (event.target === modal) closeModal(modal); });
	});

	['feedbackForm','supportForm','reportForm'].forEach(id => {
		const form = document.getElementById(id);
		if (!form) return;
		form.addEventListener('submit', function(e){
			e.preventDefault();
			alert('Submitted. This is a demo only.');
			const modal = this.closest('.settings-modal');
			if (modal) closeModal(modal);
		});
	});

	function openModal(modal){ if (modal) modal.style.display = 'block'; }
	function closeModal(modal){ if (modal) modal.style.display = 'none'; }
});
</script>
