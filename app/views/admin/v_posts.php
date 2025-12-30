<?php ob_start()?>
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/css/admin/common.css">
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/css/admin/posts.css">
    <style>
        .post-modal-overlay { display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; overflow-y: auto; background: rgba(0,0,0,0.7); padding: 20px 0; }
        .post-modal-card { background: #1a1a1a; border: 1px solid #333; border-radius: 12px; margin: 20px auto; padding: 0; width: 90%; max-width: 500px; box-shadow: 0 8px 24px rgba(0,0,0,0.5); }
        .post-modal-header { display: flex; justify-content: space-between; align-items: center; padding: 16px 20px; border-bottom: 1px solid #333; }
        .post-modal-header h2 { margin: 0; font-size: 20px; color: #fff; }
        .post-modal-close { cursor: pointer; font-size: 28px; color: #999; line-height: 1; }
        .post-modal-close:hover { color: #fff; }
        .post-modal-body { padding: 20px; }
        .post-author-section { display: flex; align-items: center; gap: 12px; margin-bottom: 16px; }
        .post-author-avatar { width: 48px; height: 48px; border-radius: 50%; background: #333; object-fit: cover; }
        .post-author-info { flex: 1; }
        .post-author-name { font-size: 16px; font-weight: 600; color: #fff; }
        .post-author-date { font-size: 13px; color: #999; margin-top: 2px; }
        .post-image { width: 100%; border-radius: 8px; margin: 16px 0; object-fit: contain; aspect-ratio: 1; background: #0a0a0a; }
        .post-content { font-size: 14px; line-height: 1.6; color: #ddd; white-space: pre-wrap; word-wrap: break-word; margin: 16px 0; }
        .post-stats { display: flex; gap: 24px; padding: 16px 0; border-top: 1px solid #333; border-bottom: 1px solid #333; margin: 16px 0; color: #999; font-size: 13px; }
        .post-stat { display: flex; align-items: center; gap: 6px; }
        .post-modal-actions { display: flex; gap: 10px; padding-top: 16px; }
        .post-modal-actions button { flex: 1; padding: 10px; border: none; border-radius: 6px; cursor: pointer; font-weight: 600; }
        .post-modal-delete { background: #dc3545; color: white; }
        .post-modal-delete:hover { background: #c82333; }
    </style>
<?php $styles = ob_get_clean()?>


<?php
    $sidebar_left = [
        ['label'=>'Overview', 'url'=>'/admin','active'=>false, 'icon'=>'tachometer-alt'],
        ['label'=>'User Management', 'url'=>'/admin/users','active'=>false, 'icon' => 'users'],
        ['label'=>'Engagement Metrics', 'url'=>'/admin/engagement','active'=>false, 'icon' => 'chart-bar'],
        ['label'=>'Reports', 'url'=>'/admin/reports','active'=>false, 'icon' => 'fas fa-exclamation-triangle'],
        ['label'=>'Event Moderation', 'url'=>'/admin/eventrequests','active'=>false, 'icon' => 'clipboard-list'],
        ['label'=>'Content Management', 'url'=>'/admin/posts','active'=>true, 'icon' => 'pencil-alt'],
        ['label'=>'Fundraisers', 'url'=>'/admin/fundraisers','active'=>false, 'icon' => 'donate'],
        ['label'=>'Alumni Verifications', 'url'=>'/admin/verifications','active'=>false, 'icon' => 'check-circle']
    ]
?>

<?php ob_start();?>
<div class="admin-header" style="border-bottom: 2px solid #3a3a3a; padding-bottom: 15px;">
    <h1>Content Management</h1>
    <div class="admin-actions">
        <button id="bulk-delete" class="admin-btn admin-btn-danger">Delete Selected</button>
    </div>
</div>
<div class="admin-card">
    <div class="card-header">
        <h3>Posts Moderation</h3>
        <div class="card-tools">
            <input type="text" id="postSearch" style="background-color:#3a3a3a; color:aliceblue; padding:4px 8px; border:none; border-radius:4px;" placeholder="Search posts by user, content...">
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
<div id="postModal" class="post-modal-overlay" style="display:none;">
    <div class="post-modal-card">
        <div class="post-modal-header">
            <h2>Post Details</h2>
            <span class="post-modal-close">&times;</span>
        </div>
        <div class="post-modal-body">
            <div id="modalPostContent">
                <!-- Filled by JS -->
            </div>
        </div>
    </div>
</div>

<script>
// JS for Content Management (posts moderation)
document.addEventListener('DOMContentLoaded', function() {
    const postsTableBody = document.querySelector('#postsTable tbody');
    const selectAll = document.getElementById('selectAllPosts');
    const modal = document.getElementById('postModal');
    const modalClose = document.querySelector('.post-modal-close');
    let postsCache = [];

    function fetchPosts() {
        const search = document.getElementById('postSearch').value;
        fetch(`<?php echo URLROOT; ?>/post/admin_list?status=all&search=${encodeURIComponent(search)}`)
            .then(r => r.json())
            .then(data => {
                if (data.error) {
                    console.error('Error:', data.error);
                    postsTableBody.innerHTML = '<tr><td colspan="7" style="text-align:center;color:red">Error loading posts</td></tr>';
                    return;
                }
                postsCache = data.posts || [];
                renderPosts(postsCache);
            })
            .catch(err => {
                console.error('Fetch error:', err);
                postsTableBody.innerHTML = '<tr><td colspan="7" style="text-align:center;color:red">Failed to load posts</td></tr>';
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
            const status = 'Active';
            const dateStr = post.created_at || post.date || '';
            const displayDate = dateStr ? new Date(dateStr).toLocaleDateString() : '';
            tr.innerHTML = `
                <td><input type="checkbox" class="selectPost"></td>
                <td>${escapeHtml(post.id)}</td>
                <td>${escapeHtml(post.author)}</td>
                <td>${escapeHtml(post.content.length > 60 ? post.content.substring(0, 60) + '...' : post.content)}</td>
                <td>${escapeHtml(displayDate)}</td>
                <td><span class="status-badge status-pending">${status}</span></td>
                <td>
                    <button class="admin-btn view-post" style="margin:0.2em; width:80px;">View</button>
                    <button class="admin-btn admin-btn-danger delete-post" style="width:80px;">Delete</button>
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
                if (!post) return;
                
                console.log('Post data:', post);
                console.log('Image field:', post.image);
                
                const dateStr = post.created_at || post.date || '';
                const displayDate = dateStr ? new Date(dateStr).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' }) : 'N/A';
                
                let imageHtml = '';
                if (post.image) {
                    const imgUrl = `<?php echo URLROOT; ?>/post/image/${escapeHtml(post.image)}`;
                    console.log('Image URL:', imgUrl);
                    imageHtml = `<img src="${imgUrl}" class="post-image" alt="Post image">`;
                }
                
                document.getElementById('modalPostContent').innerHTML = `
                    <div class="post-author-section">
                        <img src="${escapeHtml(URLROOT)}/media/profile/default.jpg" class="post-author-avatar" alt="Author">
                        <div class="post-author-info">
                            <div class="post-author-name">${escapeHtml(post.author)}</div>
                            <div class="post-author-date">${escapeHtml(displayDate)}</div>
                        </div>
                    </div>
                    ${imageHtml}
                    <div class="post-content">${escapeHtml(post.content)}</div>
                    <div class="post-stats">
                        <div class="post-stat"><i class="fas fa-eye"></i> Post ID: ${escapeHtml(post.id)}</div>
                    </div>
                    <div class="post-modal-actions">
                        <button class="post-modal-delete" onclick="deletePostFromModal(${post.id})">Delete Post</button>
                    </div>
                `;
                modal.style.display = 'block';
            };
        });
        // Approve, Reject, Delete
        document.querySelectorAll('.delete-post').forEach(btn => {
            btn.onclick = function() {
                const row = this.closest('tr');
                const postId = row.getAttribute('data-post-id');
                if(confirm('Delete this post? This action cannot be undone.')) {
                    fetch(`<?php echo URLROOT; ?>/post/admin_delete/${postId}`, { method: 'GET' })
                        .then(r => r.json())
                        .then(data => {
                            if (data.ok) {
                                fetchPosts();
                            } else {
                                alert('Failed to delete post');
                            }
                        })
                        .catch(err => {
                            console.error('Delete error:', err);
                            alert('Error deleting post');
                        });
                }
            };
        });
    }

    // Bulk actions
    document.getElementById('bulk-delete').onclick = function() {
        const ids = getSelectedPostIds();
        if (!ids.length) {
            alert('Please select at least one post.');
            return;
        }
        if(confirm(`Delete ${ids.length} selected post(s)? This action cannot be undone.`)) {
            Promise.all(ids.map(id => fetch(`<?php echo URLROOT; ?>/post/admin_delete/${id}`, { method: 'GET' }).then(r => r.json())))
                .then(() => fetchPosts())
                .catch(err => {
                    console.error('Bulk delete error:', err);
                    alert('Error during bulk delete');
                });
        }
    };

    function getSelectedPostIds() {
        return Array.from(document.querySelectorAll('.selectPost:checked')).map(cb => cb.closest('tr').getAttribute('data-post-id'));
    }

    // Search/filter
    document.getElementById('postSearch').oninput = debounce(fetchPosts, 400);

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
    function deletePostFromModal(postId) {
        if(confirm('Delete this post? This action cannot be undone.')) {
            fetch(`<?php echo URLROOT; ?>/post/admin_delete/${postId}`, { method: 'GET' })
                .then(r => r.json())
                .then(data => {
                    if (data.ok) {
                        modal.style.display = 'none';
                        fetchPosts();
                    } else {
                        alert('Failed to delete post');
                    }
                })
                .catch(err => {
                    console.error('Delete error:', err);
                    alert('Error deleting post');
                });
        }
    }
    
    // Make URLROOT and modal accessible to deletePostFromModal
    const URLROOT = '<?php echo URLROOT; ?>';

    // Initial load
    fetchPosts();
});
</script>

<?php $content = ob_get_clean(); ?>
<?php require APPROOT . '/views/admin/dashboard_layout.php'; ?>

