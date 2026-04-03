<?php
class M_settings extends Database {
    
    /**
     * Get user by ID
     */
    public function getUserById($userId) {
        $sql = "SELECT * FROM users WHERE id = :user_id LIMIT 1";
        
        $this->query($sql);
        $this->bind(':user_id', $userId);
        
        return $this->single();
    }
    
    /**
     * Update user name and display name
     */
    public function updateName($userId, $name, $displayName = null) {
        $sql = "UPDATE users 
                SET name = :name, 
                    display_name = :display_name 
                WHERE id = :user_id";
        
        $this->query($sql);
        $this->bind(':user_id', $userId);
        $this->bind(':name', $name);
        $this->bind(':display_name', $displayName);
        
        return $this->execute();
    }
    
    /**
     * Update user bio
     */
    public function updateBio($userId, $bio) {
        $sql = "UPDATE users 
                SET bio = :bio 
                WHERE id = :user_id";
        
        $this->query($sql);
        $this->bind(':user_id', $userId);
        $this->bind(':bio', $bio);
        
        return $this->execute();
    }
    
    /**
     * Update user email
     */
    public function updateEmail($userId, $email) {
        $sql = "UPDATE users 
                SET email = :email 
                WHERE id = :user_id";
        
        $this->query($sql);
        $this->bind(':user_id', $userId);
        $this->bind(':email', $email);
        
        return $this->execute();
    }
    
    /**
     * Update user password
     */
    public function updatePassword($userId, $hashedPassword) {
        $sql = "UPDATE users 
                SET password = :password 
                WHERE id = :user_id";
        
        $this->query($sql);
        $this->bind(':user_id', $userId);
        $this->bind(':password', $hashedPassword);
        
        return $this->execute();
    }
    
    /**
     * Check if email already exists (excluding current user)
     */
    public function emailExists($email, $excludeUserId = null) {
        $sql = "SELECT id FROM users WHERE email = :email";
        
        if ($excludeUserId) {
            $sql .= " AND id != :exclude_id";
        }
        
        $sql .= " LIMIT 1";
        
        $this->query($sql);
        $this->bind(':email', $email);
        
        if ($excludeUserId) {
            $this->bind(':exclude_id', $excludeUserId);
        }
        
        return $this->single() !== false;
    }

    /**
     * Fetch notification preferences for current user.
     */
    public function getNotificationSettings($userId) {
        $sql = "SELECT * FROM user_notification_settings WHERE user_id = :user_id LIMIT 1";
        $this->query($sql);
        $this->bind(':user_id', $userId);

        return $this->single();
    }

    /**
     * Upsert notification preferences for current user.
     */
    public function upsertNotificationSettings($userId, $settings) {
        $sql = "INSERT INTO user_notification_settings (
                    user_id,
                    email_enabled,
                    sound_enabled,
                    mentions_enabled,
                    followers_enabled,
                    engagement_enabled,
                    dnd_enabled,
                    dnd_start,
                    dnd_end,
                    dnd_days
                ) VALUES (
                    :user_id,
                    :email_enabled,
                    :sound_enabled,
                    :mentions_enabled,
                    :followers_enabled,
                    :engagement_enabled,
                    :dnd_enabled,
                    :dnd_start,
                    :dnd_end,
                    :dnd_days
                )
                ON DUPLICATE KEY UPDATE
                    email_enabled = VALUES(email_enabled),
                    sound_enabled = VALUES(sound_enabled),
                    mentions_enabled = VALUES(mentions_enabled),
                    followers_enabled = VALUES(followers_enabled),
                    engagement_enabled = VALUES(engagement_enabled),
                    dnd_enabled = VALUES(dnd_enabled),
                    dnd_start = VALUES(dnd_start),
                    dnd_end = VALUES(dnd_end),
                    dnd_days = VALUES(dnd_days)";

        $this->query($sql);
        $this->bind(':user_id', $userId);
        $this->bind(':email_enabled', (int)($settings['email_enabled'] ?? 1));
        $this->bind(':sound_enabled', (int)($settings['sound_enabled'] ?? 0));
        $this->bind(':mentions_enabled', (int)($settings['mentions_enabled'] ?? 1));
        $this->bind(':followers_enabled', (int)($settings['followers_enabled'] ?? 1));
        $this->bind(':engagement_enabled', (int)($settings['engagement_enabled'] ?? 1));
        $this->bind(':dnd_enabled', (int)($settings['dnd_enabled'] ?? 0));
        $this->bind(':dnd_start', $settings['dnd_start'] ?? null);
        $this->bind(':dnd_end', $settings['dnd_end'] ?? null);
        $this->bind(':dnd_days', $settings['dnd_days'] ?? null);

