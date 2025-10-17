<?php ob_start()?>
<title>Alumni Profile</title>
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/style.css">
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/color-pallate.css">
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/components/postCardStyles.css">
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/profile_styles.css"> <!-- Import profile specific styles -->
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/mainfeed_styles.css"> <!-- Import main feed styles -->

<?php $styles = ob_get_clean();?>
<?php
    $notifications = [
        (object)[
            'type' => 'like',
            'user' => 'Alice',
            'content' => ' liked your post.',
            'time' => '2h ago',
            'userImg' => URLROOT . '/media/profile/alice.jpg'
        ],
        (object)[
            'type' => 'follow',
            'user' => 'Bob',
            'content' => ' started following you.',
            'time' => '3h ago',
            'userImg' => URLROOT . '/media/profile/bob.jpg'
        ]
    ];
    $isOwner = isset($_SESSION['user_id']) && isset($data['userDetails']->id) && $_SESSION['user_id'] == $data['userDetails']->id;
    ?>
 <?php ob_start() ?>
    <?php
    $leftside_buttons = [
        ['icon' => 'home', 'label' => 'Home', 'onclick' => "window.location.href='" . URLROOT . "/mainfeed'" ],
        ['icon' => 'search', 'label' => 'Explore', 'onclick' => "window.location.href='" . URLROOT . "/explore'"],
        ['icon' => 'bell', 'label' => 'Notifications', 'onclick' => "NotificationModal()", 'require' => APPROOT . '/views/inc/commponents/notification_pop_up.php', 'notifications' => $notifications],
        ['icon' => 'envelope', 'label' => 'Messages', 'onclick' => "window.location.href='" . URLROOT . "/messages'"],
        ['icon' => 'user', 'label' => 'Profile' , 'onclick' => "window.location.href='" . URLROOT . "/profile?userid=".$_SESSION['user_id'] . "'",'active' => true],
        // icon for fundraiser
        ['icon' => 'hand-holding-heart', 'label' => 'Fundraisers', 'onclick' => "window.location.href='" . URLROOT . "/fundraiser'"],
        //icon for post requests
        ['icon' => 'clipboard-list', 'label' => 'Post Requests', 'onclick' => "window.location.href='" . URLROOT . "/postrequest/'"],
        ['icon' => 'calendar-alt', 'label' => 'Calender', 'onclick' => "window.location.href='" . URLROOT . "/calender'"],
        ['icon' => 'cog', 'label' => 'Settings', 'onclick' => "window.location.href='" . URLROOT . "/settings'"]
    ];
    require APPROOT . '/views/inc/commponents/leftSideBar.php'; ?>
    <?php $leftsidebar = ob_get_clean(); ?>


