<div class="account_content">
    <h2>Account Settings</h2>
    <p class="settings-description">Manage your account settings here.</p>
    
    <div class="settings-section">
        <h3>Profile Information</h3>
        <div class="section-divider"></div>
        <div class="settings-option">
            <div class="settings-option-details">
                <h4>Name</h4>
                <p>Update your name as it appears across GRADLINK</p>
            </div>
            <button class="settings-btn edit-name-btn">Edit</button>
        </div>
        
        <div class="settings-option">
            <div class="settings-option-details">
                <h4>Bio</h4>
                <p>Update your profile bio and description</p>
            </div>
            <button class="settings-btn edit-bio-btn">Edit</button>
        </div>
        
        <div class="settings-option">
            <div class="settings-option-details">
                <h4>Email Address</h4>
                <p>Update or change your email address</p>
            </div>
            <button class="settings-btn edit-email-btn">Edit</button>
        </div>
        
        <div class="settings-option">
            <div class="settings-option-details">
                <h4>Password</h4>
                <p>Update your password to keep your account secure</p>
            </div>
            <button class="settings-btn change-password-btn">Change</button>
        </div>
    </div>
    
    <div class="settings-section">
        <h3>Profile Analytics</h3>
        <div class="section-divider"></div>
        <div class="analytics-dashboard">
            <div class="analytics-card">
                <div class="analytics-value"><?= isset($userAnalytics) ? $userAnalytics['profile_views'] : '0' ?></div>
                <div class="analytics-label">Profile Views</div>
                <div class="analytics-trend up">+5% <span class="trend-icon">↑</span></div>
            </div>
            
            <div class="analytics-card">
                <div class="analytics-value"><?= isset($userAnalytics) ? $userAnalytics['post_engagement'] : '0' ?></div>
                <div class="analytics-label">Post Engagement</div>
                <div class="analytics-trend up">+12% <span class="trend-icon">↑</span></div>
            </div>
            
            <div class="analytics-card">
                <div class="analytics-value"><?= isset($userAnalytics) ? $userAnalytics['connections'] : '0' ?></div>
                <div class="analytics-label">Connections</div>
                <div class="analytics-trend">0% <span class="trend-icon">→</span></div>
            </div>
            
            <!-- <div class="analytics-card">
                <div class="analytics-value"><?= isset($userAnalytics) ? $userAnalytics['avg_response_time'] : '0' ?>h</div>
                <div class="analytics-label">Avg. Response Time</div>
                <div class="analytics-trend down">-8% <span class="trend-icon">↓</span></div>
            </div> -->
        </div>
        
        <div class="view-more-analytics">
            <a href="#" class="view-more-link">View Detailed Analytics</a>
        </div>
    </div>
    
    <div class="settings-section">
        <h3>Account Management</h3>
        <div class="section-divider"></div>
        <div class="settings-option">
            <div class="settings-option-details">
                <h4>Deactivate or Delete Account</h4>
                <p>Temporarily deactivate your account or deactivate and <br> auto-delete later</p>
            </div>
            <button class="settings-btn settings-btn-danger delete-account-btn">Manage</button>
        </div>
    </div>
    
    <!-- Edit Name Modal -->
    <div id="editNameModal" class="settings-modal">
        <div class="settings-modal-content">
            <div class="settings-modal-header">
                <h3>Edit Name</h3>
                <span class="settings-close-modal">&times;</span>
            </div>
            <div class="settings-modal-body">
                <form id="editNameForm">
                    <div class="form-group">
                        <label for="fullName">Full Name</label>
                        <input type="text" id="fullName" name="fullName" required>
                    </div>
                    <div class="form-group">
                        <label for="displayName">Display Name</label>
                        <input type="text" id="displayName" name="displayName">
                    </div>
                    <div class="form-actions">
                        <button type="button" class="settings-btn-secondary cancel-modal">Cancel</button>
                        <button type="submit" class="settings-btn">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Edit Bio Modal -->
    <div id="editBioModal" class="settings-modal">
        <div class="settings-modal-content">
            <div class="settings-modal-header">
                <h3>Edit Bio</h3>
                <span class="settings-close-modal">&times;</span>
            </div>
            <div class="settings-modal-body">
                <form id="editBioForm">
                    <div class="form-group">
                        <label for="bio">Bio</label>
                        <textarea id="bio" name="bio" rows="5"></textarea>
                    </div>
                    <div class="form-actions">
                        <button type="button" class="settings-btn-secondary cancel-modal">Cancel</button>
                        <button type="submit" class="settings-btn">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Edit Email Modal -->
    <div id="editEmailModal" class="settings-modal">
        <div class="settings-modal-content">
            <div class="settings-modal-header">
                <h3>Change Email Address</h3>
                <span class="settings-close-modal">&times;</span>
            </div>
            <div class="settings-modal-body">
                <form id="editEmailForm">
                    <div class="form-group">
                        <label for="currentEmail">Current Email</label>
                        <input type="email" id="currentEmail" disabled>
                    </div>
                    <div class="form-group">
                        <label for="newEmail">New Email</label>
                        <input type="email" id="newEmail" name="newEmail" required>
                    </div>
                    <div class="form-group">
                        <label for="emailPassword">Confirm with Password</label>
                        <input type="password" id="emailPassword" name="emailPassword" required>
                    </div>
                    <div class="form-actions">
                        <button type="button" class="settings-btn-secondary cancel-modal">Cancel</button>
                        <button type="submit" class="settings-btn">Update Email</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Change Password Modal -->
    <div id="changePasswordModal" class="settings-modal">
        <div class="settings-modal-content">
            <div class="settings-modal-header">
                <h3>Change Password</h3>
                <span class="settings-close-modal">&times;</span>
            </div>
            <div class="settings-modal-body">
                <form id="changePasswordForm">
                    <div class="form-group">
                        <label for="currentPassword">Current Password</label>
                        <input type="password" id="currentPassword" name="currentPassword" required>
                    </div>
                    <div class="form-group">
                        <label for="newPassword">New Password</label>
                        <input type="password" id="newPassword" name="newPassword" required>
                        <div class="password-strength-meter">
                            <div class="strength-bar"></div>
                        </div>
                        <p class="password-hint">Password must be at least 8 characters with a mix of letters, numbers, and symbols</p>
                    </div>
                    <div class="form-group">
                        <label for="confirmNewPassword">Confirm New Password</label>
                        <input type="password" id="confirmNewPassword" name="confirmNewPassword" required>
                    </div>
                    <div class="form-actions">
                        <button type="button" class="settings-btn-secondary cancel-modal">Cancel</button>
                        <button type="submit" class="settings-btn">Update Password</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Delete Account Modal -->
    <div id="deleteAccountModal" class="settings-modal">
        <div class="settings-modal-content">
            <div class="settings-modal-header">
                <h3>Deactivate / Delete Account</h3>
                <span class="settings-close-modal">&times;</span>
            </div>
            <div class="settings-modal-body">
                <div class="danger-message">
                    <div class="danger-icon">⚠️</div>
                    <p>Select what should happen to your account. Logging in again will reactivate your account before auto-deletion.</p>
                </div>
                <form id="deleteAccountForm">
                    <div class="form-group account-action-group">
                        <label>Choose account action</label>
                        <label class="account-action-option">
                            <input type="radio" name="accountAction" value="deactivate_only" required>
                            <span>Deactivate Account (30 days)</span>
                        </label>
                        <label class="account-action-option">
                            <input type="radio" name="accountAction" value="deactivate_and_delete" required>
                            <span>Deactivate and Delete Account (30 days if you do not log in)</span>
                    </div>
                    <div class="form-group">
                        <label for="deletionReason">Why are you deleting your account?</label>
                        <select id="deletionReason" name="deletionReason" required>
                            <option value="">Select a reason</option>
                            <option value="privacy_concerns">Privacy concerns</option>
                            <option value="created_new_account">Created a new account</option>
                            <option value="not_useful">Not finding GRADLINK useful</option>
                            <option value="data_concerns">Concerns about my data</option>
                            <option value="other">Other reason</option>
                        </select>
                    </div>
                    <div class="form-group" id="otherDeletionReasonGroup" style="display: none;">
                        <label for="otherDeletionReason">Please specify</label>
                        <textarea id="otherDeletionReason" name="otherDeletionReason" rows="3"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="deleteConfirmation">Type "CONFIRM" to proceed</label>
                        <input type="text" id="deleteConfirmation" name="deleteConfirmation" required>
                    </div>
                    <div class="form-group">
                        <label for="deletePassword">Enter your password to continue</label>
                        <input type="password" id="deletePassword" name="deletePassword" required>
                    </div>
                    <div class="form-actions">
                        <button type="button" class="settings-btn-secondary cancel-modal">Cancel</button>
                        <button type="submit" class="settings-btn-danger">Proceed</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Account Settings JavaScript -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const URLROOT = '<?= URLROOT ?>';
    let currentUser = null;
    
    // Load user data on page load
    loadUserData();
    
    // Modal functionality
    const modals = {
        editName: document.getElementById('editNameModal'),
        editBio: document.getElementById('editBioModal'),
        editEmail: document.getElementById('editEmailModal'),
        changePassword: document.getElementById('changePasswordModal'),
        deleteAccount: document.getElementById('deleteAccountModal')
    };
    
    const buttons = {
        editName: document.querySelector('.edit-name-btn'),
        editBio: document.querySelector('.edit-bio-btn'),
        editEmail: document.querySelector('.edit-email-btn'),
        changePassword: document.querySelector('.change-password-btn'),
        deleteAccount: document.querySelector('.delete-account-btn')
    };
    
    // Open modal functions
    buttons.editName.addEventListener('click', () => {
        if (currentUser) {
            document.getElementById('fullName').value = currentUser.name || '';
            document.getElementById('displayName').value = currentUser.display_name || '';
        }
        openModal(modals.editName);
    });
    buttons.editBio.addEventListener('click', () => {
        if (currentUser) {
            document.getElementById('bio').value = currentUser.bio || '';
        }
        openModal(modals.editBio);
    });
    buttons.editEmail.addEventListener('click', () => {
        if (currentUser) {
            document.getElementById('currentEmail').value = currentUser.email || '';
        }
        openModal(modals.editEmail);
    });
    buttons.changePassword.addEventListener('click', () => openModal(modals.changePassword));
    buttons.deleteAccount.addEventListener('click', () => openModal(modals.deleteAccount));
    
    // Close modal when clicking the X or Cancel button
    document.querySelectorAll('.settings-close-modal, .cancel-modal').forEach(element => {
        element.addEventListener('click', function() {
            const modal = this.closest('.settings-modal');
            closeModal(modal);
        });
    });
    
    // Close modal when clicking outside of it
    window.addEventListener('click', function(event) {
        Object.values(modals).forEach(modal => {
            if (event.target === modal) {
                closeModal(modal);
            }
        });
    });
    
    // Show "other" reason field when selected
    document.getElementById('deletionReason').addEventListener('change', function() {
        const otherDeletionReasonGroup = document.getElementById('otherDeletionReasonGroup');
        otherDeletionReasonGroup.style.display = this.value === 'other' ? 'block' : 'none';
    });
    
    // Password strength meter
    const passwordInput = document.getElementById('newPassword');
    const strengthBar = document.querySelector('.strength-bar');
    
    passwordInput.addEventListener('input', function() {
        const password = this.value;
        let strength = 0;
        
        if (password.length >= 8) strength += 25;
        if (password.match(/[a-z]+/)) strength += 25;
        if (password.match(/[A-Z]+/)) strength += 25;
        if (password.match(/[0-9]+/) || password.match(/[^a-zA-Z0-9]+/)) strength += 25;
        
        strengthBar.style.width = strength + '%';
        
        if (strength < 50) {
            strengthBar.style.backgroundColor = '#ff4d4d';
        } else if (strength < 75) {
            strengthBar.style.backgroundColor = '#ffa64d';
        } else if (strength < 100) {
            strengthBar.style.backgroundColor = '#ffff4d';
        } else {
            strengthBar.style.backgroundColor = '#4dff4d';
        }
    });
    
    // Form submission handlers
    document.getElementById('editNameForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        const submitBtn = this.querySelector('button[type="submit"]');
        submitBtn.disabled = true;
        submitBtn.textContent = 'Saving...';
        
        try {
            const response = await fetch(`${URLROOT}/settings/updateName`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    name: document.getElementById('fullName').value,
                    display_name: document.getElementById('displayName').value
                })
            });
            
            const data = await response.json();
            
            if (data.success) {
                showNotification('Name updated successfully!', 'success');
                closeModal(modals.editName);
                loadUserData();
            } else {
                showNotification(data.error || 'Failed to update name', 'error');
            }
        } catch (error) {
            showNotification('An error occurred', 'error');
        } finally {
            submitBtn.disabled = false;
            submitBtn.textContent = 'Save Changes';
        }
    });
    
    document.getElementById('editBioForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        const submitBtn = this.querySelector('button[type=\"submit\"]');
        submitBtn.disabled = true;
        submitBtn.textContent = 'Saving...';
        
        try {
            const response = await fetch(`${URLROOT}/settings/updateBio`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    bio: document.getElementById('bio').value
                })
            });
            
            const data = await response.json();
            
            if (data.success) {
                showNotification('Bio updated successfully!', 'success');
                closeModal(modals.editBio);
                loadUserData();
            } else {
                showNotification(data.error || 'Failed to update bio', 'error');
            }
        } catch (error) {
            showNotification('An error occurred', 'error');
        } finally {
            submitBtn.disabled = false;
            submitBtn.textContent = 'Save Changes';
        }
    });
    
    document.getElementById('editEmailForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        const submitBtn = this.querySelector('button[type=\"submit\"]');
        submitBtn.disabled = true;
        submitBtn.textContent = 'Updating...';
        
        try {
            const response = await fetch(`${URLROOT}/settings/updateEmail`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    current_email: document.getElementById('currentEmail').value,
                    new_email: document.getElementById('newEmail').value,
                    password: document.getElementById('emailPassword').value
                })
            });
            
            const data = await response.json();
            
            if (data.success) {
                showNotification('Email updated successfully!', 'success');
                closeModal(modals.editEmail);
                loadUserData();
            } else {
                showNotification(data.error || 'Failed to update email', 'error');
            }
        } catch (error) {
            showNotification('An error occurred', 'error');
        } finally {
            submitBtn.disabled = false;
            submitBtn.textContent = 'Update Email';
        }
    });
    
    document.getElementById('changePasswordForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        const submitBtn = this.querySelector('button[type=\"submit\"]');
        submitBtn.disabled = true;
        submitBtn.textContent = 'Updating...';
        
        try {
            const response = await fetch(`${URLROOT}/settings/changePassword`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    current_password: document.getElementById('currentPassword').value,
                    new_password: document.getElementById('newPassword').value,
                    confirm_password: document.getElementById('confirmNewPassword').value
                })
            });
            
            const data = await response.json();
            
            if (data.success) {
                showNotification('Password changed successfully!', 'success');
                closeModal(modals.changePassword);
                this.reset();
            } else {
                showNotification(data.error || 'Failed to change password', 'error');
            }
        } catch (error) {
            showNotification('An error occurred', 'error');
        } finally {
            submitBtn.disabled = false;
            submitBtn.textContent = 'Update Password';
        }
    });
    
    document.getElementById('deleteAccountForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        const submitBtn = this.querySelector('button[type=\"submit\"]');
        submitBtn.disabled = true;
        submitBtn.textContent = 'Deleting...';
        
        try {
            const response = await fetch(`${URLROOT}/settings/deleteAccount`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    password: document.getElementById('deletePassword').value,
                    confirmation: document.getElementById('deleteConfirmation').value,
                    action_type: (document.querySelector('input[name="accountAction"]:checked') || {}).value || '',
                    deletion_reason: document.getElementById('deletionReason').value,
                    other_deletion_reason: document.getElementById('otherDeletionReason').value
                })
            });
            
            const data = await response.json();
            
            if (data.success) {
                showNotification(data.message || 'Account action scheduled. Redirecting...', 'success');
                setTimeout(() => {
                    window.location.href = `${URLROOT}/auth`;
                }, 2000);
            } else {
                showNotification(data.error || 'Failed to delete account', 'error');
                submitBtn.disabled = false;
                submitBtn.textContent = 'Permanently Delete Account';
            }
        } catch (error) {
            showNotification('An error occurred', 'error');
            submitBtn.disabled = false;
            submitBtn.textContent = 'Permanently Delete Account';
        }
    });
    
    // Helper functions
    async function loadUserData() {
        try {
            const response = await fetch(`${URLROOT}/settings/getUserData`);
            const data = await response.json();
            
            if (data.success) {
                currentUser = data.user;
            }
        } catch (error) {
            console.error('Failed to load user data:', error);
        }
    }
    
    function openModal(modal) {
        if (modal) {
            modal.style.display = 'block';
        }
    }
    
    function closeModal(modal) {
        if (modal) {
            modal.style.display = 'none';
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
