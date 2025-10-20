<?php
class M_message extends Database {
    
    public function __construct() {
        parent::__construct();
    }
    
    // Get all conversations for a user - Simplified Backend
    public function getUserConversations($userId) {
        $this->query("
            SELECT DISTINCT
                c.conversation_id,
                c.created_at as conversation_created,
                c.last_activity,
                CASE 
                    WHEN c.user1_id = :user_id THEN u2.name 
                    ELSE u1.name 
                END as other_full_name,
                CASE 
                    WHEN c.user1_id = :user_id THEN u2.profile_image 
                    ELSE u1.profile_image 
                END as other_avatar,
                CASE 
                    WHEN c.user1_id = :user_id THEN u2.id 
                    ELSE u1.id 
                END as other_user_id,
                m.message_text as last_message,
                m.message_time as last_message_time,
                COALESCE(unread.unread_count, 0) as unread_count
            FROM conversations c
            JOIN users u1 ON c.user1_id = u1.id
            JOIN users u2 ON c.user2_id = u2.id
            LEFT JOIN messages m ON c.conversation_id = m.conversation_id 
                AND m.message_id = (
                    SELECT MAX(message_id) 
                    FROM messages 
                    WHERE conversation_id = c.conversation_id
                )
            LEFT JOIN (
                SELECT conversation_id, COUNT(*) as unread_count
                FROM messages 
                WHERE receiver_id = :user_id AND is_read = 0
                GROUP BY conversation_id
            ) unread ON c.conversation_id = unread.conversation_id
            WHERE (c.user1_id = :user_id OR c.user2_id = :user_id)
                AND c.is_deleted = 0
            ORDER BY c.last_activity DESC
        ");
        $this->bind(':user_id', $userId);
        return $this->resultSet();
    }
    
    // Get messages in a conversation - Simple Backend Structure
    public function getConversationMessages($conversationId, $userId, $limit = 50, $offset = 0) {
        $limit = max(1, (int)$limit);
        $offset = max(0, (int)$offset);
        $sql = "
            SELECT 
                m.message_id,
                m.sender_id,
                m.receiver_id,
                m.conversation_id,
                m.message_text,
                m.message_time,
                m.is_read,
                u.name as sender_name,
                u.profile_image as sender_avatar
            FROM messages m
            JOIN users u ON m.sender_id = u.id
            JOIN conversations c ON m.conversation_id = c.conversation_id
            WHERE m.conversation_id = :conversation_id 
                AND (c.user1_id = :user_id OR c.user2_id = :user_id)
                AND c.is_deleted = 0
            ORDER BY m.message_time ASC
            LIMIT $limit OFFSET $offset
        ";
        $this->query($sql);
        $this->bind(':conversation_id', $conversationId);
        $this->bind(':user_id', $userId);
        return $this->resultSet();
    }
    
    // Send a message - Exact fields: message_id, sender_id, receiver_id, conversation_id, message_text, message_time
    public function sendMessage($senderId, $receiverId, $messageText, $conversationId = null) {
        // If a conversationId is provided, validate it and derive the actual receiver from participants
        if ($conversationId) {
            $this->query("SELECT user1_id, user2_id FROM conversations WHERE conversation_id = :cid AND is_deleted = 0 LIMIT 1");
            $this->bind(':cid', $conversationId);
            $c = $this->single();
            if (!$c) { return false; }
            // Ensure sender is a participant
            if ($c->user1_id != $senderId && $c->user2_id != $senderId) { return false; }
            // Derive the receiver strictly from the conversation participants
            $receiverId = ($c->user1_id == $senderId) ? (int)$c->user2_id : (int)$c->user1_id;
        } else {
            // Find or create a conversation for this unordered pair
            $conversationId = $this->findOrCreateConversation($senderId, $receiverId);
            if (!$conversationId) { return false; }
        }

        // Insert message with exact backend structure
        $this->query("
            INSERT INTO messages (sender_id, receiver_id, conversation_id, message_text, message_time)
            VALUES (:sender_id, :receiver_id, :conversation_id, :message_text, NOW())
        ");
        $this->bind(':sender_id', $senderId);
        $this->bind(':receiver_id', $receiverId);
        $this->bind(':conversation_id', $conversationId);
        $this->bind(':message_text', $messageText);

        if ($this->execute()) {
            // Update conversation last_activity
            $this->updateConversationActivity($conversationId);
            $messageId = $this->lastInsertId();

            return [
                'message_id' => $messageId,
                'conversation_id' => $conversationId
            ];
        }
        return false;
    }
    
    // Find or create conversation between two users
    private function findOrCreateConversation($user1Id, $user2Id) {
        // Always normalize order (smallest id first) to enforce a single row per pair
        $a = min((int)$user1Id, (int)$user2Id);
        $b = max((int)$user1Id, (int)$user2Id);

        // Check if conversation already exists in normalized order
        $this->query("
            SELECT conversation_id 
            FROM conversations 
            WHERE user1_id = :a AND user2_id = :b AND is_deleted = 0
            LIMIT 1
        ");
        $this->bind(':a', $a);
        $this->bind(':b', $b);
        $conversation = $this->single();

        if ($conversation) {
            return (int)$conversation->conversation_id;
        }

        // Create new conversation in normalized order
        $this->query("
            INSERT INTO conversations (user1_id, user2_id, created_at, last_activity)
            VALUES (:a, :b, NOW(), NOW())
        ");
        $this->bind(':a', $a);
        $this->bind(':b', $b);

        if ($this->execute()) {
            return (int)$this->lastInsertId();
        }
        return false;
    }
    
    // Update conversation last activity
    private function updateConversationActivity($conversationId) {
        $this->query("
            UPDATE conversations 
            SET last_activity = NOW() 
            WHERE conversation_id = :conversation_id
        ");
        $this->bind(':conversation_id', $conversationId);
        $this->execute();
    }
    
    // Get conversation between two users
    public function getConversation($userId1, $userId2) {
        // Find conversation using normalized order
        $a = min((int)$userId1, (int)$userId2);
        $b = max((int)$userId1, (int)$userId2);
        $this->query("
            SELECT conversation_id 
            FROM conversations 
            WHERE user1_id = :a AND user2_id = :b AND is_deleted = 0
            LIMIT 1
        ");
        $this->bind(':a', $a);
        $this->bind(':b', $b);
        $conversation = $this->single();
        
        if (!$conversation) {
            return null; // No conversation exists
        }
        
        $conversationId = $conversation->conversation_id;
        
        // Get messages with simple structure
        $messages = $this->getConversationMessages($conversationId, $userId1);
        
        return [
            'conversation_id' => $conversationId,
            'messages' => $messages ? $messages : []
        ];
    }
    
    // Get available users to start conversation with
    public function getAvailableUsers($currentUserId, $searchQuery = '', $limit = 20) {
        try {
            $searchCondition = '';
            if (!empty($searchQuery)) {
                $searchCondition = "AND (u.name LIKE :search OR u.email LIKE :search OR u.display_name LIKE :search)";
            }
            
            // Simple query matching users table
            $this->query("
                SELECT 
                    u.id as user_id,
                    u.email as username,
                    COALESCE(u.display_name, u.name) as full_name,
                    u.profile_image as profile_picture,
                    u.role,
                    u.batch_no,
                    CASE 
                        WHEN c.conversation_id IS NOT NULL THEN 1 
                        ELSE 0 
                    END as has_conversation
                FROM users u
                LEFT JOIN conversations c ON 
                    ((c.user1_id = u.id AND c.user2_id = :current_user_id) OR 
                     (c.user2_id = u.id AND c.user1_id = :current_user_id))
                    AND c.is_deleted = 0
                WHERE u.id != :current_user_id
                    $searchCondition
                ORDER BY u.name ASC
                LIMIT :limit
            ");
            
            $this->bind(':current_user_id', $currentUserId);
            $this->bind(':limit', $limit);
            
            if (!empty($searchQuery)) {
                $searchTerm = "%$searchQuery%";
                $this->bind(':search', $searchTerm);
            }
            
            $result = $this->resultSet();
            return $result ? $result : [];
            
        } catch (Exception $e) {
            error_log("Database error in getAvailableUsers: " . $e->getMessage());
            return [];
        }
    }
    
    // Delete conversation (soft delete)
    public function deleteConversation($conversationId, $userId) {
        // Verify user is part of this conversation
        $this->query("
            SELECT conversation_id 
            FROM conversations 
            WHERE conversation_id = :conversation_id 
                AND (user1_id = :user_id OR user2_id = :user_id)
        ");
        $this->bind(':conversation_id', $conversationId);
        $this->bind(':user_id', $userId);
        
        if (!$this->single()) {
            return false;
        }
        
        // Soft delete conversation
        $this->query("
            UPDATE conversations 
            SET is_deleted = 1, deleted_at = NOW() 
            WHERE conversation_id = :conversation_id
        ");
        $this->bind(':conversation_id', $conversationId);
        return $this->execute();
    }
    
    // Report conversation
    public function reportConversation($conversationId, $reporterId, $reason) {
        $this->query("
            INSERT INTO conversation_reports (conversation_id, reporter_id, reason, created_at)
            VALUES (:conversation_id, :reporter_id, :reason, NOW())
        ");
        $this->bind(':conversation_id', $conversationId);
        $this->bind(':reporter_id', $reporterId);
        $this->bind(':reason', $reason);
        return $this->execute();
    }
    
    // Mark messages as read
    public function markAsRead($conversationId, $userId) {
        $this->query("
            UPDATE messages 
            SET is_read = 1, read_at = NOW() 
            WHERE conversation_id = :conversation_id 
                AND receiver_id = :user_id 
                AND is_read = 0
        ");
        $this->bind(':conversation_id', $conversationId);
        $this->bind(':user_id', $userId);
        return $this->execute();
    }

    // Get conversation details for a specific conversation for this user
    public function getConversationDetails($conversationId, $userId) {
        $this->query("
            SELECT 
                c.conversation_id,
                c.user1_id,
                c.user2_id,
                CASE WHEN c.user1_id = :user_id THEN u2.id ELSE u1.id END AS other_user_id,
                CASE WHEN c.user1_id = :user_id THEN u2.name ELSE u1.name END AS other_full_name,
                CASE WHEN c.user1_id = :user_id THEN u2.profile_image ELSE u1.profile_image END AS other_avatar
            FROM conversations c
            JOIN users u1 ON c.user1_id = u1.id
            JOIN users u2 ON c.user2_id = u2.id
            WHERE c.conversation_id = :conversation_id
              AND (c.user1_id = :user_id OR c.user2_id = :user_id)
              AND c.is_deleted = 0
            LIMIT 1
        ");
        $this->bind(':user_id', $userId);
        $this->bind(':conversation_id', $conversationId);
        $row = $this->single();
        if (!$row) {
            return null;
        }
        return [
            'conversation_id' => (int)$row->conversation_id,
            'other_user_id' => (int)$row->other_user_id,
            'other_full_name' => $row->other_full_name,
            'other_avatar' => $row->other_avatar,
        ];
    }

    // Edit message text (only by sender)
    public function editMessage($messageId, $userId, $newText) {
        $this->query("UPDATE messages SET message_text = :text WHERE message_id = :id AND sender_id = :uid");
        $this->bind(':text', $newText);
        $this->bind(':id', $messageId);
        $this->bind(':uid', $userId);
        $ok = $this->execute();
        if ($ok) {
            // bump conversation last activity
            $this->query("UPDATE conversations c JOIN messages m ON c.conversation_id = m.conversation_id SET c.last_activity = NOW() WHERE m.message_id = :id");
            $this->bind(':id', $messageId);
            $this->execute();
        }
        return $ok;
    }

    // Delete message (soft or hard); here we hard delete, sender only
    public function deleteMessage($messageId, $userId) {
        // Get conversation id for activity update
        $this->query("SELECT conversation_id FROM messages WHERE message_id = :id AND sender_id = :uid");
        $this->bind(':id', $messageId);
        $this->bind(':uid', $userId);
        $row = $this->single();
        if (!$row) return false;
        $convId = $row->conversation_id;

        $this->query("DELETE FROM messages WHERE message_id = :id AND sender_id = :uid");
        $this->bind(':id', $messageId);
        $this->bind(':uid', $userId);
        $ok = $this->execute();
        if ($ok && $convId) { $this->updateConversationActivity($convId); }
        return $ok;
    }
}
?>