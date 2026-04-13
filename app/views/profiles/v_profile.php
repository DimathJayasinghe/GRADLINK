<?php ob_start() ?>
<title>Alumni Profile</title>
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/style.css">
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/color-pallate.css">
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/components/postCardStyles.css">
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/profile_styles.css"> <!-- Import profile specific styles -->
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/mainfeed_styles.css"> <!-- Import main feed styles -->
<?php $styles = ob_get_clean(); ?>

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
        ['icon' => 'home', 'label' => 'Home', 'onclick' => "window.location.href='" . URLROOT . "/mainfeed'"],
        ['icon' => 'search', 'label' => 'Explore', 'onclick' => "window.location.href='" . URLROOT . "/explore'"],
        ['icon' => 'bell', 'label' => 'Notifications', 'onclick' => "NotificationModal()", 'require' => APPROOT . '/views/inc/commponents/notification_pop_up.php', 'badge' => true],
        ['icon' => 'envelope', 'label' => 'Messages', 'onclick' => "window.location.href='" . URLROOT . "/messages'"],
        ['icon' => 'user', 'label' => 'Profile', 'onclick' => "window.location.href='" . URLROOT . "/profile?userid=" . $_SESSION['user_id'] . "'", 'active' => true],
        // icon for fundraiser
        ['icon' => 'hand-holding-heart', 'label' => 'Fundraisers', 'onclick' => "window.location.href='" . URLROOT . "/fundraiser'"],
        //icon for post requests
        ['icon' => 'clipboard-list', 'label' => 'Event Requests', 'onclick' => "window.location.href='" . URLROOT . "/eventrequest/'"],
        ['icon' => 'calendar-alt', 'label' => 'Calender', 'onclick' => "window.location.href='" . URLROOT . "/calender'"],
    ];
    //  new portal to approve new alumnis only available for special alumnis
    if ($_SESSION['special_alumni']) {
        $leftside_buttons[] = [
            'icon' => 'user-check',
            'label' => 'Approve Alumni',
            'onclick' => "window.location.href='" . URLROOT . "/alumni/approve'"
        ];
    };
    $leftside_buttons[] = ['icon' => 'cog', 'label' => 'Settings', 'onclick' => "window.location.href='" . URLROOT . "/settings'"];
    require APPROOT . '/views/inc/commponents/leftSideBar.php'; ?>
<?php $leftsidebar = ob_get_clean(); ?>


