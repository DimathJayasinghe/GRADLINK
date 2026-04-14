# Follow Request System - Quick Setup Guide

## Step 1: Database Migration

Run the SQL script to create the `follow_requests` table:

```bash
mysql -u your_username -p your_database < dev/follow_requests.sql
```

Or manually execute the SQL in your database tool:

```sql
CREATE TABLE IF NOT EXISTS follow_requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    requester_id INT NOT NULL,
    target_id INT NOT NULL,
    status ENUM('pending', 'accepted', 'rejected') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY unique_request (requester_id, target_id),
    FOREIGN KEY (requester_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (target_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_target_pending (target_id, status),
    INDEX idx_requester (requester_id)
);
```

## Step 2: Verify File Changes

All code changes have been applied to:

### Backend
- ✅ `app/models/M_Profile.php` - Added follow request methods
- ✅ `app/controllers/Profile.php` - Updated follow logic and added approve/reject endpoints

### Frontend
- ✅ `public/js/notificationManager.js` - Added follow request notification UI with buttons
- ✅ `app/views/profiles/v_profile.php` - Updated follow button states
- ✅ `public/css/profile_styles.css` - Added pending button styling

### Documentation
- ✅ `FOLLOW_REQUEST_IMPLEMENTATION.md` - Complete implementation guide
- ✅ `dev/follow_requests.sql` - Database schema

## Step 3: Test the Feature

### Test Flow

1. **Send Follow Request**
   - User A visits User B's profile
   - Clicks "Follow" button
   - Button changes to "Pending" (orange color)
   - User B receives notification

2. **View Notification**
   - User B clicks notification bell
   - Sees "User A wants to follow you"
   - Two buttons visible: "Accept" and "Reject"

3. **Accept Request**
   - User B clicks "Accept"
   - Notification disappears
   - User A is now following User B
   - Entry moved from `follow_requests` to `followers` table

4. **Reject Request**
   - User B clicks "Reject"
   - Notification disappears
   - Request deleted from `follow_requests` table
   - User A's button returns to "Follow" state

5. **Cancel Request**
   - User A can click "Pending" button again
   - Request is cancelled
   - Button returns to "Follow" state

### Database Verification

Check the tables after each action:

```sql
-- Check pending requests
SELECT * FROM follow_requests WHERE status = 'pending';

-- Check followers
SELECT * FROM followers WHERE follower_id = X AND followed_id = Y;

-- Check notifications
SELECT * FROM notifications WHERE type = 'follow_request' AND is_read = 0;
```

## Button States

| State | Button Text | Icon | Color | Database State |
|-------|-------------|------|-------|----------------|
| Not Following | Follow | user-plus | Default (blue) | No entry |
| Request Sent | Pending | clock | Orange | Entry in `follow_requests` |
| Following | Following | user-check | Green | Entry in `followers` |

## API Endpoints

### Send/Cancel Follow Request
```
POST /profile/follow
Body: { "profile_user_id": 123 }
```

### Approve Request
```
POST /profile/approveFollowRequest  
Body: { "requester_id": 123 }
```

### Reject Request
```
POST /profile/rejectFollowRequest
Body: { "requester_id": 123 }
```

## Troubleshooting

### Issue: Button doesn't change to "Pending"
- Check browser console for JavaScript errors
- Verify `/profile/follow` endpoint returns correct `action` field
- Check network tab for response: `{"success":true,"action":"requested","connected":"pending"}`

### Issue: Notification doesn't show Accept/Reject buttons
- Verify notification type is exactly `'follow_request'`
- Check notification content has `requester_id` field
- Open browser console and check for JavaScript errors

### Issue: Accept/Reject doesn't work
- Check browser network tab for 404 or 500 errors
- Verify endpoints `/profile/approveFollowRequest` and `/profile/rejectFollowRequest` exist
- Check that `requester_id` is being sent in request body
- Look at PHP error logs for backend issues

### Issue: Database constraint errors
- Ensure `follow_requests` table was created successfully
- Check foreign key constraints reference correct `users` table
- Verify UNIQUE constraint on `(requester_id, target_id)` exists

## Rollback (if needed)

If you need to revert the changes:

```sql
-- Drop the follow_requests table
DROP TABLE IF EXISTS follow_requests;

-- Restore old follow functionality by reverting controller/model code
-- Use git to restore previous versions of files
```

## Next Steps

Consider adding these enhancements:
- Email notifications when someone requests to follow
- Bulk accept/reject for multiple requests
- Follow request expiration (auto-reject after X days)
- Request limits (max X pending requests at a time)
- Analytics on follow request acceptance rate
