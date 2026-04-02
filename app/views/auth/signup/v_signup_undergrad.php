<?php require APPROOT . '/views/inc/header.php'; ?>
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/auth/login_signup_styles.css">

<?php
// Import the skills data
$skills = require APPROOT . '/data/skills_data.php';
?>
<style>
    /* Container styles */
    .back-button {
        display: flex;
        justify-content: center;
        align-items: center;
        border: 1px solid var(--border);
        padding: 10px;
        text-decoration: none;
        border-radius: 5px;
        color: wheat;
        width: fit-content;
    }
</style>

<div class="signup-container">
    <a href="<?php echo URLROOT; ?>" class="back-button" aria-label="Back to Gradlink home">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M15 18l-6-6 6-6" />
        </svg>
        Back
    </a>

    <div class="signup-header">
        <div class="title-section">
            <h1>Sign up</h1>
            <p class="subtitle">Undergraduate</p>
        </div>
        <div class="logo-container">
            <img src="<?php echo URLROOT; ?>/img/logo_white.png" alt="GRADLINK" class="logo-img">
            <div class="logo-text">GRADLINK</div>
        </div>
    </div>

    <?php
    if (isset($data['errors']) && !empty($data['errors'])) {
        foreach ($data['errors'] as $error) {
            echo "<p class='error-message'>$error</p>";
        }
    }
    ?>

    <form class="signup-form" method="post" action="<?php echo URLROOT; ?>/signup/undergrad" enctype="multipart/form-data">
        <div class="form-grid">
            <!-- Left column -->
            <div class="form-column">
                <div class="form-group">
                    <input type="text" id="full_name" name="full_name" placeholder="Name" required
                        value="<?php echo htmlspecialchars($data['full_name'] ?? ''); ?>">
                </div>

                <div class="form-group email-group">
                    <input type="email" id="email" name="email" placeholder="Student Email" required
                        value="<?php echo htmlspecialchars($data['email'] ?? ''); ?>"
                        pattern="^[0-9]{4}(?:[cC][sS]|[iI][sS])[0-9]{3}@stu\.ucsc\.cmb\.ac\.lk$"
                        title="Use your student email (e.g., 20XXcsXXX@stu.ucsc.cmb.ac.lk)">
                    <button type="button" id="send_otp_btn" class="otp-btn">Send OTP</button>
                </div>

                <div class="form-group otp-section" id="otp_section" style="display: none;">
                    <div class="otp-input-group">
                        <input type="text" id="otp_input" placeholder="Enter 6-digit OTP" maxlength="6" pattern="\d{6}">
                        <button type="button" id="verify_otp_btn" class="otp-btn verify-btn">Verify</button>
                    </div>
                </div>

                <div id="otp_status" class="otp-status"></div>

                <div id="email_verified_badge" class="verified-badge" style="display: none;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14" />
                        <polyline points="22 4 12 14.01 9 11.01" />
                    </svg>
                    Email Verified
                </div>

                <input type="hidden" id="email_verified" name="email_verified" value="0">

                <div class="form-group">
                    <input type="password" id="password" name="password" placeholder="Password" required minlength="6">
                </div>

                <div class="form-group">
                    <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirm Password" required minlength="6">
                </div>

                <!-- Gender moved above Skills -->
                <div class="form-group">
                    <div class="gender-group" role="radiogroup" aria-label="Gender">
                        <span class="gender-label">Gender:</span>
                        <label class="gender-option"><input type="radio" name="gender" value="male" <?php echo (isset($data['gender']) && $data['gender'] === 'male') ? 'checked' : ''; ?> required> Male</label>
                        <label class="gender-option"><input type="radio" name="gender" value="female" <?php echo (isset($data['gender']) && $data['gender'] === 'female') ? 'checked' : ''; ?> required> Female</label>
                    </div>
                </div>

                <div class="form-group">
                    <div class="custom-select">
                        <div class="select-field">
                            <span>Add Skills</span>
                            <div class="arrow-icon">▼</div>
                        </div>
                        <div class="dropdown-menu">
                            <?php foreach ($skills as $skill): ?>
                                <div class="skill-option" data-value="<?php echo $skill['id'] ?? strtoupper(substr($skill['name'], 0, strpos($skill['name'], ' ') ?: strlen($skill['name']))); ?>">
                                    <?php echo $skill['name']; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <select id="skills" name="skills[]" multiple hidden>
                            <?php foreach ($skills as $skill): ?>
                                <?php $id = $skill['id'] ?? strtoupper(substr($skill['name'], 0, strpos($skill['name'], ' ') ?: strlen($skill['name']))); ?>
                                <option value="<?php echo $id; ?>"><?php echo $skill['name']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="skills-tags">
                </div>
            </div>

            <!-- Middle column -->
            <div class="form-column">
                <div class="form-group">
                    <input type="text" id="student_id" name="student_id" placeholder="Student ID"
                        value="<?php echo htmlspecialchars($data['student_id'] ?? ''); ?>" required
                        pattern="^\d{4}/(?:[cC][sS]|[iI][sS])/\d{3}$" title="Format: YYYY/cs/XXX or YYYY/is/XXX (e.g., 2021/cs/123)">
                </div>

                <div class="form-group">
                    <div class="custom-select">
                        <div class="select-field">
                            <span>Batch No</span>
                            <div class="arrow-icon">▼</div>
                        </div>
                        <div class="dropdown-menu batch-dropdown">
                            <?php for ($i = 1; $i <= 22; $i++): ?>
                                <div class="skill-option batch-option" data-value="<?php echo $i; ?>"><?php echo $i; ?></div>
                            <?php endfor; ?>
                        </div>
                        <select id="batch_no" name="batch_no" hidden required>
                            <option value="" disabled selected>Select Batch</option>
                            <?php for ($i = 1; $i <= 22; $i++): ?>
                                <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <input type="text" id="nic" name="nic" placeholder="NIC"
                        value="<?php echo htmlspecialchars($data['nic'] ?? ''); ?>"
                        pattern="^\d{12}$" title="12-digit NIC (e.g., 200012345678)">
                </div>

                <div class="form-group">
                    <input type="text" id="display_name" name="display_name" placeholder="Display Name"
                        value="<?php echo htmlspecialchars($data['display_name'] ?? ''); ?>">
                </div>



                <div class="form-group bio-group">
                    <textarea id="bio" name="bio" placeholder="Add bio"><?php echo htmlspecialchars($data['bio'] ?? ''); ?></textarea>
                </div>
            </div>

            <!-- Right column (profile upload) -->
            <div class="profile-upload">
                <label for="profile_image" class="upload-area">
                    <span>Upload Profile Pic</span>
                    <input type="file" id="profile_image" name="profile_image" accept="image/*" hidden>
                </label>
            </div>
        </div>

        <div class="form-footer">
            <p class="login-link">Already have an account? <a href="<?php echo URLROOT; ?>/login/undergrad">Login</a></p>
            <button type="submit" class="next-btn">Signup</button>
        </div>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Back button is a normal link; no JS needed

        // Profile image preview with size validation
        const profileInput = document.getElementById('profile_image');
        const uploadArea = document.querySelector('.upload-area');
        const signupBtn = document.querySelector('.next-btn');
        const MAX_FILE_SIZE = 2 * 1024 * 1024; // 2MB in bytes

        // OTP Elements
        const emailInput = document.getElementById('email');
        const sendOtpBtn = document.getElementById('send_otp_btn');
        const otpSection = document.getElementById('otp_section');
        const otpInput = document.getElementById('otp_input');
        const verifyOtpBtn = document.getElementById('verify_otp_btn');
        const otpStatus = document.getElementById('otp_status');
        const emailVerifiedBadge = document.getElementById('email_verified_badge');
        const emailVerifiedInput = document.getElementById('email_verified');

        let otpCooldown = 0;
        let cooldownInterval = null;
        let isEmailVerified = false;

        // Initially disable signup button until email is verified
        signupBtn.disabled = true;
        signupBtn.style.opacity = '0.5';
        signupBtn.style.cursor = 'not-allowed';

        // Email validation pattern for student email
        const studentEmailPattern = /^[0-9]{4}(?:cs|is)[0-9]{3}@stu\.ucsc\.cmb\.ac\.lk$/i;

        // Send OTP handler
        sendOtpBtn.addEventListener('click', async function() {
            const email = emailInput.value.trim();

            if (!email) {
                emailInput.focus();
                showOtpStatus('Please enter your email address', 'error');
                return;
            }

            if (!studentEmailPattern.test(email)) {
                emailInput.focus();
                showOtpStatus('Please enter a valid student email (e.g., 20XXcsXXX@stu.ucsc.cmb.ac.lk)', 'error');
                return;
            }

            if (otpCooldown > 0) {
                showOtpStatus(`Please wait ${otpCooldown} seconds before requesting a new OTP`, 'error');
                return;
            }

            // Disable button and show loading
            sendOtpBtn.disabled = true;
            sendOtpBtn.textContent = 'Sending...';

            try {
                const formData = new FormData();
                formData.append('email', email);
                formData.append('purpose', 'signup');

                const response = await fetch('<?php echo URLROOT; ?>/signup/sendOTP', {
                    method: 'POST',
                    body: formData
                });

                const responseText = await response.text();
                console.log('Response:', responseText); // Debug log

                let data;
                try {
                    data = JSON.parse(responseText);
                } catch (parseError) {
                    console.error('JSON Parse Error:', parseError);
                    console.error('Raw response:', responseText);
                    showOtpStatus('Server error. Check console for details.', 'error');
                    sendOtpBtn.disabled = false;
                    sendOtpBtn.textContent = 'Send OTP';
                    return;
                }

                if (data.success) {
                    otpSection.style.display = 'block';
                    otpInput.focus();
                    showOtpStatus('OTP sent to your email. Check your inbox.', 'success');
                    startCooldown(60);
                    emailInput.readOnly = true;
                } else {
                    showOtpStatus(data.message || 'Failed to send OTP', 'error');
                    sendOtpBtn.disabled = false;
                    sendOtpBtn.textContent = 'Send OTP';
                }
            } catch (error) {
                console.error('Fetch error:', error);
                showOtpStatus('Network error. Please try again.', 'error');
                sendOtpBtn.disabled = false;
                sendOtpBtn.textContent = 'Send OTP';
            }
        });

        // Verify OTP handler
        verifyOtpBtn.addEventListener('click', async function() {
            const email = emailInput.value.trim();
            const otp = otpInput.value.trim();

            if (!otp) {
                otpInput.focus();
                showOtpStatus('Please enter the OTP', 'error');
                return;
            }

            if (!/^\d{6}$/.test(otp)) {
                otpInput.focus();
                showOtpStatus('OTP must be 6 digits', 'error');
                return;
            }

            verifyOtpBtn.disabled = true;
            verifyOtpBtn.textContent = 'Verifying...';

            try {
                const formData = new FormData();
                formData.append('email', email);
                formData.append('otp', otp);
                formData.append('purpose', 'signup');

                const response = await fetch('<?php echo URLROOT; ?>/signup/verifyOTP', {
                    method: 'POST',
                    body: formData
                });

                const data = await response.json();

                if (data.success) {
                    isEmailVerified = true;
                    emailVerifiedInput.value = '1';
                    otpSection.style.display = 'none';
                    emailVerifiedBadge.style.display = 'flex';
                    sendOtpBtn.style.display = 'none';
                    showOtpStatus('', 'success');

                    // Enable signup button
                    signupBtn.disabled = false;
                    signupBtn.style.opacity = '1';
                    signupBtn.style.cursor = 'pointer';

                    // Clear cooldown
                    if (cooldownInterval) {
                        clearInterval(cooldownInterval);
                        cooldownInterval = null;
                    }
                } else {
                    showOtpStatus(data.message || 'Verification failed', 'error');
                    verifyOtpBtn.disabled = false;
                    verifyOtpBtn.textContent = 'Verify';
                }
            } catch (error) {
                showOtpStatus('Network error. Please try again.', 'error');
                verifyOtpBtn.disabled = false;
                verifyOtpBtn.textContent = 'Verify';
            }
        });

        // Helper function to show OTP status messages
        function showOtpStatus(message, type) {
            otpStatus.textContent = message;
            otpStatus.className = 'otp-status ' + type;
        }

        // Cooldown timer
        function startCooldown(seconds) {
            otpCooldown = seconds;
            sendOtpBtn.textContent = `Resend (${otpCooldown}s)`;

            cooldownInterval = setInterval(() => {
                otpCooldown--;
                if (otpCooldown <= 0) {
                    clearInterval(cooldownInterval);
                    cooldownInterval = null;
                    sendOtpBtn.disabled = false;
                    sendOtpBtn.textContent = 'Resend OTP';
                } else {
                    sendOtpBtn.textContent = `Resend (${otpCooldown}s)`;
                }
            }, 1000);
        }

        // Prevent form submission if email not verified
        document.querySelector('.signup-form').addEventListener('submit', function(e) {
            if (!isEmailVerified) {
                e.preventDefault();
                showOtpStatus('Please verify your email before signing up', 'error');
                otpSection.style.display = 'block';
            }
        });

        // Create or get error message element
        let imageErrorDiv = document.querySelector('.image-error-message');
        if (!imageErrorDiv) {
            imageErrorDiv = document.createElement('div');
            imageErrorDiv.className = 'image-error-message';
            imageErrorDiv.style.cssText = 'background-color: rgba(220, 38, 38, 0.1); color: #ef4444; padding: 10px; border-radius: 4px; margin-top: 10px; border-left: 3px solid #dc2626; display: none;';
            uploadArea.parentElement.appendChild(imageErrorDiv);
        }


        profileInput.addEventListener('change', function(e) {
            if (this.files && this.files[0]) {
                const file = this.files[0];

                // Validate file size
                if (file.size > MAX_FILE_SIZE) {
                    const sizeMB = (file.size / (1024 * 1024)).toFixed(2);
                    imageErrorDiv.textContent = `Image size (${sizeMB}MB) exceeds the maximum allowed size of 2MB. Please choose a smaller image.`;
                    imageErrorDiv.style.display = 'block';
                    signupBtn.disabled = true;
                    signupBtn.style.opacity = '0.5';
                    signupBtn.style.cursor = 'not-allowed';

                    // Clear the input
                    this.value = '';
                    return;
                }

                // Clear any previous error
                imageErrorDiv.style.display = 'none';
                signupBtn.disabled = false;
                signupBtn.style.opacity = '1';
                signupBtn.style.cursor = 'pointer';

                const reader = new FileReader();
                reader.onload = function(e) {
                    uploadArea.style.backgroundImage = `url(${e.target.result})`;
                    uploadArea.style.backgroundSize = 'cover';
                    uploadArea.style.backgroundPosition = 'center';
                    uploadArea.querySelector('span').style.display = 'none';
                }
                reader.readAsDataURL(file);
            }
        });

        // Skills dropdown functionality
        const skillsField = document.querySelector('.form-column:first-child .custom-select .select-field');
        const skillsDropdown = document.querySelector('.form-column:first-child .dropdown-menu');
        const skillOptions = document.querySelectorAll('.form-column:first-child .skill-option');
        const skillsSelect = document.getElementById('skills');
        const skillTags = document.querySelector('.skills-tags');

        // Toggle skills dropdown
        skillsField.addEventListener('click', function(e) {
            e.stopPropagation();
            skillsDropdown.classList.toggle('show');

            // Close batch dropdown when opening skills dropdown
            document.querySelector('.batch-dropdown').classList.remove('show');
        });

        // Batch dropdown functionality
        const batchField = document.querySelector('.form-column:nth-child(2) .custom-select .select-field');
        const batchDropdown = document.querySelector('.batch-dropdown');
        const batchOptions = document.querySelectorAll('.batch-option');
        const batchSelect = document.getElementById('batch_no');

        // Toggle batch dropdown
        batchField.addEventListener('click', function(e) {
            e.stopPropagation();
            batchDropdown.classList.toggle('show');

            // Close skills dropdown when opening batch dropdown
            skillsDropdown.classList.remove('show');
        });

        // Handle batch selection
        batchOptions.forEach(option => {
            option.addEventListener('click', function(e) {
                e.stopPropagation();

                // Clear previous selection
                batchOptions.forEach(opt => opt.classList.remove('selected'));

                // Mark this option as selected
                this.classList.add('selected');

                // Update field text
                batchField.querySelector('span').textContent = this.textContent;

                // Update hidden select
                batchSelect.value = this.dataset.value;

                // Hide dropdown
                batchDropdown.classList.remove('show');
            });
        });

        // Close dropdowns when clicking outside
        document.addEventListener('click', function() {
            skillsDropdown.classList.remove('show');
            batchDropdown.classList.remove('show');
        });

        // Prevent dropdown from closing when clicking inside
        skillsDropdown.addEventListener('click', function(e) {
            e.stopPropagation();
        });

        batchDropdown.addEventListener('click', function(e) {
            e.stopPropagation();
        });

        // Handle skill selection
        skillOptions.forEach(option => {
            option.addEventListener('click', function() {
                const value = this.dataset.value;
                this.classList.toggle('selected');

                // Update hidden select
                const optElement = Array.from(skillsSelect.options).find(opt => opt.value === value);
                if (optElement) {
                    optElement.selected = this.classList.contains('selected');
                }

                // Update skill tags
                updateSkillTags();
            });
        });

        function updateSkillTags() {
            skillTags.innerHTML = '';
            Array.from(skillsSelect.selectedOptions).forEach(option => {
                const tag = document.createElement('span');
                tag.className = 'skill-tag';
                tag.innerHTML = `#${option.textContent}<span class="remove-skill">&times;</span>`;

                // Add event listener to remove button
                tag.querySelector('.remove-skill').addEventListener('click', function(e) {
                    e.stopPropagation();
                    // Find and deselect the option
                    const optElement = Array.from(skillsSelect.options).find(opt => opt.value === option.value);
                    if (optElement) {
                        optElement.selected = false;
                    }

                    // Find and deselect the dropdown option
                    const dropdownOption = Array.from(skillOptions).find(opt => opt.dataset.value === option.value);
                    if (dropdownOption) {
                        dropdownOption.classList.remove('selected');
                    }

                    // Update skill tags
                    updateSkillTags();
                });

                skillTags.appendChild(tag);
            });
        }

        // Handle initial remove buttons for default tags
        document.querySelectorAll('.remove-skill').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.stopPropagation();
                this.parentElement.remove();
            });
        });
    });
