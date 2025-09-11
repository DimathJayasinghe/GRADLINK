<?php require APPROOT . '/views/inc/header.php'; ?>
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/auth/login_signup_styles.css">

<div class="signup-container">
    <a href="<?php echo URLROOT;?>/events" class="close-button">&times;</a>
    
    <div class="signup-header">
        <div class="title-section">
            <h1>Event Request</h1>
            <p class="subtitle">Create a new event</p>
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

    <form class="signup-form" method="post" action="<?php echo URLROOT; ?>/events/request" enctype="multipart/form-data">
        <div class="form-grid">
            <!-- Left column -->
            <div class="form-column">
                <div class="form-group">
                    <input type="text" id="event_title" name="event_title" placeholder="Event Title" required>
                </div>
                
                <div class="form-group">
                    <input type="text" id="organizer" name="organizer" placeholder="Organizer (Club/Society Name)" required>
                </div>
                
                <div class="form-group">
                    <input type="date" id="event_date" name="event_date" placeholder="Date of Event" required>
                </div>
                
                <div class="form-group">
                    <input type="time" id="event_time" name="event_time" placeholder="Time of Event" required>
                </div>
            </div>
            
            <!-- Middle column -->
            <div class="form-column">
                <div class="form-group">
                    <input type="text" id="venue" name="venue" placeholder="Venue/Location" required>
                </div>
                
                <div class="form-group">
                    <div class="custom-select">
                        <div class="select-field">
                            <span>Event Type</span>
                            <div class="arrow-icon">▼</div>
                        </div>
                        <div class="dropdown-menu event-type-dropdown">
                            <div class="skill-option event-option" data-value="Workshop">Workshop</div>
                            <div class="skill-option event-option" data-value="Seminar">Seminar</div>
                            <div class="skill-option event-option" data-value="Competition">Competition</div>
                            <div class="skill-option event-option" data-value="Cultural Event">Cultural Event</div>
                            <div class="skill-option event-option" data-value="Fundraiser">Fundraiser</div>
                            <div class="skill-option event-option" data-value="Other">Other</div>
                        </div>
                        <select id="event_type" name="event_type" hidden required>
                            <option value="" disabled selected>Select Event Type</option>
                            <option value="Workshop">Workshop</option>
                            <option value="Seminar">Seminar</option>
                            <option value="Competition">Competition</option>
                            <option value="Cultural Event">Cultural Event</option>
                            <option value="Fundraiser">Fundraiser</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                </div>
                
                <div class="form-group bio-group">
                    <textarea id="description" name="description" placeholder="Event Description"></textarea>
                </div>
            </div>
            
            <!-- Right column (image upload) -->
            <div class="profile-upload">
                <label for="event_image" class="upload-area">
                    <span>Upload Event Image</span>
                    <input type="file" id="event_image" name="event_image" accept="image/*" hidden>
                </label>
            </div>
        </div>
        
        <div class="form-footer">
            <p class="back-link">Back to <a href="<?php echo URLROOT; ?>/events">Events</a></p>
            <button type="submit" class="next-btn">Request Event</button>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Close button functionality
    document.querySelector('.close-button').addEventListener('click', function(e) {
        e.preventDefault();
        window.location.href = '<?php echo URLROOT; ?>/events';
    });

    // Event image preview
    const imageInput = document.getElementById('event_image');
    const uploadArea = document.querySelector('.upload-area');
    
    imageInput.addEventListener('change', function(e) {
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
    
    // Event Type dropdown functionality
    const eventTypeField = document.querySelector('.custom-select .select-field');
    const eventTypeDropdown = document.querySelector('.event-type-dropdown');
    const eventOptions = document.querySelectorAll('.event-option');
    const eventTypeSelect = document.getElementById('event_type');
    
    // Toggle event type dropdown
    eventTypeField.addEventListener('click', function(e) {
        e.stopPropagation();
        eventTypeDropdown.classList.toggle('show');
    });
    
    // Handle event type selection
    eventOptions.forEach(option => {
        option.addEventListener('click', function(e) {
            e.stopPropagation();
            
            // Clear previous selection
            eventOptions.forEach(opt => opt.classList.remove('selected'));
            
            // Mark this option as selected
            this.classList.add('selected');
            
            // Update field text
            eventTypeField.querySelector('span').textContent = this.textContent;
            
            // Update hidden select
            eventTypeSelect.value = this.dataset.value;
            
            // Hide dropdown
            eventTypeDropdown.classList.remove('show');
        });
    });
    
    // Close dropdowns when clicking outside
    document.addEventListener('click', function() {
        eventTypeDropdown.classList.remove('show');
    });
    
    // Prevent dropdown from closing when clicking inside
    eventTypeDropdown.addEventListener('click', function(e) {
        e.stopPropagation();
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

/* Fix for description section height */
.bio-group {
    height: 155px; /* Added more height for event description */
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

/* Image upload section */
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

.back-link {
    color: var(--text);
}

.back-link a {
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