<?php ob_start()?>
        <div class="main-content">
            <!-- Profile Section -->
            <?php $hasProfileActions = (isset($_SESSION['user_id']) && isset($data['userDetails']->id) && $_SESSION['user_id'] != $data['userDetails']->id); ?>
            <div class="profile <?= $hasProfileActions ? 'has-actions' : 'no-actions' ?>">
                <div class="profile-up-part">
                    <?php if ($isOwner){
                        echo '
                            <div class="profile-edit-btn">
                        <i class="fas fa-pencil-alt"></i>
                    </div>
                        ';
                    }?>
                    
                </div>
                <div class="profile-down-part">
                    <div class="profile-image">
                         <img src="<?php echo URLROOT; ?>/media/profile/<?php echo $data['userDetails']->profile_image ?? 'default.jpg'; ?>" alt="Profile">
                    </div>
                    <div class="profile-name-container">
                        <div class="profile-name">
                            <?= isset($data['userDetails']->name) ? htmlspecialchars($data['userDetails']->name) : 'Alumni Name' ?>
                            <div class="batch-indicator">
                                <?= isset($data['userDetails']->batch_no) ? htmlspecialchars($data['userDetails']->batch_no) : '20' ?>
                            </div>
                        </div>
                        <div class="profile-bio">
                            <?= isset($data['userDetails']->bio) ? htmlspecialchars($data['userDetails']->bio) : 'Software Engineer at Google' ?>
                        </div>
                        <div class="profile-footer-spacer"></div>
                        <?php if (isset($_SESSION['user_id']) && isset($data['userDetails']->id) && $_SESSION['user_id'] != $data['userDetails']->id): ?>
                        <div class="profile-actions">
                            <button
                                class="action-btn connect-btn"
                                id="connectBtn"
                                data-user-id="<?= htmlspecialchars($data['userDetails']->id) ?>"
                                data-connected="0"
                                title="Connect with <?= htmlspecialchars($data['userDetails']->name ?? 'user') ?>">
                                <i class="fas fa-user-plus" aria-hidden="true"></i>
                                <span>Connect</span>
                            </button>
                            <button
                                class="action-btn message-btn"
                                id="messageBtn"
                                data-user-id="<?= htmlspecialchars($data['userDetails']->id) ?>"
                                title="Message <?= htmlspecialchars($data['userDetails']->name ?? 'user') ?>">
                                <i class="fas fa-envelope" aria-hidden="true"></i>
                                <span>Message</span>
                            </button>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Navigation Buttons -->
            <div class="profile-navigation">
                <div class="nav-button active" id="postsTab" onclick="showTab('posts')">
                    POSTS
                </div>
                <div class="nav-button" id="infoTab" onclick="showTab('info')">
                    INFO
                </div>
            </div>


            <!-- Import newpost_section below navigation -->

            <?php
            if ($isOwner){
                require APPROOT . '/views/inc/commponents/newpost_section.php';
            }?>

            <!-- Posts Section - Using same structure as main feed -->
            <div class="feed" id="postsSection">
                <?php if(!empty($data['posts'])): foreach($data['posts'] as $p): ?>
                    <post-card
                        profile-img="<?php echo htmlspecialchars($p->profile_image ?? ''); ?>"
                        user-role="<?php echo htmlspecialchars($p->role ?? ''); ?>"
                        user-name="<?php echo htmlspecialchars($p->name ?? 'User'); ?>"
                        tag="@user<?php echo $p->user_id ?? ''; ?>"
                        post-time="<?php echo isset($p->created_at) ? date('M d', strtotime($p->created_at)) : ''; ?>"
                        post-content="<?php echo htmlspecialchars($p->content ?? ''); ?>"
                        post-img="<?php echo htmlspecialchars($p->image ?? ''); ?>"
                        like-count="<?php echo $p->likes ?? 0; ?>"
                        cmnt-count="<?php echo $p->comments ?? 0; ?>"
                        liked="<?php echo !empty($p->liked) ? 1 : 0; ?>"
                        post-id="<?php echo $p->id ?? ''; ?>"
                        post-user-id="<?php echo $p->user_id ?? ''; ?>"
                        current-user-id="<?php echo $_SESSION['user_id'] ?? ''; ?>"
                        current-user-role="<?php echo $_SESSION['user_role'] ?? ''; ?>">
                    </post-card>
                <?php endforeach; else: ?>
                    <div class="no-posts-message">No posts yet.</div>
                <?php endif; ?>
            </div>

            <!-- Info Section: Certificates, Work Experience and Projects -->
            <div class="info-section" id="infoSection">
                <!-- Work Experience Section -->
                <div class="section-header">
                    <div class="section-title">Work Experience</div>
                    <?php if($isOwner){
                        echo '
                        <div class="section-actions">
                        <div class="section-action-btn" id="editWorkBtn" title="Edit Work Experience">
                            <i class="fas fa-pencil-alt"></i>
                        </div>
                        <div class="section-action-btn" id="addWorkBtn" title="Add Work Experience">
                            <i class="fas fa-plus"></i>
                        </div>
                        </div>';
                        }?>
                    
                </div>
                
                <!-- Work Experience Cards -->
                <div id="workContainer">
                    <?php 
                    // Sample work experience data
                    $sampleWork = [
                        ['id' => 1, 'title' => 'Software Engineer', 'company' => 'Google', 'period' => '2021 - Present'],
                        ['id' => 2, 'title' => 'Associate Developer', 'company' => 'Microsoft', 'period' => '2019 - 2021'],
                        ['id' => 3, 'title' => 'Intern', 'company' => 'Facebook', 'period' => '2018 - 2019']
                    ];
                    
                    if(!empty($sampleWork)):
                        foreach($sampleWork as $work):
                    ?>
                    <div class="certificate-card" data-id="<?= $work['id'] ?>">
                        <div class="certificate-card-image">
                            <i class="fas fa-briefcase"></i>
                        </div>
                        <div class="certificate-details">
                            <div class="certificate-card-title"><?= htmlspecialchars($work['title']) ?></div>
                            <div class="certificate-issuer"><?= htmlspecialchars($work['company']) ?></div>
                            <div class="certificate-date"><?= htmlspecialchars($work['period']) ?></div>
                        </div>
                        <?php if ($isOwner){
                            echo '<div class="certificate-actions">
                            <div class="certificate-action-btn edit-btn" title="Edit Work Experience">
                                <i class="fas fa-pencil-alt"></i>
                            </div>
                            <div class="certificate-action-btn delete-btn" title="Delete Work Experience">
                                <i class="fas fa-trash-alt"></i>
                            </div>
                        </div>';
                        }?>
                        
                    </div>
                    <?php 
                        endforeach; 
                    else: 
                    ?>
                    <div>No work experience added yet.</div>
                    <?php endif; ?>
                </div>
                
                <!-- Certificates Section with Action Buttons -->
                <div class="section-header" style="margin-top:1.5em;">
                    <div class="section-title">Certificates</div>
                    <?php if($isOwner){
                        echo '
                            <div class="section-actions">
                        <div class="section-action-btn" id="editCertificatesBtn" title="Edit Certificates">
                            <i class="fas fa-pencil-alt"></i>
                        </div>
                        <div class="section-action-btn" id="addCertificateBtn" title="Add Certificate">
                            <i class="fas fa-plus"></i>
                        </div>
                    </div>
                        ';

                    }?>
                    
                </div>
                
                <!-- Certificate Cards -->
                <div id="certificatesContainer">
                    <?php
