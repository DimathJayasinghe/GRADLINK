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
    ]
    ?>
 <?php ob_start() ?>
    <?php
    $leftside_buttons = [
        ['icon' => 'home', 'label' => 'Home', 'onclick' => "window.location.href='" . URLROOT . "/mainfeed'" ],
        ['icon' => 'search', 'label' => 'Explore', 'onclick' => "window.location.href='" . URLROOT . "/explore'"],
        ['icon' => 'bell', 'label' => 'Notifications', 'onclick' => "NotificationModal()", 'require' => APPROOT . '/views/inc/commponents/notification_pop_up.php', 'notifications' => $notifications],
        ['icon' => 'envelope', 'label' => 'Messages', 'onclick' => "window.location.href='" . URLROOT . "/messages'"],
        ['icon' => 'user', 'label' => 'Profile' , 'onclick' => "window.location.href='" . URLROOT . "/profile/watch/".$_SESSION['user_id'] . "'",'active' => true],
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
            <div class="profile">
                <div class="profile-up-part">
                    <div class="profile-edit-btn">
                        <i class="fas fa-pencil-alt"></i>
                    </div>
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
            <?php require APPROOT . '/views/inc/commponents/newpost_section.php'; ?>

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
                    <div class="section-actions">
                        <div class="section-action-btn" id="editWorkBtn" title="Edit Work Experience">
                            <i class="fas fa-pencil-alt"></i>
                        </div>
                        <div class="section-action-btn" id="addWorkBtn" title="Add Work Experience">
                            <i class="fas fa-plus"></i>
                        </div>
                    </div>
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
                        <div class="certificate-actions">
                            <div class="certificate-action-btn edit-btn" title="Edit Work Experience">
                                <i class="fas fa-pencil-alt"></i>
                            </div>
                            <div class="certificate-action-btn delete-btn" title="Delete Work Experience">
                                <i class="fas fa-trash-alt"></i>
                            </div>
                        </div>
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
                    <div class="section-actions">
                        <div class="section-action-btn" id="editCertificatesBtn" title="Edit Certificates">
                            <i class="fas fa-pencil-alt"></i>
                        </div>
                        <div class="section-action-btn" id="addCertificateBtn" title="Add Certificate">
                            <i class="fas fa-plus"></i>
                        </div>
                    </div>
                </div>
                
                <!-- Certificate Cards -->
                <div id="certificatesContainer">
                    <?php 
                    // Fetch certificates from $data['certificates'] (assumed to be passed from controller)
                    $sampleCertificates = !empty($data['certificates']) ? $data['certificates'] : [];
                    ?>
                    <?php
                    if(!empty($sampleCertificates)):
                        foreach($sampleCertificates as $cert):
                            $date = new DateTime($cert->issued_date);
                            $formattedDate = $date->format('F Y');
                    ?>
                    <div class="certificate-card" data-id="<?= $cert->id ?>">
                        <div class="certificate-card-image">
                            <i class="fas fa-certificate"></i>
                        </div>
                        <div class="certificate-details">
                            <div class="certificate-card-title"><?= htmlspecialchars($cert->name) ?></div>
                            <div class="certificate-issuer"><?= htmlspecialchars($cert->issuer) ?></div>
                            <div class="certificate-date"><?= htmlspecialchars($formattedDate) ?></div>
                        </div>
                        <div class="certificate-actions">
                            <div class="certificate-action-btn edit-btn" title="Edit Certificate">
                                <i class="fas fa-pencil-alt"></i>
                            </div>
                            <div class="certificate-action-btn delete-btn" title="Delete Certificate">
                                <i class="fas fa-trash-alt"></i>
                            </div>
                        </div>
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
                    <div class="section-actions">
                        <div class="section-action-btn" id="editProjectsBtn" title="Edit Projects">
                            <i class="fas fa-pencil-alt"></i>
                        </div>
                        <div class="section-action-btn" id="addProjectBtn" title="Add Project">
                            <i class="fas fa-plus"></i>
                        </div>
                    </div>
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
                            <div class="certificate-actions">
                                <div class="certificate-action-btn edit-btn" title="Edit Project">
                                    <i class="fas fa-pencil-alt"></i>
                                </div>
                                <div class="certificate-action-btn delete-btn" title="Delete Project">
                                    <i class="fas fa-trash-alt"></i>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; else: ?>
                        <div>No projects added yet.</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>



    <!-- Certificate Add Popup -->
    <div id="certificateAddPopup" class="certificate-add-popup">
        <div class="certificate-add">
            <button class="close-popup" title="Close">
                <i class="fas fa-times"></i>
            </button>
            
            <div class="form-title">Add New Certificate</div>

            <form method="post" action="<?php echo URLROOT; ?>/profile/addCertificate" enctype="multipart/form-data" class="certificate-form" id="certificateForm">
                <div class="form-group">
                    <label for="certificateName">Name</label>
                    <input type="text" id="certificateName" name="certificate_name" placeholder="Certificate name" required>
                </div>
                
                <div class="form-group">
                    <label for="certificateIssuer">Issuing Organization</label>
                    <input type="text" id="certificateIssuer" name="certificate_issuer" placeholder="Organization name" required>
                </div>
                
                <div class="form-group">
                    <label for="certificateDate">Issue Date</label>
                    <input type="date" id="certificateDate" name="certificate_date" required>
                </div>
                
                <div class="form-group">
                    <label for="certificateFile">Upload Certificate (PDF)</label>
                    <div class="file-upload-container">
                        <input type="file" id="certificateFile" name="certificate_file" accept=".pdf" style="display: none;">
                        <button type="button" class="file-upload-btn" onclick="document.getElementById('certificateFile').click()">Choose File</button>
                        <span class="file-name" id="fileName">No file chosen</span>
                    </div>
                </div>
                
                <button type="submit" class="save-btn">Save Certificate</button>
            </form>
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
            
            // Open add certificate popup
            const addCertificateBtn = document.getElementById('addCertificateBtn');
            if (addCertificateBtn) {
                addCertificateBtn.addEventListener('click', function() {
                    document.getElementById('certificateAddPopup').style.display = 'flex';
                });
            }
            
            // Setup card action buttons for all card types
            setupCardActionButtons('.certificate-card');
            setupCardActionButtons('.project-card');
            
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
<?php $scripts = ob_get_clean();?>
<?php require APPROOT . '/views/layouts/threeColumnLayout.php'; ?>