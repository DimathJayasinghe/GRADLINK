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

        // Check if email already exists
        public function findUserByEmail($email) {
            $this->db->query("SELECT * FROM Users WHERE email = :email");
            $this->db->bind(':email', $email);
            
            $row = $this->db->single();
            
            // Check if row exists
            if($this->db->rowCount() > 0) {
                return true;
            } else {
                return false;
            }
        }

        // Login method
        public function login($email, $password) {
            $this->db->query("SELECT * FROM Users WHERE email = :email");
            $this->db->bind(':email', $email);
            
            $user = $this->db->single();
            
            if($user && password_verify($password, $user->password)) {
                return $user;
            } else {
                return false;
            }
        }

        // Save remember token
        public function saveRememberToken($userId, $tokenHash) {
            $this->db->query("UPDATE Users SET remember_token = :token WHERE id = :id");
            $this->db->bind(':token', $tokenHash);
            $this->db->bind(':id', $userId);
            
            return $this->db->execute();
        }

        // Verify remember token
        public function verifyRememberToken($userId, $tokenHash) {
            $this->db->query("SELECT * FROM Users WHERE id = :id AND remember_token = :token");
            $this->db->bind(':id', $userId);
            $this->db->bind(':token', $tokenHash);
            
            $user = $this->db->single();
            
            if ($this->db->rowCount() > 0) {
                return $user;
            } else {
                return false;
            }
        }

        // Register user method
        public function registerUser($data) {
            $this->db->query("INSERT INTO Users (name, password, email) VALUES (:name, :password, :email)");
            // Bind values
            $this->db->bind(':name', $data['name']);
            $this->db->bind(':password', password_hash($data['password'], PASSWORD_DEFAULT)); // Hash the password
            $this->db->bind(':email', $data['email']);

            // Execute and return success status
            return $this->db->execute();
        }
    }

?>