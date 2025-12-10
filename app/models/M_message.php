<?php
class M_message extends Database {
    
    /**
     * Get all available users (excluding current user) with optional search
     * Shows users that:
     * 1. Current user follows them, OR
     * 2. They follow current user AND have sent at least one message, OR
     * 3. Admin role AND there's a message history
     */
    public function getAvailableUsers($currentUserId, $searchTerm = null, $lastPoll = null) {
        $sql = "SELECT 
                    u.id AS user_id,
                    u.name,
                    u.display_name,
                    u.email,
                    u.profile_image AS profile_picture,
                    COUNT(DISTINCT CASE 
                        WHEN m.receiver_id = :current_user_id 
                             AND m.status IN ('unread', 'edited-unread') 
                        THEN m.message_id 
                    END) AS unread_count,
                    COALESCE(
                        MAX(COALESCE(m.modified_time, m.message_time)),
                        '1970-01-01 00:00:00'
                    ) AS last_activity
                FROM users u
                LEFT JOIN followers f_following ON f_following.followed_id = u.id AND f_following.follower_id = :current_user_id
                LEFT JOIN followers f_follower ON f_follower.follower_id = u.id AND f_follower.followed_id = :current_user_id
                LEFT JOIN messages m ON ((m.sender_id = u.id AND m.receiver_id = :current_user_id) OR (m.receiver_id = u.id AND m.sender_id = :current_user_id))
                LEFT JOIN messages m_from_them ON m_from_them.sender_id = u.id AND m_from_them.receiver_id = :current_user_id
                WHERE 
                    u.id != :current_user_id
                    AND (
                        f_following.follower_id IS NOT NULL 
                        OR (f_follower.follower_id IS NOT NULL AND m_from_them.message_id IS NOT NULL)
                        OR (
                            u.role = 'admin' 
                            AND EXISTS (
                                SELECT 1 FROM messages m_admin
                                WHERE ((m_admin.sender_id = u.id AND m_admin.receiver_id = :current_user_id)
                                       OR (m_admin.receiver_id = u.id AND m_admin.sender_id = :current_user_id))
                                LIMIT 1
                            )
                        )
                    )";

        if ($lastPoll) {
            $sql .= " AND EXISTS (
                        SELECT 1 FROM messages m_poll
                        WHERE ((m_poll.sender_id = u.id AND m_poll.receiver_id = :current_user_id)
                               OR (m_poll.receiver_id = u.id AND m_poll.sender_id = :current_user_id))
                          AND COALESCE(m_poll.modified_time, m_poll.message_time) > :last_poll
                    )";
        }

