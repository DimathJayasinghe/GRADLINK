<?php
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Undergraduate Profile</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/css/style.css">
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/css/color-pallate.css">
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/css/components/postCardStyles.css">
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/css/mainfeed_styles.css"> <!-- Import main feed styles -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
        window.URLROOT = "<?php echo URLROOT; ?>";
    </script>

    <style>
        /* Profile specific styles */
        .profile {
            width: 100%;
            height: 220px;
            position: relative;
            margin-bottom: 1rem;
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-1);
            overflow: hidden;
        }

        .profile > .profile-up-part {
            width: 100%;
            height: 40%;
            background-color: var(--surface-3);
            position: relative;
        }

        .profile > .profile-up-part > .profile-edit-btn {
            position: absolute;
            top: 15px;
            right: 15px;
            width: 32px;
            height: 32px;
            background-color: var(--btn);
            color: var(--text);
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            cursor: pointer;
            transition: all 0.2s ease-in-out;
            z-index: 15;
            box-shadow: var(--shadow-2);
        }

        .profile > .profile-up-part > .profile-edit-btn:hover {
            background-color: var(--primary);
            transform: translateY(-2px);
        }

        .profile > .profile-down-part {
            width: 100%;
            height: 60%;
            background-color: var(--card);
            position: relative;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .profile > .profile-down-part > .profile-image {
            position: absolute;
            z-index: 10;
            top: -30px;
            width: 60px;
            height: 60px;
            background-color: var(--surface-4);
            border-radius: var(--radius-lg);
            border: 3px solid var(--card);
        }

        /* Batch indicator styles */
        .profile-name-container {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding-top: 40px;
        }

        .profile-name {
            font-weight: bold;
            font-size: 1.5em;
            text-align: center;
            color: var(--text);
        }

        .batch-indicator {
            background-color: #e74c3c; /* Red circle */
            color: white;
            width: 24px;
            height: 24px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.8em;
            font-weight: bold;
            box-shadow: var(--shadow-1);
        }

        .profile > .profile-down-part > .profile-bio {
            font-size: 0.9em;
            max-width: 90%;
            margin: 8px auto 0;
            text-align: center;
            color: var(--muted);
        }

        /* Updated navigation button styles */
        .profile-navigation {
            display: flex;
            justify-content: center;
            gap: 80px; /* Increased spacing between buttons */
            margin: 15px 0;
        }

        .nav-button {
            width: 130px; /* Slightly wider to account for text without icons */
            height: 40px;
            background-color: var(--btn);
            color: var(--text);
            border-radius: var(--radius-lg);
            display: flex;
            justify-content: center;
            align-items: center;
            cursor: pointer;
            transition: all 0.2s ease-in-out;
            font-weight: 600;
            box-shadow: var(--shadow-1);
            letter-spacing: 0.5px;
        }

        .nav-button:hover {
            background-color: var(--link-hover);
            transform: translateY(-2px);
        }

        .nav-button.active {
            background-color: var(--primary);
            color: var(--surface-0);
        }

        /* Info section */
        .info-section {
            display: none;
            flex-direction: column;
            gap: 1em;
            width: 100%;
            background-color: var(--surface-1);
            border-radius: var(--radius-lg);
            padding: 20px 15px;
            min-height: 200px;
            box-shadow: var(--shadow-1);
            margin-top: 15px;
        }

        /* Section title with action buttons */
        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }

        .section-title {
            font-weight: bold;
            font-size: 1.2em;
            color: var(--text);
            position: relative;
        }

        .section-title:after {
            content: '';
            position: absolute;
            left: 0;
            bottom: -5px;
            height: 2px;
            width: 40px;
            background-color: var(--primary);
        }

        .section-actions {
            display: flex;
            gap: 10px;
        }

        .section-action-btn {
            width: 32px;
            height: 32px;
            background-color: var(--btn);
            color: var(--text);
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            cursor: pointer;
            transition: all 0.2s ease-in-out;
            box-shadow: var(--shadow-1);
        }

        .section-action-btn:hover {
            background-color: var(--primary);
            transform: translateY(-2px);
            color: var(--surface-0);
        }

        /* Certificate & Project Cards */
        .project-card,
        .certificate-card {
            display: flex;
            flex-direction: row;
            align-items: center;
            width: 100%;
            min-height: 60px;
            background-color: var(--surface-2);
            padding: 15px;
            border-radius: var(--radius-lg);
            margin-bottom: 1em;
            transition: transform 0.2s;
            position: relative;
        }

        .project-card:hover,
        .certificate-card:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-1);
        }

        .project-card-image,
        .certificate-card-image {
            width: 60px;
            height: 60px;
            background-color: var(--surface-4);
            margin-right: 15px;
            border-radius: var(--radius-lg);
            display: flex;
            justify-content: center;
            align-items: center;
            color: var(--muted);
            font-size: 24px;
        }

        .certificate-details {
            display: flex;
            flex-direction: column;
            gap: 5px;
            flex-grow: 1;
        }

        .project-card-title,
        .certificate-card-title {
            font-weight: bold;
            color: var(--text);
        }

        .certificate-issuer {
            font-size: 0.85em;
            color: var(--muted);
        }

        .certificate-date {
            font-size: 0.8em;
            color: var(--muted);
        }

        /* Certificate card actions */
        .certificate-actions {
            display: none; /* Hidden by default, shown in edit mode */
            position: absolute;
            right: 15px;
            gap: 8px;
        }

        .certificate-card.edit-mode .certificate-actions {
            display: flex;
        }

        .certificate-action-btn {
            width: 28px;
            height: 28px;
            background-color: var(--surface-3);
            color: var(--muted);
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            cursor: pointer;
            transition: all 0.2s ease-in-out;
        }

        .certificate-action-btn:hover {
            background-color: var(--primary);
            color: var(--surface-0);
        }

        .delete-btn:hover {
            background-color: #d32f2f; /* Red color for delete */
        }

        /* Certificate Add Form */
        .certificate-add-popup {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(15, 21, 24, 0.8);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 1000;
        }

        .certificate-add {
            width: 90%;
            max-width: 500px;
            height: auto;
            max-height: 90vh;
            overflow-y: auto;
            padding: 20px;
            border-radius: var(--radius-lg);
            background-color: var(--card);
            box-shadow: var(--shadow-1);
            position: relative;
            margin: auto;
        }

        .close-popup {
            position: absolute;
            top: 10px;
            right: 10px;
            width: 30px;
            height: 30px;
            background-color: var(--surface-3);
            border: none;
            border-radius: 50%;
            color: var(--muted);
            font-size: 18px;
            cursor: pointer;
            display: flex;
            justify-content: center;
            align-items: center;
            transition: all 0.2s ease;
        }

        .close-popup:hover {
            background-color: var(--primary);
            color: var(--text);
        }

        .certificate-form {
            display: flex;
            flex-direction: column;
            gap: 20px;
            margin-top: 10px;
        }

        .form-title {
            font-size: 1.3em;
            font-weight: 600;
            color: var(--text);
            text-align: center;
            margin-bottom: 10px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .form-group label {
            font-size: 0.9em;
            color: var(--muted);
            font-weight: 500;
        }

        .form-group input,
        .form-group select {
            padding: 10px 12px;
            border-radius: var(--radius-lg);
            border: 1px solid var(--border);
            background-color: var(--input);
            color: var(--text);
            font-family: inherit;
            transition: border 0.2s;
        }

        .form-group input:focus,
        .form-group select:focus {
            border-color: var(--primary);
            outline: none;
        }

        .file-upload-container {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .file-upload-btn {
            padding: 8px 12px;
            background-color: var(--surface-3);
            color: var(--text);
            border-radius: var(--radius-lg);
            cursor: pointer;
            font-size: 0.9em;
            transition: all 0.2s;
        }

        .file-upload-btn:hover {
            background-color: var(--primary);
            color: var(--surface-0);
        }

        .file-name {
            font-size: 0.85em;
            color: var(--muted);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 200px;
        }

        .save-btn {
            background-color: var(--primary);
            color: var(--surface-0);
            padding: 12px;
            border: none;
            border-radius: var(--radius-lg);
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            margin-top: 10px;
        }

        .save-btn:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-2);
        }

        /* Responsive adjustments */
        @media (max-width: 1200px) {
            .feed-container {
                grid-template-columns: 250px 1fr 300px;
            }
        }

        @media (max-width: 992px) {
            .feed-container {
                grid-template-columns: 200px 1fr 250px;
            }
        }

        @media (max-width: 768px) {
            .feed-container {
                grid-template-columns: 1fr;
            }
            
            .left-sidebar, .right-sidebar {
                display: none;
            }
            
            .profile > .profile-down-part > .profile-bio {
                max-width: 95%;
            }
            
            .profile-navigation {
                gap: 60px;
            }
            
            .nav-button {
                width: 120px;
            }
        }

        @media (max-width: 576px) {
            .profile {
                height: 230px;
            }
            
            .profile-name-container {
                padding-top: 36px;
                gap: 6px;
            }
            
            .profile-name {
                font-size: 1.2em;
            }
            
            .batch-indicator {
                width: 20px;
                height: 20px;
                font-size: 0.75em;
            }
            
            .profile > .profile-down-part > .profile-bio {
                font-size: 0.8em;
            }
            
            .profile-navigation {
                gap: 40px;
            }
            
            .nav-button {
                width: 110px;
                height: 36px;
                font-size: 0.9em;
            }
        }
    </style>
