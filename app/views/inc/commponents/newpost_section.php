<form class="compose-post" method="post" action="<?php echo URLROOT; ?>/post/create" enctype="multipart/form-data">
    <div class="compose-input">
        <img src="<?php echo URLROOT; ?>/media/profile/<?php echo $_SESSION['profile_image'] ?? 'default.jpg'; ?>" alt="Profile" class="profile-photo">
        <input name="content" required maxlength="500" placeholder="What's happening?" />
        <input type="file" name="image" accept="image/*" style="display:none" id="postImageInput" />
    </div>
    <div id="imagePreview" style="display:none;margin-top:8px;position:relative;max-width:300px">
        <img id="imagePreviewImg" src="" alt="Preview" style="max-width:100%;border:1px solid var(--border);border-radius:8px;display:block" />
        <button type="button" id="removeImage" style="position:absolute;top:4px;right:4px;background:#0008;color:#fff;border:0;border-radius:4px;padding:2px 6px;cursor:pointer">x</button>
    </div>
    <div class="compose-actions">
        <div class="action-buttons">
            <button type="button" id="attachBtn" class="attach-btn"><i class="far fa-image"></i> Attach</button>
            <button class="post-btn" type="submit">Post</button>
        </div>
    </div>
</form>

<style>
    /* Override existing styles for the compose post section */
    .compose-actions {
        margin-top: 10px;
        margin-left: 0 !important; /* Override default margin */
    }
    
    .action-buttons {
        display: flex;
        justify-content: space-between;
        align-items: center;
        width: 100%;
    }
    
    /* Style for attach button */
    .attach-btn {
        display: flex;
        align-items: center;
        gap: 5px;
        background: transparent;
        color: var(--link);
        border: 1px solid var(--border);
        padding: 8px 16px;
        border-radius: 20px;
        cursor: pointer;
        font-size: 14px;
        transition: all 0.2s ease;
    }
    
    .attach-btn:hover {
        background-color: rgba(158, 212, 220, 0.1);
    }
    
    /* Style for post button */
    .post-btn {
        background-color: var(--link);
        color: var(--bg);
        border: none;
        border-radius: 20px;
        padding: 8px 20px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    
    .post-btn:hover {
        background-color: var(--link-hover);
        transform: translateY(-1px);
    }
</style>

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