# Analytics Dashboard Enhancements

## Overview
The GRADLINK Admin Analytics Dashboard has been significantly expanded to provide comprehensive insights into system usage, user engagement, content metrics, community activity, and platform health.

## What's New

### 1. **Expanded Metrics & KPIs**

#### User Activity Metrics
- **Total Users**: Overall platform users
- **Active Users (Last 30 Days)**: 30-day active user count
- **User Growth (Last 3 Months)**: Growth percentage
- **DAU / WAU / MAU**: Daily / Weekly / Monthly Active Users
- **Engagement Rate**: Percentage of active users relative to total users
- **Profile Completion Rate**: Percentage of profiles with complete information

#### Content & Engagement Metrics
- **Total Posts**: All posts created
- **Total Comments**: All comments on posts
- **Total Reactions**: All post likes/reactions
- **Messages**: Total direct messages
- **Avg Posts/User**: Average posts per user
- **Avg Comments/Post**: Average comments per post
- **Avg Reactions/Post**: Average reactions per post

#### Community Metrics
- **Followers**: Total follow relationships
- **Events**: Total events created
- **Event Attendees**: Total event attendance records
- **Event Bookmarks**: Total bookmarked events
- **Pending Alumni**: Unverified alumni waiting approval
- **Unread Notifications**: System notifications pending user read

### 2. **New Distribution Charts**

#### User Demographics
- **User Distribution by Graduation/Batch**: Bar chart showing users per batch
- **Distribution by Role**: Doughnut chart (Admin, Alumni, Undergrad)
- **Distribution by Gender**: Doughnut chart (Male, Female, Unspecified)

#### Event Pipeline
- **Event Requests Pipeline**: Status breakdown (Pending, Approved, Rejected)
- **Events Status**: Status breakdown (Draft, Published, Cancelled)
- **Event Attendee Distribution**: Shows event attendance trends

### 3. **Time Series Analytics**

#### Temporal Trend Analysis
- **Signups Over Time**: Monthly user registration trends
- **Content Volume Over Time**: Posts, Comments, and Reactions monthly trends
- **Messaging Over Time**: Monthly messaging activity
- **Active Users Over Time**: Monthly active user counts

#### Engagement Mix Visualization
- **Engagement Mix Chart**: Doughnut showing distribution across Posts, Comments, Reactions, and Messages
- **Top Skills Chart**: Bar chart of most commonly listed skills

### 4. **Export Capabilities**

Multiple export options for reports:
- **Export Summary**: Core metrics snapshot (CSV)
- **Export Users**: User list and details (CSV)
- **Export Content**: Post and content statistics (CSV)
- **Export Events**: Event data and status (CSV)

## Technical Implementation

### Backend Changes (M_admin.php)

#### New Methods

```php
// Safe database queries with fallbacks
private function safeCount(string $sql): int
private function safeActiveUsersWindow(int $days): int
private function safeActiveUsersOverTime(): array

// Time series data generation
private function getTimeSeriesBundle(): array
private function safeTimeSeries(string $table, string $dateColumn): array

// Pipeline and profile metrics
private function getEventPipelineMetrics(): array
private function getProfileCompletionMetrics(): array
```

#### Enhanced getEngagementMetrics()
Returns comprehensive engagement data including:
- Raw counts (posts, comments, reactions, messages, events, etc.)
- Calculated metrics (averages, rates, percentages)
- Activity windows (DAU, WAU, MAU)
- Time series data for trends
- Event pipeline status breakdown
- Profile completion metrics

#### Enhanced getChartData()
Now provides:
- Role distribution
- Batch/graduation year distribution
- Gender distribution
- Event status distribution
- Event request status distribution

### Frontend Changes (v_engagement.php)

#### New UI Sections
1. **Analytics Header with Actions**
   - Dashboard title and description
   - Export buttons for quick data extraction

2. **Enhanced KPI Sections**
   - User activity KPIs (5 cards)
   - Content engagement KPIs (6 cards)
   - Community metrics KPIs (6 cards)
   - Profile metrics KPIs (4 cards)

3. **Chart Sections**
   - **Section 1**: User demographics (3 charts)
   - **Section 2**: Geographic/temporal data (2 charts)
   - **Section 3**: Time series trends (3 charts)
   - **Section 4**: Engagement analysis (3 charts)

#### JavaScript Enhancements
- Dynamic chart generation from server data
- Safe data access with optional chaining
- Responsive chart containers
- Multi-series trend lines
- Export-to-CSV functionality

### Styling Improvements (engagement.css)

#### Responsive Grids
- **grid-3**: Auto-fit 3-column layout for desktop
- **grid-2**: Auto-fit 2-column layout
- Mobile-first responsive breakpoints
- Flexible KPI cards with growth

#### Header Layout
- Flexbox header with text and action buttons
- Responsive action button stack on mobile
- Improved visual hierarchy