        if ($searchTerm) {
            $sql .= " AND (u.name LIKE :search 
                      OR u.display_name LIKE :search
                      OR u.email LIKE :search)";
        }

        $sql .= " GROUP BY u.id, u.name, u.display_name, u.email, u.profile_image
                  ORDER BY 
                    CASE WHEN unread_count > 0 THEN 0 ELSE 1 END,
                    last_activity DESC, 
                    u.name ASC";

        $this->query($sql);
        $this->bind(':current_user_id', $currentUserId);
        if ($lastPoll) {
            $this->bind(':last_poll', $lastPoll);
        }

        if ($searchTerm) {
            $this->bind(':search', '%' . $searchTerm . '%');
        }

        $users = $this->resultSet();

        foreach ($users as $user) {
            $user->unread_count = (int)($user->unread_count ?? 0);
        }

        return $users;
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
    public function getMessages($userId1, $userId2, $since = null) {
        $sql = "SELECT 
                    m.message_id,
                    m.sender_id,
                    m.receiver_id,
                    CASE WHEN m.status = 'deleted' THEN NULL ELSE m.message_text END AS content,
                    DATE_FORMAT(m.message_time, '%Y-%m-%d %H:%i') AS timestamp,
                    DATE_FORMAT(m.modified_time, '%Y-%m-%d %H:%i') AS modified_timestamp,
                    m.status,
                    m.message_time,
                    m.modified_time,
                    u.name AS sender_name,
                    u.display_name AS sender_display_name,
                    u.profile_image AS sender_picture
                FROM messages m
                JOIN users u ON m.sender_id = u.id
                WHERE (
                    (m.sender_id = :user1 AND m.receiver_id = :user2)
                    OR (m.sender_id = :user2 AND m.receiver_id = :user1)
                )";

        if ($since) {
            $sql .= " AND COALESCE(m.modified_time, m.message_time) > :since";
        }

        $sql .= " ORDER BY m.message_time ASC, m.message_id ASC";

        $this->query($sql);
        $this->bind(':user1', $userId1);
        $this->bind(':user2', $userId2);
        if ($since) {
            $this->bind(':since', $since);
        }

        return $this->resultSet();
    }
    
    /**
     * Send a message
     */
    public function sendMessage($senderId, $receiverId, $messageText) {
        $sql = "INSERT INTO messages (sender_id, receiver_id, message_text, status) 
            VALUES (:sender_id, :receiver_id, :message_text, 'unread')";
        
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
     * Soft-delete a message (only if sender matches)
     */
    public function deleteMessage($messageId, $userId) {
        // Ensure message belongs to user
        $this->query("SELECT status FROM messages WHERE message_id = :message_id AND sender_id = :user_id LIMIT 1");
        $this->bind(':message_id', $messageId);
        $this->bind(':user_id', $userId);
        $existing = $this->single();

        if (!$existing) {
            return false;
        }

        if (strtolower((string)$existing->status) === 'deleted') {
            return $this->getMessageById($messageId);
        }

        $sql = "UPDATE messages 
            SET status = 'deleted',
                message_text = '',
                modified_time = CURRENT_TIMESTAMP
            WHERE message_id = :message_id AND sender_id = :user_id";

        $this->query($sql);
        $this->bind(':message_id', $messageId);
        $this->bind(':user_id', $userId);

        if ($this->execute()) {
            return $this->getMessageById($messageId);
        }

        return false;
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
        $this->query("SELECT status FROM messages WHERE message_id = :message_id AND sender_id = :user_id LIMIT 1");
        $this->bind(':message_id', $messageId);
        $this->bind(':user_id', $userId);
        $existing = $this->single();

        if (!$existing) {
            return false;
        }

        if (strtolower((string)$existing->status) === 'deleted') {
            return false;
        }

        $newStatus = $this->resolveEditedStatus((string)$existing->status);

        $sql = "UPDATE messages 
                SET message_text = :text,
                    status = :status,
                    modified_time = CURRENT_TIMESTAMP
                WHERE message_id = :message_id AND sender_id = :user_id";

        $this->query($sql);
        $this->bind(':text', $newText);
        $this->bind(':status', $newStatus);
        $this->bind(':message_id', $messageId);
        $this->bind(':user_id', $userId);

        if ($this->execute()) {
            return $this->getMessageById($messageId);
        }

        return false;
    }

    /**
     * Mark messages as read (clear unread count)
     */
    public function markConversationAsRead($currentUserId, $otherUserId) {
        $sql = "UPDATE messages
                SET status = CASE 
                    WHEN status = 'edited-unread' THEN 'edited-read'
                    WHEN status = 'unread' THEN 'read'
                    ELSE status
                END
                WHERE sender_id = :other_user_id 
                  AND receiver_id = :current_user_id
                  AND status IN ('unread', 'edited-unread')";

        $this->query($sql);
        $this->bind(':current_user_id', $currentUserId);
        $this->bind(':other_user_id', $otherUserId);

        $this->execute();
        return true;
    }

    /**
     * Get unread count for a specific conversation
     */
    public function getUnreadCount($currentUserId, $otherUserId) {
        $sql = "SELECT COUNT(*) AS unread_count
                FROM messages
                WHERE sender_id = :other_user_id
                  AND receiver_id = :current_user_id
                  AND status IN ('unread', 'edited-unread')";

        $this->query($sql);
        $this->bind(':current_user_id', $currentUserId);
        $this->bind(':other_user_id', $otherUserId);

        $result = $this->single();
        return $result ? (int)$result->unread_count : 0;
    }

    /**
     * Get total unread messages count for current user
     */
    public function getTotalUnreadCount($userId) {
        $sql = "SELECT COUNT(*) AS total
                FROM messages
                WHERE receiver_id = :user_id
                  AND status IN ('unread', 'edited-unread')";

        $this->query($sql);
        $this->bind(':user_id', $userId);

        $result = $this->single();
        return $result ? (int)$result->total : 0;
    }

    /**
     * Get all conversations with unread counts
     */
    public function getConversationsWithUnread($userId) {
        $sql = "SELECT
                    u.id AS user_id,
                    u.name,
                    u.display_name,
                    u.email,
                    u.profile_image AS profile_picture,
                    COUNT(DISTINCT m.message_id) AS unread_count,
                    MAX(COALESCE(m.modified_time, m.message_time)) AS last_unread_at
                FROM messages m
                JOIN users u ON u.id = m.sender_id
                WHERE m.receiver_id = :current_user_id
                  AND m.status IN ('unread', 'edited-unread')
                GROUP BY u.id, u.name, u.display_name, u.email, u.profile_image
                ORDER BY last_unread_at DESC, u.name ASC";

        $this->query($sql);
        $this->bind(':current_user_id', $userId);

        $result = $this->resultSet();
        foreach ($result as $row) {
            $row->unread_count = (int)$row->unread_count;
        }

        return $result;
    }

    /**
     * Fetch a single message payload by ID with presentation fields
     */
    public function getMessageById($messageId) {
        $sql = "SELECT 
                    m.message_id,
                    m.sender_id,
                    m.receiver_id,
                    CASE WHEN m.status = 'deleted' THEN NULL ELSE m.message_text END AS content,
                    DATE_FORMAT(m.message_time, '%Y-%m-%d %H:%i') AS timestamp,
                    DATE_FORMAT(m.modified_time, '%Y-%m-%d %H:%i') AS modified_timestamp,
                    m.status,
                    m.message_time,
                    m.modified_time,
                    u.name AS sender_name,
                    u.display_name AS sender_display_name,
                    u.profile_image AS sender_picture
                FROM messages m
                JOIN users u ON m.sender_id = u.id
                WHERE m.message_id = :message_id
                LIMIT 1";

        $this->query($sql);
        $this->bind(':message_id', $messageId);

        return $this->single();
    }

    /**
     * Decide next status for a message that is being edited
     */
    private function resolveEditedStatus($currentStatus) {
        $status = strtolower($currentStatus ?? '');

        if ($status === 'read' || $status === 'edited-read') {
            return 'edited-read';
        }

        if ($status === 'edited-unread') {
            return 'edited-unread';
        }

        // Default for unread / unknown states
        return 'edited-unread';
    }
}
?>