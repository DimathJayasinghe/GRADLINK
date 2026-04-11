<?php
class M_mainfeed{
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    // Method to fetch posts for the main feed
    public function getPosts($feed_type = 'for_you', $offsetRound = 1) {
        $limit = 10; // Number of posts to fetch per request
        $round = (int)$offsetRound;
        if ($round < 1) { $round = 1; }
        $offset = ($round - 1) * $limit;

        if ($feed_type === 'following'){
            // Posts from accounts the current user follows (private or public allowed)
            $this->db->query("SELECT 
                                p.*, 
                                u.name, 
                                u.profile_image, 
                                u.role,
                                (SELECT COUNT(*) FROM comments c WHERE c.post_id = p.id) AS comments,
                                (SELECT COUNT(*) FROM post_likes l WHERE l.post_id = p.id) AS likes
                              FROM posts p
                              INNER JOIN followers f ON p.user_id = f.followed_id AND f.follower_id = :current_user_id
                              INNER JOIN users u ON u.id = p.user_id
                              ORDER BY p.created_at DESC
                              LIMIT :limit OFFSET :offset");
            $this->db->bind(':current_user_id', $_SESSION['user_id']);
            $this->db->bind(':limit', (int)$limit, PDO::PARAM_INT);
            $this->db->bind(':offset', (int)$offset, PDO::PARAM_INT);
            return $this->db->resultSet();
        }else{
            // For you: show all posts from all users
            $this->db->query("SELECT 
                                p.*, 
                                u.name, 
                                u.profile_image, 
                                u.role,
                                (SELECT COUNT(*) FROM comments c WHERE c.post_id = p.id) AS comments,
                                (SELECT COUNT(*) FROM post_likes l WHERE l.post_id = p.id) AS likes
                              FROM posts p
                              INNER JOIN users u ON u.id = p.user_id
                              ORDER BY p.created_at DESC
                              LIMIT :limit OFFSET :offset");
            $this->db->bind(':limit', (int)$limit, PDO::PARAM_INT);
            $this->db->bind(':offset', (int)$offset, PDO::PARAM_INT);
            return $this->db->resultSet();
        }
    }

    public function getPostById($id) {
        $this->db->query("SELECT
                                p.*,
                                u.name,
                                u.profile_image,
                                u.role,
                                (SELECT COUNT(*) FROM comments c WHERE c.post_id = p.id) AS comments,
                                (SELECT COUNT(*) FROM post_likes l WHERE l.post_id = p.id) AS likes
                          FROM posts p
                          INNER JOIN users u ON u.id = p.user_id
                          WHERE p.id = :id
                          LIMIT 1");
        $this->db->bind(':id', (int)$id, PDO::PARAM_INT);
        return $this->db->single();
    }

    // Additional methods for managing main feed can be added here...
    
}
?>