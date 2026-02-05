<?php
class M_admin {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    public function getOverviewMetrics(): array {
        // Total users
        $totalUsers = 0;
        $active30 = 0;
        $growthPct = 0;
        
        try {
            $this->db->query('SELECT COUNT(*) AS c FROM users');
            $totalUsers = $this->db->single()->c ?? 0;

            // Active users last 30 days (approx by login_time if stored; fallback to created_at)
            $this->db->query('SELECT COUNT(*) AS c FROM users WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)');
            $active30 = $this->db->single()->c ?? 0;

            // User growth last 3 months (new signups vs previous 3 months)
            $this->db->query('SELECT 
                    SUM(created_at >= DATE_SUB(NOW(), INTERVAL 3 MONTH)) AS recent,
                    SUM(created_at < DATE_SUB(NOW(), INTERVAL 3 MONTH) AND created_at >= DATE_SUB(NOW(), INTERVAL 6 MONTH)) AS previous
                FROM users');
            $growthRow = $this->db->single();
            $recent = (int)($growthRow->recent ?? 0);
            $previous = (int)($growthRow->previous ?? 0);
            $growthPct = $previous > 0 ? round((($recent - $previous) / $previous) * 100) : ($recent > 0 ? 100 : 0);
        } catch (Exception $e) {
            // Users table might not exist, return default values
            $totalUsers = 0;
            $active30 = 0;
            $growthPct = 0;
        }

        return [
            'total_users' => (int)$totalUsers,
            'active_30_days' => (int)$active30,
            'growth_3_months_pct' => (int)$growthPct,
        ];
    }

    public function getChartData(): array {
        // Distribution by role
        $this->db->query('SELECT role, COUNT(*) AS count FROM users GROUP BY role');
        $roles = $this->db->resultSet();

        // Distribution by batch (graduation_year or batch_no)
        $batches = [];
        try {
            $this->db->query('SELECT batch_no AS batch, COUNT(*) AS count FROM users WHERE batch_no IS NOT NULL GROUP BY batch_no ORDER BY batch_no');
            $batches = $this->db->resultSet();
        } catch (Exception $e) {
            // batch_no column might not exist
            $batches = [];
        }

        // Skills distribution (skills stored as JSON/TEXT of ids)
        $skillCounts = [];
        try {
            $this->db->query('SELECT skills FROM users WHERE skills IS NOT NULL');
            $rows = $this->db->resultSet();
            foreach ($rows as $row) {
                $val = $row->skills;
                $list = [];
                $decoded = json_decode($val, true);
                if (is_array($decoded)) {
                    $list = $decoded;
                } else {
                    // Fallback: comma-separated
                    $list = array_filter(array_map('trim', explode(',', (string)$val)));
                }
                foreach ($list as $skill) {
                    $key = (string)$skill;
                    $skillCounts[$key] = ($skillCounts[$key] ?? 0) + 1;
                }
            }
        } catch (Exception $e) {
            // skills column might not exist
            $skillCounts = [];
        }

        // Gender distribution
        $genders = [];
        try {
            $this->db->query('SELECT gender, COUNT(*) AS count FROM users GROUP BY gender');
            $genders = $this->db->resultSet();
        } catch (Exception $e) {
            $genders = [];
        }

        // Event status distribution
        $eventStatus = [];
        try {
            $this->db->query('SELECT status, COUNT(*) AS count FROM events GROUP BY status');
            $eventStatus = $this->db->resultSet();
        } catch (Exception $e) {
            $eventStatus = [];
        }

        // Event request status distribution
        $eventRequestStatus = [];
        try {
            $this->db->query('SELECT status, COUNT(*) AS count FROM event_requests GROUP BY status');
            $eventRequestStatus = $this->db->resultSet();
        } catch (Exception $e) {
            $eventRequestStatus = [];
        }

        return [
            'roles' => $roles,
            'batches' => $batches,
            'skills' => $skillCounts,
            'genders' => $genders,
            'event_status' => $eventStatus,
            'event_request_status' => $eventRequestStatus,
        ];
    }

