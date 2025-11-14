class PostCard extends HTMLElement {
  // Store edit mode state
  isEditing = false;
  originalContent = {
    text: '',
    image: ''
  };
  
  connectedCallback() {
    // Ensure URLROOT is available (fallback if not injected by layout)
    if (!window.URLROOT || typeof window.URLROOT !== 'string') {
      window.URLROOT = `${location.origin}/GRADLINK`;
    }
    const root = `${location.origin}/GRADLINK/public/img`;
    // Use Media controller endpoints: /media/profile and /media/post
    const appBase = `${location.origin}/GRADLINK`;
    const mediaProfile = (name) =>`${appBase}/media/profile/${encodeURIComponent(name)}`;
    const mediaPost = (name) =>`${appBase}/media/post/${encodeURIComponent(name)}`;
    const rawProfile = this.getAttribute("profile-img");
    const profileImg =rawProfile && rawProfile.trim() !== "" ? rawProfile : "default.jpg";

    const userName = this.getAttribute("user-name") || "User";
    const postOwnerRole = this.getAttribute("user-role") || "undergrad";
    const userHandle = this.getAttribute("tag") || "@user";
    
    const postTime = this.getAttribute("post-time") || "";
    const postText = this.getAttribute("post-content") || "";
    const postImg = this.getAttribute("post-img");
    const likeCount = this.getAttribute("like-count") || "0";
    const likedInitial = this.getAttribute("liked") === "1";
    const commentCount = this.getAttribute("cmnt-count") || "0";
    const repostCount = this.getAttribute("repost-count") || "0"; // unused
    
    const postId = this.getAttribute("post-id") || "post-0";
    const postUserId = this.getAttribute('post-user-id') || null;
    const currentUserId = this.getAttribute('current-user-id') || null;
    const currentUserRole = this.getAttribute('current-user-role') || 'undergrad';
    const isOwner = postUserId && currentUserId && postUserId === currentUserId;

    this.innerHTML = `
      <div class="post ${(postOwnerRole == "admin") ? "admin-post":""}" id="post-${postId}">
        <div class="post-header">
          <div class="post-user">
            <img src="${mediaProfile(profileImg)}" alt="User" style="cursor: pointer;" class="profile-photo" onerror="this.onerror=null;this.src='${mediaProfile("default.jpg")}'">
            <div class="post-user-info">
              <span class="post-user-name" style="cursor: pointer;">${userName + ((postOwnerRole == "admin") ? "⭐⭐" : (postOwnerRole == "alumni") ? "⭐" : "")}</span>
              <span class="post-user-handle" style="cursor: pointer;">${userHandle}</span>
              <span class="post-time"> · ${postTime}</span>
              <div class="post-content"><p class="post-text">${truncatePostText(postText)}</p></div>
            </div>
          </div>
          <div class="post-menu" role="button" aria-haspopup="true" aria-expanded="false">
            <i class="fas fa-ellipsis-h post-menu-btn"></i>
            <div class="post-dropdown hidden" role="menu">
              <!-- Always available -->
              <div class="dropdown-item" data-action="bookmark" role="menuitem">Bookmark</div>
              <div class="dropdown-item" data-action="share" role="menuitem">Share</div>
              <!-- Report: Available to all users -->
              <div class="dropdown-item" data-action="report" role="menuitem">Report</div>
              <!-- Admin specific actions -->
              ${currentUserRole.toLowerCase() === 'admin' ? `
                <!-- Suspend: Admin can suspend post owner (not themselves) -->
                ${!isOwner ? '<div class="dropdown-item" data-action="suspend" role="menuitem">Suspend User</div>' : ''}
                <!-- Delete: Admin can delete any post -->
                <div class="dropdown-item" data-action="delete-post" role="menuitem">Delete Post</div>
              ` : ''}
              <!-- Owner specific actions (if not already shown via admin) -->
              ${isOwner && currentUserRole.toLowerCase() !== 'admin' ? `
                <div class="dropdown-item" data-action="delete-post" role="menuitem">Delete Post</div>
              ` : ''}
              <!-- Only owner can edit -->
              ${isOwner ? '<div class="dropdown-item" data-action="edit-post" role="menuitem">Edit Post</div>' : ''}
            </div>
          </div>
        </div>
        ${postImg? `<div class=\"post-media\"><img src=\"${mediaPost(postImg)}\" alt=\"Post image\" onerror=\"this.style.display='none'\"></div>`: ""}
        
        <div class="post-actions">
          <div class="like-btn${likedInitial ? " liked" : ""}" data-post-id="${postId}"><i class="${likedInitial ? "fas" : "far"} fa-heart"></i> <span class="like-count">${likeCount}</span></div>
          <div class="comment-btn" data-post-id="${postId}"><i class="far fa-comment"></i> <span class="comment-count">${commentCount}</span></div>
        </div>
        
        <div class="pc-comments" style="display:none;border-top:1px solid var(--border);margin-top:10px;padding-top:8px">
          <div class="pc-comments-list" style="max-height:200px;overflow:auto;color:#ccc"></div>
          <div style="display:flex;gap:6px;margin-top:6px">
            <input type="text" class="pc-comment-input" placeholder="Add a comment" style="flex:1;padding:6px;border:1px solid var(--border);background:transparent;color:#fff" />
            <button class="pc-comment-send" style="padding:6px 10px">Send</button>
          </div>
        </div>

      </div>`;

    // Wire up profile navigation without inline handlers
    const clickableProfileEls = this.querySelectorAll('.profile-photo, .post-user-name, .post-user-handle');
    if (postUserId) {
      clickableProfileEls.forEach(el => {
        el.addEventListener('click', () => routeToProfile(postUserId));
      });
    }



    function  routeToProfile(userId){
      window.location.href = `${window.URLROOT}/profile?userid=${userId}`;
    }
    // Dropdown logic
    const menu = this.querySelector('.post-menu');
    const menuBtn = this.querySelector('.post-menu-btn');
    const dropdown = this.querySelector('.post-dropdown');
    if(menu && menuBtn && dropdown){
      menuBtn.addEventListener('click', (e)=>{
        e.stopPropagation();
        const isHidden = dropdown.classList.toggle('hidden');
        menu.setAttribute('aria-expanded', (!isHidden).toString());
      });
      // outside click close
      document.addEventListener('click', (e)=>{
        if(!this.contains(e.target)){
          if(!dropdown.classList.contains('hidden')){
            dropdown.classList.add('hidden');
            menu.setAttribute('aria-expanded','false');
          }
        }
      });
      
      // simple action handlers with popups
      dropdown.addEventListener('click', async (e)=>{
        const item = e.target.closest('.dropdown-item');
        if(!item) return;
        const act = item.getAttribute('data-action');
        dropdown.classList.add('hidden');
        menu.setAttribute('aria-expanded','false');
        
        const isAdmin = currentUserRole.toLowerCase() === 'admin';
        
        if(act === 'bookmark'){
          // Open bookmark confirmation popup
          this._openBookmarkPopup(postId);
        } else if (act === 'share') {
          // Build a shareable link to this post (anchor link)
          const shareUrl = `${window.URLROOT}/mainfeed#post-${postId}`;
          this._openSharePopup(postId, shareUrl);
        } else if(act === 'report') {
          // Open report form popup
          this._openReportPopup(postId);
        } else if(act === 'suspend') {
          // Only admin can suspend (not themselves)
          if(!isAdmin || isOwner) return;
          // Backend API: POST /api/users/{postUserId}/suspend
          console.log('Suspend user placeholder for', postUserId);
        } else if(act === 'delete-post') {
          // Owner OR admin can delete
          if(!isOwner && !isAdmin) return;
          this._openDeletePopup(postId);
        } else if(act === 'edit-post') {
          // Only owner can edit
          if(!isOwner) return;
          
          // Toggle edit mode for this post
          this.toggleEditMode(postId, postText, postImg);
        }
      });
    }

    // Pure function that returns truncated markup (no inline onclick)
    function truncatePostText(full){
      if(!full) return "";
      if(full.length <= 100) return full;
      const short = full.slice(0, 100).trim() + "...";
      return `${short} <span class="seemore-btn" data-action="expand-post">Show more</span>`;
    }

    // Add event listeners
    const panel = this.querySelector(".pc-comments");
    const list = panel.querySelector(".pc-comments-list");
    const cBtn = this.querySelector(".comment-btn");
    // Post text expand / collapse (event delegation)
  const postTextEl = this.querySelector('.post-content .post-text');
    // Delegated click handler (post + comments)
    this.addEventListener('click', (e)=>{
      const target = e.target;
      // Comment author click -> navigate to profile
      const author = target.closest('.comment-author');
      if (author) {
        const uid = author.getAttribute('data-user-id');
        if (uid) {
          routeToProfile(uid);
          return;
        }
      }
      // Post text expand/collapse
      if(postText.length > 100){
        const expandBtn = target.closest('[data-action="expand-post"]');
        const collapseBtn = target.closest('[data-action="collapse-post"]');
        if(expandBtn){
          if (postTextEl) {
            postTextEl.innerHTML = `${postText} <span class=\"seemore-btn\" data-action=\"collapse-post\">Show less</span>`;
          }
          return;
        }
        if(collapseBtn){
          if (postTextEl) {
            postTextEl.innerHTML = truncatePostText(postText);
          }
          return;
        }
      }
      // Comment expand
      if(target.matches('[data-action="expand-comment"]')){
        const full = decodeURIComponent(target.getAttribute('data-full')||'');
        const wrapper = target.closest('.comment-item');
        const span = wrapper?.querySelector('.comment-text');
        if(span){
          span.innerHTML = `${full} <span class=\"seemore-btn\" data-action=\"collapse-comment\" data-full=\"${encodeURIComponent(full)}\">Show less</span>`;
        }
        return;
      }
      // Comment collapse
      if(target.matches('[data-action="collapse-comment"]')){
        const full = decodeURIComponent(target.getAttribute('data-full')||'');
        const short = full.length > 160 ? full.slice(0,140) + '...' : full;
        const wrapper = target.closest('.comment-item');
        const span = wrapper?.querySelector('.comment-text');
        if(span){
          if(full.length > 160){
            span.innerHTML = `${short} <span class=\"seemore-btn\" data-action=\"expand-comment\" data-full=\"${encodeURIComponent(full)}\">Show more</span>`;
          } else {
            span.textContent = full;
          }
        }
        return;
      }
    });

    // Toggle comment panel and load comments if not loaded
    cBtn.addEventListener("click", async () => {
      panel.style.display = panel.style.display === "none" ? "block" : "none";
      if (panel.style.display === "block" && !panel.dataset.loaded) {
        list.innerHTML = "Loading...";
        await this._loadComments(postId, list);
        panel.dataset.loaded = "1";
      }
    });

    // Handle comment submission
    panel.querySelector(".pc-comment-send").addEventListener("click", async () => {
        const input = panel.querySelector(".pc-comment-input");
        const txt = input.value.trim();
        if (!txt) return;
        const fd = new FormData();
        fd.append("content", txt);
        input.disabled = true;
        const r = await fetch(`${window.URLROOT}/post/comment/${postId}`, {
          method: "POST",
          body: fd,
        });
        const js = await r.json();
        input.value = "";
        input.disabled = false;
        this._renderComments(js.comments, list);
        this._setCommentCount(js.comments.length);
      });

    // Like functionality with error handling
    const likeBtn = this.querySelector(".like-btn");
    likeBtn.addEventListener("click", async () => {
      try {
        // Disable button to prevent double-clicks
        likeBtn.style.pointerEvents = "none";

        const icon = likeBtn.querySelector("i");
        const countEl = likeBtn.querySelector(".like-count");

        // Check if URLROOT is defined
        if (!window.URLROOT) {
          console.error(
            "URLROOT is not defined. Check if it's properly set in the head section."
          );
          window.URLROOT = location.origin + "/GRADLINK"; // Fallback
        }

        const r = await fetch(`${window.URLROOT}/post/like/${postId}`);
        const js = await r.json();

        if (js.status === "error") {
          console.error("Like error:", js.message);
          alert("Error: " + js.message);
          return;
        }

        const liked = js.status === "liked";
        likeBtn.classList.toggle("liked", liked);
        icon.classList.toggle("fas", liked);
        icon.classList.toggle("far", !liked);

        let c = parseInt(countEl.textContent || "0", 10);
        c = liked ? c + 1 : Math.max(0, c - 1);
        countEl.textContent = c;
        this.setAttribute("like-count", c);
      } catch (err) {
        console.error("Like action error:", err);
      } finally {
        // Re-enable button
        likeBtn.style.pointerEvents = "";
      }
    });

    // Repost functionality (client-side demo)
    // Repost removed
  }
  async _loadComments(pid, list) {
    try {
      const r = await fetch(`${window.URLROOT}/post/comments/${pid}`);
      const js = await r.json();
      this._renderComments(js, list);
      this._setCommentCount(js.length);
    } catch (e) {
      list.innerHTML = "Error";
    }
  }
  _renderComments(arr, list) {
    if (!Array.isArray(arr)) {
      list.innerHTML = "<em>No comments</em>";
      return;
    }
    if (!arr.length) {
      list.innerHTML = "<em>No comments</em>";
      return;
    }
    list.innerHTML = arr
      .map((c) => {
        const rel = this._relTime(c.created_at);
        const star = c.role === "alumni" ? " ★" : c.role === "admin" ? " ★★" : "";
        const full = this._esc(c.content || "");
        const needsTruncate = full.length > 160;
        const short = needsTruncate ? full.slice(0, 140) + "..." : full;
        const body = needsTruncate
          ? `${short} <span class=\"seemore-btn\" data-action=\"expand-comment\" data-full=\"${encodeURIComponent(full)}\">Show more</span>`
          : full;
        const uid = c.user_id || c.id; // fallback for older API
        return `<div class=\"comment-item\" style=\"margin-bottom:10px\"><div class=\"bubble\"><strong class=\"comment-author\" data-user-id=\"${this._esc(uid)}\" style=\"cursor: pointer;\">${this._esc((c.name || "User") + star)}</strong><br><span class=\"comment-text post-text\">${body}</span><span class=\"meta\">${rel}</span></div></div>`;
      })
      .join("");
  }

 
  _relTime(ts) {
    if (!ts) return "";
    const d = new Date(ts.replace(" ", "T"));
    const diff = (Date.now() - d.getTime()) / 1000;
    if (diff < 60) return Math.max(1, Math.floor(diff)) + "s";
    if (diff < 3600) return Math.floor(diff / 60) + "m";
    if (diff < 86400) return Math.floor(diff / 3600) + "h";
    if (diff < 604800) return Math.floor(diff / 86400) + "d";
    if (diff < 2629800) return Math.floor(diff / 604800) + "w";
    return Math.floor(diff / 2629800) + "mo";
  }
  _esc(s) {
    return String(s).replace(
      /[&<>"']/g,
      (m) =>
        ({
          "&": "&amp;",
          "<": "&lt;",
          ">": "&gt;",
          '"': "&quot;",
          "'": "&#39;",
        }[m])
    );
  }
  _setCommentCount(n) {
    const el = this.querySelector(".comment-count");
    if (el) el.textContent = n;
  }
  
  /**
   * Toggle edit mode for the post
   */
  toggleEditMode(postId, originalPostText, originalPostImg) {
    this.isEditing = !this.isEditing;
    
    // Get references to relevant elements
    const postContent = this.querySelector('.post-content');
    const postTextEl = this.querySelector('.post-text');
    const postMedia = this.querySelector('.post-media');
    
    if (this.isEditing) {
      // Store original content for canceling
      this.originalContent = {
        text: postTextEl.textContent,
        image: postMedia ? postMedia.querySelector('img').src : ''
      };
      
      // Add visual indicators for edit mode
      postContent.style.border = '2px dotted var(--link)';
      postContent.style.padding = '10px';
      postContent.style.borderRadius = '5px';
      
      if (postMedia) {
        postMedia.style.border = '2px dotted var(--link)';
        postMedia.style.padding = '10px';
        postMedia.style.borderRadius = '5px';
        postMedia.style.marginTop = '10px';
      }
      
      // Make text editable - use a textarea for better editing
      const fullText = originalPostText || '';
      postTextEl.innerHTML = '';
      const textarea = document.createElement('textarea');
      textarea.className = 'edit-textarea';
      textarea.value = fullText;
      textarea.style.width = '100%';
      textarea.style.minHeight = '80px';
      textarea.style.padding = '8px';
      textarea.style.background = 'transparent';
      textarea.style.color = 'var(--text)';
      textarea.style.border = '1px solid var(--border)';
      textarea.style.borderRadius = '4px';
      textarea.style.resize = 'vertical';
      postTextEl.appendChild(textarea);
      
      // Create image edit controls
      const editControls = document.createElement('div');
      editControls.className = 'edit-controls';
      editControls.style.marginTop = '10px';
      editControls.style.display = 'flex';
      editControls.style.justifyContent = 'space-between';
      editControls.style.alignItems = 'center';
      
      // Add image button
      const imageInput = document.createElement('input');
      imageInput.type = 'file';
      imageInput.accept = 'image/*';
      imageInput.id = `file-${postId}`;
      imageInput.style.display = 'none';
      
      const imageLabel = document.createElement('label');
      imageLabel.htmlFor = `file-${postId}`;
      imageLabel.innerHTML = '<i class="fas fa-image"></i> Change Image';
      imageLabel.className = 'edit-image-btn';
      imageLabel.style.cursor = 'pointer';
      imageLabel.style.padding = '5px 10px';
      imageLabel.style.background = 'var(--secondary)';
      imageLabel.style.color = 'var(--text)';
      imageLabel.style.borderRadius = '5px';
      
      // Add action buttons
      const actionButtons = document.createElement('div');
      actionButtons.style.display = 'flex';
      actionButtons.style.gap = '10px';
      
      const saveBtn = document.createElement('button');
      saveBtn.innerHTML = '<i class="fas fa-save"></i> Save';
      saveBtn.className = 'save-btn';
      saveBtn.style.padding = '5px 10px';
      saveBtn.style.background = 'var(--link)';
      saveBtn.style.color = 'var(--text)';
      saveBtn.style.border = 'none';
      saveBtn.style.borderRadius = '5px';
      saveBtn.style.cursor = 'pointer';
      
      const cancelBtn = document.createElement('button');
      cancelBtn.innerHTML = '<i class="fas fa-times"></i> Cancel';
      cancelBtn.className = 'cancel-btn';
      cancelBtn.style.padding = '5px 10px';
      cancelBtn.style.background = 'var(--secondary)';
      cancelBtn.style.color = 'var(--text)';
      cancelBtn.style.border = 'none';
      cancelBtn.style.borderRadius = '5px';
      cancelBtn.style.cursor = 'pointer';
      
      actionButtons.appendChild(saveBtn);
      actionButtons.appendChild(cancelBtn);
      
      editControls.appendChild(imageLabel);
      editControls.appendChild(imageInput);
      editControls.appendChild(actionButtons);
      
      postContent.appendChild(editControls);
      
      // Set up event handlers
      textarea.focus();
      
      // Image selection preview
      imageInput.addEventListener('change', (e) => {
        const file = e.target.files[0];
        if (file) {
          const reader = new FileReader();
          reader.onload = (event) => {
            // Create or update image preview
            if (!postMedia) {
              // Create new media container if none exists
              const newMedia = document.createElement('div');
              newMedia.className = 'post-media';
              newMedia.style.border = '2px dotted var(--link)';
              newMedia.style.padding = '10px';
              newMedia.style.borderRadius = '5px';
              newMedia.style.marginTop = '10px';
              
              const img = document.createElement('img');
              img.src = event.target.result;
              img.alt = 'Post image';
              img.style.maxWidth = '100%';
              
              newMedia.appendChild(img);
              postContent.parentNode.insertBefore(newMedia, postContent.nextSibling);
            } else {
              // Update existing image
              const img = postMedia.querySelector('img');
              img.src = event.target.result;
              img.style.display = '';
            }
          };
          reader.readAsDataURL(file);
        }
      });
      
      // Save button action
      saveBtn.addEventListener('click', () => {
        this.savePostEdit(postId, textarea.value, imageInput.files[0]);
      });
      
      // Cancel button action
      cancelBtn.addEventListener('click', () => {
        if (confirm('Discard your changes?')) {
          this.cancelPostEdit();
        }
      });
    } else {
      this.resetEditMode();
    }
  }
  
  /**
   * Save post edits to the server
   */
  async savePostEdit(postId, newText, imageFile) {
    try {
      if (!confirm('Save changes to this post?')) {
        return;
      }
      
      const formData = new FormData();
      formData.append('content', newText);
      
      if (imageFile) {
        formData.append('image', imageFile);
      }
      
      // Show loading state
      const saveBtn = this.querySelector('.save-btn');
      const originalText = saveBtn.innerHTML;
      saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';
      saveBtn.disabled = true;
      
      const response = await fetch(`${window.URLROOT}/post/edit/${postId}`, {
        method: 'POST',
        body: formData
      });
      
      const result = await response.json();
      
      if (result.status === 'success') {
        // Update the post content
        this.resetEditMode();
        
        // Update post content
        const postTextEl = this.querySelector('.post-text');
        postTextEl.innerHTML = this.truncateContent(newText);
        
        // Update image if provided
        if (result.data && result.data.imagePath) {
          const mediaPath = `${window.URLROOT}/public/media/post/${result.data.imagePath}`;
          
          const postMedia = this.querySelector('.post-media');
          if (postMedia) {
            // Update existing image
            postMedia.querySelector('img').src = mediaPath;
          } else {
            // Create new media container
            const newMedia = document.createElement('div');
            newMedia.className = 'post-media';
            const img = document.createElement('img');
            img.src = mediaPath;
            img.alt = 'Post image';
            img.onerror = function() { this.style.display='none'; };
            newMedia.appendChild(img);
            
            // Insert after post content
            const postContent = this.querySelector('.post-content');
            postContent.parentNode.insertBefore(newMedia, postContent.nextSibling);
          }
        }
      } else {
        alert('Error: ' + (result.message || 'Failed to update post'));
      }
    } catch (error) {
      console.error('Edit error:', error);
      alert('Error saving changes. Please try again.');
    }
  }
  
  /**
   * Cancel post editing and restore original content
   */
  cancelPostEdit() {
    this.resetEditMode();
    
    // Restore original text content
    const postTextEl = this.querySelector('.post-text');
    postTextEl.innerHTML = this.truncateContent(this.originalContent.text);
    
    // Restore original image if it exists
    const postMedia = this.querySelector('.post-media');
    if (this.originalContent.image) {
      if (postMedia) {
        postMedia.querySelector('img').src = this.originalContent.image;
      }
    } else if (postMedia) {
      // If there was no image originally, remove the added one
      postMedia.remove();
    }
  }
  
  /**
   * Reset edit mode UI
   */
  resetEditMode() {
    this.isEditing = false;
    
    // Remove styling
    const postContent = this.querySelector('.post-content');
    const postMedia = this.querySelector('.post-media');
    
    if (postContent) {
      postContent.style.border = '';
      postContent.style.padding = '';
      postContent.style.borderRadius = '';
    }
    
    if (postMedia) {
      postMedia.style.border = '';
      postMedia.style.padding = '';
      postMedia.style.borderRadius = '';
      postMedia.style.marginTop = '';
    }
    
    // Remove edit controls
    const editControls = this.querySelector('.edit-controls');
    if (editControls) {
      editControls.remove();
    }
  }
  
  /**
   * Helper to truncate content with show more button
   */
  truncateContent(text) {
    if (!text) return "";
    if (text.length <= 100) return text;
    const short = text.slice(0, 100).trim() + "...";
    return `${short} <span class="seemore-btn" data-action="expand-post">Show more</span>`;
  }

  /**
   * UI Popups (delete, bookmark, report) using shared styles from profile popups
   */
  _ensureOverlay(id) {
    let el = document.getElementById(id);
    if (!el) {
      el = document.createElement('div');
      el.id = id;
      el.className = 'certificate-add-popup';
      el.style.display = 'none';
      document.body.appendChild(el);
      // backdrop close
      el.addEventListener('click', (e)=>{ if (e.target === el) el.style.display = 'none'; });
    }
    return el;
  }

  _openDeletePopup(postId){
    const overlay = this._ensureOverlay(`post-delete-popup-${postId}`);
    overlay.innerHTML = `
      <div class="certificate-add">
        <button class="close-popup" title="Close"><i class="fas fa-times"></i></button>
        <div class="form-title">Delete Post</div>
        <div class="certificate-delete-body" style="color:var(--text); padding:16px;">
          <p>Are you sure you want to permanently delete this post? This action cannot be undone.</p>
        </div>
        <div style="display:flex; gap:12px; justify-content:flex-end; padding:12px 16px 4px;">
          <button type="button" class="save-btn" data-action="cancel" style="background:transparent;color:var(--text);border:1px solid var(--border);">Cancel</button>
          <button type="button" class="save-btn" data-action="confirm" style="background:var(--danger);color:#fff;">Delete</button>
        </div>
      </div>`;
    const close = overlay.querySelector('.close-popup');
    close?.addEventListener('click', ()=> overlay.style.display='none');
    overlay.querySelector('[data-action="cancel"]')?.addEventListener('click', ()=> overlay.style.display='none');
    overlay.querySelector('[data-action="confirm"]')?.addEventListener('click', async ()=>{
      try {
        const r = await fetch(`${window.URLROOT}/post/delete`, {
          method: 'DELETE',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ post_id: postId })
        });
        const js = await r.json().catch(()=>null);
        if (r.ok && js && js.status === 'success'){
          (this.closest('.post-container') || this).remove();
          overlay.style.display='none';
        } else {
          alert(js && js.message ? js.message : 'Failed to delete post');
        }
      } catch(err){
        console.error('Delete error', err);
        alert('Network error while deleting');
      }
    });
    overlay.style.display = 'flex';
  }

  _openBookmarkPopup(postId){
    const overlay = this._ensureOverlay(`post-bookmark-popup-${postId}`);
    overlay.innerHTML = `
      <div class="certificate-add">
        <button class="close-popup" title="Close"><i class="fas fa-times"></i></button>
        <div class="form-title">Add to Bookmarks</div>
        <div style="color:var(--text); padding:16px;">
          <p>Add this post to your bookmarks?</p>
        </div>
        <div style="display:flex; gap:12px; justify-content:flex-end; padding:12px 16px 4px;">
          <button type="button" class="save-btn" data-action="cancel" style="background:transparent;color:var(--text);border:1px solid var(--border);">Cancel</button>
          <button type="button" class="save-btn" data-action="confirm" style="background:var(--primary);color:#fff;">Add Bookmark</button>
        </div>
      </div>`;
    overlay.querySelector('.close-popup')?.addEventListener('click', ()=> overlay.style.display='none');
    overlay.querySelector('[data-action="cancel"]')?.addEventListener('click', ()=> overlay.style.display='none');
    overlay.querySelector('[data-action="confirm"]')?.addEventListener('click', async ()=>{
      // Dispatch an event for app-level handling; backend can listen or we can add later
      const ev = new CustomEvent('post:bookmark', { bubbles: true, detail: { postId } });
      this.dispatchEvent(ev);
      overlay.style.display='none';
      // Optional UX hint
      // alert('Bookmarked');
    });
    overlay.style.display = 'flex';
  }

  _openReportPopup(postId){
    const overlay = this._ensureOverlay(`post-report-popup-${postId}`);
    overlay.innerHTML = `
      <div class="certificate-add" style="max-width:560px;">
        <button class="close-popup" title="Close"><i class="fas fa-times"></i></button>
        <div class="form-title">Report Post</div>
        <form class="certificate-form" id="reportForm-${postId}" novalidate>
          <div class="form-group">
            <label for="reportCategory-${postId}">Category</label>
            <select id="reportCategory-${postId}" required>
              <option value="" disabled selected>Select a category</option>
              <option>Spam</option>
              <option>Harassment or bullying</option>
              <option>Hate or abusive content</option>
              <option>Misinformation</option>
              <option>Self-harm or suicide</option>
              <option>Illegal or dangerous acts</option>
              <option>Sexual content</option>
              <option>Other</option>
            </select>
          </div>
          <div class="form-group">
            <label for="reportDetails-${postId}">Details (optional)</label>
            <textarea id="reportDetails-${postId}" rows="4" placeholder="Add any details or context..." style="padding:10px;border-radius: var(--radius-lg);border:1px solid var(--border);background:var(--input);color:var(--text);"></textarea>
          </div>
          <div class="form-group">
            <label for="reportLink-${postId}">Reference link (optional)</label>
            <input type="url" id="reportLink-${postId}" placeholder="https://..." />
          </div>
          <div style="display:flex; gap:12px; justify-content:flex-end;">
            <button type="button" class="save-btn" data-action="cancel" style="background:transparent;color:var(--text);border:1px solid var(--border);">Cancel</button>
            <button type="submit" class="save-btn" style="background:var(--primary);color:#fff;">Submit Report</button>
          </div>
        </form>
      </div>`;
    overlay.querySelector('.close-popup')?.addEventListener('click', ()=> overlay.style.display='none');
    overlay.querySelector('[data-action="cancel"]')?.addEventListener('click', ()=> overlay.style.display='none');
    const form = overlay.querySelector(`#reportForm-${postId}`);
    form?.addEventListener('submit', async (e)=>{
      e.preventDefault();
      const cat = overlay.querySelector(`#reportCategory-${postId}`);
      if (!cat || !cat.value){
        alert('Please select a category');
        return;
      }
      const details = overlay.querySelector(`#reportDetails-${postId}`)?.value || '';
      const link = overlay.querySelector(`#reportLink-${postId}`)?.value || '';
      // Emit an event; backend integration can be added later
      const ev = new CustomEvent('post:report', { bubbles: true, detail: { postId, category: cat.value, details, link } });
      this.dispatchEvent(ev);
      overlay.style.display = 'none';
      // Optional: optimistic toast
      // alert('Thanks for your report');
    });
    overlay.style.display = 'flex';
  }

  _openSharePopup(postId, shareUrl){
    const overlay = this._ensureOverlay(`post-share-popup-${postId}`);
    overlay.innerHTML = `
      <div class="certificate-add" style="max-width:560px;">
        <button class="close-popup" title="Close"><i class="fas fa-times"></i></button>
        <div class="form-title">Share Post</div>
        <form class="certificate-form" id="shareForm-${postId}" onsubmit="return false;">
          <div class="form-group">
            <label for="shareLink-${postId}">Link to this post</label>
            <input type="url" id="shareLink-${postId}" value="${shareUrl}" readonly />
          </div>
          <div style="display:flex; gap:12px; justify-content:flex-end;">
            <button type="button" class="save-btn" data-action="copy" style="background:var(--primary);color:#fff;">Copy Link</button>
            <a class="save-btn" href="${shareUrl}" target="_blank" rel="noopener" style="text-decoration:none; display:inline-flex; align-items:center; justify-content:center;">Open</a>
          </div>
        </form>
      </div>`;
    overlay.querySelector('.close-popup')?.addEventListener('click', ()=> overlay.style.display='none');
    const copyBtn = overlay.querySelector('[data-action="copy"]');
    copyBtn?.addEventListener('click', async ()=>{
      const input = overlay.querySelector(`#shareLink-${postId}`);
      const val = input?.value || shareUrl;
      try {
        if (navigator.clipboard?.writeText) {
          await navigator.clipboard.writeText(val);
        } else {
          input?.select();
          document.execCommand('copy');
        }
        copyBtn.textContent = 'Copied!';
        setTimeout(()=>{ copyBtn.textContent = 'Copy Link'; }, 1200);
      } catch(err){
        alert('Could not copy link');
      }
    });
    overlay.style.display = 'flex';
  }
}

if (!customElements.get("post-card"))
  customElements.define("post-card", PostCard);
