<?php
    class M_login{
        private $db;

        public function __construct() {
            $this->db = new Database();
        }
        
        public function loginUndergrad($email,$password){
            // Prepare SQL with role condition for undergraduate students
            $this->db->query('SELECT * FROM users WHERE email = :email AND role = :role LIMIT 1');
            
            // Bind values
            $this->db->bind(':email', $email);
            $this->db->bind(':role', 'undergrad'); // Assuming 'undergrad' is the role identifier
            
            // Get user record
            $user = $this->db->single();
            
            // Check if user exists and verify password
            if ($user && password_verify($password, $user->password)) {
                return $user; // Return user object
            }
            
            return false; // Authentication failed
        }
        
        public function loginAlumni($email,$password){
            // Prepare SQL with role condition for alumni users
            $this->db->query('SELECT * FROM users WHERE email = :email AND role = :role LIMIT 1');
            
            // Bind values
            $this->db->bind(':email', $email);
            $this->db->bind(':role', 'alumni'); // Assuming 'alumni' is the role identifier
            
            // Get user record
            $user = $this->db->single();
            
            // Check if user exists and verify password
            if ($user && password_verify($password, $user->password)) {
                return $user; // Return user object if authentication succeeds
            }
            
            return false; // Authentication failed
        }
    }
?>