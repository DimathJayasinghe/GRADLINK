# Geographic Analytics - Implementation Guide

## Overview

The Geographic Analytics system provides country-level distribution visualization of users with advanced filtering capabilities. Simple and effective - users select their country at registration, and admins can visualize the distribution through interactive charts and tables.

## Features

### 1. **Dashboard Chart (Card View)**
- Bar chart showing top 10 countries by user count
- Shows summary statistics (total users, total countries)
- "📊 View Details" button to open detailed modal
- Role-based filtering (All/Admin/Alumni/Students)

### 2. **Detailed Modal View**
- **Large Horizontal Bar Chart**: All countries visualized
- **Triple Filter System**:
  - Country filter (dropdown with all countries)
  - Batch filter (dropdown with all batches)
  - Role filter (All/Admin/Alumni/Students)
- **Interactive Table**: 
  - Country name with emoji
  - User count per country
  - Roles present (admin, alumni, undergrad)
  - Batches present (e.g., "2018, 2019, 2020")
- **Real-Time Statistics**: Updates as filters change (users, countries, top country)

### 3. **Simple Data Collection**
- One field at registration: Country dropdown
- One database table: user_id → country mapping
- No complex coordinates or geocoding needed
- Easy to understand and maintain

## Database Schema

### Table: `user_locations`

```sql
CREATE TABLE IF NOT EXISTS `user_locations` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `user_id` INT(11) NOT NULL,
  `country` VARCHAR(100) NOT NULL DEFAULT 'Sri Lanka',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_user_location` (`user_id`),
  KEY `idx_country` (`country`),
  CONSTRAINT `fk_user_locations_user_id` 
    FOREIGN KEY (`user_id`) 
    REFERENCES `users` (`id`) 
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

**Key Features:**
- Simple 5-column structure
- One country per user (UNIQUE constraint on user_id)
- Foreign key ensures data integrity
- Index on country for fast GROUP BY queries
- Default value: 'Sri Lanka'

## Backend Implementation

### Model Methods (M_admin.php)

#### 1. `getUserLocations(?string $role, ?string $batch, ?string $country): array`
Fetches country distribution with optional filtering.

```php
$locations = $this->adminModel->getUserLocations('alumni', '2020', null);
```

**Returns:**
```php
[
    [
        'country' => 'Sri Lanka',
        'user_count' => 234,
        'roles' => 'alumni,students',
        'batches' => '2018,2019,2020'
    ],
    // ...
]
```

#### 2. `getLocationSummary(?string $role): array`
Returns aggregated statistics.

```php
$summary = $this->adminModel->getLocationSummary('students');
```

**Returns:**
```php
[
    'total_countries' => 5,
    'total_users_with_location' => 234,
    'most_common_country' => 'Sri Lanka'
]
```

#### 3. `getCountriesWithUsers(): array`
Returns list of countries for filter dropdown.

```php
$countries = $this->adminModel->getCountriesWithUsers();
```

**Returns:**
```php
[
    ['country' => 'Sri Lanka', 'user_count' => 234],
    ['country' => 'United Kingdom', 'user_count' => 45],
    // ...
]
```

#### 4. `getBatches(): array`
Returns list of distinct batch numbers.

```php
$batches = $this->adminModel->getBatches();
```

**Returns:**
```php
[
    ['batch_no' => '2018'],
    ['batch_no' => '2019'],
    // ...
]
```

## Frontend Implementation

### JavaScript Functions

#### `renderExpandedChart(locations)`
Renders horizontal bar chart in modal with color-coded bars.

**Parameters:**
- `locations` (array): Country data from backend

**Behavior:**
- Destroys existing chart if present
- Creates horizontal bar chart
- Each country gets a different color
- Tooltips show roles and batches

#### `renderLocationTable(locations)`
Generates HTML table with detailed country breakdown.

**Parameters:**
- `locations` (array): Country data

**Renders:**
- Country name with 🌍 emoji
- User count (bold, right-aligned)
- Roles comma-separated
- Batches comma-separated
- Alternating row colors

#### `updateLocationStats(locations)`
Updates modal footer statistics in real-time.

**Parameters:**
- `locations` (array): Current filtered locations

