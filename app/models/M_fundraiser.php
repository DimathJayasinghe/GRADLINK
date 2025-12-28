<?php
class M_fundraiser{
    private $db = null;
    public function __construct(){
        $this->db = new Database();
    }
    public function searchUsers($query){
        $this->db->query(("SELECT id, display_name,name,email FROM users WHERE name LIKE :query OR display_name LIKE :query"));
        $this->db->bind(':query', '%' . $query . '%');
        return $this->db->resultSet();
    }
}
?>