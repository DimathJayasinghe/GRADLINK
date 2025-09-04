class PostCard extends HTMLElement {
  connectedCallback() {
    const root = `${location.origin}/GRADLINK/public/img`;
    // Use secure media controller endpoints (served via /media/...)
    const appBase = `${location.origin}/GRADLINK`;
    const mediaProfile = (name) =>`${appBase}/public/media/profile/${encodeURIComponent(name)}`; // Core.php default RewriteBase may need adjusting for /GRADLINK/public
    const mediaPost = (name) =>`${appBase}/public/media/post/${encodeURIComponent(name)}`;
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
      <div class="post ${(postOwnerRole == "admin") ? "admin-post":""}">
        <div class="post-header">
          <div class="post-user">
            <img src="${mediaProfile(profileImg)}" alt="User" class="profile-photo" onerror="this.onerror=null;this.src='${mediaProfile("default.jpg")}'">
            <div class="post-user-info">
              <span class="post-user-name">${userName + ((postOwnerRole == "admin") ? "⭐⭐" : (postOwnerRole == "alumni") ? "⭐" : "")}</span>
              <span class="post-user-handle">${userHandle}</span>
              <span class="post-time"> · ${postTime}</span>
              <div class="post-content"><p class="post-text">${truncatePostText(postText)}</p></div>
            </div>
          </div>
          <div class="post-menu" role="button" aria-haspopup="true" aria-expanded="false">
            <i class="fas fa-ellipsis-h post-menu-btn"></i>
            <div class="post-dropdown hidden" role="menu">
              <div class="dropdown-item" data-action="bookmark" role="menuitem">Bookmark</div>
              <div class="dropdown-item" data-action="report" role="menuitem">Report</div>
              ${currentUserRole ==="admin"? '<div class="dropdown-item" data-action="suspend" role="menuitem">suspend</div>':""}
              ${isOwner ? '<div class="dropdown-item" data-action="delete-post" role="menuitem">Delete</div>' : ''}
              ${isOwner ? '<div class="dropdown-item" data-action="edit-post" role="menuitem">Edit</div>' : ''}
              
            </div>
          </div>
        </div>
        ${postImg? `<div class=\"post-media\"><img src=\"${mediaPost(postImg)}\" alt=\"Post image\" onerror=\"this.style.display='none'\"></div>`: ""}
        
        <div class="post-actions">
          <div class="comment-btn" data-post-id="${postId}"><i class="far fa-comment"></i> <span class="comment-count">${commentCount}</span></div>
          <div class="like-btn${likedInitial ? " liked" : ""}" data-post-id="${postId}"><i class="${likedInitial ? "fas" : "far"} fa-heart"></i> <span class="like-count">${likeCount}</span></div>
        </div>
        
        <div class="pc-comments" style="display:none;border-top:1px solid var(--border);margin-top:10px;padding-top:8px">
          <div class="pc-comments-list" style="max-height:200px;overflow:auto;color:#ccc"></div>
          <div style="display:flex;gap:6px;margin-top:6px">
            <input type="text" class="pc-comment-input" placeholder="Add a comment" style="flex:1;padding:6px;border:1px solid var(--border);background:transparent;color:#fff" />
            <button class="pc-comment-send" style="padding:6px 10px">Send</button>
          </div>
        </div>

      </div>`;

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
      // simple action handlers (placeholder)
      dropdown.addEventListener('click', (e)=>{
        const item = e.target.closest('.dropdown-item');
        if(!item) return;
        const act = item.getAttribute('data-action');
        dropdown.classList.add('hidden');
        menu.setAttribute('aria-expanded','false');
        if(act === 'bookmark'){
          // TODO: implement bookmark endpoint
          console.log('Bookmark placeholder');
        } else if(act === 'report') {
          console.log('Report placeholder');
        } else if(act === 'delete-post') {
          if(!isOwner) return; // safety
          console.log('Delete post placeholder for', postId);
          // TODO: call delete endpoint then remove element
        } else if(act === 'edit-post') {
          if(!isOwner) return; // safety
          console.log('Edit post placeholder for', postId);
          // TODO: open edit modal / inline editor
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
    const postTextEl = this.querySelector('.post-text');
    // Delegated click handler (post + comments)
    this.addEventListener('click', (e)=>{
      const target = e.target;
      // Post text expand/collapse
      if(postText.length > 100){
        if(target.matches('[data-action="expand-post"]')){
          postTextEl.innerHTML = `${postText} <span class=\"seemore-btn\" data-action=\"collapse-post\">Show less</span>`;
          return;
        } else if(target.matches('[data-action="collapse-post"]')) {
          postTextEl.innerHTML = truncatePostText(postText);
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
        return `<div class=\"comment-item\" style=\"margin-bottom:10px\"><div class=\"bubble\"><strong>${this._esc((c.name || "User") + star)}</strong><br><span class=\"comment-text post-text">${body}</span><span class=\"meta\">${rel}</span></div></div>`;
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
}
if (!customElements.get("post-card"))
  customElements.define("post-card", PostCard);
