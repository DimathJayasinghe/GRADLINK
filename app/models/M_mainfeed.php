<?php
class M_mainfeed{
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    // Method to fetch posts for the main feed
    public function getPosts() {
        $this->db->query("SELECT * FROM Posts ORDER BY created_at DESC");
        return $this->db->resultSet();
    }

    // Additional methods for managing main feed can be added here...
    
}
?>