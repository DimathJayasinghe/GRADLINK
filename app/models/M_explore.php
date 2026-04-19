<?php
class M_explore {
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
    
    /**
     * Search for posts
     */
    public function searchPosts($query, $limit = 20, $offset = 0) {
        $searchTerm = '%' . $query . '%';
        
        $sql = "SELECT p.*, 
                       u.name, 
                       COALESCE(NULLIF(u.display_name, ''), u.name) AS display_name,
                       u.profile_image, 
                       u.role,
                       u.id as user_id,
                       (SELECT COUNT(*) FROM post_likes l WHERE l.post_id = p.id) as likes,
                       (SELECT COUNT(*) FROM comments c WHERE c.post_id = p.id) as comments
                FROM posts p 
                JOIN users u ON u.id = p.user_id 
                                LEFT JOIN suspended_users su ON su.user_id = u.id AND su.status = 'active'
                                WHERE (p.content LIKE :search 
                                      OR u.name LIKE :search
                                      OR u.display_name LIKE :search)
                                    AND su.id IS NULL
                ORDER BY p.created_at DESC 
                LIMIT :limit OFFSET :offset";
        
        $this->db->query($sql);
        $this->db->bind(':search', $searchTerm);
        $this->db->bind(':limit', (int)$limit, PDO::PARAM_INT);
        $this->db->bind(':offset', (int)$offset, PDO::PARAM_INT);
        
        return $this->db->resultSet();
    }
    
    /**
     * Search for users (all)
     */
    public function searchUsers($query, $limit = 20, $offset = 0) {
        $searchTerm = '%' . $query . '%';
        
        $sql = "SELECT u.id, 
                       u.name, 
                       u.email, 
                       u.role, 
                       u.profile_image,
                       u.skills,
                       u.bio,
                       (SELECT COUNT(*) FROM followers f WHERE f.followed_id = u.id) as follower_count,
                       (SELECT COUNT(*) FROM followers f WHERE f.follower_id = u.id) as following_count
                FROM users u 
                                LEFT JOIN suspended_users su ON su.user_id = u.id AND su.status = 'active'
                                WHERE (u.name LIKE :search 
                   OR u.email LIKE :search 
                   OR u.skills LIKE :search
                                     OR u.bio LIKE :search)
                                    AND su.id IS NULL
                ORDER BY u.name ASC 
                LIMIT :limit OFFSET :offset";
        
        $this->db->query($sql);
        $this->db->bind(':search', $searchTerm);
        $this->db->bind(':limit', (int)$limit, PDO::PARAM_INT);
        $this->db->bind(':offset', (int)$offset, PDO::PARAM_INT);
        
        return $this->db->resultSet();
    }
    
    /**
     * Search for alumni only
     */
    public function searchAlumni($query, $limit = 20, $offset = 0) {
        $searchTerm = '%' . $query . '%';
        
        $sql = "SELECT u.id, 
                       u.name, 
                       u.email, 
                       u.role, 
                       u.profile_image,
                       u.skills,
                       u.bio,
                       (SELECT COUNT(*) FROM followers f WHERE f.followed_id = u.id) as follower_count,
                       (SELECT COUNT(*) FROM followers f WHERE f.follower_id = u.id) as following_count
                FROM users u 
                                LEFT JOIN suspended_users su ON su.user_id = u.id AND su.status = 'active'
                WHERE u.role = 'alumni'
                                    AND su.id IS NULL
                  AND (u.name LIKE :search 
                   OR u.email LIKE :search 
                   OR u.skills LIKE :search
                   OR u.bio LIKE :search)
                ORDER BY u.name ASC 
                LIMIT :limit OFFSET :offset";
        
        $this->db->query($sql);
        $this->db->bind(':search', $searchTerm);
        $this->db->bind(':limit', (int)$limit, PDO::PARAM_INT);
        $this->db->bind(':offset', (int)$offset, PDO::PARAM_INT);
        
        return $this->db->resultSet();
    }
    
    /**
     * Search for undergrads only
     */
    public function searchUndergrads($query, $limit = 20, $offset = 0) {
        $searchTerm = '%' . $query . '%';
        
        $sql = "SELECT u.id, 
                       u.name, 
                       u.email, 
                       u.role, 
                       u.profile_image,
                       u.skills,
                       u.bio,
                       (SELECT COUNT(*) FROM followers f WHERE f.followed_id = u.id) as follower_count,
                       (SELECT COUNT(*) FROM followers f WHERE f.follower_id = u.id) as following_count
                FROM users u 
                                LEFT JOIN suspended_users su ON su.user_id = u.id AND su.status = 'active'
                WHERE u.role = 'undergrad'
                                    AND su.id IS NULL
                  AND (u.name LIKE :search 
                   OR u.email LIKE :search 
                   OR u.skills LIKE :search
                   OR u.bio LIKE :search)
                ORDER BY u.name ASC 
                LIMIT :limit OFFSET :offset";
        
        $this->db->query($sql);
        $this->db->bind(':search', $searchTerm);
        $this->db->bind(':limit', (int)$limit, PDO::PARAM_INT);
        $this->db->bind(':offset', (int)$offset, PDO::PARAM_INT);
        
        return $this->db->resultSet();
    }
    
