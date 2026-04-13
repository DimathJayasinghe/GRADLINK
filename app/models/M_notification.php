<?php

class M_notification {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    private function getNotificationSettingsForUser(int $userId): ?object {
        $row = null;

        try {
            $this->db->query('SELECT mentions_enabled, followers_enabled, engagement_enabled, dnd_enabled, dnd_start, dnd_end, dnd_days, in_app_disabled_types FROM user_notification_settings WHERE user_id = :user_id LIMIT 1');
            $this->db->bind(':user_id', $userId);
            $row = $this->db->single();
        } catch (Throwable $e) {
            // Backward-compat: older schema may not have in_app_disabled_types
            try {
                $this->db->query('SELECT mentions_enabled, followers_enabled, engagement_enabled, dnd_enabled, dnd_start, dnd_end, dnd_days FROM user_notification_settings WHERE user_id = :user_id LIMIT 1');
                $this->db->bind(':user_id', $userId);
                $row = $this->db->single();
            } catch (Throwable $e2) {
                // If settings lookup fails, fail open (do not block notifications)
                error_log('[notifications] settings lookup failed: ' . $e2->getMessage());
                return null;
            }
        }

        if (!$row) {
            return null;
        }

        // Normalize to ints for consistent comparisons
        $row->mentions_enabled = (int)($row->mentions_enabled ?? 1);
        $row->followers_enabled = (int)($row->followers_enabled ?? 1);
        $row->engagement_enabled = (int)($row->engagement_enabled ?? 1);
        $row->dnd_enabled = (int)($row->dnd_enabled ?? 0);

        // Normalize per-type disabled list (JSON array)
        if (property_exists($row, 'in_app_disabled_types')) {
            if (is_string($row->in_app_disabled_types) && $row->in_app_disabled_types !== '') {
                $decoded = json_decode($row->in_app_disabled_types, true);
                $row->in_app_disabled_types = is_array($decoded) ? array_values($decoded) : [];
            } elseif (is_array($row->in_app_disabled_types)) {
                $row->in_app_disabled_types = array_values($row->in_app_disabled_types);
            } else {
                $row->in_app_disabled_types = [];
            }
        } else {
            $row->in_app_disabled_types = [];
        }

        return $row;
    }

    private function getSuppressedTypesForSettings(?object $settings): array {
        if ($settings === null) {
            return [];
        }

        $suppressed = [];

        // Per-type in-app toggles
        if (isset($settings->in_app_disabled_types) && is_array($settings->in_app_disabled_types)) {
            foreach ($settings->in_app_disabled_types as $t) {
                if (is_string($t) && $t !== '') {
                    $suppressed[] = $t;
                }
            }
        }

        if ((int)($settings->followers_enabled ?? 1) === 0) {
            $suppressed[] = 'follow_request';
            $suppressed[] = 'started_following';
        }

        if ((int)($settings->engagement_enabled ?? 1) === 0) {
            $suppressed[] = 'like';
            $suppressed[] = 'comment';
            $suppressed[] = 'share';
        }

        if ((int)($settings->mentions_enabled ?? 1) === 0) {
            // These types may be introduced later; keeping mapping here is harmless.
            $suppressed[] = 'mention';
            $suppressed[] = 'reply';
        }

        return array_values(array_unique($suppressed));
    }

    private function isTypeSuppressedForUser(int $receiverId, string $type): bool {
        $settings = $this->getNotificationSettingsForUser($receiverId);
        $suppressed = $this->getSuppressedTypesForSettings($settings);
        return in_array($type, $suppressed, true);
    }

    private function appendTypeNotInClause(string $baseSql, array $types, array &$bindMap): string {
        if (empty($types)) {
            return $baseSql;
        }

        $placeholders = [];
        foreach (array_values($types) as $i => $t) {
            $ph = ':type_ex_' . $i;
            $placeholders[] = $ph;
            $bindMap[$ph] = $t;
        }

        return $baseSql . ' AND type NOT IN (' . implode(',', $placeholders) . ')';
    }

