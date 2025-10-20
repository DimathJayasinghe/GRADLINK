<?php ob_start()?>
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/css/admin/common.css">
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/css/admin/dashboard-common.css">
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/css/admin/fundraiser.css">
<?php $styles = ob_get_clean()?>
<?php
    $sidebar_left = [
        ['label'=>'Overview', 'url'=>'/admin','active'=>false, 'icon'=>'tachometer-alt'],
        ['label'=>'User Management', 'url'=>'/admin/users','active'=>false, 'icon' => 'users'],
        ['label'=>'Engagement Metrics', 'url'=>'/admin/engagement','active'=>false, 'icon' => 'chart-bar'],
        ['label'=>'Reports', 'url'=>'/admin/reports','active'=>false, 'icon' => 'fas fa-exclamation-triangle'],
        ['label'=>'Event Moderation', 'url'=>'/admin/eventrequests','active'=>false, 'icon' => 'clipboard-list'],
        ['label'=>'Content Management', 'url'=>'/admin/posts','active'=>false, 'icon' => 'pencil-alt'],
        ['label'=>'Fundraisers', 'url'=>'/admin/fundraisers','active'=>true, 'icon' => 'donate'],
        ['label'=>'Alumni Verifications', 'url'=>'/admin/verifications','active'=>false, 'icon' => 'check-circle']
    ]
