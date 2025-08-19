<?php require APPROOT . '/views/inc/header.php'; ?>
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/auth/login_signup_styles.css">

<?php
// Import the skills data
$skills = require APPROOT . '/data/skills_data.php';
?>

<div class="signup-container">
    <a href="<?php echo URLROOT;?>/auth" class="close-button">&times;</a>
    
    <div class="signup-header">
        <div class="title-section">
            <h1>Sign up</h1>
            <p class="subtitle">Alumni - Member</p>
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

    <form class="signup-form" method="post" action="<?php echo URLROOT; ?>/signup/alumni" enctype="multipart/form-data">
        <div class="form-grid">
            <!-- Left column -->
            <div class="form-column">
                <div class="form-group">
                    <input type="text" id="full_name" name="full_name" placeholder="Name" required
                        value="<?php echo htmlspecialchars($data['full_name'] ?? ''); ?>">
                </div>
                
                <div class="form-group">
                    <input type="email" id="email" name="email" placeholder="Email" required
                        value="<?php echo htmlspecialchars($data['email'] ?? ''); ?>">
                </div>
                
                <div class="form-group">
                    <input type="password" id="password" name="password" placeholder="Password" required>
                </div>
                
                <div class="form-group">
                    <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirm Password" required>
                </div>
                
                <div class="form-group">
                    <div class="custom-select">
                        <div class="select-field">
                            <span>Add Skills</span>
                            <div class="arrow-icon">▼</div>
                        </div>
                        <div class="dropdown-menu">
                            <?php foreach($skills as $skill): ?>
                                <div class="skill-option" data-value="<?php echo $skill['id']; ?>"><?php echo $skill['name']; ?></div>
                            <?php endforeach; ?>
                        </div>
                        <select id="skills" name="skills[]" multiple hidden>
                            <?php foreach($skills as $skill): ?>
                                <option value="<?php echo $skill['id']; ?>"><?php echo $skill['name']; ?></option>
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
                    <div class="custom-select">
                        <div class="select-field">
                            <span>Batch No</span>
                            <div class="arrow-icon">▼</div>
                        </div>
                        <div class="dropdown-menu batch-dropdown">
                            <?php for($i = 1; $i <= 22; $i++): ?>
                                <div class="skill-option batch-option" data-value="<?php echo $i; ?>"><?php echo $i; ?></div>
                            <?php endfor; ?>
                        </div>
                        <select id="batch_no" name="graduation_year" hidden required>
                            <option value="" disabled selected>Select Batch</option>
                            <?php for($i = 1; $i <= 22; $i++): ?>
                                <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>
                </div>
                
                <div class="form-group">
                    <input type="text" id="nic" name="nic" placeholder="NIC" 
                        value="<?php echo htmlspecialchars($data['nic'] ?? ''); ?>">
                </div>
                
                <div class="form-group">
                    <input type="text" id="display_name" name="display_name" placeholder="Display Name" 
                        value="<?php echo htmlspecialchars($data['display_name'] ?? ''); ?>">
                </div>
                
                <div class="form-group bio-group">
                    <textarea id="bio" name="bio" placeholder="Add bio"><?php echo htmlspecialchars($data['bio'] ?? ''); ?></textarea>
                </div>
            </div>
            
            <!-- Right column (profile upload) - now square -->
            <div class="profile-upload">
                <label for="profile_image" class="upload-area">
                    <span>Upload Profile Pic</span>
                    <input type="file" id="profile_image" name="profile_image" accept="image/*" hidden>
                </label>
            </div>
        </div>
        
        <div class="form-footer">
            <p class="login-link">Already have an account? <a href="<?php echo URLROOT; ?>/login/alumni">Login</a></p>
            <button type="submit" class="next-btn">Signup</button>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Close button functionality
    document.querySelector('.close-button').addEventListener('click', function(e) {
        e.preventDefault();
        window.location.href = '<?php echo URLROOT; ?>/auth';
    });

    // Profile image preview
    const profileInput = document.getElementById('profile_image');
    const uploadArea = document.querySelector('.upload-area');
    
    profileInput.addEventListener('change', function(e) {
        if (this.files && this.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                uploadArea.style.backgroundImage = `url(${e.target.result})`;
                uploadArea.style.backgroundSize = 'cover';
                uploadArea.style.backgroundPosition = 'center';
                uploadArea.querySelector('span').style.display = 'none';
            }
            reader.readAsDataURL(this.files[0]);
        }
    });
    
    // Skills dropdown functionality
    const skillsField = document.querySelector('.form-column:first-child .custom-select .select-field');
    const skillsDropdown = document.querySelector('.form-column:first-child .dropdown-menu');
    const skillOptions = document.querySelectorAll('.form-column:first-child .skill-option');
    const skillsSelect = document.getElementById('skills');
    const skillTags = document.querySelector('.skills-tags');
    
    // Initialize hidden select options for skills
    skillOptions.forEach(option => {
        const value = option.dataset.value;
        const opt = document.createElement('option');
        opt.value = value;
        opt.textContent = option.textContent;
        skillsSelect.appendChild(opt);
    });
    
    // Toggle skills dropdown
    skillsField.addEventListener('click', function(e) {
        e.stopPropagation();
        skillsDropdown.classList.toggle('show');
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

/* Close button - fixed positioning */
.close-button {
    position: absolute;
    top: 1rem;
    right: 1rem;
    background: none;
    border: none;
    font-size: 2rem;
    cursor: pointer;
    color: var(--text);
    text-decoration: none;
    z-index: 10;
}

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
    height: 125px; /* Match height of skills section (55px + 15px + ~55px for tags) */
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
    box-shadow: 0 4px 8px rgba(0,0,0,0.3);
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
    background-color: rgba(255,255,255,0.05);
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

<?php require APPROOT . '/views/inc/footer.php'; ?>