    public function getAllUsers() {
        try {
            $this->db->query('SELECT id, name, email, role, batch_no, graduation_year, profile_image FROM users ORDER BY created_at DESC');
            return $this->db->resultSet();
        } catch (Exception $e) {
            // Some columns might not exist, try with basic columns
            try {
                $this->db->query('SELECT id, name, email, role FROM users ORDER BY created_at DESC');
                return $this->db->resultSet();
            } catch (Exception $e2) {
                // Return empty array if users table doesn't exist
                return [];
            }
        }
    }

    public function getDetailedOverview(): array {
        // Verified alumni (heuristic: role=alumni and maybe nic not null)
        $this->db->query("SELECT COUNT(*) AS c FROM users WHERE role='alumni'");
        $alumni = $this->db->single()->c ?? 0;

        $this->db->query("SELECT COUNT(*) AS c FROM users WHERE role='undergrad'");
        $students = $this->db->single()->c ?? 0;

        // Check if posts table exists before querying
        $posts = 0;
        try {
            $this->db->query("SELECT COUNT(*) AS c FROM Posts");
            $posts = $this->db->single()->c ?? 0;
        } catch (Exception $e) {
            // Posts table doesn't exist, set to 0
            $posts = 0;
        }

        $this->db->query("SELECT COUNT(*) AS c FROM users WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)");
        $new7 = $this->db->single()->c ?? 0;

        return [
            'alumni' => (int)$alumni,
            'students' => (int)$students,
            'posts' => (int)$posts,
            'new_last_7_days' => (int)$new7,
        ];
    }

    public function getRecentActivity(): array {
        // Simulate system activity from existing tables
        $posts = [];
        try {
            $this->db->query("SELECT id, title, created_at FROM Posts ORDER BY created_at DESC LIMIT 5");
            $posts = $this->db->resultSet();
        } catch (Exception $e) {
            // Posts table doesn't exist, return empty array
            $posts = [];
        }
        return [
            'posts' => $posts,
        ];
    }

    public function getEngagementMetrics(): array {
        // Posts, comments, reactions — comments/reactions tables may not exist yet; compute what exists
        $posts = $this->safeCount('SELECT COUNT(*) AS c FROM posts');
        $comments = $this->safeCount('SELECT COUNT(*) AS c FROM comments');
        $reactions = $this->safeCount('SELECT COUNT(*) AS c FROM post_likes');
        $messages = $this->safeCount('SELECT COUNT(*) AS c FROM messages');
        $events = $this->safeCount('SELECT COUNT(*) AS c FROM events');
        $eventAttendees = $this->safeCount('SELECT COUNT(*) AS c FROM event_attendees');
        $eventBookmarks = $this->safeCount('SELECT COUNT(*) AS c FROM event_bookmarks');
        $followers = $this->safeCount('SELECT COUNT(*) AS c FROM followers');
        $notifications = $this->safeCount('SELECT COUNT(*) AS c FROM notifications');
        $notificationsUnread = $this->safeCount("SELECT COUNT(*) AS c FROM notifications WHERE is_read = 0");
        $pendingAlumni = $this->safeCount("SELECT COUNT(*) AS c FROM unregisted_alumni WHERE status = 'pending'");
        $totalUsers = $this->safeCount('SELECT COUNT(*) AS c FROM users');

        // Activity over time (active users per month based on any activity)
        $overTime = $this->safeActiveUsersOverTime();

        $active30 = $this->safeActiveUsersWindow(30);
        $dau = $this->safeActiveUsersWindow(1);
        $wau = $this->safeActiveUsersWindow(7);
        $mau = $this->safeActiveUsersWindow(30);

        $avgPostsPerUser = $totalUsers > 0 ? round($posts / $totalUsers, 2) : 0;
        $avgCommentsPerPost = $posts > 0 ? round($comments / $posts, 2) : 0;
        $avgReactionsPerPost = $posts > 0 ? round($reactions / $posts, 2) : 0;
        $avgMessagesPerUser = $totalUsers > 0 ? round($messages / $totalUsers, 2) : 0;
        $engagementRate = $totalUsers > 0 ? round(($active30 / $totalUsers) * 100, 1) : 0;

        return [
            'posts' => (int)$posts,
            'comments' => (int)$comments,
            'reactions' => (int)$reactions,
            'messages' => (int)$messages,
            'events' => (int)$events,
            'event_attendees' => (int)$eventAttendees,
            'event_bookmarks' => (int)$eventBookmarks,
            'followers' => (int)$followers,
            'notifications' => (int)$notifications,
            'notifications_unread' => (int)$notificationsUnread,
            'pending_alumni' => (int)$pendingAlumni,
            'active_30_days' => (int)$active30,
            'dau' => (int)$dau,
            'wau' => (int)$wau,
            'mau' => (int)$mau,
            'avg_posts_per_user' => $avgPostsPerUser,
            'avg_comments_per_post' => $avgCommentsPerPost,
            'avg_reactions_per_post' => $avgReactionsPerPost,
            'avg_messages_per_user' => $avgMessagesPerUser,
            'engagement_rate' => $engagementRate,
            'active_over_time' => $overTime,
            'time_series' => $this->getTimeSeriesBundle(),
            'event_pipeline' => $this->getEventPipelineMetrics(),
            'profile_metrics' => $this->getProfileCompletionMetrics(),
        ];
    }

