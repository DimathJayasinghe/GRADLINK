<?php ob_start() ?>
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/admin/common.css">
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/admin/posts.css">
<?php $styles = ob_get_clean() ?>

<?php
    $sidebar_left = [
        ['label'=>'Overview', 'url'=>'/admin','active'=>false, 'icon'=>'tachometer-alt'],
        ['label'=>'User Management', 'url'=>'/admin/users','active'=>false, 'icon' => 'users'],
        ['label'=>'Engagement Metrics', 'url'=>'/admin/engagement','active'=>false, 'icon' => 'chart-bar'],
        ['label'=>'Reports', 'url'=>'/admin/reports','active'=>false, 'icon' => 'fas fa-exclamation-triangle'],
        ['label'=>'Event Moderation', 'url'=>'/admin/eventrequests','active'=>true, 'icon' => 'clipboard-list'],
        ['label'=>'Content Management', 'url'=>'/admin/posts','active'=>false, 'icon' => 'pencil-alt'],
        ['label'=>'Fundraisers', 'url'=>'/admin/fundraisers','active'=>false, 'icon' => 'donate'],
        ['label'=>'Alumni Verifications', 'url'=>'/admin/verifications','active'=>false, 'icon' => 'check-circle']
    ];
?>

<?php ob_start(); ?>
<div class="admin-header" style="border-bottom: 2px solid #3a3a3a; padding-bottom: 15px;">
    <h1>Event Requests Moderation</h1>
    <div class="admin-actions">
        <button id="bulk-approve" class="admin-btn">Approve Selected</button>
        <button id="bulk-reject" class="admin-btn" style="background-color: #b32d2dff;">Reject Selected</button>
    </div>
</div>
<div class="admin-card">
    <div class="card-header">
        <h3>Event Requests</h3>
        <div class="card-tools">
            <input type="text" id="reqSearch" placeholder="Search requests by title, club, user...">
            <select id="reqStatusFilter">
                <option value="all">All</option>
                <option value="pending">Pending</option>
                <option value="approved">Approved</option>
                <option value="rejected">Rejected</option>
            </select>
        </div>
    </div>
    <div class="admin-table-wrapper">
        <table class="admin-table" id="reqTable">
            <thead>
                <tr>
                    <th><input type="checkbox" id="selectAllReqs"></th>
                    <th>Req ID</th>
                    <th>Title</th>
                    <th>Club</th>
                    <th>Requester</th>
                    <th>Date</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <!-- filled by JS -->
            </tbody>
        </table>
    </div>
</div>