<?php ob_start() ?>
<div class="main-content">
    
    <!-- Profile Section -->
    <?php require APPROOT . '/views/profiles/partials/sections/profile.php'; ?>

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
    if ($isOwner) {
        require APPROOT . '/views/inc/commponents/newpost_section.php';
    } ?>

    <!-- Posts Section -->
    <?php require APPROOT . '/views/profiles/partials/sections/post.php'; ?>

    <!-- Info Section: Certificates, Work Experience and Projects -->
    <div class="info-section" id="infoSection">
        <!-- Work Experience Section -->
        <div class="section-header">
            <div class="section-title">Work Experience</div>
            <?php if ($isOwner) {
                echo '
                        <div class="section-actions">
                        <div class="section-action-btn" id="editWorkBtn" title="Edit Work Experience">
                            <i class="fas fa-pencil-alt"></i>
                        </div>
                        <div class="section-action-btn" id="addWorkBtn" title="Add Work Experience">
                            <i class="fas fa-plus"></i>
                        </div>
                        </div>';
            } ?>

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

            if (!empty($sampleWork)):
                foreach ($sampleWork as $work):
            ?>
                    <div class="certificate-card work-card" data-id="<?= $work['id'] ?>" data-title="<?= htmlspecialchars($work['title']) ?>" data-company="<?= htmlspecialchars($work['company']) ?>" data-period="<?= htmlspecialchars($work['period']) ?>">
                        <div class="certificate-card-image">
                            <i class="fas fa-briefcase"></i>
                        </div>
                        <div class="certificate-details">
                            <div class="certificate-card-title"><?= htmlspecialchars($work['title']) ?></div>
                            <div class="certificate-issuer"><?= htmlspecialchars($work['company']) ?></div>
                            <div class="certificate-date"><?= htmlspecialchars($work['period']) ?></div>
                        </div>
                        <?php if ($isOwner) {
                            echo '<div class="certificate-actions">
                            <div class="certificate-action-btn edit-btn" title="Edit Work Experience">
                                <i class="fas fa-pencil-alt"></i>
                            </div>
                            <div class="certificate-action-btn delete-btn" title="Delete Work Experience">
                                <i class="fas fa-trash-alt"></i>
                            </div>
                        </div>';
                        } ?>

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
            <?php if ($isOwner) {
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
            } ?>

        </div>

        <!-- Certificate Cards -->
        <div id="certificatesContainer">
            <?php
            if (!empty($data['certificates'])):
                foreach ($data['certificates'] as $cert):
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
                        <!-- Always-visible View button -->
                        <div class="certificate-view-btn-wrapper" style="display:flex; gap:8px; align-items:center;">
                            <div class="certificate-action-btn view-btn" title="View Certificate"><i class="fas fa-eye"></i></div>
                        </div>
                        <?php if ($isOwner): ?>
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
            <?php if ($isOwner) {
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
            } ?>

        </div>

        <div id="projectsContainer">
            <?php
            // Sample project data
            $sampleProjects = [
                ['id' => 1, 'title' => 'AI-Powered Healthcare App'],
                ['id' => 2, 'title' => 'Smart Home Automation System']
            ];

            if (!empty($sampleProjects)): foreach ($sampleProjects as $project): ?>
                    <div class="project-card" data-id="<?= $project['id'] ?>" data-title="<?= htmlspecialchars($project['title']) ?>">
                        <div class="project-card-image">
                            <i class="fas fa-project-diagram"></i>
                        </div>
                        <div class="project-card-title"><?= htmlspecialchars($project['title']) ?></div>
                        <?php if ($isOwner) {
                            echo '<div class="certificate-actions">
                                <div class="certificate-action-btn edit-btn" title="Edit Project">
                                    <i class="fas fa-pencil-alt"></i>
                                </div>
                                <div class="certificate-action-btn delete-btn" title="Delete Project">
                                    <i class="fas fa-trash-alt"></i>
                                </div>
                            </div>';
                        } ?>

                    </div>
                <?php endforeach;
            else: ?>
                <div>No projects added yet.</div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php
// Hidden form for POST-based navigation to Messages with target user id
require_once APPROOT . '/helpers/Csrf.php';
?>
<form id="profileMessageForm" method="post" action="<?= URLROOT; ?>/messages" style="display:none;">
    <input type="hidden" name="user" id="profileMessageUserId" value="">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(Csrf::getToken(), ENT_QUOTES); ?>">
</form>

<!-- Profile Popup -->
<?php require APPROOT . '/views/profiles/partials/popups/profile.php'; ?>

<!-- Work Experience Popup -->
<?php require APPROOT . '/views/profiles/partials/popups/work_experience.php'; ?>

<!-- Projects Popup -->
<?php require APPROOT . '/views/profiles/partials/popups/projects.php'; ?>

<!-- Certificates Popup -->
<?php require APPROOT . '/views/profiles/partials/popups/certificates.php'; ?>

<?php $center_content = ob_get_clean(); ?>
<?php ob_start() ?>
<!-- Include the right sidebar component -->
<?php
    $rightSidebarStylesIncluded = true; // Prevent duplicate styles
    require APPROOT . '/views/inc/commponents/rightSideBar.php';
?>
<?php $rightsidebar = ob_get_clean(); ?>

<script>window.URLROOT = "<?= URLROOT; ?>";</script>
<script defer src="<?php echo URLROOT ?>/js/component/postCard.js"></script>
<script defer src="<?php echo URLROOT ?>/js/profile/index.js"></script>

<?php ob_start() ?>

<script>
    window.URLROOT = "<?php echo URLROOT; ?>";
