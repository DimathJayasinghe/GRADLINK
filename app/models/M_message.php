<?php
class M_message extends Database {
    
    /**
     * Get all available users (excluding current user) with optional search
     */
    public function getAvailableUsers($currentUserId, $searchTerm = null) {
        $sql = "SELECT 
                    u.id as user_id,
                    u.name,
                    u.display_name,
                    u.email,
                    u.profile_image as profile_picture
                FROM users u
                WHERE u.id != :current_user_id";
        
        // Add search filter if provided
        if ($searchTerm) {
            $sql .= " AND (u.name LIKE :search 
                      OR u.display_name LIKE :search
                      OR u.email LIKE :search)";
        }
        
        $sql .= " ORDER BY u.name ASC";
        
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
        
        return $this->execute();
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