        return $this->execute();
    }

    /**
     * Get blocked users list for current user.
     */
    public function getBlockedUsers($userId) {
        $sql = "SELECT ub.blocked_user_id, ub.created_at, u.name, u.display_name, u.profile_image
                FROM user_blocks ub
                INNER JOIN users u ON u.id = ub.blocked_user_id
                WHERE ub.user_id = :user_id
                ORDER BY ub.created_at DESC";

        $this->query($sql);
        $this->bind(':user_id', $userId);

        return $this->resultSet();
    }

    /**
     * Block a user.
     */
    public function blockUser($userId, $blockedUserId) {
        $sql = "INSERT IGNORE INTO user_blocks (user_id, blocked_user_id)
                VALUES (:user_id, :blocked_user_id)";

        $this->query($sql);
        $this->bind(':user_id', $userId);
        $this->bind(':blocked_user_id', $blockedUserId);

        return $this->execute();
    }

    /**
     * Unblock a user.
     */
    public function unblockUser($userId, $blockedUserId) {
        $sql = "DELETE FROM user_blocks
                WHERE user_id = :user_id AND blocked_user_id = :blocked_user_id";

        $this->query($sql);
        $this->bind(':user_id', $userId);
        $this->bind(':blocked_user_id', $blockedUserId);

        return $this->execute();
    }

    /**
     * Get privacy/security settings row for current user.
     */
    public function getPrivacySettings($userId) {
        $sql = "SELECT * FROM user_security_settings WHERE user_id = :user_id LIMIT 1";
        $this->query($sql);
        $this->bind(':user_id', $userId);

        return $this->single();
    }

    /**
     * Upsert privacy/security settings.
     */
    public function upsertPrivacySettings($userId, $settings) {
        $sql = "INSERT INTO user_security_settings (
                    user_id,
                    is_public,
                    two_factor_enabled,
                    two_factor_method,
                    two_factor_phone,
                    login_alerts_enabled
                ) VALUES (
                    :user_id,
                    :is_public,
                    :two_factor_enabled,
                    :two_factor_method,
                    :two_factor_phone,
                    :login_alerts_enabled
                )
                ON DUPLICATE KEY UPDATE
                    is_public = VALUES(is_public),
                    two_factor_enabled = VALUES(two_factor_enabled),
                    two_factor_method = VALUES(two_factor_method),
                    two_factor_phone = VALUES(two_factor_phone),
                    login_alerts_enabled = VALUES(login_alerts_enabled)";

        $this->query($sql);
        $this->bind(':user_id', $userId);
        $this->bind(':is_public', (int)($settings['is_public'] ?? 1));
        $this->bind(':two_factor_enabled', (int)($settings['two_factor_enabled'] ?? 0));
        $this->bind(':two_factor_method', $settings['two_factor_method'] ?? null);
        $this->bind(':two_factor_phone', $settings['two_factor_phone'] ?? null);
        $this->bind(':login_alerts_enabled', (int)($settings['login_alerts_enabled'] ?? 1));

        return $this->execute();
    }

    /**
     * Store support ticket.
     */
    public function createSupportTicket($userId, $email, $topic, $message) {
        $sql = "INSERT INTO support_tickets (user_id, email, topic, message)
                VALUES (:user_id, :email, :topic, :message)";

        $this->query($sql);
        $this->bind(':user_id', $userId);
        $this->bind(':email', $email);
        $this->bind(':topic', $topic);
        $this->bind(':message', $message);

        if (!$this->execute()) {
            return false;
        }

        return $this->lastInsertId();
    }

    /**
     * Store problem report.
     */
    public function createProblemReport($userId, $reportType, $details) {
        $sql = "INSERT INTO support_problem_reports (user_id, report_type, details)
                VALUES (:user_id, :report_type, :details)";

        $this->query($sql);
        $this->bind(':user_id', $userId);
        $this->bind(':report_type', $reportType);
        $this->bind(':details', $details);

        if (!$this->execute()) {
            return false;
        }

        return $this->lastInsertId();
    }

    /**
     * Store product feedback.
     */
    public function createFeedback($userId, $feedbackType, $message) {
        $sql = "INSERT INTO support_feedback (user_id, feedback_type, message)
                VALUES (:user_id, :feedback_type, :message)";

        $this->query($sql);
        $this->bind(':user_id', $userId);
        $this->bind(':feedback_type', $feedbackType);
        $this->bind(':message', $message);

        if (!$this->execute()) {
            return false;
        }

        return $this->lastInsertId();
    }

    /**
     * Check table existence before optional cleanup operations.
     */
    public function tableExists($tableName) {
        $sql = "SELECT 1 FROM information_schema.TABLES
                WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = :table_name LIMIT 1";

        $this->query($sql);
        $this->bind(':table_name', $tableName);

        return $this->single() !== false;
    }
    
    /**
     * Delete user account and all related data
     */
    public function deleteAccount($userId) {
        try {
            // Start transaction
            $this->beginTransaction();
            
            // Delete related data in order (delete child records first to avoid foreign key issues)
            
            // Delete certificates
            try {
                $this->query("DELETE FROM certificates WHERE user_id = :user_id");
                $this->bind(':user_id', $userId);
                $this->execute();
            } catch (Exception $e) {
                error_log("Error deleting certificates: " . $e->getMessage());
                throw new Exception("Failed to delete certificates: " . $e->getMessage());
            }
            
            // Delete comments
            $this->query("DELETE FROM comments WHERE user_id = :user_id");
            $this->bind(':user_id', $userId);
            $this->execute();
            
            // Delete post likes
            $this->query("DELETE FROM post_likes WHERE user_id = :user_id");
            $this->bind(':user_id', $userId);
            $this->execute();
            
            // Delete posts
            $this->query("DELETE FROM posts WHERE user_id = :user_id");
            $this->bind(':user_id', $userId);
            $this->execute();
            
            // Delete notifications (both sent and received)
            $this->query("DELETE FROM notifications WHERE receiver_id = :user_id");
            $this->bind(':user_id', $userId);
            $this->execute();
            
            // Delete follow requests (both sent and received)
            $this->query("DELETE FROM follow_requests WHERE requester_id = :user_id OR target_id = :user_id");
            $this->bind(':user_id', $userId);
            $this->execute();
            
            // Delete followers (both follower and followed)
            $this->query("DELETE FROM followers WHERE follower_id = :user_id OR followed_id = :user_id");
            $this->bind(':user_id', $userId);
            $this->execute();

            // Delete user blocks
            $this->query("DELETE FROM user_blocks WHERE user_id = :user_id OR blocked_user_id = :user_id");
            $this->bind(':user_id', $userId);
            $this->execute();

            // Delete user notification settings
            $this->query("DELETE FROM user_notification_settings WHERE user_id = :user_id");
            $this->bind(':user_id', $userId);
            $this->execute();

            // Delete user security settings
            $this->query("DELETE FROM user_security_settings WHERE user_id = :user_id");
            $this->bind(':user_id', $userId);
            $this->execute();

            // Delete support records
            $this->query("DELETE FROM support_tickets WHERE user_id = :user_id");
            $this->bind(':user_id', $userId);
            $this->execute();

            $this->query("DELETE FROM support_problem_reports WHERE user_id = :user_id");
            $this->bind(':user_id', $userId);
            $this->execute();

            $this->query("DELETE FROM support_feedback WHERE user_id = :user_id");
            $this->bind(':user_id', $userId);
            $this->execute();

            // Delete online user session tracking
            $this->query("DELETE FROM online_users WHERE user_id = :user_id");
            $this->bind(':user_id', $userId);
            $this->execute();
            
            // Delete message unread tracker (both sent and received)
            if ($this->tableExists('message_unread_tracker')) {
                $this->query("DELETE FROM message_unread_tracker WHERE sender_id = :user_id OR receiver_id = :user_id");
                $this->bind(':user_id', $userId);
                $this->execute();
            }
            
            // Delete messages
            $this->query("DELETE FROM messages WHERE sender_id = :user_id OR receiver_id = :user_id");
            $this->bind(':user_id', $userId);
            $this->execute();
            
            // Delete event attendees
            $this->query("DELETE FROM event_attendees WHERE user_id = :user_id");
            $this->bind(':user_id', $userId);
            $this->execute();
            
            // Delete event bookmarks
            $this->query("DELETE FROM event_bookmarks WHERE user_id = :user_id");
            $this->bind(':user_id', $userId);
            $this->execute();
            
            // Delete event images for events organized by this user
            $this->query("DELETE FROM event_images WHERE event_id IN (SELECT id FROM events WHERE organizer_id = :user_id)");
            $this->bind(':user_id', $userId);
            $this->execute();
            
            // Delete event requests
            $this->query("DELETE FROM event_requests WHERE user_id = :user_id");
            $this->bind(':user_id', $userId);
            $this->execute();
            
            // Delete events organized
            $this->query("DELETE FROM events WHERE organizer_id = :user_id");
            $this->bind(':user_id', $userId);
            $this->execute();
            
            // Delete user profile visibility
            $this->query("DELETE FROM user_profiles_visibility WHERE user_id = :user_id");
            $this->bind(':user_id', $userId);
            $this->execute();

            // Delete fundraising team memberships
            $this->query("DELETE FROM fundraising_team_members WHERE user_id = :user_id");
            $this->bind(':user_id', $userId);
            $this->execute();

            // Delete donations made by the user
            $this->query("DELETE FROM fundraising_donations WHERE donor_user_id = :user_id");
            $this->bind(':user_id', $userId);
            $this->execute();

            // Delete requests created by the user
            $this->query("DELETE FROM fundraising_requests WHERE user_id = :user_id");
            $this->bind(':user_id', $userId);
            $this->execute();

            // Delete projects and work experiences
            $this->query("DELETE FROM projects WHERE user_id = :user_id");
            $this->bind(':user_id', $userId);
            $this->execute();

            $this->query("DELETE FROM work_experiences WHERE user_id = :user_id");
            $this->bind(':user_id', $userId);
            $this->execute();
            
            // Finally delete user
            $this->query("DELETE FROM users WHERE id = :user_id");
            $this->bind(':user_id', $userId);
            $result = $this->execute();
            
            // Commit transaction
            $this->commit();
            
            return $result;
        } catch (Exception $e) {
            // Rollback on error
            $this->rollBack();
            throw $e;
        }
    }
}
?>