</script>
<script src="<?php echo URLROOT ?>/js/component/postCard.js"></script>
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
        setupEditModeForSection('editWorkBtn', 'workContainer', 'work-card');
        setupEditModeForSection('editCertificatesBtn', 'certificatesContainer', 'certificate-card');
        setupEditModeForSection('editProjectsBtn', 'projectsContainer', 'project-card');

        // Setup card action buttons for all card types
        setupCardActionButtons('.work-card');
        setupCardActionButtons('.project-card');

        // No-op: certificate handlers are implemented in the later script block per-form

        // Profile edit button -> open profile popup
        const profileEditBtn = document.querySelector('.profile-edit-btn');
        if (profileEditBtn) {
            profileEditBtn.addEventListener('click', function() {
                const popup = document.getElementById('editProfilePopup');
                if (!popup) return;
                const bioEl = document.getElementById('profileBioEl');
                const bioInput = document.getElementById('profileBioInput');
                if (bioEl && bioInput) bioInput.value = bioEl.textContent.trim();
                const preview = document.getElementById('profileImagePreview');
                const img = document.getElementById('profileImageEl');
                if (preview && img) preview.src = img.src;
                popup.style.display = 'flex';
            });
        }

        // Add fundraising section to the existing right sidebar
        // Follow button behavior
        const connectBtn = document.getElementById('connectBtn');
        if (connectBtn) {
            connectBtn.addEventListener('click', async function() {
                const targetId = this.getAttribute('data-user-id');
                if (!targetId) return;
                if (this.disabled) return;
                this.disabled = true;
                const originalText = this.querySelector('span').textContent;
                this.querySelector('span').textContent = '...';
                try {
                    const res = await fetch(`<?php echo URLROOT ?>/profile/follow`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({ profile_user_id: targetId })
                    });
                    const json = await res.json();
                    if (json && json.success) {
                        const action = json.action || '';
                        const connected = json.connected;
                        
                        if (action === 'requested') {
                            // Pending state
                            this.setAttribute('data-pending', '1');
                            this.setAttribute('data-connected', '0');
                            this.classList.remove('active');
                            this.classList.add('pending');
                            this.querySelector('span').textContent = 'Pending';
                            this.querySelector('i').className = 'fas fa-clock';
                        } else if (action === 'cancelled') {
                            // Back to follow state
                            this.setAttribute('data-pending', '0');
                            this.setAttribute('data-connected', '0');
                            this.classList.remove('active', 'pending');
                            this.querySelector('span').textContent = 'Follow';
                            this.querySelector('i').className = 'fas fa-user-plus';
                        } else if (action === 'unfollowed') {
                            // Unfollowed state
                            this.setAttribute('data-connected', '0');
                            this.setAttribute('data-pending', '0');
                            this.classList.remove('active', 'pending');
                            this.querySelector('span').textContent = 'Follow';
                            this.querySelector('i').className = 'fas fa-user-plus';
                        } else {
                            // Following state (fallback)
                            const isConnected = !!connected && connected !== 'pending';
                            this.setAttribute('data-connected', isConnected ? '1' : '0');
                            this.setAttribute('data-pending', '0');
                            this.classList.toggle('active', isConnected);
                            this.classList.remove('pending');
                            this.querySelector('span').textContent = isConnected ? 'Following' : 'Follow';
                            this.querySelector('i').className = isConnected ? 'fas fa-user-check' : 'fas fa-user-plus';
                        }
                    } else {
                        alert((json && json.error) || 'Failed to update follow status');
                        this.querySelector('span').textContent = originalText;
                    }
                } catch (err) {
                    console.error(err);
                    alert('Network error updating follow status');
                    this.querySelector('span').textContent = originalText;
                } finally {
                    this.disabled = false;
                }
            });
        }

        // Message button behavior - submit POST form to open conversation without query params
        const messageBtn = document.getElementById('messageBtn');
        if (messageBtn) {
            messageBtn.addEventListener('click', function() {
                const targetId = this.getAttribute('data-user-id');
                const form = document.getElementById('profileMessageForm');
                const input = document.getElementById('profileMessageUserId');
                if (form && input) {
                    input.value = targetId || '';
                    form.submit();
                } else {
                    // Fallback to GET navigation if form is missing for any reason
                    window.location.href = `<?php echo URLROOT ?>/messages?user=${encodeURIComponent(targetId)}`;
                }
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
        // Keep generic handlers for non-certificate cards only
        const cards = document.querySelectorAll(cardSelector);
        cards.forEach(card => {
            if (card.classList.contains('certificate-card')) return; // certificate has custom logic
            const editBtn = card.querySelector('.edit-btn');
            const deleteBtn = card.querySelector('.delete-btn');
            if (editBtn) {
                editBtn.addEventListener('click', function() {
                    const cardId = card.dataset.id;
                    console.log('Edit item:', cardId);
                });
            }
            if (deleteBtn) {
                deleteBtn.addEventListener('click', function() {
                    const cardId = card.dataset.id;
                    if (confirm('Are you sure you want to delete this item?')) {
                        card.remove();
                    }
                });
            }
        });
    }
</script>
<script>
    // Certificate Preview Modal logic (uses explicit View button)
    (function() {
        const container = document.getElementById('certificatesContainer');
        const modal = document.getElementById('certificatePreviewModal');
        const iframe = document.getElementById('certificatePreviewFrame');
        const titleEl = document.getElementById('certificatePreviewTitle');
        if (!container || !modal || !iframe) return;

        function openCertPreview(card) {
            if (!card) return;
            const file = card.dataset.file || '';
            if (!file) return;
            if (titleEl) {
                const nm = card.dataset.name || 'Certificate Preview';
                titleEl.textContent = nm;
            }
            iframe.src = <?php echo URLROOT?> + `/media/certificate/${encodeURIComponent(file)}`;
            modal.style.display = 'flex';
        }

        // open on View button click only
        container.addEventListener('click', function(e) {
            const viewBtn = e.target.closest('.certificate-card .view-btn');
            if (!viewBtn) return;
            e.preventDefault();
            e.stopPropagation();
            const card = viewBtn.closest('.certificate-card');
            openCertPreview(card);
        });

        // Also bind directly like edit button mechanism for robustness
        document.querySelectorAll('#certificatesContainer .certificate-card .view-btn').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                const card = this.closest('.certificate-card');
                openCertPreview(card);
            });
        });

        // close button
        modal.querySelector('.close-popup')?.addEventListener('click', function() {
            modal.style.display = 'none';
            iframe.src = 'about:blank';
        });

        // optional: close on backdrop click
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                modal.style.display = 'none';
                iframe.src = 'about:blank';
            }
        });
    })();
    // --- New: per-form element references ---
    const addCertificateBtn = document.getElementById('addCertificateBtn'); // existing in page
    const addCertificatePopup = document.getElementById('addCertificatePopup');
    const addCertificateForm = document.getElementById('addCertificateForm');
    const chooseFileBtnAdd = document.getElementById('chooseFileBtnAdd');
    const certificateFileAdd = document.getElementById('certificateFileAdd');
    const fileNameAdd = document.getElementById('fileNameAdd');
    const saveNewBtnAdd = document.getElementById('saveNewBtnAdd');
    const certAddTooLarge = document.getElementById('certAddTooLarge');

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
    const saveChangesBtnEdit = document.getElementById('saveChangesBtnEdit');
    const certEditTooLarge = document.getElementById('certEditTooLarge');
    const CERT_MAX_SIZE = 5 * 1024 * 1024; // 5MB

    // close buttons for both popups (reuse existing selector)
    document.querySelectorAll('.certificate-add .close-popup').forEach(btn => {
        btn.addEventListener('click', function() {
            const popup = this.closest('.certificate-add-popup');
            if (popup) popup.style.display = 'none';
        });
    });

    // Open add popup
    if (addCertificateBtn && addCertificatePopup) {
        addCertificateBtn.addEventListener('click', function() {
            // clear add form
            addCertificateForm.reset();
            fileNameAdd.textContent = 'No file chosen';
            if (chooseFileBtnAdd) chooseFileBtnAdd.style.display = 'inline-block';
            if (certAddTooLarge) certAddTooLarge.style.display = 'none';
            if (saveNewBtnAdd) {
                saveNewBtnAdd.disabled = false;
                saveNewBtnAdd.removeAttribute('title');
            }
            addCertificatePopup.style.display = 'flex';
        });
    }

    // Edit button behavior: populate edit form and open edit popup
    document.querySelectorAll('#certificatesContainer .certificate-card .edit-btn').forEach(btn => {
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
                currentFileLinkEdit.href = window.URLROOT + '/storage/certificates/' + file;
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
        cutFileBtnEdit.addEventListener('click', function() {
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
                if (f.size > CERT_MAX_SIZE) {
                    if (certEditTooLarge) certEditTooLarge.style.display = 'inline';
                    if (saveChangesBtnEdit) {
                        saveChangesBtnEdit.disabled = true;
                        saveChangesBtnEdit.title = 'Certificate exceeds 5MB';
                    }
                } else {
                    if (certEditTooLarge) certEditTooLarge.style.display = 'none';
                    if (saveChangesBtnEdit) {
                        saveChangesBtnEdit.disabled = false;
                        saveChangesBtnEdit.removeAttribute('title');
                    }
                }
            } else {
                if (certEditTooLarge) certEditTooLarge.style.display = 'none';
                if (saveChangesBtnEdit) {
                    saveChangesBtnEdit.disabled = false;
                    saveChangesBtnEdit.removeAttribute('title');
                }
            }
        });
    }

    // Submit handlers (use fetch like before, per form)
    // Reflect selected file name in Add form
    if (certificateFileAdd) {
        certificateFileAdd.addEventListener('change', function() {
            const f = this.files[0];
            if (fileNameAdd) fileNameAdd.textContent = f ? f.name : 'No file chosen';
            if (f) {
                if (f.size > CERT_MAX_SIZE) {
                    if (certAddTooLarge) certAddTooLarge.style.display = 'inline';
                    if (saveNewBtnAdd) {
                        saveNewBtnAdd.disabled = true;
                        saveNewBtnAdd.title = 'Certificate exceeds 5MB';
                    }
                } else {
                    if (certAddTooLarge) certAddTooLarge.style.display = 'none';
                    if (saveNewBtnAdd) {
                        saveNewBtnAdd.disabled = false;
                        saveNewBtnAdd.removeAttribute('title');
                    }
                }
            } else {
                if (certAddTooLarge) certAddTooLarge.style.display = 'none';
                if (saveNewBtnAdd) {
                    saveNewBtnAdd.disabled = false;
                    saveNewBtnAdd.removeAttribute('title');
                }
            }
        });
    }
    if (addCertificateForm) {
        addCertificateForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const f = certificateFileAdd && certificateFileAdd.files ? certificateFileAdd.files[0] : null;
            if (f && f.size > CERT_MAX_SIZE) {
                alert('Certificate file exceeds 5MB');
                return;
            }
            const fd = new FormData(this);
            fetch(this.action, {
                    method: 'POST',
                    body: fd,
                    headers: {
                        'Accept': 'application/json'
                    }
                })
                .then(r => r.json()).then(json => {
                    if (json.success) {
                        // show uploaded original filename if provided
                        window.location.reload();
                    } else {
                        alert(json.error || 'Failed to save certificate');
                    }
                }).catch(() => alert('Error while saving certificate'));
        });
    }

    if (editCertificateForm) {
        editCertificateForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const f = certificateFileEdit && certificateFileEdit.files ? certificateFileEdit.files[0] : null;
            if (f && f.size > CERT_MAX_SIZE) {
                alert('Certificate file exceeds 5MB');
                return;
            }
            const fd = new FormData(this);
            fetch(this.action, {
                    method: 'POST',
                    body: fd,
                    headers: {
                        'Accept': 'application/json'
                    }
                })
                .then(r => r.json()).then(json => {
                    if (json.success) {
                        // if server returned original uploaded name, show it
                        window.location.reload();
                    } else {
                        alert(json.error || 'Failed to update certificate');
                    }
                }).catch(() => alert('Error while updating certificate'));
        });
    }

    // New: delete-certificate popup logic
    (function() {
        const deletePopup = document.getElementById('deleteCertificatePopup');
        const confirmDeleteBtn = document.getElementById('confirmDeleteCertBtn');
        const cancelDeleteBtn = document.getElementById('cancelDeleteCertBtn');
        let pendingDeleteCertId = null;
        // open delete popup when any certificate-card delete-btn clicked
        document.querySelectorAll('#certificatesContainer .certificate-card .delete-btn').forEach(btn => {
            btn.addEventListener('click', function(e) {
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
        document.querySelectorAll('.certificate-add .close-popup').forEach(btn => {
            btn.addEventListener('click', function() {
                const popup = this.closest('.certificate-add-popup');
                if (popup) popup.style.display = 'none';
            });
        });

        if (cancelDeleteBtn) {
            cancelDeleteBtn.addEventListener('click', function() {
                pendingDeleteCertId = null;
                if (deletePopup) deletePopup.style.display = 'none';
            });
        }

        if (confirmDeleteBtn) {
            confirmDeleteBtn.addEventListener('click', async function() {
                if (!pendingDeleteCertId) return;
                try {
                    const url = '<?php echo URLROOT; ?>/profile/deleteCertificate?id=' + encodeURIComponent(pendingDeleteCertId);
                    const res = await fetch(url, {
                        method: 'DELETE',
                        credentials: 'same-origin',
                        headers: {
                            'Accept': 'application/json'
                        }
                    });
                    const ct = res.headers.get('content-type') || '';
                    if (!ct.includes('application/json')) {
                        const text = await res.text();
                        console.error('Non-JSON response:', text);
                        alert('Failed to delete certificate: unexpected server response');
                        return;
                    }
                    const json = await res.json();
                    if (json && json.success) {
                        // Refresh to ensure server state is reflected in UI
                        window.location.reload();
                    } else {
                        alert((json && json.error) || 'Failed to delete certificate');
                    }
                } catch (err) {
                    console.error(err);
                    alert('Error while deleting certificate: ' + (err && err.message ? err.message : 'unknown error'));
                }
            });
        }
    })();

    // Profile Edit Handlers (visual only)
    (function() {
        const popup = document.getElementById('editProfilePopup');
        if (!popup) return; // only for owner
        const form = document.getElementById('editProfileForm');
        const chooseBtn = document.getElementById('chooseProfileImgBtn');
        const fileInput = document.getElementById('profileImageInput');
        const fileName = document.getElementById('profileImgFileName');
        const preview = document.getElementById('profileImagePreview');
        const pageImg = document.getElementById('profileImageEl');
        const bioInput = document.getElementById('profileBioInput');
        const bioEl = document.getElementById('profileBioEl');

        if (chooseBtn && fileInput) {
            chooseBtn.addEventListener('click', () => fileInput.click());
        }
        if (fileInput) {
            fileInput.addEventListener('change', function() {
                const f = this.files && this.files[0];
                fileName.textContent = f ? f.name : 'No file chosen';
                if (f) {
                    const reader = new FileReader();
                    reader.onload = e => {
                        preview.src = e.target.result;
                    };
                    reader.readAsDataURL(f);
                }
            });
        }
        if (form) {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                // visually update page image and bio
                if (pageImg && preview) pageImg.src = preview.src;
                if (bioEl && bioInput) bioEl.textContent = bioInput.value || '';
                // close popup
                const closeBtn = popup.querySelector('.close-popup');
                if (closeBtn) closeBtn.click();
                else popup.style.display = 'none';
            });
        }
    })();

    // Work Experience and Projects Popups (visual only)
    (function() {
        // Work Add
        const addWorkBtn = document.getElementById('addWorkBtn');
        const addWorkPopup = document.getElementById('addWorkPopup');
        const addWorkForm = document.getElementById('addWorkForm');
        const workContainer = document.getElementById('workContainer');
        if (addWorkBtn && addWorkPopup) {
            addWorkBtn.addEventListener('click', () => {
                addWorkForm && addWorkForm.reset();
                addWorkPopup.style.display = 'flex';
            });
        }
        if (addWorkForm && workContainer) {
            addWorkForm.addEventListener('submit', function(e) {
                e.preventDefault();
                const title = document.getElementById('workTitleAdd').value.trim();
                const company = document.getElementById('workCompanyAdd').value.trim();
                const period = document.getElementById('workPeriodAdd').value.trim();
                if (!title || !company || !period) return;
                const id = Date.now();
                const card = document.createElement('div');
                card.className = 'certificate-card work-card';
                card.setAttribute('data-id', String(id));
                card.setAttribute('data-title', title);
                card.setAttribute('data-company', company);
                card.setAttribute('data-period', period);
                card.innerHTML = `
                <div class="certificate-card-image"><i class="fas fa-briefcase"></i></div>
                <div class="certificate-details">
                    <div class="certificate-card-title"></div>
                    <div class="certificate-issuer"></div>
                    <div class="certificate-date"></div>
                </div>
                <div class="certificate-actions">
                    <div class="certificate-action-btn edit-btn" title="Edit Work Experience"><i class="fas fa-pencil-alt"></i></div>
                    <div class="certificate-action-btn delete-btn" title="Delete Work Experience"><i class="fas fa-trash-alt"></i></div>
                </div>`;
                card.querySelector('.certificate-card-title').textContent = title;
                card.querySelector('.certificate-issuer').textContent = company;
                card.querySelector('.certificate-date').textContent = period;
                workContainer.appendChild(card);
                bindWorkCardActions(card);
                addWorkPopup.style.display = 'none';
            });
        }

        // Work Edit
        const editWorkPopup = document.getElementById('editWorkPopup');
        const editWorkForm = document.getElementById('editWorkForm');

        function bindWorkCardActions(card) {
            const editBtn = card.querySelector('.edit-btn');
            const deleteBtn = card.querySelector('.delete-btn');
            if (editBtn) editBtn.addEventListener('click', function() {
                const id = card.dataset.id || '';
                document.getElementById('workIdEdit').value = id;
                document.getElementById('workTitleEdit').value = card.dataset.title || '';
                document.getElementById('workCompanyEdit').value = card.dataset.company || '';
                document.getElementById('workPeriodEdit').value = card.dataset.period || '';
                editWorkPopup.style.display = 'flex';
            });
            if (deleteBtn) deleteBtn.addEventListener('click', function() {
                // if (confirm('Are you sure you want to delete this work experience?')) card.remove();
            });
        }
        // bind existing
        document.querySelectorAll('#workContainer .work-card').forEach(bindWorkCardActions);
        if (editWorkForm) {
            editWorkForm.addEventListener('submit', function(e) {
                e.preventDefault();
                const id = document.getElementById('workIdEdit').value;
                const title = document.getElementById('workTitleEdit').value.trim();
                const company = document.getElementById('workCompanyEdit').value.trim();
                const period = document.getElementById('workPeriodEdit').value.trim();
                const card = document.querySelector(`#workContainer .work-card[data-id="${CSS.escape(id)}"]`);
                if (card) {
                    card.dataset.title = title;
                    card.dataset.company = company;
                    card.dataset.period = period;
                    card.querySelector('.certificate-card-title').textContent = title;
                    card.querySelector('.certificate-issuer').textContent = company;
                    card.querySelector('.certificate-date').textContent = period;
                }
                editWorkPopup.style.display = 'none';
            });
        }

        // Project Add
        const addProjectBtn = document.getElementById('addProjectBtn');
        const addProjectPopup = document.getElementById('addProjectPopup');
        const addProjectForm = document.getElementById('addProjectForm');
        const projectsContainer = document.getElementById('projectsContainer');
        if (addProjectBtn && addProjectPopup) {
            addProjectBtn.addEventListener('click', () => {
                addProjectForm && addProjectForm.reset();
                addProjectPopup.style.display = 'flex';
            });
        }
        if (addProjectForm && projectsContainer) {
            addProjectForm.addEventListener('submit', function(e) {
                e.preventDefault();
                const title = document.getElementById('projectTitleAdd').value.trim();
                if (!title) return;
                const id = Date.now();
                const card = document.createElement('div');
                card.className = 'project-card';
                card.setAttribute('data-id', String(id));
                card.setAttribute('data-title', title);
                card.innerHTML = `
                <div class="project-card-image"><i class="fas fa-project-diagram"></i></div>
                <div class="project-card-title"></div>
                <div class="certificate-actions">
                    <div class="certificate-action-btn edit-btn" title="Edit Project"><i class="fas fa-pencil-alt"></i></div>
                    <div class="certificate-action-btn delete-btn" title="Delete Project"><i class="fas fa-trash-alt"></i></div>
                </div>`;
                card.querySelector('.project-card-title').textContent = title;
                projectsContainer.appendChild(card);
                bindProjectCardActions(card);
                addProjectPopup.style.display = 'none';
            });
        }

        // Project Edit
        const editProjectPopup = document.getElementById('editProjectPopup');
        const editProjectForm = document.getElementById('editProjectForm');

        function bindProjectCardActions(card) {
            const editBtn = card.querySelector('.edit-btn');
            const deleteBtn = card.querySelector('.delete-btn');
            if (editBtn) editBtn.addEventListener('click', function() {
                const id = card.dataset.id || '';
                document.getElementById('projectIdEdit').value = id;
                document.getElementById('projectTitleEdit').value = card.dataset.title || card.querySelector('.project-card-title')?.textContent || '';
                editProjectPopup.style.display = 'flex';
            });
            if (deleteBtn) deleteBtn.addEventListener('click', function() {
                if (confirm('Are you sure you want to delete this project?')) card.remove();
            });
        }
        // bind existing
        document.querySelectorAll('#projectsContainer .project-card').forEach(bindProjectCardActions);
        if (editProjectForm) {
            editProjectForm.addEventListener('submit', function(e) {
                e.preventDefault();
                const id = document.getElementById('projectIdEdit').value;
                const title = document.getElementById('projectTitleEdit').value.trim();
                const card = document.querySelector(`#projectsContainer .project-card[data-id="${CSS.escape(id)}"]`);
                if (card) {
                    card.dataset.title = title;
                    const titleEl = card.querySelector('.project-card-title');
                    if (titleEl) titleEl.textContent = title;
                }
                editProjectPopup.style.display = 'none';
            });
        }
    })();

    // Delegated handler to ensure Project edit popup always opens
    (function() {
        const container = document.getElementById('projectsContainer');
        const editProjectPopup = document.getElementById('editProjectPopup');
        if (!container || !editProjectPopup) return;
        container.addEventListener('click', function(e) {
            const btn = e.target.closest('.project-card .edit-btn');
            if (!btn) return;
            const card = btn.closest('.project-card');
            if (!card) return;
            const id = card.dataset.id || '';
            const title = card.dataset.title || card.querySelector('.project-card-title')?.textContent || '';
            const idInput = document.getElementById('projectIdEdit');
            const titleInput = document.getElementById('projectTitleEdit');
            if (idInput) idInput.value = id;
            if (titleInput) titleInput.value = title;
            editProjectPopup.style.display = 'flex';
        });
    })();

    // Work/Project delete confirmation popups (visual only)
    (function() {
        // Work delete
        const workContainer = document.getElementById('workContainer');
        const deleteWorkPopup = document.getElementById('deleteWorkPopup');
        const confirmWorkBtn = document.getElementById('confirmDeleteWorkBtn');
        const cancelWorkBtn = document.getElementById('cancelDeleteWorkBtn');
        let pendingWorkCard = null;
        if (workContainer && deleteWorkPopup) {
            workContainer.addEventListener('click', function(e) {
                const btn = e.target.closest('.work-card .delete-btn');
                if (!btn) return;
                e.stopPropagation();
                pendingWorkCard = btn.closest('.work-card');
                deleteWorkPopup.style.display = 'flex';
            });
        }
        if (cancelWorkBtn) {
            cancelWorkBtn.addEventListener('click', function() {
                pendingWorkCard = null;
                if (deleteWorkPopup) deleteWorkPopup.style.display = 'none';
            });
        }
        if (confirmWorkBtn) {
            confirmWorkBtn.addEventListener('click', function() {
                if (pendingWorkCard) pendingWorkCard.remove();
                pendingWorkCard = null;
                if (deleteWorkPopup) deleteWorkPopup.style.display = 'none';
            });
        }

        // Project delete
        const projectsContainer = document.getElementById('projectsContainer');
        const deleteProjectPopup = document.getElementById('deleteProjectPopup');
        const confirmProjectBtn = document.getElementById('confirmDeleteProjectBtn');
        const cancelProjectBtn = document.getElementById('cancelDeleteProjectBtn');
        let pendingProjectCard = null;
        if (projectsContainer && deleteProjectPopup) {
            projectsContainer.addEventListener('click', function(e) {
                const btn = e.target.closest('.project-card .delete-btn');
                if (!btn) return;
                e.stopPropagation();
                pendingProjectCard = btn.closest('.project-card');
                deleteProjectPopup.style.display = 'flex';
            });
        }
        if (cancelProjectBtn) {
            cancelProjectBtn.addEventListener('click', function() {
                pendingProjectCard = null;
                if (deleteProjectPopup) deleteProjectPopup.style.display = 'none';
            });
        }
        if (confirmProjectBtn) {
            confirmProjectBtn.addEventListener('click', function() {
                if (pendingProjectCard) pendingProjectCard.remove();
                pendingProjectCard = null;
                if (deleteProjectPopup) deleteProjectPopup.style.display = 'none';
            });
        }

        // Allow using the small X close button for both popups
        document.querySelectorAll('#deleteWorkPopup .close-popup, #deleteProjectPopup .close-popup').forEach(btn => {
            btn.addEventListener('click', function() {
                const popup = this.closest('.certificate-add-popup');
                if (popup) popup.style.display = 'none';
            });
        });
    })();
</script>
<?php $scripts = ob_get_clean(); ?>

<?php require APPROOT . '/views/layouts/threeColumnLayout.php'; ?>