**Updates:**
- Total users count
- Total countries count
- Top country name
- Current view mode

### Filter System

**Apply Filters:**
```javascript
document.getElementById('applyLocationFilters').addEventListener('click', function() {
    const country = document.getElementById('filterCountry').value;
    const batch = document.getElementById('filterBatch').value;
    
    let filtered = allLocations;
    
    if (country) {
        filtered = filtered.filter(loc => loc.country === country);
    }
    if (batch) {
        filtered = filtered.filter(loc => loc.batches.includes(batch));
    }
    
    renderExpandedChart(filtered);
    renderLocationTable(filtered);
    updateLocationStats(filtered);
});
```

**Reset Filters:**
```javascript
document.getElementById('resetLocationFilters').addEventListener('click', function() {
    document.getElementById('filterCountry').value = '';
    document.getElementById('filterBatch').value = '';
    renderExpandedChart(allLocations);
    renderLocationTable(allLocations);
    updateLocationStats(allLocations);
});
```

## CSS Styling

### Key Classes

**`.map-modal`**: Full-screen overlay with backdrop blur
- `position: fixed; inset: 0; z-index: 1000;`
- `background: rgba(0,0,0,0.75);`

**`.map-modal-content`**: Modal container with animation
- `max-width: 95vw; max-height: 95vh;`
- `animation: slideUp 0.3s ease;`

**`.map-modal-filters`**: Filter section layout
- `display: flex; flex-wrap: wrap; gap: 1rem;`

**`.location-table`**: Table wrapper with overflow
- `margin-top: 2rem; overflow-x: auto;`

**`.location-table table`**: Styled table
- `width: 100%; border-collapse: collapse;`

### Responsive Breakpoints

- **900px**: Tablet layout (stack filters vertically)
- **600px**: Mobile layout (full-width filters, smaller text)

## Usage Guide

### 1. Adding User Countries

**At Registration:**
```php
// In signup controller after creating user
$userId = $this->db->lastInsertId();
$country = $_POST['country'];

$stmt = $this->db->prepare("INSERT INTO user_locations (user_id, country) VALUES (?, ?)");
$stmt->execute([$userId, $country]);
```

**Bulk Insert for Existing Users:**
```sql
INSERT INTO user_locations (user_id, country)
SELECT id, 'Sri Lanka'
FROM users
WHERE id NOT IN (SELECT user_id FROM user_locations);
```

**Manual Insert:**
```sql
INSERT INTO user_locations (user_id, country)
VALUES (1, 'Sri Lanka'), (2, 'United Kingdom'), (3, 'United States');
```

### 2. Viewing Geographic Analytics

1. Navigate to Admin Dashboard → Engagement
2. Scroll to "Geographic Distribution" card
3. View bar chart with top 10 countries
4. Click "📊 View Details" button
5. Modal opens with:
   - Large horizontal bar chart (all countries)
   - Detailed table with roles and batches
   - Filter dropdowns (Country, Batch, Role)
6. Use filters to narrow down view:
   - Select specific country
   - Select batch year
   - Change role filter
7. Click "Apply Filters" to update
8. Click "Reset" to clear filters
9. Close modal with X button or click outside

### 3. Interpreting the Data

**Bar Colors:**
- Different colors for each country for easy distinction
- Height = number of users

**Table Shows:**
- Country name with 🌍 emoji
- Total users in that country
- Roles present (e.g., "admin, alumni, students")
- Batch numbers present (e.g., "2018, 2019, 2020")

**Statistics:**
- Total Users: Sum across all (filtered) countries
- Countries: Number of unique countries
- Top Country: Country with most users

## Deployment Steps

### 1. Deploy Database Schema

```bash
mysql -u username -p database_name < dev/user_locations.sql
```

### 2. Add Country Field to Registration

See GEOGRAPHIC_DEPLOYMENT.md for complete code examples.

### 3. Migrate Existing Users

```sql
-- Default to Sri Lanka for students
INSERT INTO user_locations (user_id, country)
SELECT id, 'Sri Lanka' FROM users WHERE role = 'students'
AND id NOT IN (SELECT user_id FROM user_locations);
```

