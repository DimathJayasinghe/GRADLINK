<?php ob_start()?>
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/admin/common.css">
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/admin/dashboard-common.css">
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/admin/engagement.css">
<?php $styles = ob_get_clean()?>


<?php
    $sidebar_left = [
        ['label'=>'Overview', 'url'=>'/admin','active'=>false, 'icon'=>'tachometer-alt'],
        ['label'=>'User Management', 'url'=>'/admin/users','active'=>false, 'icon' => 'users'],
        ['label'=>'Engagement Metrics', 'url'=>'/admin/engagement','active'=>true, 'icon' => 'chart-bar'],
        ['label'=>'Reports', 'url'=>'/admin/reports','active'=>false, 'icon' => 'fas fa-exclamation-triangle'],
        ['label'=>'Event Moderation', 'url'=>'/admin/eventrequests','active'=>false, 'icon' => 'clipboard-list'],
        ['label'=>'Content Management', 'url'=>'/admin/posts','active'=>false, 'icon' => 'pencil-alt'],
        ['label'=>'Fundraisers', 'url'=>'/admin/fundraisers','active'=>false, 'icon' => 'donate'],
        ['label'=>'Alumni Verifications', 'url'=>'/admin/verifications','active'=>false, 'icon' => 'check-circle']
    ]
?>


