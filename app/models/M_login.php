<?php
    class M_login{
        private $db;

        private function ensureSuspendedUsersTable(): void {
            try {
                $this->db->query("CREATE TABLE IF NOT EXISTS suspended_users (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    user_id INT NOT NULL,
                    suspended_by INT NOT NULL,
                    reason TEXT NULL,
                    status ENUM('active','lifted','removed') NOT NULL DEFAULT 'active',
                    suspended_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                    lifted_at DATETIME NULL,
                    lifted_by INT NULL,
                    removed_at DATETIME NULL,
                    removed_by INT NULL,
                    snapshot_name VARCHAR(255) NULL,
                    snapshot_email VARCHAR(255) NULL,
                    snapshot_role VARCHAR(50) NULL,
                    INDEX idx_suspended_users_user (user_id),
                    INDEX idx_suspended_users_status (status),
                    INDEX idx_suspended_users_suspended_at (suspended_at)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
                $this->db->execute();
            } catch (Exception $e) {
            }
        }

        public function __construct() {
            $this->db = new Database();
            $this->ensureSuspendedUsersTable();
        }

        public function getActiveSuspensionByEmail($email) {
            try {
                $this->db->query("SELECT
                                    su.id,
                                    su.reason,
                                    su.suspended_at,
                                    u.id AS user_id,
                                    u.role
                                  FROM users u
                                  INNER JOIN suspended_users su ON su.user_id = u.id AND su.status = 'active'
                                  WHERE u.email = :email
                                  ORDER BY su.suspended_at DESC
                                  LIMIT 1");
                $this->db->bind(':email', $email);
                return $this->db->single();
            } catch (Exception $e) {
                return false;
            }
        }
        
        public function loginUndergrad($email,$password){
            // Prepare SQL with role condition for undergraduate students
            $this->db->query('SELECT id,name,email,password,role,profile_image FROM users WHERE email = :email AND role = :role LIMIT 1');
            
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
            $this->db->query('SELECT id,name,email,password,role,profile_image,special_alumni FROM users WHERE email = :email AND role = :role LIMIT 1');
            
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

        /**
         * If alumni not found in users table, check the unregistered table and return status
         * Returns 'pending' | 'rejected' on password match, or false otherwise
         */
        public function checkUnregisteredAlumniStatus($email, $password) {
            $this->db->query('SELECT email, password, status FROM unregisted_alumni WHERE email = :email LIMIT 1');
            $this->db->bind(':email', $email);
            $row = $this->db->single();
            if (!$row) return false;
            // Verify the password
            if (!password_verify($password, $row->password)) return false;
            $status = strtolower(trim((string)($row->status ?? '')));
            if ($status === 'pending' || $status === 'rejected') {
                return $status;
            }
            return false;
        }
    }
?>