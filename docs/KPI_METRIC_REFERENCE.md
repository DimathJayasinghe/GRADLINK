# Analytics Dashboard - KPI & Metric Reference

## Dashboard Structure Overview

```
┌─────────────────────────────────────────────────────────────────┐
│                      Analytics Dashboard                         │
│  [Export Summary] [Export Users] [Export Content] [Export Events]│
└─────────────────────────────────────────────────────────────────┘

SECTION 1: USER ACTIVITY METRICS
┌─────────────┬──────────────┬──────────────┬──────────────┬─────────────┐
│ Total Users │ Active Users │ User Growth  │ Engagement   │ DAU/WAU/MAU │
│   (count)   │   (30d)      │  (3m %)      │  Rate (%)    │    (ratio)  │
└─────────────┴──────────────┴──────────────┴──────────────┴─────────────┘

SECTION 2: CONTENT ENGAGEMENT METRICS
┌────────────┬─────────────┬────────────┬──────────┬────────────┬──────────────┐
│   Posts    │   Comments  │ Reactions  │ Messages │Avg Posts/U │Avg Comments/ │
│  (count)   │   (count)   │  (count)   │ (count)  │   (ratio)  │   Post       │
└────────────┴─────────────┴────────────┴──────────┴────────────┴──────────────┘

SECTION 3: COMMUNITY METRICS
┌────────────┬──────────────┬────────────┬──────────┬──────────────┬──────────────┐
│   Events   │Event Bookmark│ Followers  │ Pending  │Unread Notif  │              │
│  (count)   │    (count)   │   (count)  │ (count)  │   (count)    │    (count)   │
└────────────┴──────────────┴────────────┴──────────┴──────────────┴──────────────┘

SECTION 4: PROFILE METRICS
┌────────────────┬──────────────┬───────────────┬──────────────┐
│ Profile Compl. │ Private Prof │Completed Prof │ Total Profil │
│   Rate (%)     │   (count)    │    (count)    │    (count)   │
└────────────────┴──────────────┴───────────────┴──────────────┘

SECTION 5: DISTRIBUTION CHARTS (3 Charts)
┌──────────────────────┬──────────────────────┬──────────────────────┐
│  User by Batch       │  Distribution by     │  Distribution by     │
│  (Bar Chart)         │  Role                │  Gender              │
│  - Shows graduation  │  (Doughnut Chart)    │  (Doughnut Chart)    │
│    year breakdown    │  - Admin/Alumni/     │  - Male/Female/      │
│                      │    Undergrad         │    Unspecified       │
└──────────────────────┴──────────────────────┴──────────────────────┘

SECTION 6: GEOGRAPHIC & TEMPORAL DATA (2 Charts)
┌──────────────────────┬──────────────────────────────────────┐
│  Alumni Locations    │  Active Users Over Time              │
│  (Map with Markers)  │  (Line Chart)                        │
│  - Shows by count    │  - Monthly active user trend         │
│  - Interactive       │  - Identifies growth patterns        │
└──────────────────────┴──────────────────────────────────────┘

SECTION 7: TIME SERIES TRENDS (3 Charts)
┌──────────────────────┬──────────────────────┬──────────────────────┐
│  Signups Over Time   │  Content Volume Over │  Messaging Over Time │
│  (Line Chart)        │  Time                │  (Bar Chart)         │
│  - New registrations │  (Multi-series Line) │  - Monthly messages  │
│  - Shows growth rate │  - Posts/Comments/   │  - Communication     │
│                      │    Reactions mixed   │    trends            │
└──────────────────────┴──────────────────────┴──────────────────────┘

SECTION 8: ENGAGEMENT ANALYSIS (3 Charts)
┌──────────────────────┬──────────────────────┬──────────────────────┐
│  Engagement Mix      │  Event Pipeline      │  Top Skills          │
│  (Doughnut Chart)    │  (Bar Chart)         │  (Bar Chart)         │
│  - Posts vs Comments │  - Request states    │  - Most listed       │
│    vs Reactions vs   │  - Event statuses    │    skills by users   │
│    Messages          │  - Funnel view       │  - Top 8 skills      │
└──────────────────────┴──────────────────────┴──────────────────────┘
```

## Detailed KPI Specifications

### SECTION 1: USER ACTIVITY METRICS

#### KPI 1: Total Users
- **Display**: Large number
- **Data Type**: Integer count
- **Source**: COUNT(*) FROM users
- **Trending**: Shows cumulative growth
- **Update**: Real-time
- **Target**: Growing trend

#### KPI 2: Active Users (Last 30 Days)
- **Display**: Large number with label
- **Data Type**: Integer count
- **Source**: UNION of last 30 days activity
- **Trending**: Cycle-based (monthly)
- **Update**: Real-time
- **Target**: > 50% of total users

#### KPI 3: User Growth (Last 3 Months)
- **Display**: Percentage with + sign
- **Data Type**: Percentage (0-100)
- **Source**: (recent - previous) / previous * 100
- **Trending**: Month-over-month
- **Update**: Monthly
- **Target**: > 10% growth

