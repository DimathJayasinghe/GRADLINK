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
        return [
            (object)['title' => 'Certificate in Web Development', 'image_url' => null],
            (object)['title' => 'Certificate in Data Science', 'image_url' => null],
            (object)['title' => 'Certificate in Machine Learning', 'image_url' => null]
        ];
    }
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