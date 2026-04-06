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

		<!-- <div class="settings-option">
			<div class="settings-option-details">
				<h4>Push Notifications</h4>
				<p>Notifications to your device</p>
			</div>
			<label class="toggle">
				<input type="checkbox" id="pushNotif" checked>
				<span class="slider"></span>
			</label>
		</div> -->

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

	<!-- <div class="settings-section">
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
	</div> -->

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
	const URLROOT = '<?= URLROOT ?>';
	const dndModal = document.getElementById('dndModal');
	const openDND = document.getElementById('openDND');
	const dndForm = document.getElementById('dndForm');

	// Load initial settings from backend
	loadNotificationSettings();

	// Modal handlers
	if (openDND) openDND.addEventListener('click', () => openModal(dndModal));

	document.querySelectorAll('.settings-close-modal, .cancel-modal').forEach(el => {
		el.addEventListener('click', function(){ closeModal(this.closest('.settings-modal')); });
	});

	window.addEventListener('click', function(event) {
		if (event.target === dndModal) closeModal(dndModal);
	});

	// Auto-save handlers for all toggle checkboxes
	const toggleCheckboxes = [
		'emailNotif',
		'soundNotif',
		'catMentions',
		'catFollowers',
		'catEngagement'
	];

	toggleCheckboxes.forEach(checkboxId => {
		const checkbox = document.getElementById(checkboxId);
		if (checkbox) {
			checkbox.addEventListener('change', handleToggleChange);
		}
	});

	// DND form submission
	if (dndForm) {
		dndForm.addEventListener('submit', handleDNDFormSubmit);
	}

	// Load settings from backend
	async function loadNotificationSettings() {
		try {
			const response = await fetch(`${URLROOT}/settings/getNotificationSettings`);
			const data = await response.json();

			if (data.success && data.settings) {
				const settings = data.settings;

				// Set delivery channel toggles
				safeSetCheckbox('emailNotif', settings.email_enabled);
				safeSetCheckbox('soundNotif', settings.sound_enabled);

				// Set category toggles
				safeSetCheckbox('catMentions', settings.mentions_enabled);
				safeSetCheckbox('catFollowers', settings.followers_enabled);
				safeSetCheckbox('catEngagement', settings.engagement_enabled);

				// Set DND fields
				if (settings.dnd_start) document.getElementById('dndStart').value = settings.dnd_start;
				if (settings.dnd_end) document.getElementById('dndEnd').value = settings.dnd_end;
				if (settings.dnd_days) document.getElementById('dndDays').value = settings.dnd_days;
			}
		} catch (error) {
			console.error('Failed to load notification settings:', error);
		}
	}

	// Handle toggle checkbox changes
	async function handleToggleChange(e) {
		const checkbox = e.target;
		const settings = {
			email_enabled: document.getElementById('emailNotif')?.checked ? 1 : 0,
			sound_enabled: document.getElementById('soundNotif')?.checked ? 1 : 0,
			mentions_enabled: document.getElementById('catMentions')?.checked ? 1 : 0,
			followers_enabled: document.getElementById('catFollowers')?.checked ? 1 : 0,
			engagement_enabled: document.getElementById('catEngagement')?.checked ? 1 : 0,
			dnd_enabled: 0,
			dnd_start: null,
			dnd_end: null,
			dnd_days: null
		};

		try {
			const response = await fetch(`${URLROOT}/settings/updateNotificationSettings`, {
				method: 'POST',
				headers: { 'Content-Type': 'application/json' },
				body: JSON.stringify(settings)
			});

			const data = await response.json();

			if (data.success) {
				showNotification('Settings saved', 'success');
			} else {
				showNotification(data.error || 'Failed to save settings', 'error');
				checkbox.checked = !checkbox.checked;
			}
		} catch (error) {
			showNotification('Error saving settings', 'error');
			checkbox.checked = !checkbox.checked;
		}
	}

	// Handle DND form submission
	async function handleDNDFormSubmit(e) {
		e.preventDefault();

		const dndStart = document.getElementById('dndStart').value;
		const dndEnd = document.getElementById('dndEnd').value;
		const dndDays = document.getElementById('dndDays').value;

		if (!dndStart || !dndEnd) {
			showNotification('Please fill in all DND fields', 'error');
			return;
		}

		const settings = {
			email_enabled: document.getElementById('emailNotif')?.checked ? 1 : 0,
			sound_enabled: document.getElementById('soundNotif')?.checked ? 1 : 0,
			mentions_enabled: document.getElementById('catMentions')?.checked ? 1 : 0,
			followers_enabled: document.getElementById('catFollowers')?.checked ? 1 : 0,
			engagement_enabled: document.getElementById('catEngagement')?.checked ? 1 : 0,
			dnd_enabled: 1,
			dnd_start: dndStart,
			dnd_end: dndEnd,
			dnd_days: dndDays
		};

		try {
			const response = await fetch(`${URLROOT}/settings/updateNotificationSettings`, {
				method: 'POST',
				headers: { 'Content-Type': 'application/json' },
				body: JSON.stringify(settings)
			});

			const data = await response.json();

			if (data.success) {
				showNotification('Quiet hours saved successfully', 'success');
				closeModal(dndModal);
			} else {
				showNotification(data.error || 'Failed to save quiet hours', 'error');
			}
		} catch (error) {
			showNotification('Error saving quiet hours', 'error');
		}
	}

	// Helper: safely set checkbox state
	function safeSetCheckbox(id, value) {
		const checkbox = document.getElementById(id);
		if (checkbox) {
			// DB/API may return 0/1 as strings; treat only 1/true as enabled
			checkbox.checked = (value === 1 || value === '1' || value === true);
		}
	}

	// Helper: open modal
	function openModal(modal) {
		if (modal) modal.style.display = 'block';
	}

	// Helper: close modal
	function closeModal(modal) {
		if (modal) modal.style.display = 'none';
	}

	// Helper: show notification
	function showNotification(message, type) {
		const notification = document.createElement('div');
		notification.className = `notification notification-${type}`;
		notification.textContent = message;
		notification.style.cssText = `
			position: fixed;
			top: 20px;
			right: 20px;
			padding: 15px 20px;
			background: ${type === 'success' ? '#4caf50' : '#f44336'};
			color: white;
			border-radius: 5px;
			z-index: 10000;
			box-shadow: 0 4px 6px rgba(0,0,0,0.3);
		`;

		document.body.appendChild(notification);

		setTimeout(() => {
			notification.style.transition = 'opacity 0.3s';
			notification.style.opacity = '0';
			setTimeout(() => notification.remove(), 300);
		}, 3000);
	}
});
</script>
