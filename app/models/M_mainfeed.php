<?php
class M_mainfeed{
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

    // Method to fetch posts for the main feed
    public function getPosts($feed_type = 'for_you', $offsetRound = 1) {
        $limit = 10; // Number of posts to fetch per request
        $round = (int)$offsetRound;
        if ($round < 1) { $round = 1; }
        $offset = ($round - 1) * $limit;

        if ($feed_type === 'following'){
            // Posts from users the current user follows.
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
                                                            LEFT JOIN suspended_users su ON su.user_id = p.user_id AND su.status = 'active'
                                                            WHERE su.id IS NULL
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
                                                            LEFT JOIN suspended_users su ON su.user_id = p.user_id AND su.status = 'active'
                                                            WHERE su.id IS NULL
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
                                                    LEFT JOIN suspended_users su ON su.user_id = p.user_id AND su.status = 'active'
                          WHERE p.id = :id
                                                        AND su.id IS NULL
                          LIMIT 1");
        $this->db->bind(':id', (int)$id, PDO::PARAM_INT);
        return $this->db->single();
    }

    // Additional methods for managing main feed can be added here...
    
}
?>