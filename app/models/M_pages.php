<?php
class M_Pages {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    

    // Additional methods for managing pages can be added here...
    public function getUsers(){
        $this->db->query("SELECT * FROM Users");
        return $this->db->resultSet();
    }
}
?>