    public function createNotification($receiverId, $type, $referenceId, $content) {
        try {
            $receiverId = (int)$receiverId;

            // Respect user's notification category preferences
            if ($receiverId > 0 && $this->isTypeSuppressedForUser($receiverId, (string)$type)) {
                return true; // Suppressed by settings; treat as successful no-op
            }

            // Ensure content is stored as JSON string
            $payload = is_string($content) ? $content : json_encode($content, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            // created_at and is_read have defaults per schema
            $this->db->query('INSERT INTO notifications (receiver_id, type, reference_id, content) VALUES (:receiver_id, :type, :reference_id, :content)');
            $this->db->bind(':receiver_id', $receiverId);
            $this->db->bind(':type', $type);
            $this->db->bind(':reference_id', (int)$referenceId);
            $this->db->bind(':content', $payload);
            return $this->db->execute();
        } catch (Throwable $e) {
            error_log('[notifications] insert failed: ' . $e->getMessage() . ' payload=' . json_encode([
                'receiverId' => $receiverId,
                'type' => $type,
                'referenceId' => $referenceId
            ]));
            return false;
        }
    }

    public function getUserNotifications($userId) {
        $userId = (int)$userId;
        $settings = $this->getNotificationSettingsForUser($userId);
        $suppressed = $this->getSuppressedTypesForSettings($settings);

        $bindMap = [':receiver_id' => $userId];
        $sql = 'SELECT * FROM notifications WHERE receiver_id = :receiver_id';
        $sql = $this->appendTypeNotInClause($sql, $suppressed, $bindMap);
        $sql .= ' ORDER BY created_at DESC';

        $this->db->query($sql);
        foreach ($bindMap as $k => $v) {
            $this->db->bind($k, $v);
        }
        $rows = $this->db->resultSet();
        // Decode JSON content for each row when possible
        foreach ($rows as $row) {
            if (isset($row->content)) {
                $decoded = json_decode($row->content, true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    $row->content = $decoded;
                }
            }
        }
        return $rows;
    }

    public function getUserNotificationsCount($userId) {
        $userId = (int)$userId;
        $settings = $this->getNotificationSettingsForUser($userId);
        $suppressed = $this->getSuppressedTypesForSettings($settings);

        $bindMap = [':receiver_id' => $userId];
        $sql = 'SELECT COUNT(*) AS count FROM notifications WHERE receiver_id = :receiver_id AND is_read = 0';
        $sql = $this->appendTypeNotInClause($sql, $suppressed, $bindMap);

        $this->db->query($sql);
        foreach ($bindMap as $k => $v) {
            $this->db->bind($k, $v);
        }
        $row = $this->db->single();
        return $row ? (int)$row->count : 0;
    }

    public function markNotificationAsRead($notificationId) {
        $this->db->query('UPDATE notifications SET is_read = 1 WHERE id = :notification_id');
        $this->db->bind(':notification_id', (int)$notificationId);
        return $this->db->execute();
    }

    public function markNotificationAsReadForUser($notificationId, $userId) {
        $this->db->query('UPDATE notifications SET is_read = 1 WHERE id = :notification_id AND receiver_id = :receiver_id');
        $this->db->bind(':notification_id', (int)$notificationId);
        $this->db->bind(':receiver_id', (int)$userId);
        return $this->db->execute();
    }

    public function markAllAsRead($userId) {
        $this->db->query('UPDATE notifications SET is_read = 1 WHERE receiver_id = :receiver_id AND is_read = 0 AND type <> :follow_type');
        $this->db->bind(':receiver_id', $userId);
        $this->db->bind(':follow_type', 'follow_request');
        return $this->db->execute();
    }

    public function getNotificationID($receiverId,$referenceId, $type) {
        $this->db->query('SELECT id FROM notifications WHERE receiver_id = :receiver_id AND reference_id = :reference_id AND type = :type ORDER BY created_at DESC LIMIT 1');
        $this->db->bind(':receiver_id', $receiverId);
        $this->db->bind(':reference_id', $referenceId);
        $this->db->bind(':type', $type);
        $row = $this->db->single();
        return $row ? (int)$row->id : null;
    }

    public function updateNotification($receiverId,$referenceId, $type, $content) {
        $payload = is_string($content) ? $content : json_encode($content, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        $this->db->query('UPDATE notifications SET content = :content WHERE receiver_id = :receiver_id AND reference_id = :reference_id AND type = :type ORDER BY created_at DESC LIMIT 1');
        $this->db->bind(':content', $payload); 
        $this->db->bind(':receiver_id', $receiverId);
        $this->db->bind(':reference_id', $referenceId);
        $this->db->bind(':type', $type);
        return $this->db->execute();
    }

    /**
     * Delete all new_message notifications from a specific user
     */
    public function markMessagesAsRead($currentUserId, $senderId) {
        $this->db->query('DELETE FROM notifications WHERE receiver_id = :receiver_id AND reference_id = :sender_id AND type = :type');
        $this->db->bind(':receiver_id', $currentUserId);
        $this->db->bind(':sender_id', $senderId);
        $this->db->bind(':type', 'new_message');
        return $this->db->execute();
    }

    /**
     * Check if there's an unread new_message notification from a specific sender
     */
    public function hasUnreadMessageNotification($receiverId, $senderId) {
        $this->db->query('SELECT id FROM notifications WHERE receiver_id = :receiver_id AND reference_id = :sender_id AND type = :type AND is_read = 0 LIMIT 1');
        $this->db->bind(':receiver_id', $receiverId);
        $this->db->bind(':sender_id', $senderId);
        $this->db->bind(':type', 'new_message');
        $row = $this->db->single();
        return $row ? true : false;
    }

    /**
     * Update existing unread message notification timestamp
     */
    public function updateMessageNotificationTime($receiverId, $senderId) {
        $this->db->query('UPDATE notifications SET created_at = CURRENT_TIMESTAMP WHERE receiver_id = :receiver_id AND reference_id = :sender_id AND type = :type AND is_read = 0');
        $this->db->bind(':receiver_id', $receiverId);
        $this->db->bind(':sender_id', $senderId);
        $this->db->bind(':type', 'new_message');
        return $this->db->execute();
    }
}
?>