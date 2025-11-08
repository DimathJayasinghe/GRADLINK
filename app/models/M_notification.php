<?php

class M_notification {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    public function createNotification($receiverId, $type, $referenceId, $content) {
        try {
            // Ensure content is stored as JSON string
            $payload = is_string($content) ? $content : json_encode($content, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            // created_at and is_read have defaults per schema
            $this->db->query('INSERT INTO notifications (receiver_id, type, reference_id, content) VALUES (:receiver_id, :type, :reference_id, :content)');
            $this->db->bind(':receiver_id', (int)$receiverId);
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
        $this->db->query('SELECT * FROM notifications WHERE receiver_id = :receiver_id ORDER BY created_at DESC');
        $this->db->bind(':receiver_id', $userId);
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
        $this->db->query('SELECT COUNT(*) AS count FROM notifications WHERE receiver_id = :receiver_id AND is_read = 0');
        $this->db->bind(':receiver_id', $userId);
        $row = $this->db->single();
        return $row ? (int)$row->count : 0;
    }

    public function markNotificationAsRead($notificationId) {
        $this->db->query('UPDATE notifications SET is_read = 1 WHERE id = :notification_id');
        $this->db->bind(':notification_id', $notificationId);
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
}
?>