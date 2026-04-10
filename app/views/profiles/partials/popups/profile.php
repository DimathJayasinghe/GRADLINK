<!-- Profile Edit Popup  -->
<?php if ($isOwner): ?>
    <div id="editProfilePopup" class="certificate-add-popup" style="display:none;">
        <div class="certificate-add">
            <button class="close-popup" title="Close"><i class="fas fa-times"></i></button>
            <div class="form-title">Edit Profile</div>

            <form id="editProfileForm" class="certificate-form" action="<?= URLROOT; ?>/profile/updateProfileBioImage" method="post" enctype="multipart/form-data">
                <div class="form-group">
                    <label>Profile Picture</label>
                    <div class="file-upload-container">
                        <input type="file" id="profileImageInput" name="profileImageInput" accept="image/*" style="display:none;">
                        <button type="button" class="file-upload-btn" id="chooseProfileImgBtn">Choose Image</button>
                        <span class="file-name" id="profileImgFileName" style="color:var(--text)">No file chosen</span>
                    </div>
                    <div style="margin-top:10px;display:flex;align-items:center;gap:12px;">
                        <img id="profileImagePreview" src="<?php echo URLROOT; ?>/media/profile/<?php echo $data['userDetails']->profile_image ?? 'default.jpg'; ?>" alt="Preview" style="width:64px;height:64px;border-radius:50%;object-fit:cover;border:1px solid var(--border);">
                        <span style="color:var(--muted);font-size:0.9rem;">Preview</span>
                    </div>
                </div>
                <div class="form-group">
                    <label for="profileBioInput">Bio</label>
                    <textarea id="profileBioInput" name="profileBioInput" style="
                                max-width: 100%;
                                background: rgba(255, 255, 255, 0.05);
                                border: 1px solid var(--border);
                                border-radius: 5px;
                                color: var(--text);
                                padding:5px;
                        " rows="3" placeholder="Tell others about you..."></textarea>
                </div>
                <div style="margin-top:12px;">
                    <button type="submit" class="save-btn" id="saveProfileBtn">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
<?php endif; ?>