</head>

<body>
    <div class="feed-container">
        <?php require APPROOT . '/views/inc/commponents/leftSideBar.php'; ?>

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
                        <!-- User profile image here -->
                    </div>
                    <div class="profile-name-container">
                        <div class="profile-name">
                            <?= isset($data['userDetails']->name) ? htmlspecialchars($data['userDetails']->name) : 'User Name' ?>
                        </div>
                        <div class="batch-indicator">
                            <?= isset($data['userDetails']->batch) ? htmlspecialchars($data['userDetails']->batch) : '21' ?>
                        </div>
                    </div>
                    <div class="profile-bio">
                        <?= isset($data['userDetails']->bio) ? htmlspecialchars($data['userDetails']->bio) : 'User bio goes here' ?>
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
                        user-name="<?php echo htmlspecialchars($p->name ?? 'User'); ?>"
                        tag="@user<?php echo $p->user_id ?? ''; ?>"
                        post-time="<?php echo isset($p->created_at) ? date('M d', strtotime($p->created_at)) : ''; ?>"
                        post-content="<?php echo htmlspecialchars($p->content ?? ''); ?>"
                        post-img="<?php echo htmlspecialchars($p->image ?? ''); ?>"
                        like-count="<?php echo $p->likes ?? 0; ?>"
                        cmnt-count="<?php echo $p->comments ?? 0; ?>"
                        liked="<?php echo !empty($p->liked) ? 1 : 0; ?>"
                        post-id="<?php echo $p->id ?? ''; ?>"></post-card>
                <?php endforeach; else: ?>
                    <div class="no-posts-message">No posts yet.</div>
                <?php endif; ?>
            </div>

            <!-- Info Section: Certificates and Projects -->
            <div class="info-section" id="infoSection">
                <!-- Certificates Section with Action Buttons -->
                <div class="section-header">
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
                    // Sample certificate data - this would come from your database
                    $sampleCertificates = [
                        ['id' => 1, 'title' => 'Web Development Certificate', 'issuer' => 'Udemy', 'date' => '2024-05-15'],
                        ['id' => 2, 'title' => 'JavaScript Advanced', 'issuer' => 'Coursera', 'date' => '2023-11-20'],
                        ['id' => 3, 'title' => 'Full Stack Developer', 'issuer' => 'FreeCodeCamp', 'date' => '2024-02-08']
                    ];
                    
                    if(!empty($sampleCertificates)):
                        foreach($sampleCertificates as $cert):
                            $date = new DateTime($cert['date']);
                            $formattedDate = $date->format('F Y');
                    ?>
                    <div class="certificate-card" data-id="<?= $cert['id'] ?>">
                        <div class="certificate-card-image">
                            <i class="fas fa-certificate"></i>
                        </div>
                        <div class="certificate-details">
                            <div class="certificate-card-title"><?= htmlspecialchars($cert['title']) ?></div>
                            <div class="certificate-issuer"><?= htmlspecialchars($cert['issuer']) ?></div>
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
                    <?php if(!empty($data['projects'])): foreach($data['projects'] as $project): ?>
                        <div class="project-card">
                            <div class="project-card-image">
                                <i class="fas fa-project-diagram"></i>
                            </div>
                            <div class="project-card-title"><?= htmlspecialchars($project['title'] ?? '') ?></div>
                        </div>
                    <?php endforeach; else: ?>
                        <div>No projects added yet.</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Replace right panel with rightSideBar component -->
        <?php
        $rightSidebarStylesIncluded = true; // Prevent duplicate styles
        require APPROOT . '/views/inc/commponents/rightSideBar.php';
        ?>
    </div>

    <!-- Certificate Add Popup -->
    <div id="certificateAddPopup" class="certificate-add-popup">
        <div class="certificate-add">
            <button class="close-popup" title="Close">
                <i class="fas fa-times"></i>
            </button>
            
            <div class="form-title">Add New Certificate</div>
            
            <form class="certificate-form" id="certificateForm">
                <div class="form-group">
                    <label for="certificateName">Name</label>
                    <input type="text" id="certificateName" name="certificateName" placeholder="Certificate name" required>
                </div>
                
                <div class="form-group">
                    <label for="certificateIssuer">Issuing Organization</label>
                    <input type="text" id="certificateIssuer" name="certificateIssuer" placeholder="Organization name" required>
                </div>
                
                <div class="form-group">
                    <label for="certificateDate">Issue Date</label>
                    <input type="date" id="certificateDate" name="certificateDate" required>
                </div>
                
                <div class="form-group">
                    <label for="certificateFile">Upload Certificate (PDF)</label>
                    <div class="file-upload-container">
                        <input type="file" id="certificateFile" name="certificateFile" accept=".pdf" style="display: none;">
                        <button type="button" class="file-upload-btn" onclick="document.getElementById('certificateFile').click()">Choose File</button>
                        <span class="file-name" id="fileName">No file chosen</span>
                    </div>
                </div>
                
                <button type="submit" class="save-btn">Save Certificate</button>
            </form>
        </div>
    </div>

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

        // Certificate management
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize with posts tab active
            showTab('posts');
            
            // Set up certificate management
            const editCertificatesBtn = document.getElementById('editCertificatesBtn');
            const addCertificateBtn = document.getElementById('addCertificateBtn');
            const certificateCards = document.querySelectorAll('.certificate-card');
            
            // Toggle edit mode for certificates
            if (editCertificatesBtn) {
                editCertificatesBtn.addEventListener('click', function() {
                    const isInEditMode = certificateCards[0]?.classList.contains('edit-mode');
                    
                    certificateCards.forEach(card => {
                        if (isInEditMode) {
                            card.classList.remove('edit-mode');
                        } else {
                            card.classList.add('edit-mode');
                        }
                    });
                    
                    // Toggle edit button appearance
                    if (isInEditMode) {
                        editCertificatesBtn.style.backgroundColor = '';
                        editCertificatesBtn.style.color = '';
                    } else {
                        editCertificatesBtn.style.backgroundColor = 'var(--primary)';
                        editCertificatesBtn.style.color = 'var(--surface-0)';
                    }
                });
            }
            
            // Open add certificate popup
            if (addCertificateBtn) {
                addCertificateBtn.addEventListener('click', function() {
                    document.getElementById('certificateAddPopup').style.display = 'flex';
                });
            }
            
            // Setup certificate card action buttons
            certificateCards.forEach(card => {
                const editBtn = card.querySelector('.edit-btn');
                const deleteBtn = card.querySelector('.delete-btn');
                
                if (editBtn) {
                    editBtn.addEventListener('click', function() {
                        const cardId = card.dataset.id;
                        console.log('Edit certificate:', cardId);
                        // Future implementation for editing
                    });
                }
                
                if (deleteBtn) {
                    deleteBtn.addEventListener('click', function() {
                        const cardId = card.dataset.id;
                        if (confirm('Are you sure you want to delete this certificate?')) {
                            console.log('Delete certificate:', cardId);
                            // Future implementation for deletion
                            card.remove();
                        }
                    });
                }
            });
            
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
                    console.log('Certificate form submitted - will be implemented later');
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
            
            // Keep the profile edit button, but remove functionality for now
            const profileEditBtn = document.querySelector('.profile-edit-btn');
            if (profileEditBtn) {
                profileEditBtn.addEventListener('click', function() {
                    console.log('Profile edit functionality will be implemented later');
                    // Alert user that this feature is coming soon
                    alert('Profile edit feature will be available soon.');
                });
            }
        });
    </script>
</body>
</html>