<?php
class M_chat {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    // Fetch messages by chat ID
    public function getMessages($chatId) {
        $this->db->query("SELECT * FROM messages WHERE chat_id = :chat_id ORDER BY created_at ASC");
        $this->db->bind(':chat_id', $chatId);
        return $this->db->resultSet();
    }

    // Insert a new message
    public function addMessage($chatId, $userId, $message) {
        $this->db->query("INSERT INTO messages (chat_id, user_id, message, created_at) VALUES (:chat_id, :user_id, :message, NOW())");
        $this->db->bind(':chat_id', $chatId);
        $this->db->bind(':user_id', $userId);
        $this->db->bind(':message', $message);
        return $this->db->execute();
    }
}
