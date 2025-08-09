<?php
    class M_users{
        private $db;

        public function __construct() {
            $this->db = new Database();
        }

        // Method to get all users
        public function getUsers(){
            $this->db->query("SELECT * FROM Users");
            return $this->db->resultSet();
        }

        // Additional methods for managing users can be added here...
    }

?>