<!-- Projects: Add Popup -->
<?php if ($isOwner): ?>
    <div id="addProjectPopup" class="certificate-add-popup" style="display:none;">
        <div class="certificate-add">
            <button class="close-popup" title="Close"><i class="fas fa-times"></i></button>
            <div class="form-title">Add Project</div>
            <form id="addProjectForm" class="certificate-form" action="<?= URLROOT; ?>/profile/addProjects" method="POST">
                <div class="form-group">
                    <label for="projectTitleAdd">Title</label>
                    <input type="text" id="projectTitleAdd" name="project_title" required>
                </div>
                <div class="form-group">
                    <label for="projectDescAdd">Description</label>
                    <textarea id="projectDescAdd" name="project_description" style="
                                max-width: 100%;
                                background: rgba(255, 255, 255, 0.05);
                                border: 1px solid var(--border);
                                border-radius: 5px;
                                color: var(--text);
                                padding:5px;
                        " rows="3" placeholder="Brief description..."></textarea>
                </div>
                <div class="form-group">
                    <label for="projectSkillsAdd">Skills</label>
                    <input type="text" id="projectSkillsAdd" name="project_skills" required>
                </div>
                <div class="form-group">
                    <label for="startDateAdd">Start Date</label>
                    <input type="date" id="startDateAdd" name="project_start_date" required>
                </div>
                <div class="form-group">
                    <label for="endDateAdd">End Date</label>
                    <input type="date" id="endDateAdd" name="project_end_date">
                </div>
                <div style="margin-top:12px;">
                    <button type="submit" class="save-btn">Add Project</button>
                </div>
            </form>
        </div>
    </div>
<?php endif; ?>

<!-- Projects: Edit Popup -->
<?php if ($isOwner): ?>
    <div id="editProjectPopup" class="certificate-add-popup" style="display:none;">
        <div class="certificate-add">
            <button class="close-popup" title="Close"><i class="fas fa-times"></i></button>
            <div class="form-title">Edit Project</div>
            <form id="editProjectForm" class="certificate-form" action="<?= URLROOT; ?>/profile/updateProjects" method="POST">
                <input type="hidden" id="projectIdEdit" name="project_id">
                <div class="form-group">
                    <label for="projectTitleEdit">Title</label>
                    <input type="text" id="projectTitleEdit" name="project_title" required>
                </div>
                <div class="form-group">
                    <label for="projectDescEdit">Description</label>
                    <textarea id="projectDescEdit" name="project_description" rows="3" required></textarea>
                </div>
                <div class="form-group">
                    <label for="projectSkillsEdit">Skills</label>
                    <input type="text" id="projectSkillsEdit" name="project_skills" required>
                </div>
                <div class="form-group">
                    <label for="startDateEdit">Start Date</label>
                    <input type="date" id="startDateEdit" name="project_start_date" required>
                </div>
                <div class="form-group">
                    <label for="endDateEdit">End Date</label>
                    <input type="date" id="endDateEdit" name="project_end_date">
                </div>
                <div style="margin-top:12px;">
                    <button type="submit" class="save-btn">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
<?php endif; ?>

<!--Projects: View Popup -->
<div id="viewProjectPopup" class="certificate-add-popup" style="display:none;">
    <div class="certificate-add">
        <button class="close-popup" title="Close"><i class="fas fa-times"></i></button>
        <div class="form-title">Project Details</div>

        <div class="certificate-form">
            <div class="form-group">
                <label style="font-weight: 600; color: var(--text); margin-bottom: 4px;">Project Title</label>
                <div id="viewProjectTitle" style="color: var(--text); padding: 8px 12px; background: rgba(255, 255, 255, 0.05); border: 1px solid var(--border); border-radius: 12px; min-height: 40px; display: flex; align-items: center;"></div>
            </div>

            <div class="form-group">
                <label style="font-weight: 600; color: var(--text); margin-bottom: 4px;">Description</label>
                <div id="viewProjectDesc" style="color: var(--text); padding: 8px 12px; background: rgba(255, 255, 255, 0.05); border: 1px solid var(--border); border-radius: 12px; min-height: 80px; line-height: 1.5;"></div>
            </div>

            <div class="form-group">
                <label style="font-weight: 600; color: var(--text); margin-bottom: 4px;">Skills</label>
                <div id="viewProjectSkills" style="color: var(--text); padding: 8px 12px; background: rgba(255, 255, 255, 0.05); border: 1px solid var(--border); border-radius: 12px; min-height: 40px; display: flex; align-items: center;"></div>
            </div>

            <div class="form-group">
                <label style="font-weight: 600; color: var(--text); margin-bottom: 4px;">Start Date</label>
                <div id="viewProjectStartDate" style="color: var(--text); padding: 8px 12px; background: rgba(255, 255, 255, 0.05); border: 1px solid var(--border); border-radius: 12px; min-height: 40px; display: flex; align-items: center;"></div>
            </div>

            <div class="form-group">
                <label style="font-weight: 600; color: var(--text); margin-bottom: 4px;">End Date</label>
                <div id="viewProjectEndDate" style="color: var(--text); padding: 8px 12px; background: rgba(255, 255, 255, 0.05); border: 1px solid var(--border); border-radius: 12px; min-height: 40px; display: flex; align-items: center;"></div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Project Popup (visual only) -->
<?php if ($isOwner): ?>
    <div id="deleteProjectPopup" class="certificate-add-popup" style="display:none;">
        <div class="certificate-add">
            <button class="close-popup" title="Close"><i class="fas fa-times"></i></button>
            <div class="form-title">Delete Project</div>
            <div class="certificate-delete-body" style="color:var(--text); padding:16px;">
                <p>Are you sure you want to permanently delete this project? This action cannot be undone.</p>
            </div>
            <div style="display:flex; gap:12px; justify-content:flex-end; padding:12px 16px 20px;">
                <button type="button" id="cancelDeleteProjectBtn" class="save-btn" style="background:transparent;color:var(--text);border:1px solid var(--border);">Cancel</button>
                <button type="button" id="confirmDeleteProjectBtn" class="save-btn" style="background:var(--danger);color:#fff;">Delete</button>
            </div>
        </div>
    </div>

    <!-- Certificate PDF Preview Modal -->
    <div id="certificatePreviewModal" class="certificate-add-popup" style="display:none;">
        <div class="certificate-add" style="max-width: 900px; width: 90%; height: 85vh; display: flex; flex-direction: column;">
            <button class="close-popup" title="Close"><i class="fas fa-times"></i></button>
            <div class="form-title" id="certificatePreviewTitle" style="margin-bottom: 8px;">Certificate Preview</div>
            <div style="flex: 1; border: 1px solid var(--border); border-radius: 6px; overflow: hidden; background: #fff;">
                <iframe id="certificatePreviewFrame" src="about:blank" title="Certificate PDF" style="width:100%;height:100%;border:0;"></iframe>
            </div>
        </div>
    </div>
<?php endif; ?>