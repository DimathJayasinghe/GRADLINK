<div class="account_content">
	<h2>Privacy & Safety</h2>
	<p class="settings-description">Control your privacy and security preferences.</p>

	<div class="settings-section">
		<h3>Privacy Controls</h3>
		<div class="section-divider"></div>

		<div class="settings-option">
			<div class="settings-option-details">
				<h4>Profile Visibility</h4>
				<p>Choose who can see your profile and activity</p>
			</div>
			<div>
				<select id="profileVisibility" class="select-inline">
					<option value="public">Public</option>
					<option value="connections">Connections only</option>
					<option value="private">Only me</option>
				</select>
			</div>
		</div>

		<div class="settings-option">
			<div class="settings-option-details">
				<h4>Search Engine Indexing</h4>
				<p>Allow search engines to link to your profile</p>
			</div>
			<label class="toggle">
				<input type="checkbox" id="searchIndexing" checked>
				<span class="slider"></span>
			</label>
		</div>

		<div class="settings-option">
			<div class="settings-option-details">
				<h4>Show Online Status</h4>
				<p>Display when you're active on GRADLINK</p>
			</div>
			<label class="toggle">
				<input type="checkbox" id="onlineStatus" checked>
				<span class="slider"></span>
			</label>
		</div>
	</div>

	<div class="settings-section">
		<h3>Security</h3>
		<div class="section-divider"></div>

		<div class="settings-option">
			<div class="settings-option-details">
				<h4>Two-Factor Authentication (2FA)</h4>
				<p>Add an extra layer of security to your account</p>
			</div>
			<button class="settings-btn" id="openTwoFA">Set up</button>
		</div>

		<div class="settings-option">
			<div class="settings-option-details">
				<h4>Login Alerts</h4>
				<p>Get notified when your account is accessed from a new device</p>
			</div>
			<label class="toggle">
				<input type="checkbox" id="loginAlerts" checked>
				<span class="slider"></span>
			</label>
		</div>
	</div>

	<div class="settings-section">
		<h3>Blocked Users</h3>
		<div class="section-divider"></div>
		<ul class="simple-list" id="blockedUsersList">
			<li>
				<span>No blocked users</span>
			</li>
		</ul>
	</div>

	<div class="settings-section">
		<h3>Active Sessions</h3>
		<div class="section-divider"></div>
		<ul class="simple-list">
			<li>
				<div>
					<strong>Current Device</strong>
					<p style="margin: 4px 0 0; color: var(--muted); font-size: 0.9rem;">Windows • Chrome • Colombo, LK</p>
				</div>
				<span style="color: var(--success); font-weight: 600;">Active</span>
			</li>
			<li>
				<div>
					<strong>iPhone</strong>
					<p style="margin: 4px 0 0; color: var(--muted); font-size: 0.9rem;">iOS App • Galle, LK</p>
				</div>
				<button class="settings-btn-secondary" id="logoutThis">Log out</button>
			</li>
		</ul>
		<div class="form-actions" style="justify-content: flex-start;">
			<button class="settings-btn-danger" id="logoutOthers">Log out of all other sessions</button>
		</div>
	</div>

	<!-- Two Factor Modal -->
	<div id="twoFAModal" class="settings-modal">
		<div class="settings-modal-content">
			<div class="settings-modal-header">
				<h3>Enable Two-Factor Authentication</h3>
				<span class="settings-close-modal">&times;</span>
			</div>
			<div class="settings-modal-body">
				<form id="twoFAForm">
					<div class="form-group">
						<label>Choose a method</label>
						<select id="twoFAMethod" required>
							<option value="app" selected>Authenticator App</option>
							<option value="sms">SMS (Text message)</option>
						</select>
					</div>
					<div class="form-group" id="smsPhoneGroup" style="display:none;">
						<label for="twoFAPhone">Phone Number</label>
						<input type="tel" id="twoFAPhone" placeholder="e.g. +94 7X XXX XXXX">
					</div>
					<div class="form-group">
						<label>How it works</label>
						<p style="color: var(--muted); font-size: 0.9rem;">After enabling, you'll enter a code from your chosen method each time you log in.</p>
					</div>
					<div class="form-actions">
						<button type="button" class="settings-btn-secondary cancel-modal">Cancel</button>
						<button type="submit" class="settings-btn">Enable 2FA</button>
					</div>
				</form>
			</div>
		</div>
	</div>

	<!-- Logout Others Modal -->
	<div id="logoutOthersModal" class="settings-modal">
		<div class="settings-modal-content">
			<div class="settings-modal-header">
				<h3>Log out of other sessions</h3>
				<span class="settings-close-modal">&times;</span>
			</div>
			<div class="settings-modal-body">
				<p class="settings-description">This will sign you out on all devices except the one you're currently using.</p>
				<form id="logoutOthersForm">
					<div class="form-group">
						<label for="confirmLogoutPassword">Enter your password to confirm</label>
						<input type="password" id="confirmLogoutPassword" required>
					</div>
					<div class="form-actions">
						<button type="button" class="settings-btn-secondary cancel-modal">Cancel</button>
						<button type="submit" class="settings-btn-danger">Log out others</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
	const twoFAModal = document.getElementById('twoFAModal');
	const logoutOthersModal = document.getElementById('logoutOthersModal');

	const openTwoFA = document.getElementById('openTwoFA');
	const logoutOthers = document.getElementById('logoutOthers');

	if (openTwoFA) openTwoFA.addEventListener('click', () => openModal(twoFAModal));
	if (logoutOthers) logoutOthers.addEventListener('click', () => openModal(logoutOthersModal));

	document.querySelectorAll('.settings-close-modal, .cancel-modal').forEach(el => {
		el.addEventListener('click', function(){ closeModal(this.closest('.settings-modal')); });
	});

	window.addEventListener('click', function(event) {
		[twoFAModal, logoutOthersModal].forEach(modal => {
			if (event.target === modal) { closeModal(modal); }
		});
	});

	const methodSelect = document.getElementById('twoFAMethod');
	const smsPhoneGroup = document.getElementById('smsPhoneGroup');
	if (methodSelect) {
		methodSelect.addEventListener('change', function(){
			smsPhoneGroup.style.display = this.value === 'sms' ? 'block' : 'none';
		});
	}

	document.querySelectorAll('#twoFAForm, #logoutOthersForm').forEach(form => {
		form.addEventListener('submit', function(e){
			e.preventDefault();
			alert('Settings saved. This is a demo submission.');
			closeModal(this.closest('.settings-modal'));
		});
	});

	function openModal(modal){ if (modal) modal.style.display = 'block'; }
	function closeModal(modal){ if (modal) modal.style.display = 'none'; }
});
</script>
