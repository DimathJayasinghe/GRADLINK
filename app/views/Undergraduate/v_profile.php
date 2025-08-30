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

        .profile > .profile-down-part > .profile-name {
            padding-top: 40px;
            font-weight: bold;
            font-size: 1.5em;
            text-align: center;
            color: var(--text);
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
        }

        .project-card-title,
        .certificate-card-title {
            font-weight: bold;
            color: var(--text);
        }

        /* Profile Edit Popup Styles */
        .profile-edit-popup {
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

        .profile-edit-popup .profile-edit {
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

        .close-edit-popup {
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

        .close-edit-popup:hover {
            background-color: var(--primary);
            color: var(--text);
        }

        /* Section title styling */
        .section-title {
            font-weight: bold;
            font-size: 1.2em;
            color: var(--text);
            margin-bottom: 15px;
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
                gap: 60px; /* Slightly reduce gap on smaller screens */
            }
            
            .nav-button {
                width: 120px;
            }
        }

        @media (max-width: 576px) {
            .profile {
                height: 230px;
            }
            
            .profile > .profile-down-part > .profile-name {
                font-size: 1.2em;
            }
            
            .profile > .profile-down-part > .profile-bio {
                font-size: 0.8em;
            }
            
            .profile-navigation {
                gap: 40px; /* Further reduce gap on mobile */
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
                    <div class="profile-name">
                        <?= isset($data['userDetails']['name']) ? htmlspecialchars($data['userDetails']['name']) : 'User Name' ?>
                    </div>
                    <div class="profile-bio">
                        <?= isset($data['userDetails']['bio']) ? htmlspecialchars($data['userDetails']['bio']) : 'User bio goes here' ?>
                    </div>
                </div>
            </div>

            <!-- Updated Navigation Buttons (removed icons) -->
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
                <div class="section-title">Certificates</div>
                <?php if(!empty($data['certificates'])): foreach($data['certificates'] as $certificate): ?>
                    <div class="certificate-card">
                        <div class="certificate-card-image"></div>
                        <div class="certificate-card-title"><?= htmlspecialchars($certificate['title'] ?? '') ?></div>
                    </div>
                <?php endforeach; else: ?>
                    <div>No certificates added yet.</div>
                <?php endif; ?>

                <div class="section-title" style="margin-top:1.5em;">Projects</div>
                <?php if(!empty($data['projects'])): foreach($data['projects'] as $project): ?>
                    <div class="project-card">
                        <div class="project-card-image"></div>
                        <div class="project-card-title"><?= htmlspecialchars($project['title'] ?? '') ?></div>
                    </div>
                <?php endforeach; else: ?>
                    <div>No projects added yet.</div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Replace right panel with rightSideBar component -->
        <?php
        $rightSidebarStylesIncluded = true; // Prevent duplicate styles
        require APPROOT . '/views/inc/commponents/rightSideBar.php';
        ?>
    </div>

    <!-- Profile Edit Popup Container -->
    <div id="profileEditPopup" class="profile-edit-popup">
        <!-- Content will be loaded here -->
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

        // Initialize with posts tab active
        document.addEventListener('DOMContentLoaded', function() {
            showTab('posts');
        });

        // Add the event listener to the edit button
        document.querySelector('.profile-edit-btn').addEventListener('click', openProfileEdit);

        // Function to open profile edit popup
        function openProfileEdit() {
            // Get the popup container
            const popup = document.getElementById('profileEditPopup');
            
            // Show the popup with fade-in effect
            popup.style.display = 'flex';
            
            // Load the profile edit content via AJAX
            fetch(`${window.URLROOT}/Undergraduate/getProfileEditForm`)
                .then(response => response.text())
                .then(html => {
                    // Insert the content into the popup
                    popup.innerHTML = html;
                    
                    // Add close button event listener
                    if (popup.querySelector('.close-edit-popup')) {
                        popup.querySelector('.close-edit-popup').addEventListener('click', closeProfileEdit);
                    }
                    
                    // Add save button event listener
                    if (popup.querySelector('.save-changes-btn')) {
                        popup.querySelector('.save-changes-btn').addEventListener('click', saveProfileChanges);
                    }
                })
                .catch(error => {
                    console.error('Error loading profile edit form:', error);
                });
        }

        // Function to close profile edit popup
        function closeProfileEdit() {
            const popup = document.getElementById('profileEditPopup');
            popup.style.display = 'none';
        }
        
        // Function to save profile changes
        function saveProfileChanges() {
            // Get form data
            const name = document.querySelector('.name textarea').value;
            const bio = document.querySelector('.bio textarea').value;
            
            // Create form data object
            const formData = new FormData();
            formData.append('name', name);
            formData.append('bio', bio);
            
            // Send AJAX request to save changes
            fetch(`${window.URLROOT}/Undergraduate/updateProfile`, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update profile information without page reload
                    document.querySelector('.profile-name').textContent = name;
                    document.querySelector('.profile-bio').textContent = bio;
                    
                    // Close the popup
                    closeProfileEdit();
                } else {
                    alert('Failed to update profile: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error updating profile:', error);
            });
        }
    </script>
</body>
</html>