### 4. Test Frontend

1. Access `/admin/dashboard/engagement`
2. Open browser console (F12)
3. Check for JavaScript errors
4. Click "View Details" button
5. Verify modal opens
6. Test all filters
7. Check table display
8. Verify responsive design on mobile

## Troubleshooting

### Chart Not Displaying

**Issue:** Countries chart shows blank/empty
**Solution:**
- Verify Chart.js is loaded: Check `<script src="...chart.js">`
- Check browser console for errors
- Verify location data exists: `SELECT * FROM user_locations LIMIT 5;`
- Check JS: `console.log(allLocations);` should show array

### Table Not Showing

**Issue:** Modal opens but no table displays
**Solution:**
- Check data being passed to view: `var_dump($data['locations']);` in controller
- Verify JavaScript receives data: `console.log(allLocations);` in browser console
- Check `renderLocationTable()` function is called

### Filters Not Working

**Issue:** Apply Filters button doesn't update chart/table
**Solution:**
- Check browser console for JavaScript errors
- Verify filter values: `console.log(country, batch);`
- Ensure `allLocations` variable is populated
- Check if `renderExpandedChart()` and `renderLocationTable()` functions are defined

### Modal Not Opening

**Issue:** Click "View Details" but nothing happens
**Solution:**
- Verify CSS is loaded: Check `engagement.css` includes `.map-modal` styles
- Check JavaScript event listener: `console.log('Button clicked');` inside handler
- Verify modal HTML exists: Inspect DOM for `<div id="locationModal">`
- Check z-index conflicts with other elements

## Future Enhancements

### Planned Features
1. **City-Level Data**: Optional city field for more granular analysis
2. **Export Data**: Download country data as CSV/Excel
3. **Bulk Upload**: Import locations from CSV file
4. **Interactive Map**: Add world map visualization with country highlighting
5. **Time Filter**: View country changes over time periods
6. **Department Filter**: Filter by academic department
7. **Alumni Network**: Connect users in same country
8. **Country Flags**: Display flag icons next to country names
9. **Percentage View**: Show percentage distribution pie chart
10. **Regional Grouping**: Group countries by continent/region

### Integration Opportunities
- **Profile Settings**: Let users update their country
- **Event Planning**: Suggest event locations based on concentration
- **Networking**: Connect users in same country
- **Alumni Relations**: Target campaigns by region
- **Job Board**: Show jobs in user's country

## Version History

**v2.0.0** (February 6, 2026)
- Simplified to country-only approach
- Removed complex map/coordinates
- Added bar chart visualization
- Added detailed table view
- Removed dependencies on Leaflet.js
- Reduced database fields from 10 to 5
- Simplified backend queries
- Improved performance
- Easier to maintain

**v1.0.0** (February 6, 2026 - Deprecated)
- Initial release with full map features
- City, state, lat/lng fields
- Leaflet.js marker clustering
- Complex database schema


## Features

### 1. **Small Map Card (Dashboard View)**
- Displays top 20 user locations on embedded map
- Shows summary statistics (total users, cities, countries)
- Expandable to full-screen modal via "🗺️ Expand Map" button
- Centered on Sri Lanka (default view)

### 2. **Expandable Full-Screen Modal**
- **Large Interactive Map**: World view with marker clustering
- **Triple Filter System**:
  - Country filter (dropdown with all countries)
  - Batch filter (dropdown with all batches)
  - Role filter (All/Admin/Alumni/Students)
- **Color-Coded Markers**:
  - 🔴 Red: Admins
  - 🔵 Teal: Alumni  
  - 🔵 Blue: Students
- **Marker Size**: Dynamically scales based on user count (√user_count)
- **Clustering**: Automatically groups nearby markers for better visualization
- **Real-Time Statistics**: Updates as filters change (users, cities, countries)

### 3. **Interactive Features**
- Click markers for detailed popups showing:
  - City, country
  - Total users at that location
  - Batch numbers present
  - Role breakdown
- Zoom and pan to explore regions
- Cluster markers expand on click (spiderfy)
- Auto-fit bounds to show all markers

## Database Schema

### Table: `user_locations`

