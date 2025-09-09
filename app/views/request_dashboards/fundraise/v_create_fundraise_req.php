<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/req_dashboard/fundraise_req_create_styles.css">    

<?php ob_start(); ?>
<?php $styles = ob_get_clean(); ?>

<?php
    $sidebar_left = [
        ['label'=>'View All Fundraise Requests', 'url'=>'/fundraiser/all','active'=>false ,'icon'=>'list'],
        ['label'=>'Create Fundraise Request', 'url'=>'/fundraiser/request','active'=>true ,'icon'=>'plus-circle'],
    ]
?>

<?php ob_start(); ?>
<div class="formbody">
    <div class="signup-container">
        <div class="signup-header">
            <div class="title-section">
                <h1>Fundraising Request</h1>
                <p class="subtitle">Submit your fundraising project proposal</p>
            </div>
        </div>
        
        <!-- Progress Bar -->
        <div class="progress-container">
            <div class="progress-bar">
                <div class="progress-step active" id="step-1">1</div>
                <div class="progress-step" id="step-2">2</div>
                <div class="progress-step" id="step-3">3</div>
            </div>
            <div class="progress-labels">
                <div class="progress-label active">Basic Information</div>
                <div class="progress-label">Financial Details</div>
                <div class="progress-label">Content & Approval</div>
            </div>
        </div>

        <!-- Form Container -->
        <form id="fundraisingForm" method="post" action="#" enctype="multipart/form-data">
            
            <!-- Page 1: Basic Information -->
            <div class="form-page active" id="page-1">
                <div class="form-section">
                    <h2 class="section-title">Contact & Project Information</h2>
                    
                    <div class="form-group">
                        <label class="form-label" for="club_name">Club/Society Name:</label>
                        <input type="text" class="form-control" id="club_name" name="club_name" required>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label" for="contact_person">Contact Person/Project Coordinator:</label>
                        <input type="text" class="form-control" id="contact_person" name="contact_person" required>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label" for="position">Position in Club/Society:</label>
                        <input type="text" class="form-control" id="position" name="position" required>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label" for="email">Email:</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label" for="phone">Phone:</label>
                        <input type="tel" class="form-control" id="phone" name="phone" required>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label" for="project_title">Project Title:</label>
                        <input type="text" class="form-control" id="project_title" name="project_title" required>
                    </div>
                </div>
                
                <div class="form-section">
                    <div class="form-group">
                        <label class="form-label" for="objective">Purpose/Objective of Fundraising:</label>
                        <textarea class="form-control" id="objective" name="objective" required></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label" for="start_date">Proposed Start Date:</label>
                        <input type="date" class="form-control" id="start_date" name="start_date" required>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label" for="end_date">Proposed End Date:</label>
                        <input type="date" class="form-control" id="end_date" name="end_date" required>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Target Audience:</label>
                        <div class="checkbox-group">
                            <div class="checkbox-item">
                                <input type="checkbox" id="audience_alumni" name="audience[]" value="Alumni">
                                <label for="audience_alumni">Alumni</label>
                            </div>
                            <div class="checkbox-item">
                                <input type="checkbox" id="audience_students" name="audience[]" value="Students">
                                <label for="audience_students">Students</label>
                            </div>
                            <div class="checkbox-item">
                                <input type="checkbox" id="audience_staff" name="audience[]" value="Staff">
                                <label for="audience_staff">Staff</label>
                            </div>
                            <div class="checkbox-item">
                                <input type="checkbox" id="audience_public" name="audience[]" value="General Public">
                                <label for="audience_public">General Public</label>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="form-footer">
                    <div class="back-link">Back to <a href="#">Dashboard</a></div>
                    <button type="button" class="btn btn-next" onclick="nextPage(1)">Next</button>
                </div>
            </div>
            
            <!-- Page 2: Financial Details -->
            <div class="form-page" id="page-2">
                <div class="form-section">
                    <h2 class="section-title">Financial Information</h2>
                    
                    <div class="form-group">
                        <label class="form-label" for="amount_needed">Total Amount Needed (LKR):</label>
                        <input type="number" class="form-control" id="amount_needed" name="amount_needed" min="0" required>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label" for="min_contribution">Minimum Expected Contribution (per alumni, if applicable):</label>
                        <input type="number" class="form-control" id="min_contribution" name="min_contribution" min="0">
                        <p class="form-text">Leave blank if no minimum is required</p>
                    </div>
                </div>
                
                <div class="form-section">
                    <h2 class="section-title">Suggested Contribution Tiers</h2>
                    
                    <div class="form-group">
                        <label class="form-label" for="tier_1">Tier 1 (LKR):</label>
                        <input type="text" class="form-control" id="tier_1" name="tier_1" placeholder="e.g., 1,000 LKR - Basic Supporter">
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label" for="tier_2">Tier 2 (LKR):</label>
                        <input type="text" class="form-control" id="tier_2" name="tier_2" placeholder="e.g., 5,000 LKR - Silver Supporter">
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label" for="tier_3">Tier 3 (LKR):</label>
                        <input type="text" class="form-control" id="tier_3" name="tier_3" placeholder="e.g., 10,000 LKR - Gold Supporter">
                    </div>
                </div>
                
                <div class="form-section">
                    <h2 class="section-title">Fund Management</h2>
                    
                    <div class="form-group">
                        <label class="form-label" for="fund_manager">Who will manage the funds? (Name of Treasurer/Committee):</label>
                        <input type="text" class="form-control" id="fund_manager" name="fund_manager" required>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label" for="fund_recording">How will funds be recorded and reported?</label>
                        <textarea class="form-control" id="fund_recording" name="fund_recording" required></textarea>
                    </div>
                </div>
                
                <div class="form-section">
                    <h2 class="section-title">Bank Account Details (University-Linked/Official Account Only)</h2>
                    
                    <div class="form-group">
                        <label class="form-label" for="bank_name">Bank Name:</label>
                        <input type="text" class="form-control" id="bank_name" name="bank_name" required>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label" for="account_number">Account Number:</label>
                        <input type="text" class="form-control" id="account_number" name="account_number" required>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label" for="account_holder">Account Holder (University/Club/Society):</label>
                        <input type="text" class="form-control" id="account_holder" name="account_holder" required>
                    </div>
                </div>
                
                <div class="form-footer">
                    <div class="btn-group">
                        <button type="button" class="btn btn-previous" onclick="previousPage(2)">Previous</button>
                        <button type="button" class="btn" onclick="nextPage(2)">Next</button>
                    </div>
                </div>
            </div>
            
            <!-- Page 3: Content & Approval -->
            <div class="form-page" id="page-3">
                <div class="form-section">
                    <h2 class="section-title">Content for Alumni Website Post</h2>
                    
                    <div class="form-group">
                        <label class="form-label" for="headline">Short Title/Headline:</label>
                        <input type="text" class="form-control" id="headline" name="headline" required>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label" for="description">Detailed Project Description (for Alumni Website):</label>
                        <textarea class="form-control" id="description" name="description" style="height: 200px;" required></textarea>
                        <p class="form-text">Please provide a comprehensive description of your project, including its purpose, impact, and how funds will be used.</p>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label" for="project_poster">Project Poster:</label>
                        <div class="file-upload">
                            <label for="project_poster" class="upload-area" id="poster-upload-area">
                                <i>📁</i>
                                <span>Click to upload or drag and drop</span>
                                <p class="form-text">Recommended size: 1200 x 800 pixels, PNG or JPG format</p>
                            </label>
                            <input type="file" id="project_poster" name="project_poster" accept="image/*" hidden>
                        </div>
                    </div>
                </div>
                
                <div class="signature-section">
                    <h2 class="section-title">Approvals & Signatures</h2>
                    
                    <div class="signature-row">
                        <div class="signature-field">
                            <div class="form-group">
                                <label class="form-label" for="president_name">President/Head of Club/Society:</label>
                                <input type="text" class="form-control" id="president_name" name="president_name" required>
                            </div>
                            <div class="signature-line">
                                <span class="signature-label">Signature/Stamp</span>
                            </div>
                        </div>
                        
                        <div class="signature-field">
                            <div class="form-group">
                                <label class="form-label" for="signature_date">Date:</label>
                                <input type="date" class="form-control" id="signature_date" name="signature_date" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="signature-row">
                        <div class="signature-field">
                            <div class="form-group">
                                <label class="form-label" for="advisor_name">Faculty Advisor/Staff-in-Charge (if applicable):</label>
                                <input type="text" class="form-control" id="advisor_name" name="advisor_name">
                            </div>
                            <div class="signature-line">
                                <span class="signature-label">Signature/Stamp</span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="form-footer">
                    <div class="btn-group">
                        <button type="button" class="btn btn-previous" onclick="previousPage(3)">Previous</button>
                        <button type="submit" class="btn">Submit Request</button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <script>
    // Current page tracker
    let currentPage = 1;
    const totalPages = 3;
    
    // Function to navigate to the next page
    function nextPage(pageNum) {
        // Validate current page fields before proceeding
        if (!validatePage(pageNum)) {
            return false;
        }
        
        // Hide current page
        document.getElementById('page-' + pageNum).classList.remove('active');
        
        // Mark current page step as completed
        document.getElementById('step-' + pageNum).classList.remove('active');
        document.getElementById('step-' + pageNum).classList.add('completed');
        
        // Update current page
        currentPage = pageNum + 1;
        
        // Show next page
        document.getElementById('page-' + currentPage).classList.add('active');
        
        // Mark next page step as active
        document.getElementById('step-' + currentPage).classList.add('active');
        
        // Update progress labels
        updateProgressLabels();
        
        // Scroll to top of form
        window.scrollTo(0, 0);
        
        return true;
    }
    
    // Function to navigate to the previous page
    function previousPage(pageNum) {
        // Hide current page
        document.getElementById('page-' + pageNum).classList.remove('active');
        
        // Mark current page step as not active
        document.getElementById('step-' + pageNum).classList.remove('active');
        
        // Update current page
        currentPage = pageNum - 1;
        
        // Show previous page
        document.getElementById('page-' + currentPage).classList.add('active');
        
        // Mark previous page step as active
        document.getElementById('step-' + currentPage).classList.add('active');
        document.getElementById('step-' + currentPage).classList.remove('completed');
        
        // Update progress labels
        updateProgressLabels();
        
        // Scroll to top of form
        window.scrollTo(0, 0);
        
        return true;
    }
    
    // Function to update progress labels
    function updateProgressLabels() {
        // Remove active class from all labels
        const labels = document.querySelectorAll('.progress-label');
        labels.forEach(label => label.classList.remove('active'));
        
        // Add active class to current page label
        labels[currentPage - 1].classList.add('active');
    }
    
    // Form validation for each page
    function validatePage(pageNum) {
        let isValid = true;
        const page = document.getElementById('page-' + pageNum);
        
        // Find all required fields on current page
        const requiredFields = page.querySelectorAll('[required]');
        
        // Check each required field
        requiredFields.forEach(field => {
            if (!field.value) {
                field.style.borderColor = '#ef4444';
                isValid = false;
                
                // Add error message if it doesn't already exist
                const errorId = field.id + '-error';
                if (!document.getElementById(errorId)) {
                    const errorMsg = document.createElement('p');
                    errorMsg.id = errorId;
                    errorMsg.classList.add('form-text');
                    errorMsg.style.color = '#ef4444';
                    errorMsg.textContent = 'This field is required';
                    field.parentNode.appendChild(errorMsg);
                }
            } else {
                field.style.borderColor = '';
                
                // Remove error message if exists
                const errorMsg = document.getElementById(field.id + '-error');
                if (errorMsg) {
                    errorMsg.remove();
                }
            }
        });
        
        // For page 1, check if at least one target audience is selected
        if (pageNum === 1) {
            const audienceChecks = page.querySelectorAll('input[name="audience[]"]:checked');
            if (audienceChecks.length === 0) {
                const audienceGroup = page.querySelector('.checkbox-group');
                isValid = false;
                
                // Add error message if it doesn't already exist
                const errorId = 'audience-error';
                if (!document.getElementById(errorId)) {
                    const errorMsg = document.createElement('p');
                    errorMsg.id = errorId;
                    errorMsg.classList.add('form-text');
                    errorMsg.style.color = '#ef4444';
                    errorMsg.textContent = 'Please select at least one target audience';
                    audienceGroup.parentNode.appendChild(errorMsg);
                }
            } else {
                // Remove error message if exists
                const errorMsg = document.getElementById('audience-error');
                if (errorMsg) {
                    errorMsg.remove();
                }
            }
        }
        
        if (!isValid) {
            // Show a general error message at top of page
            const errorMsg = document.createElement('div');
            errorMsg.classList.add('error-message');
            errorMsg.textContent = 'Please fill in all required fields before proceeding.';
            
            // Insert at top of form if doesn't exist
            const existingError = page.querySelector('.error-message');
            if (!existingError) {
                page.insertBefore(errorMsg, page.firstChild);
                
                // Scroll to error message
                errorMsg.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        } else {
            // Remove general error message if exists
            const existingError = page.querySelector('.error-message');
            if (existingError) {
                existingError.remove();
            }
        }
        
        return isValid;
    }
    
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize progress labels
        updateProgressLabels();
        
        // Set up form submission
        const form = document.getElementById('fundraisingForm');
        form.addEventListener('submit', function(e) {
            // Validate last page before submission
            if (!validatePage(3)) {
                e.preventDefault();
                return false;
            }
            
            // In a real app, this would submit the form
            // For demo purposes, just show an alert
            e.preventDefault();
            alert('Form submitted successfully!');
            console.log('Form data would be submitted here.');
            
            // Log form data
            const formData = new FormData(form);
            for (const [key, value] of formData.entries()) {
                console.log(`${key}: ${value}`);
            }
        });
        
        // File upload preview for project poster
        const posterInput = document.getElementById('project_poster');
        const uploadArea = document.getElementById('poster-upload-area');
        
        posterInput.addEventListener('change', function(e) {
            if (this.files && this.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    uploadArea.style.backgroundImage = `url(${e.target.result})`;
                    uploadArea.style.backgroundSize = 'cover';
                    uploadArea.style.backgroundPosition = 'center';
                    uploadArea.querySelector('i').style.display = 'none';
                    uploadArea.querySelector('span').style.display = 'none';
                    uploadArea.querySelector('p').style.display = 'none';
                }
                reader.readAsDataURL(this.files[0]);
            }
        });
        
        // Drag and drop functionality for poster upload
        uploadArea.addEventListener('dragover', function(e) {
            e.preventDefault();
            this.style.backgroundColor = '#2a2a2a';
        });
        
        uploadArea.addEventListener('dragleave', function(e) {
            e.preventDefault();
            this.style.backgroundColor = '#1e1e1e';
        });
        
        uploadArea.addEventListener('drop', function(e) {
            e.preventDefault();
            this.style.backgroundColor = '#1e1e1e';
            
            if (e.dataTransfer.files && e.dataTransfer.files[0]) {
                posterInput.files = e.dataTransfer.files;
                
                // Trigger the change event manually
                const event = new Event('change');
                posterInput.dispatchEvent(event);
            }
        });
        
        // Close button functionality
        document.querySelector('.close-button').addEventListener('click', function(e) {
            e.preventDefault();
            if (confirm('Are you sure you want to cancel this fundraising request?')) {
                alert('Request cancelled. Redirecting to dashboard...');
                // In a real app, this would redirect to the dashboard
            }
        });
    });
    </script>
</div>
</html>

<?php $content = ob_get_clean(); ?>
<?php require APPROOT . '/views/request_dashboards/dashboard_layout.php';?>