<?php ob_start();?>
<div class="admin-dashboard">
    <div class="admin-header analytics-header" >
        <div class="header-text">
            <h1 style="border-bottom: 2px solid #3a3a3a; padding-bottom: 10px; text-align: left;">Analytics Dashboard</h1>
            <p style="color: var(--muted); margin-top: 1.5rem; margin-bottom: 1rem;">Track user activity, engagement, and platform usage.</p>
        </div>
        <div class="header-actions">
            <button id="export-analytics" class="btn">Export Summary</button>
            <button id="export-users" class="btn btn-secondary">Export Users</button>
            <button id="export-content" class="btn btn-secondary">Export Content</button>
            <button id="export-events" class="btn btn-secondary">Export Events</button>
        </div>
    </div>

    <section class="filters">
        <h3 style="color: var(--muted); margin-bottom: .25rem;">Filter by User Role</h3>
        <div class="filters-form role-filter">
            <?php 
                $roleFilter = $data['roleFilter'] ?? null;
                $usersByRole = $data['usersByRole'] ?? ['all' => 0, 'admin' => 0, 'alumni' => 0, 'undergrad' => 0];
                $roles = [
                    'all' => ['label' => 'All Users', 'color' => '#3a3a3a', 'icon' => '👥'],
                    'admin' => ['label' => 'Admins', 'color' => '#ff6b6b', 'icon' => '🔐'],
                    'alumni' => ['label' => 'Alumni', 'color' => '#4ecdc4', 'icon' => '🎓'],
                    'undergrad' => ['label' => 'Students', 'color' => '#45b7d1', 'icon' => '📚'],
                ];
            ?>
            <?php foreach ($roles as $key => $role): ?>
                <?php 
                    $countKey = ($key === 'all') ? 'all' : $key;
                    $count = $usersByRole[$countKey] ?? 0;
                    $isActive = ($roleFilter === $key || ($roleFilter === null && $key === 'all'));
                    $urlRole = ($key === 'all') ? '' : "?role=$key";
                ?>
                <a href="<?php echo URLROOT; ?>/admin/engagement<?php echo $urlRole; ?>" 
                   class="role-filter-btn <?php echo $isActive ? 'active' : ''; ?>"
                   style="<?php echo $isActive ? "background-color: {$role['color']}; color: white;" : ""; ?>">
                    <span class="filter-icon"><?php echo $role['icon']; ?></span>
                    <span class="filter-label"><?php echo $role['label']; ?></span>
                    <span class="filter-count"><?php echo number_format($count); ?></span>
                </a>
            <?php endforeach; ?>
        </div>
        <?php if ($roleFilter): ?>
            <p style="color: var(--muted); font-size: 0.85rem; margin-top: 0.5rem;">
                📊 Showing metrics for: <strong><?php echo ucfirst($roleFilter); ?></strong> users only
            </p>
        <?php endif; ?>
    </section>

    <?php $e = $data['engagement'] ?? ['posts'=>0,'comments'=>0,'reactions'=>0,'messages'=>0,'events'=>0,'event_attendees'=>0,'event_bookmarks'=>0,'followers'=>0,'notifications'=>0,'notifications_unread'=>0,'pending_alumni'=>0,'active_30_days'=>0,'dau'=>0,'wau'=>0,'mau'=>0,'avg_posts_per_user'=>0,'avg_comments_per_post'=>0,'avg_reactions_per_post'=>0,'avg_messages_per_user'=>0,'engagement_rate'=>0,'active_over_time'=>[],'time_series'=>[],'event_pipeline'=>[],'profile_metrics'=>[]]; ?>

    <section class="kpis">
        <div class="kpi">
            <span class="kpi-label">Total Users</span>
            <span id="analytics-users" class="kpi-value"><?php echo number_format($data['metrics']['total_users'] ?? 0); ?></span>
        </div>
        <div class="kpi">
            <span class="kpi-label">Active Users (Last 30 Days)</span>
            <span id="analytics-active" class="kpi-value"><?php echo number_format($e['active_30_days'] ?? ($data['metrics']['active_30_days'] ?? 0)); ?></span>
        </div>
        <div class="kpi">
            <span class="kpi-label">User Growth (Last 3 Months)</span>
            <span id="analytics-growth" class="kpi-value">+<?php echo (int)($data['metrics']['growth_3_months_pct'] ?? 0); ?>%</span>
        </div>
        <div class="kpi">
            <span class="kpi-label">Engagement Rate</span>
            <span id="metric-engagement-rate" class="kpi-value"><?php echo (float)($e['engagement_rate'] ?? 0); ?>%</span>
        </div>
        <div class="kpi">
            <span class="kpi-label">DAU / WAU / MAU</span>
            <span id="metric-dau-wau" class="kpi-value"><?php echo (int)($e['dau'] ?? 0); ?> / <?php echo (int)($e['wau'] ?? 0); ?> / <?php echo (int)($e['mau'] ?? 0); ?></span>
        </div>
    </section>

    <section class="charts">
        <div class="card">
            <h3>User Distribution by Graduation/Batch</h3>
            <div class="chart-wrap-batch" style="height: 300px; width: 300px; display: flex; align-items: center; justify-content: center;"><canvas id="batchChart"></canvas></div>
        </div>
        <div class="card">
            <h3>Distribution by Role</h3>
            <div class="chart-wrap" style="height: 300px; width: 300px; display: flex; align-items: center; justify-content: center;"><canvas id="roleChart"></canvas></div>
        </div>
        <div class="card">
            <h3>Distribution by Gender</h3>
            <div class="chart-wrap" style="height: 300px; width: 300px; display: flex; align-items: center; justify-content: center;"><canvas id="genderChart"></canvas></div>
        </div>
    </section>

    <section class="kpis">
        <div class="kpi"><span class="kpi-label">Total Posts</span><span id="metric-posts" class="kpi-value"><?php echo (int)$e['posts']; ?></span></div>
        <div class="kpi"><span class="kpi-label">Total Comments</span><span id="metric-comments" class="kpi-value"><?php echo (int)$e['comments']; ?></span></div>
        <div class="kpi"><span class="kpi-label">Total Reactions</span><span id="metric-reactions" class="kpi-value"><?php echo (int)$e['reactions']; ?></span></div>
        <div class="kpi"><span class="kpi-label">Messages</span><span id="metric-messages" class="kpi-value"><?php echo (int)$e['messages']; ?></span></div>
        <div class="kpi"><span class="kpi-label">Avg Posts/User</span><span id="metric-avg-posts" class="kpi-value"><?php echo (float)($e['avg_posts_per_user'] ?? 0); ?></span></div>
        <div class="kpi"><span class="kpi-label">Avg Comments/Post</span><span id="metric-avg-comments" class="kpi-value"><?php echo (float)($e['avg_comments_per_post'] ?? 0); ?></span></div>
    </section>

    <section class="kpis">
        <div class="kpi"><span class="kpi-label">Events</span><span id="metric-events" class="kpi-value"><?php echo (int)$e['events']; ?></span></div>
        <div class="kpi"><span class="kpi-label">Event Attendees</span><span id="metric-event-attendees" class="kpi-value"><?php echo (int)$e['event_attendees']; ?></span></div>
        <div class="kpi"><span class="kpi-label">Event Bookmarks</span><span id="metric-event-bookmarks" class="kpi-value"><?php echo (int)$e['event_bookmarks']; ?></span></div>
        <div class="kpi"><span class="kpi-label">Followers</span><span id="metric-followers" class="kpi-value"><?php echo (int)$e['followers']; ?></span></div>
        <div class="kpi"><span class="kpi-label">Pending Alumni</span><span id="metric-pending-alumni" class="kpi-value"><?php echo (int)$e['pending_alumni']; ?></span></div>
        <div class="kpi"><span class="kpi-label">Unread Notifications</span><span id="metric-unread-notifications" class="kpi-value"><?php echo (int)$e['notifications_unread']; ?></span></div>
    </section>

    <?php $profile = $e['profile_metrics'] ?? ['completion_rate'=>0,'private_profiles'=>0,'completed'=>0,'total'=>0]; ?>
    <section class="kpis">
        <div class="kpi"><span class="kpi-label">Profile Completion</span><span id="metric-profile-completion" class="kpi-value"><?php echo (float)($profile['completion_rate'] ?? 0); ?>%</span></div>
        <div class="kpi"><span class="kpi-label">Private Profiles</span><span id="metric-private-profiles" class="kpi-value"><?php echo (int)($profile['private_profiles'] ?? 0); ?></span></div>
        <div class="kpi"><span class="kpi-label">Completed Profiles</span><span id="metric-completed-profiles" class="kpi-value"><?php echo (int)($profile['completed'] ?? 0); ?></span></div>
        <div class="kpi"><span class="kpi-label">Profiles Total</span><span id="metric-total-profiles" class="kpi-value"><?php echo (int)($profile['total'] ?? 0); ?></span></div>
    </section>

    <section class="grid-2">
        <div class="card map-placeholder">
            <h3>Alumni Locations</h3>
            <div id="workMap" style="width: 100%; height: 300px; background: #f0f0f0; border-radius: 8px;"></div>
        </div>
        <div class="card">
            <h3>Active Users Over Time</h3>
            <div class="chart-wrap" style="height: auto; width: 100%; display: flex; align-items: center; justify-content: center;">
                <canvas id="activeOverTime" height="100"></canvas>
            </div>
        </div>
    </section>

    <section class="grid-3">
        <div class="card">
            <h3>Signups Over Time</h3>
            <div class="chart-wrap-wide"><canvas id="signupsChart" height="120"></canvas></div>
        </div>
        <div class="card">
            <h3>Content Volume Over Time</h3>
            <div class="chart-wrap-wide"><canvas id="contentChart" height="120"></canvas></div>
        </div>
        <div class="card">
            <h3>Messaging Over Time</h3>
            <div class="chart-wrap-wide"><canvas id="messagesChart" height="120"></canvas></div>
        </div>
    </section>

    <section class="grid-3">
        <div class="card">
            <h3>Engagement Mix</h3>
            <div class="chart-wrap" style="height: 260px; width: 260px; display: flex; align-items: center; justify-content: center;"><canvas id="engagementMix"></canvas></div>
        </div>
        <div class="card">
            <h3>Event Pipeline</h3>
            <div class="chart-wrap-wide"><canvas id="eventPipeline" height="120"></canvas></div>
        </div>
        <div class="card">
            <h3>Top Skills</h3>
            <div class="chart-wrap-wide"><canvas id="skillsChart" height="120"></canvas></div>
        </div>
    </section>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script>
    // Initialize work location map
    document.addEventListener('DOMContentLoaded', () => {
        const map = L.map('workMap').setView([7.8731, 80.7718], 7); // Centered on Sri Lanka
        
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors',
            maxZoom: 18
        }).addTo(map);

        // Sample alumni work locations (replace with actual data from server)
        const workLocations = <?php echo json_encode($data['work_locations'] ?? [
            ['name' => 'Colombo', 'lat' => 6.9271, 'lng' => 79.8612, 'count' => 45],
            ['name' => 'Kandy', 'lat' => 7.2906, 'lng' => 80.6337, 'count' => 23],
            ['name' => 'Galle', 'lat' => 6.0535, 'lng' => 80.2210, 'count' => 12]
        ]); ?>;

        workLocations.forEach(location => {
            const marker = L.circleMarker([location.lat, location.lng], {
                radius: Math.sqrt(location.count) * 3,
                fillColor: '#60a5fa',
                color: '#2563eb',
                weight: 2,
                opacity: 1,
                fillOpacity: 0.6
            }).addTo(map);
            
            marker.bindPopup(`<b>${location.name}</b><br>${location.count} alumni working here`);
        });
    });

    const roleData = <?php echo json_encode($data['charts']['roles'] ?? []); ?>;
    const batchData = <?php echo json_encode($data['charts']['batches'] ?? []); ?>;
    const genderData = <?php echo json_encode($data['charts']['genders'] ?? []); ?>;
    const skillData = <?php echo json_encode($data['charts']['skills'] ?? []); ?>;
    const eventStatusData = <?php echo json_encode($data['charts']['event_status'] ?? []); ?>;
    const eventRequestStatusData = <?php echo json_encode($data['charts']['event_request_status'] ?? []); ?>;
    const engagement = <?php echo json_encode($data['engagement'] ?? []); ?>;

    // Populate simple metrics
    document.addEventListener('DOMContentLoaded', () => {
        // Populate numeric metrics (already seeded server-side but keep JS-safe updates)
        const postsEl = document.getElementById('metric-posts');
        const commentsEl = document.getElementById('metric-comments');
        const reactionsEl = document.getElementById('metric-reactions');
        const usersEl = document.getElementById('analytics-users');
        const activeEl = document.getElementById('analytics-active');
        const growthEl = document.getElementById('analytics-growth');

        if(postsEl) postsEl.textContent = (engagement.posts ?? 0).toLocaleString();
        if(commentsEl) commentsEl.textContent = (engagement.comments ?? 0).toLocaleString();
        if(reactionsEl) reactionsEl.textContent = (engagement.reactions ?? 0).toLocaleString();
        if(usersEl) usersEl.textContent = (<?php echo json_encode($data['metrics']['total_users'] ?? 0); ?>).toLocaleString();
        if(activeEl) activeEl.textContent = (<?php echo json_encode($data['metrics']['active_30_days'] ?? 0); ?>).toLocaleString();
        if(growthEl) growthEl.textContent = ('+' + (<?php echo json_encode((int)($data['metrics']['growth_3_months_pct'] ?? 0)); ?>) + '%');

        const messagesEl = document.getElementById('metric-messages');
        const eventsEl = document.getElementById('metric-events');
        const attendeesEl = document.getElementById('metric-event-attendees');
        const bookmarksEl = document.getElementById('metric-event-bookmarks');
        const followersEl = document.getElementById('metric-followers');
        const pendingAlumniEl = document.getElementById('metric-pending-alumni');
        const unreadNotiEl = document.getElementById('metric-unread-notifications');
        const engagementRateEl = document.getElementById('metric-engagement-rate');
        const dauWauEl = document.getElementById('metric-dau-wau');
        const avgPostsEl = document.getElementById('metric-avg-posts');
        const avgCommentsEl = document.getElementById('metric-avg-comments');
        const completionEl = document.getElementById('metric-profile-completion');
        const privateProfilesEl = document.getElementById('metric-private-profiles');
        const completedProfilesEl = document.getElementById('metric-completed-profiles');
        const totalProfilesEl = document.getElementById('metric-total-profiles');

        if(messagesEl) messagesEl.textContent = (engagement.messages ?? 0).toLocaleString();
        if(eventsEl) eventsEl.textContent = (engagement.events ?? 0).toLocaleString();
        if(attendeesEl) attendeesEl.textContent = (engagement.event_attendees ?? 0).toLocaleString();
        if(bookmarksEl) bookmarksEl.textContent = (engagement.event_bookmarks ?? 0).toLocaleString();
        if(followersEl) followersEl.textContent = (engagement.followers ?? 0).toLocaleString();
        if(pendingAlumniEl) pendingAlumniEl.textContent = (engagement.pending_alumni ?? 0).toLocaleString();
        if(unreadNotiEl) unreadNotiEl.textContent = (engagement.notifications_unread ?? 0).toLocaleString();
        if(engagementRateEl) engagementRateEl.textContent = ((engagement.engagement_rate ?? 0) + '%');
        if(dauWauEl) dauWauEl.textContent = `${(engagement.dau ?? 0)} / ${(engagement.wau ?? 0)} / ${(engagement.mau ?? 0)}`;
        if(avgPostsEl) avgPostsEl.textContent = (engagement.avg_posts_per_user ?? 0);
        if(avgCommentsEl) avgCommentsEl.textContent = (engagement.avg_comments_per_post ?? 0);
        if(completionEl) completionEl.textContent = ((engagement.profile_metrics?.completion_rate ?? 0) + '%');
        if(privateProfilesEl) privateProfilesEl.textContent = (engagement.profile_metrics?.private_profiles ?? 0);
        if(completedProfilesEl) completedProfilesEl.textContent = (engagement.profile_metrics?.completed ?? 0);
        if(totalProfilesEl) totalProfilesEl.textContent = (engagement.profile_metrics?.total ?? 0);

        // Export helpers (simple CSV export of current charts/metrics)
        function downloadCSV(filename, rows){
            const csv = rows.map(r => r.map(c => '"' + String(c).replace(/"/g,'""') + '"').join(',')).join('\n');
            const blob = new Blob([csv], { type: 'text/csv' });
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url; a.download = filename; document.body.appendChild(a); a.click(); a.remove();
            URL.revokeObjectURL(url);
        }

        document.getElementById('export-analytics').addEventListener('click', function(){
            const rows = [
                ['Metric','Value'],
                ['Total Users', usersEl ? usersEl.textContent : '0'],
                ['Active 30d', activeEl ? activeEl.textContent : '0'],
                ['Growth 3mo', growthEl ? growthEl.textContent : '0'],
                ['Posts', postsEl ? postsEl.textContent : '0'],
                ['Comments', commentsEl ? commentsEl.textContent : '0'],
                ['Reactions', reactionsEl ? reactionsEl.textContent : '0']
            ];
            downloadCSV('analytics_summary.csv', rows);
        });

        document.getElementById('export-users').addEventListener('click', function(){
            // Placeholder: in real app, this should call server for a full export. Here export empty header.
            downloadCSV('users_export.csv', [['id','name','email','role','batch']]);
        });

        document.getElementById('export-content').addEventListener('click', function(){
            downloadCSV('content_export.csv', [['id','title','type','status','date']]);
        });

        const exportEventsBtn = document.getElementById('export-events');
        if(exportEventsBtn){
            exportEventsBtn.addEventListener('click', function(){
                downloadCSV('events_export.csv', [['id','title','status','start_datetime','venue']]);
            });
        }
    });

    function toLabelsCounts(arr, labelKey, valueKey){
        const labels = [];
        const values = [];
        (arr || []).forEach(r => { labels.push(r[labelKey]); values.push(parseInt(r[valueKey] || 0)); });
        return {labels, values};
    }

    // Build charts from server-provided data (guard DOM presence)
    (function(){
        const roleArr = <?php echo json_encode($data['charts']['roles'] ?? []); ?>;
        const batchArr = <?php echo json_encode($data['charts']['batches'] ?? []); ?>;
        const genderArr = <?php echo json_encode($data['charts']['genders'] ?? []); ?>;
        const overArr = <?php echo json_encode($data['engagement']['active_over_time'] ?? []); ?>;
        const timeSeries = <?php echo json_encode($data['engagement']['time_series'] ?? []); ?>;
        const eventPipeline = <?php echo json_encode($data['engagement']['event_pipeline'] ?? []); ?>;

        function buildLabelsValues(arr, labelKey, valueKey){
            const labels = [], values = [];
            (arr||[]).forEach(r=>{ labels.push(r[labelKey]); values.push(parseInt(r[valueKey] || 0)); });
            return {labels, values};
        }

        const roleDataObj = buildLabelsValues(roleArr, 'role', 'count');
        const roleCtx = document.getElementById('roleChart');
        if(roleCtx && roleDataObj.labels.length){
            new Chart(roleCtx.getContext('2d'), { type:'doughnut', data: { labels: roleDataObj.labels, datasets:[{ data: roleDataObj.values, backgroundColor: ['#60a5fa','#34d399','#fbbf24','#f87171','#a78bfa'] }] }, options:{responsive:true, plugins:{legend:{position:'bottom'}}} });
        }

        const batchDataObj = buildLabelsValues(batchArr, 'batch', 'count');
        const batchCtx = document.getElementById('batchChart');
        if(batchCtx && batchDataObj.labels.length){
            new Chart(batchCtx.getContext('2d'), { type:'bar', data:{ labels: batchDataObj.labels, datasets:[{ data: batchDataObj.values, backgroundColor:'#93c5fd' }] }, options:{responsive:true, scales:{y:{beginAtZero:true}}} });
        }

        const genderDataObj = buildLabelsValues(genderArr, 'gender', 'count');
        const genderCtx = document.getElementById('genderChart');
        if(genderCtx && genderDataObj.labels.length){
            new Chart(genderCtx.getContext('2d'), { type:'doughnut', data:{ labels: genderDataObj.labels.map(l => l || 'Unspecified'), datasets:[{ data: genderDataObj.values, backgroundColor:['#f472b6','#60a5fa','#a3a3a3'] }] }, options:{responsive:true, plugins:{legend:{position:'bottom'}}} });
        }

        const overCtx = document.getElementById('activeOverTime');
        if(overCtx && (overArr||[]).length){
            const labels = overArr.map(r=>r.ym);
            const values = overArr.map(r=>parseInt(r.c||0));
            new Chart(overCtx.getContext('2d'), { type:'line', data:{ labels, datasets:[{ data: values, borderColor:'#4ade80', fill:false, tension:0.3 }] }, options:{responsive:true, scales:{y:{beginAtZero:true}}} });
        }

        const signupsArr = timeSeries.signups || [];
        const signupsCtx = document.getElementById('signupsChart');
        if(signupsCtx && signupsArr.length){
            new Chart(signupsCtx.getContext('2d'), { type:'line', data:{ labels: signupsArr.map(r=>r.ym), datasets:[{ data: signupsArr.map(r=>parseInt(r.c||0)), borderColor:'#60a5fa', fill:false, tension:0.3 }] }, options:{responsive:true, scales:{y:{beginAtZero:true}}} });
        }

        const postsArr = timeSeries.posts || [];
        const commentsArr = timeSeries.comments || [];
        const reactionsArr = timeSeries.reactions || [];
        const contentCtx = document.getElementById('contentChart');
        if(contentCtx && (postsArr.length || commentsArr.length || reactionsArr.length)){
            const labels = Array.from(new Set([...postsArr, ...commentsArr, ...reactionsArr].map(r=>r.ym))).sort();
            const toSeries = (arr) => labels.map(l => {
                const row = arr.find(r=>r.ym===l); return parseInt(row?.c||0);
            });
            new Chart(contentCtx.getContext('2d'), { type:'line', data:{ labels, datasets:[
                { label:'Posts', data: toSeries(postsArr), borderColor:'#93c5fd', fill:false, tension:0.3 },
                { label:'Comments', data: toSeries(commentsArr), borderColor:'#fbbf24', fill:false, tension:0.3 },
                { label:'Reactions', data: toSeries(reactionsArr), borderColor:'#f87171', fill:false, tension:0.3 }
            ] }, options:{responsive:true, plugins:{legend:{position:'bottom'}}, scales:{y:{beginAtZero:true}}} });
        }

        const messagesArr = timeSeries.messages || [];
        const messagesCtx = document.getElementById('messagesChart');
        if(messagesCtx && messagesArr.length){
            new Chart(messagesCtx.getContext('2d'), { type:'bar', data:{ labels: messagesArr.map(r=>r.ym), datasets:[{ data: messagesArr.map(r=>parseInt(r.c||0)), backgroundColor:'#a78bfa' }] }, options:{responsive:true, scales:{y:{beginAtZero:true}}} });
        }

        const mixCtx = document.getElementById('engagementMix');
        if(mixCtx){
            const mixLabels = ['Posts','Comments','Reactions','Messages'];
            const mixValues = [engagement.posts||0, engagement.comments||0, engagement.reactions||0, engagement.messages||0];
            new Chart(mixCtx.getContext('2d'), { type:'doughnut', data:{ labels: mixLabels, datasets:[{ data: mixValues, backgroundColor:['#60a5fa','#fbbf24','#f87171','#a78bfa'] }] }, options:{responsive:true, plugins:{legend:{position:'bottom'}}} });
        }

        const pipelineCtx = document.getElementById('eventPipeline');
        if(pipelineCtx){
            const reqArr = eventPipeline.requests || [];
            const evArr = eventPipeline.events || [];
            const labels = ['Requests: Pending','Requests: Approved','Requests: Rejected','Events: Draft','Events: Published','Events: Cancelled'];
            const getCount = (arr, key) => {
                const row = (arr||[]).find(r => String(r.status).toLowerCase() === key);
                return parseInt(row?.c||0);
            };
            const values = [
                getCount(reqArr, 'pending'),
                getCount(reqArr, 'approved'),
                getCount(reqArr, 'rejected'),
                getCount(evArr, 'draft'),
                getCount(evArr, 'published'),
                getCount(evArr, 'cancelled')
            ];
            new Chart(pipelineCtx.getContext('2d'), { type:'bar', data:{ labels, datasets:[{ data: values, backgroundColor:'#34d399' }] }, options:{responsive:true, plugins:{legend:{display:false}}, scales:{y:{beginAtZero:true}}} });
        }

        const skillsCtx = document.getElementById('skillsChart');
        if(skillsCtx){
            const entries = Object.entries(skillData || {}).map(([k,v]) => ({ k, v: parseInt(v||0) }))
                .sort((a,b)=>b.v-a.v).slice(0, 8);
            if(entries.length){
                new Chart(skillsCtx.getContext('2d'), { type:'bar', data:{ labels: entries.map(e=>e.k), datasets:[{ data: entries.map(e=>e.v), backgroundColor:'#f59e0b' }] }, options:{responsive:true, scales:{y:{beginAtZero:true}}} });
            }
        }
    })();
</script>

<?php $content = ob_get_clean(); ?>
<?php require APPROOT . '/views/admin/dashboard_layout.php'; ?>


