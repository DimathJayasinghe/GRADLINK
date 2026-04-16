# Analytics Dashboard - Quick Reference Guide

## Dashboard URL
`/admin/engagement`

## Available Metrics at a Glance

### User Metrics
| Metric | What It Shows | Good Target |
|--------|---------------|------------|
| Total Users | All registered users | Growing |
| Active Users (30d) | Users active in past month | > 50% |
| User Growth (3m) | Growth percentage | > 10% |
| DAU / WAU / MAU | Daily/Weekly/Monthly active | DAU > 30% of MAU |
| Engagement Rate | % of users active in 30d | > 50% |

### Content Metrics
| Metric | What It Shows | Good Target |
|--------|---------------|------------|
| Total Posts | All content created | Growing |
| Total Comments | Discussions on posts | Growing |
| Total Reactions | Likes/reactions count | Growing |
| Messages | Private communications | Growing |
| Avg Posts/User | Content per user | > 2 |
| Avg Comments/Post | Discussion level | > 1.5 |

### Community Metrics
| Metric | What It Shows | Good Target |
|--------|---------------|------------|
| Events | Total events | Growing |
| Event Bookmarks | Saved events | Growing |
| Followers | Follow relationships | Growing |
| Pending Alumni | Verification queue | < 20 |
| Unread Notifications | Unactioned notifications | < 50 |

### Profile Metrics
| Metric | What It Shows | Good Target |
|--------|---------------|------------|
| Profile Completion | % with complete profiles | > 70% |
| Completed Profiles | Actual count | Growing |
| Private Profiles | Users with hidden profiles | < 20% |

## Charts Explained

### Distribution Charts
- **Batch Distribution**: See which graduation years are most represented
- **Role Distribution**: Alumni vs Undergrad vs Admin breakdown
- **Gender Distribution**: Demographic diversity

### Trend Charts
- **Signups**: New user registration velocity
- **Content Volume**: Post/Comment/Reaction activity over time
- **Messaging**: Direct message usage trends
- **Active Users**: Monthly engagement consistency

### Analysis Charts
- **Engagement Mix**: Shows which features users engage with most
- **Event Pipeline**: Event request approval process health
- **Top Skills**: Most commonly listed user skills

## How to Use Export Buttons

### Export Summary
**Best for**: Executive reports, quick snapshots
- Downloads: Core metrics in CSV
- Use case: Presentations, quarterly reviews

### Export Users
**Best for**: User analysis
- Downloads: User list with role and batch
- Use case: User research, segmentation

### Export Content
**Best for**: Content analysis
- Downloads: Post/comment data
- Use case: Content strategy review

### Export Events
**Best for**: Event management
- Downloads: Event data with status
- Use case: Event planning, capacity analysis

## Interpretation Guidelines

### Engagement Health Indicators

#### Excellent (✓)
- Engagement Rate > 60%
- DAU/MAU ratio > 0.35
- Avg Comments/Post > 2.5
- Profile Completion > 80%
- Monthly growth > 15%

#### Good (✓)
- Engagement Rate 40-60%
- DAU/MAU ratio 0.20-0.35
- Avg Comments/Post 1.5-2.5
- Profile Completion 60-80%
- Monthly growth 5-15%

#### Needs Attention (!)
- Engagement Rate < 40%
- DAU/MAU ratio < 0.20
- Avg Comments/Post < 1.5
- Profile Completion < 60%
- Monthly growth < 5%

#### Critical (‼)
- Engagement Rate < 20%
- DAU/MAU ratio < 0.10
- Avg Comments/Post < 0.5
- Profile Completion < 40%
- Negative growth

### Content Health Indicators

#### Posts & Comments
- **Growing both**: Healthy ecosystem
- **Posts up, Comments flat**: Content not resonating
- **Comments growing faster**: Viral engagement
- **All declining**: User disengagement

### Activity Pattern Analysis
- **DAU declining**: Check for maintenance, issues
- **WAU stable but DAU low**: Passive users
- **MAU > WAU**: Sporadic user behavior
- **Even distribution DAU/WAU/MAU**: Healthy consistency