if(!empty($data['certificates'])):
    foreach($data['certificates'] as $cert):
        $date = new DateTime($cert->issued_date);
        $formattedDate = $date->format('F Y');
?>
<div class="certificate-card"
     data-id="<?= htmlspecialchars($cert->id) ?>"
     data-name="<?= htmlspecialchars($cert->name) ?>"
     data-issuer="<?= htmlspecialchars($cert->issuer) ?>"
     data-issued_date="<?= htmlspecialchars($cert->issued_date) ?>"
     data-file="<?= htmlspecialchars($cert->certificate_file ?? '') ?>">
    <div class="certificate-card-image"><i class="fas fa-certificate"></i></div>
    <div class="certificate-details">
        <div class="certificate-card-title"><?= htmlspecialchars($cert->name) ?></div>
        <div class="certificate-issuer"><?= htmlspecialchars($cert->issuer) ?></div>
        <div class="certificate-date"><?= htmlspecialchars($formattedDate) ?></div>
    </div>
    <?php if($isOwner): ?>
    <div class="certificate-actions">
        <div class="certificate-action-btn edit-btn" title="Edit Certificate"><i class="fas fa-pencil-alt"></i></div>
        <div class="certificate-action-btn delete-btn" title="Delete Certificate"><i class="fas fa-trash-alt"></i></div>
    </div>
    <?php endif; ?>
</div>
<?php
    endforeach;