```sql
CREATE TABLE user_locations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    city VARCHAR(100) NOT NULL,
    state VARCHAR(100),
    country VARCHAR(100) NOT NULL,
    latitude DECIMAL(10, 8),
    longitude DECIMAL(11, 8),
    location_type ENUM('home', 'work', 'study', 'other') DEFAULT 'home',
    is_primary BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id),
    INDEX idx_country (country),
    INDEX idx_city (city),
    INDEX idx_is_primary (is_primary),
    INDEX idx_location_type (location_type)
);
```

### Supporting Objects

**View: `v_user_locations`**
- Joins `user_locations` with `users` table
- Includes: user details, location info, role, batch

**Stored Procedure: `sp_get_location_distribution_by_role()`**
- Parameters: `p_role VARCHAR(20)`
- Returns: Location distribution grouped by role

**Function: `fn_get_city_user_count()`**
- Parameters: `p_city VARCHAR(100)`, `p_country VARCHAR(100)`
- Returns: INT (user count for that city)

**Triggers:**
- `before_insert_location`: Ensures only one primary location per user
- `before_update_location`: Maintains primary location constraint

## Backend Implementation

### Model Methods (M_admin.php)

#### 1. `getUserLocations(?string $role, ?string $batch, ?string $country): array`
Fetches location data with optional filtering.

```php
$locations = $this->adminModel->getUserLocations('alumni', '2020', 'Sri Lanka');
```

**Returns:**
```php
[
    [
        'city' => 'Colombo',
        'country' => 'Sri Lanka',
        'latitude' => 6.9271,
        'longitude' => 79.8612,
        'user_count' => 45,
        'batches' => '2018,2019,2020',
        'roles' => 'alumni,students'
    ],
    // ...
]
```

#### 2. `getLocationSummary(?string $role): array`
Returns aggregated statistics.

```php
$summary = $this->adminModel->getLocationSummary('students');
```

**Returns:**
```php
[
    'total_countries' => 1,
    'total_cities' => 12,
    'total_users_with_location' => 234,
    'most_common_country' => 'Sri Lanka'
]
```

#### 3. `getCountriesWithUsers(): array`
Returns list of countries for filter dropdown.

```php
$countries = $this->adminModel->getCountriesWithUsers();
```

**Returns:**
```php
[
    ['country' => 'Sri Lanka', 'user_count' => 234],
    ['country' => 'United Kingdom', 'user_count' => 45],
    // ...
]
```

#### 4. `getBatches(): array`
Returns list of distinct batch numbers.

```php
$batches = $this->adminModel->getBatches();
```

**Returns:**
```php
[
    ['batch' => '2018'],
    ['batch' => '2019'],
    // ...
]
```

#### 5. `getLocationHeatmapData(?string $role): array`
Returns coordinates with intensity values for heatmap visualization.

```php
$heatmap = $this->adminModel->getLocationHeatmapData('alumni');
```

**Returns:**
```php
[
    [
        'latitude' => 6.9271,
        'longitude' => 79.8612,
        'intensity' => 45
    ],
    // ...
]
```

## Frontend Implementation

### JavaScript Functions

#### `loadMapMarkers(locations)`
Clears existing markers and loads new ones with color coding and clustering.

**Parameters:**
- `locations` (array): Location data from backend

**Behavior:**
- Clears marker cluster group
- Creates circle markers with role-based colors
- Binds popups with detailed information
- Fits map bounds to show all markers

#### `updateMapStats(locations)`
Updates modal footer statistics in real-time.

**Parameters:**
- `locations` (array): Current filtered locations

**Updates:**
- Total users count
- Total cities count
- Total countries count
- Current view mode

### Filter System

**Apply Filters:**
```javascript
document.getElementById('applyMapFilters').addEventListener('click', function() {
    const country = document.getElementById('countryFilter').value;
    const batch = document.getElementById('batchFilter').value;
    
    let filtered = allLocations;
    
    if (country) {
        filtered = filtered.filter(loc => loc.country === country);
    }
    if (batch) {
        filtered = filtered.filter(loc => loc.batches.includes(batch));
    }
    
    loadMapMarkers(filtered);
    updateMapStats(filtered);
});
```

