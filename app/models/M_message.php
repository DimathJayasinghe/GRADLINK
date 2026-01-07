<?php
class M_message extends Database {
    
    /**
     * Get all available users (excluding current user) with optional search
     * Shows users that:
     * 1. Current user follows them, OR
     * 2. They follow current user AND have sent at least one message, OR
     * 3. Admin role AND there's a message history
     */
    public function getAvailableUsers($currentUserId, $searchTerm = null) {
        $sql = "SELECT 
                    u.id as user_id,
                    u.name,
                    u.display_name,
                    u.email,
                    u.profile_image as profile_picture,
                    COALESCE(MAX(m.message_time), '1970-01-01') as last_activity
                FROM users u
                LEFT JOIN followers f_following ON f_following.followed_id = u.id AND f_following.follower_id = :current_user_id
                LEFT JOIN followers f_follower ON f_follower.follower_id = u.id AND f_follower.followed_id = :current_user_id
                LEFT JOIN messages m ON (m.sender_id = u.id AND m.receiver_id = :current_user_id) OR (m.receiver_id = u.id AND m.sender_id = :current_user_id)
                LEFT JOIN messages m_from_them ON m_from_them.sender_id = u.id AND m_from_them.receiver_id = :current_user_id
                WHERE u.id != :current_user_id
                    AND (
                        f_following.follower_id IS NOT NULL 
                        OR (f_follower.follower_id IS NOT NULL AND m_from_them.message_id IS NOT NULL)
                        OR (u.role = 'admin' AND m.message_id IS NOT NULL)
                    )";
        
        // Add search filter if provided
        if ($searchTerm) {
            $sql .= " AND (u.name LIKE :search 
                      OR u.display_name LIKE :search
                      OR u.email LIKE :search)";
        }
        
        $sql .= " GROUP BY u.id, u.name, u.display_name, u.email, u.profile_image
                  ORDER BY last_activity DESC, u.name ASC";
        
        $this->query($sql);
        $this->bind(':current_user_id', $currentUserId);
        
        if ($searchTerm) {
            $this->bind(':search', '%' . $searchTerm . '%');
        }
        
        return $this->resultSet();
    }
    
    /**
     * Get conversation partner info
     */
    public function getConversationPartner($userId) {
        $sql = "SELECT 
                    id as user_id,
                    name,
                    display_name,
                    email,
                    profile_image as profile_picture
                FROM users
                WHERE id = :user_id
                LIMIT 1";
        
        $this->query($sql);
        $this->bind(':user_id', $userId);
        
        return $this->single();
    }
    
    /**
     * Get messages between two users
     */
    public function getMessages($userId1, $userId2) {
        $sql = "SELECT 
                    m.message_id,
                    m.sender_id,
                    m.receiver_id,
                    m.message_text as content,
                    DATE_FORMAT(m.message_time, '%Y-%m-%d %H:%i') as timestamp,
                    u.name as sender_name,
                    u.display_name as sender_display_name,
                    u.profile_image as sender_picture
                FROM messages m
                JOIN users u ON m.sender_id = u.id
                WHERE (m.sender_id = :user1 AND m.receiver_id = :user2)
                   OR (m.sender_id = :user2 AND m.receiver_id = :user1)
                ORDER BY m.message_time ASC";
        
        $this->query($sql);
        $this->bind(':user1', $userId1);
        $this->bind(':user2', $userId2);
        
        return $this->resultSet();
    }
    
    /**
     * Send a message
     */
    public function sendMessage($senderId, $receiverId, $messageText) {
        $sql = "INSERT INTO messages (sender_id, receiver_id, message_text) 
                VALUES (:sender_id, :receiver_id, :message_text)";
        
        $this->query($sql);
        $this->bind(':sender_id', $senderId);
        $this->bind(':receiver_id', $receiverId);
        $this->bind(':message_text', $messageText);
        
        if ($this->execute()) {
            return $this->lastInsertId();
        }
        return false;
    }
    
    /**
     * Delete a message (only if sender matches)
     */
    public function deleteMessage($messageId, $userId) {
        $sql = "DELETE FROM messages 
                WHERE message_id = :message_id 
                AND sender_id = :user_id";
        
        $this->query($sql);
        $this->bind(':message_id', $messageId);
        $this->bind(':user_id', $userId);
        
        return $this->execute();
    }
    
    /**
     * Check if a message exists and belongs to user
     */
    public function messageExists($messageId, $userId) {
        $sql = "SELECT message_id 
                FROM messages 
                WHERE message_id = :message_id 
                AND sender_id = :user_id
                LIMIT 1";
        
        $this->query($sql);
        $this->bind(':message_id', $messageId);
        $this->bind(':user_id', $userId);
        
        return $this->single() !== false;
    }

    /**
     * Update message content (only by the sender)
     */
    public function updateMessage($messageId, $userId, $newText) {
        $sql = "UPDATE messages 
                SET message_text = :text
                WHERE message_id = :message_id AND sender_id = :user_id";

        $this->query($sql);
        $this->bind(':text', $newText);
        $this->bind(':message_id', $messageId);
        $this->bind(':user_id', $userId);

        return $this->execute();
    }




}
?>