?>
<?php ob_start();?>
<div class="admin-fundraiser">
    <div class="admin-header">
        <h1>Fundraisers</h1>
        <div class="admin-actions">
            <button class="admin-btn" id="create-fund">Create Fundraiser</button>
            <button class="admin-btn" id="export-funds">Export CSV</button>
        </div>
    </div>

    <section class="fund-kpis">
        <div class="fund-kpi"><span class="label">Open Campaigns</span><span class="value" id="kpi-open">--</span></div>
        <div class="fund-kpi"><span class="label">Total Raised</span><span class="value" id="kpi-raised">--</span></div>
        <div class="fund-kpi"><span class="label">Active Clubs</span><span class="value" id="kpi-clubs">--</span></div>
        <div class="fund-kpi"><span class="label">Expiring Soon</span><span class="value" id="kpi-expire">--</span></div>
    </section>

    <div class="fund-controls">
        <input type="search" id="fund-search" placeholder="Search by title, club or organizer">
        <select id="fund-status">
            <option value="">All status</option>
            <option>Pending</option>
            <option>Approved</option>
            <option>Rejected</option>
            <option>Expired</option>
        </select>
        <select id="fund-club">
            <option value="">All clubs</option>
        </select>
        <button class="btn" id="fund-filter">Apply</button>
        <div style="flex:1"></div>
        <div class="card-compact"><strong>Sort</strong>
            <select id="fund-sort"><option value="new">Newest</option><option value="raised">Raised %</option></select>
        </div>
    </div>

    <div class="fund-grid">
        <div class="fund-list-card">
            <table class="fund-list-table" id="fund-list">
                <thead>
                    <tr><th>Title</th><th>Club</th><th>Raised</th><th>Target</th><th>Progress</th><th>Deadline</th><th>Status</th><th>Actions</th></tr>
                </thead>
                <tbody>
                    <!-- Rows will be server-rendered or filled client-side -->
                    <?php foreach (($data['fundraise_reqs'] ?? []) as $req):
                        $percentage = $req->target_amount ? round(($req->raised_amount/$req->target_amount)*100,2) : 0; ?>
                    <tr>
                        <td><?php echo htmlspecialchars($req->title); ?></td>
                        <td><?php echo htmlspecialchars($req->club_name ?? ''); ?></td>
                        <td>Rs.<?php echo number_format($req->raised_amount,2); ?></td>
                        <td>Rs.<?php echo number_format($req->target_amount,2); ?></td>
                        <td><div class="progress"><i style="width:<?php echo $percentage; ?>%"></i></div><small><?php echo $percentage; ?>%</small></td>
                        <td><?php echo htmlspecialchars($req->deadline); ?></td>
                        <td><?php echo htmlspecialchars($req->status); ?></td>
                        <td><button class="admin-btn" onclick="location.href='<?php echo URLROOT; ?>/fundraiser/show/<?php echo $req->req_id; ?>'">View</button></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <aside class="fund-sidebar">
            <h4>Summary</h4>
            <p id="sidebar-summary">Loading...</p>
            <h4>Quick Actions</h4>
            <div style="display:flex;flex-direction:column;gap:.5rem">
                <button class="btn" id="approve-selected">Approve Selected</button>
                <button class="btn" id="reject-selected">Reject Selected</button>
            </div>
            <h4 style="margin-top:1rem">Charts</h4>
            <div class="card" style="margin-top:.5rem"><canvas id="fundsByClub" height="140"></canvas></div>
        </aside>
    </div>

    <!-- Modal scaffold -->
    <div class="modal" id="fund-modal">
        <div class="modal-inner">
            <button onclick="document.getElementById('fund-modal').style.display='none'">Close</button>
            <div id="fund-modal-body">Loading...</div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    (function(){
        const rawData = <?php echo json_encode($data['fundraise_reqs'] ?? []); ?>;

        const formatCurrency = v => 'Rs.' + Number(v||0).toLocaleString();

        // Populate KPIs
        const kpiOpen = document.getElementById('kpi-open');
        const kpiRaised = document.getElementById('kpi-raised');
        const kpiClubs = document.getElementById('kpi-clubs');
        const kpiExpire = document.getElementById('kpi-expire');

        const openCount = rawData.filter(r=> r.status !== 'Rejected' && new Date(r.deadline) > new Date()).length;
        const totalRaised = rawData.reduce((s,r)=> s + Number(r.raised_amount||0), 0);
        const clubs = Array.from(new Set(rawData.map(r=> r.club_name).filter(Boolean))).length;
        const expiringSoon = rawData.filter(r=> { const d=new Date(r.deadline); const diff=(d - new Date())/(1000*60*60*24); return diff>0 && diff<=14; }).length;

        if(kpiOpen) kpiOpen.textContent = openCount;
        if(kpiRaised) kpiRaised.textContent = formatCurrency(totalRaised);
        if(kpiClubs) kpiClubs.textContent = clubs;
        if(kpiExpire) kpiExpire.textContent = expiringSoon;

        // Populate club filter
        const clubSelect = document.getElementById('fund-club');
        const clubNames = Array.from(new Set(rawData.map(r=> r.club_name).filter(Boolean))).sort();
        clubNames.forEach(c=> { const o=document.createElement('option'); o.value=c; o.textContent=c; clubSelect.appendChild(o); });

        // Render table rows (client-side to allow filtering/sorting)
        const tbody = document.querySelector('#fund-list tbody');
        function renderRows(list){
            tbody.innerHTML = '';
            list.forEach(r=>{
                const tr = document.createElement('tr');
                const pct = r.target_amount ? Math.round((r.raised_amount/r.target_amount)*10000)/100 : 0;
                tr.innerHTML = `
                    <td>${escapeHtml(r.title)}</td>
                    <td>${escapeHtml(r.club_name||'')}</td>
                    <td>${formatCurrency(r.raised_amount)}</td>
                    <td>${formatCurrency(r.target_amount)}</td>
                    <td><div class="progress"><i style="width:${pct}%"></i></div><small>${pct}%</small></td>
                    <td>${escapeHtml(r.deadline||'')}</td>
                    <td>${escapeHtml(r.status||'')}</td>
                    <td><button class="admin-btn view-btn" data-id="${r.req_id}">View</button></td>
                `;
                tbody.appendChild(tr);
            });
            attachViewHandlers();
        }

        function escapeHtml(s){ return String(s||'').replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;','\'":"'"'}[c] || c)); }

        function attachViewHandlers(){
            document.querySelectorAll('.view-btn').forEach(b=> b.onclick = (e)=>{
                const id = e.currentTarget.dataset.id;
                const item = rawData.find(x=> String(x.req_id) === String(id));
                const modal = document.getElementById('fund-modal');
                const body = document.getElementById('fund-modal-body');
                body.innerHTML = `<h3>${escapeHtml(item.title)}</h3><p>${escapeHtml(item.description||'')}</p><p>Club: ${escapeHtml(item.club_name||'')}</p><p>Raised: ${formatCurrency(item.raised_amount)} of ${formatCurrency(item.target_amount)}</p>`;
                modal.style.display = 'flex';
            });
        }

        // Initial render
        renderRows(rawData);

        // Filtering and sorting
        const search = document.getElementById('fund-search');
        const status = document.getElementById('fund-status');
        const sort = document.getElementById('fund-sort');
        document.getElementById('fund-filter').onclick = function(){
            let list = rawData.slice();
            const q = (search.value||'').toLowerCase().trim();
            if(q) list = list.filter(r=> (r.title||'').toLowerCase().includes(q) || (r.club_name||'').toLowerCase().includes(q));
            if(status.value) list = list.filter(r=> r.status === status.value);
            if(clubSelect.value) list = list.filter(r=> r.club_name === clubSelect.value);
            if(sort.value === 'raised') list.sort((a,b)=> (b.raised_amount/a.target_amount || 0) - (a.raised_amount/a.target_amount || 0));
            else list.sort((a,b)=> new Date(b.created_at) - new Date(a.created_at));
            renderRows(list);
        };

        // Export CSV of current visible rows
        document.getElementById('export-funds').onclick = function(){
            const rows = [['id','title','club','raised','target','deadline','status']];
            document.querySelectorAll('#fund-list tbody tr').forEach(tr=>{
                const cells = Array.from(tr.children).slice(0,7).map(td=>td.textContent.trim());
                rows.push(cells);
            });
            const csv = rows.map(r=> r.map(c => '"'+String(c).replace(/"/g,'""')+'"').join(',')).join('\n');
            const blob = new Blob([csv], {type:'text/csv'});
            const url = URL.createObjectURL(blob); const a=document.createElement('a'); a.href=url; a.download='funds_export.csv'; a.click(); URL.revokeObjectURL(url);
        };

        // Funds by club chart
        (function(){
            const ctx = document.getElementById('fundsByClub');
            if(!ctx) return;
            const groups = {};
            rawData.forEach(r=> { const k = r.club_name||'Unknown'; groups[k] = (groups[k]||0) + Number(r.raised_amount||0); });
            const labels = Object.keys(groups); const vals = labels.map(l=> groups[l]);
            new Chart(ctx.getContext('2d'), { type:'bar', data:{ labels, datasets:[{ data: vals, backgroundColor: labels.map((_,i)=> ['#60a5fa','#34d399','#fbbf24','#f87171','#a78bfa'][i%5]) }] }, options:{responsive:true, scales:{y:{beginAtZero:true}}} });
        })();
    })();
    </script>
<?php $content = ob_get_clean(); ?>
<?php require APPROOT . '/views/admin/dashboard_layout.php'; ?>