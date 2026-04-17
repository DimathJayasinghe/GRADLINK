<?php
class M_message extends Database {

    public function __construct() {
        parent::__construct();
        $this->ensureSuspendedUsersTable();
        $this->ensureUnreadTrackerTable();
    }

    private function ensureSuspendedUsersTable(): void {
        try {
            $this->query("CREATE TABLE IF NOT EXISTS suspended_users (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT NOT NULL,
                suspended_by INT NOT NULL,
                reason TEXT NULL,
                status ENUM('active','lifted','removed') NOT NULL DEFAULT 'active',
                suspended_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                lifted_at DATETIME NULL,
                lifted_by INT NULL,
                removed_at DATETIME NULL,
                removed_by INT NULL,
                snapshot_name VARCHAR(255) NULL,
                snapshot_email VARCHAR(255) NULL,
                snapshot_role VARCHAR(50) NULL,
                INDEX idx_suspended_users_user (user_id),
                INDEX idx_suspended_users_status (status),
                INDEX idx_suspended_users_suspended_at (suspended_at)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
            $this->execute();
        } catch (Exception $e) {
        }
    }

    private function ensureUnreadTrackerTable(): void {
        try {
            $this->query("CREATE TABLE IF NOT EXISTS message_unread_tracker (
                id INT AUTO_INCREMENT PRIMARY KEY,
                sender_id INT NOT NULL,
                receiver_id INT NOT NULL,
                unread_count INT DEFAULT 0,
                last_message_id INT NULL,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                UNIQUE KEY unique_conversation (sender_id, receiver_id),
                INDEX idx_receiver_unread (receiver_id, unread_count)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
            $this->execute();
        } catch (Throwable $e) {
        }
    }

    private function tableExists(string $tableName): bool {
        try {
            $this->query("SELECT 1 FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = :table_name LIMIT 1");
            $this->bind(':table_name', $tableName);
            return $this->single() !== false;
        } catch (Throwable $e) {
            return false;
        }
    }

    private function columnExists(string $tableName, string $columnName): bool {
        try {
            $this->query("SELECT 1 FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = :table_name AND COLUMN_NAME = :column_name LIMIT 1");
            $this->bind(':table_name', $tableName);
            $this->bind(':column_name', $columnName);
            return $this->single() !== false;
        } catch (Throwable $e) {
            return false;
        }
    }

    private function incrementUnreadTracker($senderId, $receiverId, $messageId): void {
        try {
            if (!$this->tableExists('message_unread_tracker')) {
                return;
            }

            $sql = "INSERT INTO message_unread_tracker (sender_id, receiver_id, unread_count, last_message_id)
                    VALUES (:sender_id, :receiver_id, 1, :last_message_id)
                    ON DUPLICATE KEY UPDATE
                        unread_count = unread_count + 1,
                        last_message_id = VALUES(last_message_id),
                        updated_at = CURRENT_TIMESTAMP";

            $this->query($sql);
            $this->bind(':sender_id', (int)$senderId);
            $this->bind(':receiver_id', (int)$receiverId);
            $this->bind(':last_message_id', (int)$messageId);
            $this->execute();
        } catch (Throwable $e) {
            // Unread tracking must not break messaging flow.
        }
    }
    
    /**
     * Get all available users (excluding current user) with optional search.
     * A chat is allowed when at least one user follows the other.
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
                LEFT JOIN suspended_users su ON su.user_id = u.id AND su.status = 'active'
                WHERE u.id != :current_user_id
                    AND su.id IS NULL
                    AND (
                        f_following.follower_id IS NOT NULL 
                        OR f_follower.follower_id IS NOT NULL
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

    public function canUsersChat($userIdA, $userIdB): bool {
        $userIdA = (int)$userIdA;
        $userIdB = (int)$userIdB;

        if ($userIdA <= 0 || $userIdB <= 0 || $userIdA === $userIdB) {
            return false;
        }

        try {
            $sql = "SELECT 1
                    FROM followers
                    WHERE (follower_id = :user_a AND followed_id = :user_b)
                       OR (follower_id = :user_b AND followed_id = :user_a)
                    LIMIT 1";

            $this->query($sql);
            $this->bind(':user_a', $userIdA);
            $this->bind(':user_b', $userIdB);

            return $this->single() !== false;
        } catch (Throwable $e) {
            return false;
        }
    }
    
    /**
     * Get conversation partner info
     */
    public function getConversationPartner($userId) {
                $sql = "SELECT 
                                        u.id as user_id,
                                        u.name,
                                        u.display_name,
                                        u.email,
                                        u.profile_image as profile_picture
                                FROM users u
                                LEFT JOIN suspended_users su ON su.user_id = u.id AND su.status = 'active'
                                WHERE u.id = :user_id
                                    AND su.id IS NULL
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
        if ($this->isUserSuspended($senderId) || $this->isUserSuspended($receiverId)) {
            return false;
        }

        $sql = "INSERT INTO messages (sender_id, receiver_id, message_text) 
                VALUES (:sender_id, :receiver_id, :message_text)";
        
        $this->query($sql);
        $this->bind(':sender_id', $senderId);
        $this->bind(':receiver_id', $receiverId);
        $this->bind(':message_text', $messageText);
        
        if ($this->execute()) {
            $messageId = (int)$this->lastInsertId();
            $this->incrementUnreadTracker($senderId, $receiverId, $messageId);
            return $messageId;
        }
        return false;
    }

    public function markConversationAsRead($currentUserId, $otherUserId) {
        $currentUserId = (int)$currentUserId;
        $otherUserId = (int)$otherUserId;

        try {
            if ($this->tableExists('message_unread_tracker')) {
                $this->query("UPDATE message_unread_tracker
                            SET unread_count = 0,
                                updated_at = CURRENT_TIMESTAMP
                            WHERE sender_id = :sender_id
                              AND receiver_id = :receiver_id");
                $this->bind(':sender_id', $otherUserId);
                $this->bind(':receiver_id', $currentUserId);
                $this->execute();
            }
        } catch (Throwable $e) {
            // Ignore tracker failures and try fallback.
        }

        try {
            if ($this->columnExists('messages', 'is_read')) {
                $this->query("UPDATE messages
                            SET is_read = 1
                            WHERE sender_id = :sender_id
                              AND receiver_id = :receiver_id
                              AND is_read = 0");
                $this->bind(':sender_id', $otherUserId);
                $this->bind(':receiver_id', $currentUserId);
                $this->execute();
            }
        } catch (Throwable $e) {
            // Swallow to preserve API stability.
        }

        return true;
    }

    public function getTotalUnreadCount($currentUserId) {
        $currentUserId = (int)$currentUserId;

        try {
            if ($this->tableExists('message_unread_tracker')) {
                $this->query("SELECT COALESCE(SUM(unread_count), 0) AS total
                            FROM message_unread_tracker
                            WHERE receiver_id = :receiver_id");
                $this->bind(':receiver_id', $currentUserId);
                $row = $this->single();
                return $row ? (int)($row->total ?? 0) : 0;
            }
        } catch (Throwable $e) {
            // Fall back to messages table if tracker unavailable.
        }

        try {
            if ($this->columnExists('messages', 'is_read')) {
                $this->query("SELECT COUNT(*) AS total
                            FROM messages
                            WHERE receiver_id = :receiver_id
                              AND is_read = 0");
                $this->bind(':receiver_id', $currentUserId);
                $row = $this->single();
                return $row ? (int)($row->total ?? 0) : 0;
            }
        } catch (Throwable $e) {
            return 0;
        }

        return 0;
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

    public function isUserSuspended($userId) {
        try {
            $sql = "SELECT 1
                    FROM suspended_users
                    WHERE user_id = :user_id
                      AND status = 'active'
                    LIMIT 1";

            $this->query($sql);
            $this->bind(':user_id', (int)$userId);

            return $this->single() !== false;
        } catch (Exception $e) {
            return false;
        }
    }




}
?>