<div class="account_content">
	<h2>Notifications</h2>
	<p class="settings-description">Choose how and when you get notified.</p>

	<div class="settings-section">
		<h3>Delivery Channels</h3>
		<div class="section-divider"></div>

		<div class="settings-option">
			<div class="settings-option-details">
				<h4>Email Notifications</h4>
				<p>Receive updates via email</p>
			</div>
			<label class="toggle">
				<input type="checkbox" id="emailNotif" checked>
				<span class="slider"></span>
			</label>
		</div>

		<div class="settings-option">
			<div class="settings-option-details">
				<h4>Push Notifications</h4>
				<p>Notifications to your device</p>
			</div>
			<label class="toggle">
				<input type="checkbox" id="pushNotif" checked>
				<span class="slider"></span>
			</label>
		</div>

		<div class="settings-option">
			<div class="settings-option-details">
				<h4>In-App Sounds</h4>
				<p>Play a sound for new notifications</p>
			</div>
			<label class="toggle">
				<input type="checkbox" id="soundNotif">
				<span class="slider"></span>
			</label>
		</div>
	</div>

	<div class="settings-section">
		<h3>Frequency</h3>
		<div class="section-divider"></div>
		<div class="settings-option">
			<div class="settings-option-details">
				<h4>Email Digest</h4>
				<p>Get a summary of notifications via email</p>
			</div>
			<select id="emailDigest" class="select-inline">
				<option value="off">Off</option>
				<option value="daily" selected>Daily</option>
				<option value="weekly">Weekly</option>
			</select>
		</div>
		<div class="settings-option">
			<div class="settings-option-details">
				<h4>Quiet Hours</h4>
				<p>Silence push notifications during specific times</p>
			</div>
			<button class="settings-btn" id="openDND">Set schedule</button>
		</div>
	</div>

	<div class="settings-section">
		<h3>Categories</h3>
		<div class="section-divider"></div>
		<div class="settings-option">
			<div class="settings-option-details">
				<h4>Mentions and Replies</h4>
				<p>When someone mentions you or replies</p>
			</div>
			<label class="toggle">
				<input type="checkbox" id="catMentions" checked>
				<span class="slider"></span>
			</label>
		</div>
		<div class="settings-option">
			<div class="settings-option-details">
				<h4>New Followers</h4>
				<p>When someone starts following you</p>
			</div>
			<label class="toggle">
				<input type="checkbox" id="catFollowers" checked>
				<span class="slider"></span>
			</label>
		</div>
		<div class="settings-option">
			<div class="settings-option-details">
				<h4>Post Engagement</h4>
				<p>Likes, comments, and shares</p>
			</div>
			<label class="toggle">
				<input type="checkbox" id="catEngagement" checked>
				<span class="slider"></span>
			</label>
		</div>
	</div>

	<!-- DND Modal -->
	<div id="dndModal" class="settings-modal">
		<div class="settings-modal-content">
			<div class="settings-modal-header">
				<h3>Quiet Hours</h3>
				<span class="settings-close-modal">&times;</span>
			</div>
			<div class="settings-modal-body">
				<form id="dndForm">
					<div class="form-group">
						<label for="dndStart">Start time</label>
						<input type="time" id="dndStart" value="22:00">
					</div>
					<div class="form-group">
						<label for="dndEnd">End time</label>
						<input type="time" id="dndEnd" value="07:00">
					</div>
					<div class="form-group">
						<label for="dndDays">Days</label>
						<select id="dndDays">
							<option value="weekdays" selected>Weekdays</option>
							<option value="weekends">Weekends</option>
							<option value="everyday">Every day</option>
						</select>
					</div>
					<div class="form-actions">
						<button type="button" class="settings-btn-secondary cancel-modal">Cancel</button>
						<button type="submit" class="settings-btn">Save</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function(){
	const dndModal = document.getElementById('dndModal');
	const openDND = document.getElementById('openDND');
	if (openDND) openDND.addEventListener('click', () => openModal(dndModal));

	document.querySelectorAll('.settings-close-modal, .cancel-modal').forEach(el => {
		el.addEventListener('click', function(){ closeModal(this.closest('.settings-modal')); });
	});

	window.addEventListener('click', function(event) {
		if (event.target === dndModal) closeModal(dndModal);
	});

	const dndForm = document.getElementById('dndForm');
	if (dndForm) dndForm.addEventListener('submit', function(e){
		e.preventDefault();
		alert('Quiet hours saved. Demo only.');
		closeModal(dndModal);
	});

	function openModal(modal){ if (modal) modal.style.display = 'block'; }
	function closeModal(modal){ if (modal) modal.style.display = 'none'; }
});
</script>
