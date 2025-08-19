<?php
class M_Undergrad{
    protected $db;
    public function __construct() {
        $this->db = new Database();
    }

    public function getUserDetails($user_id){
        $this->db->query("SELECT * FROM Users WHERE id = :user_id");
        $this->db->bind(':user_id', $user_id);
        
        return $this->db->single();
    }
}
?>