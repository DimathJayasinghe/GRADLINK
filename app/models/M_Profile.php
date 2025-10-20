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

    /**
     * Fetch a single certificate by id.
     */
    public function getCertificateById($cert_id){
        $this->db->query('SELECT * FROM certificates WHERE id = :id LIMIT 1');
        $this->db->bind(':id', $cert_id);
        return $this->db->single();
    }


// ...existing code...

    /**
     * Create a certificate for a user.
     * If the certificate_file column does not exist yet, it will gracefully
     * fall back to inserting without the file instead of crashing.
     */
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

// ...existing code...

// ...existing code...

    public function getProjects($user_id){
        return [
            (object)['title' => 'Project A', 'description' => 'Description for Project A'],
            (object)['title' => 'Project B', 'description' => 'Description for Project B'],
            (object)['title' => 'Project C', 'description' => 'Description for Project C']
        ];
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


}
?>