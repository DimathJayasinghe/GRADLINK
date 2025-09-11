<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/components/newpost_popup.css">
<div class="post-modal" id="postModal">
    <div class="post-modal-content">
        <div class="post-modal-header">
            <button class="modal-close-btn"><i class="fas fa-times"></i></button>
        </div>
        <form class="post-modal-body" method="post" action="<?php echo URLROOT; ?>/post/create" enctype="multipart/form-data" style="display:flex;gap:15px;flex-direction:column;">
            <div style="display:flex;align-items:flex-start;gap:15px;">
                <img src="<?php echo URLROOT; ?>/media/profile/<?php echo $_SESSION['profile_image'] ?? 'default.jpg'; ?>" alt="Profile" class="profile-photo">
                
                <div class="post-input-container" style="flex:1;">
                    <input name="content" required maxlength="500" placeholder="What's happening?" style="width:100%;min-height:80px;background:transparent;border:none;color:var(--text);font-size:18px;resize:none;font-family:'Poppins',sans-serif;outline:none;"/>
                    <div class="divider"></div>
                    
                    <div class="post-modal-actions" style="display:flex;justify-content:space-between;align-items:center;">
                        <div class="post-modal-tools" style="display:flex;gap:10px;">
                            <input type="file" name="image" accept="image/*" style="display:none" id="sidebarPostImageInput" />
                            <button type="button" class="attach-btn" id="sidebarAttachBtn">Attach</button>
                        </div>
                        <button class="modal-post-btn" type="submit">Post</button>
                    </div>
                </div>
            </div>
            <div id="sidebarImagePreview" style="display:none;margin-top:8px;position:relative;max-width:300px">
                <img id="sidebarImagePreviewImg" src="" alt="Preview" style="max-width:100%;border:1px solid var(--border);border-radius:8px;display:block" />
                <button type="button" id="sidebarRemoveImage" style="position:absolute;top:4px;right:4px;background:#0008;color:#fff;border:0;border-radius:4px;padding:2px 6px;cursor:pointer">x</button>
            </div>
        </form>
    </div>
</div>

<script>
    // Sidebar new post image preview logic
        const sidebarAttachBtn=document.getElementById('sidebarAttachBtn');
        const sidebarFileInput=document.getElementById('sidebarPostImageInput');
        const sidebarPreviewWrap=document.getElementById('sidebarImagePreview');
        const sidebarPreviewImg=document.getElementById('sidebarImagePreviewImg');
        const sidebarRemoveImage=document.getElementById('sidebarRemoveImage');
        sidebarAttachBtn?.addEventListener('click',()=>sidebarFileInput.click());
        sidebarFileInput?.addEventListener('change',e=>{
            const f=e.target.files[0];
            if(f){
                const url=URL.createObjectURL(f);
                sidebarPreviewImg.src=url; sidebarPreviewWrap.style.display='block';
            } else { sidebarPreviewWrap.style.display='none'; sidebarPreviewImg.src=''; }
        });
        sidebarRemoveImage?.addEventListener('click',()=>{ sidebarFileInput.value=''; sidebarPreviewImg.src=''; sidebarPreviewWrap.style.display='none'; });
        // ----- FIX: define JS URL root -----
        const URLROOT_JS = "<?php echo URLROOT; ?>";

        // Post modal functionality (unchanged)
        const postButton = document.querySelector('.post-button');
        const postModal = document.getElementById('postModal');
        const postModalCloseBtn = document.querySelector('#postModal .modal-close-btn');
        const modalPostBtn = document.querySelector('.modal-post-btn');

        if (postButton) {
            postButton.addEventListener('click', () => {
                postModal.style.display = 'flex';
                postModal.querySelector('textarea').focus();
            });
        }
        if (postModalCloseBtn) postModalCloseBtn.addEventListener('click', () => postModal.style.display = 'none');
        window.addEventListener('click', e => {
            if (e.target === postModal) postModal.style.display = 'none';
        });
        if (modalPostBtn) {
            modalPostBtn.addEventListener('click', () => {
                const txt = postModal.querySelector('textarea').value.trim();
                if (!txt) return;
                postModal.querySelector('textarea').value = '';
                postModal.style.display = 'none';
            });
        }
</script>