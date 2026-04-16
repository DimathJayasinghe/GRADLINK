# Role-Based Analytics Filtering - Quick Reference

## 🚀 Getting Started

### Access the Filter
1. Go to **Admin Dashboard** → **Analytics**
2. Look for **"Filter by User Role"** section below the header
3. Click any role button to filter

### Available Roles
| Button | Filter | Users | Emoji |
|--------|--------|-------|-------|
| **All Users** | None | System-wide | 👥 |
| **Admins** | ?role=admin | Administrators | 🔐 |
| **Alumni** | ?role=alumni | Graduated alumni | 🎓 |
| **Students** | ?role=undergrad | Undergraduate students | 📚 |

## 📊 What Changes When You Filter

### ✅ Metrics That Update by Role
- Posts created
- Comments made
- Reactions received
- Messages sent
- Events created
- Followers gained
- Active users (DAU/WAU/MAU)
- Engagement rate
- All averages
- Profile completion

### ℹ️ Metrics That Stay System-Wide
- Total users count
- User growth %
- Role distribution chart

## 🎯 Common Tasks

### Task: View Alumni Engagement
```
1. Click 🎓 Alumni button
2. See only alumni-created content
3. Compare alumni posts vs. students
4. Check alumni profile completion
```

### Task: Monitor Admin Activity
```
1. Click 🔐 Admins button
2. Review admin-created content
3. Check if admins are active users
4. Export admin metrics
```

### Task: Student Engagement Analysis
```
1. Click 📚 Students button
2. View student post/comment rates
3. Check event activity
4. Review student network growth
```

### Task: Compare Two Roles
```
1. Open dashboard with role A filter
2. Note key metrics
3. Switch to role B filter
4. Compare side-by-side metrics
```

## 🔗 Direct URLs

### Quick Access Links
```
/admin/engagement              # All users
/admin/engagement?role=admin   # Admins only
/admin/engagement?role=alumni  # Alumni only
/admin/engagement?role=undergrad # Students only
```

### Bookmark These
- Save filtered views as bookmarks for quick access
- Share URLs with team members
- Build custom reports for each role

## 📱 Mobile Usage

### On Small Screens
- Role buttons show **icons only** (🔐 🎓 📚 👥)
- User counts still visible
- Full-width buttons for easy tapping
- Same filtering functionality

### Responsive Breakdown
| Screen | Layout | Labels |
|--------|--------|--------|
| Desktop (1200px+) | Inline buttons | Labels + counts visible |
| Tablet (640-900px) | Stacked buttons | Labels hidden, counts visible |
| Mobile (<640px) | Full-width stacked | Icons only, counts visible |

## 📈 Interpreting Metrics by Role

### For Alumni Filter
- **High posts/comments** = Strong alumni engagement
- **High followers** = Alumni network building
- **High event activity** = Alumni interested in events
- **High profile completion** = Serious alumni profiles

### For Student Filter
- **High posts/comments** = Active student community
- **High messages** = Good peer communication
- **High event activity** = Event popularity with students
- **Growing numbers** = Student acquisition working

### For Admin Filter
- **Any posts/events** = Admins creating content
- **Followers** = Admin visibility/influence
- **Activity** = Admins engaging with platform

## 🎨 Visual Indicators

### Button Colors
- 🔐 **Admins**: Red - Alert/Authority
- 🎓 **Alumni**: Teal - Professional/Network
- 📚 **Students**: Blue - Learning/Academic
- 👥 **All**: Gray - Neutral/Total

### Active State
- Selected role has **full color background**
- Unselected roles have **outline only**
- User count shows **population size**

## ⚡ Tips & Tricks

### Tip 1: Monitor Engagement Gaps
```
View alumni metrics → note low engagement
View student metrics → compare numbers
Identify where to focus efforts
```

### Tip 2: Track Demographic Trends
```
Filter by role → view demographic charts
See batch/gender distribution by role
Understand role-specific demographics
```

### Tip 3: Export Role Reports
```
Select role filter
Click "Export Summary"
Save role-specific CSV
Share with stakeholders
```

### Tip 4: Spot Outliers
```
View all users metrics
Switch to individual role
Significant difference = opportunity
```

### Tip 5: Time-Based Analysis
```
Filter by role
View time-series charts
See role activity patterns
Plan around peak times
```

## 🔍 Troubleshooting

| Problem | Solution |
|---------|----------|
| Filter shows 0 users | Role might have no users in system |
| Metrics showing old data | Refresh page (Ctrl+F5) |
| Charts not displaying | Clear browser cache |
| Export not filtering | Check URL has role parameter |
| Mobile buttons unresponsive | Try different browser |

## 📊 Key Metrics by Role

### Alumni Metrics to Watch
```
✓ Profile Completion > 70%
✓ Monthly Active Users > 30%
✓ Posts per user > 2
✓ Followers growing
```

### Student Metrics to Watch
```
✓ Engagement Rate > 50%
✓ Event activity growing
✓ Comment volume high
✓ New user signups
```

### System Health (All Users)
```
✓ Engagement Rate > 50%
✓ DAU/WAU ratio > 0.3
✓ Profile Completion > 70%
✓ Month-over-month growth
```

## 📚 Related Documentation

- **Full Guide**: [ROLE_BASED_FILTERING.md](./ROLE_BASED_FILTERING.md)
- **Analytics Help**: [ANALYTICS_QUICK_REFERENCE.md](./ANALYTICS_QUICK_REFERENCE.md)
- **All Metrics**: [KPI_METRIC_REFERENCE.md](./KPI_METRIC_REFERENCE.md)
- **Technical Details**: [ROLE_FILTERING_IMPLEMENTATION.md](./ROLE_FILTERING_IMPLEMENTATION.md)

## 🎓 Learning Path

### Beginner
1. Explore all roles (click each button)
2. Notice how metrics change
3. Get familiar with layout

### Intermediate
4. Compare two roles side-by-side
5. Export a role report
6. Interpret the metrics

### Advanced
7. Identify trends by role
8. Spot anomalies
9. Use insights for decisions

## ❓ FAQ

**Q: Can I filter by multiple roles?**
A: Currently no, one role at a time. Quick switching available.

**Q: Are counts updated in real-time?**
A: Updated on page load. Refresh for latest.

**Q: Do exports include all history?**
A: Yes, all historical data from selected role.

**Q: Is this data accurate?**
A: Yes, calculated from database at query time.

**Q: Can I undo a filter?**
A: Yes, click "All Users" to see system-wide again.

**Q: What if a role has no users?**
A: Shows 0 count, metrics display empty states.

## 💡 Best Practices

✅ **Do**
- Check each role regularly
- Compare roles to identify gaps
- Export reports for analysis
- Use filters for targeted insights
- Share role-specific reports

❌ **Don't**
- Assume all roles have equal activity
- Ignore role-based trends
- Compare roles without context
- Export all data unnecessarily
- Forget to note time period

## 🚀 Next Steps

1. **Explore** - Click each role button
2. **Analyze** - Review role-specific metrics
3. **Compare** - Note differences between roles
4. **Export** - Save reports for sharing
5. **Act** - Use insights to improve engagement

---

**Quick Reference Version**: 1.0
**Last Updated**: February 5, 2026
**Status**: Ready to Use

**For more details, see**: [ROLE_BASED_FILTERING.md](./ROLE_BASED_FILTERING.md)