#### KPI 4: Engagement Rate
- **Display**: Percentage
- **Data Type**: Float (0-100)
- **Formula**: (Active 30d / Total) * 100
- **Trending**: Weekly
- **Update**: Real-time
- **Target**: > 50% healthy, > 40% acceptable

#### KPI 5: DAU / WAU / MAU
- **Display**: Three numbers separated by slashes
- **Data Type**: Integer / Integer / Integer
- **DAU**: Distinct users active in 24h
- **WAU**: Distinct users active in 7d
- **MAU**: Distinct users active in 30d
- **Target**: DAU/MAU ratio > 0.3 is healthy

---

### SECTION 2: CONTENT ENGAGEMENT METRICS

#### KPI 6: Total Posts
- **Display**: Large number
- **Data Type**: Integer count
- **Source**: COUNT(*) FROM posts
- **Trending**: Cumulative growth
- **Update**: Real-time
- **Target**: Growing trend

#### KPI 7: Total Comments
- **Display**: Large number
- **Data Type**: Integer count
- **Source**: COUNT(*) FROM comments
- **Trending**: Engagement metric
- **Update**: Real-time
- **Target**: Growing faster than posts

#### KPI 8: Total Reactions
- **Display**: Large number
- **Data Type**: Integer count
- **Source**: COUNT(*) FROM post_likes
- **Trending**: Engagement metric
- **Update**: Real-time
- **Target**: Highest volume metric

#### KPI 9: Messages
- **Display**: Large number
- **Data Type**: Integer count
- **Source**: COUNT(*) FROM messages
- **Trending**: Communication metric
- **Update**: Real-time
- **Target**: Active community indicator

#### KPI 10: Avg Posts/User
- **Display**: Decimal number
- **Data Type**: Float (2 decimals)
- **Formula**: Total Posts / Total Users
- **Trending**: Content creation rate
- **Update**: Real-time
- **Target**: > 2 posts per user is good

#### KPI 11: Avg Comments/Post
- **Display**: Decimal number
- **Data Type**: Float (2 decimals)
- **Formula**: Total Comments / Total Posts
- **Trending**: Discussion health
- **Update**: Real-time
- **Target**: > 1.5 comments per post is good

---

### SECTION 3: COMMUNITY METRICS

#### KPI 12: Events
- **Display**: Large number
- **Data Type**: Integer count
- **Source**: COUNT(*) FROM events
- **Trending**: Event creation rate
- **Update**: Real-time
- **Target**: Consistent monthly growth

#### KPI 13: Event Bookmarks
- **Display**: Large number
- **Data Type**: Integer count
- **Source**: COUNT(*) FROM event_bookmarks
- **Trending**: Interest indicator
- **Update**: Real-time
- **Target**: High save ratio

#### KPI 14: Followers
- **Display**: Large number
- **Data Type**: Integer count
- **Source**: COUNT(*) FROM followers
- **Trending**: Social network growth
- **Update**: Real-time
- **Target**: Distributed follow graph

#### KPI 15: Pending Alumni
- **Display**: Large number
- **Data Type**: Integer count
- **Source**: COUNT(*) FROM unregisted_alumni WHERE status='pending'
- **Trending**: Verification queue
- **Update**: Real-time
- **Target**: < 20 pending (< 1 week old)

#### KPI 16: Unread Notifications
- **Display**: Large number
- **Data Type**: Integer count
- **Source**: COUNT(*) FROM notifications WHERE is_read=0
- **Trending**: Engagement health
- **Update**: Real-time
- **Target**: < 100 unread

---

### SECTION 4: PROFILE METRICS

#### KPI 18: Profile Completion Rate
- **Display**: Percentage
- **Data Type**: Float (0-100)
- **Definition**: Users with bio OR skills OR custom image OR display name
- **Formula**: (Completed / Total) * 100
- **Update**: Real-time
- **Target**: > 70% is healthy

#### KPI 19: Private Profiles
- **Display**: Large number
- **Data Type**: Integer count
- **Source**: COUNT(*) FROM user_profiles_visibility WHERE is_public=0
- **Update**: Real-time
- **Target**: < 20% of total users

#### KPI 20: Completed Profiles
- **Display**: Large number
- **Data Type**: Integer count
- **Formula**: Count of users with profile data
- **Update**: Real-time
- **Target**: Growing trend

#### KPI 21: Total Profiles
- **Display**: Large number
- **Data Type**: Integer count
- **Source**: COUNT(*) FROM users
- **Update**: Real-time
- **Target**: Same as total users

---

## Chart Specifications

### CHART TYPE 1: Distribution Charts (3 charts)

#### Batch Distribution (Bar Chart)
- **X-Axis**: Batch numbers/graduation years
- **Y-Axis**: User count
- **Color**: #93c5fd (light blue)
- **Interaction**: Hover shows count
- **Data Source**: GROUP BY batch_no

