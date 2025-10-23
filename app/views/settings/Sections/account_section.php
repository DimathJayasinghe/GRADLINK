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
                <h4>Deactivate Account</h4>
                <p>Temporarily disable your GRADLINK account</p>
            </div>
            <button class="settings-btn settings-btn-secondary deactivate-account-btn">Deactivate</button>
        </div>
        
        <div class="settings-option">
            <div class="settings-option-details">
                <h4>Delete Account</h4>
                <p>Permanently delete your GRADLINK account and all data</p>
            </div>
            <button class="settings-btn settings-btn-danger delete-account-btn">Delete</button>
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
                        <label for="firstName">First Name</label>
                        <input type="text" id="firstName" name="firstName" value="<?= isset($user) ? $user->first_name : '' ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="lastName">Last Name</label>
                        <input type="text" id="lastName" name="lastName" value="<?= isset($user) ? $user->last_name : '' ?>" required>
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
                        <label for="headline">Headline/Title</label>
                        <input type="text" id="headline" name="headline" value="<?= isset($user) ? $user->headline : '' ?>">
                    </div>
                    <div class="form-group">
                        <label for="bio">Bio</label>
                        <textarea id="bio" name="bio" rows="5"><?= isset($user) ? $user->bio : '' ?></textarea>
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
                        <input type="email" id="currentEmail" value="<?= isset($user) ? $user->email : '' ?>" disabled>
                    </div>
                    <div class="form-group">
                        <label for="newEmail">New Email</label>
                        <input type="email" id="newEmail" name="newEmail" required>
                    </div>
                    <div class="form-group">
                        <label for="confirmPassword">Confirm with Password</label>
                        <input type="password" id="confirmPassword" name="confirmPassword" required>
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
    
    <!-- Deactivate Account Modal -->
    <div id="deactivateAccountModal" class="settings-modal">
        <div class="settings-modal-content">
            <div class="settings-modal-header">
                <h3>Deactivate Account</h3>
                <span class="settings-close-modal">&times;</span>
            </div>
            <div class="settings-modal-body">
                <div class="warning-message">
                    <i class="warning-icon">⚠️</i>
                    <p>Your account will be temporarily disabled. All your content will be hidden until you log back in.</p>
                </div>
                <form id="deactivateAccountForm">
                    <div class="form-group">
                        <label for="deactivationReason">Why are you deactivating?</label>
                        <select id="deactivationReason" name="deactivationReason" required>
                            <option value="">Select a reason</option>
                            <option value="taking_break">Taking a break</option>
                            <option value="privacy_concerns">Privacy concerns</option>
                            <option value="too_many_emails">Receiving too many emails</option>
                            <option value="not_useful">Not finding GRADLINK useful</option>
                            <option value="other">Other reason</option>
                        </select>
                    </div>
                    <div class="form-group" id="otherReasonGroup" style="display: none;">
                        <label for="otherReason">Please specify</label>
                        <textarea id="otherReason" name="otherReason" rows="3"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="deactivatePassword">Enter your password to continue</label>
                        <input type="password" id="deactivatePassword" name="deactivatePassword" required>
                    </div>
                    <div class="form-actions">
                        <button type="button" class="settings-btn-secondary cancel-modal">Cancel</button>
                        <button type="submit" class="settings-btn-danger">Deactivate Account</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Delete Account Modal -->
    <div id="deleteAccountModal" class="settings-modal">
        <div class="settings-modal-content">
            <div class="settings-modal-header">
                <h3>Delete Account</h3>
                <span class="settings-close-modal">&times;</span>
            </div>
            <div class="settings-modal-body">
                <div class="danger-message">
                    <i class="danger-icon">⚠️</i>
                    <p>This action <strong>cannot be undone</strong>. Your account and all associated data will be permanently deleted.</p>
                </div>
                <form id="deleteAccountForm">
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
                        <label for="deleteConfirmation">Type "DELETE" to confirm</label>
                        <input type="text" id="deleteConfirmation" name="deleteConfirmation" required>
                    </div>
                    <div class="form-group">
                        <label for="deletePassword">Enter your password to continue</label>
                        <input type="password" id="deletePassword" name="deletePassword" required>
                    </div>
                    <div class="form-actions">
                        <button type="button" class="settings-btn-secondary cancel-modal">Cancel</button>
                        <button type="submit" class="settings-btn-danger">Permanently Delete Account</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Account Settings JavaScript -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Modal functionality
    const modals = {
        editName: document.getElementById('editNameModal'),
        editBio: document.getElementById('editBioModal'),
        editEmail: document.getElementById('editEmailModal'),
        changePassword: document.getElementById('changePasswordModal'),
        deactivateAccount: document.getElementById('deactivateAccountModal'),
        deleteAccount: document.getElementById('deleteAccountModal')
    };
    
    const buttons = {
        editName: document.querySelector('.edit-name-btn'),
        editBio: document.querySelector('.edit-bio-btn'),
        editEmail: document.querySelector('.edit-email-btn'),
        changePassword: document.querySelector('.change-password-btn'),
        deactivateAccount: document.querySelector('.deactivate-account-btn'),
        deleteAccount: document.querySelector('.delete-account-btn')
    };
    
    // Open modal functions
    buttons.editName.addEventListener('click', () => openModal(modals.editName));
    buttons.editBio.addEventListener('click', () => openModal(modals.editBio));
    buttons.editEmail.addEventListener('click', () => openModal(modals.editEmail));
    buttons.changePassword.addEventListener('click', () => openModal(modals.changePassword));
    buttons.deactivateAccount.addEventListener('click', () => openModal(modals.deactivateAccount));
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
    document.getElementById('deactivationReason').addEventListener('change', function() {
        const otherReasonGroup = document.getElementById('otherReasonGroup');
        otherReasonGroup.style.display = this.value === 'other' ? 'block' : 'none';
    });
    
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
    
    // Form submission handling (for demonstration only)
    document.querySelectorAll('form').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // In a real app, you would send these form data to the server
            // For now, we'll just simulate success with an alert
            alert('Form submitted successfully! In a real app, this would be sent to the server.');
            
            // Close the modal
            const modal = this.closest('.settings-modal');
            closeModal(modal);
        });
    });
    
    // Helper functions
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
});
</script>