</script>

<style>
    /* Reset and base styles */
    * {
        box-sizing: border-box;
        margin: 0;
        padding: 0;
    }

    body {
        background-color: var(--bg);
        font-family: 'Poppins', sans-serif;
        color: var(--text);
    }

    /* Container styles */
    .signup-container {
        max-width: 1100px;
        margin: 0 auto;
        padding: 2rem;
        position: relative;
        background-color: var(--bg);
    }

    /* back-button styles are in login_signup_styles.css */

    /* Header section */
    .signup-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 2rem;
        position: relative;
    }

    .title-section h1 {
        font-size: 2.5rem;
        margin-bottom: 0.25rem;
        font-weight: 600;
        color: var(--text);
    }

    .subtitle {
        color: var(--muted);
    }

    /* Logo container with text */
    .logo-container {
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    .logo-img {
        height: 80px;
        margin-bottom: 5px;
    }

    .logo-text {
        font-weight: bold;
        color: var(--text);
        font-size: 1.2rem;
        letter-spacing: 1px;
    }

    /* Form grid layout */
    .form-grid {
        display: grid;
        grid-template-columns: 1fr 1fr auto;
        grid-gap: 15px;
    }

    .form-column {
        display: flex;
        flex-direction: column;
        gap: 15px;
    }

    /* Form controls */
    .form-group {
        position: relative;
    }

    .form-group input,
    .form-group textarea,
    .select-field {
        width: 100%;
        height: 55px;
        padding: 0 15px;
        border: none;
        background-color: var(--input);
        border-radius: 8px;
        font-family: 'Poppins', sans-serif;
        font-size: 0.95rem;
        color: var(--text);
    }

    /* Fix for bio section height */
    .bio-group {
        height: 125px;
        /* Match height of skills section (55px + 15px + ~55px for tags) */
    }

    .form-group textarea {
        height: 100%;
        padding-top: 15px;
        resize: none;
    }

    /* Custom select styles */
    .custom-select {
        position: relative;
    }

    .select-field {
        display: flex;
        justify-content: space-between;
        align-items: center;
        cursor: pointer;
    }

    .arrow-icon {
        font-size: 0.8rem;
        color: var(--text);
    }

    .dropdown-menu {
        position: absolute;
        top: 100%;
        left: 0;
        width: 100%;
        background-color: var(--card);
        border-radius: 8px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
        margin-top: 5px;
        z-index: 100;
        display: none;
        max-height: 200px;
        overflow-y: auto;
    }

    .dropdown-menu.show {
        display: block;
    }

    .skill-option {
        padding: 12px 15px;
        cursor: pointer;
        border-bottom: 1px solid var(--border);
    }

    .skill-option:last-child {
        border-bottom: none;
    }

    .skill-option:hover {
        background-color: rgba(255, 255, 255, 0.05);
    }

    .skill-option.selected {
        background-color: rgba(158, 212, 220, 0.1);
        color: var(--link);
    }

    /* Skills tags */
    .skills-tags {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-top: 5px;
    }

    .skill-tag {
        background-color: var(--card);
        color: var(--text);
        padding: 5px 12px;
        border-radius: 30px;
        font-size: 0.9rem;
        display: inline-flex;
        align-items: center;
    }

    /* Remove skill button */
    .remove-skill {
        margin-left: 6px;
        font-size: 1.1rem;
        line-height: 1;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        opacity: 0.7;
        transition: opacity 0.2s;
    }

    .remove-skill:hover {
        opacity: 1;
    }

    /* Profile upload section */
    .profile-upload {
        width: 220px;
        height: 220px;
    }

    .upload-area {
        display: flex;
        align-items: center;
        justify-content: center;
        height: 100%;
        width: 100%;
        background-color: var(--input);
        border: 2px dashed var(--muted);
        border-radius: 8px;
        cursor: pointer;
        text-align: center;
        color: var(--muted);
    }

    /* Footer section */
    .form-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 2rem;
    }

    .login-link {
        color: var(--text);
    }

    .login-link a {
        color: var(--link);
        text-decoration: none;
        font-weight: 600;
    }

    .next-btn {
        background-color: var(--btn);
        color: var(--btn-text);
        border: none;
        padding: 12px 30px;
        border-radius: 4px;
        cursor: pointer;
        font-family: 'Poppins', sans-serif;
    }

    /* Error messages */
    .error-message {
        background-color: rgba(220, 38, 38, 0.1);
        color: #ef4444;
        padding: 10px;
        border-radius: 4px;
        margin-bottom: 15px;
        border-left: 3px solid #dc2626;
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .form-grid {
            grid-template-columns: 1fr 1fr;
        }

        .profile-upload {
            grid-column: span 2;
            width: 100%;
            max-width: 220px;
            margin: 0 auto;
        }
    }

    @media (max-width: 576px) {
        .form-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<style>
    /* Gender compact styles (match alumni) */
    .gender-group {
        display: flex;
        gap: 10px;
        align-items: center;
    }

    .gender-group .gender-label {
        font-size: 0.85rem;
        color: var(--muted);
    }

    .gender-group .gender-option {
        font-size: 0.85rem;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .gender-group input[type="radio"] {
        transform: scale(0.9);
        margin-right: 0;
    }
</style>

<style>
    /* OTP Verification Styles */
    .email-group {
        display: flex;
        gap: 10px;
        align-items: center;
    }

    .email-group input[type="email"] {
        flex: 1;
    }

    .otp-btn {
        background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
        color: white;
        border: none;
        padding: 12px 20px;
        border-radius: 8px;
        cursor: pointer;
        font-family: 'Poppins', sans-serif;
        font-size: 0.9rem;
        font-weight: 500;
        transition: all 0.3s ease;
        white-space: nowrap;
    }

    .otp-btn:hover:not(:disabled) {
        background: linear-gradient(135deg, #4338ca 0%, #6d28d9 100%);
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3);
    }

    .otp-btn:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }

    .otp-section {
        margin-top: -5px;
    }

    .otp-input-group {
        display: flex;
        gap: 10px;
        align-items: center;
    }

    .otp-input-group input {
        flex: 1;
        height: 55px;
        padding: 0 15px;
        border: none;
        background-color: var(--input);
        border-radius: 8px;
        font-family: 'Poppins', sans-serif;
        font-size: 1.1rem;
        letter-spacing: 3px;
        text-align: center;
        color: var(--text);
    }

    .otp-input-group input:focus {
        outline: 2px solid #4f46e5;
    }

    .verify-btn {
        background: linear-gradient(135deg, #059669 0%, #10b981 100%);
    }

    .verify-btn:hover:not(:disabled) {
        background: linear-gradient(135deg, #047857 0%, #059669 100%);
        box-shadow: 0 4px 12px rgba(5, 150, 105, 0.3);
    }

    .otp-status {
        margin-top: 8px;
        font-size: 0.85rem;
        padding: 8px 12px;
        border-radius: 6px;
    }

    .otp-status.error {
        background-color: rgba(220, 38, 38, 0.1);
        color: #ef4444;
        border-left: 3px solid #dc2626;
    }

    .otp-status.success {
        background-color: rgba(5, 150, 105, 0.1);
        color: #10b981;
        border-left: 3px solid #059669;
    }

    .otp-status:empty {
        display: none;
    }

    .verified-badge {
        display: flex;
        align-items: center;
        gap: 6px;
        background: linear-gradient(135deg, rgba(5, 150, 105, 0.1) 0%, rgba(16, 185, 129, 0.1) 100%);
        color: #10b981;
        padding: 10px 16px;
        border-radius: 8px;
        font-size: 0.9rem;
        font-weight: 500;
        border: 1px solid rgba(16, 185, 129, 0.3);
    }

    .verified-badge svg {
        stroke: #10b981;
    }
</style>

<?php require APPROOT . '/views/inc/footer.php'; ?>