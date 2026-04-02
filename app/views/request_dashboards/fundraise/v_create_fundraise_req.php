
<?php ob_start(); ?>
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/req_dashboard/fundraise_req_create_styles.css">
<style>
    .tag-input-wrapper { position: relative; }
    .tag-suggestions { position: absolute; top: 100%; left: 0; right: 0; background: #111518; border: 1px solid #2f2f2f; border-radius: 8px; margin-top: 4px; padding: 6px 0; max-height: 220px; overflow-y: auto; z-index: 5; display: none; }
    .tag-suggestion { width: 100%; text-align: left; padding: 8px 12px; background: transparent; color: #f5f5f5; border: 0; cursor: pointer; }
    .tag-suggestion:hover { background: #1f1f1f; }
    .tag-suggestion.status { cursor: default; opacity: 0.8; }
    .tagged-members { display: flex; flex-wrap: wrap; gap: 8px; margin-top: 8px; }
    .tagged-member { display: inline-flex; align-items: center; gap: 6px; padding: 6px 10px; background: #1a1a1a; border: 1px solid #2f2f2f; border-radius: 999px; color: #e5e7eb; font-size: 0.95rem; }
    .tag-remove { background: none; border: 0; color: #f87171; cursor: pointer; font-size: 0.9rem; line-height: 1; padding: 2px 6px; border-radius: 4px; }
    .tag-remove:hover { color: #ef4444; background: #262626; }
</style>

<?php $styles = ob_get_clean(); ?>

<?php
    $sidebar_left = [
        ['label'=>'View All Fundraise Requests', 'url'=>'/fundraiser/all','active'=>false ,'icon'=>'list'],
        ['label'=>'View my Fundraise Requests', 'url'=>'/fundraiser/myrequests','active'=>false, 'icon'=>'user'],
        ['label'=>'Create Fundraise Request', 'url'=>'/fundraiser/request','active'=>true ,'icon'=>'plus-circle'],
    ]
?>

<?php ob_start(); ?>
<div class="formbody">
    <div class="signup-container">
        <div class="signup-header">
            <div class="title-section">
                <h2>Create New Request</h2>
            </div>
        </div>
        
        <!-- Progress Bar -->
        <div class="progress-container">
            <div class="progress-bar">
                <div class="progress-step active" id="step-1">1</div>
                <div class="progress-step" id="step-2">2</div>
                <div class="progress-step" id="step-3">3</div>
                <div class="progress-step" id="step-4">4</div>
            </div>
            <div class="progress-labels">
                <div class="progress-label active">Contact Details</div>
                <div class="progress-label">Campaign Details</div>
                <div class="progress-label">Financial Details</div>
                <div class="progress-label">Content & Approval</div>
            </div>
        </div>

        <!-- Form Container -->
        <form id="fundraisingForm" method="post" action="<?php echo URLROOT . "/fundraiser/create" ?>" enctype="multipart/form-data">
            
            <!-- Page 1: Basic Information -->
            <div class="form-page active" id="page-1">
                <div class="form-section">
                    <h2 class="section-title">Contact & Project Information</h2>
                    
                    <div class="form-group">
                        <label class="form-label" for="club_name">Club/Society/Team Name:</label>
                        <input type="text" class="form-control" id="club_name" name="club_name" required>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label" for="contact_person">Contact Person/Project Coordinator:</label>
                        <input type="text" class="form-control" id="contact_person" name="contact_person" value="<?php echo $_SESSION['user_name']?>" disabled>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label" for="position">Position in Club/Society/Team:</label>
                        <input type="text" class="form-control" id="position" name="position" required>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label" for="email">Email:</label>
                        <input type="email" class="form-control" id="email" name="email" value="dimathjaya@gmail.com" disabled>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="phone">Applicant Phone Number:</label>
                        <input type="tel" class="form-control" id="phone" name="phone" required>
                    </div>
                </div>
                <div class="form-section">
                    <h3 class="section-title">Team Details (If applicable)</h3>
                    <div class="form-group">
                        <label class="form-label" for="team_mention_input">Mention Team Members (optional):</label>
                        <div class="tag-input-wrapper">
                            <input type="text" class="form-control" id="team_mention_input" name="team_mention_input" placeholder="Type @username to search" autocomplete="off" oninput="getTags(this)">
                            <div id="tag-suggestions" class="tag-suggestions"></div>
                        </div>
                        <p class="form-text">Start with @ to search users, then click a result to add them as a team member.</p>
                        <div id="team-members" class="tagged-members"></div>
                        <div id="team-members-hidden"></div>
                    </div>
                </div>
                
                
                <div class="form-footer">
                    <button type="button" class="btn btn-next" onclick="nextPage(1)">Next</button>
                </div>
            </div>


            <div class="form-page" id="page-2"> 
                <div class="form-section">
                    <div class="form-group">
                        <label class="form-label" for="project_title">Project Title:</label>
                        <input type="text" class="form-control" id="project_title" name="project_title" required>
                    </div>
                    <div class="form-section">
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
                                <p class="form-text">Recommended: <strong>1200 × 400 px</strong> (3:1 ratio, banner style)</p>
                                <p class="form-text" style="font-size: 0.75rem; color: var(--text-muted);">PNG or JPG format, max 5MB</p>
                            </label>
                            <input type="file" id="project_poster" name="project_poster" accept="image/*" hidden>
                        </div>
                    </div>
                </div>
                </div>
                <div class="form-footer">
                    <div class="btn-group">
                        <button type="button" class="btn btn-previous" onclick="previousPage(2)">Previous</button>
                        <button type="button" class="btn" onclick="nextPage(2)">Next</button>
                    </div>
                </div>
            </div>
            

            <!-- Page 3: Financial Details -->
            <div class="form-page" id="page-3">
                <div class="form-section">
                    <h2 class="section-title">Financial Information</h2>
                    
                    <div class="form-group">
                        <label class="form-label" for="amount_needed">Total Amount Needed (LKR):</label>
                        <input type="number" class="form-control" id="amount_needed" name="amount_needed" min="0" required>
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
                    
                </div>
                
                
                <div class="form-section">
                    <h2 class="section-title">Fund Management</h2>
                    
                    <div class="form-group">
                        <label class="form-label" for="fund_manager">Who will manage the funds? (Name of Treasurer/Committee):</label>
                        <input type="text" class="form-control" id="fund_manager" name="fund_manager" required>
                        <label class="form-label" for="fund_manager_contact">Contact Number:</label>
                        <input type="tel" class="form-control" id="fund_manager_contact" name="fund_manager_contact" required>
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
                        <label class="form-label" for="branch">Branch:</label>
                        <input type="text" class="form-control" id="branch" name="branch" required>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label" for="account_holder">Account Holder (University/Club/Society):</label>
                        <input type="text" class="form-control" id="account_holder" name="account_holder" required>
                    </div>
                </div>
                
                <div class="form-footer">
                    <div class="btn-group">
                        <button type="button" class="btn btn-previous" onclick="previousPage(3)">Previous</button>
                        <button type="button" class="btn" onclick="nextPage(3)">Next</button>
                    </div>
                </div>
            </div>
            
            <!-- Page 4: Content & Approval -->
            <div class="form-page" id="page-4">
                
                
                <div class="inchange-section">
                    <h2 class="section-title">Approval Authority</h2>
                    <div class="inchange-row">
                        <div class="inchange-field">
                            <div class="form-group">
                                <label class="form-label" for="advisor_name">Lecture in charge of Club/Society (If applicable)</label>
                                <div class="tag-input-wrapper">
                                    <input type="text" class="form-control" id="advisor_name" name="advisor_name" placeholder="Type @ to search" autocomplete="off" oninput="getTags(this)" onchange="getTags(this)">
                                    <div id="advisor-suggestions" class="tag-suggestions"></div>
                                </div>
                                <input type="hidden" id="advisor_id" name="advisor_id">
                                <p class="form-text">Pick a lecturer to lock their user id into the request.</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="form-footer">
                    <div class="btn-group">
                        <button type="button" class="btn btn-previous" onclick="previousPage(4)">Previous</button>
                        <button type="submit" class="btn">Submit Request</button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <script>
    // Current page tracker
    let currentPage = 1;
    const totalPages = 4;

    const TAG_ENDPOINT = '<?php echo URLROOT; ?>/fundraiser/getAvailableUsers';

    const teamMembers = new Map();
    let advisorId = '';

    const tagState = {
        ensureBox(inputEl) {
            const explicitId = inputEl.dataset.suggestionId || inputEl.getAttribute('data-suggestion-id');
            let box = explicitId ? document.getElementById(explicitId) : null;
            if (!box) {
                box = inputEl.parentNode.querySelector('.tag-suggestions');
            }
            if (!box) {
                box = document.createElement('div');
                box.className = 'tag-suggestions';
                inputEl.parentNode.appendChild(box);
            }
            box.style.display = 'block';
            const parent = inputEl.parentNode;
            if (parent && window.getComputedStyle(parent).position === 'static') {
                parent.style.position = 'relative';
            }
            return box;
        },
        hide(box) {
            if (box) {
                box.style.display = 'none';
                box.innerHTML = '';
            }
        }
    };

    function renderTeamMembers() {
        const list = document.getElementById('team-members');
        const hidden = document.getElementById('team-members-hidden');
        if (!list || !hidden) { return; }

        list.innerHTML = '';
        hidden.innerHTML = '';

        teamMembers.forEach(user => {
            const pill = document.createElement('span');
            pill.className = 'tagged-member';
            pill.textContent = user.name || user.email || `User ${user.id}`;

            const removeBtn = document.createElement('button');
            removeBtn.type = 'button';
            removeBtn.className = 'tag-remove';
            removeBtn.textContent = '×';
            removeBtn.addEventListener('click', () => {
                teamMembers.delete(user.id);
                renderTeamMembers();
            });

            pill.appendChild(removeBtn);
            list.appendChild(pill);

            const hiddenInput = document.createElement('input');
            hiddenInput.type = 'hidden';
            hiddenInput.name = 'team_members[]';
            hiddenInput.value = user.id;
            hidden.appendChild(hiddenInput);
        });
    }

    function addAdvisor(user) {
        const advisorInput = document.getElementById('advisor_name');
        const advisorHidden = document.getElementById('advisor_id');
        const displayName = user.name || user.email || `User ${user.id}`;
        if (advisorInput) { advisorInput.value = displayName; }
        if (advisorHidden) { advisorHidden.value = user.id; }
        advisorId = user.id;
    }

    function selectSuggestion(user, inputEl, box) {
        if (!inputEl) { return; }
        if (inputEl.id === 'team_mention_input') {
            if (!teamMembers.has(user.id)) {
                teamMembers.set(user.id, user);
                renderTeamMembers();
            }
            inputEl.value = '';
        } else if (inputEl.id === 'advisor_name') {
            addAdvisor(user);
        } else {
            const displayName = user.name || user.email || `User ${user.id}`;
            inputEl.value = displayName;
        }
        tagState.hide(box);
    }

    async function getTags(inputEl) {
        if (!inputEl) { return; }
        const rawValue = inputEl.value || '';
        const match = rawValue.match(/@([\w.\-_]{1,50})$/);
        if (!match || match[1].length < 2) {
            tagState.hide(tagState.ensureBox(inputEl));
            return;
        }

        const query = match[1];
        const box = tagState.ensureBox(inputEl);
        box.innerHTML = '<div class="tag-suggestion status">Searching…</div>';

        try {
            const res = await fetch(`${TAG_ENDPOINT}?search=${encodeURIComponent(query)}`);
            const data = await res.json();
            if (!data.success || !Array.isArray(data.users) || data.users.length === 0) {
                box.innerHTML = '<div class="tag-suggestion status">No users found</div>';
                return;
            }

            box.innerHTML = '';
            data.users.forEach(user => {
                const btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'tag-suggestion';
                btn.textContent = user.name;
                btn.addEventListener('click', () => selectSuggestion(user, inputEl, box));
                box.appendChild(btn);
            });
        } catch (err) {
            console.error('Tag lookup failed', err);
            box.innerHTML = '<div class="tag-suggestion status">Unable to load users</div>';
        }
    }

    function copyRequesterName(checkbox, targetFieldId) {
        const requesterName = document.getElementById('contact_person').value;
        const targetField = document.getElementById(targetFieldId);
        
        if (checkbox.checked) {
            targetField.value = requesterName;
        } else {
            targetField.value = '';
        }
    }
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

        // Close suggestion dropdowns when clicking outside
        document.addEventListener('click', function(e) {
            const boxes = document.querySelectorAll('.tag-suggestions');
            boxes.forEach(box => {
                const wrapper = box.closest('.tag-input-wrapper') || box.parentNode;
                if (box.style.display === 'block' && wrapper && !wrapper.contains(e.target)) {
                    tagState.hide(box);
                }
            });
        });

        // Initialize team members list UI
        renderTeamMembers();
        const teamInput = document.getElementById('team_mention_input');
        if (teamInput) {
            teamInput.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    tagState.hide(tagState.ensureBox(teamInput));
                }
            });
        }
        
        
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
    });
    </script>
</div>
</html>

<?php $content = ob_get_clean(); ?>
<?php require APPROOT . '/views/request_dashboards/request_dashboard_layout_adapter.php';?>