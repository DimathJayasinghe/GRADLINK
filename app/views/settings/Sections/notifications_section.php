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

	<div class="settings-section">
		<h3>In-App Notification Types</h3>
		<div class="section-divider"></div>

		<?php
		$inAppTypes = [
			// 'welcome' => 'Welcome',
			// 'info' => 'Info',
			'warning' => 'Warning',
			'alert' => 'Alert',
			'message' => 'Message',
			'follow_request' => 'Follow request',
			'started_following' => 'Started following',
			'new_message' => 'New message',
			'like' => 'Like',
			'comment' => 'Comment',
			'event_update' => 'Event update',
			'post_approval' => 'Post approval',
			'fundraiser_update' => 'Fundraiser update',
			'system_announcement' => 'System announcement',
			'admin_message' => 'Admin message'
		];
		?>

		<?php foreach ($inAppTypes as $typeKey => $typeLabel) : ?>
			<div class="settings-option">
				<div class="settings-option-details">
					<h4><?= htmlspecialchars($typeLabel) ?></h4>
				</div>
				<label class="toggle">
					<input
						type="checkbox"
						class="inapp-type-toggle"
						id="inAppType_<?= htmlspecialchars($typeKey) ?>"
						data-inapp-type="<?= htmlspecialchars($typeKey) ?>"
						checked
					>
					<span class="slider"></span>
				</label>
			</div>
		<?php endforeach; ?>
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
document.addEventListener('DOMContentLoaded', function(){
	const URLROOT = '<?= URLROOT ?>';
	const dndModal = document.getElementById('dndModal');
	const openDND = document.getElementById('openDND');
	const dndForm = document.getElementById('dndForm');

	const IN_APP_TYPES = [
		'welcome',
		'info',
		'warning',
		'alert',
		'message',
		'follow_request',
		'started_following',
		'new_message',
		'like',
		'comment',
		'event_update',
		'post_approval',
		'fundraiser_update',
		'system_announcement',
		'admin_message'
	];

	// Keep a local cache so missing UI controls don't accidentally overwrite saved values.
	let settingsCache = {
		email_enabled: 1,
		sound_enabled: 0,
		mentions_enabled: 1,
		followers_enabled: 1,
		engagement_enabled: 1,
		dnd_enabled: 0,
		dnd_start: null,
		dnd_end: null,
		dnd_days: null,
		in_app_disabled_types: []
	};

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

	// Auto-save handlers
	['emailNotif', 'soundNotif', 'catMentions', 'catFollowers', 'catEngagement'].forEach(checkboxId => {
		const checkbox = document.getElementById(checkboxId);
		if (checkbox) checkbox.addEventListener('change', handleToggleChange);
	});

	document.querySelectorAll('.inapp-type-toggle').forEach(checkbox => {
		checkbox.addEventListener('change', handleToggleChange);
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
				settingsCache = { ...settingsCache, ...settings };

				// Normalize in_app_disabled_types
				if (!Array.isArray(settingsCache.in_app_disabled_types)) {
					settingsCache.in_app_disabled_types = [];
				}

				// Set delivery channel toggles
				safeSetCheckbox('emailNotif', settings.email_enabled);
				safeSetCheckbox('soundNotif', settings.sound_enabled);

				// Set category toggles (if present in UI)
				safeSetCheckbox('catMentions', settings.mentions_enabled);
				safeSetCheckbox('catFollowers', settings.followers_enabled);
				safeSetCheckbox('catEngagement', settings.engagement_enabled);

				// Set per-type in-app toggles
				const disabled = new Set(settingsCache.in_app_disabled_types || []);
				IN_APP_TYPES.forEach(type => {
					const el = document.getElementById(`inAppType_${type}`);
					if (el) el.checked = !disabled.has(type);
				});

				// Set DND fields (if present in UI)
				const dndStartEl = document.getElementById('dndStart');
				const dndEndEl = document.getElementById('dndEnd');
				const dndDaysEl = document.getElementById('dndDays');
				if (dndStartEl && settings.dnd_start) dndStartEl.value = settings.dnd_start;
				if (dndEndEl && settings.dnd_end) dndEndEl.value = settings.dnd_end;
				if (dndDaysEl && settings.dnd_days) dndDaysEl.value = settings.dnd_days;
			}
		} catch (error) {
			console.error('Failed to load notification settings:', error);
		}
	}

	// Handle toggle checkbox changes
	async function handleToggleChange(e) {
		const checkbox = e.target;

		const next = collectSettingsFromUI();

		try {
			const response = await fetch(`${URLROOT}/settings/updateNotificationSettings`, {
				method: 'POST',
				headers: { 'Content-Type': 'application/json' },
				body: JSON.stringify(next)
			});

			const data = await response.json();

			if (data.success) {
				settingsCache = { ...settingsCache, ...next };
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

	function collectSettingsFromUI() {
		const out = { ...settingsCache };

		const emailEl = document.getElementById('emailNotif');
		if (emailEl) out.email_enabled = emailEl.checked ? 1 : 0;

		const soundEl = document.getElementById('soundNotif');
		if (soundEl) out.sound_enabled = soundEl.checked ? 1 : 0;

		const mentionsEl = document.getElementById('catMentions');
		if (mentionsEl) out.mentions_enabled = mentionsEl.checked ? 1 : 0;

		const followersEl = document.getElementById('catFollowers');
		if (followersEl) out.followers_enabled = followersEl.checked ? 1 : 0;

		const engagementEl = document.getElementById('catEngagement');
		if (engagementEl) out.engagement_enabled = engagementEl.checked ? 1 : 0;

		const disabled = [];
		IN_APP_TYPES.forEach(type => {
			const el = document.getElementById(`inAppType_${type}`);
			if (el && !el.checked) disabled.push(type);
		});
		out.in_app_disabled_types = disabled;

		// Don't change DND values here; handled by DND form.
		return out;
	}

	// Handle DND form submission
	async function handleDNDFormSubmit(e) {
		e.preventDefault();

		const dndStartEl = document.getElementById('dndStart');
		const dndEndEl = document.getElementById('dndEnd');
		const dndDaysEl = document.getElementById('dndDays');
		if (!dndStartEl || !dndEndEl || !dndDaysEl) {
			showNotification('DND settings UI is not available', 'error');
			return;
		}

		const dndStart = dndStartEl.value;
		const dndEnd = dndEndEl.value;
		const dndDays = dndDaysEl.value;

		if (!dndStart || !dndEnd) {
			showNotification('Please fill in all DND fields', 'error');
			return;
		}

		const settings = collectSettingsFromUI();
		settings.dnd_enabled = 1;
		settings.dnd_start = dndStart;
		settings.dnd_end = dndEnd;
		settings.dnd_days = dndDays;

		try {
			const response = await fetch(`${URLROOT}/settings/updateNotificationSettings`, {
				method: 'POST',
				headers: { 'Content-Type': 'application/json' },
				body: JSON.stringify(settings)
			});

			const data = await response.json();

			if (data.success) {
				settingsCache = { ...settingsCache, ...settings };
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
