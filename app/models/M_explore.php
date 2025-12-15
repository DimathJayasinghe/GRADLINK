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
     * Search for fundraisers - returns dummy data until database implementation is complete
     */
    public function searchFundraisers($query, $limit = 20, $offset = 0) {
        // Dummy fundraiser data
        $allFundraisers = [
            (object)[
                'id' => 1,
                'title' => 'IEEE Student Branch Technology Fund',
                'description' => 'We\'re raising funds to get new laptops for our coding workshops and hackathons. Right now, many of our participants share or borrow devices, which limits how much they can create and learn.',
                'club_name' => 'IEEE Student Branch',
                'club_id' => 1,
                'target_amount' => 150000,
                'raised_amount' => 89000,
                'deadline' => '2025-12-31',
                'status' => 'Approved',
                'created_at' => '2024-10-01 10:00:00',
                'days_left' => 30
            ],
            (object)[
                'id' => 2,
                'title' => 'Robotics Club Equipment Drive',
                'description' => 'Help us purchase essential robotics equipment and components for our annual robotics competition and weekly workshops.',
                'club_name' => 'Robotics Club',
                'club_id' => 2,
                'target_amount' => 200000,
                'raised_amount' => 145000,
                'deadline' => '2025-11-30',
                'status' => 'Approved',
                'created_at' => '2024-09-15 14:30:00',
                'days_left' => 60
            ],
            (object)[
                'id' => 3,
                'title' => 'Music Society Instrument Fund',
                'description' => 'We need new music instruments for our college band and orchestra. Support us in bringing more music to campus events.',
                'club_name' => 'Music Society',
                'club_id' => 3,
                'target_amount' => 180000,
                'raised_amount' => 72000,
                'deadline' => '2025-12-20',
                'status' => 'Approved',
                'created_at' => '2024-09-10 13:20:00',
                'days_left' => 49
            ],
            (object)[
                'id' => 4,
                'title' => 'Environmental Club Green Campus Initiative',
                'description' => 'Join us in creating a sustainable campus environment with solar panels, recycling stations, and a community garden.',
                'club_name' => 'Environmental Club',
                'club_id' => 4,
                'target_amount' => 250000,
                'raised_amount' => 98000,
                'deadline' => '2026-01-31',
                'status' => 'Approved',
                'created_at' => '2024-10-05 11:45:00',
                'days_left' => 91
            ],
            (object)[
                'id' => 5,
                'title' => 'Drama Society Stage Equipment',
                'description' => 'Help us upgrade our stage lighting and sound equipment for better theatrical productions and performances.',
                'club_name' => 'Drama Society',
                'club_id' => 5,
                'target_amount' => 120000,
                'raised_amount' => 55000,
                'deadline' => '2025-12-15',
                'status' => 'Approved',
                'created_at' => '2024-08-20 09:00:00',
                'days_left' => 44
            ],
            (object)[
                'id' => 6,
                'title' => 'Sports Club Athletic Equipment',
                'description' => 'Support our athletes by helping us purchase new sports equipment for cricket, basketball, and badminton teams.',
                'club_name' => 'Sports Club',
                'club_id' => 6,
                'target_amount' => 175000,
                'raised_amount' => 131000,
                'deadline' => '2025-11-25',
                'status' => 'Approved',
                'created_at' => '2024-08-15 09:15:00',
                'days_left' => 55
            ]
        ];
        
        // Filter by search query if provided
        $filtered = [];
        if (!empty($query)) {
            foreach ($allFundraisers as $fundraiser) {
                if (stripos($fundraiser->title, $query) !== false ||
                    stripos($fundraiser->description, $query) !== false ||
                    stripos($fundraiser->club_name, $query) !== false) {
                    $filtered[] = $fundraiser;
                }
            }
        } else {
            $filtered = $allFundraisers;
        }
        
        // Apply limit and offset
        $filtered = array_slice($filtered, $offset, $limit);
        
        return $filtered;
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