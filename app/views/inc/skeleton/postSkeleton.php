<style>
    /* Skeleton placeholders (loading state) */
    .post.skeleton {
        opacity: 0.95;
    }

    .skeleton-box {
        position: relative;
        overflow: hidden;
        background: linear-gradient(90deg,
                var(--skeleton-base, rgba(255, 255, 255, 0.10)) 25%,
                var(--skeleton-highlight, rgba(255, 255, 255, 0.18)) 37%,
                var(--skeleton-base, rgba(255, 255, 255, 0.10)) 63%);
        background-size: 400% 100%;
        animation: skeleton-shimmer 1.4s ease-in-out infinite;
    }

    .skeleton-circle {
        width: 48px;
        height: 48px;
        border-radius: 50%;
    }

    .skeleton-line {
        height: 12px;
        border-radius: 6px;
        margin: 6px 0;
    }

    .skeleton-line.sm {
        height: 10px;
    }

    .skeleton-line.lg {
        height: 14px;
    }

    .skeleton-rect {
        width: 100%;
        aspect-ratio: 1 / 1;
        border-radius: 12px;
    }

    @keyframes skeleton-shimmer {
        0% {
            background-position: 100% 0;
        }

        100% {
            background-position: -100% 0;
        }
    }

    /* Layout tweaks for placeholder card */
    .post.skeleton {
        padding: 12px 16px;
        border-bottom: 1px solid var(--border);
    }

    .post.skeleton .post-header {
        display: flex;
        justify-content: space-between;
    }

    .post.skeleton .post-user {
        display: flex;
    }

    .post.skeleton .post-user-info {
        margin-left: 12px;
        width: 100%;
    }

    .post.skeleton .post-media {
        margin-top: 10px;
    }

    /* Ensure media skeleton fills the same square area as real images */
    .post.skeleton .post-media .skeleton-rect {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        border-radius: 15px;
    }

    .post.skeleton .post-actions {
        display: flex;
        gap: 16px;
        align-items: center;
        margin-top: 10px;
    }

    /* Caption block placeholder */
    .skeleton-caption {
        width: 100%;
        /* height: 96px; */
        border-radius: 12px;
        /* margin-top: 10px; */
    }
</style>

<div class="post skeleton" aria-hidden="true">
    <div class="post-header">
        <div class="post-user">
            <div class="profile-photo skeleton-circle skeleton-box"></div>
            <div class="post-user-info skeleton-info" style="width: 190px;">
                <div class="skeleton-line lg skeleton-box" style="width: 80%;"></div>
                <div class="skeleton-line sm skeleton-box" style="width: 60%;"></div>
                <div class="skeleton-line sm skeleton-box" style="width: 40%;"></div>
                <div class="post-content" style="margin-top:8px;">
                    <br><br>
                    <div class="post-content skeleton-line sm skeleton-box">
                        <p class="post-text">                                                                                      </p>
                    </div>
                </div>
            </div>
        </div>
        <div class="post-menu" role="button" aria-hidden="true">
            <div class="skeleton-line sm skeleton-box" style="width: 18px; height: 18px; border-radius: 4px;"></div>
        </div>
    </div>
    <div class="post-media">
        <div class="skeleton-rect skeleton-box"></div>
    </div>
    <div class="post-actions">
        <div class="liked" aria-hidden="true">
            <div class="skeleton-line sm skeleton-box" style="width: 50px;"></div>
        </div>
        <div class="comment-btn" aria-hidden="true">
            <div class="skeleton-line sm skeleton-box" style="width: 60px;"></div>
        </div>
    </div>
</div> 