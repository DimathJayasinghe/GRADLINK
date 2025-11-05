<?php
class M_mainfeed{
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    // Method to fetch posts for the main feed
    public function getPosts($feed_type = 'for_you') {
        if ($feed_type === 'following'){
            $this->db->query("SELECT 
                                p.*, 
                                u.name, 
                                u.profile_image, 
                                u.role,
                                (SELECT COUNT(*) FROM comments c WHERE c.post_id = p.id) AS comments,
                                (SELECT COUNT(*) FROM post_likes l WHERE l.post_id = p.id) AS likes
                              FROM posts p
                              JOIN followers f ON p.user_id = f.followed_id
                              JOIN users u ON u.id = p.user_id
                              WHERE f.follower_id = :current_user_id
                              ORDER BY p.created_at DESC");
            $this->db->bind(':current_user_id', $_SESSION['user_id']);
            return $this->db->resultSet();
        }else{
            $this->db->query("SELECT 
                                p.*, 
                                u.name, 
                                u.profile_image, 
                                u.role,
                                (SELECT COUNT(*) FROM comments c WHERE c.post_id = p.id) AS comments,
                                (SELECT COUNT(*) FROM post_likes l WHERE l.post_id = p.id) AS likes
                              FROM posts p
                              JOIN users u ON u.id = p.user_id
                              ORDER BY p.created_at DESC");
            return $this->db->resultSet();
        }
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