<?php require APPROOT . '/views/inc/header.php'; ?>
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/admin/posts.css">

<div class="admin-header">
    <h1>Content Management</h1>
    <div class="admin-actions">
        <button id="bulk-approve" class="admin-btn">Approve Selected</button>
        <button id="bulk-reject" class="admin-btn">Reject Selected</button>
        <button id="bulk-delete" class="admin-btn admin-btn-danger">Delete Selected</button>
    </div>
</div>
<div class="admin-card">
    <div class="card-header">
        <h3>Posts Moderation</h3>
        <div class="card-tools">
            <input type="text" id="postSearch" placeholder="Search posts by user, content, status...">
            <select id="postStatusFilter">
                <option value="all">All</option>
                <option value="pending">Pending</option>
                <option value="approved">Approved</option>
                <option value="rejected">Rejected</option>
                <option value="reported">Reported</option>
            </select>
        </div>
    </div>
    <div class="admin-table-wrapper">
        <table class="admin-table" id="postsTable">
            <thead>
                <tr>
                    <th><input type="checkbox" id="selectAllPosts"></th>
                    <th>Post ID</th>
                    <th>Author</th>
                    <th>Content</th>
                    <th>Date</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <!-- Posts will be loaded here by JS -->
            </tbody>
        </table>
    </div>
</div>

<!-- Modal for viewing post details -->
<div id="postModal" class="admin-modal" style="display:none;">
    <div class="admin-modal-content">
        <span class="admin-modal-close">&times;</span>
        <h2>Post Details</h2>
        <div id="modalPostContent">
            <!-- Filled by JS -->
        </div>
    </div>
</div>