**Reset Filters:**
```javascript
document.getElementById('resetMapFilters').addEventListener('click', function() {
    document.getElementById('countryFilter').value = '';
    document.getElementById('batchFilter').value = '';
    loadMapMarkers(allLocations);
    updateMapStats(allLocations);
});
```

## CSS Styling

### Key Classes

**`.map-modal`**: Full-screen overlay with backdrop blur
- `position: fixed; inset: 0; z-index: 1000;`
- `background: rgba(0,0,0,0.75);`

**`.map-modal-content`**: Modal container with animation
- `max-width: 95vw; max-height: 95vh;`
- `animation: slideUp 0.3s ease;`

**`.map-modal-filters`**: Filter section layout
- `display: flex; flex-wrap: wrap; gap: 1rem;`

**`.expand-map-btn`**: Expand button in small card
- `position: absolute; top: 10px; right: 10px;`
- `z-index: 500;`

### Responsive Breakpoints

- **900px**: Tablet layout (stack filters vertically)
- **600px**: Mobile layout (full-width filters, smaller text)

## Usage Guide

### 1. Adding User Locations

**Via SQL:**
```sql
INSERT INTO user_locations (user_id, city, state, country, latitude, longitude, is_primary)
VALUES 
    (1, 'Colombo', 'Western', 'Sri Lanka', 6.9271, 79.8612, TRUE),
    (2, 'Kandy', 'Central', 'Sri Lanka', 7.2906, 80.6337, TRUE);
```

**Via PHP:**
```php
$stmt = $db->prepare("
    INSERT INTO user_locations (user_id, city, country, latitude, longitude, is_primary)
    VALUES (?, ?, ?, ?, ?, ?)
");
$stmt->execute([$userId, $city, $country, $lat, $lng, true]);
```

### 2. Viewing Geographic Analytics

1. Navigate to Admin Dashboard → Engagement
2. Scroll to "User Locations" card
3. View small map with top 20 locations
4. Click "🗺️ Expand Map" button
5. Use filters to narrow down view:
   - Select specific country
   - Select batch year
   - Change role filter (uses page-level filter)
6. Click "Apply Filters" to update map
7. Click markers to see details
8. Zoom/pan to explore regions
9. Click "Reset" to clear filters
10. Close modal with X button or click outside

### 3. Interpreting the Data

**Marker Colors:**
- Red circles = Admin locations
- Teal circles = Alumni locations  
- Blue circles = Student locations

**Marker Sizes:**
- Larger circles = More users at that location
- Calculated as: `radius = Math.sqrt(userCount) * 2`

**Cluster Numbers:**
- Blue circles with numbers = Multiple locations grouped
- Click to zoom in and separate

**Popups Show:**
- Exact city and country
- Total users at that location
- Batch numbers present (e.g., "2018, 2019, 2020")
- Roles present (e.g., "alumni, students")

## Deployment Steps

### 1. Deploy Database Schema

```bash
mysql -u username -p database_name < dev/user_locations.sql
```

### 2. Add Sample Data

For Sri Lankan students:
```sql
INSERT INTO user_locations (user_id, city, state, country, latitude, longitude, is_primary)
SELECT id, 'Colombo', 'Western', 'Sri Lanka', 6.9271, 79.8612, TRUE
FROM users WHERE role = 'students' AND id BETWEEN 1 AND 50;
```

For international alumni:
```sql
INSERT INTO user_locations (user_id, city, country, latitude, longitude, is_primary)
VALUES 
    (51, 'London', 'United Kingdom', 51.5074, -0.1278, TRUE),
    (52, 'New York', 'United States', 40.7128, -74.0060, TRUE),
    (53, 'Dubai', 'United Arab Emirates', 25.2048, 55.2708, TRUE);
```

### 3. Verify Installation

```sql
-- Check table structure
DESCRIBE user_locations;

-- View sample data
SELECT * FROM v_user_locations LIMIT 10;

-- Test stored procedure
CALL sp_get_location_distribution_by_role('alumni');

-- Test function
SELECT fn_get_city_user_count('Colombo', 'Sri Lanka');
```

### 4. Test Frontend