#### Role Distribution (Doughnut Chart)
- **Categories**: Admin, Alumni, Undergrad
- **Colors**: [#60a5fa, #34d399, #fbbf24, #f87171, #a78bfa]
- **Legend**: Bottom positioned
- **Data Source**: GROUP BY role

#### Gender Distribution (Doughnut Chart)
- **Categories**: Male, Female, Unspecified
- **Colors**: [#f472b6, #60a5fa, #a3a3a3]
- **Legend**: Bottom positioned
- **Data Source**: GROUP BY gender

### CHART TYPE 2: Temporal Charts (Time Series)

#### Signups Over Time (Line Chart)
- **X-Axis**: Month (YYYY-MM format)
- **Y-Axis**: New user count
- **Color**: #60a5fa
- **Style**: Line, no fill
- **Data Source**: GROUP BY month of created_at

#### Content Volume Over Time (Multi-series Line)
- **Series 1 - Posts**: #93c5fd
- **Series 2 - Comments**: #fbbf24
- **Series 3 - Reactions**: #f87171
- **X-Axis**: Month
- **Y-Axis**: Count
- **Data Source**: Three separate GROUP BY queries

#### Messaging Over Time (Bar Chart)
- **X-Axis**: Month
- **Y-Axis**: Message count
- **Color**: #a78bfa
- **Data Source**: GROUP BY month of message_time

#### Active Users Over Time (Line Chart)
- **X-Axis**: Month
- **Y-Axis**: Active user count
- **Color**: #4ade80
- **Style**: Line, no fill
- **Data Source**: UNION of activity across all features

### CHART TYPE 3: Engagement Charts (Analysis)

#### Engagement Mix (Doughnut)
- **Series**: Posts, Comments, Reactions, Messages
- **Colors**: [#60a5fa, #fbbf24, #f87171, #a78bfa]
- **Shows**: Relative volume of each engagement type
- **Data Source**: Engagement metrics array

#### Event Pipeline (Horizontal Bar)
- **Categories**: 
  - Requests: Pending, Approved, Rejected
  - Events: Draft, Published, Cancelled
- **Color**: #34d399
- **Data Source**: GROUP BY status from two tables

#### Top Skills (Bar Chart)
- **X-Axis**: Skill names (top 8)
- **Y-Axis**: User count
- **Color**: #f59e0b
- **Sorting**: Descending by count
- **Data Source**: JSON parse from skills column

---

## Responsive Design Specifications

### Desktop (1400px+)
- Grid: 3 columns (KPIs flex, Charts auto-fit 3)
- Header: Flex row, actions on right
- Button size: 0.6rem padding
- Font sizes: Full scale

### Tablet (1200px)
- Grid: 2-3 columns (adaptive)
- Header: Stacked (text above actions)
- Chart grid: 2 columns
- Font sizes: Slightly reduced

### Large Mobile (900px)
- Grid: 1 column
- All charts: Full width
- KPI: Flexible min-width
- Header: Stacked, vertical
- Buttons: Full width

### Small Mobile (640px)
- Grid: 1 column
- KPI cards: Compact layout
- Font sizes: Reduced for readability
- Buttons: Stacked vertically
- Header: Minimal spacing

---

## Color Scheme Reference

### Primary Colors
- Chart 1 (Posts/Primary): #60a5fa (Light Blue)
- Chart 2 (Comments): #fbbf24 (Amber)
- Chart 3 (Reactions): #f87171 (Red)
- Chart 4 (Messages): #a78bfa (Purple)
- Chart 5 (Growth): #34d399 (Green)
- Chart 6 (Events): #34d399 (Green)
- Chart 7 (Skills): #f59e0b (Orange)

### Gender Specific
- Male: #60a5fa (Blue)
- Female: #f472b6 (Pink)
- Unspecified: #a3a3a3 (Gray)

### Button Colors
- Primary: #2563eb (Blue)
- Secondary: #4b5563 (Gray)
- Danger: #dc3545 (Red)
- Success: #34d399 (Green)

---

## Update Frequency & Data Freshness

| Metric | Real-time | Daily | Weekly | Monthly |
|--------|-----------|-------|--------|---------|
| Total Users | ✓ | | | |
| Active Users | ✓ | | | |
| Posts/Comments | ✓ | | | |
| Growth Rate | | | | ✓ |
| Engagement Rate | ✓ | | | |
| Event Data | ✓ | | | |
| Time Series | ✓ | | | |
| Profile Metrics | ✓ | | | |

---

## Export Format Specifications

### CSV Column Order

#### Analytics Summary Export
1. Metric
2. Value

#### Users Export
1. id
2. name
3. email
4. role
5. batch

#### Content Export
1. id
2. title
3. type
4. status
5. date

#### Events Export
1. id
2. title
3. status
4. start_datetime
5. venue

---

**Version**: 2.0
**Last Updated**: February 2026
**Status**: Active Dashboard
