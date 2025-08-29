<?php
class M_mainfeed{
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    // Method to fetch posts for the main feed
    public function getPosts() {
        $this->db->query("SELECT * FROM Posts ORDER BY created_at DESC");
        return $this->db->resultSet();
    }

    public function getPostById($id) {
        $this->db->query("SELECT p.id, p.content, p.image, p.created_at,
                                 u.name as user_name, u.handle as user_handle, u.avatar,
                                 (SELECT COUNT(*) FROM comments c WHERE c.post_id = p.id) as comments_count,
                                 (SELECT COUNT(*) FROM likes l WHERE l.post_id = p.id) as likes_count,
                                 (SELECT COUNT(*) FROM reposts r WHERE r.post_id = p.id) as reposts_count
                          FROM posts p
                          JOIN users u ON u.id = p.user_id
                          WHERE p.id = :id");
        $this->db->bind(':id',$id);
        return $this->db->single();
    }

    // Additional methods for managing main feed can be added here...
    
}
?>