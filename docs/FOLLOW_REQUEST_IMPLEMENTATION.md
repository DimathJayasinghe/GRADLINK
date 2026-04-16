# Follow Request System Implementation

## Overview
Implemented a comprehensive follow request system where users must wait for approval before following someone. Requests are stored in a pending state and can be approved or rejected by the target user.

## Database Changes

### New Table: `follow_requests`
Created in `dev/follow_requests.sql`:

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

**To deploy**: Run the SQL file in your database:
```bash
mysql -u your_user -p your_database < dev/follow_requests.sql
```

## Backend Changes

### 1. Model: `M_Profile.php`
Added new methods to handle follow requests:

- `hasPendingFollowRequest($requester_id, $target_id)` - Check if a pending request exists
- `createFollowRequest($requester_id, $target_id)` - Create a new follow request
- `cancelFollowRequest($requester_id, $target_id)` - Cancel a pending request
- `approveFollowRequest($request_id, $target_id)` - Approve request and migrate to followers table
- `rejectFollowRequest($request_id, $target_id)` - Reject and delete request

### 2. Controller: `Profile.php`
Updated methods:

- **`index()`**: Now checks for pending follow requests and passes `has_pending_request` to view
- **`follow()`**: Modified to create follow requests instead of direct follows
  - If already following → unfollow
  - If pending request exists → cancel request
  - If not following and no pending → create follow request
  - Sends `follow_request` notification when request is created
  
- **New: `approveFollowRequest()`**: Endpoint to approve a follow request
  - Route: `POST /profile/approveFollowRequest`
  - Accepts: `request_id` in JSON body
  - Moves data from `follow_requests` to `followers` table

- **New: `rejectFollowRequest()`**: Endpoint to reject a follow request
  - Route: `POST /profile/rejectFollowRequest`
  - Accepts: `request_id` in JSON body
  - Deletes request from `follow_requests` table

## Frontend Changes

### 1. Notification Manager: `notificationManager.js`
Enhanced to handle follow request notifications:

- **Updated `createNotificationHTML()`**: Special handling for `follow_request` type
  - Displays Accept and Reject buttons
  - Prevents navigation on click (so buttons work)
  
- **New: `handleFollowRequest(requesterId, action, notificationId)`**: 
  - Makes API call to approve/reject endpoints
  - Updates UI by removing notification after action
  - Shows toast notification for feedback
  
- **New: `showToast(message, type)`**: Simple toast notification for user feedback

### 2. Profile View: `v_profile.php`
Updated follow button to show three states:

1. **Follow** (default): Blue button with user-plus icon
2. **Pending** (request sent): Orange button with clock icon
3. **Following** (approved): Green button with user-check icon

JavaScript updated to handle new response format:
- Detects `action` field in response ('requested', 'cancelled', 'unfollowed')
- Updates button state, icon, and text accordingly
- Adds/removes appropriate CSS classes

### 3. Styles: `profile_styles.css`
Added styling for pending state:

```css
.profile-actions .connect-btn.pending {
    background-color: #FFA726;
    color: var(--surface-0);
    border-color: #FFA726;
}

.profile-actions .connect-btn.pending:hover {
    background-color: #FF9800;
    border-color: #FF9800;
}
```

## User Flow

### Requesting to Follow
1. User A clicks "Follow" on User B's profile
2. System creates entry in `follow_requests` table
3. Notification sent to User B with type `follow_request`
4. Button changes to "Pending" with orange color

### Approving/Rejecting Request
1. User B receives notification with Accept/Reject buttons
2. Clicking Accept:
   - Calls `/profile/approveFollowRequest`
   - Moves data to `followers` table
   - Deletes from `follow_requests`
   - Removes notification from UI
3. Clicking Reject:
   - Calls `/profile/rejectFollowRequest`
   - Deletes from `follow_requests`
   - Removes notification from UI

### Canceling Request
1. User A can click "Pending" button again to cancel
2. System deletes from `follow_requests`
3. Button changes back to "Follow"

## API Endpoints

### POST `/profile/follow`
**Body**: `{ "profile_user_id": 123 }`

**Response**:
```json
{
  "success": true,
  "action": "requested|cancelled|unfollowed",
  "connected": false|true|"pending"
}
```

### POST `/profile/approveFollowRequest`
**Body**: `{ "requester_id": 123 }`

**Response**:
```json
{
  "success": true,
  "message": "Follow request approved"
}
```

### POST `/profile/rejectFollowRequest`
**Body**: `{ "requester_id": 123 }`

**Response**:
```json
{
  "success": true,
  "message": "Follow request rejected"
}
```

## Notification Type

New notification type: `follow_request`

**Content structure**:
```json
{
  "text": "John Doe wants to follow you",
  "requester_id": 123,
  "requester_name": "John Doe",
  "requester_image": "profile.jpg",
  "link": "/profile?userid=123",
  "request_id": 456
}
```

**Note**: The `reference_id` field of the notification stores the `requester_id` (the user who wants to follow). The approve/reject endpoints use this `requester_id` along with the logged-in user's ID to identify and process the correct follow request.

## Testing Checklist

- [ ] Run SQL migration to create `follow_requests` table
- [ ] Test sending follow request
- [ ] Verify pending state shows on profile
- [ ] Test notification appears with Accept/Reject buttons
- [ ] Test accepting follow request
- [ ] Verify user appears in followers after approval
- [ ] Test rejecting follow request
- [ ] Test canceling pending request
- [ ] Test unfollowing (should still work normally)
- [ ] Verify notifications mark as read after action

## Notes

- The system prevents duplicate requests using UNIQUE constraint on `(requester_id, target_id)`
- Users cannot send follow requests to themselves (validation in controller)
- All old follows remain in `followers` table (backward compatible)
- The notification uses `reference_id` to store the requester's ID (`requester_id`)
- The approve/reject endpoints identify requests using `requester_id` + `target_id` combination
- When a request is approved, it's moved to the `followers` table and deleted from `follow_requests`
- When a request is rejected, it's simply deleted from `follow_requests`
