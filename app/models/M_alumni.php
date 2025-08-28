<?php
class M_Alumni {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }
    
    // Add your model methods here
    public function getUserDetails($userId) {
        $this->db->query("SELECT * FROM alumni_users WHERE id = :id");
        $this->db->bind(':id', $userId);
        return $this->db->single();
    }
    
    // Add other methods as needed
}