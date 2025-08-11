<?php
class M_auth{
    protected $db;
    public function __construct() {
        $this->db = new Database();
    }

    public function verifyRememberToken($userId, $tokenHash) {
        $this->db->query("SELECT * FROM Users WHERE id = :id AND remember_token = :token");
        $this->db->bind(':id', $userId);
        $this->db->bind(':token', $tokenHash);
        
        $user = $this->db->single();
        
        // Check if user exists
        if ($user) {
            return $user;
        } else {
            return false;
        }
    }

    public function saveRememberToken($userId, $tokenHash){
        $this->db->query("UPDATE Users SET remember_token = :token WHERE id = :id");
        $this->db->bind(':token', $tokenHash);
        $this->db->bind(':id', $userId);
        
        return $this->db->execute();
    }

    public function login($email, $password){
        $this->db->query("SELECT * FROM Users WHERE email = :email");
        $this->db->bind(':email', $email);
        
        $user = $this->db->single();
        
        if ($user && password_verify($password, $user->password)) {
            return $user;
        } else {
            return false;
        }
    }
}
?>