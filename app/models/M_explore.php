<?php
class M_explore {
    private $db;
    
    public function __construct() {
        $this->db = new Database();
    }
    
    /**
     * Search for posts
     */
    public function searchPosts($query, $limit = 20, $offset = 0) {
        $searchTerm = '%' . $query . '%';
        
        $sql = "SELECT p.*, 
                       u.name, 
                       u.profile_image, 
                       u.role,
                       u.id as user_id,
                       (SELECT COUNT(*) FROM post_likes l WHERE l.post_id = p.id) as likes,
                       (SELECT COUNT(*) FROM comments c WHERE c.post_id = p.id) as comments
                FROM posts p 
                JOIN users u ON u.id = p.user_id 
                WHERE p.content LIKE :search 
                   OR u.name LIKE :search
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
                WHERE u.name LIKE :search 
                   OR u.email LIKE :search 
                   OR u.skills LIKE :search
                   OR u.bio LIKE :search
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
                WHERE u.role = 'alumni'
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
                WHERE u.role = 'undergrad'
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
                WHERE (e.title LIKE :search 
                   OR e.description LIKE :search 
                   OR e.venue LIKE :search)
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