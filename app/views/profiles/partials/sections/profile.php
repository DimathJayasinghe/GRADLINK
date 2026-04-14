<!-- Profile Section -->
    <?php $hasProfileActions = (isset($_SESSION['user_id']) && isset($data['userDetails']->id) && $_SESSION['user_id'] != $data['userDetails']->id); ?>
    <div class="profile <?= $hasProfileActions ? 'has-actions' : 'no-actions' ?>">
        <div class="profile-up-part">
            <?php if ($isOwner) {
                echo '
                            <div class="profile-edit-btn">
                        <i class="fas fa-pencil-alt"></i>
                    </div>
                        ';
            } ?>

        </div>
        <div class="profile-down-part">
            <div class="profile-image">
                <img src="<?php echo URLROOT; ?>/media/profile/<?php echo $data['userDetails']->profile_image ?? 'default.jpg'; ?>" alt="Profile" id="profileImageEl">
            </div>
            <div class="profile-name-container">
                <div class="profile-name">
                    <?= isset($data['userDetails']->name) ? htmlspecialchars($data['userDetails']->name) : 'Alumni Name' ?>
                    <div class="batch-indicator">
                        <?= isset($data['userDetails']->batch_no) ? htmlspecialchars($data['userDetails']->batch_no) : '20' ?>
                    </div>
                </div>
                <div class="profile-bio" id="profileBioEl">
                    <?= isset($data['userDetails']->bio) ? htmlspecialchars($data['userDetails']->bio) : 'Software Engineer at Google' ?>
                </div>
                <div class="profile-footer-spacer"></div>
                <?php if (isset($_SESSION['user_id']) && isset($data['userDetails']->id) && $_SESSION['user_id'] != $data['userDetails']->id): ?>
                    <div class="profile-actions">
                        <?php $isFollowing = $data['isfollowed'] ?>
                        <button
                            class="action-btn connect-btn <?= $isFollowing ? 'active' : '' ?>"
                            id="connectBtn"
                            data-user-id="<?= htmlspecialchars($data['userDetails']->id) ?>"
                            data-connected="<?= $isFollowing ?  '1' : '0'; ?>"
                            title="<?= htmlspecialchars(($isFollowing ? 'Following ' : 'Follow ') . ($data['userDetails']->name ?? 'user')) ?>">
                            <i class="<?= $isFollowing ? 'fas fa-user-check' : 'fas fa-user-plus' ?>" aria-hidden="true"></i>
                            <span><?= $isFollowing ? 'Following' : 'Follow' ?></span>
                        </button>
                        <?php if ($isFollowing): ?>
                            <button
                                class="action-btn message-btn"
                                id="messageBtn"
                                data-user-id="<?= htmlspecialchars($data['userDetails']->id) ?>"
                                title="Message <?= htmlspecialchars($data['userDetails']->name ?? 'user') ?>">
                                <i class="fas fa-envelope" aria-hidden="true"></i>
                                <span>Message</span>
                            </button>
                        <?php endif; ?>
                        <!-- <?php $isBlocked = $data['isBlocked'] ?> -->
                        <button
                            class="action-btn block-btn <?= $isBlocked ? 'active' : '' ?>"
                            id="blockBtn"
                            data-user-id="<?= htmlspecialchars($data['userDetails']->id) ?>"
                            data-blocked="<?= $isBlocked ? '1' : '0'; ?>"
                            title="<?= htmlspecialchars(($isBlocked ? 'Unblock ' : 'Block ') . ($data['userDetails']->name ?? 'user')) ?>">
                            <i class="<?= $isBlocked ? 'fas fa-user-slash' : 'fas fa-ban' ?>" aria-hidden="true"></i>
                            <span><?= $isBlocked ? 'Unblock' : 'Block' ?></span>
                        </button>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>