#### Chart Containers
- Dynamic height calculation
- Proper canvas sizing
- Mobile-optimized dimensions

#### Responsive Breakpoints
- **1400px**: 3-column optimal layout
- **1200px**: Stack header, adjusted grid
- **900px**: Single column charts
- **640px**: Compact mobile view

## Data Collection & Queries

### Activity Windows
The dashboard calculates user activity across multiple windows:
- **DAU (Daily)**: Active in last 24 hours
- **WAU (Weekly)**: Active in last 7 days
- **MAU (Monthly)**: Active in last 30 days
- **Active 30**: Alternate 30-day metric

### Activity Detection
Users counted as "active" if they have created:
- Posts
- Comments
- Reactions (likes)
- Messages
- Events

### Engagement Rates
Calculated as: (Active Users 30d / Total Users) * 100

### Profile Completion
Profiles considered complete if they have:
- Non-null/non-empty bio, OR
- Non-null/non-empty skills, OR
- Non-default profile image, OR
- Non-null/non-empty display name

## Database Requirements

Ensure the following tables exist:
- `users` (core user data)
- `posts` (content)
- `comments` (post comments)
- `post_likes` (reactions)
- `messages` (direct messaging)
- `events` (event management)
- `event_attendees` (attendance tracking)
- `event_bookmarks` (saved events)
- `followers` (follow relationships)
- `notifications` (system notifications)
- `unregisted_alumni` (pending alumni)
- `user_profiles_visibility` (profile privacy settings)
- `event_requests` (event moderation queue)

## Usage

### Accessing the Dashboard
Navigate to: `/admin/engagement`

### Filtering & Exports
1. Use filter options to narrow date ranges or user types
2. Click export buttons to download CSV reports
3. All data updates dynamically when filters are applied

### Interpreting Metrics
- **Engagement Rate > 50%**: Healthy, active user base
- **DAU/MAU Ratio > 0.3**: High daily engagement
- **Avg Comments/Post > 2**: Strong discussion activity
- **Profile Completion > 70%**: Good profile adoption
- **Event Attendance Rate**: Shows community event interest

## Performance Considerations

### Optimizations Implemented
1. **Safe Fallbacks**: All queries wrapped with exception handling
2. **Lazy Evaluation**: Charts only render if data exists
3. **Optional Chaining**: Safe property access in JavaScript
4. **Responsive Images**: Charts scale to container size
5. **Batch Queries**: Combined queries for active user calculations

### Query Performance Tips
- Time-series queries are limited to recent months
- Activity windows use date filtering
- Aggregations are performed at database level
- Consider adding indexes on timestamp columns for large datasets

## Future Enhancements

Potential additions to consider:
- Real-time dashboard updates
- Cohort analysis (user lifecycle)
- Churn prediction
- Feature adoption tracking
- Revenue/monetization metrics
- User segment analysis
- Custom date range selection
- Scheduled report email delivery
- Data drill-down capabilities
- Comparative period analysis

## Troubleshooting

### Missing Charts
- Verify database tables exist
- Check for PHP errors in browser console
- Ensure data exists in respective tables
- Validate JSON encoding in server responses

### Incorrect Metrics
- Verify timestamp columns have correct data types
- Check table relationships and foreign keys
- Review safe count logic in M_admin.php
- Test SQL queries directly in database client

### Export Issues
- Ensure browser allows file downloads
- Check for special characters in data
- Verify CSV encoding settings
- Test with different data sizes

## Files Modified

### Backend
- `app/models/M_admin.php` - Enhanced metrics and query methods
- `app/controllers/Admin.php` - Updated engagement() method

### Frontend
- `app/views/admin/v_engagement.php` - New UI, KPIs, charts
- `public/css/admin/engagement.css` - Responsive styling

## Code Examples

### Accessing Engagement Data in Controller
```php
$engagement = $this->adminModel->getEngagementMetrics();
$charts = $this->adminModel->getChartData();
$data = [
    'engagement' => $engagement,
    'charts' => $charts,
    'metrics' => $this->adminModel->getOverviewMetrics(),
];
```

### Using Chart.js with Server Data
```javascript
const labels = engagement.active_over_time.map(r => r.ym);
const values = engagement.active_over_time.map(r => parseInt(r.c||0));
new Chart(ctx, {
    type: 'line',
    data: { labels, datasets: [{ data: values }] },
    options: { responsive: true }
});
```

## Best Practices

1. **Regular Monitoring**: Check dashboard at least weekly
2. **Baseline Tracking**: Note metrics periodically for comparison
3. **Action Items**: Use insights to inform platform decisions
4. **Data Validation**: Cross-check metrics with source tables
5. **Archival**: Export reports regularly for historical analysis

---

**Last Updated**: February 2026
**Version**: 2.0 - Enhanced Analytics