else:
?>
<div>No certificates added yet.</div>
<?php endif; ?>
                </div>

                <!-- Projects Section -->
                <div class="section-header" style="margin-top:1.5em;">
                    <div class="section-title">Projects</div>
                    <?php if ($isOwner){
                        echo '
                            <div class="section-actions">
                        <div class="section-action-btn" id="editProjectsBtn" title="Edit Projects">
                            <i class="fas fa-pencil-alt"></i>
                        </div>
                        <div class="section-action-btn" id="addProjectBtn" title="Add Project">
                            <i class="fas fa-plus"></i>
                        </div>
                    </div>
                        ';

                    }?>
                    
                </div>
                
                <div id="projectsContainer">
                    <?php
                    // Sample project data
                    $sampleProjects = [
                        ['id' => 1, 'title' => 'AI-Powered Healthcare App'],
                        ['id' => 2, 'title' => 'Smart Home Automation System']
                    ];
                    
                    if(!empty($sampleProjects)): foreach($sampleProjects as $project): ?>
                        <div class="project-card" data-id="<?= $project['id'] ?>">
                            <div class="project-card-image">
                                <i class="fas fa-project-diagram"></i>
                            </div>
                            <div class="project-card-title"><?= htmlspecialchars($project['title']) ?></div>
                            <?php if($isOwner){
                                echo '<div class="certificate-actions">
                                <div class="certificate-action-btn edit-btn" title="Edit Project">
                                    <i class="fas fa-pencil-alt"></i>
                                </div>
                                <div class="certificate-action-btn delete-btn" title="Delete Project">
                                    <i class="fas fa-trash-alt"></i>
                                </div>
                            </div>';
                            }?>
                            
                        </div>
                    <?php endforeach; else: ?>
                        <div>No projects added yet.</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>



    <!-- Add Certificate Popup (separate form) -->
    <div id="addCertificatePopup" class="certificate-add-popup" style="display:none;">
        <div class="certificate-add">
            <button class="close-popup" title="Close"><i class="fas fa-times"></i></button>
            <div class="form-title">Add New Certificate</div>

            <form id="addCertificateForm" method="post" action="<?= URLROOT; ?>/profile/addCertificate" enctype="multipart/form-data" class="certificate-form">
                <div class="form-group">
                    <label for="certificateNameAdd">Name</label>
                    <input type="text" id="certificateNameAdd" name="certificate_name" required>
                </div>
                <div class="form-group">
                    <label for="certificateIssuerAdd">Issuing Organization</label>
                    <input type="text" id="certificateIssuerAdd" name="certificate_issuer" required>
                </div>
                <div class="form-group">
                    <label for="certificateDateAdd">Issue Date</label>
                    <input type="date" id="certificateDateAdd" name="certificate_date" required>
                </div>
                <div class="form-group">
                    <label for="certificateFileAdd">Upload Certificate (PDF)</label>
                    <div class="file-upload-container">
                        <input type="file" id="certificateFileAdd" name="certificate_file" accept=".pdf" style="display:none;">
                        <button type="button" class="file-upload-btn" id="chooseFileBtnAdd" onclick="document.getElementById('certificateFileAdd').click()">Choose File</button>
                        <span class="file-name" id="fileNameAdd" style="color:var(--text)">No file chosen</span>
                    </div>
                </div>

                <div style="margin-top:12px;">
                    <button type="submit" class="save-btn" id="saveNewBtnAdd">Save Certificate</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Certificate Popup (separate form) -->
    <div id="editCertificatePopup" class="certificate-add-popup" style="display:none;">
        <div class="certificate-add">
            <button class="close-popup" title="Close"><i class="fas fa-times"></i></button>
            <div class="form-title" id="certificateFormTitleEdit">Edit Certificate</div>

            <form id="editCertificateForm" method="post" action="<?= URLROOT; ?>/profile/updateCertificate" enctype="multipart/form-data" class="certificate-form">
                <input type="hidden" name="certificate_id" id="certificateIdEdit" value="">
                <div class="form-group">
                    <label for="certificateNameEdit">Name</label>
                    <input type="text" id="certificateNameEdit" name="certificate_name" required>
                </div>
                <div class="form-group">
                    <label for="certificateIssuerEdit">Issuing Organization</label>
                    <input type="text" id="certificateIssuerEdit" name="certificate_issuer" required>
                </div>
                <div class="form-group">
                    <label for="certificateDateEdit">Issue Date</label>
                    <input type="date" id="certificateDateEdit" name="certificate_date" required>
                </div>
                <div class="form-group">
                    
                    <div class="file-upload-container" id="fileUploadContainerEdit">
                        <label for="certificateFileEdit">Upload Certificate (PDF)</label>
                        </br>
                        <input type="file" id="certificateFileEdit" name="certificate_file" accept=".pdf" style="display:none;">
                        <button type="button" class="file-upload-btn" id="chooseFileBtnEdit" onclick="document.getElementById('certificateFileEdit').click()">Choose File</button>
                        <span class="file-name" id="fileNameEdit" style="color:var(--text)">No file chosen</span>
                        <input type="hidden" id="removeFileInputEdit" name="remove_certificate_file" value="0">
                        <input type="hidden" id="existingFileInputEdit" name="existing_certificate_file" value="">
                    </div>
                    
                    <div id="currentFileContainerEdit" style="margin-top:8px; display:none;">
                        <span style="color:var(--muted)">Current file: </span>
                        <a href="#" id="currentFileLinkEdit" target="_blank" style="color:var(--link);"></a>
                        <button type="button" id="cutFileBtnEdit" class="file-cut-btn" title="Remove current file"
                                style="margin-left:12px;background:none;border:none;color:var(--link);cursor:pointer;font-size:1rem;">
                            &times;
                        </button>
                    </div>
                </div>

                <div style="margin-top:12px;">
                    <button type="submit" class="save-btn" id="saveChangesBtnEdit">Save Changes</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Add Delete Confirmation Popup (uses same styles as certificate-add-popup) -->
    <div id="deleteCertificatePopup" class="certificate-add-popup" style="display:none;">
        <div class="certificate-add">
            <button class="close-popup" title="Close"><i class="fas fa-times"></i></button>
            <div class="form-title">Delete Certificate</div>
            <div class="certificate-delete-body" style="color:var(--text); padding:16px;">
                <p>Are you sure you want to permanently delete this certificate? This action cannot be undone.</p>
            </div>
            <div style="display:flex; gap:12px; justify-content:flex-end; padding:12px 16px 20px;">
                <button type="button" id="cancelDeleteCertBtn" class="save-btn" style="background:transparent;color:var(--text);border:1px solid var(--border);">Cancel</button>
                <button type="button" id="confirmDeleteCertBtn" class="save-btn" style="background:var(--danger);color:#fff;">Delete</button>
            </div>
        </div>
    </div>