    private function safeCount(string $sql): int {
        try {
            $this->db->query($sql);
            $row = $this->db->single();
            return (int)($row->c ?? 0);
        } catch (Exception $e) {
            return 0;
        }
    }

    public function countUsersByRole(?string $role = null): int {
        try {
            if ($role === null) {
                $this->db->query('SELECT COUNT(*) AS c FROM users');
            } else {
                $this->db->query('SELECT COUNT(*) AS c FROM users WHERE role = :role');
                $this->db->bind(':role', $role);
            }
            return (int)($this->db->single()->c ?? 0);
        } catch (Exception $e) {
            return 0;
        }
    }

    private function safeActiveUsersWindow(int $days): int {
        try {
            $this->db->query("SELECT COUNT(DISTINCT user_id) AS c FROM (
                SELECT user_id FROM posts WHERE created_at >= DATE_SUB(NOW(), INTERVAL $days DAY)
                UNION ALL SELECT user_id FROM comments WHERE created_at >= DATE_SUB(NOW(), INTERVAL $days DAY)
                UNION ALL SELECT user_id FROM post_likes WHERE created_at >= DATE_SUB(NOW(), INTERVAL $days DAY)
                UNION ALL SELECT sender_id AS user_id FROM messages WHERE message_time >= DATE_SUB(NOW(), INTERVAL $days DAY)
                UNION ALL SELECT organizer_id AS user_id FROM events WHERE created_at >= DATE_SUB(NOW(), INTERVAL $days DAY)
            ) t");
            $row = $this->db->single();
            return (int)($row->c ?? 0);
        } catch (Exception $e) {
            try {
                $this->db->query("SELECT COUNT(*) AS c FROM users WHERE created_at >= DATE_SUB(NOW(), INTERVAL $days DAY)");
                $row = $this->db->single();
                return (int)($row->c ?? 0);
            } catch (Exception $e2) {
                return 0;
            }
        }
    }

    private function safeActiveUsersOverTime(): array {
        try {
            $this->db->query("SELECT ym, COUNT(DISTINCT user_id) AS c FROM (
                SELECT DATE_FORMAT(created_at, '%Y-%m') AS ym, user_id FROM posts
                UNION ALL SELECT DATE_FORMAT(created_at, '%Y-%m') AS ym, user_id FROM comments
                UNION ALL SELECT DATE_FORMAT(created_at, '%Y-%m') AS ym, user_id FROM post_likes
                UNION ALL SELECT DATE_FORMAT(message_time, '%Y-%m') AS ym, sender_id AS user_id FROM messages
            ) t GROUP BY ym ORDER BY ym ASC");
            return $this->db->resultSet();
        } catch (Exception $e) {
            try {
                $this->db->query("SELECT DATE_FORMAT(created_at, '%Y-%m') AS ym, COUNT(*) AS c FROM users GROUP BY ym ORDER BY ym ASC");
                return $this->db->resultSet();
            } catch (Exception $e2) {
                return [];
            }
        }
    }