## Filtering Tips

### Date Range Filters
- Use to isolate specific periods
- Compare before/after changes
- Track seasonal patterns

### User Type Filters
- Alumni: Check alumni-specific engagement
- Undergrad: Monitor student activity
- All: Comprehensive view

## Common Questions

**Q: Why is Engagement Rate high but posts low?**
A: Users may be active via other features (messages, follows, reactions). Doesn't necessarily mean low content creation.

**Q: What if a chart shows no data?**
A: Either no data exists in that category, or there's a database issue. Check server logs.

**Q: How often does data update?**
A: Dashboard reflects current database state. Refresh page for latest data.

**Q: Can I export full user data?**
A: Current export is limited. Contact admin for full database exports.

**Q: Why might metrics differ from expectations?**
A: Check date ranges, verify data quality in source tables, confirm table relationships.

## Performance Recommendations

### When Metrics Are Declining
1. Review recent platform changes
2. Check for technical issues
3. Analyze unread notification buildup
4. Review pending alumni approvals
5. Check for spammers in pending content

### When Engagement Rate is Low
1. Identify inactive user cohorts
2. Send re-engagement campaigns
3. Review alumni visibility settings
4. Promote trending content
5. Host engagement events

### When Profile Completion is Low
1. Send profile setup reminders
2. Simplify profile requirements
3. Highlight profile benefits
4. Create profile badges/rewards
5. Add onboarding flow

## Best Practices

### Weekly Review
- [ ] Check overall engagement trend
- [ ] Review pending items (alumni, notifications)
- [ ] Spot any sudden drops
- [ ] Note any significant growth

### Monthly Review
- [ ] Compare metrics month-over-month
- [ ] Analyze trend charts
- [ ] Review event pipeline health
- [ ] Export report for records
- [ ] Identify top content

### Quarterly Review
- [ ] Deep-dive into cohort analysis
- [ ] Review feature adoption
- [ ] Assess platform health
- [ ] Plan improvements
- [ ] Set targets for next quarter

## Keyboard Shortcuts

| Action | Shortcut |
|--------|----------|
| Refresh page | F5 or Ctrl+R |
| Export results | Click export button |
| Focus filter | Tab to filters |
| Apply filter | Enter key |
| Select all text | Ctrl+A |
| Copy text | Ctrl+C |

## API Reference

### Getting Engagement Data (PHP)
```php
$adminModel = $this->model('M_admin');
$engagement = $adminModel->getEngagementMetrics();
$charts = $adminModel->getChartData();
```

### Engagement Data Structure
```php
[
    'posts' => int,
    'comments' => int,
    'reactions' => int,
    'messages' => int,
    'events' => int,
    'engagement_rate' => float,
    'dau' => int,
    'wau' => int,
    'mau' => int,
    'avg_posts_per_user' => float,
    'profile_metrics' => [...],
    'time_series' => [...],
    'event_pipeline' => [...]
]
```

## Troubleshooting

### Charts Not Displaying
1. Refresh page (Ctrl+F5)
2. Check browser console (F12)
3. Verify database tables exist
4. Check for JavaScript errors
5. Try different browser

### Export Not Working
1. Check browser download settings
2. Disable pop-up blockers
3. Check disk space
4. Try different file format
5. Contact admin

### Metrics Seem Wrong
1. Cross-check database directly
2. Verify table relationships
3. Check for data corruption
4. Review query logic
5. Verify date filters

### Page Loading Slowly
1. Check internet connection
2. Close other browser tabs
3. Clear browser cache
4. Disable browser extensions
5. Contact admin for query optimization

## Resources

### Documentation
- [Full Analytics Enhancements](./ANALYTICS_ENHANCEMENTS.md)
- [Implementation Details](./IMPLEMENTATION_DETAILS.md)

### Related Pages
- [Admin Dashboard](./admin)
- [User Management](./admin/users)
- [Event Moderation](./admin/eventrequests)
- [Content Management](./admin/posts)

---

**Last Updated**: February 2026
**Dashboard Version**: 2.0
**Maintained By**: Admin Team