<?php $center_content = ob_get_clean();?>
<?php ob_start() ?>
    <!-- Include the right sidebar component -->
    <?php
    $rightSidebarStylesIncluded = true; // Prevent duplicate styles
    require APPROOT . '/views/inc/commponents/rightSideBar.php';
    ?>
<?php $rightsidebar = ob_get_clean(); ?>

<?php ob_start()?>
    <script>
        window.URLROOT = "<?php echo URLROOT; ?>";
    </script>
    <script src="<?php echo URLROOT?>/js/component/postCard.js"></script>
    <script>
        // Function to switch between posts and info tabs
        function showTab(tab) {
            // Get the sections
            const postsSection = document.getElementById('postsSection');
            const infoSection = document.getElementById('infoSection');
            const newPostSection = document.querySelector('.new-post-section');
            
            // Get the tab buttons
            const postsTab = document.getElementById('postsTab');
            const infoTab = document.getElementById('infoTab');
            
            // Hide all sections first
            postsSection.style.display = 'none';
            infoSection.style.display = 'none';
            
            // Remove active class from all tabs
            postsTab.classList.remove('active');
            infoTab.classList.remove('active');
            
            // Show the selected section and activate its tab
            if (tab === 'posts') {
                postsSection.style.display = 'block';
                if (newPostSection) newPostSection.style.display = 'block';
                postsTab.classList.add('active');
            } else if (tab === 'info') {
                infoSection.style.display = 'flex';
                if (newPostSection) newPostSection.style.display = 'none'; // Hide new post section in info tab
                infoTab.classList.add('active');
            }
        }

        // Certificate and other info management
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize with posts tab active
            showTab('posts');
            
            // Set up edit mode functionality for all sections
            setupEditModeForSection('editWorkBtn', 'workContainer', 'certificate-card');
            setupEditModeForSection('editCertificatesBtn', 'certificatesContainer', 'certificate-card');
            setupEditModeForSection('editProjectsBtn', 'projectsContainer', 'project-card');
            
            // Setup card action buttons for all card types
            setupCardActionButtons('.certificate-card');
            setupCardActionButtons('.project-card');
            
            // Removed obsolete certificate handlers that referenced old IDs:
            // - document.getElementById('certificateAddPopup')
            // - document.getElementById('certificateForm')
            // - document.getElementById('certificateFile')
            // These selectors no longer exist and threw JS errors preventing the new handlers from executing.
            //
            // The new per-form handlers (addCertificatePopup / editCertificatePopup / addCertificateForm / editCertificateForm)
            // defined later in this file handle add/update flows and should remain.
            
            // Close certificate add popup
            const closePopupBtn = document.querySelector('.certificate-add-popup .close-popup');
            if (closePopupBtn) {
                closePopupBtn.addEventListener('click', function() {
                    document.getElementById('certificateAddPopup').style.display = 'none';
                });
            }
            
            // Handle certificate form submission
            const certificateForm = document.getElementById('certificateForm');
            if (certificateForm) {
                certificateForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    
                    fetch(this.action, {
                        method: 'POST',
                        body: new FormData(this)
                    })
                    .then(response => response.json())
                    .then(data => {
                        console.log('Success:', data);
                    })
                    .catch((error) => {
                        console.error('Error:', error);
                    });

                    document.getElementById('certificateAddPopup').style.display = 'none';
                });
            }
            
            // File upload display filename
            const certificateFile = document.getElementById('certificateFile');
            if (certificateFile) {
                certificateFile.addEventListener('change', function() {
                    const fileName = this.files[0] ? this.files[0].name : 'No file chosen';
                    document.getElementById('fileName').textContent = fileName;
                });
            }
            
            // Profile edit button
            const profileEditBtn = document.querySelector('.profile-edit-btn');
            if (profileEditBtn) {
                profileEditBtn.addEventListener('click', function() {
                    console.log('Profile edit functionality will be implemented later');
                    alert('Profile edit feature will be available soon.');
                });
            }

            // Add fundraising section to the existing right sidebar
            // Connect button behavior (basic toggle for now)
            const connectBtn = document.getElementById('connectBtn');
            if (connectBtn) {
                connectBtn.addEventListener('click', async function() {
                    const targetId = this.getAttribute('data-user-id');
                    const isConnected = this.getAttribute('data-connected') === '1';

                    try {
                        // TODO: Replace this with real follow/connect API call
                        // const res = await fetch(`${window.URLROOT}/follow/toggle`, { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify({ target_id: targetId }) });
                        // const json = await res.json();
                        // const connected = !!json.connected;
                        const connected = !isConnected; // Simulate

                        this.setAttribute('data-connected', connected ? '1' : '0');
                        this.classList.toggle('active', connected);
                        this.querySelector('span').textContent = connected ? 'Connected' : 'Connect';
                        this.querySelector('i').className = connected ? 'fas fa-user-check' : 'fas fa-user-plus';
                    } catch (e) {
                        console.error(e);
                        alert('Failed to update connection. Please try again.');
                    }
                });
            }

            // Message button behavior - navigate to messages (optionally with query param)
            const messageBtn = document.getElementById('messageBtn');
            if (messageBtn) {
                messageBtn.addEventListener('click', function() {
                    const targetId = this.getAttribute('data-user-id');
                    // If conversations by user supported, pass param; else go to messages home
                    window.location.href = `${window.URLROOT}/messages?user=${encodeURIComponent(targetId)}`;
                });
            }
        });

        // Updated function to set up edit mode only for specific sections
        function setupEditModeForSection(btnId, containerId, cardClass) {
            const editBtn = document.getElementById(btnId);
            const container = document.getElementById(containerId);
            
            if (editBtn && container) {
                const cards = container.querySelectorAll('.' + cardClass);
                
                if (cards.length > 0) {
                    editBtn.addEventListener('click', function() {
                        const isInEditMode = cards[0].classList.contains('edit-mode');
                        
                        cards.forEach(card => {
                            if (isInEditMode) {
                                card.classList.remove('edit-mode');
                            } else {
                                card.classList.add('edit-mode');
                            }
                        });
                        
                        // Toggle edit button appearance
                        if (isInEditMode) {
                            editBtn.style.backgroundColor = '';
                            editBtn.style.color = '';
                        } else {
                            editBtn.style.backgroundColor = 'var(--primary)';
                            editBtn.style.color = 'var(--surface-0)';
                        }
                    });
                }
            }
        }

        // Setup action buttons for different card types
        function setupCardActionButtons(cardSelector) {
            const cards = document.querySelectorAll(cardSelector);
            
            cards.forEach(card => {
                const editBtn = card.querySelector('.edit-btn');
                const deleteBtn = card.querySelector('.delete-btn');
                
                if (editBtn) {
                    editBtn.addEventListener('click', function() {
                        const cardId = card.dataset.id;
                        console.log('Edit item:', cardId);
                        // Future implementation for editing
                    });
                }
                
                if (deleteBtn) {
                    deleteBtn.addEventListener('click', function() {
                        const cardId = card.dataset.id;
                        if (confirm('Are you sure you want to delete this item?')) {
                            console.log('Delete item:', cardId);
                            // Future implementation for deletion
                            card.remove();
                        }
                    });
                }
            });
        }
    </script>
    <script>
        // --- New: per-form element references ---
