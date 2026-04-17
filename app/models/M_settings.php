<?php
class M_settings extends Database {

    const ACTION_DEACTIVATE_ONLY = 'deactivate_only';
    const ACTION_DEACTIVATE_AND_DELETE = 'deactivate_and_delete';
    private static $notificationSettingsSchemaChecked = false;

    /**
     * Ensure notification settings table exists.
     * This keeps notification settings APIs resilient on fresh/local DBs.
     */
    private function ensureNotificationSettingsTable() {
        if (self::$notificationSettingsSchemaChecked) {
            return true;
        }

        try {
            if (!$this->tableExists('user_notification_settings')) {
                $sql = "CREATE TABLE user_notification_settings (
                            user_id INT PRIMARY KEY,
                            email_enabled TINYINT(1) NOT NULL DEFAULT 0,
                            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
                        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

                $this->query($sql);
                if (!$this->execute()) {
                    return false;
                }
            }

            if (!$this->columnExists('user_notification_settings', 'email_enabled')) {
                $this->query('ALTER TABLE user_notification_settings ADD COLUMN email_enabled TINYINT(1) NOT NULL DEFAULT 0');
                if (!$this->execute()) {
                    return false;
                }
            }

            // Force default-off behavior for users who have no explicit preference saved.
            $this->query('ALTER TABLE user_notification_settings MODIFY COLUMN email_enabled TINYINT(1) NOT NULL DEFAULT 0');
            if (!$this->execute()) {
                return false;
            }

            if (!$this->columnExists('user_notification_settings', 'updated_at')) {
                $this->query('ALTER TABLE user_notification_settings ADD COLUMN updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP');
                if (!$this->execute()) {
                    return false;
                }
            }

            $legacyColumns = [
                'sound_enabled',
                'mentions_enabled',
                'followers_enabled',
                'engagement_enabled',
                'dnd_enabled',
                'dnd_start',
                'dnd_end',
                'dnd_days',
                'in_app_disabled_types',
            ];

            foreach ($legacyColumns as $column) {
                if ($this->columnExists('user_notification_settings', $column)) {
                    $this->query("ALTER TABLE user_notification_settings DROP COLUMN {$column}");
                    if (!$this->execute()) {
                        return false;
                    }
                }
            }

            self::$notificationSettingsSchemaChecked = true;

            return true;
        } catch (Throwable $e) {
            error_log('[settings] ensureNotificationSettingsTable failed: ' . $e->getMessage());
            return false;
        }
    }
    
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
     * Convert undergrad account to alumni.
     */
    public function convertUndergradToAlumni($userId) {
        $sql = "UPDATE users
                SET role = 'alumni'
                WHERE id = :user_id AND role = 'undergrad'";

        $this->query($sql);
        $this->bind(':user_id', (int)$userId);

        if (!$this->execute()) {
            return false;
        }

        return $this->rowCount() > 0;
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
        if (!$this->ensureNotificationSettingsTable()) {
            return false;
        }

        try {
            $sql = "SELECT user_id, email_enabled, updated_at FROM user_notification_settings WHERE user_id = :user_id LIMIT 1";
            $this->query($sql);
            $this->bind(':user_id', $userId);

            return $this->single();
        } catch (Throwable $e) {
            error_log('[settings] getNotificationSettings failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Upsert notification preferences for current user.
     */
    public function upsertNotificationSettings($userId, $settings) {
        if (!$this->ensureNotificationSettingsTable()) {
            return false;
        }

        $emailEnabled = (int)($settings['email_enabled'] ?? 0) === 1 ? 1 : 0;

        try {
            $sql = "INSERT INTO user_notification_settings (user_id, email_enabled)
                    VALUES (:user_id, :email_enabled)
                    ON DUPLICATE KEY UPDATE email_enabled = VALUES(email_enabled)";

            $this->query($sql);
            $this->bind(':user_id', (int)$userId);
            $this->bind(':email_enabled', $emailEnabled);

            return $this->execute();
        } catch (Throwable $e) {
            error_log('[settings] upsertNotificationSettings failed: ' . $e->getMessage());
            return false;
        }
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
     * Check column existence for compatibility across DB versions.
     */
    public function columnExists($tableName, $columnName) {
        $sql = "SELECT 1 FROM information_schema.COLUMNS
                WHERE TABLE_SCHEMA = DATABASE()
                  AND TABLE_NAME = :table_name
                  AND COLUMN_NAME = :column_name
                LIMIT 1";

        $this->query($sql);
        $this->bind(':table_name', $tableName);
        $this->bind(':column_name', $columnName);

        return $this->single() !== false;
    }

    /**
     * List current user's support tickets.
     */
    public function getSupportTicketsByUser($userId, $limit = 10) {
        $limit = (int)$limit;
        if ($limit <= 0) {
            $limit = 10;
        }

        $selectAdminReply = $this->columnExists('support_tickets', 'admin_reply') ? ', admin_reply, admin_replied_at' : '';
        $sql = "SELECT id, user_id, email, topic, message, status, created_at, updated_at{$selectAdminReply}
                FROM support_tickets
                WHERE user_id = :user_id
                ORDER BY created_at DESC
                LIMIT {$limit}";

        $this->query($sql);
        $this->bind(':user_id', (int)$userId);

        return $this->resultSet();
    }

    /**
     * List current user's problem reports.
     */
    public function getProblemReportsByUser($userId, $limit = 10) {
        $limit = (int)$limit;
        if ($limit <= 0) {
            $limit = 10;
        }

        $selectAdminReply = $this->columnExists('support_problem_reports', 'admin_reply') ? ', admin_reply, admin_replied_at' : '';
        $sql = "SELECT id, user_id, report_type, details, status, created_at, updated_at{$selectAdminReply}
                FROM support_problem_reports
                WHERE user_id = :user_id
                ORDER BY created_at DESC
                LIMIT {$limit}";

        $this->query($sql);
        $this->bind(':user_id', (int)$userId);

        return $this->resultSet();
    }

    /**
     * Update a support ticket only while it's still editable.
     * Editable means: owned by user, status is still 'open', and (if available) no admin_reply.
     */
    public function updateSupportTicket($userId, $ticketId, $email, $topic, $message) {
        $hasAdminReply = $this->columnExists('support_tickets', 'admin_reply');

        $sql = "UPDATE support_tickets
                SET email = :email,
                    topic = :topic,
                    message = :message
                WHERE id = :id
                  AND user_id = :user_id
                  AND status = 'open'";

        if ($hasAdminReply) {
            $sql .= " AND (admin_reply IS NULL OR admin_reply = '')";
        }

        $this->query($sql);
        $this->bind(':id', (int)$ticketId);
        $this->bind(':user_id', (int)$userId);
        $this->bind(':email', $email);
        $this->bind(':topic', $topic);
        $this->bind(':message', $message);

        if (!$this->execute()) {
            return false;
        }

        return $this->rowCount() > 0;
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
     * Update a problem report only while it's still editable.
     * Editable means: owned by user, status is still 'pending', and (if available) no admin_reply.
     */
    public function updateProblemReport($userId, $reportId, $reportType, $details) {
        $hasAdminReply = $this->columnExists('support_problem_reports', 'admin_reply');

        $sql = "UPDATE support_problem_reports
                SET report_type = :report_type,
                    details = :details
                WHERE id = :id
                  AND user_id = :user_id
                  AND status = 'pending'";

        if ($hasAdminReply) {
            $sql .= " AND (admin_reply IS NULL OR admin_reply = '')";
        }

        $this->query($sql);
        $this->bind(':id', (int)$reportId);
        $this->bind(':user_id', (int)$userId);
        $this->bind(':report_type', $reportType);
        $this->bind(':details', $details);

        if (!$this->execute()) {
            return false;
        }

        return $this->rowCount() > 0;
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
     * Get pending lifecycle action for a user.
     */
    public function getPendingLifecycleAction($userId) {
        $sql = "SELECT * FROM account_lifecycle_actions
                WHERE user_id = :user_id AND status = 'pending'
                ORDER BY id DESC
                LIMIT 1";

        $this->query($sql);
        $this->bind(':user_id', $userId);

        return $this->single();
    }

    /**
     * Schedule account deactivation/deletion lifecycle action.
     */
    public function scheduleAccountLifecycleAction($userId, $actionType, $reason = null, $otherReason = null) {
        if (!in_array($actionType, [self::ACTION_DEACTIVATE_ONLY, self::ACTION_DEACTIVATE_AND_DELETE], true)) {
            return false;
        }

        $deactivatedAt = new DateTime('now');

        if ($actionType === self::ACTION_DEACTIVATE_ONLY) {
            $reactivateAt = (clone $deactivatedAt)->modify('+30 days');
            $deleteAt = null;
        } else {
            $reactivateAt = (clone $deactivatedAt)->modify('+30 days');
            $deleteAt = clone $reactivateAt;
        }

        $sql = "INSERT INTO account_lifecycle_actions (
                    user_id,
                    action_type,
                    status,
                    reason,
                    other_reason,
                    deactivated_at,
                    reactivate_at,
                    delete_at,
                    processed_at,
                    created_at,
                    updated_at
                ) VALUES (
                    :user_id,
                    :action_type,
                    'pending',
                    :reason,
                    :other_reason,
                    :deactivated_at,
                    :reactivate_at,
                    :delete_at,
                    NULL,
                    NOW(),
                    NOW()
                )
                ON DUPLICATE KEY UPDATE
                    action_type = VALUES(action_type),
                    status = 'pending',
                    reason = VALUES(reason),
                    other_reason = VALUES(other_reason),
                    deactivated_at = VALUES(deactivated_at),
                    reactivate_at = VALUES(reactivate_at),
                    delete_at = VALUES(delete_at),
                    processed_at = NULL,
                    updated_at = NOW()";

        $this->query($sql);
        $this->bind(':user_id', $userId);
        $this->bind(':action_type', $actionType);
        $this->bind(':reason', $reason);
        $this->bind(':other_reason', $otherReason);
        $this->bind(':deactivated_at', $deactivatedAt->format('Y-m-d H:i:s'));
        $this->bind(':reactivate_at', $reactivateAt->format('Y-m-d H:i:s'));
        $this->bind(':delete_at', $deleteAt ? $deleteAt->format('Y-m-d H:i:s') : null);

        return $this->execute();
    }

    /**
     * Resolve pending lifecycle action during login.
     *
     * - deactivate_only: user login reactivates account.
     * - deactivate_and_delete: if delete window passed, delete account; otherwise login reactivates account.
     */
    public function handleLifecycleOnLogin($userId) {
        $action = $this->getPendingLifecycleAction($userId);
        if (!$action) {
            return ['status' => 'none'];
        }

        $nowTs = time();
        $deleteAtTs = !empty($action->delete_at) ? strtotime($action->delete_at) : null;

        if (
            $action->action_type === self::ACTION_DEACTIVATE_AND_DELETE &&
            $deleteAtTs !== null &&
            $nowTs >= $deleteAtTs
        ) {
            $deleted = $this->deleteAccount($userId);
            if (!$deleted) {
                return ['status' => 'error'];
            }

            $this->query("UPDATE account_lifecycle_actions
                          SET status = 'deleted', processed_at = NOW(), updated_at = NOW()
                          WHERE user_id = :user_id");
            $this->bind(':user_id', $userId);
            $this->execute();

            return ['status' => 'deleted'];
        }

        $this->query("UPDATE account_lifecycle_actions
                      SET status = 'reactivated', processed_at = NOW(), updated_at = NOW()
                      WHERE user_id = :user_id AND status = 'pending'");
        $this->bind(':user_id', $userId);
        $this->execute();

        return ['status' => 'reactivated', 'action_type' => $action->action_type];
    }
    
    /**
     * Delete user account and all related data
     */
    public function deleteAccount($userId) {
        $userId = (int)$userId;
        if ($userId <= 0) {
            return false;
        }

        try {
            $this->beginTransaction();
            $userEmail = null;
            try {
                $this->query("SELECT email FROM users WHERE id = :user_id LIMIT 1");
                $this->bind(':user_id', $userId);
                $userRow = $this->single();
                $userEmail = isset($userRow->email) ? (string)$userRow->email : null;
            } catch (Throwable $ignored) {
            }

            $cleanupQueries = [
                "DELETE FROM certificates WHERE user_id = :user_id",
                "DELETE FROM comments WHERE user_id = :user_id",
                "DELETE FROM post_likes WHERE user_id = :user_id",
                "DELETE FROM post_reports WHERE reporter_id = :user_id OR post_owner_id = :user_id",
                "UPDATE reports SET reviewed_by = NULL WHERE reviewed_by = :user_id",
                "DELETE FROM reports WHERE reporter_id = :user_id",
                "DELETE FROM posts WHERE user_id = :user_id",
                "DELETE FROM notifications WHERE receiver_id = :user_id",
                "DELETE FROM notifications WHERE receiver_id = :user_id OR sender_id = :user_id",
                "DELETE FROM follow_requests WHERE requester_id = :user_id OR target_id = :user_id",
                "DELETE FROM followers WHERE follower_id = :user_id OR followed_id = :user_id",
                "DELETE FROM user_blocks WHERE user_id = :user_id OR blocked_user_id = :user_id",
                "DELETE FROM user_blocks WHERE blocker_id = :user_id OR blocked_id = :user_id",
                "DELETE FROM user_notification_settings WHERE user_id = :user_id",
                "DELETE FROM user_security_settings WHERE user_id = :user_id",
                "DELETE FROM support_tickets WHERE user_id = :user_id",
                "DELETE FROM support_problem_reports WHERE user_id = :user_id",
                "DELETE FROM support_feedback WHERE user_id = :user_id",
                "DELETE FROM online_users WHERE user_id = :user_id",
                "DELETE FROM access_logs WHERE user_id = :user_id",
                "DELETE FROM message_unread_tracker WHERE sender_id = :user_id OR receiver_id = :user_id",
                "DELETE FROM messages WHERE sender_id = :user_id OR receiver_id = :user_id",
                "DELETE FROM event_attendees WHERE user_id = :user_id",
                "DELETE FROM event_bookmarks WHERE user_id = :user_id",
                "DELETE FROM bookmarks WHERE user_id = :user_id",
                "DELETE FROM event_images WHERE event_id IN (SELECT id FROM events WHERE organizer_id = :user_id)",
                "DELETE FROM event_requests WHERE user_id = :user_id",
                "DELETE FROM events WHERE organizer_id = :user_id",
                "DELETE FROM user_profiles_visibility WHERE user_id = :user_id",
                "DELETE FROM fundraising_team_members WHERE user_id = :user_id",
                "DELETE FROM fundraising_bank_details WHERE request_id IN (SELECT id FROM fundraising_requests WHERE user_id = :user_id)",
                "DELETE FROM fundraising_donations WHERE donor_user_id = :user_id OR request_id IN (SELECT id FROM fundraising_requests WHERE user_id = :user_id)",
                "DELETE FROM fundraising_requests WHERE user_id = :user_id OR advisor_id = :user_id",
                "DELETE FROM projects WHERE user_id = :user_id",
                "DELETE FROM work_experiences WHERE user_id = :user_id",
                "DELETE FROM account_lifecycle_actions WHERE user_id = :user_id",
                "DELETE FROM suspended_users WHERE user_id = :user_id",
                "DELETE FROM suspended_users WHERE suspended_by = :user_id OR lifted_by = :user_id OR removed_by = :user_id"
            ];

            foreach ($cleanupQueries as $sql) {
                try {
                    $this->query($sql);
                    $this->bind(':user_id', $userId);
                    $this->execute();
                } catch (Throwable $ignored) {
                    // Keep account deletion resilient across schema variants.
                }
            }

            if (!empty($userEmail)) {
                try {
                    $this->query("DELETE FROM email_otps WHERE email = :email");
                    $this->bind(':email', $userEmail);
                    $this->execute();
                } catch (Throwable $ignored) {
                }

                try {
                    $this->query("DELETE FROM unregisted_alumni WHERE email = :email");
                    $this->bind(':email', $userEmail);
                    $this->execute();
                } catch (Throwable $ignored) {
                }
            }

            $this->query("DELETE FROM users WHERE id = :user_id");
            $this->bind(':user_id', $userId);
            if (!$this->execute()) {
                $this->rollBack();
                return false;
            }

            if ($this->rowCount() < 1) {
                $this->rollBack();
                return false;
            }

            $this->commit();

            return true;
        } catch (Throwable $e) {
            try {
                $this->rollBack();
            } catch (Throwable $ignored) {
            }

            error_log('[settings] deleteAccount failed: ' . $e->getMessage());
            return false;
        }
    }
}
?>