    private function getTimeSeriesBundle(): array {
        return [
            'signups' => $this->safeTimeSeries('users', 'created_at'),
            'posts' => $this->safeTimeSeries('posts', 'created_at'),
            'comments' => $this->safeTimeSeries('comments', 'created_at'),
            'reactions' => $this->safeTimeSeries('post_likes', 'created_at'),
            'messages' => $this->safeTimeSeries('messages', 'message_time'),
            'events' => $this->safeTimeSeries('events', 'created_at'),
            'event_attendees' => $this->safeTimeSeries('event_attendees', 'created_at'),
        ];
    }

    private function safeTimeSeries(string $table, string $dateColumn): array {
        try {
            $this->db->query("SELECT DATE_FORMAT($dateColumn, '%Y-%m') AS ym, COUNT(*) AS c FROM $table GROUP BY ym ORDER BY ym ASC");
            return $this->db->resultSet();
        } catch (Exception $e) {
            return [];
        }
    }

    private function getEventPipelineMetrics(): array {
        $requests = [];
        $events = [];
        try {
            $this->db->query('SELECT status, COUNT(*) AS c FROM event_requests GROUP BY status');
            $requests = $this->db->resultSet();
        } catch (Exception $e) {
            $requests = [];
        }
        try {
            $this->db->query('SELECT status, COUNT(*) AS c FROM events GROUP BY status');
            $events = $this->db->resultSet();
        } catch (Exception $e) {
            $events = [];
        }
        return [
            'requests' => $requests,
            'events' => $events,
        ];
    }

    private function getProfileCompletionMetrics(): array {
        $completed = 0;
        $total = 0;
        $privateProfiles = 0;
        try {
            $this->db->query("SELECT COUNT(*) AS c FROM users");
            $total = (int)($this->db->single()->c ?? 0);
        } catch (Exception $e) {
            $total = 0;
        }
        try {
            $this->db->query("SELECT COUNT(*) AS c FROM users WHERE (bio IS NOT NULL AND bio != '') OR (skills IS NOT NULL AND skills != '') OR (profile_image IS NOT NULL AND profile_image != 'default.jpg') OR (display_name IS NOT NULL AND display_name != '')");
            $completed = (int)($this->db->single()->c ?? 0);
        } catch (Exception $e) {
            $completed = 0;
        }
        try {
            $this->db->query("SELECT COUNT(*) AS c FROM user_profiles_visibility WHERE is_public = 0");
            $privateProfiles = (int)($this->db->single()->c ?? 0);
        } catch (Exception $e) {
            $privateProfiles = 0;
        }

        $completionRate = $total > 0 ? round(($completed / $total) * 100, 1) : 0;
        return [
            'completed' => $completed,
            'total' => $total,
            'completion_rate' => $completionRate,
            'private_profiles' => $privateProfiles,
        ];
    }

