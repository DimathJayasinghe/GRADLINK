<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>GRADLINK - Main Feed</title>
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/css/style.css">
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/css/mainfeed_styles.css">
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/css/components/postCardStyles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body>
    <div class="feed-container">
        <?php require APPROOT . '/views/inc/commponents/leftSideBar.php'; ?>

        <div class="main-content">
            <div class="tabs">
                <div class="tab active">For you</div>
                <div class="tab">Following</div>
            </div>

        <form class="compose-post" method="post" action="<?php echo URLROOT; ?>/post/create" enctype="multipart/form-data">
                <div class="compose-input">
                    <img src="<?php echo URLROOT; ?>/media/profile/<?php echo $_SESSION['profile_image'] ?? 'default.jpg'; ?>" alt="Profile" class="profile-photo">
            <input name="content" required maxlength="500" placeholder="What's happening?" />
            <input type="file" name="image" accept="image/*" style="display:none" id="postImageInput" />
            <button type="button" id="attachBtn" class="attach-btn">Attach</button>
                </div>
                <div id="imagePreview" style="display:none;margin-top:8px;position:relative;max-width:300px">
                    <img id="imagePreviewImg" src="" alt="Preview" style="max-width:100%;border:1px solid var(--border);border-radius:8px;display:block" />
                    <button type="button" id="removeImage" style="position:absolute;top:4px;right:4px;background:#0008;color:#fff;border:0;border-radius:4px;padding:2px 6px;cursor:pointer">x</button>
                </div>
                <div class="compose-actions">
                    <button class="post-btn" type="submit">Post</button>
                </div>
            </form>

            <div class="feed" id="feed">
            <?php if(!empty($data['posts'])): foreach($data['posts'] as $p): ?>
                <post-card
                    profile-img="<?php echo htmlspecialchars($p->profile_image); ?>"
                    user-name="<?php echo htmlspecialchars($p->name); ?>"
                    tag="@user<?php echo $p->user_id; ?>"
                    post-time="<?php echo date('M d', strtotime($p->created_at)); ?>"
                    post-content="<?php echo htmlspecialchars($p->content); ?>"
                    post-img="<?php echo htmlspecialchars($p->image ?? ''); ?>"
                    like-count="<?php echo $p->likes; ?>"
                    cmnt-count="<?php echo $p->comments; ?>"
                    post-id="<?php echo $p->id; ?>"></post-card>
            <?php endforeach; else: ?>
                <p>No posts yet.</p>
            <?php endif; ?>
            </div>

        </div>
            <!-- Include the right sidebar component -->
            <?php
            $rightSidebarStylesIncluded = true; // Prevent duplicate styles
            require APPROOT . '/views/inc/commponents/rightSideBar.php';
            ?>
        <!-- Define JS URLROOT BEFORE PostCard usage -->
        <script>
            window.URLROOT = "<?php echo URLROOT; ?>";
        </script>

        <script src="<?php echo URLROOT?>/js/component/postCard.js"></script>
        <script>
        const attachBtn=document.getElementById('attachBtn');
        const fileInput=document.getElementById('postImageInput');
        const previewWrap=document.getElementById('imagePreview');
        const previewImg=document.getElementById('imagePreviewImg');
        const removeImage=document.getElementById('removeImage');
        attachBtn?.addEventListener('click',()=>fileInput.click());
        fileInput?.addEventListener('change',e=>{
            const f=e.target.files[0];
            if(f){
                const url=URL.createObjectURL(f);
                previewImg.src=url; previewWrap.style.display='block';
            } else { previewWrap.style.display='none'; previewImg.src=''; }
        });
        removeImage?.addEventListener('click',()=>{ fileInput.value=''; previewImg.src=''; previewWrap.style.display='none'; });
        // Interaction now handled inside post-card component
        // Auto refresh posts once after initial load (fetch latest then swap if different count)
        (async function(){
            try {
                const feedEl=document.getElementById('feed');
                const initialCount=feedEl.querySelectorAll('post-card').length;
                const r = await fetch(`${URLROOT}/post?json=1`);
                if(r.ok){
                    const data=await r.json();
                    if(Array.isArray(data.posts) && data.posts.length && data.posts.length!==initialCount){
                        feedEl.innerHTML = data.posts.map(p=>`<post-card profile-img="${p.profile_image||'default.jpg'}" user-name="${p.name}" tag="@user${p.user_id}" post-time="${(p.created_at||'').slice(5,10)}" post-content="${(p.content||'').replace(/["<>]/g,'')}" post-img="${p.image||''}" like-count="${p.likes}" cmnt-count="${p.comments}" liked="${p.liked?1:0}" post-id="${p.id}"></post-card>`).join('');
                    }
                }
            } catch(e) {}
        })();
        </script>
</body>

</html>