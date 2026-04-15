<?php
class M_Profile{
    protected $db;
    public function __construct() {
        $this->db = new Database();
    }

    public function getUser($user_id){
        $this->db->query("SELECT * FROM users WHERE id = :user_id");
        $this->db->bind(':user_id', $user_id);
        $this->db->execute();
        return $this->db->rowCount();
    }
    public function getUserDetails($user_id){
        $this->db->query("SELECT id, name, email, role, display_name, profile_image, bio, skills, nic, batch_no, student_id, created_at FROM users WHERE id = :user_id");
        $this->db->bind(':user_id', $user_id);
        return $this->db->single();
    }
    public function getCertificates($user_id){
        $this->db->query('SELECT c.*, u.name AS user_name, u.profile_image 
                          FROM certificates c 
                          JOIN users u ON u.id = c.user_id 
                          WHERE c.user_id = :uid 
                          ORDER BY c.issued_date DESC');
        $this->db->bind(':uid', $user_id);
        return $this->db->resultSet();
    }

    public function updateProfileBioImage($user_id, $profile_image, $bio, $batch_no){
        $this->db->query('UPDATE users SET profile_image = :profile_image, bio = :bio, batch_no = :batch_no WHERE id = :user_id');
        $this->db->bind(':profile_image', $profile_image);
        $this->db->bind(':bio', $bio);
        $this->db->bind(':batch_no', $batch_no);
        $this->db->bind(':user_id', $user_id);

        $ok = $this->db->execute();
        return $ok;
    }

    /**
     * Fetch a single certificate by id. use to view the certificate.
     */
    public function getCertificateById($cert_id){
        $this->db->query('SELECT * FROM certificates WHERE id = :id LIMIT 1');
        $this->db->bind(':id', $cert_id);
        return $this->db->single();
    }

    


    
    public function createCertificate($user_id, $name, $issuer, $issued_date, $certificate_file = null) {
        if ($certificate_file === null) {
            // Insert without file
            $this->db->query('INSERT INTO certificates (user_id, name, issuer, issued_date) VALUES (:uid, :name, :issuer, :issued_date)');
            $this->db->bind(':uid', $user_id);
            $this->db->bind(':name', $name);
            $this->db->bind(':issuer', $issuer);
            $this->db->bind(':issued_date', $issued_date);
            return $this->db->execute();
        }
        try {
            $this->db->query('INSERT INTO certificates (user_id, name, issuer, issued_date, certificate_file) VALUES (:uid, :name, :issuer, :issued_date, :file)');
            $this->db->bind(':uid', $user_id);
            $this->db->bind(':name', $name);
            $this->db->bind(':issuer', $issuer);
            $this->db->bind(':issued_date', $issued_date);
            $this->db->bind(':file', $certificate_file);
            return $this->db->execute();
        } catch (Throwable $e) {
            // If schema not updated yet (Unknown column 'certificate_file'), retry without file
            if (stripos($e->getMessage(), 'unknown column') !== false && stripos($e->getMessage(), "certificate_file") !== false) {
                $this->db->query('INSERT INTO certificates (user_id, name, issuer, issued_date) VALUES (:uid, :name, :issuer, :issued_date)');
                $this->db->bind(':uid', $user_id);
                $this->db->bind(':name', $name);
                $this->db->bind(':issuer', $issuer);
                $this->db->bind(':issued_date', $issued_date);
                return $this->db->execute();
            }
            throw $e; // Different error, rethrow
        }
}
// ...existing code...

    /**
     * Update certificate record.
     * @return bool
     */
    public function updateCertificate($user_id, $cert_id, $name, $issuer, $issued_date, $certificate_file = null, $remove_file = false) {
        // fetch existing record
        $this->db->query('SELECT certificate_file FROM certificates WHERE id = :id AND user_id = :uid LIMIT 1');
        $this->db->bind(':id', $cert_id);
        $this->db->bind(':uid', $user_id);
        $existing = $this->db->single();

        if ($certificate_file !== null) {
            $this->db->query('UPDATE certificates SET name = :name, issuer = :issuer, issued_date = :issued_date, certificate_file = :file WHERE id = :id AND user_id = :uid');
            $this->db->bind(':file', $certificate_file);
        } elseif ($remove_file) {
            $this->db->query('UPDATE certificates SET name = :name, issuer = :issuer, issued_date = :issued_date, certificate_file = NULL WHERE id = :id AND user_id = :uid');
        } else {
            $this->db->query('UPDATE certificates SET name = :name, issuer = :issuer, issued_date = :issued_date WHERE id = :id AND user_id = :uid');
        }

        $this->db->bind(':name', $name);
        $this->db->bind(':issuer', $issuer);
        $this->db->bind(':issued_date', $issued_date);
        $this->db->bind(':id', $cert_id);
        $this->db->bind(':uid', $user_id);

        $ok = $this->db->execute();

        if ($ok && $existing) {
            $oldFile = $existing->certificate_file ?? null;
            if ($oldFile && ($certificate_file !== null || $remove_file)) {
                $path = APPROOT . '/storage/certificates/' . $oldFile;
                if (is_file($path)) @unlink($path);
            }
        }

        return (bool)$ok;
    }

    public function deleteCertificate($user_id, $cert_id) {
        // fetch existing record
        $this->db->query('SELECT certificate_file FROM certificates WHERE id = :id AND user_id = :uid LIMIT 1');
        $this->db->bind(':id', $cert_id);
        $this->db->bind(':uid', $user_id);
        $existing = $this->db->single();

        // delete record
        $this->db->query('DELETE FROM certificates WHERE id = :id AND user_id = :uid');
        $this->db->bind(':id', $cert_id);
        $this->db->bind(':uid', $user_id);
        $ok = $this->db->execute();

        if ($ok && $existing) {
            $oldFile = $existing->certificate_file ?? null;
            if ($oldFile) {
                $path = APPROOT . '/storage/certificates/' . $oldFile;
                if (is_file($path)) @unlink($path);
            }
        }

        return (bool)$ok;
    }

    public function getWorkExperiences($user_id){
        $this->db->query('SELECT w.*, u.name AS user_name, u.profile_image 
                          FROM work_experiences w 
                          JOIN users u ON u.id = w.user_id 
                          WHERE w.user_id = :uid 
                          ORDER BY w.created_at DESC');
        $this->db->bind(':uid', $user_id);
        return $this->db->resultSet();
    }

    public function getWorkExperiencesById($work_id){
        $this->db->query('SELECT * FROM work_experiences WHERE id = :id LIMIT 1');
        $this->db->bind(':id', $work_id);
        return $this->db->single();
    }

    public function createWorkExperience($user_id, $position, $company, $period){
        
        try {
            $this->db->query('INSERT INTO work_experiences (user_id, position, company, period) VALUES (:uid, :position, :company, :period)');
            $this->db->bind(':uid', $user_id);
            $this->db->bind(':position', $position);
            $this->db->bind(':company', $company);
            $this->db->bind(':period', $period);
            return $this->db->execute();
        } catch (Exception $e) {
            error_log("WorkExperience Creation Error: " . $e->getMessage());
            return false;
        }
    }

    public function updateWorkExperience($user_id, $work_id, $position, $company, $period){
        $this->db->query('SELECT * FROM work_experiences WHERE id = :id AND user_id = :uid LIMIT 1');
        $this->db->bind(':id', $work_id);
        $this->db->bind(':uid', $user_id);
        $existing = $this->db->single();

        if(!$existing){
            return false; // Work experience not found or does not belong to user
        }

        $this->db->query('UPDATE work_experiences SET position = :position, company = :company, period = :period WHERE id = :id AND user_id = :uid');

        $this->db->bind(':position', $position);
        $this->db->bind(':company', $company);
        $this->db->bind(':period', $period);
        $this->db->bind(':id', $work_id);
        $this->db->bind(':uid', $user_id);
        
        $ok =$this->db->execute();
        return $ok;

    }

    public function deleteWorkExperience($user_id, $work_id){
        //fetch existing record
        $this->db->query('SELECT * FROM work_experiences WHERE id = :id AND user_id = :uid LIMIT 1');
        $this->db->bind(':id', $work_id);
        $this->db->bind(':uid', $user_id);
        $this->db->single();

        //delete record
        $this->db->query('DELETE FROM work_experiences WHERE id = :id AND user_id = :uid');
        $this->db->bind(':id', $work_id);
        $this->db->bind(':uid', $user_id);
        $ok = $this->db->execute();

        return $ok;


    }

    public function getProjects($user_id){
        $this->db->query('SELECT pr.*, u.name AS user_name, u.profile_image
                          FROM projects pr 
                          JOIN users u ON u.id = pr.user_id 
                          WHERE pr.user_id = :uid 
                          ORDER BY pr.created_at DESC');
        $this->db->bind(':uid', $user_id);
        return $this->db->resultSet();
    }

    public function getProjectById($project_id){
        $this->db->query('SELECT * FROM projects WHERE id = :id LIMIT 1');
        $this->db->bind(':id', $project_id);
        return $this->db->single();
    }

    public function createProject($user_id, $title, $description, $skills, $start_date, $end_date){
        try {
            $this->db->query('INSERT INTO projects (user_id, title, description, skills_used, start_date, end_date) VALUES (:uid, :title, :description, :skills, :start_date, :end_date)');
            $this->db->bind(':uid', $user_id);
            $this->db->bind(':title', $title);
            $this->db->bind(':description', $description);
            $this->db->bind(':skills', $skills);
            $this->db->bind(':start_date', $start_date);
            $this->db->bind(':end_date', $end_date);
            return $this->db->execute();
        } catch (Throwable $e) {
            error_log("Project Creation Error: " . $e->getMessage());
            return false;
        }
    }

    public function updateProject($user_id, $project_id, $title, $description, $skills, $start_date, $end_date){
        $this->db->query('SELECT * FROM projects WHERE id = :id AND user_id = :uid LIMIT 1');
        $this->db->bind(':id', $project_id);
        $this->db->bind(':uid', $user_id);
        $existing = $this->db->single();

        if(!$existing){
            return false; // Project not found or does not belong to user
        }

        $this->db->query('UPDATE projects SET title = :title, description = :description, skills_used = :skills, start_date = :start_date, end_date = :end_date WHERE id = :id AND user_id = :uid');

        $this->db->bind(':title', $title);
        $this->db->bind(':description', $description);
        $this->db->bind(':skills', $skills);
        $this->db->bind(':start_date', $start_date);
        $this->db->bind(':end_date', $end_date);
        $this->db->bind(':id', $project_id);
        $this->db->bind(':uid', $user_id);

        $ok = $this->db->execute();
        return $ok;
    }

    public function deleteProject($user_id, $project_id){
        //fetch existing record
        $this->db->query('SELECT * FROM projects WHERE id = :id AND user_id = :uid LIMIT 1');
        $this->db->bind(':id', $project_id);
        $this->db->bind(':uid', $user_id);
        $this->db->single();

        //delete record
        $this->db->query('DELETE FROM projects WHERE id = :id AND user_id = :uid');
        $this->db->bind(':id', $project_id);
        $this->db->bind(':uid', $user_id);
        $ok = $this->db->execute();

        return $ok;
        
    }
    
    public function getPosts($user_id){
        $this->db->query('SELECT p.*, u.name, u.profile_image, u.role, 
                          (SELECT COUNT(*) FROM post_likes l WHERE l.post_id=p.id) likes,
                          (SELECT COUNT(*) FROM comments c WHERE c.post_id=p.id) comments 
                          FROM posts p 
                          JOIN users u ON u.id=p.user_id 
                          WHERE p.user_id=:uid 
                          ORDER BY p.created_at DESC');
        $this->db->bind(':uid', $user_id);
        return $this->db->resultSet();
    }


    public function isFollowed($current_user_id, $profile_user_id){
        $this->db->query('SELECT 1 FROM followers WHERE follower_id = :follower_id AND followed_id = :followed_id LIMIT 1');
        $this->db->bind(':follower_id',$current_user_id);
        $this->db->bind(':followed_id',$profile_user_id);
        $result = $this->db->single();
        return $result ? true : false;
    }

    public function hasPendingFollowRequest($requester_id, $target_id){
        $this->db->query('SELECT 1 FROM follow_requests WHERE requester_id = :requester_id AND target_id = :target_id AND status = "pending" LIMIT 1');
        $this->db->bind(':requester_id',$requester_id);
        $this->db->bind(':target_id',$target_id);
        $result = $this->db->single();
        return $result ? true : false;
    }

    public function createFollowRequest($requester_id, $target_id){
        $this->db->query('INSERT INTO follow_requests (requester_id, target_id, status) VALUES (:requester_id, :target_id, "pending")');
        $this->db->bind(':requester_id',$requester_id);
        $this->db->bind(':target_id',$target_id);
        if ($this->db->execute()) {
            return $this->db->lastInsertId();
        }
        return false;
    }

    public function cancelFollowRequest($requester_id, $target_id){
        $this->db->query('DELETE FROM follow_requests WHERE requester_id = :requester_id AND target_id = :target_id');
        $this->db->bind(':requester_id',$requester_id);
        $this->db->bind(':target_id',$target_id);
        return $this->db->execute();
    }

    public function approveFollowRequest($requester_id, $target_id){
        // Get request details - use requester_id and target_id for lookup
        $this->db->query('SELECT requester_id, target_id FROM follow_requests WHERE requester_id = :requester_id AND target_id = :target_id AND status = "pending" LIMIT 1');
        $this->db->bind(':requester_id', $requester_id);
        $this->db->bind(':target_id', $target_id);
        $request = $this->db->single();
        
        if (!$request) {
            return false;
        }

        // Start transaction
        try {
            // Insert into followers table
            $this->db->query('INSERT IGNORE INTO followers (follower_id, followed_id) VALUES (:follower_id, :followed_id)');
            $this->db->bind(':follower_id', $request->requester_id);
            $this->db->bind(':followed_id', $request->target_id);
            $this->db->execute();

            // Delete from follow_requests
            $this->db->query('DELETE FROM follow_requests WHERE requester_id = :requester_id AND target_id = :target_id');
            $this->db->bind(':requester_id', $requester_id);
            $this->db->bind(':target_id', $target_id);
            $this->db->execute();

            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public function rejectFollowRequest($requester_id, $target_id){
        $this->db->query('DELETE FROM follow_requests WHERE requester_id = :requester_id AND target_id = :target_id');
        $this->db->bind(':requester_id', $requester_id);
        $this->db->bind(':target_id', $target_id);
        return $this->db->execute();
    }

    public function followUser($current_user_id, $profile_user_id){
        $this->db->query('INSERT INTO followers (follower_id, followed_id) VALUES (:follower_id, :followed_id)');
        $this->db->bind(':follower_id',$current_user_id);
        $this->db->bind(':followed_id',$profile_user_id);
        return $this->db->execute();
    }
    public function unfollowUser($current_user_id, $profile_user_id){
        $this->db->query('DELETE FROM followers WHERE follower_id = :follower_id AND followed_id = :followed_id');
        $this->db->bind(':follower_id',$current_user_id);
        $this->db->bind(':followed_id',$profile_user_id);
        return $this->db->execute();
    }

    public function blockUser($blocker_id, $blocked_id){
        $this->db->query('INSERT INTO User_blocks (blocker_id, blocked_id) VALUES (:blocker_id, :blocked_id)');
        $this->db->bind(':blocker_id', $blocker_id);
        $this->db->bind(':blocked_id', $blocked_id);
        return $this->db->execute();
    }

    public function unblockUser($blocker_id, $blocked_id){
        $this->db->query('DELETE FROM User_blocks WHERE blocker_id = :blocker_id AND blocked_id = :blocked_id');
        $this->db->bind(':blocker_id', $blocker_id);    
        $this->db->bind(':blocked_id', $blocked_id);
        return $this->db->execute();
    }

    public function isBlocked($current_user_id, $profile_user_id){
        $this->db->query('SELECT 1 FROM User_blocks WHERE 
                        (blocker_id = :current_user_id AND blocked_id = :profile_user_id) 
                        OR (blocker_id = :profile_user_id AND blocked_id = :current_user_id) 
                        LIMIT 1');
        $this->db->bind(':current_user_id', $current_user_id);
        $this->db->bind(':profile_user_id', $profile_user_id);
        $result = $this->db->single();
        return $result ? true : false;
    }
}