1. Access `/admin/dashboard/engagement`
2. Open browser console (F12)
3. Check for JavaScript errors
4. Click "Expand Map" button
5. Verify modal opens
6. Test all filters
7. Check marker interactions
8. Verify responsive design on mobile

## Troubleshooting

### Map Not Displaying

**Issue:** Small map or expanded map shows blank/gray
**Solution:**
- Verify Leaflet.js is loaded: Check `<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js">`
- Check browser console for errors
- Verify location data has coordinates: `SELECT * FROM user_locations WHERE latitude IS NULL;`

### Markers Not Appearing

**Issue:** Map loads but no markers show
**Solution:**
- Check data being passed to view: `var_dump($data['locations']);` in controller
- Verify JavaScript receives data: `console.log(allLocations);` in browser console
- Ensure coordinates are valid: Latitude (-90 to 90), Longitude (-180 to 180)

### Filters Not Working

**Issue:** Apply Filters button doesn't update map
**Solution:**
- Check browser console for JavaScript errors
- Verify filter values: `console.log(country, batch);`
- Ensure `allLocations` variable is populated
- Check if `loadMapMarkers()` function is defined

### Modal Not Opening

**Issue:** Click "Expand Map" but nothing happens
**Solution:**
- Verify CSS is loaded: Check `engagement.css` includes `.map-modal` styles
- Check JavaScript event listener: `console.log('Button clicked');` inside handler
- Verify modal HTML exists: Inspect DOM for `<div id="mapModal">`
- Check z-index conflicts with other elements

### Clustering Not Working

**Issue:** Many markers overlap without clustering
**Solution:**
- Verify MarkerCluster library is loaded: Check CDN links in view
- Check CSS is loaded: MarkerCluster CSS file
- Verify `markerClusterGroup` is initialized
- Check cluster configuration: `maxClusterRadius`, `spiderfyOnMaxZoom`

### Performance Issues

**Issue:** Map is slow with many markers (1000+)
**Solution:**
- Increase cluster radius: `maxClusterRadius: 80`
- Implement pagination: Load markers in chunks
- Use server-side filtering: Filter before sending to frontend
- Consider heatmap for very large datasets: Use `getLocationHeatmapData()`

## API Reference

### Controller Endpoint

**URL:** `/admin/dashboard/engagement`  
**Method:** GET  
**Parameters:**
- `role` (optional): 'admin', 'alumni', 'students', or empty for all

**Response Data:**
```php
[
    'locations' => [...],           // Location markers
    'locationSummary' => [...],     // Aggregate statistics
    'countries' => [...],           // Country list for filter
    'batches' => [...],             // Batch list for filter
    'heatmapData' => [...]         // Heatmap coordinates
]
```

## Future Enhancements

### Planned Features
1. **Heatmap Layer**: Toggle between markers and heatmap visualization
2. **Export Data**: Download location data as CSV/Excel
3. **Bulk Upload**: Import locations from CSV file
4. **Geocoding**: Auto-fill coordinates from city names
5. **Time Filter**: View location changes over time periods
6. **Department Filter**: Filter by academic department
7. **Search**: Search for specific cities or users
8. **Custom Markers**: Different icons for location types (home, work, study)
9. **Connection Lines**: Show student-alumni connections by location
10. **Analytics**: Most popular cities, migration patterns

### Integration Opportunities
- **Profile Settings**: Let users add/update their location
- **Event Planning**: Use location data to suggest event venues
- **Networking**: Connect users in same geographic area
- **Alumni Relations**: Target campaigns by region
- **Job Board**: Show jobs near user's location

## Support

For issues or questions:
1. Check this documentation first
2. Review browser console for errors
3. Verify database schema is correctly deployed
4. Test with sample data before production
5. Check responsive design on multiple devices

## Version History

**v1.0.0** (February 6, 2026)
- Initial release
- Small map card with top 20 locations
- Expandable full-screen modal
- Triple filter system (Country, Batch, Role)
- Color-coded markers by role
- Marker clustering with Leaflet MarkerCluster
- Real-time statistics
- Responsive design for mobile
- Complete database schema with triggers
- 5 backend API methods
- Comprehensive documentation
