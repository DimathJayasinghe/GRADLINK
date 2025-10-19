<?php
class M_message extends Database {
    
    public function __construct() {
        parent::__construct();
    }
    
    // Get all conversations for a user
    public function getUserConversations($userId) {
        $this->query("
            SELECT DISTINCT
                c.*,
                CASE 
                    WHEN c.user1_id = :user_id THEN u2.username 
                    ELSE u1.username 
                END as other_username,
                CASE 
                    WHEN c.user1_id = :user_id THEN u2.full_name 
                    ELSE u1.full_name 
                END as other_full_name,
                CASE 
                    WHEN c.user1_id = :user_id THEN u2.profile_picture 
                    ELSE u1.profile_picture 
                END as other_avatar,
                m.message_text as last_message,
                m.created_at as last_message_time,
                COALESCE(unread.unread_count, 0) as unread_count
            FROM conversations c
            JOIN users u1 ON c.user1_id = u1.user_id
            JOIN users u2 ON c.user2_id = u2.user_id
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
            ORDER BY m.created_at DESC
        ");
        $this->bind(':user_id', $userId);
        return $this->resultSet();
    }
    
    // Get messages in a conversation
    public function getConversationMessages($conversationId, $userId, $limit = 50, $offset = 0) {
        $this->query("
            SELECT m.*, u.username, u.full_name, u.profile_picture
            FROM messages m
            JOIN users u ON m.sender_id = u.user_id
            JOIN conversations c ON m.conversation_id = c.conversation_id
            WHERE m.conversation_id = :conversation_id 
                AND (c.user1_id = :user_id OR c.user2_id = :user_id)
                AND c.is_deleted = 0
            ORDER BY m.created_at DESC
            LIMIT :limit OFFSET :offset
        ");
        $this->bind(':conversation_id', $conversationId);
        $this->bind(':user_id', $userId);
        $this->bind(':limit', $limit);
        $this->bind(':offset', $offset);
        return $this->resultSet();
    }
    
    // Send a message
    public function sendMessage($senderId, $receiverId, $messageText) {
        // First, find or create conversation
        $conversationId = $this->findOrCreateConversation($senderId, $receiverId);
        
        if (!$conversationId) {
            return false;
        }
        
        // Insert message
        $this->query("
            INSERT INTO messages (conversation_id, sender_id, receiver_id, message_text, created_at)
            VALUES (:conversation_id, :sender_id, :receiver_id, :message_text, NOW())
        ");
        $this->bind(':conversation_id', $conversationId);
        $this->bind(':sender_id', $senderId);
        $this->bind(':receiver_id', $receiverId);
        $this->bind(':message_text', $messageText);
        
        if ($this->execute()) {
            // Update conversation last_activity
            $this->updateConversationActivity($conversationId);
            return $this->lastInsertId();
        }
        return false;
    }
    
    // Find or create conversation between two users
    private function findOrCreateConversation($user1Id, $user2Id) {
        // Check if conversation already exists
        $this->query("
            SELECT conversation_id 
            FROM conversations 
            WHERE ((user1_id = :user1 AND user2_id = :user2) 
                OR (user1_id = :user2 AND user2_id = :user1))
                AND is_deleted = 0
        ");
        $this->bind(':user1', $user1Id);
        $this->bind(':user2', $user2Id);
        $conversation = $this->single();
        
        if ($conversation) {
            return $conversation->conversation_id;
        }
        
        // Create new conversation
        $this->query("
            INSERT INTO conversations (user1_id, user2_id, created_at, last_activity)
            VALUES (:user1_id, :user2_id, NOW(), NOW())
        ");
        $this->bind(':user1_id', $user1Id);
        $this->bind(':user2_id', $user2Id);
        
        if ($this->execute()) {
            return $this->lastInsertId();
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
    
    // Get conversation details
    public function getConversationDetails($conversationId, $userId) {
        $this->query("
            SELECT c.*,
                CASE 
                    WHEN c.user1_id = :user_id THEN u2.username 
                    ELSE u1.username 
                END as other_username,
                CASE 
                    WHEN c.user1_id = :user_id THEN u2.full_name 
                    ELSE u1.full_name 
                END as other_full_name,
                CASE 
                    WHEN c.user1_id = :user_id THEN u2.profile_picture 
                    ELSE u1.profile_picture 
                END as other_avatar,
                CASE 
                    WHEN c.user1_id = :user_id THEN u2.user_id 
                    ELSE u1.user_id 
                END as other_user_id
            FROM conversations c
            JOIN users u1 ON c.user1_id = u1.user_id
            JOIN users u2 ON c.user2_id = u2.user_id
            WHERE c.conversation_id = :conversation_id 
                AND (c.user1_id = :user_id OR c.user2_id = :user_id)
                AND c.is_deleted = 0
        ");
        $this->bind(':conversation_id', $conversationId);
        $this->bind(':user_id', $userId);
        return $this->single();
    }
}
?>