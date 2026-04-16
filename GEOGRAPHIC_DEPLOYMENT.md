# Geographic Analytics - Quick Deployment Guide

## 🚀 Fast Track Deployment (3 Minutes)

### Step 1: Deploy Database (1 minute)

```sql
-- Execute the schema file
mysql -u root -p gradlink < dev/user_locations.sql

-- Or run manually:
USE gradlink;
SOURCE dev/user_locations.sql;
```

### Step 2: Add Sample Data (1 minute)

```sql
-- Insert sample countries for existing users
INSERT INTO user_locations (user_id, country)
VALUES
    (1, 'Sri Lanka'),
    (2, 'Sri Lanka'),
    (3, 'United Kingdom'),
    (4, 'United States'),
    (5, 'Canada'),
    (6, 'Australia'),
    (7, 'United Arab Emirates'),
    (8, 'Singapore'),
    (9, 'Sri Lanka'),
    (10, 'India');
```

### Step 3: Test Installation (1 minute)

```sql
-- Check table
SELECT COUNT(*) FROM user_locations;

-- View distribution
SELECT country, COUNT(*) as user_count 
FROM user_locations 
GROUP BY country 
ORDER BY user_count DESC;
```

### Step 4: Access Frontend

1. Navigate to: `http://localhost/GRADLINK/admin/dashboard/engagement`
2. Scroll to "Geographic Distribution" chart
3. Click "📊 View Details" button
4. Test filters and view detailed breakdown

---

## ✅ Verification Checklist

- [ ] Database table `user_locations` created
- [ ] Index on `country` column created
- [ ] Unique constraint on `user_id` working
- [ ] Sample data inserted (minimum 10 rows)
- [ ] Countries chart displays on dashboard
- [ ] View Details button opens modal
- [ ] Modal displays chart and table correctly
- [ ] Filters populate with data (countries, batches, roles)
- [ ] Apply filters updates chart and table
- [ ] Statistics update in real-time
- [ ] Close button works
- [ ] Responsive design works on mobile

---

## 🔧 Production Deployment

### 1. Collect Country Data at Registration

Add country field to your registration form:

**Signup Form (HTML):**
```html
<div class="form-group">
    <label for="country">Country</label>
    <select name="country" id="country" required>
        <option value="">Select your country</option>
        <option value="Sri Lanka">Sri Lanka</option>
        <option value="United States">United States</option>
        <option value="United Kingdom">United Kingdom</option>
        <option value="Canada">Canada</option>
        <option value="Australia">Australia</option>
        <option value="United Arab Emirates">United Arab Emirates</option>
        <option value="India">India</option>
        <option value="Singapore">Singapore</option>
        <!-- Add more countries as needed -->
    </select>
</div>
```

**Signup Controller (PHP):**
```php
// After user is created
$userId = $this->db->lastInsertId();
$country = $_POST['country'] ?? 'Sri Lanka';

// Insert location
$stmt = $this->db->prepare("INSERT INTO user_locations (user_id, country) VALUES (?, ?)");
$stmt->execute([$userId, $country]);
```

### 2. Bulk Update for Existing Users

For users already in the system without location data:

```sql
-- Set default country for existing students
INSERT INTO user_locations (user_id, country)
SELECT id, 'Sri Lanka'
FROM users
WHERE role = 'students'
  AND id NOT IN (SELECT user_id FROM user_locations);

-- Set default for alumni (you can customize based on your data)
INSERT INTO user_locations (user_id, country)
SELECT id, 'Sri Lanka'
FROM users
WHERE role = 'alumni'
  AND id NOT IN (SELECT user_id FROM user_locations);
```

### 3. Add Country Dropdown List

Create a reusable PHP array of countries:

**app/data/countries.php:**
```php
<?php
return [
    'Sri Lanka',
    'Afghanistan',
    'Australia',
    'Bangladesh',
    'Canada',
    'China',
    'France',
    'Germany',
    'India',
    'Indonesia',
    'Japan',
    'Malaysia',
    'Maldives',
    'Nepal',
    'New Zealand',
    'Pakistan',
    'Philippines',
    'Saudi Arabia',
    'Singapore',
    'South Korea',
    'Thailand',
    'United Arab Emirates',
    'United Kingdom',
    'United States',
    // Add more countries as needed
];
```