    /**
     * Get engagement metrics filtered by user role
     * @param string|null $role - 'admin', 'alumni', 'undergrad', or null for all
     */
    public function getEngagementMetricsByRole(?string $role = null): array {
        // Get user IDs for the specified role (or all if null)
        $userIds = [];
        try {
            if ($role === null) {
                // All users
                $this->db->query('SELECT id FROM users');
            } else {
                // Specific role
                $this->db->query('SELECT id FROM users WHERE role = ?');
                $this->db->query('SELECT id FROM users WHERE role = :role');
                $this->db->bind(':role', $role);
            }
            $users = $this->db->resultSet();
            $userIds = array_map(function($u) { return $u->id; }, $users);
        } catch (Exception $e) {
            $userIds = [];
        }

        if (empty($userIds)) {
            return $this->getEmptyEngagementMetrics();
        }

        $idList = implode(',', $userIds);
        $totalUsers = count($userIds);

        // Count metrics from these users
        $posts = 0;
        try {
            $this->db->query("SELECT COUNT(*) AS c FROM posts WHERE user_id IN ($idList)");
            $posts = (int)($this->db->single()->c ?? 0);
        } catch (Exception $e) {
            $posts = 0;
        }

        $comments = 0;
        try {
            $this->db->query("SELECT COUNT(*) AS c FROM comments WHERE user_id IN ($idList)");
            $comments = (int)($this->db->single()->c ?? 0);
        } catch (Exception $e) {
            $comments = 0;
        }

        $reactions = 0;
        try {
            $this->db->query("SELECT COUNT(*) AS c FROM post_likes WHERE user_id IN ($idList)");
            $reactions = (int)($this->db->single()->c ?? 0);
        } catch (Exception $e) {
            $reactions = 0;
        }

        $messages = 0;
        try {
            $this->db->query("SELECT COUNT(*) AS c FROM messages WHERE sender_id IN ($idList)");
            $messages = (int)($this->db->single()->c ?? 0);
        } catch (Exception $e) {
            $messages = 0;
        }

        $events = 0;
        $eventAttendees = 0;
        try {
            $this->db->query("SELECT COUNT(*) AS c FROM events WHERE organizer_id IN ($idList)");
            $events = (int)($this->db->single()->c ?? 0);
            
            $this->db->query("SELECT COUNT(*) AS c FROM event_attendees WHERE user_id IN ($idList)");
            $eventAttendees = (int)($this->db->single()->c ?? 0);
        } catch (Exception $e) {
            $events = 0;
            $eventAttendees = 0;
        }

        $eventBookmarks = 0;
        try {
            $this->db->query("SELECT COUNT(*) AS c FROM event_bookmarks WHERE user_id IN ($idList)");
            $eventBookmarks = (int)($this->db->single()->c ?? 0);
        } catch (Exception $e) {
            $eventBookmarks = 0;
        }

        $followers = 0;
        try {
            $this->db->query("SELECT COUNT(*) AS c FROM followers WHERE follower_id IN ($idList)");
            $followers = (int)($this->db->single()->c ?? 0);
        } catch (Exception $e) {
            $followers = 0;
        }

        $notifications = 0;
        $notificationsUnread = 0;
        try {
            $this->db->query("SELECT COUNT(*) AS c FROM notifications WHERE user_id IN ($idList)");
            $notifications = (int)($this->db->single()->c ?? 0);

            $this->db->query("SELECT COUNT(*) AS c FROM notifications WHERE user_id IN ($idList) AND is_read = 0");
            $notificationsUnread = (int)($this->db->single()->c ?? 0);
        } catch (Exception $e) {
            $notifications = 0;
            $notificationsUnread = 0;
        }

        // Pending alumni (only if filtering by alumni)
        $pendingAlumni = 0;
        if ($role === 'alumni') {
            try {
                $this->db->query("SELECT COUNT(*) AS c FROM unregisted_alumni WHERE status = 'pending'");
                $pendingAlumni = (int)($this->db->single()->c ?? 0);
            } catch (Exception $e) {
                $pendingAlumni = 0;
            }
        }

        // Active users window (using user list)
        $active30 = $this->safeActiveUsersWindowForRole($userIds, 30);
        $dau = $this->safeActiveUsersWindowForRole($userIds, 1);
        $wau = $this->safeActiveUsersWindowForRole($userIds, 7);
        $mau = $this->safeActiveUsersWindowForRole($userIds, 30);

        // Calculate averages
        $avgPostsPerUser = $totalUsers > 0 ? round($posts / $totalUsers, 2) : 0;
        $avgCommentsPerPost = $posts > 0 ? round($comments / $posts, 2) : 0;
        $avgReactionsPerPost = $posts > 0 ? round($reactions / $posts, 2) : 0;
        $avgMessagesPerUser = $totalUsers > 0 ? round($messages / $totalUsers, 2) : 0;
        $engagementRate = $totalUsers > 0 ? round(($active30 / $totalUsers) * 100, 1) : 0;

        return [
            'posts' => (int)$posts,
            'comments' => (int)$comments,
            'reactions' => (int)$reactions,
            'messages' => (int)$messages,
            'events' => (int)$events,
            'event_attendees' => (int)$eventAttendees,
            'event_bookmarks' => (int)$eventBookmarks,
            'followers' => (int)$followers,
            'notifications' => (int)$notifications,
            'notifications_unread' => (int)$notificationsUnread,
            'pending_alumni' => (int)$pendingAlumni,
            'active_30_days' => (int)$active30,
            'dau' => (int)$dau,
            'wau' => (int)$wau,
            'mau' => (int)$mau,
            'avg_posts_per_user' => $avgPostsPerUser,
            'avg_comments_per_post' => $avgCommentsPerPost,
            'avg_reactions_per_post' => $avgReactionsPerPost,
            'avg_messages_per_user' => $avgMessagesPerUser,
            'engagement_rate' => $engagementRate,
            'time_series' => $this->getTimeSeriesBundleForRole($userIds),
            'event_pipeline' => $this->getEventPipelineMetricsForRole($userIds),
            'profile_metrics' => $this->getProfileCompletionMetricsForRole($userIds),
            'role_filtered' => $role,
        ];
    }

