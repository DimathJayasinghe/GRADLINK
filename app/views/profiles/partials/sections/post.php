<!-- Posts Section - Using same structure as main feed -->
    <div class="feed" id="postsSection" style="display:block;">
        <?php if (!empty($data['posts'])): foreach ($data['posts'] as $p): ?>
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
            <?php endforeach;
        else: ?>
            <div class="no-posts-message">No posts yet.</div>
        <?php endif; ?>
    </div>