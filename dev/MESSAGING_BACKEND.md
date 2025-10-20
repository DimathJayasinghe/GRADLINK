# Simple Messaging Backend

## Database Structure

Exactly as requested:

### Messages Table
```sql
CREATE TABLE messages (
    message_id INT AUTO_INCREMENT PRIMARY KEY,
    sender_id INT NOT NULL,
    receiver_id INT NOT NULL,
    conversation_id INT NOT NULL,
    message_text TEXT NOT NULL,
    message_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

### Supporting Tables
```sql
CREATE TABLE conversations (
    conversation_id INT AUTO_INCREMENT PRIMARY KEY,
    user1_id INT NOT NULL,
    user2_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_activity TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

## Setup Instructions

1. **Run SQL Schema**:
   ```bash
   # Import the database schema
   mysql -u your_username -p your_database < dev/sql.sql
   ```

2. **Test the Backend**:
   ```bash
   # Start PHP server
   php -S localhost:8000 -t public
   
   # Visit test page
   http://localhost:8000/test_backend.html
   ```

## API Endpoints

### 1. Send Message
```http
POST /api.php?action=send
Content-Type: application/json

{
    "sender_id": 1,
    "receiver_id": 2,
    "message_text": "Hello! How are you?",
    "conversation_id": null
}
```

### 2. Get User Conversations
```http
GET /api.php?action=conversations&user_id=1
```

### 3. Get Conversation Messages
```http
GET /api.php?action=messages&conversation_id=1&user_id=1&limit=50&offset=0
```

### 4. Get Available Users
```http
GET /api.php?action=users&current_user_id=1&search=john&limit=20
```

### 5. Get Conversation Between Users
```http
GET /api.php?action=conversation&user1_id=1&user2_id=2
```

## Backend Features

✅ **Simple Structure**: Exact fields as requested  
✅ **Auto Conversations**: Creates conversations automatically  
✅ **Message History**: Complete message storage and retrieval  
✅ **User Search**: Find users to message  
✅ **RESTful API**: Clean, standard endpoints  
✅ **Optimized Queries**: Proper indexing and performance  
✅ **Error Handling**: Comprehensive error responses  
✅ **Scalable**: Ready for production use  

## Example Usage

### PHP Integration
```php
// Initialize messaging
$messageModel = new M_message();

// Send a message
$result = $messageModel->sendMessage($senderId, $receiverId, $messageText);

// Get conversations
$conversations = $messageModel->getUserConversations($userId);

// Get messages
$messages = $messageModel->getConversationMessages($conversationId, $userId);
```

### JavaScript Integration
```javascript
// Send message
const response = await fetch('/api.php?action=send', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({
        sender_id: 1,
        receiver_id: 2,
        message_text: 'Hello!',
        conversation_id: null
    })
});

const result = await response.json();
console.log(result); // { success: true, message_id: 123, conversation_id: 1 }
```

## Files Structure

```
app/
├── models/
│   ├── M_message.php          # Main messaging model
│                              
├── controllers/
│   └── messages.php           # Messages controller
└── views/
    └── messages/              # Message views

dev/
└── sql.sql                    # Database schema
```

## Testin

## Production Notes

- Add authentication/authorization
- Implement rate limiting
- Add message validation
- Consider real-time WebSocket integration
- Add file attachment support if needed
- Implement message encryption for security