    /**
     * Get chart data filtered by user role
     */
    public function getChartDataByRole(?string $role = null): array {
        $userIds = [];
        try {
            if ($role === null) {
                $this->db->query('SELECT id FROM users');
            } else {
                $this->db->query('SELECT id FROM users WHERE role = :role');
                $this->db->bind(':role', $role);
            }
            $users = $this->db->resultSet();
            $userIds = array_map(function($u) { return $u->id; }, $users);
        } catch (Exception $e) {
            $userIds = [];
        }

        if (empty($userIds)) {
            return $this->getEmptyChartData();
        }

        $idList = implode(',', $userIds);

        // Distribution by role (only if not filtering by role)
        $roles = [];
        if ($role === null) {
            try {
                $this->db->query('SELECT role, COUNT(*) AS count FROM users GROUP BY role');
                $roles = $this->db->resultSet();
            } catch (Exception $e) {
                $roles = [];
            }
        }

        // Distribution by batch (filtered by user role if specified)
        $batches = [];
        try {
            if ($role === null) {
                $this->db->query('SELECT batch_no AS batch, COUNT(*) AS count FROM users WHERE batch_no IS NOT NULL GROUP BY batch_no ORDER BY batch_no');
            } else {
                $this->db->query("SELECT batch_no AS batch, COUNT(*) AS count FROM users WHERE role = :role AND batch_no IS NOT NULL GROUP BY batch_no ORDER BY batch_no");
                $this->db->bind(':role', $role);
            }
            $batches = $this->db->resultSet();
        } catch (Exception $e) {
            $batches = [];
        }

        // Gender distribution (filtered by user role if specified)
        $genders = [];
        try {
            if ($role === null) {
                $this->db->query('SELECT gender, COUNT(*) AS count FROM users GROUP BY gender');
            } else {
                $this->db->query("SELECT gender, COUNT(*) AS count FROM users WHERE role = :role GROUP BY gender");
                $this->db->bind(':role', $role);
            }
            $genders = $this->db->resultSet();
        } catch (Exception $e) {
            $genders = [];
        }

        // Skills distribution (filtered)
        $skillCounts = [];
        try {
            if ($role === null) {
                $this->db->query('SELECT skills FROM users WHERE skills IS NOT NULL');
            } else {
                $this->db->query('SELECT skills FROM users WHERE role = :role AND skills IS NOT NULL');
                $this->db->bind(':role', $role);
            }
            $rows = $this->db->resultSet();
            foreach ($rows as $row) {
                $val = $row->skills;
                $list = [];
                $decoded = json_decode($val, true);
                if (is_array($decoded)) {
                    $list = $decoded;
                } else {
                    $list = array_filter(array_map('trim', explode(',', (string)$val)));
                }
                foreach ($list as $skill) {
                    $key = (string)$skill;
                    $skillCounts[$key] = ($skillCounts[$key] ?? 0) + 1;
                }
            }
        } catch (Exception $e) {
            $skillCounts = [];
        }

        // Event status distribution
        $eventStatus = [];
        try {
            $this->db->query('SELECT status, COUNT(*) AS count FROM events GROUP BY status');
            $eventStatus = $this->db->resultSet();
        } catch (Exception $e) {
            $eventStatus = [];
        }

        // Event request status distribution
        $eventRequestStatus = [];
        try {
            $this->db->query('SELECT status, COUNT(*) AS count FROM event_requests GROUP BY status');
            $eventRequestStatus = $this->db->resultSet();
        } catch (Exception $e) {
            $eventRequestStatus = [];
        }

        return [
            'roles' => $roles,
            'batches' => $batches,
            'skills' => $skillCounts,
            'genders' => $genders,
            'event_status' => $eventStatus,
            'event_request_status' => $eventRequestStatus,
        ];
    }