<!-- Modal -->
<div id="reqModal" class="admin-modal" style="display:none;">
    <div class="admin-modal-content" style="background-color:var(--surface-4);">
        <span class="admin-modal-close">&times;</span>
        <h2>Request Details</h2>
        <div id="modalReqContent"></div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function(){
    const tableBody = document.querySelector('#reqTable tbody');
    const selectAll = document.getElementById('selectAllReqs');
    let cache = [];

    function fetchReqs(){
        const status = document.getElementById('reqStatusFilter').value;
        const search = document.getElementById('reqSearch').value;
        fetch('<?php echo URLROOT; ?>/eventrequest/admin_list?status='+encodeURIComponent(status)+'&search='+encodeURIComponent(search))
            .then(r=>r.json()).then(data=>{ cache = data.requests || []; render(cache); });
    }

    function render(rows){
        tableBody.innerHTML = '';
        if(!rows.length){ tableBody.innerHTML = '<tr><td colspan="8" style="text-align:center;color:var(--text-secondary)">No requests found.</td></tr>'; return; }
        for(const r of rows){
            const tr = document.createElement('tr');
            tr.setAttribute('data-req-id', r.req_id);
            tr.innerHTML = `
                <td><input type="checkbox" class="selectReq"></td>
                <td>${escapeHtml(r.req_id)}</td>
                <td>${escapeHtml(r.title)}</td>
                <td>${escapeHtml(r.club_name)}</td>
                <td>${escapeHtml(r.user_name)}</td>
                <td>${escapeHtml(r.event_date||'')}</td>
                <td>${escapeHtml(r.status||'')}</td>
                <td>
                    <button class="admin-btn view-req" style="background-color: #525253ff; color: white;">View</button>
                    <button class="admin-btn approve-req">Approve</button>
                    <button class="admin-btn admin-btn-danger reject-req">Reject</button>
                </td>
            `;
            tableBody.appendChild(tr);
        }
        attachEvents();
    }

    function attachEvents(){
        selectAll.checked = false;
        selectAll.onchange = function(){ document.querySelectorAll('.selectReq').forEach(cb=>cb.checked=this.checked); };
        document.querySelectorAll('.view-req').forEach(btn=> btn.onclick = function(){
            const id = this.closest('tr').getAttribute('data-req-id');
            const req = cache.find(x=>x.req_id==id);
            document.getElementById('modalReqContent').innerHTML = `
                <b>Title:</b> ${escapeHtml(req.title)}<br>
                <b>Club:</b> ${escapeHtml(req.club_name)}<br>
                <b>Requester:</b> ${escapeHtml(req.user_name)}<br>
                <b>Date:</b> ${escapeHtml(req.event_date||'')}<br>
                <b>Time:</b> ${escapeHtml(req.event_time||'')}<br>
                <b>Venue:</b> ${escapeHtml(req.event_venue||'')}<br>
                <b>Description:</b><div style='white-space:pre-line;background:#f8f8f8;padding:0.5em;border-radius:4px; color: black'>${escapeHtml(req.description||'')}</div>
            `;
            document.getElementById('reqModal').style.display='block';
        });
        document.querySelectorAll('.approve-req').forEach(btn=> btn.onclick = function(){
            const id = this.closest('tr').getAttribute('data-req-id');
            fetch('<?php echo URLROOT; ?>/eventrequest/admin_approve_ajax/'+id).then(r=>r.json()).then(()=>fetchReqs());
        });
        document.querySelectorAll('.reject-req').forEach(btn=> btn.onclick = function(){
            const id = this.closest('tr').getAttribute('data-req-id');
            fetch('<?php echo URLROOT; ?>/eventrequest/admin_reject_ajax/'+id).then(r=>r.json()).then(()=>fetchReqs());
        });
    }

    document.querySelector('.admin-modal-close').onclick = function(){ document.getElementById('reqModal').style.display='none'; };
    window.onclick = function(e){ if(e.target == document.getElementById('reqModal')) document.getElementById('reqModal').style.display='none'; };

    document.getElementById('reqSearch').oninput = debounce(fetchReqs, 400);
    document.getElementById('reqStatusFilter').onchange = fetchReqs;
    document.getElementById('bulk-approve').onclick = function(){ const ids = Array.from(document.querySelectorAll('.selectReq:checked')).map(cb=>cb.closest('tr').getAttribute('data-req-id')); if(!ids.length) return; Promise.all(ids.map(id=>fetch('<?php echo URLROOT; ?>/eventrequest/admin_approve_ajax/'+id).then(r=>r.json()))).then(fetchReqs); };
    document.getElementById('bulk-reject').onclick = function(){ const ids = Array.from(document.querySelectorAll('.selectReq:checked')).map(cb=>cb.closest('tr').getAttribute('data-req-id')); if(!ids.length) return; Promise.all(ids.map(id=>fetch('<?php echo URLROOT; ?>/eventrequest/admin_reject_ajax/'+id).then(r=>r.json()))).then(fetchReqs); };

    function escapeHtml(str){ return String(str||'').replace(/[&<>"'`]/g, function(m){ return ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;','`':'&#96;'}[m]) || m; }); }
    function debounce(fn,ms){ let t; return function(...a){ clearTimeout(t); t=setTimeout(()=>fn.apply(this,a),ms); }; }

    fetchReqs();
});
</script>

<?php $content = ob_get_clean(); ?>
<?php require APPROOT . '/views/admin/dashboard_layout.php'; ?>

<!-- Admin Event Requests View -->