const addCertificateBtn = document.getElementById('addCertificateBtn'); // existing in page
const addCertificatePopup = document.getElementById('addCertificatePopup');
const addCertificateForm = document.getElementById('addCertificateForm');
const chooseFileBtnAdd = document.getElementById('chooseFileBtnAdd');
const certificateFileAdd = document.getElementById('certificateFileAdd');
const fileNameAdd = document.getElementById('fileNameAdd');

const editCertificatePopup = document.getElementById('editCertificatePopup');
const editCertificateForm = document.getElementById('editCertificateForm');
const certificateIdEdit = document.getElementById('certificateIdEdit');
const chooseFileBtnEdit = document.getElementById('chooseFileBtnEdit');
const certificateFileEdit = document.getElementById('certificateFileEdit');
const fileNameEdit = document.getElementById('fileNameEdit');
const currentFileContainerEdit = document.getElementById('currentFileContainerEdit');
const currentFileLinkEdit = document.getElementById('currentFileLinkEdit');
const cutFileBtnEdit = document.getElementById('cutFileBtnEdit');
const removeFileInputEdit = document.getElementById('removeFileInputEdit');
const existingFileInputEdit = document.getElementById('existingFileInputEdit');
// NEW: reference to the upload container so we can hide/show it
const fileUploadContainerEdit = document.getElementById('fileUploadContainerEdit');