    /**
     * Private helper methods for role-based filtering
     */
    private function safeActiveUsersWindowForRole(array $userIds, int $days): int {
        if (empty($userIds)) return 0;
        
        $idList = implode(',', $userIds);
        try {
            $this->db->query("SELECT COUNT(DISTINCT user_id) AS c FROM (
                SELECT user_id FROM posts WHERE user_id IN ($idList) AND created_at >= DATE_SUB(NOW(), INTERVAL $days DAY)
                UNION ALL SELECT user_id FROM comments WHERE user_id IN ($idList) AND created_at >= DATE_SUB(NOW(), INTERVAL $days DAY)
                UNION ALL SELECT user_id FROM post_likes WHERE user_id IN ($idList) AND created_at >= DATE_SUB(NOW(), INTERVAL $days DAY)
                UNION ALL SELECT sender_id AS user_id FROM messages WHERE sender_id IN ($idList) AND message_time >= DATE_SUB(NOW(), INTERVAL $days DAY)
            ) t");
            $row = $this->db->single();
            return (int)($row->c ?? 0);
        } catch (Exception $e) {
            return 0;
        }
    }

    private function getTimeSeriesBundleForRole(array $userIds): array {
        if (empty($userIds)) return [];
        
        $idList = implode(',', $userIds);
        
        return [
            'signups' => $this->safeTimeSeriesForRole($idList, 'users', 'created_at'),
            'posts' => $this->safeTimeSeriesForRole($idList, 'posts', 'created_at', 'user_id'),
            'comments' => $this->safeTimeSeriesForRole($idList, 'comments', 'created_at', 'user_id'),
            'reactions' => $this->safeTimeSeriesForRole($idList, 'post_likes', 'created_at', 'user_id'),
            'messages' => $this->safeTimeSeriesForRole($idList, 'messages', 'message_time', 'sender_id'),
            'events' => $this->safeTimeSeriesForRole($idList, 'events', 'created_at', 'organizer_id'),
            'event_attendees' => $this->safeTimeSeriesForRole($idList, 'event_attendees', 'created_at', 'user_id'),
        ];
    }

    private function safeTimeSeriesForRole(string $idList, string $table, string $dateColumn, ?string $userColumn = 'id'): array {
        try {
            if ($userColumn === 'id') {
                // For users table
                $this->db->query("SELECT DATE_FORMAT($dateColumn, '%Y-%m') AS ym, COUNT(*) AS c FROM $table WHERE id IN ($idList) GROUP BY ym ORDER BY ym ASC");
            } else {
                // For other tables with user_id or sender_id
                $this->db->query("SELECT DATE_FORMAT($dateColumn, '%Y-%m') AS ym, COUNT(*) AS c FROM $table WHERE $userColumn IN ($idList) GROUP BY ym ORDER BY ym ASC");
            }
            return $this->db->resultSet();
        } catch (Exception $e) {
            return [];
        }
    }

