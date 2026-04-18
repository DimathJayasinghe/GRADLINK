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

    <?php require APPROOT . '/views/profiles/partials/sections/info.php'; ?>
</div>

<?php
// Hidden form for POST-based navigation to Messages with target user id
?>
<form id="profileMessageForm" method="post" action="<?= URLROOT; ?>/messages" style="display:none;">
    <input type="hidden" name="user" id="profileMessageUserId" value="">
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

<?php ob_start() ?>
    window.URLROOT = "<?php echo URLROOT; ?>";
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

        // No-op: certificate handlers are implemented in the later script block per-form

        // Profile/location edit button -> open profile popup
        const profileEditBtn = document.querySelector('.profile-edit-btn');
        const locationEditBtn = document.getElementById('editLocationBtn');

        const openProfilePopup = function() {
            const popup = document.getElementById('editProfilePopup');
            const form = document.getElementById('editProfileForm');
            if (!popup || !form) return;

            const bioInput = document.getElementById('profileBioInput');
            const tagInput = document.getElementById('profileTagInput');
            const batchInput = document.getElementById('profileBatchNoInput');
            const countryInput = document.getElementById('profileCountryInput');

            if (bioInput) bioInput.value = form.dataset.initialBio || '';
            if (tagInput) tagInput.value = form.dataset.initialTag || '';
            if (batchInput) batchInput.value = form.dataset.initialBatch || '';
            if (countryInput) countryInput.value = form.dataset.initialCountry || 'Sri Lanka';

            const fileInput = document.getElementById('profileImageInput');
            const fileName = document.getElementById('profileImgFileName');
            if (fileInput) fileInput.value = '';
            if (fileName) fileName.textContent = 'No file chosen';

            const preview = document.getElementById('profileImagePreview');
            const img = document.getElementById('profileImageEl');
            if (preview && img) preview.src = img.src;

            popup.style.display = 'flex';
        };

        if (profileEditBtn) {
            profileEditBtn.addEventListener('click', openProfilePopup);
        }
        if (locationEditBtn) {
            locationEditBtn.addEventListener('click', openProfilePopup);
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

        // Report profile behavior
        const reportProfileBtn = document.getElementById('reportProfileBtn');
        if (reportProfileBtn) {
            reportProfileBtn.addEventListener('click', function() {
                const targetId = this.getAttribute('data-user-id');
                if (!targetId) return;

                const overlayId = `profile-report-popup-${targetId}`;
                let overlay = document.getElementById(overlayId);

                if (!overlay) {
                    overlay = document.createElement('div');
                    overlay.id = overlayId;
                    overlay.className = 'certificate-add-popup';
                    overlay.style.display = 'none';
                    overlay.innerHTML = `
                        <div class="certificate-add" style="max-width:560px;">
                            <button class="close-popup" title="Close"><i class="fas fa-times"></i></button>
                            <div class="form-title">Report Profile</div>
                            <form class="certificate-form" id="profileReportForm-${targetId}" novalidate>
                                <div class="form-group">
                                    <label for="profileReportCategory-${targetId}">Category</label>
                                    <select id="profileReportCategory-${targetId}" required>
                                        <option value="" disabled selected>Select a category</option>
                                        <option>Fake account</option>
                                        <option>Impersonation</option>
                                        <option>Harassment or bullying</option>
                                        <option>Hate or abusive content</option>
                                        <option>Spam</option>
                                        <option>Other</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="profileReportDetails-${targetId}">Details (optional)</label>
                                    <textarea id="profileReportDetails-${targetId}" rows="4" placeholder="Add any details or context..." style="padding:10px;border-radius: var(--radius-lg);border:1px solid var(--border);background:var(--input);color:var(--text);"></textarea>
                                </div>
                                <div class="form-group">
                                    <label for="profileReportLink-${targetId}">Reference link (optional)</label>
                                    <input type="url" id="profileReportLink-${targetId}" placeholder="https://..." />
                                </div>
                                <div style="display:flex; gap:12px; justify-content:flex-end;">
                                    <button type="button" class="save-btn" data-action="cancel" style="background:transparent;color:var(--text);border:1px solid var(--border);">Cancel</button>
                                    <button type="submit" class="save-btn" style="background:var(--primary);color:#fff;">Submit Report</button>
                                </div>
                            </form>
                        </div>`;
                    document.body.appendChild(overlay);

                    const closePopup = () => {
                        overlay.style.display = 'none';
                    };

                    overlay.querySelector('.close-popup')?.addEventListener('click', closePopup);
                    overlay.querySelector('[data-action="cancel"]')?.addEventListener('click', closePopup);
                    overlay.addEventListener('click', function(e) {
                        if (e.target === overlay) {
                            closePopup();
                        }
                    });

                    const notify = (message) => {
                        if (typeof show_popup === 'function') {
                            show_popup(message);
                            return;
                        }
                        alert(message);
                    };

                    const form = overlay.querySelector(`#profileReportForm-${targetId}`);
                    form?.addEventListener('submit', async function(e) {
                        e.preventDefault();

                        const categoryEl = overlay.querySelector(`#profileReportCategory-${targetId}`);
                        const detailsEl = overlay.querySelector(`#profileReportDetails-${targetId}`);
                        const linkEl = overlay.querySelector(`#profileReportLink-${targetId}`);
                        const submitBtn = form.querySelector('button[type="submit"]');

                        const category = categoryEl ? categoryEl.value : '';
                        const details = detailsEl ? detailsEl.value.trim() : '';
                        const link = linkEl ? linkEl.value.trim() : '';

                        if (!category) {
                            notify('Please select a category');
                            return;
                        }

                        if (submitBtn) {
                            submitBtn.disabled = true;
                            submitBtn.textContent = 'Submitting...';
                        }

                        try {
                            const fd = new FormData();
                            fd.append('profile_id', targetId);
                            fd.append('category', category);
                            fd.append('details', details);
                            if (link) {
                                fd.append('link', link);
                            }

                            const res = await fetch(`${window.URLROOT}/report/submitReport/profile`, {
                                method: 'POST',
                                body: fd
                            });

                            const data = await res.json().catch(() => null);
                            if (!res.ok || !data || (data.success !== true && data.status !== 'success')) {
                                throw new Error((data && data.message) ? data.message : 'Failed to submit report');
                            }

                            notify('Thanks for your report. Our team will review it shortly.');
                            closePopup();
                        } catch (err) {
                            console.error('Profile report submission error', err);
                            notify(err && err.message ? err.message : 'Error submitting report. Please try again later.');
                        } finally {
                            if (submitBtn) {
                                submitBtn.disabled = false;
                                submitBtn.textContent = 'Submit Report';
                            }
                        }
                    });
                }

                overlay.style.display = 'flex';
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
            iframe.src = <?php echo json_encode(URLROOT); ?> + `/media/certificate/${encodeURIComponent(file)}`;
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

    // Profile Edit Handlers (API-backed)
    (function() {
        const popup = document.getElementById('editProfilePopup');
        if (!popup) return; // only for owner

        const form = document.getElementById('editProfileForm');
        const chooseBtn = document.getElementById('chooseProfileImgBtn');
        const fileInput = document.getElementById('profileImageInput');
        const fileName = document.getElementById('profileImgFileName');
        const preview = document.getElementById('profileImagePreview');
        const saveBtn = document.getElementById('saveProfileBtn');
        const PROFILE_IMG_MAX_SIZE = 5 * 1024 * 1024;

        async function parseJsonResponse(response) {
            const body = await response.text();
            if (!body) return {};
            try {
                return JSON.parse(body);
            } catch (e) {
                throw new Error('Invalid server response');
            }
        }

        if (chooseBtn && fileInput) {
            chooseBtn.addEventListener('click', () => fileInput.click());
        }

        if (fileInput) {
            fileInput.addEventListener('change', function() {
                const f = this.files && this.files[0] ? this.files[0] : null;
                if (fileName) fileName.textContent = f ? f.name : 'No file chosen';
                if (!f) return;

                if (!f.type || !f.type.startsWith('image/')) {
                    alert('Please select a valid image file.');
                    this.value = '';
                    if (fileName) fileName.textContent = 'No file chosen';
                    return;
                }

                if (f.size > PROFILE_IMG_MAX_SIZE) {
                    alert('Profile image exceeds 5MB.');
                    this.value = '';
                    if (fileName) fileName.textContent = 'No file chosen';
                    return;
                }

                if (f) {
                    const reader = new FileReader();
                    reader.onload = e => {
                        if (preview) preview.src = e.target.result;
                    };
                    reader.readAsDataURL(f);
                }
            });
        }

        if (form) {
            form.addEventListener('submit', async function(e) {
                e.preventDefault();

                if (saveBtn) {
                    saveBtn.disabled = true;
                    saveBtn.textContent = 'Saving...';
                }

                try {
                    const response = await fetch(form.action, {
                        method: 'POST',
                        body: new FormData(form),
                        credentials: 'same-origin',
                        headers: {
                            'Accept': 'application/json'
                        }
                    });

                    const json = await parseJsonResponse(response);
                    if (!response.ok || !json.success) {
                        throw new Error(json.error || 'Failed to update profile');
                    }

                    window.location.reload();
                } catch (err) {
                    alert(err && err.message ? err.message : 'Failed to update profile');
                } finally {
                    if (saveBtn) {
                        saveBtn.disabled = false;
                        saveBtn.textContent = 'Save Changes';
                    }
                }
            });
        }
    })();

    // Work Experience and Projects (API-backed)
    (function() {
        const workContainer = document.getElementById('workContainer');
        const projectsContainer = document.getElementById('projectsContainer');

        const addWorkBtn = document.getElementById('addWorkBtn');
        const addWorkPopup = document.getElementById('addWorkPopup');
        const addWorkForm = document.getElementById('addWorkForm');
        const editWorkPopup = document.getElementById('editWorkPopup');
        const editWorkForm = document.getElementById('editWorkForm');
        const deleteWorkPopup = document.getElementById('deleteWorkPopup');
        const confirmDeleteWorkBtn = document.getElementById('confirmDeleteWorkBtn');
        const cancelDeleteWorkBtn = document.getElementById('cancelDeleteWorkBtn');

        const addProjectBtn = document.getElementById('addProjectBtn');
        const addProjectPopup = document.getElementById('addProjectPopup');
        const addProjectForm = document.getElementById('addProjectForm');
        const editProjectPopup = document.getElementById('editProjectPopup');
        const editProjectForm = document.getElementById('editProjectForm');
        const viewProjectPopup = document.getElementById('viewProjectPopup');
        const deleteProjectPopup = document.getElementById('deleteProjectPopup');
        const confirmDeleteProjectBtn = document.getElementById('confirmDeleteProjectBtn');
        const cancelDeleteProjectBtn = document.getElementById('cancelDeleteProjectBtn');

        if (!workContainer || !projectsContainer) {
            return;
        }

        let pendingWorkId = null;
        let pendingProjectId = null;

        function openPopup(popup) {
            if (popup) popup.style.display = 'flex';
        }

        function closePopup(popup) {
            if (popup) popup.style.display = 'none';
        }

        async function parseJsonResponse(response) {
            const body = await response.text();
            if (!body) return {};
            try {
                return JSON.parse(body);
            } catch (e) {
                throw new Error('Invalid server response');
            }
        }

        async function postForm(form) {
            const response = await fetch(form.action, {
                method: 'POST',
                body: new FormData(form),
                credentials: 'same-origin',
                headers: { 'Accept': 'application/json' }
            });
            const json = await parseJsonResponse(response);
            if (!response.ok || !json.success) {
                throw new Error(json.error || 'Operation failed');
            }
            return json;
        }

        async function deleteByUrl(url) {
            const response = await fetch(url, {
                method: 'DELETE',
                credentials: 'same-origin',
                headers: { 'Accept': 'application/json' }
            });
            const json = await parseJsonResponse(response);
            if (!response.ok || !json.success) {
                throw new Error(json.error || 'Delete failed');
            }
            return json;
        }

        // Open Add popups
        if (addWorkBtn && addWorkPopup && addWorkForm) {
            addWorkBtn.addEventListener('click', function() {
                addWorkForm.reset();
                openPopup(addWorkPopup);
            });
        }

        if (addProjectBtn && addProjectPopup && addProjectForm) {
            addProjectBtn.addEventListener('click', function() {
                addProjectForm.reset();
                openPopup(addProjectPopup);
            });
        }

        // Close popups with built-in close buttons
        [
            addWorkPopup,
            editWorkPopup,
            deleteWorkPopup,
            addProjectPopup,
            editProjectPopup,
            viewProjectPopup,
            deleteProjectPopup
        ].forEach(function(popup) {
            popup?.querySelector('.close-popup')?.addEventListener('click', function() {
                closePopup(popup);
            });
        });

        // Work: add/edit/delete
        if (addWorkForm) {
            addWorkForm.addEventListener('submit', async function(e) {
                e.preventDefault();
                try {
                    await postForm(addWorkForm);
                    window.location.reload();
                } catch (err) {
                    alert(err.message || 'Failed to add work experience');
                }
            });
        }

        if (workContainer && editWorkPopup) {
            workContainer.addEventListener('click', function(e) {
                const editBtn = e.target.closest('.work-card .edit-btn');
                if (!editBtn) return;

                const card = editBtn.closest('.work-card');
                if (!card) return;

                document.getElementById('workIdEdit').value = card.dataset.id || '';
                document.getElementById('workPositionEdit').value = card.dataset.position || '';
                document.getElementById('workCompanyEdit').value = card.dataset.company || '';
                document.getElementById('workPeriodEdit').value = card.dataset.period || '';
                openPopup(editWorkPopup);
            });
        }

        if (editWorkForm) {
            editWorkForm.addEventListener('submit', async function(e) {
                e.preventDefault();
                try {
                    await postForm(editWorkForm);
                    window.location.reload();
                } catch (err) {
                    alert(err.message || 'Failed to update work experience');
                }
            });
        }

        if (workContainer && deleteWorkPopup) {
            workContainer.addEventListener('click', function(e) {
                const deleteBtn = e.target.closest('.work-card .delete-btn');
                if (!deleteBtn) return;

                const card = deleteBtn.closest('.work-card');
                pendingWorkId = card?.dataset?.id || null;
                if (pendingWorkId) openPopup(deleteWorkPopup);
            });
        }

        cancelDeleteWorkBtn?.addEventListener('click', function() {
            pendingWorkId = null;
            closePopup(deleteWorkPopup);
        });

        confirmDeleteWorkBtn?.addEventListener('click', async function() {
            if (!pendingWorkId) return;
            try {
                await deleteByUrl(`${window.URLROOT}/profile/deleteWorkExperience?id=${encodeURIComponent(pendingWorkId)}`);
                window.location.reload();
            } catch (err) {
                alert(err.message || 'Failed to delete work experience');
            }
        });

        // Project: add/edit/view/delete
        if (addProjectForm) {
            addProjectForm.addEventListener('submit', async function(e) {
                e.preventDefault();
                try {
                    await postForm(addProjectForm);
                    window.location.reload();
                } catch (err) {
                    alert(err.message || 'Failed to add project');
                }
            });
        }

        if (projectsContainer) {
            projectsContainer.addEventListener('click', function(e) {
                const card = e.target.closest('.project-card');
                if (!card) return;

                const editBtn = e.target.closest('.project-card .edit-btn');
                if (editBtn && editProjectPopup) {
                    document.getElementById('projectIdEdit').value = card.dataset.id || '';
                    document.getElementById('projectTitleEdit').value = card.dataset.title || '';
                    document.getElementById('projectDescEdit').value = card.dataset.desc || '';
                    document.getElementById('projectSkillsEdit').value = card.dataset.skills || '';
                    document.getElementById('startDateEdit').value = card.dataset.start_date || '';
                    document.getElementById('endDateEdit').value = card.dataset.end_date || '';
                    openPopup(editProjectPopup);
                    return;
                }

                const viewBtn = e.target.closest('.project-card .view-btn');
                if (viewBtn && viewProjectPopup) {
                    const start = card.dataset.start_date || '';
                    const end = card.dataset.end_date || '';
                    document.getElementById('viewProjectTitle').textContent = card.dataset.title || '';
                    document.getElementById('viewProjectDesc').textContent = card.dataset.desc || '';
                    document.getElementById('viewProjectSkills').textContent = card.dataset.skills || '';
                    document.getElementById('viewProjectStartDate').textContent = start || 'N/A';
                    document.getElementById('viewProjectEndDate').textContent = end || 'N/A';
                    openPopup(viewProjectPopup);
                    return;
                }

                const deleteBtn = e.target.closest('.project-card .delete-btn');
                if (deleteBtn) {
                    pendingProjectId = card.dataset.id || null;
                    if (pendingProjectId) openPopup(deleteProjectPopup);
                }
            });
        }

        if (editProjectForm) {
            editProjectForm.addEventListener('submit', async function(e) {
                e.preventDefault();
                try {
                    await postForm(editProjectForm);
                    window.location.reload();
                } catch (err) {
                    alert(err.message || 'Failed to update project');
                }
            });
        }

        cancelDeleteProjectBtn?.addEventListener('click', function() {
            pendingProjectId = null;
            closePopup(deleteProjectPopup);
        });

        confirmDeleteProjectBtn?.addEventListener('click', async function() {
            if (!pendingProjectId) return;
            try {
                await deleteByUrl(`${window.URLROOT}/profile/deleteProjects?id=${encodeURIComponent(pendingProjectId)}`);
                window.location.reload();
            } catch (err) {
                alert(err.message || 'Failed to delete project');
            }
        });
    })();

<?php $scripts = ob_get_clean(); ?>

<?php require APPROOT . '/views/layouts/threeColumnLayout.php'; ?>