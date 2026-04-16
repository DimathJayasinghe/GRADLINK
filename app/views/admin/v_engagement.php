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
        ['label'=>'Alumni Verifications', 'url'=>'/admin/verifications','active'=>false, 'icon' => 'check-circle'],
        ['label'=>'Suspended Users', 'url'=>'/admin/suspendedUsers','active'=>false, 'icon' => 'user-slash'],
        ['label'=>'Help & Support', 'url'=>'/admin/support','active'=>false, 'icon' => 'circle-question']
    ]
?>


<?php ob_start();?>
<div class="admin-dashboard">
    <div class="admin-header analytics-header" >
        <div class="header-text">
            <h1 class="analytics-title">Analytics Dashboard</h1>
            <p class="analytics-subtitle">Track user activity, engagement, and platform usage.</p>
        </div>
        <div class="header-actions">
            <button id="export-analytics" class="btn">Export Summary</button>
            <!-- <button id="export-users" class="btn btn-secondary">Export Users</button>
            <button id="export-content" class="btn btn-secondary">Export Content</button>
            <button id="export-events" class="btn btn-secondary">Export Events</button> -->
        </div>
    </div>

    <section class="filters">
        <h3>Filter by User Role</h3>
        <div class="filters-form role-filter">
            <?php 
                $roleFilter = $data['roleFilter'] ?? null;
                $usersByRole = $data['usersByRole'] ?? ['all' => 0, 'admin' => 0, 'alumni' => 0, 'undergrad' => 0];
                $contextTotalUsers = $roleFilter ? ($usersByRole[$roleFilter] ?? 0) : ($data['metrics']['total_users'] ?? 0);
                $roles = [
                    'all' => ['label' => 'All Users', 'color' => '#3a3a3a', 'icon' => ''],
                    'admin' => ['label' => 'Admins', 'color' => '#ff6b6b', 'icon' => ''],
                    'alumni' => ['label' => 'Alumni', 'color' => '#4ecdc4', 'icon' => ''],
                    'undergrad' => ['label' => 'Students', 'color' => '#45b7d1', 'icon' => ''],
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
                   data-role-color="<?php echo htmlspecialchars($role['color']); ?>"
                   <?php if ($isActive): ?>style="background-color: <?php echo htmlspecialchars($role['color']); ?>; color: #ffffff;"<?php endif; ?>>
                    <span class="filter-icon"><?php echo $role['icon']; ?></span>
                    <span class="filter-label"><?php echo $role['label']; ?></span>
                    <span class="filter-count"><?php echo number_format($count); ?></span>
                </a>
            <?php endforeach; ?>
        </div>
        <?php if ($roleFilter): ?>
            <p class="role-filter-summary">
                Showing metrics for: <strong><?php echo ucfirst($roleFilter); ?></strong> users only
            </p>
        <?php endif; ?>
    </section>

    <?php $e = $data['engagement'] ?? ['posts'=>0,'comments'=>0,'reactions'=>0,'messages'=>0,'events'=>0,'event_bookmarks'=>0,'followers'=>0,'notifications'=>0,'notifications_unread'=>0,'pending_alumni'=>0,'active_30_days'=>0,'dau'=>0,'wau'=>0,'mau'=>0,'avg_posts_per_user'=>0,'avg_comments_per_post'=>0,'avg_reactions_per_post'=>0,'avg_messages_per_user'=>0,'engagement_rate'=>0,'active_over_time'=>[],'time_series'=>[],'event_pipeline'=>[],'profile_metrics'=>[]]; ?>

    <section class="kpis">
        <div class="kpi">
            <span class="kpi-label"><?php echo $roleFilter ? 'Filtered Users' : 'Total Users'; ?></span>
            <span id="analytics-users" class="kpi-value"><?php echo number_format((int)$contextTotalUsers); ?></span>
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
    </section>

    <section class="charts">
        <div class="card">
            <h3>User Distribution by Graduation/Batch</h3>
            <div class="chart-wrap chart-wrap-batch"><canvas id="batchChart"></canvas></div>
        </div>
        <div class="card">
            <h3>Distribution by Role</h3>
            <div class="chart-wrap"><canvas id="roleChart"></canvas></div>
        </div>
        <div class="card">
            <h3>Distribution by Gender</h3>
            <div class="chart-wrap"><canvas id="genderChart"></canvas></div>
        </div>
    </section>

    <section class="kpis">
        <div class="kpi"><span class="kpi-label">Total Posts</span><span id="metric-posts" class="kpi-value"><?php echo (int)$e['posts']; ?></span></div>

        <div class="kpi"><span class="kpi-label">Events</span><span id="metric-events" class="kpi-value"><?php echo (int)$e['events']; ?></span></div>
    
        <div class="kpi"><span class="kpi-label">Pending Alumni</span><span id="metric-pending-alumni" class="kpi-value"><?php echo (int)$e['pending_alumni']; ?></span></div>
        <div class="kpi"><span class="kpi-label">Daily AU</span><span id="metric-dau" class="kpi-value"><?php echo (int)($e['dau'] ?? 0); ?></span></div>
        <div class="kpi"><span class="kpi-label">Weekly AU</span><span id="metric-wau" class="kpi-value"><?php echo (int)($e['wau'] ?? 0); ?></span></div>
        <div class="kpi"><span class="kpi-label">Monthly AU</span><span id="metric-mau" class="kpi-value"><?php echo    (int)($e['mau'] ?? 0); ?></span></div>
    </section>

    <?php $profile = $e['profile_metrics'] ?? ['completion_rate'=>0,'private_profiles'=>0,'completed'=>0,'total'=>0]; ?>
    <section class="kpis">
        <div class="kpi"><span class="kpi-label">Avg Posts/User</span><span id="metric-avg-posts" class="kpi-value"><?php echo (float)($e['avg_posts_per_user'] ?? 0); ?></span></div>
        <div class="kpi"><span class="kpi-label">Avg Comments/Post</span><span id="metric-avg-comments" class="kpi-value"><?php echo (float)($e['avg_comments_per_post'] ?? 0); ?></span></div>
        <div class="kpi"><span class="kpi-label">Profile Completion</span><span id="metric-profile-completion" class="kpi-value"><?php echo (float)($profile['completion_rate'] ?? 0); ?>%</span></div>
        <div class="kpi"><span class="kpi-label">Private Profiles</span><span id="metric-private-profiles" class="kpi-value"><?php echo (int)($profile['private_profiles'] ?? 0); ?></span></div>
        <div class="kpi"><span class="kpi-label">Completed Profiles</span><span id="metric-completed-profiles" class="kpi-value"><?php echo (int)($profile['completed'] ?? 0); ?></span></div>
        <div class="kpi"><span class="kpi-label">Profiles Total</span><span id="metric-total-profiles" class="kpi-value"><?php echo (int)($profile['total'] ?? 0); ?></span></div>
    </section>

    <section class="grid-2">
        <div class="card">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                <h3 style="margin: 0;">User Locations</h3>
                <button id="expandMapBtn" class="btn btn-secondary" style="background-color: #3d3d3d; padding: 0.4rem 0.8rem; font-size: 0.85rem;">
                    Expand Map
                </button>
            </div>
            <div id="smallMap" style="width: 100%; height: 300px; border-radius: 8px;"></div>
            <div style="margin-top: 0.5rem; font-size: 0.85rem; color: var(--muted);">
                <?php 
                    $summary = $data['locationSummary'] ?? ['total_users_with_location' => 0, 'total_countries' => 0];
                ?>
                <strong><?php echo number_format($summary['total_users_with_location'] ?? 0); ?></strong> users across 
                <strong><?php echo ($summary['total_countries'] ?? 0); ?></strong> countries
            </div>
        </div>
        <div class="card">
            <h3>Active Users Over Time</h3>
            <div class="chart-wrap chart-wrap-wide chart-wrap-timewide">
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
            <div class="chart-wrap"><canvas id="engagementMix"></canvas></div>
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

<!-- Expanded Map Modal -->
<div id="mapModal" class="map-modal" style="display: none;">
    <div class="map-modal-content">
        <div class="map-modal-header">
            <div>
                <h2 style="margin: 0; font-size: 1.5rem;">Geographic Distribution</h2>
                <p style="color: var(--muted); font-size: 0.9rem; margin: 0.25rem 0 0 0;">Interactive world map with country markers</p>
            </div>
            <button id="closeMapModal" class="close-modal-btn" title="Close">&times;</button>
        </div>
        
        <div class="map-modal-filters">
            <div class="filter-group">
                <label>Country:</label>
                <select id="filterCountry" class="filter-select">
                    <option value="">All Countries</option>
                    <?php foreach ($data['countries'] ?? [] as $country): ?>
                        <option value="<?php echo htmlspecialchars($country->country); ?>">
                            <?php echo htmlspecialchars($country->country); ?> (<?php echo $country->user_count; ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="filter-group">
                <label>Batch:</label>
                <select id="filterBatch" class="filter-select">
                    <option value="">All Batches</option>
                    <?php foreach ($data['batches'] ?? [] as $batch): ?>
                        <option value="<?php echo htmlspecialchars($batch->batch_no); ?>">
                            Batch <?php echo htmlspecialchars($batch->batch_no); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="filter-group">
                <label>Role:</label>
                <select id="filterRole" class="filter-select">
                    <option value="">All Roles</option>
                    <option value="admin">Admins</option>
                    <option value="alumni">Alumni</option>
                    <option value="undergrad">Students</option>
                </select>
            </div>
            <button id="applyMapFilters" class="btn" style="padding: 0.5rem 1rem;">Apply Filters</button>
            <button id="resetMapFilters" class="btn btn-secondary" style="padding: 0.5rem 1rem;">Reset</button>
        </div>
        
        <div style="position: relative;">
            <div id="expandedMap" style="width: 100%; height: calc(100vh - 280px); min-height: 500px;"></div>
            <button id="toggleView" class="btn" style="position: absolute; top: 10px; right: 10px; z-index: 1000; padding: 0.5rem 1rem;">Show Chart</button>
        </div>
        
        <div id="chartView" style="display: none; padding: 2rem; height: calc(100vh - 280px); overflow-y: auto;">
            <canvas id="expandedCountriesChart" height="400"></canvas>
            <div id="locationTable" class="location-table" style="margin-top: 2rem;"></div>
        </div>
        
        <div class="map-modal-stats">
            <div class="stat-item">
                <span class="stat-label">Total Users:</span>
                <span class="stat-value" id="modalStatUsers">-</span>
            </div>
            <div class="stat-item">
                <span class="stat-label">Countries:</span>
                <span class="stat-value" id="modalStatCountries">-</span>
            </div>
            <div class="stat-item">
                <span class="stat-label">Top Country:</span>
                <span class="stat-value" id="modalStatTopCountry">-</span>
            </div>
            <div class="stat-item">
                <span class="stat-label">Current View:</span>
                <span class="stat-value" id="modalStatMode">Map</span>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://unpkg.com/leaflet.markercluster@1.5.3/dist/leaflet.markercluster.js"></script>
<link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.css" />
<link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.Default.css" />
<script>
    // Country coordinates mapping (approximate center of each country)
    const countryCoordinates = {
        'Sri Lanka': [7.8731, 80.7718],
        'United States': [37.0902, -95.7129],
        'United Kingdom': [55.3781, -3.4360],
        'Canada': [56.1304, -106.3468],
        'Australia': [-25.2744, 133.7751],
        'India': [20.5937, 78.9629],
        'United Arab Emirates': [23.4241, 53.8478],
        'Singapore': [1.3521, 103.8198],
        'Malaysia': [4.2105, 101.9758],
        'China': [35.8617, 104.1954],
        'Japan': [36.2048, 138.2529],
        'Germany': [51.1657, 10.4515],
        'France': [46.2276, 2.2137],
        'Italy': [41.8719, 12.5674],
        'Spain': [40.4637, -3.7492],
        'Netherlands': [52.1326, 5.2913],
        'Sweden': [60.1282, 18.6435],
        'Norway': [60.4720, 8.4689],
        'Switzerland': [46.8182, 8.2275],
        'New Zealand': [-40.9006, 174.8860],
        'South Korea': [35.9078, 127.7669],
        'Thailand': [15.8700, 100.9925],
        'Indonesia': [-0.7893, 113.9213],
        'Philippines': [12.8797, 121.7740],
        'Pakistan': [30.3753, 69.3451],
        'Bangladesh': [23.6850, 90.3563],
        'Saudi Arabia': [23.8859, 45.0792],
        'Qatar': [25.3548, 51.1839],
        'Kuwait': [29.3117, 47.4818],
        'Oman': [21.4735, 55.9754],
        'Bahrain': [26.0667, 50.5577],
        'South Africa': [-30.5595, 22.9375],
        'Egypt': [26.8206, 30.8025],
        'Kenya': [-0.0236, 37.9062],
        'Nigeria': [9.0820, 8.6753],
        'Brazil': [-14.2350, -51.9253],
        'Argentina': [-38.4161, -63.6167],
        'Mexico': [23.6345, -102.5528],
        'Russia': [61.5240, 105.3188]
    };

    // Global location data
    const allLocations = <?php echo json_encode($data['locations'] ?? []); ?>;
    let smallMapInstance, expandedMapInstance, markerClusterGroup;
    let expandedChart = null;
    let currentView = 'map'; // 'map' or 'chart'
    
    // Initialize small map
    document.addEventListener('DOMContentLoaded', () => {
        if (document.getElementById('smallMap')) {
            smallMapInstance = L.map('smallMap').setView([20, 0], 2);
            
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap contributors',
                maxZoom: 18
            }).addTo(smallMapInstance);
            
            // Add markers for all locations
            if (allLocations && allLocations.length > 0) {
                allLocations.forEach(location => {
                    const coords = countryCoordinates[location.country];
                    if (coords) {
                        const marker = L.marker(coords, {
                            icon: buildLocationIcon(location, false)
                        }).addTo(smallMapInstance);
                        
                        marker.bindPopup(`
                            <div style="min-width: 180px;">
                                <h4 style="margin: 0 0 0.5rem 0; color: #2563eb;">${location.country}</h4>
                                <p style="margin: 0.25rem 0;"><strong>Users:</strong> ${location.user_count}</p>
                                ${location.roles ? `<p style="margin: 0.25rem 0;"><strong>Roles:</strong> ${location.roles}</p>` : ''}
                                ${location.batches ? `<p style="margin: 0.25rem 0;"><strong>Batches:</strong> ${location.batches}</p>` : ''}
                            </div>
                        `);
                    }
                });
            }
        }
    });
    
    // Get color based on roles
    function getRoleColor(roles) {
        if (!roles) return '#60a5fa';
        if (roles.includes('admin')) return '#ff6b6b';
        if (roles.includes('alumni')) return '#4ecdc4';
        if (roles.includes('undergrad')) return '#45b7d1';
        return '#60a5fa';
    }

    function buildLocationIcon(location, isExpanded = false) {
        const count = Math.max(0, parseInt(location.user_count || 0));
        const color = getRoleColor(location.roles);
        const size = isExpanded ? 44 : 36;
        const displayCount = count > 999 ? '999+' : String(count);

        return L.divIcon({
            className: 'location-pin-wrapper',
            html: `
                <div class="location-pin" style="--pin-color: ${color}; --pin-size: ${size}px;">
                    <span class="location-pin-value">${displayCount}</span>
                </div>
            `,
            iconSize: [size, size],
            iconAnchor: [size / 2, size / 2],
            popupAnchor: [0, -size / 2]
        });
    }

    // Expanded Map Modal
    document.getElementById('expandMapBtn').addEventListener('click', function() {
        const modal = document.getElementById('mapModal');
        modal.style.display = 'flex';
        
        // Initialize expanded map if not already done
        if (!expandedMapInstance) {
            setTimeout(() => {
                expandedMapInstance = L.map('expandedMap').setView([20, 0], 2);
                
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '© OpenStreetMap contributors',
                    maxZoom: 18
                }).addTo(expandedMapInstance);
                
                // Initialize marker cluster
                markerClusterGroup = L.markerClusterGroup({
                    maxClusterRadius: 60,
                    spiderfyOnMaxZoom: true,
                    showCoverageOnHover: true
                });
                expandedMapInstance.addLayer(markerClusterGroup);
                
                loadMapMarkers(allLocations);
                updateLocationStats(allLocations);
                
                // Also prepare chart for toggle
                setTimeout(() => {
                    renderExpandedChart(allLocations);
                    renderLocationTable(allLocations);
                }, 200);
            }, 100);
        } else {
            expandedMapInstance.invalidateSize();
        }
    });
    
    // Toggle between map and chart view
    document.getElementById('toggleView').addEventListener('click', function() {
        const mapView = document.getElementById('expandedMap').parentElement;
        const chartView = document.getElementById('chartView');
        const toggleBtn = this;
        const modeLabel = document.getElementById('modalStatMode');
        
        if (currentView === 'map') {
            mapView.style.display = 'none';
            chartView.style.display = 'block';
            toggleBtn.textContent = 'Show Map';
            modeLabel.textContent = 'Chart';
            currentView = 'chart';
        } else {
            mapView.style.display = 'block';
            chartView.style.display = 'none';
            toggleBtn.textContent = 'Show Chart';
            modeLabel.textContent = 'Map';
            currentView = 'map';
            if (expandedMapInstance) {
                setTimeout(() => expandedMapInstance.invalidateSize(), 100);
            }
        }
    });
    
    // Load markers onto map
    function loadMapMarkers(locations) {
        if (!expandedMapInstance || !markerClusterGroup) return;
        
        markerClusterGroup.clearLayers();
        
        if (!locations || locations.length === 0) {
            alert('No location data available.');
            return;
        }
        
        locations.forEach(location => {
            const coords = countryCoordinates[location.country];
            if (coords) {
                const marker = L.marker(coords, {
                    icon: buildLocationIcon(location, true)
                }).bindPopup(`
                    <div style=\"min-width: 200px;\">
                        <h4 style="margin: 0 0 0.5rem 0; color: #2563eb;">${location.country}</h4>
                        <p style=\"margin: 0.25rem 0;\"><strong>Users:</strong> ${location.user_count}</p>
                        ${location.roles ? `<p style=\"margin: 0.25rem 0;\"><strong>Roles:</strong> ${location.roles}</p>` : ''}
                        ${location.batches ? `<p style=\"margin: 0.25rem 0;\"><strong>Batches:</strong> ${location.batches}</p>` : ''}
                    </div>
                `);
                
                markerClusterGroup.addLayer(marker);
            }
        });
        
        // Fit bounds if markers exist
        if (markerClusterGroup.getLayers().length > 0) {
            expandedMapInstance.fitBounds(markerClusterGroup.getBounds(), {padding: [50, 50]});
        }
    }
    
    // Close modal
    document.getElementById('closeMapModal').addEventListener('click', function() {
        document.getElementById('mapModal').style.display = 'none';
    });
    
    // Close modal when clicking outside
    document.getElementById('mapModal').addEventListener('click', function(e) {
        if (e.target.id === 'mapModal') {
            this.style.display = 'none';
        }
    });

    // Render expanded chart
    function renderExpandedChart(locations) {
        const chartCtx = document.getElementById('expandedCountriesChart');
        if (!chartCtx || locations.length === 0) return;
        
        if (expandedChart) {
            expandedChart.destroy();
        }
        
        expandedChart = new Chart(chartCtx.getContext('2d'), {
            type: 'bar',
            data: {
                labels: locations.map(loc => loc.country),
                datasets: [{
                    label: 'Users',
                    data: locations.map(loc => parseInt(loc.user_count || 0)),
                    backgroundColor: locations.map((loc, i) => {
                        const colors = ['#60a5fa', '#34d399', '#fbbf24', '#f87171', '#a78bfa'];
                        return colors[i % colors.length];
                    }),
                    borderWidth: 1
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            afterLabel: function(context) {
                                const location = locations[context.dataIndex];
                                let info = [];
                                if (location.roles) info.push(`Roles: ${location.roles}`);
                                if (location.batches) info.push(`Batches: ${location.batches}`);
                                return info.join('\\n');
                            }
                        }
                    }
                },
                scales: {
                    x: { 
                        beginAtZero: true,
                        ticks: { precision: 0 }
                    }
                }
            }
        });
    }

    // Render location table
    function renderLocationTable(locations) {
        const table = document.getElementById('locationTable');
        if (!table || locations.length === 0) {
            table.innerHTML = '<p style="text-align: center; color: var(--muted);">No location data available</p>';
            return;
        }
        
        let html = `
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="background: var(--surface); border-bottom: 2px solid var(--border);">
                        <th style="padding: 0.75rem; text-align: left;">Country</th>
                        <th style="padding: 0.75rem; text-align: right;">Users</th>
                        <th style="padding: 0.75rem; text-align: left;">Roles</th>
                        <th style="padding: 0.75rem; text-align: left;">Batches</th>
                    </tr>
                </thead>
                <tbody>
        `;
        
        locations.forEach((loc, i) => {
            html += `
                <tr style="border-bottom: 1px solid var(--border); ${i % 2 === 0 ? 'background: var(--surface-2);' : ''}">
                    <td style="padding: 0.75rem;">${loc.country}</td>
                    <td style="padding: 0.75rem; text-align: right; font-weight: 600;">${loc.user_count}</td>
                    <td style="padding: 0.75rem; font-size: 0.85rem;">${loc.roles || '-'}</td>
                    <td style="padding: 0.75rem; font-size: 0.85rem;">${loc.batches || '-'}</td>
                </tr>
            `;
        });
        
        html += '</tbody></table>';
        table.innerHTML = html;
    }

    // Update location statistics
    function updateLocationStats(locations) {
        const totalUsers = locations.reduce((sum, loc) => sum + parseInt(loc.user_count || 0), 0);
        const uniqueCountries = locations.length;
        const topCountry = locations.length > 0 ? locations[0].country : 'N/A';
        
        document.getElementById('modalStatUsers').textContent = totalUsers.toLocaleString();
        document.getElementById('modalStatCountries').textContent = uniqueCountries;
        document.getElementById('modalStatTopCountry').textContent = topCountry;
    }

    // Apply filters
    document.getElementById('applyMapFilters').addEventListener('click', function() {
        const country = document.getElementById('filterCountry').value;
        const batch = document.getElementById('filterBatch').value;
        const role = document.getElementById('filterRole').value;
        
        // Build filter URL for role change
        if (role && role !== '<?php echo $data['roleFilter'] ?? ''; ?>') {
            window.location.href = `<?php echo URLROOT; ?>/admin/engagement?role=${role}`;
        } else {
            // Filter client-side
            let filtered = allLocations;
            
            if (country) {
                filtered = filtered.filter(loc => loc.country === country);
            }
            if (batch) {
                filtered = filtered.filter(loc => loc.batches && loc.batches.includes(batch));
            }
            
            // Update both map and chart views
            loadMapMarkers(filtered);
            renderExpandedChart(filtered);
            renderLocationTable(filtered);
            updateLocationStats(filtered);
        }
    });

    // Reset filters
    document.getElementById('resetMapFilters').addEventListener('click', function() {
        document.getElementById('filterCountry').value = '';
        document.getElementById('filterBatch').value = '';
        document.getElementById('filterRole').value = '';
        
        // Reset both map and chart views
        loadMapMarkers(allLocations);
        renderExpandedChart(allLocations);
        renderLocationTable(allLocations);
        updateLocationStats(allLocations);
    });

    // Set initial role filter value if present
    <?php if ($data['roleFilter'] ?? null): ?>
    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('filterRole').value = '<?php echo $data['roleFilter']; ?>';
    });
    <?php endif; ?>

    const roleData = <?php echo json_encode($data['charts']['roles'] ?? []); ?>;
    const batchData = <?php echo json_encode($data['charts']['batches'] ?? []); ?>;
    const genderData = <?php echo json_encode($data['charts']['genders'] ?? []); ?>;
    const skillData = <?php echo json_encode($data['charts']['skills'] ?? []); ?>;
    const eventStatusData = <?php echo json_encode($data['charts']['event_status'] ?? []); ?>;
    const eventRequestStatusData = <?php echo json_encode($data['charts']['event_request_status'] ?? []); ?>;
    const engagement = <?php echo json_encode($data['engagement'] ?? []); ?>;
    const roleFilterValue = <?php echo json_encode($data['roleFilter'] ?? null); ?>;
    const usersByRole = <?php echo json_encode($data['usersByRole'] ?? []); ?>;
    const contextTotalUsers = <?php echo json_encode((int)$contextTotalUsers); ?>;

    // Populate simple metrics
    document.addEventListener('DOMContentLoaded', () => {
        if (window.Chart) {
            Chart.defaults.color = '#9caec3';
            Chart.defaults.borderColor = 'rgba(148, 163, 184, 0.2)';
            Chart.defaults.font.family = "'Poppins', 'Segoe UI', sans-serif";
        }

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
        if(usersEl) usersEl.textContent = (contextTotalUsers ?? 0).toLocaleString();
        if(activeEl) activeEl.textContent = (<?php echo json_encode($data['metrics']['active_30_days'] ?? 0); ?>).toLocaleString();
        if(growthEl) growthEl.textContent = ('+' + (<?php echo json_encode((int)($data['metrics']['growth_3_months_pct'] ?? 0)); ?>) + '%');

        const messagesEl = document.getElementById('metric-messages');
        const eventsEl = document.getElementById('metric-events');
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

        function buildCommonOptions(extra = {}) {
            return {
                responsive: true,
                maintainAspectRatio: false,
                interaction: { mode: 'index', intersect: false },
                ...extra,
            };
        }

        const roleDataObj = buildLabelsValues(roleArr, 'role', 'count');
        const roleCtx = document.getElementById('roleChart');
        if(roleCtx){
            let labels = roleDataObj.labels;
            let values = roleDataObj.values;
            if(!labels.length && roleFilterValue){
                labels = [roleFilterValue.charAt(0).toUpperCase() + roleFilterValue.slice(1)];
                values = [parseInt(usersByRole[roleFilterValue] || 0)];
            }
            if(labels.length){
                new Chart(roleCtx.getContext('2d'), {
                    type:'doughnut',
                    data: {
                        labels,
                        datasets:[{ data: values, backgroundColor: ['#60a5fa','#34d399','#fbbf24','#f87171','#a78bfa'] }]
                    },
                    options: buildCommonOptions({
                        plugins:{legend:{position:'bottom', labels:{usePointStyle:true, boxWidth:10}}}
                    })
                });
            }
        }

        const batchDataObj = buildLabelsValues(batchArr, 'batch', 'count');
        const batchCtx = document.getElementById('batchChart');
        if(batchCtx && batchDataObj.labels.length){
            new Chart(batchCtx.getContext('2d'), {
                type:'bar',
                data:{ labels: batchDataObj.labels, datasets:[{ label: 'Users', data: batchDataObj.values, backgroundColor:'#7fb3f0', borderRadius: 8, maxBarThickness: 38 }] },
                options: buildCommonOptions({
                    plugins: { legend: { display: false } },
                    scales:{ y:{beginAtZero:true, ticks:{precision:0}}, x: { ticks: { maxRotation: 0, minRotation: 0 } } }
                })
            });
        }

        const genderDataObj = buildLabelsValues(genderArr, 'gender', 'count');
        const genderCtx = document.getElementById('genderChart');
        if(genderCtx && genderDataObj.labels.length){
            new Chart(genderCtx.getContext('2d'), {
                type:'doughnut',
                data:{ labels: genderDataObj.labels.map(l => l || 'Unspecified'), datasets:[{ data: genderDataObj.values, backgroundColor:['#f472b6','#60a5fa','#a3a3a3'] }] },
                options: buildCommonOptions({
                    plugins:{legend:{position:'bottom', labels:{usePointStyle:true, boxWidth:10}}}
                })
            });
        }

        const overCtx = document.getElementById('activeOverTime');
        if(overCtx && (overArr||[]).length){
            const labels = overArr.map(r=>r.ym);
            const values = overArr.map(r=>parseInt(r.c||0));
            new Chart(overCtx.getContext('2d'), {
                type:'line',
                data:{ labels, datasets:[{ label: 'Active Users', data: values, borderColor:'#4ade80', pointBackgroundColor:'#4ade80', fill:false, tension:0.35 }] },
                options: buildCommonOptions({
                    plugins: { legend: { display: false } },
                    scales:{ y:{beginAtZero:true, ticks:{precision:0}} }
                })
            });
        }

        const signupsArr = timeSeries.signups || [];
        const signupsCtx = document.getElementById('signupsChart');
        if(signupsCtx && signupsArr.length){
            new Chart(signupsCtx.getContext('2d'), {
                type:'line',
                data:{ labels: signupsArr.map(r=>r.ym), datasets:[{ label:'Signups', data: signupsArr.map(r=>parseInt(r.c||0)), borderColor:'#60a5fa', pointBackgroundColor:'#60a5fa', fill:false, tension:0.35 }] },
                options: buildCommonOptions({
                    plugins: { legend: { display: false } },
                    scales:{y:{beginAtZero:true, ticks:{precision:0}}}
                })
            });
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
            new Chart(contentCtx.getContext('2d'), {
                type:'line',
                data:{ labels, datasets:[
                    { label:'Posts', data: toSeries(postsArr), borderColor:'#93c5fd', pointBackgroundColor:'#93c5fd', fill:false, tension:0.35 },
                    { label:'Comments', data: toSeries(commentsArr), borderColor:'#fbbf24', pointBackgroundColor:'#fbbf24', fill:false, tension:0.35 },
                    { label:'Reactions', data: toSeries(reactionsArr), borderColor:'#f87171', pointBackgroundColor:'#f87171', fill:false, tension:0.35 }
                ] },
                options: buildCommonOptions({
                    plugins:{legend:{position:'bottom', labels:{usePointStyle:true, boxWidth:10}}},
                    scales:{y:{beginAtZero:true, ticks:{precision:0}}}
                })
            });
        }

        const messagesArr = timeSeries.messages || [];
        const messagesCtx = document.getElementById('messagesChart');
        if(messagesCtx && messagesArr.length){
            new Chart(messagesCtx.getContext('2d'), {
                type:'bar',
                data:{ labels: messagesArr.map(r=>r.ym), datasets:[{ label:'Messages', data: messagesArr.map(r=>parseInt(r.c||0)), backgroundColor:'#a78bfa', borderRadius: 8, maxBarThickness: 42 }] },
                options: buildCommonOptions({
                    plugins: { legend: { display: false } },
                    scales:{y:{beginAtZero:true, ticks:{precision:0}}}
                })
            });
        }

        const mixCtx = document.getElementById('engagementMix');
        if(mixCtx){
            const mixLabels = ['Posts','Comments','Reactions','Messages'];
            const mixValues = [engagement.posts||0, engagement.comments||0, engagement.reactions||0, engagement.messages||0];
            new Chart(mixCtx.getContext('2d'), {
                type:'doughnut',
                data:{ labels: mixLabels, datasets:[{ data: mixValues, backgroundColor:['#60a5fa','#fbbf24','#f87171','#a78bfa'] }] },
                options: buildCommonOptions({
                    plugins:{legend:{position:'bottom', labels:{usePointStyle:true, boxWidth:10}}}
                })
            });
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
            new Chart(pipelineCtx.getContext('2d'), {
                type:'bar',
                data:{ labels, datasets:[{ label: 'Items', data: values, backgroundColor:'#34d399', borderRadius: 8, maxBarThickness: 34 }] },
                options: buildCommonOptions({
                    plugins:{legend:{display:false}},
                    scales:{y:{beginAtZero:true, ticks:{precision:0}}, x:{ticks:{maxRotation:30, minRotation:30}}}
                })
            });
        }

        const skillsCtx = document.getElementById('skillsChart');
        if(skillsCtx){
            const entries = Object.entries(skillData || {}).map(([k,v]) => ({ k, v: parseInt(v||0) }))
                .sort((a,b)=>b.v-a.v).slice(0, 8);
            if(entries.length){
                new Chart(skillsCtx.getContext('2d'), {
                    type:'bar',
                    data:{ labels: entries.map(e=>e.k), datasets:[{ label:'Users', data: entries.map(e=>e.v), backgroundColor:'#f59e0b', borderRadius: 8, maxBarThickness: 34 }] },
                    options: buildCommonOptions({
                        plugins: { legend: { display: false } },
                        scales:{y:{beginAtZero:true, ticks:{precision:0}}, x:{ticks:{maxRotation:40, minRotation:40}}}
                    })
                });
            }
        }
    })();
</script>

<?php $content = ob_get_clean(); ?>
<?php require APPROOT . '/views/admin/dashboard_layout.php'; ?>