    private function getEventPipelineMetricsForRole(array $userIds): array {
        if (empty($userIds)) return ['requests' => [], 'events' => []];
        
        $idList = implode(',', $userIds);
        $requests = [];
        $events = [];
        
        try {
            $this->db->query("SELECT status, COUNT(*) AS c FROM event_requests WHERE organizer_id IN ($idList) GROUP BY status");
            $requests = $this->db->resultSet();
        } catch (Exception $e) {
            $requests = [];
        }
        
        try {
            $this->db->query("SELECT status, COUNT(*) AS c FROM events WHERE organizer_id IN ($idList) GROUP BY status");
            $events = $this->db->resultSet();
        } catch (Exception $e) {
            $events = [];
        }
        
        return ['requests' => $requests, 'events' => $events];
    }

    private function getProfileCompletionMetricsForRole(array $userIds): array {
        if (empty($userIds)) {
            return ['completed' => 0, 'total' => 0, 'completion_rate' => 0, 'private_profiles' => 0];
        }
        
        $idList = implode(',', $userIds);
        $completed = 0;
        $total = count($userIds);
        $privateProfiles = 0;
        
        try {
            $this->db->query("SELECT COUNT(*) AS c FROM users WHERE id IN ($idList) AND ((bio IS NOT NULL AND bio != '') OR (skills IS NOT NULL AND skills != '') OR (profile_image IS NOT NULL AND profile_image != 'default.jpg') OR (display_name IS NOT NULL AND display_name != ''))");
            $completed = (int)($this->db->single()->c ?? 0);
        } catch (Exception $e) {
            $completed = 0;
        }
        
        try {
            $this->db->query("SELECT COUNT(*) AS c FROM user_profiles_visibility WHERE user_id IN ($idList) AND is_public = 0");
            $privateProfiles = (int)($this->db->single()->c ?? 0);
        } catch (Exception $e) {
            $privateProfiles = 0;
        }

        $completionRate = $total > 0 ? round(($completed / $total) * 100, 1) : 0;
        return [
            'completed' => $completed,
            'total' => $total,
            'completion_rate' => $completionRate,
            'private_profiles' => $privateProfiles,
        ];
    }

    private function getEmptyEngagementMetrics(): array {
        return [
            'posts' => 0, 'comments' => 0, 'reactions' => 0, 'messages' => 0, 'events' => 0,
            'event_attendees' => 0, 'event_bookmarks' => 0, 'followers' => 0, 'notifications' => 0,
            'notifications_unread' => 0, 'pending_alumni' => 0, 'active_30_days' => 0,
            'dau' => 0, 'wau' => 0, 'mau' => 0, 'avg_posts_per_user' => 0, 'avg_comments_per_post' => 0,
            'avg_reactions_per_post' => 0, 'avg_messages_per_user' => 0, 'engagement_rate' => 0,
            'time_series' => [], 'event_pipeline' => [], 'profile_metrics' => [],
            'role_filtered' => 'none',
        ];
    }

    private function getEmptyChartData(): array {
        return ['roles' => [], 'batches' => [], 'skills' => [], 'genders' => [], 'event_status' => [], 'event_request_status' => []];
    }

    /**
     * Authenticate admin user
     */
    public function authenticateAdmin(string $email, string $password): ?object {
        // Query for admin user with role 'admin'
        $this->db->query('SELECT * FROM users WHERE email = :email AND role = "admin" LIMIT 1');
        $this->db->bind(':email', $email);
        
        $admin = $this->db->single();
        
        // if ($admin && password_verify($password, $admin->password)) {
        //     return $admin;
        // }
        if ($admin && ($password == $admin->password)) {
            return $admin;
        }
        
        return null;
    }

    /**
     * Get admin user by ID
     */
    public function getAdminById(int $id): ?object {
        $this->db->query('SELECT * FROM users WHERE id = :id AND role = "admin" LIMIT 1');
        $this->db->bind(':id', $id);
        return $this->db->single();
    }
}
?>