**Usage in views:**
```php
<?php
$countries = require_once '../app/data/countries.php';
?>
<select name="country" required>
    <option value="">Select country</option>
    <?php foreach ($countries as $country): ?>
        <option value="<?php echo htmlspecialchars($country); ?>">
            <?php echo htmlspecialchars($country); ?>
        </option>
    <?php endforeach; ?>
</select>
```

### 4. Enable User Self-Service Location Update

Allow users to update their country via profile settings:

**Profile Settings Form:**
```html
<form method="POST" action="/profile/update-location">
    <div class="form-group">
        <label>Your Country</label>
        <select name="country" required>
            <!-- Country options here -->
        </select>
    </div>
    <button type="submit">Update Location</button>
</form>
```

**Profile Controller:**
```php
public function updateLocation() {
    $userId = $_SESSION['user_id'];
    $country = $_POST['country'];
    
    // Insert or update
    $stmt = $this->db->prepare("
        INSERT INTO user_locations (user_id, country)
        VALUES (?, ?)
        ON DUPLICATE KEY UPDATE country = VALUES(country)
    ");
    
    $stmt->execute([$userId, $country]);
    
    // Redirect back with success message
    $_SESSION['success'] = 'Location updated successfully!';
    header('Location: ' . URLROOT . '/settings');
}
```

---

## 📊 Common SQL Queries

### Get All Locations by Role
```sql
SELECT ul.country, COUNT(*) as users
FROM user_locations ul
JOIN users u ON ul.user_id = u.id
WHERE u.role = 'alumni'
GROUP BY ul.country
ORDER BY users DESC;
```

### Find Users Without Location
```sql
SELECT u.id, u.name, u.email, u.role
FROM users u
LEFT JOIN user_locations ul ON u.id = ul.user_id
WHERE ul.id IS NULL;
```

### Top 10 Countries by User Count
```sql
SELECT country, COUNT(*) as user_count
FROM user_locations
GROUP BY country
ORDER BY user_count DESC
LIMIT 10;
```

### Alumni Distribution by Country
```sql
SELECT ul.country, COUNT(DISTINCT ul.user_id) as alumni_count
FROM user_locations ul
JOIN users u ON ul.user_id = u.id
WHERE u.role = 'alumni'
GROUP BY ul.country
ORDER BY alumni_count DESC;
```

### Students in Sri Lanka
```sql
SELECT COUNT(*) as sri_lankan_students
FROM user_locations ul
JOIN users u ON ul.user_id = u.id
WHERE u.role = 'students' AND ul.country = 'Sri Lanka';
```

### Batch-wise Country Distribution
```sql
SELECT u.batch_no, ul.country, COUNT(*) as students
FROM user_locations ul
JOIN users u ON ul.user_id = u.id
WHERE u.role = 'students'
GROUP BY u.batch_no, ul.country
ORDER BY u.batch_no, students DESC;
```

---

## 🐛 Quick Troubleshooting

### "Table doesn't exist"
```sql
SHOW TABLES LIKE 'user_locations';
-- If empty, re-run: SOURCE dev/user_locations.sql;
```

### "Chart not showing"
```javascript
// Open browser console and check:
console.log(allLocations); // Should show array of locations
console.log(typeof Chart); // Should be "function" (Chart.js loaded)
```

### "Modal not opening"
```javascript
// Check modal element exists:
console.log(document.getElementById('locationModal')); // Should not be null

// Check CSS loaded:
const modal = document.querySelector('.map-modal');
console.log(window.getComputedStyle(modal).position); // Should be "fixed"
```

### "Filters not working"
```sql
-- Verify data has variety:
SELECT DISTINCT country FROM user_locations; -- Multiple countries
SELECT DISTINCT u.batch_no FROM users u 
JOIN user_locations ul ON u.id = ul.user_id; -- Multiple batches
```

---

## 📝 Files Modified Summary

| File | Changes | Lines |
|------|---------|-------|
| `dev/user_locations.sql` | Simplified schema | 30 |
| `M_admin.php` | 5 simplified methods | 130 |
| `Admin.php` | Enhanced engagement() | 15 |
| `v_engagement.php` | Chart + table visualization | 200 |
| `engagement.css` | Table + modal styles | 50 |
| **Total** | | **425 lines** |

---

## 🎯 Expected Results

After successful deployment, you should see:

1. **Dashboard View:**
   - Bar chart showing top 10 countries
   - Summary: "X users across Y countries"
   - "📊 View Details" button

