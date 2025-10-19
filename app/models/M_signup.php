<?php
class M_signup {
    private $db;
    
    public function __construct() {
        $this->db = new Database();
    }
    
    /**
     * Find a user by email
     * 
     * @param string $email User email to search for
     * @return object|bool User object if found, false otherwise
     */
    public function findUserByEmail($email) {
        $this->db->query("SELECT * FROM users WHERE email = :email");
        $this->db->bind(':email', $email);
        
        $row = $this->db->single();
        
        // Check if row exists
        if($this->db->rowCount() > 0) {
            return $row;
        } else {
            return false;
        }
    }
    
    /**
     * Register an alumni user
     * 
     * @param array $data User data
     * @return int|bool User ID on success, false on failure
     */
    public function registerAlumni($data) {
        // Prepare SQL statement
        $this->db->query("INSERT INTO users (name, email, password, role, display_name, profile_image, bio, skills, nic, batch_no,special_alumni) 
                          VALUES (:name, :email, :password, :role, :display_name, :profile_image, :bio, :skills, :nic, :batch_no)");
        
        // Bind values
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':email', $data['email']);
        $this->db->bind(':password', $data['password']);
        $this->db->bind(':role', $data['role']);
        $this->db->bind(':display_name', $data['display_name'] ?? $data['name']);
        $this->db->bind(':profile_image', 'default.jpg');
        $this->db->bind(':bio', $data['bio'] ?? null);
        $this->db->bind(':skills', $data['skills_json'] ?? null);
        $this->db->bind(':nic', $data['nic'] ?? null);
        $this->db->bind(':batch_no', $data['batch_no'] ?? null);
        $this->db->bind('special_alumni',false);
        
        // Execute
        if($this->db->execute()) {
            // Since dbh is private in Database class, we need to get last ID differently
            $this->db->query("SELECT LAST_INSERT_ID() as id");
            $result = $this->db->single();
            return $result->id;
        } else {
            return false;
        }
    }
    
    /**
     * Register an undergraduate user
     * 
     * @param array $data User data
     * @return int|bool User ID on success, false on failure
     */
    public function registerUndergrad($data) {
        // Prepare SQL statement
        $this->db->query("INSERT INTO users (name, email, password, role, display_name, profile_image, bio, skills, student_id, batch_no) 
                          VALUES (:name, :email, :password, :role, :display_name, :profile_image, :bio, :skills, :student_id, :batch_no)");
        
        // Bind values
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':email', $data['email']);
        $this->db->bind(':password', $data['password']);
        $this->db->bind(':role', $data['role']);
        $this->db->bind(':display_name', $data['display_name'] ?? $data['name']);
        $this->db->bind(':profile_image', 'default.jpg');
        $this->db->bind(':bio', $data['bio'] ?? null);
        $this->db->bind(':skills', $data['skills_json'] ?? null);
        $this->db->bind(':student_id', $data['student_id'] ?? null);
        $this->db->bind(':batch_no', $data['batch_no'] ?? null);
        
        // Execute
        if($this->db->execute()) {
            // Since dbh is private in Database class, we need to get last ID differently
            $this->db->query("SELECT LAST_INSERT_ID() as id");
            $result = $this->db->single();
            return $result->id;
        } else {
            return false;
        }
    }
    
    /**
     * Get user by ID
     * 
     * @param int $id User ID
     * @return object|bool User object if found, false otherwise
     */
    public function getUserById($id) {
        $this->db->query("SELECT * FROM users WHERE id = :id");
        $this->db->bind(':id', $id);
        
        $row = $this->db->single();
        
        // Check if row exists
        if($this->db->rowCount() > 0) {
            return $row;
        } else {
            return false;
        }
    }
    
    /**
     * Update user's profile image
     * 
     * @param int $userId User ID
     * @param string $filename New profile image filename
     * @return bool True on success, false on failure
     */
    public function updateProfileImage($userId, $filename) {
        $this->db->query("UPDATE users SET profile_image = :filename WHERE id = :id");
        $this->db->bind(':filename', $filename);
        $this->db->bind(':id', $userId);
        
        // Execute
        return $this->db->execute();
    }
}
?>