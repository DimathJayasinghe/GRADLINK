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
				<input type="checkbox" id="emailNotif">
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

		<!-- <div class="settings-option">
			<div class="settings-option-details">
				<h4>In-App Notifications</h4>
				<p>Play a sound for new notifications</p>
			</div>
			<label class="toggle">
				<input type="checkbox" id="soundNotif">
				<span class="slider"></span>
			</label>
		</div> -->
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

	<!-- <div class="settings-section">
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

	 DND Modal 
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
</div> -->

<script>
document.addEventListener('DOMContentLoaded', function() {
	const URLROOT = '<?= URLROOT ?>';
	const emailToggle = document.getElementById('emailNotif');

	if (!emailToggle) {
		return;
	}

	loadNotificationSettings();
	emailToggle.addEventListener('change', handleEmailToggleChange);

	async function loadNotificationSettings() {
		try {
			const response = await fetch(`${URLROOT}/settings/getNotificationSettings`);
			const data = await parseJsonResponse(response);

			if (!response.ok || !data.success || !data.settings) {
				throw new Error(data.error || `Failed to load settings (${response.status})`);
			}

			safeSetCheckbox(emailToggle, data.settings.email_enabled);
		} catch (error) {
			console.error('Failed to load notification settings:', error);
			safeSetCheckbox(emailToggle, 0);
		}
	}

	async function handleEmailToggleChange() {
		const payload = { email_enabled: emailToggle.checked ? 1 : 0 };

		try {
			const response = await fetch(`${URLROOT}/settings/updateNotificationSettings`, {
				method: 'POST',
				headers: { 'Content-Type': 'application/json' },
				body: JSON.stringify(payload)
			});

			const data = await parseJsonResponse(response);

			if (!response.ok || !data.success) {
				throw new Error(data.error || `Failed to save settings (${response.status})`);
			}

			showNotification('Settings saved', 'success');
			refreshNotificationBadge();
		} catch (error) {
			emailToggle.checked = !emailToggle.checked;
			showNotification('Error saving settings', 'error');
		}
	}

	function safeSetCheckbox(checkbox, value) {
		checkbox.checked = (value === 1 || value === '1' || value === true);
	}

	function refreshNotificationBadge() {
		if (window.notificationManager && typeof window.notificationManager.fetchCount === 'function') {
			window.notificationManager.fetchCount();
		}
	}

	async function parseJsonResponse(response) {
		const body = await response.text();
		if (!body) {
			return {};
		}

		try {
			return JSON.parse(body);
		} catch (error) {
			throw new Error(`Server returned non-JSON response (${response.status})`);
		}
	}

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
