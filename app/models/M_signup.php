<?php
class M_signup
{
    private $db;

    private function ensureSuspendedUsersTable(): void
    {
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

    public function __construct()
    {
        $this->db = new Database();
        $this->ensureSuspendedUsersTable();
    }

    /**
     * Find a pending alumni by email (in unregisted_alumni)
     */
    public function findPendingAlumniByEmail($email)
    {
        $this->db->query("SELECT * FROM unregisted_alumni WHERE email = :email");
        $this->db->bind(':email', $email);
        $row = $this->db->single();
        if ($this->db->rowCount() > 0) {
            return $row;
        }
        return false;
    }

    /**
     * Find a user by email
     * 
     * @param string $email User email to search for
     * @return object|bool User object if found, false otherwise
     */
    public function findUserByEmail($email)
    {
        $this->db->query("SELECT * FROM users WHERE email = :email");
        $this->db->bind(':email', $email);

        $row = $this->db->single();

        // Check if row exists
        if ($this->db->rowCount() > 0) {
            return $row;
        } else {
            return false;
        }
    }

    /**
     * Check whether an email belongs to an actively suspended account.
     */
    public function getActiveSuspensionByEmail($email)
    {
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

    /**
     * Register an alumni user
     * 
     * @param array $data User data
     * @return int|bool User ID on success, false on failure
     */
    public function registerAlumni($data)
    {
        // Prepare SQL statement
        $this->db->query("INSERT INTO users (name, email, password, role, display_name, profile_image, bio, skills, nic, batch_no) 
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

        // Execute
        if ($this->db->execute()) {
            // Since dbh is private in Database class, we need to get last ID differently
            $this->db->query("SELECT LAST_INSERT_ID() as id");
            $result = $this->db->single();
            return $result->id;
        } else {
            return false;
        }
    }

    /**
     * Register a pending alumni into unregisted_alumni table
     * Returns ID on success, false on failure
     */
    public function registerPendingAlumni($data)
    {
        $this->db->query("INSERT INTO unregisted_alumni (name, email, password, role, display_name, gender, profile_image, bio, explain_yourself, skills, nic, batch_no) 
                          VALUES (:name, :email, :password, 'alumni', :display_name, :gender, :profile_image, :bio, :explain_yourself, :skills, :nic, :batch_no)");

        $this->db->bind(':name', $data['name']);
        $this->db->bind(':email', $data['email']);
        $this->db->bind(':password', $data['password']);
        $this->db->bind(':display_name', $data['display_name'] ?? $data['name']);
        $this->db->bind(':gender', isset($data['gender']) && in_array($data['gender'], ['male', 'female'], true) ? $data['gender'] : null);
        $this->db->bind(':profile_image', 'default.jpg');
        $this->db->bind(':bio', $data['bio'] ?? null);
        $this->db->bind(':explain_yourself', $data['explain_yourself'] ?? null);
        $this->db->bind(':skills', $data['skills_json'] ?? null);
        $this->db->bind(':nic', $data['nic'] ?? null);
        $this->db->bind(':batch_no', $data['batch_no'] ?? null);

        if ($this->db->execute()) {
            $this->db->query("SELECT LAST_INSERT_ID() as id");
            $result = $this->db->single();
            return $result->id;
        }
        return false;
    }

    /**
     * Register an undergraduate user
     * 
     * @param array $data User data
     * @return int|bool User ID on success, false on failure
     */
    public function registerUndergrad($data)
    {
        // Prepare SQL statement
        $this->db->query("INSERT INTO users (name, email, password, role, display_name, gender, profile_image, bio, skills, student_id, batch_no) 
              VALUES (:name, :email, :password, :role, :display_name, :gender, :profile_image, :bio, :skills, :student_id, :batch_no)");

        // Bind values
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':email', $data['email']);
        $this->db->bind(':password', $data['password']);
        $this->db->bind(':role', $data['role']);
        $this->db->bind(':display_name', $data['display_name'] ?? $data['name']);
        $this->db->bind(':gender', isset($data['gender']) && in_array($data['gender'], ['male', 'female'], true) ? $data['gender'] : null);
        $this->db->bind(':profile_image', 'default.jpg');
        $this->db->bind(':bio', $data['bio'] ?? null);
        $this->db->bind(':skills', $data['skills_json'] ?? null);
        $this->db->bind(':student_id', $data['student_id'] ?? null);
        $this->db->bind(':batch_no', $data['batch_no'] ?? null);

        // Execute
        if ($this->db->execute()) {
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
    public function getUserById($id)
    {
        $this->db->query("SELECT * FROM users WHERE id = :id");
        $this->db->bind(':id', $id);

        $row = $this->db->single();

        // Check if row exists
        if ($this->db->rowCount() > 0) {
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
    public function updateProfileImage($userId, $filename)
    {
        $this->db->query("UPDATE users SET profile_image = :filename WHERE id = :id");
        $this->db->bind(':filename', $filename);
        $this->db->bind(':id', $userId);

        // Execute
        return $this->db->execute();
    }

    /**
     * Update pending profile image
     */
    public function updatePendingProfileImage($pendingId, $filename)
    {
        $this->db->query("UPDATE unregisted_alumni SET profile_image = :filename WHERE id = :id");
        $this->db->bind(':filename', $filename);
        $this->db->bind(':id', $pendingId);
        return $this->db->execute();
    }

    /**
     * Approve a pending alumni: move from unregisted_alumni to users
     * Returns new user id on success
     */
    public function approveAlumni($pendingId)
    {
        // fetch pending record
        $this->db->query("SELECT * FROM unregisted_alumni WHERE id = :id AND status = 'pending'");
        $this->db->bind(':id', $pendingId);
        $pending = $this->db->single();
        if (!$pending) return false;

        try {
            // Begin transaction
            $this->db->beginTransaction();

            // Insert into users
            $this->db->query("INSERT INTO users (name, email, password, role, display_name, gender, profile_image, bio, skills, nic, batch_no) 
                              VALUES (:name, :email, :password, 'alumni', :display_name, :gender, :profile_image, :bio, :skills, :nic, :batch_no)");
            $this->db->bind(':name', $pending->name);
            $this->db->bind(':email', $pending->email);
            $this->db->bind(':password', $pending->password);
            $this->db->bind(':display_name', $pending->display_name ?? $pending->name);
            $this->db->bind(':gender', $pending->gender ?? null);
            $this->db->bind(':profile_image', $pending->profile_image ?? 'default.jpg');
            $this->db->bind(':bio', $pending->bio ?? null);
            $this->db->bind(':skills', $pending->skills ?? null);
            $this->db->bind(':nic', $pending->nic ?? null);
            $this->db->bind(':batch_no', $pending->batch_no ?? null);
            if (!$this->db->execute()) {
                $this->db->rollBack();
                return false;
            }

            // get inserted id
            $this->db->query("SELECT LAST_INSERT_ID() as id");
            $result = $this->db->single();
            $newUserId = $result->id ?? null;
            if (!$newUserId) {
                $this->db->rollBack();
                return false;
            }

            // delete the pending row after successful approval
            $this->db->query("DELETE FROM unregisted_alumni WHERE id = :id");
            $this->db->bind(':id', $pendingId);
            if (!$this->db->execute()) {
                $this->db->rollBack();
                return false;
            }

            $this->db->commit();
            return $newUserId;
        } catch (Exception $e) {
            // Ensure rollback on any exception
            if (method_exists($this->db, 'rollBack')) {
                $this->db->rollBack();
            }
            return false;
        }
    }

    /**
     * Reject a pending alumni by marking status = 'rejected'
     */
    public function rejectPendingAlumni($pendingId)
    {
        $this->db->query("UPDATE unregisted_alumni SET status = 'rejected' WHERE id = :id AND status <> 'rejected'");
        $this->db->bind(':id', $pendingId);
        return $this->db->execute();
    }
    /**
     * Save OTP to database (replaces any existing OTP for same email/purpose)
     * 
     * @param string $email User email
     * @param string $otp 6-digit OTP
     * @param string $purpose Purpose of OTP (signup/login)
     * @param string $expires Expiry datetime string
     * @return bool True on success, false on failure
     */
    public function saveOTP($email, $otp, $purpose)
    {
        // Delete any existing OTP for this email/purpose
        $this->db->query("DELETE FROM email_otps WHERE email = :email AND purpose = :purpose");
        $this->db->bind(':email', $email);
        $this->db->bind(':purpose', $purpose);
        $this->db->execute();

        // Insert new OTP
        $this->db->query("INSERT INTO email_otps (email, otp, purpose, expires_at) 
                          VALUES (:email, :otp, :purpose, DATE_ADD(NOW(), INTERVAL 5 MINUTE))");
        $this->db->bind(':email', $email);
        $this->db->bind(':otp', $otp);
        $this->db->bind(':purpose', $purpose);

        return $this->db->execute();
    }

    /**
     * Verify OTP against database
     * 
     * @param string $email User email
     * @param string $otp OTP to verify
     * @param string $purpose Purpose of OTP (signup/login)
     * @return array ['success' => bool, 'message' => string]
     */
    public function verifyOTP($email, $otp, $purpose)
    {
        // Find valid OTP
        $this->db->query("SELECT * FROM email_otps 
                          WHERE email = :email 
                          AND otp = :otp 
                          AND purpose = :purpose 
                          AND is_used = 0 
                          AND expires_at > NOW()");
        $this->db->bind(':email', $email);
        $this->db->bind(':otp', $otp);
        $this->db->bind(':purpose', $purpose);

        $row = $this->db->single();

        if ($this->db->rowCount() > 0) {
            // Mark OTP as used
            $this->db->query("UPDATE email_otps SET is_used = 1 WHERE id = :id");
            $this->db->bind(':id', $row->id);
            $this->db->execute();

            return ['success' => true, 'message' => 'Email verified successfully'];
        }

        // Check if OTP exists but expired
        $this->db->query("SELECT * FROM email_otps 
                          WHERE email = :email 
                          AND otp = :otp 
                          AND purpose = :purpose 
                          AND is_used = 0");
        $this->db->bind(':email', $email);
        $this->db->bind(':otp', $otp);
        $this->db->bind(':purpose', $purpose);
        $this->db->single();

        if ($this->db->rowCount() > 0) {
            return ['success' => false, 'message' => 'OTP has expired. Please request a new one.'];
        }

        return ['success' => false, 'message' => 'Invalid OTP. Please try again.'];
    }
}
