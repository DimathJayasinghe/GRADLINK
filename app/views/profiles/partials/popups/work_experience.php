<!-- Work Experience: Add Popup -->
<?php if ($isOwner): ?>
    <div id="addWorkPopup" class="certificate-add-popup" style="display:none;">
        <div class="certificate-add">
            <button class="close-popup" title="Close"><i class="fas fa-times"></i></button>
            <div class="form-title">Add Work Experience</div>
            <form id="addWorkForm" class="work-form" action="<?= URLROOT; ?>/profile/addWorkExperience">
                <div class="form-group">
                    <label for="workPositionAdd">Position</label>
                    <input type="text" name="position" id="workPositionAdd" required>
                </div>
                <div class="form-group">
                    <label for="workCompanyAdd">Company</label>
                    <input type="text" name="company" id="workCompanyAdd" required>
                </div>
                <div class="form-group">
                    <label for="workPeriodAdd">Period</label>
                    <input type="text" name="period" id="workPeriodAdd" placeholder="e.g., 2021 - Present" required>
                </div>
                <div style="margin-top:12px;">
                    <button type="submit" class="save-btn">Add Work</button>
                </div>
            </form>
        </div>
    </div>
<?php endif; ?>

<!-- Work Experience: Edit Popup -->
<?php if ($isOwner): ?>
    <div id="editWorkPopup" class="certificate-add-popup" style="display:none;">
        <div class="certificate-add">
            <button class="close-popup" title="Close"><i class="fas fa-times"></i></button>
            <div class="form-title">Edit Work Experience</div>
            <form id="editWorkForm" class="certificate-form" action="<?= URLROOT; ?>/profile/updateWorkExperience" method="POST">
                <input type="hidden" id="workIdEdit" name="work_id">
                <div class="form-group">
                    <label for="workPositionEdit">Position</label>
                    <input type="text" name="position" id="workPositionEdit" required>
                </div>
                <div class="form-group">
                    <label for="workCompanyEdit">Company</label>
                    <input type="text" name="company" id="workCompanyEdit" required>
                </div>
                <div class="form-group">
                    <label for="workPeriodEdit">Period</label>
                    <input type="text" name="period" id="workPeriodEdit" placeholder="e.g., 2021 - Present" required>
                </div>
                <div style="margin-top:12px;">
                    <button type="submit" class="save-btn">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
<?php endif; ?>

<!-- Delete Work Experience Popup (visual only) -->
<?php if ($isOwner): ?>
    <div id="deleteWorkPopup" class="certificate-add-popup" style="display:none;">
        <div class="certificate-add">
            <button class="close-popup" title="Close"><i class="fas fa-times"></i></button>
            <div class="form-title">Delete Work Experience</div>
            <div class="certificate-delete-body" style="color:var(--text); padding:16px;">
                <p>Are you sure you want to permanently delete this work experience? This action cannot be undone.</p>
            </div>
            <div style="display:flex; gap:12px; justify-content:flex-end; padding:12px 16px 20px;">
                <button type="button" id="cancelDeleteWorkBtn" class="save-btn" style="background:transparent;color:var(--text);border:1px solid var(--border);">Cancel</button>
                <button type="button" id="confirmDeleteWorkBtn"  class="save-btn" style="background:var(--danger);color:#fff;">Delete</button>
            </div>
        </div>
    </div>
<?php endif; ?>