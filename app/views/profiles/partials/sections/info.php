<!-- Info Section: Certificates, Work Experience and Projects -->
    <div id="infoSection" style="display:none; flex-direction: column;">

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
            if (!empty($data['work_experiences'])):
                foreach ($data['work_experiences'] as $work):
            ?>
                    <div class="certificate-card work-card" data-id="<?= $work->id ?>" data-position="<?= htmlspecialchars($work->position) ?>" data-company="<?= htmlspecialchars($work->company) ?>" data-period="<?= htmlspecialchars($work->period) ?>">
                        <div class="certificate-card-image">
                            <i class="fas fa-briefcase"></i>
                        </div>
                        <div class="certificate-details">
                            <div class="certificate-card-position"><?= htmlspecialchars($work->position) ?></div>
                            <div class="certificate-issuer"><?= htmlspecialchars($work->company) ?></div>
                            <div class="certificate-date"><?= htmlspecialchars($work->period) ?></div>
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
                <div id="noWorkExpMessage">No work experience added yet.</div>
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
                            <div class="certificate-card-position"><?= htmlspecialchars($cert->name) ?></div>
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
            if (!empty($data['projects'])):
                foreach ($data['projects'] as $project):
            ?>
                    <div class="certificate-card project-card"
                        data-id="<?= htmlspecialchars($project->id) ?>"
                        data-title="<?= htmlspecialchars($project->title) ?>"
                        data-desc="<?= htmlspecialchars($project->description ?? '') ?>"
                        data-skills="<?= htmlspecialchars($project->skills_used ?? '') ?>"
                        data-start_date="<?= htmlspecialchars($project->start_date ?? '') ?>"
                        data-end_date="<?= htmlspecialchars($project->end_date ?? '') ?>">

                        <div class="project-card-image">
                            <i class="fas fa-project-diagram"></i>
                        </div>

                        <div class="certificate-details">
                            <div class="certificate-card-position"><?= htmlspecialchars($project->title) ?></div>
                        </div>
                        <!--Always-visible View button -->
                        <div class="certificate-view-btn-wrapper" style="display:flex; gap:8px; align-items:center; margin-left: auto;">
                            <div class="certificate-action-btn view-btn" title="View Project"><i class="fas fa-eye"></i></div>
                        </div>
                        <?php if ($isOwner): ?>
                            <div class="certificate-actions">
                                <div class="certificate-action-btn edit-btn" title="Edit Project">
                                    <i class="fas fa-pencil-alt"></i>
                                </div>
                                <div class="certificate-action-btn delete-btn" title="Delete Project">
                                    <i class="fas fa-trash-alt"></i>
                                </div>
                            </div>
                        <?php endif; ?>

                    </div>
                <?php endforeach;
            else: ?>
                <div>No projects added yet.</div>
            <?php endif; ?>
        </div>
    </div>