2. **Modal View:**
   - Large horizontal bar chart with all countries
   - 3 filter dropdowns (Country, Batch, Role)
   - Detailed table with country, user count, roles, batches
   - Footer with live statistics

3. **Interactions:**
   - Click "View Details" → opens modal
   - Select filters → click "Apply Filters"
   - Chart and table update instantly
   - Click "Reset" → show all countries
   - Close (X or backdrop) → return to dashboard

---

## 🔗 Related Documentation

- **Full Guide**: See `GEOGRAPHIC_ANALYTICS.md` for complete documentation
- **Database Schema**: See `dev/user_locations.sql` for schema details
- **Analytics System**: See `ANALYTICS_ENHANCEMENTS.md` for overall analytics
- **Role Filtering**: See `ROLE_BASED_FILTERING.md` for role-based features

---

## ⚡ Quick Commands Cheat Sheet

```bash
# Deploy database
mysql -u root -p gradlink < dev/user_locations.sql

# Check installation
mysql -u root -p gradlink -e "SELECT COUNT(*) FROM user_locations;"

# View distribution
mysql -u root -p gradlink -e "SELECT country, COUNT(*) FROM user_locations GROUP BY country ORDER BY COUNT(*) DESC;"

# Test in browser
curl http://localhost/GRADLINK/admin/dashboard/engagement
```

---

**Next Steps:**
1. Deploy database schema ✅
2. Add sample data ✅  
3. Test in browser ✅
4. Add country field to registration📍
5. Update existing user data 🔜
6. Enable user location updates 🔜


```sql
-- Execute the schema file
mysql -u root -p gradlink < dev/user_locations.sql

-- Or run manually:
USE gradlink;
SOURCE dev/user_locations.sql;
```

### Step 2: Add Sample Data (1 minute)

```sql
-- Sri Lankan students
INSERT INTO user_locations (user_id, city, state, country, latitude, longitude, is_primary)
VALUES
    (1, 'Colombo', 'Western', 'Sri Lanka', 6.9271, 79.8612, TRUE),
    (2, 'Kandy', 'Central', 'Sri Lanka', 7.2906, 80.6337, TRUE),
    (3, 'Galle', 'Southern', 'Sri Lanka', 6.0535, 80.2210, TRUE),
    (4, 'Jaffna', 'Northern', 'Sri Lanka', 9.6615, 80.0255, TRUE),
    (5, 'Matara', 'Southern', 'Sri Lanka', 5.9549, 80.5550, TRUE);

-- International alumni
INSERT INTO user_locations (user_id, city, country, latitude, longitude, is_primary)
VALUES
    (6, 'London', 'United Kingdom', 51.5074, -0.1278, TRUE),
    (7, 'New York', 'United States', 40.7128, -74.0060, TRUE),
    (8, 'Dubai', 'United Arab Emirates', 25.2048, 55.2708, TRUE),
    (9, 'Singapore', 'Singapore', 1.3521, 103.8198, TRUE),
    (10, 'Toronto', 'Canada', 43.6532, -79.3832, TRUE);
```

### Step 3: Test Installation (2 minutes)

```sql
-- Verify table
SELECT COUNT(*) FROM user_locations;

-- Check view
SELECT * FROM v_user_locations LIMIT 5;

-- Test procedure
CALL sp_get_location_distribution_by_role('alumni');

-- Test function
SELECT fn_get_city_user_count('Colombo', 'Sri Lanka');
```

### Step 4: Access Frontend

1. Navigate to: `http://localhost/GRADLINK/admin/dashboard/engagement`
2. Scroll to "User Locations" card
3. Click "🗺️ Expand Map" button
4. Test filters and interactions

---

## ✅ Verification Checklist

- [ ] Database table `user_locations` created
- [ ] Indexes created (5 total)
- [ ] View `v_user_locations` created
- [ ] Stored procedure `sp_get_location_distribution_by_role` created
- [ ] Function `fn_get_city_user_count` created
- [ ] Triggers created (2 total)
- [ ] Sample data inserted (minimum 10 rows)
- [ ] Small map displays on dashboard
- [ ] Expand button opens modal
- [ ] Modal displays correctly (centered, overlay)
- [ ] Filters populate with data
- [ ] Apply filters updates map
- [ ] Markers show with correct colors
- [ ] Clustering works on zoom
- [ ] Popups display on marker click
- [ ] Statistics update in real-time
- [ ] Close button works
- [ ] Responsive design works on mobile

