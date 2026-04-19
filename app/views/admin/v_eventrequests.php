<?php ob_start() ?>
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/admin/common.css">
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/admin/posts.css">
<style>
.admin-modal { display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; background: rgba(6, 10, 16, 0.58); backdrop-filter: blur(2px); }
.admin-modal-content { background: #0e1b28; margin: 5% auto; padding: 1.5rem 1.6rem; border-radius: 10px; width: 92%; max-width: 560px; position: relative; border: 1px solid rgba(255, 255, 255, 0.08); box-shadow: 0 14px 36px rgba(0, 0, 0, 0.35); color: #d9e3ee; }
.admin-modal-content h2 { margin: 0 0 0.9rem; color: #ffffff; font-size: 1.25rem; }
#modalReqContent { line-height: 1.55; }
.req-modal-grid { display: grid; grid-template-columns: 92px 1fr; gap: 8px 12px; align-items: start; font-size: 0.95rem; }
.req-modal-grid dt { margin: 0; color: #b9c7d6; font-weight: 600; }
.req-modal-grid dd { margin: 0; color: #eef4fb; word-break: break-word; }
.req-modal-description { margin-top: 0.9rem; padding-top: 0.75rem; border-top: 1px solid rgba(255, 255, 255, 0.12); }
.req-modal-description-title { margin: 0 0 0.4rem; color: #b9c7d6; font-size: 0.85rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.04em; }
.req-modal-description-box { white-space: pre-line; background: rgba(255, 255, 255, 0.04); padding: 0.85rem 0.95rem; border-radius: 8px; border: 1px solid rgba(255, 255, 255, 0.08); color: #eef4fb; }
.admin-modal-close { position: absolute; top: 0.75rem; right: 0.95rem; font-size: 1.45rem; color: #c9d5e2; cursor: pointer; line-height: 1; }
.admin-modal-close:hover { color: #ffffff; }
</style>
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
        ['label'=>'Alumni Verifications', 'url'=>'/admin/verifications','active'=>false, 'icon' => 'check-circle'],
        ['label'=>'Suspended Users', 'url'=>'/admin/suspendedUsers','active'=>false, 'icon' => 'user-slash'],
        ['label'=>'Help & Support', 'url'=>'/admin/support','active'=>false, 'icon' => 'circle-question']
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
            <input type="text" id="reqSearch" style="background-color:#3a3a3a; color:aliceblue; padding:4px 8px; border:none; border-radius:4px;" placeholder="Search requests by title, club, user...">
            <select id="reqStatusFilter" style="background-color:#3a3a3a; color:aliceblue; padding:4px 8px; border:none; border-radius:4px;">
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

<div class="admin-card" style="margin-top: 1rem;">
    <div class="card-header">
        <h3>Ongoing Events</h3>
        <div class="card-tools">
            <input type="text" id="ongoingSearch" style="background-color:#3a3a3a; color:aliceblue; padding:4px 8px; border:none; border-radius:4px;" placeholder="Search events by title, venue, organizer...">
        </div>
    </div>
    <div class="admin-table-wrapper">
        <table class="admin-table" id="ongoingTable">
            <thead>
                <tr>
                    <th>Event ID</th>
                    <th>Title</th>
                    <th>Organizer</th>
                    <th>Start</th>
                    <th>Venue</th>
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
    <div class="admin-modal-content">
        <span class="admin-modal-close">&times;</span>
        <h2>Details</h2>
        <div id="modalReqContent"></div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function(){
    const reqTableBody = document.querySelector('#reqTable tbody');
    const ongoingTableBody = document.querySelector('#ongoingTable tbody');
    const selectAll = document.getElementById('selectAllReqs');
    let reqCache = [];
    let ongoingCache = [];
    const modal = document.getElementById('reqModal');
    const modalContent = document.getElementById('modalReqContent');

    function fetchReqs(){
        const status = document.getElementById('reqStatusFilter').value;
        const search = document.getElementById('reqSearch').value;
        fetch('<?php echo URLROOT; ?>/eventrequest/admin_list?status='+encodeURIComponent(status)+'&search='+encodeURIComponent(search))
            .then(r=>r.json())
            .then(data=>{
                reqCache = data.requests || [];
                renderRequests(reqCache);
            });
    }

    function fetchOngoingEvents(){
        const search = document.getElementById('ongoingSearch').value;
        fetch('<?php echo URLROOT; ?>/eventrequest/admin_events_list?search=' + encodeURIComponent(search))
            .then(r=>r.json())
            .then(data=>{
                ongoingCache = data.events || [];
                renderOngoing(ongoingCache);
            });
    }

    function renderRequests(rows){
        reqTableBody.innerHTML = '';
        if(!rows.length){
            reqTableBody.innerHTML = '<tr><td colspan="8" style="text-align:center;color:var(--text-secondary)">No requests found.</td></tr>';
            return;
        }

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
                    <button class="admin-btn view-req" style="background-color: #525253ff; color: white; margin-bottom:8px;">View</button>
                    ${escapeHtml(r.status) == 'Rejected' ? '<button class="admin-btn approve-req">Approve</button>' : ''}
                    ${escapeHtml(r.status) == 'Pending' ? '<button class="admin-btn approve-req">Approve</button><button class="admin-btn admin-btn-danger reject-req" style="margin-left: 6px;">Reject</button>' : ''}
                    ${escapeHtml(r.status) == 'Approved' ? '' : ''}
                    
                </td>
            `;
            reqTableBody.appendChild(tr);
        }

        attachRequestEvents();
    }

    function renderOngoing(rows){
        ongoingTableBody.innerHTML = '';
        if(!rows.length){
            ongoingTableBody.innerHTML = '<tr><td colspan="7" style="text-align:center;color:var(--text-secondary)">No ongoing events found.</td></tr>';
            return;
        }

        for (const e of rows) {
            const tr = document.createElement('tr');
            tr.setAttribute('data-event-id', e.id);
            tr.innerHTML = `
                <td>${escapeHtml(e.id)}</td>
                <td>${escapeHtml(e.title)}</td>
                <td>${escapeHtml(e.organizer_name || '')}</td>
                <td>${escapeHtml(e.start_datetime || '')}</td>
                <td>${escapeHtml(e.venue || '')}</td>
                <td>${escapeHtml(e.status || '')}</td>
                <td>
                    <button class="admin-btn view-event" style="background-color: #525253ff; color: white;">View</button>
                    <button class="admin-btn admin-btn-danger delete-event" style="margin-left: 6px;">Delete</button>
                </td>
            `;
            ongoingTableBody.appendChild(tr);
        }

        attachOngoingEvents();
    }

    function attachRequestEvents(){
        selectAll.checked = false;
        selectAll.onchange = function(){ document.querySelectorAll('.selectReq').forEach(cb=>cb.checked=this.checked); };
        document.querySelectorAll('.view-req').forEach(btn=> btn.onclick = function(){
            const id = this.closest('tr').getAttribute('data-req-id');
            const req = reqCache.find(x=>x.req_id==id);
            if(!req) return;
            modalContent.innerHTML = `
                <dl class="req-modal-grid">
                    <dt>Title</dt><dd>${escapeHtml(req.title)}</dd>
                    <dt>Club</dt><dd>${escapeHtml(req.club_name)}</dd>
                    <dt>Requester</dt><dd>${escapeHtml(req.user_name)}</dd>
                    <dt>Date</dt><dd>${escapeHtml(req.event_date||'')}</dd>
                    <dt>Time</dt><dd>${escapeHtml(req.event_time||'')}</dd>
                    <dt>Venue</dt><dd>${escapeHtml(req.event_venue||'')}</dd>
                    <dt>Status</dt><dd>${escapeHtml(req.status||'')}</dd>
                </dl>
                <div class="req-modal-description">
                    <div class="req-modal-description-title">Description</div>
                    <div class="req-modal-description-box">${escapeHtml(req.description||'')}</div>
                </div>
            `;
            modal.style.display='block';
        });
        document.querySelectorAll('.approve-req').forEach(btn=> btn.onclick = function(){
            const id = this.closest('tr').getAttribute('data-req-id');
            fetch('<?php echo URLROOT; ?>/eventrequest/admin_approve_ajax/'+id)
                .then(r=>r.json())
                .then(()=>{ fetchReqs(); fetchOngoingEvents(); });
        });
        document.querySelectorAll('.reject-req').forEach(btn=> btn.onclick = function(){
            const id = this.closest('tr').getAttribute('data-req-id');
            fetch('<?php echo URLROOT; ?>/eventrequest/admin_reject_ajax/'+id)
                .then(r=>r.json())
                .then(()=>{ fetchReqs(); fetchOngoingEvents(); });
        });
    }

    function attachOngoingEvents(){
        document.querySelectorAll('.view-event').forEach(btn => btn.onclick = function(){
            const id = this.closest('tr').getAttribute('data-event-id');
            const event = ongoingCache.find(x => String(x.id) === String(id));
            if(!event) return;
            modalContent.innerHTML = `
                <dl class="req-modal-grid">
                    <dt>Title</dt><dd>${escapeHtml(event.title)}</dd>
                    <dt>Organizer</dt><dd>${escapeHtml(event.organizer_name || '')}</dd>
                    <dt>Start</dt><dd>${escapeHtml(event.start_datetime || '')}</dd>
                    <dt>End</dt><dd>${escapeHtml(event.end_datetime || '')}</dd>
                    <dt>Venue</dt><dd>${escapeHtml(event.venue || '')}</dd>
                    <dt>Status</dt><dd>${escapeHtml(event.status || '')}</dd>
                </dl>
                <div class="req-modal-description">
                    <div class="req-modal-description-title">Description</div>
                    <div class="req-modal-description-box">${escapeHtml(event.description || '')}</div>
                </div>
            `;
            modal.style.display = 'block';
        });

        document.querySelectorAll('.delete-event').forEach(btn => btn.onclick = function(){
            const id = this.closest('tr').getAttribute('data-event-id');
            if(!id) return;
            if(!confirm('Delete this event? This will remove it from ongoing events.')) return;
            fetch('<?php echo URLROOT; ?>/eventrequest/admin_delete_event_ajax/' + id)
                .then(r=>r.json())
                .then(()=>{ fetchReqs(); fetchOngoingEvents(); });
        });
    }

    document.querySelector('.admin-modal-close').onclick = function(){ modal.style.display='none'; };
    window.onclick = function(e){ if(e.target == modal) modal.style.display='none'; };

    document.getElementById('reqSearch').oninput = debounce(fetchReqs, 400);
    document.getElementById('reqStatusFilter').onchange = fetchReqs;
    document.getElementById('ongoingSearch').oninput = debounce(fetchOngoingEvents, 400);
    document.getElementById('bulk-approve').onclick = function(){
        const ids = Array.from(document.querySelectorAll('.selectReq:checked')).map(cb=>cb.closest('tr').getAttribute('data-req-id'));
        if(!ids.length) return;
        Promise.all(ids.map(id=>fetch('<?php echo URLROOT; ?>/eventrequest/admin_approve_ajax/'+id).then(r=>r.json())))
            .then(()=>{ fetchReqs(); fetchOngoingEvents(); });
    };
    document.getElementById('bulk-reject').onclick = function(){
        const ids = Array.from(document.querySelectorAll('.selectReq:checked')).map(cb=>cb.closest('tr').getAttribute('data-req-id'));
        if(!ids.length) return;
        Promise.all(ids.map(id=>fetch('<?php echo URLROOT; ?>/eventrequest/admin_reject_ajax/'+id).then(r=>r.json())))
            .then(()=>{ fetchReqs(); fetchOngoingEvents(); });
    };

    function escapeHtml(str){ return String(str||'').replace(/[&<>"'`]/g, function(m){ return ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;','`':'&#96;'}[m]) || m; }); }
    function debounce(fn,ms){ let t; return function(...a){ clearTimeout(t); t=setTimeout(()=>fn.apply(this,a),ms); }; }

    fetchReqs();
    fetchOngoingEvents();
});
</script>

<?php $content = ob_get_clean(); ?>
<?php require APPROOT . '/views/admin/dashboard_layout.php'; ?>

<!-- Admin Event Requests View -->