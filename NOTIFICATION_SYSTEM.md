# In-App Notification System Documentation

## Overview
Complete in-app notification system with real-time badge updates, notification modal, and persistent storage.

## Features Implemented

### 1. Backend API Endpoints
- **GET `/notification/count`** - Returns unread notification count
- **GET `/notification`** - Returns all user notifications (with decoded JSON content)
- **GET `/notification/markAsRead?nId={id}`** - Marks single notification as read
- **GET `/notification/markAllAsRead`** - Marks all user notifications as read

### 2. Database
- Notifications stored in `notifications` table
- Fields: id, receiver_id, type, reference_id, content (JSON), is_read, created_at
- Foreign key to users table with cascade delete
- Indexes on receiver_id and is_read for performance

### 3. Frontend Components

#### Notification Badge
- Red circular badge with count
- Displays on "Notifications" button in left sidebar
- Auto-hides when count is 0
- Animates with pulse effect
- Shows "99+" for counts over 99

#### Notification Modal
- Clean, modern design with icons per notification type
- Scrollable list of notifications
- Unread notifications highlighted in blue
- Click notification to mark as read
- "Mark all as read" button
- Close button and outside-click to close
- Loading and error states

#### Notification Manager (JS)
- `NotificationManager` class handles all notification logic
- Auto-polling every 60 seconds for new notifications
- Fetches count and updates badge automatically
- Lazy-loads full notifications only when modal opens
- Formats timestamps ("2h ago", "3d ago", etc.)
- Escapes HTML to prevent XSS

### 4. Notification Types
Supported types with custom icons and colors:
- `like` - Heart icon, red
- `comment` - Comment icon, blue
- `follow_request` - User-plus icon, green
- `started_following` - User-check icon, green
- `event_update` - Calendar icon, orange
- `post_approval` - Check-circle icon, green
- `fundraiser_update` - Hand-holding-heart icon, pink
- `system_announcement` - Bullhorn icon, indigo
- `admin_message` - Shield icon, indigo

### 5. Integration Points

#### Creating Notifications
```php
// In any controller (extends Controller which uses Notifiable trait)
$this->notify(
    $receiverId,      // User ID to receive notification
    'like',          // Notification type
    $postId,         // Reference ID (e.g., post ID, event ID)
    [                // Content array (will be JSON encoded)
        'liker_name' => $userName,
        'liker_id' => $userId,
        'text' => "$userName liked your post"
    ]
);
```

#### Current Integrations
- **Post likes** - Notifies post owner when someone likes their post (only on "liked", not "unliked")
- **Post comments** - Notifies post owner when someone comments
- Both skip self-notifications (you don't get notified for your own actions)

## File Structure
```
app/
├── controllers/
│   └── Notification.php          # API endpoints
├── models/
│   └── M_notification.php        # Database operations
├── helpers/
│   └── Notifiable.php            # Trait for sending notifications
├── libraries/
│   └── Controller.php            # Base controller with notification support
└── views/
    └── inc/commponents/
        ├── leftSideBar.php       # Sidebar with badge
        └── notification_pop_up.php # Modal markup & initialization

public/
├── js/
│   └── notificationManager.js    # Client-side notification logic
└── css/
    └── components/
        └── notification_popup.css # Notification styling

dev/
└── notifications.sql             # Table schema
```

## Usage

### For Users
1. Click "Notifications" button in sidebar
2. Badge shows unread count
3. Modal opens with all notifications
4. Click notification to mark as read
5. Use "Mark all as read" button to clear all

### For Developers
To add notifications for new features:

1. **Add notification type to trait** (if custom type needed):
```php
// app/helpers/Notifiable.php
protected $AllowedTypes = [
    // ... existing types
    'your_new_type',
];
```

2. **Send notification from controller**:
```php
try {
    $receiverId = /* get receiver user ID */;
    $this->notify($receiverId, 'your_new_type', $referenceId, [
        'text' => 'Your notification message',
        // ... any other data
    ]);
} catch (Throwable $e) {
    error_log("Notification failed: " . $e->getMessage());
}
```

3. **Add icon/color in CSS** (optional):
```css
.notification-icon.your_new_type {
    background: #your-color;
    color: white;
}
```

4. **Add icon mapping in JS** (optional):
```javascript
// In notificationManager.js createNotificationHTML()
const iconMap = {
    // ... existing mappings
    'your_new_type': 'your-fontawesome-icon'
};
```

## Performance Considerations
- Polling interval: 60 seconds (configurable)
- Badge updates without full notification fetch
- Modal lazy-loads notifications only when opened
- Database indexes on receiver_id and is_read
- JSON content allows flexible notification data

## Security
- All endpoints check user authentication via SessionManager
- SQL injection prevented with PDO prepared statements
- XSS prevented with HTML escaping in JS
- Self-notifications blocked (can't notify yourself)
- Proper error handling with try/catch blocks

## Testing Checklist
- [ ] Like someone else's post → notification appears
- [ ] Comment on someone's post → notification appears
- [ ] Badge shows correct count
- [ ] Badge updates automatically (wait 60s)
- [ ] Click notification → modal opens
- [ ] Unread notifications highlighted
- [ ] Click notification → marks as read
- [ ] Mark all as read → all cleared
- [ ] Modal closes on outside click
- [ ] No notification for self-interactions
- [ ] Timestamps formatted correctly
- [ ] Icons show per notification type

## Future Enhancements
- [ ] Real-time notifications with WebSocket/SSE
- [ ] Notification preferences per type
- [ ] Pagination for large notification lists
- [ ] Group similar notifications ("John and 5 others liked...")
- [ ] Push notifications (browser API)
- [ ] Email digest for unread notifications
- [ ] Notification sound/vibration options
- [ ] Deep linking to reference items (posts, events, etc.)