---

## 🔧 Production Deployment

### 1. Bulk Location Import

For existing users without location data:

```sql
-- Update users based on signup IP or profile data
UPDATE users u
LEFT JOIN user_locations ul ON u.id = ul.user_id
SET ul.city = 'Colombo', 
    ul.country = 'Sri Lanka', 
    ul.latitude = 6.9271, 
    ul.longitude = 79.8612,
    ul.is_primary = TRUE
WHERE u.role = 'students' 
  AND ul.id IS NULL
  AND (u.signup_ip LIKE '192.168.%' OR u.profile_data LIKE '%Sri Lanka%');
```

### 2. Get Coordinates for Cities

Use this API or database to get lat/lng:

**Option A: OpenStreetMap Nominatim API (Free)**
```bash
curl "https://nominatim.openstreetmap.org/search?city=Colombo&country=Sri%20Lanka&format=json"
```

**Option B: Google Geocoding API (Paid)**
```bash
curl "https://maps.googleapis.com/maps/api/geocode/json?address=Colombo,Sri+Lanka&key=YOUR_API_KEY"
```

**Option C: Pre-populated City Database**
```sql
-- Create reference table
CREATE TABLE city_coordinates (
    city VARCHAR(100),
    country VARCHAR(100),
    latitude DECIMAL(10, 8),
    longitude DECIMAL(11, 8),
    PRIMARY KEY (city, country)
);

-- Insert common cities
INSERT INTO city_coordinates VALUES
    ('Colombo', 'Sri Lanka', 6.9271, 79.8612),
    ('Kandy', 'Sri Lanka', 7.2906, 80.6337),
    ('Galle', 'Sri Lanka', 6.0535, 80.2210),
    ('Jaffna', 'Sri Lanka', 9.6615, 80.0255),
    ('London', 'United Kingdom', 51.5074, -0.1278),
    ('New York', 'United States', 40.7128, -74.0060);

-- Use for bulk updates
UPDATE user_locations ul
JOIN city_coordinates cc ON ul.city = cc.city AND ul.country = cc.country
SET ul.latitude = cc.latitude, ul.longitude = cc.longitude
WHERE ul.latitude IS NULL;
```

### 3. Performance Optimization

For large datasets (10,000+ users):

```sql
-- Add composite index for faster filtering
CREATE INDEX idx_country_role 
ON user_locations(country, user_id) 
USING BTREE;

-- Analyze table for query optimization
ANALYZE TABLE user_locations;

-- Check query performance
EXPLAIN SELECT city, country, COUNT(*) as user_count
FROM v_user_locations
WHERE role = 'alumni'
GROUP BY city, country;
```

### 4. Enable User Self-Service

Allow users to update their location via profile settings:

**Profile Settings Form (HTML):**
```html
<form method="POST" action="/profile/update-location">
    <input type="text" name="city" placeholder="City" required>
    <input type="text" name="state" placeholder="State/Province">
    <input type="text" name="country" placeholder="Country" required>
    <select name="location_type">
        <option value="home">Home</option>
        <option value="work">Work</option>
        <option value="study">Study</option>
        <option value="other">Other</option>
    </select>
    <label>
        <input type="checkbox" name="is_primary" value="1"> Primary Location
    </label>
    <button type="submit">Save Location</button>
</form>
```

**Controller Handler:**
```php
// In your Profile controller
public function updateLocation() {
    $userId = $_SESSION['user_id'];
    $city = $_POST['city'];
    $country = $_POST['country'];
    
    // Geocode to get coordinates
    $coords = $this->geocodeCity($city, $country);
    
    // Insert or update
    $stmt = $this->db->prepare("
        INSERT INTO user_locations (user_id, city, state, country, latitude, longitude, location_type, is_primary)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE
            city = VALUES(city),
            state = VALUES(state),
            country = VALUES(country),
            latitude = VALUES(latitude),
            longitude = VALUES(longitude),
            location_type = VALUES(location_type),
            is_primary = VALUES(is_primary)
    ");
    
    $stmt->execute([
        $userId, $city, $_POST['state'], $country,
        $coords['lat'], $coords['lng'],
        $_POST['location_type'], $_POST['is_primary'] ?? 0
    ]);
}
```

---

## 📊 Common SQL Queries