// close buttons for both popups (reuse existing selector)
document.querySelectorAll('.certificate-add .close-popup').forEach(btn=>{
    btn.addEventListener('click', function(){
        const popup = this.closest('.certificate-add-popup');
        if (popup) popup.style.display = 'none';
    });
});

// Open add popup
if (addCertificateBtn && addCertificatePopup) {
    addCertificateBtn.addEventListener('click', function(){
        // clear add form
        addCertificateForm.reset();
        fileNameAdd.textContent = 'No file chosen';
        if (chooseFileBtnAdd) chooseFileBtnAdd.style.display = 'inline-block';
        addCertificatePopup.style.display = 'flex';
    });
}

// Edit button behavior: populate edit form and open edit popup
document.querySelectorAll('.certificate-card .edit-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        const card = this.closest('.certificate-card');
        const id = card.dataset.id || '';
        const name = card.dataset.name || '';
        const issuer = card.dataset.issuer || '';
        const issued_date = card.dataset.issued_date || '';
        const file = card.dataset.file || '';

        certificateIdEdit.value = id;
        document.getElementById('certificateNameEdit').value = name;
        document.getElementById('certificateIssuerEdit').value = issuer;
        document.getElementById('certificateDateEdit').value = issued_date || '';

        if (file) {
            // show current-file area; hide upload area (per requirement #1)
            currentFileContainerEdit.style.display = 'block';
            currentFileLinkEdit.href = URLROOT + '/storage/certificates/' + file;
            currentFileLinkEdit.textContent = file;
            removeFileInputEdit.value = '0';
            if (existingFileInputEdit) existingFileInputEdit.value = file;
            if (fileUploadContainerEdit) fileUploadContainerEdit.style.display = 'none';
            fileNameEdit.textContent = 'No file chosen';
        } else {
            // no existing file: hide current-file area and show upload area
            currentFileContainerEdit.style.display = 'none';
            currentFileLinkEdit.href = '#';
            currentFileLinkEdit.textContent = '';
            if (fileUploadContainerEdit) fileUploadContainerEdit.style.display = 'block';
            removeFileInputEdit.value = '0';
            if (existingFileInputEdit) existingFileInputEdit.value = '';
            fileNameEdit.textContent = 'No file chosen';
        }

        editCertificatePopup.style.display = 'flex';
    });
});

// Cut (X) behavior in edit popup: reveal upload container, mark remove flag
if (cutFileBtnEdit) {
    cutFileBtnEdit.addEventListener('click', function(){
        removeFileInputEdit.value = '1';
        currentFileContainerEdit.style.display = 'none';
        if (fileUploadContainerEdit) fileUploadContainerEdit.style.display = 'block';
        if (chooseFileBtnEdit) chooseFileBtnEdit.style.display = 'inline-block';
        fileNameEdit.textContent = 'No file chosen';
        if (certificateFileEdit) certificateFileEdit.value = '';
        if (existingFileInputEdit) existingFileInputEdit.value = '';
    });
}