    /**
     * Search for events
     */
    public function searchEvents($query, $limit = 20, $offset = 0) {
        $searchTerm = '%' . $query . '%';
        
        $sql = "SELECT e.*, 
                       u.name AS organizer_name, 
                       ei.file_path AS attachment_image,
                       (SELECT COUNT(*) FROM event_attendees ea WHERE ea.event_id = e.id) as attendee_count
                FROM events e 
                LEFT JOIN users u ON u.id = e.organizer_id 
                LEFT JOIN event_images ei ON ei.event_id = e.id AND ei.is_primary = 1
                                LEFT JOIN suspended_users su ON su.user_id = e.organizer_id AND su.status = 'active'
                WHERE (e.title LIKE :search 
                   OR e.description LIKE :search 
                   OR e.venue LIKE :search)
                                    AND su.id IS NULL
                  AND e.status = 'published'
                  AND e.visibility = 'public'
                ORDER BY e.start_datetime DESC 
                LIMIT :limit OFFSET :offset";
        
        $this->db->query($sql);
        $this->db->bind(':search', $searchTerm);
        $this->db->bind(':limit', (int)$limit, PDO::PARAM_INT);
        $this->db->bind(':offset', (int)$offset, PDO::PARAM_INT);
        
        return $this->db->resultSet();
    }
    
    /**
     * Search for fundraisers from database
     */
    public function searchFundraisers($query, $limit = 20, $offset = 0) {
        $searchTerm = '%' . $query . '%';
        
        $sql = "SELECT 
                    fr.id,
                    fr.user_id,
                    fr.club_name,
                    fr.title,
                    fr.description,
                    fr.project_poster,
                    fr.goal_amount as target_amount,
                    fr.collected_amount as raised_amount,
                    fr.end_date as deadline,
                    fr.status,
                    fr.created_at,
                    u.name as organizer_name,
                    u.profile_image
                FROM fundraising_requests fr
                JOIN users u ON u.id = fr.user_id
                                LEFT JOIN suspended_users su ON su.user_id = fr.user_id AND su.status = 'active'
                WHERE (fr.title LIKE :search
                   OR fr.description LIKE :search
                   OR fr.club_name LIKE :search)
                                    AND su.id IS NULL
                  AND fr.status IN ('Approved', 'Active')
                ORDER BY fr.created_at DESC
                LIMIT :limit OFFSET :offset";
        
        $this->db->query($sql);
        $this->db->bind(':search', $searchTerm);
        $this->db->bind(':limit', (int)$limit, PDO::PARAM_INT);
        $this->db->bind(':offset', (int)$offset, PDO::PARAM_INT);
        
        $results = $this->db->resultSet();
        
        // Calculate additional fields for each fundraiser
        foreach ($results as &$fundraiser) {
            // Calculate days left
            $now = new DateTime();
            $deadline = new DateTime($fundraiser->deadline);
            $fundraiser->days_left = null;
            
            if ($deadline > $now) {
                $interval = $now->diff($deadline);
                $fundraiser->days_left = $interval->days;
            }
            
            // Calculate percentage
            $fundraiser->percentage = ($fundraiser->raised_amount / $fundraiser->target_amount) * 100;
            
            // Add club_id (using user_id as club identifier)
            $fundraiser->club_id = $fundraiser->user_id;
        }
        
        return $results;
    }
    
    /**
     * Check if user is following another user
     */
    public function isFollowing($followerId, $followedId) {
        $this->db->query('SELECT 1 FROM followers WHERE follower_id = :follower AND followed_id = :followed');
        $this->db->bind(':follower', $followerId);
        $this->db->bind(':followed', $followedId);
        return (bool)$this->db->single();
    }
    
    /**
     * Check if there's a pending follow request
     */
    public function hasPendingRequest($requesterId, $targetId) {
        $this->db->query('SELECT 1 FROM follow_requests WHERE requester_id = :requester AND target_id = :target AND status = "pending"');
        $this->db->bind(':requester', $requesterId);
        $this->db->bind(':target', $targetId);
        return (bool)$this->db->single();
    }
    
    /**
     * Check if user has bookmarked an event
     */
    public function hasBookmarkedEvent($userId, $eventId) {
        $this->db->query('SELECT 1 FROM event_bookmarks WHERE user_id = :user AND event_id = :event');
        $this->db->bind(':user', $userId);
        $this->db->bind(':event', $eventId);
        return (bool)$this->db->single();
    }
    
    /**
     * Check if post is liked by user
     */
    public function isPostLiked($postId, $userId) {
        $this->db->query('SELECT 1 FROM post_likes WHERE post_id = :post AND user_id = :user');
        $this->db->bind(':post', $postId);
        $this->db->bind(':user', $userId);
        return (bool)$this->db->single();
    }
}
?>