### Get All Locations by Role
```sql
SELECT city, country, COUNT(*) as users
FROM v_user_locations
WHERE role = 'alumni'
GROUP BY city, country
ORDER BY users DESC;
```

### Find Users Without Location
```sql
SELECT u.id, u.name, u.email, u.role
FROM users u
LEFT JOIN user_locations ul ON u.id = ul.user_id
WHERE ul.id IS NULL;
```

### Top 10 Cities by User Count
```sql
SELECT city, country, COUNT(*) as user_count
FROM user_locations
GROUP BY city, country
ORDER BY user_count DESC
LIMIT 10;
```

### Alumni Distribution by Country
```sql
SELECT ul.country, COUNT(DISTINCT ul.user_id) as alumni_count
FROM user_locations ul
JOIN users u ON ul.user_id = u.id
WHERE u.role = 'alumni'
GROUP BY ul.country
ORDER BY alumni_count DESC;
```

### Batch-wise Location Distribution
```sql
SELECT u.batch, ul.city, ul.country, COUNT(*) as students
FROM user_locations ul
JOIN users u ON ul.user_id = u.id
WHERE u.role = 'students'
GROUP BY u.batch, ul.city, ul.country
ORDER BY u.batch, students DESC;
```

---

## 🐛 Quick Troubleshooting

### "Table doesn't exist"
```sql
SHOW TABLES LIKE 'user_locations';
-- If empty, re-run: SOURCE dev/user_locations.sql;
```

### "Markers not showing"
```javascript
// Open browser console and check:
console.log(allLocations); // Should show array of locations
console.log(typeof L); // Should be "object" (Leaflet loaded)
console.log(typeof L.markerClusterGroup); // Should be "function"
```

### "Modal not opening"
```javascript
// Check modal element exists:
console.log(document.getElementById('mapModal')); // Should not be null

// Check CSS loaded:
const modal = document.querySelector('.map-modal');
console.log(window.getComputedStyle(modal).position); // Should be "fixed"
```

### "Filters not working"
```sql
-- Verify data has variety:
SELECT DISTINCT country FROM user_locations; -- Multiple countries
SELECT DISTINCT u.batch FROM users u 
JOIN user_locations ul ON u.id = ul.user_id; -- Multiple batches
```

---

## 📝 Files Modified Summary

| File | Changes | Lines Added |
|------|---------|-------------|
| `dev/user_locations.sql` | Database schema | 200+ |
| `M_admin.php` | 5 new methods | 150+ |
| `Admin.php` | Enhanced engagement() | 15+ |
| `v_engagement.php` | Modal HTML + JS | 250+ |
| `engagement.css` | Modal styles | 350+ |
| **Total** | | **965+ lines** |

---

## 🎯 Expected Results

After successful deployment, you should see:

1. **Dashboard View:**
   - Small map card showing top locations
   - Summary: "X users in Y cities across Z countries"
   - "🗺️ Expand Map" button in top-right

2. **Modal View:**
   - Full-screen map with world view
   - 3 filter dropdowns (Country, Batch, Role)
   - Color-coded markers (red/teal/blue)
   - Clustered markers with numbers
   - Footer with live statistics

3. **Interactions:**
   - Click marker → popup with details
   - Click cluster → zoom in and separate
   - Apply filters → instant update
   - Reset → show all locations
   - Close (X or backdrop) → return to dashboard

---

## 🔗 Related Documentation

- **Full Guide**: See `GEOGRAPHIC_ANALYTICS.md` for complete documentation
- **Database Schema**: See `dev/user_locations.sql` for detailed schema
- **Analytics System**: See `ANALYTICS_ENHANCEMENTS.md` for overall analytics
- **Role Filtering**: See `ROLE_BASED_FILTERING.md` for role-based features

---

## ⚡ Quick Commands Cheat Sheet

```bash
# Deploy database
mysql -u root -p gradlink < dev/user_locations.sql

# Check installation
mysql -u root -p gradlink -e "SELECT COUNT(*) FROM user_locations;"

# View sample data
mysql -u root -p gradlink -e "SELECT * FROM v_user_locations LIMIT 5;"

# Test backend
curl http://localhost/GRADLINK/admin/dashboard/engagement

# Clear cache (if needed)
php -r "opcache_reset();"
```

---

**Next Steps:**
1. Deploy database schema ✅
2. Add sample data ✅  
3. Test in browser ✅
4. Add real user data 📍
5. Enable user self-service 🔜
6. Monitor performance 📊