// File selection handler for edit form
if (certificateFileEdit) {
    certificateFileEdit.addEventListener('change', function() {
        const f = this.files[0];
        fileNameEdit.textContent = f ? f.name : 'No file chosen';
        if (f) {
            // new file will replace existing -> unset remove flag and clear existing filename
            removeFileInputEdit.value = '0';
            if (existingFileInputEdit) existingFileInputEdit.value = '';
            // hide current-file display (we're in upload-flow)
            if (currentFileContainerEdit) currentFileContainerEdit.style.display = 'none';
            // keep upload container visible so filename shows
            if (fileUploadContainerEdit) fileUploadContainerEdit.style.display = 'block';
        }
    });
}

// Submit handlers (use fetch like before, per form)
if (addCertificateForm) {
    addCertificateForm.addEventListener('submit', function(e){
        e.preventDefault();
        const fd = new FormData(this);
        fetch(this.action, { method:'POST', body: fd, headers: { 'Accept': 'application/json' } })
        .then(r => r.json()).then(json => {
            if (json.success) {
                // show uploaded original filename if provided
                if (json.original_name) {
                    alert('Certificate uploaded: ' + json.original_name);
                }
                window.location.reload();
            } else {
                alert(json.error || 'Failed to save certificate');
            }
        }).catch(()=>alert('Error while saving certificate'));
    });
}

if (editCertificateForm) {
    editCertificateForm.addEventListener('submit', function(e){
        e.preventDefault();
        const fd = new FormData(this);
        fetch(this.action, { method:'POST', body: fd, headers: { 'Accept': 'application/json' } })
        .then(r => r.json()).then(json => {
            if (json.success) {
                // if server returned original uploaded name, show it
                if (json.original_name) {
                    alert('Certificate updated. Uploaded file: ' + json.original_name);
                } else if (json.file) {
                    // no new upload, show stored filename
                    alert('Certificate updated. File: ' + json.file);
                }
                window.location.reload();
            } else {
                alert(json.error || 'Failed to update certificate');
            }
        }).catch(()=>alert('Error while updating certificate'));
    });
}

// New: delete-certificate popup logic
(function(){
    const deletePopup = document.getElementById('deleteCertificatePopup');
    const confirmDeleteBtn = document.getElementById('confirmDeleteCertBtn');
    const cancelDeleteBtn = document.getElementById('cancelDeleteCertBtn');
    let pendingDeleteCertId = null;
    // open delete popup when any certificate-card delete-btn clicked
    document.querySelectorAll('.certificate-card .delete-btn').forEach(btn => {
        btn.addEventListener('click', function(e){
            e.stopPropagation();
            const card = this.closest('.certificate-card');
            const id = card ? card.dataset.id : null;
            if (!id) {
                alert('Invalid certificate id');
                return;
            }
            pendingDeleteCertId = id;
            if (deletePopup) deletePopup.style.display = 'flex';
        });
    });

    // close handlers (reuse close-popup buttons)
    document.querySelectorAll('.certificate-add .close-popup').forEach(btn=>{
        btn.addEventListener('click', function(){
            const popup = this.closest('.certificate-add-popup');
            if (popup) popup.style.display = 'none';
        });
    });

    if (cancelDeleteBtn) {
        cancelDeleteBtn.addEventListener('click', function(){
            pendingDeleteCertId = null;
            if (deletePopup) deletePopup.style.display = 'none';
        });
    }

    if (confirmDeleteBtn) {
        confirmDeleteBtn.addEventListener('click', async function(){
            if (!pendingDeleteCertId) return;
            try {
                const fd = new FormData();
                fd.append('certificate_id', pendingDeleteCertId);
                const res = await fetch(window.URLROOT + '/profile/deleteCertificate', {
                    method: 'POST',
                    body: fd,
                    credentials: 'same-origin'
                });
                const json = await res.json();
                if (json.success) {
                    // remove card from DOM if present
                    const card = document.querySelector('.certificate-card[data-id="'+pendingDeleteCertId+'"]');
                    if (card) card.remove();
                    // hide popup
                    if (deletePopup) deletePopup.style.display = 'none';
                    pendingDeleteCertId = null;
                } else {
                    alert(json.error || 'Failed to delete certificate');
                }
            } catch (err) {
                console.error(err);
                alert('Error while deleting certificate');
            }
        });
    }
})();
    </script>
<?php $scripts = ob_get_clean(); ?>
<?php require APPROOT . '/views/layouts/threeColumnLayout.php'; ?>