<script>
// JS for Content Management (posts moderation)
document.addEventListener('DOMContentLoaded', function() {
    const postsTableBody = document.querySelector('#postsTable tbody');
    const selectAll = document.getElementById('selectAllPosts');
    const modal = document.getElementById('postModal');
    const modalClose = document.querySelector('.admin-modal-close');
    let postsCache = [];

    function fetchPosts() {
        const status = document.getElementById('postStatusFilter').value;
        const search = document.getElementById('postSearch').value;
        fetch(`<?php echo URLROOT; ?>/post/admin_list?status=${encodeURIComponent(status)}&search=${encodeURIComponent(search)}`)
            .then(r => r.json())
            .then(data => {
                postsCache = data.posts || [];
                renderPosts(postsCache);
            });
    }

    function renderPosts(posts) {
        postsTableBody.innerHTML = '';
        if (!posts.length) {
            postsTableBody.innerHTML = '<tr><td colspan="7" style="text-align:center;color:var(--text-secondary)">No posts found.</td></tr>';
            return;
        }
        for (const post of posts) {
            const tr = document.createElement('tr');
            tr.setAttribute('data-post-id', post.id);
            const status = post.status ? capitalize(post.status) : 'N/A';
            tr.innerHTML = `
                <td><input type="checkbox" class="selectPost"></td>
                <td>${escapeHtml(post.id)}</td>
                <td>${escapeHtml(post.author)}</td>
                <td>${escapeHtml(post.content.length > 60 ? post.content.substring(0, 60) + '...' : post.content)}</td>
                <td>${escapeHtml(post.created_at || post.date || '')}</td>
                <td><span class="status-badge status-na">${status}</span></td>
                <td>
                    <button class="admin-btn view-post">View</button>
                    <button class="admin-btn admin-btn-danger delete-post">Delete</button>
                </td>
            `;
            postsTableBody.appendChild(tr);
        }
        attachRowEvents();
    }

    function attachRowEvents() {
        // Select all posts
        selectAll.checked = false;
        selectAll.onchange = function() {
            document.querySelectorAll('.selectPost').forEach(cb => cb.checked = this.checked);
        };
        // Modal logic
        document.querySelectorAll('.view-post').forEach(btn => {
            btn.onclick = function() {
                const row = this.closest('tr');
                const postId = row.getAttribute('data-post-id');
                const post = postsCache.find(p => p.id == postId);
                document.getElementById('modalPostContent').innerHTML =
                    `<b>Post ID:</b> ${escapeHtml(post.id)}<br>
                    <b>Author:</b> ${escapeHtml(post.author)}<br>
                    <b>Date:</b> ${escapeHtml(post.created_at || post.date || '')}<br>
                    <b>Status:</b> ${capitalize(post.status)}<br>
                    <b>Content:</b><br><div style='white-space:pre-line;background:#f8f8f8;padding:0.5em;border-radius:4px;'>${escapeHtml(post.content)}</div>`;
                modal.style.display = 'block';
            };
        });
        // Approve, Reject, Delete
        document.querySelectorAll('.approve-post').forEach(btn => {
            btn.onclick = function() {
                const row = this.closest('tr');
                const postId = row.getAttribute('data-post-id');
                fetch(`<?php echo URLROOT; ?>/post/admin_approve/${postId}`)
                    .then(r => r.json()).then(() => fetchPosts());
            };
        });
        document.querySelectorAll('.reject-post').forEach(btn => {
            btn.onclick = function() {
                const row = this.closest('tr');
                const postId = row.getAttribute('data-post-id');
                fetch(`<?php echo URLROOT; ?>/post/admin_reject/${postId}`)
                    .then(r => r.json()).then(() => fetchPosts());
            };
        });
        document.querySelectorAll('.delete-post').forEach(btn => {
            btn.onclick = function() {
                const row = this.closest('tr');
                const postId = row.getAttribute('data-post-id');
                if(confirm('Delete this post?')) {
                    fetch(`<?php echo URLROOT; ?>/post/admin_delete/${postId}`)
                        .then(r => r.json()).then(() => fetchPosts());
                }
            };
        });
    }

    // Bulk actions
    document.getElementById('bulk-approve').onclick = function() {
        const ids = getSelectedPostIds();
        if (!ids.length) return;
        Promise.all(ids.map(id => fetch(`<?php echo URLROOT; ?>/post/admin_approve/${id}`).then(r => r.json()))).then(fetchPosts);
    };
    document.getElementById('bulk-reject').onclick = function() {
        const ids = getSelectedPostIds();
        if (!ids.length) return;
        Promise.all(ids.map(id => fetch(`<?php echo URLROOT; ?>/post/admin_reject/${id}`).then(r => r.json()))).then(fetchPosts);
    };
    document.getElementById('bulk-delete').onclick = function() {
        const ids = getSelectedPostIds();
        if (!ids.length) return;
        if(confirm('Delete selected posts?')) {
            Promise.all(ids.map(id => fetch(`<?php echo URLROOT; ?>/post/admin_delete/${id}`).then(r => r.json()))).then(fetchPosts);
        }
    };

    function getSelectedPostIds() {
        return Array.from(document.querySelectorAll('.selectPost:checked')).map(cb => cb.closest('tr').getAttribute('data-post-id'));
    }

    // Search/filter
    document.getElementById('postSearch').oninput = debounce(fetchPosts, 400);
    document.getElementById('postStatusFilter').onchange = fetchPosts;

    // Modal close
    modalClose.onclick = () => modal.style.display = 'none';
    window.onclick = (e) => { if (e.target == modal) modal.style.display = 'none'; };

    // Helpers
    function escapeHtml(str) {
        return String(str).replace(/[&<>"']/g, function(m) {
            return ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;','\'':'&#39;'}[m]);
        });
    }
    function capitalize(str) {
        return str ? str.charAt(0).toUpperCase() + str.slice(1) : '';
    }
    function debounce(fn, ms) {
        let t; return function(...a) { clearTimeout(t); t = setTimeout(() => fn.apply(this, a), ms); };
    }

    // Initial load
    fetchPosts();
});
</script>

<?php require APPROOT . '/views/inc/footer.php'; ?>
