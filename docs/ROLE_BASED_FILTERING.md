# Role-Based Analytics Filtering

Comprehensive guide to the role-based filtering feature in the GRADLINK Analytics Dashboard.

## Overview

The analytics dashboard now supports **role-based filtering** to provide customized metrics for different user segments. Admins can view analytics segmented by:

- **All Users** - System-wide metrics (default view)
- **Admins** - Admin-only activity and engagement
- **Alumni** - Alumni-specific metrics and engagement
- **Students** - Undergraduate/student metrics and engagement

## Quick Access

### Filter Button Locations

The role filter appears as a set of colored buttons in the **"Filter by User Role"** section, just below the header on the analytics dashboard.

### Filter URL Structure

You can also access filtered views directly via URL:

```
/admin/engagement              # All users (default)
/admin/engagement?role=admin   # Admins only
/admin/engagement?role=alumni  # Alumni only
/admin/engagement?role=undergrad # Students only
```

### Visual Indicators

Each role button displays:
- **Icon**: Quick visual identifier (🔐 Admin, 🎓 Alumni, 📚 Students, 👥 All)
- **Label**: Role name (Admins, Alumni, Students, All Users)
- **Count**: Number of users in that role
- **Color Coding**: 
  - Admins: Red (#ff6b6b)
  - Alumni: Teal (#4ecdc4)
  - Students: Blue (#45b7d1)
  - All: Neutral (#3a3a3a)

## How Role Filtering Works

### Data Filtered by Role

When you select a role, **ALL metrics and charts** are recalculated to show only activity from that user segment:

#### Metrics Filtered
- ✅ Total Posts (from selected role only)
- ✅ Total Comments (from selected role only)
- ✅ Total Reactions (from selected role only)
- ✅ Messages Sent (from selected role only)
- ✅ Events Created (from selected role only)
- ✅ Event Attendances (by selected role only)
- ✅ Event Bookmarks (by selected role only)
- ✅ Followers (following from selected role)
- ✅ Active Users (in selected role)
- ✅ DAU/WAU/MAU (active from selected role)
- ✅ Engagement Rate (for selected role)
- ✅ All averaged metrics
- ✅ All time-series data
- ✅ Profile completion metrics (selected role only)

#### Charts Filtered
- ✅ Batch/Graduation Distribution (selected role only)
- ✅ Gender Distribution (selected role only)
- ✅ Skills Distribution (selected role only)
- ✅ Time Series Charts (signups, posts, messages, etc. from selected role)
- ✅ Event Pipeline (events created by selected role)
- ✅ Profile Completion (selected role only)

#### Data NOT Filtered
The following remain system-wide for context:
- ℹ️ Total users (system-wide count)
- ℹ️ User growth percentage (system-wide)
- ℹ️ Role distribution chart (always shows all roles)
- ℹ️ Event status distribution (all statuses)
- ℹ️ Event request status distribution (all statuses)

## Backend Implementation

### Model Changes (M_admin.php)

#### New Public Methods

```php
// Get engagement metrics for specific role
public function getEngagementMetricsByRole(?string $role = null): array

// Get chart data for specific role
public function getChartDataByRole(?string $role = null): array

// Count users by role (for filter display)
public function countUsersByRole(?string $role = null): int
```

#### New Private Helper Methods

```php
// Helper methods for role-based calculations
private function safeActiveUsersWindowForRole(array $userIds, int $days): int
private function getTimeSeriesBundleForRole(array $userIds): array
private function safeTimeSeriesForRole(string $idList, string $table, ...): array
private function getEventPipelineMetricsForRole(array $userIds): array
private function getProfileCompletionMetricsForRole(array $userIds): array
private function getEmptyEngagementMetrics(): array
private function getEmptyChartData(): array
```

#### Query Strategy

For role filtering:
1. Fetch all user IDs for selected role
2. Build IN() clause with user IDs
3. Filter all activity queries by user_id/sender_id/organizer_id IN (user_ids)
4. Apply same calculations as regular metrics

### Controller Changes (Admin.php)

The `engagement()` method now:
1. Reads `role` parameter from query string (`?role=admin`, etc.)
2. Validates role is one of: `admin`, `alumni`, `undergrad`, or null
3. Calls role-specific metric methods
4. Passes role filter and user counts to view
5. Returns role information for UI display

```php
public function engagement() {
    $roleFilter = $_GET['role'] ?? null;
    
    // Validate role
    if ($roleFilter && !in_array($roleFilter, ['admin', 'alumni', 'undergrad'])) {
        $roleFilter = null;
    }
    
    // Get filtered data
    $engagement = $this->adminModel->getEngagementMetricsByRole($roleFilter);
    $charts = $this->adminModel->getChartDataByRole($roleFilter);
    
    // Pass to view
    $data = [
        'roleFilter' => $roleFilter,
        'usersByRole' => [...],
        // ...
    ];
}
```

### View Changes (v_engagement.php)

The filter UI renders:
1. Role filter buttons (all, admin, alumni, undergrad)
2. User counts for each role
3. Active state indicator on current role
4. Informational message showing selected role

```html
<div class="role-filter">
    <a href="/admin/engagement" class="role-filter-btn active">
        <span class="filter-icon">👥</span>
        <span class="filter-label">All Users</span>
        <span class="filter-count">1,234</span>
    </a>
    <!-- More role buttons... -->
</div>
```

### CSS Styling (engagement.css)

New styles include:
- `.filters` - Container for filter section
- `.role-filter` - Flex layout for filter buttons
- `.role-filter-btn` - Individual filter button (normal state)
- `.role-filter-btn.active` - Active state styling
- `.filter-icon` - Role icon styling
- `.filter-label` - Role label text
- `.filter-count` - User count badge

Responsive breakpoints:
- **900px and below**: Reduced padding, smaller font
- **640px and below**: Hide labels, show icons only, full-width buttons

## Use Cases

### Use Case 1: Monitor Alumni Engagement

**Goal**: Track how active alumni are on the platform

**Steps**:
1. Click the 🎓 **Alumni** button on analytics dashboard
2. View alumni-specific metrics:
   - Alumni post creation rate
   - Alumni profile completion
   - Alumni follower relationships
   - Alumni event attendance
3. Compare to previous periods using date filters

**Key Insights**:
- Are alumni creating content?
- How engaged are alumni in events?
- What's the alumni profile completion rate?

### Use Case 2: Student vs Alumni Engagement Comparison

**Goal**: Compare student and alumni activity levels

**Steps**:
1. Note student metrics (click 📚 **Students** button)
2. Switch to alumni metrics (click 🎓 **Alumni** button)
3. Compare side-by-side:
   - Engagement rates
   - Activity windows (DAU/WAU/MAU)
   - Content creation (posts, comments)
   - Event participation

**Key Metrics to Compare**:
- Posts per user
- Comments per post
- Message volume
- Event attendance
- Profile completion %

### Use Case 3: Admin Activity Monitoring

**Goal**: Track system admin usage and activity

**Steps**:
1. Click the 🔐 **Admins** button
2. View admin-specific metrics:
   - Posts/content created by admins
   - Admin event creation
   - Admin profile completeness
3. Verify admins are active participants

### Use Case 4: System-Wide Health Check

**Goal**: Get complete platform health overview

**Steps**:
1. Start with 👥 **All Users** (default)
2. Review overall engagement metrics
3. Drill down by role to identify weak areas
4. Focus improvement efforts on low-engagement segments

## Metric Interpretation by Role

### When Viewing Admin Metrics

| Metric | Interpretation |
|--------|-----------------|
| Posts | Admin-created content (typically low) |
| Events | Admin-created system events |
| Profile Completion | Admin profile quality |
| Followers | Other users following admins |
| Engagement Rate | Admin participation in system activities |

### When Viewing Alumni Metrics

| Metric | Interpretation |
|--------|-----------------|
| Posts | Alumni user-generated content |
| Comments | Alumni engagement with content |
| Followers | Alumni network/connections |
| Events | Alumni-created events/activities |
| Profile Completion | Alumni profile quality for networking |
| Pending Alumni | Alumni signup requests (conversion funnel) |

### When Viewing Student Metrics

| Metric | Interpretation |
|--------|-----------------|
| Posts | Student engagement/participation |
| Comments | Student interactions |
| Event Attendance | Student interest in campus activities |
| Messages | Student-to-student/alumni communication |
| Followers | Student network building |
| Profile Completion | Student profile investment |

## Technical Details

### Database Queries

#### Query Pattern: User ID Filtering

All role-filtered queries follow this pattern:

```sql
-- Step 1: Get user IDs for role
SELECT id FROM users WHERE role = 'alumni'

-- Step 2: Filter by user ID
SELECT COUNT(*) FROM posts WHERE user_id IN (123, 456, 789...)
SELECT COUNT(*) FROM comments WHERE user_id IN (123, 456, 789...)
SELECT COUNT(*) FROM messages WHERE sender_id IN (123, 456, 789...)
```

### Performance Considerations

**Efficient Aspects**:
- ✅ User ID list built once, reused for all metrics
- ✅ IN() clause queries are indexed on user_id
- ✅ GROUP BY queries aggregated at database level
- ✅ Time-series data calculated efficiently

**Large Dataset Notes**:
- For thousands of users in a role, IN() clause might be large
- Consider pagination if response time exceeds 2 seconds
- Add database indexes on: `posts.user_id`, `comments.user_id`, `messages.sender_id`

### Fallback Behavior

If filtering finds 0 users in a role:
- All metrics return 0
- All charts return empty data
- Graceful degradation with no errors
- UI still displays, showing "no data"

## Common Questions

### Q: Do I need to manually select a role every time?

**A**: No, the URL persists the filter. Once you select a role, you can navigate to other sections and return - the role filter is maintained in the URL.

### Q: Can I export role-specific data?

**A**: Yes! The export buttons (Export Summary, Export Users, etc.) are aware of the role filter and will export only data for the selected role.

### Q: Are the user counts in the filter buttons real-time?

**A**: Yes, they are calculated on each page load. They reflect the current user distribution across roles.

### Q: Can I filter by multiple roles at once?

**A**: Currently no. You can view one role at a time, but you can quickly switch between roles for comparison.

### Q: What happens to archived data when filtering?

**A**: All historical data is included. Filtering by role looks at ALL activity from that role, across all time periods in the database.

### Q: How are metrics calculated for very old user data?

**A**: The same way as current data. Historical posts, comments, messages from users in the selected role are all included in time-series and aggregate metrics.

## Troubleshooting

### Filter Buttons Not Showing Counts

**Problem**: Filter buttons show 0 users for all roles

**Solutions**:
1. Verify users table has data: `SELECT COUNT(*) FROM users`
2. Check role column exists: `DESCRIBE users`
3. Clear browser cache (Ctrl+F5)
4. Check browser console for JavaScript errors (F12)

### Metrics Show Zero When Role Selected

**Problem**: Clicking a role filter shows 0 for all metrics

**Solutions**:
1. Verify users exist for that role: `SELECT COUNT(*) FROM users WHERE role = 'alumni'`
2. Verify activity exists from those users: `SELECT COUNT(*) FROM posts WHERE user_id IN (...)`
3. Check URL includes role parameter: `/admin/engagement?role=alumni`
4. Check PHP error logs for database errors

### Export Not Respecting Filter

**Problem**: Export includes all users, not just selected role

**Solutions**:
1. Verify role parameter in URL
2. Check controller passes `roleFilter` to export handler
3. Clear browser cache
4. Verify export methods check `$data['roleFilter']`

### Performance Slow with Many Users

**Problem**: Dashboard loads slowly when filtering by a role with many users

**Solutions**:
1. Add database indexes: `CREATE INDEX idx_posts_user_id ON posts(user_id)`
2. Consider pagination for large result sets
3. Archive old data (> 1 year) to separate table
4. Check database query performance with EXPLAIN

## Future Enhancements

Potential improvements for role-based filtering:

- [ ] Multi-role filtering (select multiple roles to compare)
- [ ] Role+batch filtering (alumni + 2019 batch, etc.)
- [ ] Custom role creation and filtering
- [ ] Role-based date range presets
- [ ] Role comparison reports
- [ ] Role trend analysis (how roles change over time)
- [ ] Role-specific anomaly detection
- [ ] Predictive models by role

## Related Documentation

- [ANALYTICS_ENHANCEMENTS.md](./ANALYTICS_ENHANCEMENTS.md) - Technical overview
- [KPI_METRIC_REFERENCE.md](./KPI_METRIC_REFERENCE.md) - All metrics explained
- [ANALYTICS_QUICK_REFERENCE.md](./ANALYTICS_QUICK_REFERENCE.md) - User guide

## Support

### For Admins

Review the role-specific metrics in your dashboard. Each role shows relevant engagement data for that user segment.

### For Developers

The role filtering system is extensible. To add new metrics:

1. Add calculation to `getEngagementMetricsByRole()` in M_admin.php
2. Filter by user IDs in the IN() clause
3. Return data with role context
4. Display in view with role awareness

### For Questions

Refer to [ANALYTICS_QUICK_REFERENCE.md](./ANALYTICS_QUICK_REFERENCE.md) for interpretation of metrics by role.

---

**Version**: 1.0 - Role-Based Filtering
**Status**: Active
**Last Updated**: February 5, 2026

