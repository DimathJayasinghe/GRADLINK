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
            $this->db->query('SELECT id, name, email, role, batch_no, profile_image, special_alumni FROM users ORDER BY created_at DESC');
            return $this->db->resultSet();
        } catch (Exception $e) {
            return [];
        }
    }

    public function setSpecialAlumniStatus(int $userId, bool $isSpecial): array {
        if ($userId <= 0) {
            return ['ok' => false, 'message' => 'Invalid user ID.'];
        }

        try {
            $this->db->query('UPDATE users SET special_alumni = :special_alumni WHERE id = :id');
            $this->db->bind(':special_alumni', $isSpecial ? 1 : 0);
            $this->db->bind(':id', $userId);

            if ($this->db->execute()) {
                return [
                    'ok' => true,
                    'message' => $isSpecial ? 'User marked as special alumni.' : 'Special alumni status removed.',
                ];
            }

            return ['ok' => false, 'message' => 'Failed to update special alumni status.'];
        } catch (Exception $e) {
            return ['ok' => false, 'message' => 'Failed to update special alumni status.'];
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

    public function getPostReports(): array {
        $all = [];

        try {
            // Preferred source: generic reports table used by /report/submitReport/post.
            if ($this->tableExists('reports') && $this->columnExists('reports', 'report_type') && $this->columnExists('reports', 'reported_item_id')) {
                $idColumn = $this->columnExists('reports', 'report_id') ? 'report_id' : ($this->columnExists('reports', 'id') ? 'id' : null);
                if ($idColumn !== null) {
                    $this->db->query("SELECT
                                        r.$idColumn AS id,
                                        'reports' AS source,
                                        r.reported_item_id AS post_id,
                                        p.user_id AS post_owner_id,
                                        r.reporter_id,
                                        r.category,
                                        r.details,
                                        r.link AS reference_link,
                                        r.status,
                                        r.created_at,
                                        r.updated_at,
                                        p.content AS post_content,
                                        p.created_at AS post_created_at,
                                        reporter.name AS reporter_name,
                                        reporter.role AS reporter_role,
                                        owner.name AS owner_name,
                                        owner.role AS owner_role
                                    FROM reports r
                                    LEFT JOIN posts p ON p.id = r.reported_item_id
                                    LEFT JOIN users reporter ON reporter.id = r.reporter_id
                                    LEFT JOIN users owner ON owner.id = p.user_id
                                    WHERE r.report_type = 'post'
                                    ORDER BY r.created_at DESC");
                    $rows = $this->db->resultSet();
                    if (is_array($rows)) {
                        $all = array_merge($all, $rows);
                    }
                }
            }

            // Legacy source: post_reports table.
            if ($this->tableExists('post_reports')) {
                $this->db->query("SELECT
                                    r.id,
                                    'post_reports' AS source,
                                    r.post_id,
                                    COALESCE(p.user_id, r.post_owner_id) AS post_owner_id,
                                    r.reporter_id,
                                    r.category,
                                    r.details,
                                    r.reference_link,
                                    r.status,
                                    r.created_at,
                                    r.updated_at,
                                    p.content AS post_content,
                                    p.created_at AS post_created_at,
                                    reporter.name AS reporter_name,
                                    reporter.role AS reporter_role,
                                    owner.name AS owner_name,
                                    owner.role AS owner_role
                                FROM post_reports r
                                LEFT JOIN posts p ON p.id = r.post_id
                                LEFT JOIN users reporter ON reporter.id = r.reporter_id
                                LEFT JOIN users owner ON owner.id = COALESCE(p.user_id, r.post_owner_id)
                                ORDER BY r.created_at DESC");
                $rows = $this->db->resultSet();
                if (is_array($rows)) {
                    $all = array_merge($all, $rows);
                }
            }

            if (!empty($all)) {
                usort($all, function ($a, $b) {
                    $aTs = strtotime((string)($a->created_at ?? '')) ?: 0;
                    $bTs = strtotime((string)($b->created_at ?? '')) ?: 0;
                    return $bTs <=> $aTs;
                });
            }

            return $all;
        } catch (Throwable $e) {
            error_log("Error fetching post reports: " . $e->getMessage());
            return [];
        }
    }

    public function getProfileReports(): array {
        try {
            if (!$this->tableExists('reports') || !$this->columnExists('reports', 'report_type') || !$this->columnExists('reports', 'reported_item_id')) {
                return [];
            }

            $idColumn = $this->columnExists('reports', 'report_id') ? 'report_id' : ($this->columnExists('reports', 'id') ? 'id' : null);
            if ($idColumn === null) {
                return [];
            }

            $this->db->query("SELECT
                                r.$idColumn AS id,
                                'reports' AS source,
                                r.reported_item_id AS profile_id,
                                r.reporter_id,
                                r.category,
                                r.details,
                                r.link AS reference_link,
                                r.status,
                                r.created_at,
                                r.updated_at,
                                reporter.name AS reporter_name,
                                reporter.role AS reporter_role,
                                target.name AS target_name,
                                target.role AS target_role,
                                target.profile_image AS target_profile_image
                            FROM reports r
                            LEFT JOIN users reporter ON reporter.id = r.reporter_id
                            LEFT JOIN users target ON target.id = r.reported_item_id
                            WHERE r.report_type = 'profile'
                            ORDER BY r.created_at DESC");
            return $this->db->resultSet();
        } catch (Throwable $e) {
            error_log("Error fetching profile reports: " . $e->getMessage());
            return [];
        }
    }

    public function updateContentReportStatus(int $reportId, string $status, int $adminId, string $source = 'reports'): bool {
        try {
            if ($reportId <= 0) {
                return false;
            }

            if (!in_array($status, ['pending', 'resolved', 'rejected'], true)) {
                return false;
            }

            $safeSource = strtolower(trim($source));

            if ($safeSource === 'post_reports') {
                if ($this->updatePostReportsStatus($reportId, $status)) {
                    return true;
                }
                return $this->updateGenericReportsStatus($reportId, $status, $adminId);
            }

            if ($this->updateGenericReportsStatus($reportId, $status, $adminId)) {
                return true;
            }

            return $this->updatePostReportsStatus($reportId, $status);
        } catch (Throwable $e) {
            error_log('Error updating content report status: ' . $e->getMessage());
            return false;
        }
    }

    private function updateGenericReportsStatus(int $reportId, string $status, int $adminId): bool {
        if (!$this->tableExists('reports')) {
            return false;
        }

        // Resolve schema flags once. Avoid schema helper calls after preparing UPDATE,
        // because those helpers replace the active PDO statement in Database.
        $hasUpdatedAt = $this->columnExists('reports', 'updated_at');
        $hasReviewedBy = $this->columnExists('reports', 'reviewed_by');
        $hasReviewedAt = $this->columnExists('reports', 'reviewed_at');

        $pkCandidates = [];
        if ($this->columnExists('reports', 'report_id')) {
            $pkCandidates[] = 'report_id';
        }
        if ($this->columnExists('reports', 'id')) {
            $pkCandidates[] = 'id';
        }
        if (empty($pkCandidates)) {
            $pkCandidates = ['report_id', 'id'];
        }

        foreach (array_unique($pkCandidates) as $idColumn) {
            try {
                $setParts = ['status = :status'];
                if ($hasUpdatedAt) {
                    $setParts[] = 'updated_at = NOW()';
                }
                if ($hasReviewedBy) {
                    $setParts[] = 'reviewed_by = :reviewed_by';
                }
                if ($hasReviewedAt) {
                    $setParts[] = 'reviewed_at = NOW()';
                }

                $sql = 'UPDATE reports SET ' . implode(', ', $setParts) . " WHERE $idColumn = :id";
                $this->db->query($sql);
                $this->db->bind(':status', $status);
                if ($hasReviewedBy) {
                    $this->db->bind(':reviewed_by', $adminId > 0 ? $adminId : null);
                }
                $this->db->bind(':id', $reportId);

                if (!$this->db->execute()) {
                    continue;
                }

                if ($this->db->rowCount() > 0) {
                    return true;
                }

                // If rowCount is 0, it may still be successful when status is unchanged.
                $this->db->query("SELECT 1 AS found_row FROM reports WHERE $idColumn = :id LIMIT 1");
                $this->db->bind(':id', $reportId);
                if ($this->db->single()) {
                    return true;
                }
            } catch (Throwable $e) {
            }
        }

        return false;
    }

    private function updatePostReportsStatus(int $reportId, string $status): bool {
        if (!$this->tableExists('post_reports')) {
            return false;
        }

        try {
            $setParts = ['status = :status'];
            if ($this->columnExists('post_reports', 'updated_at')) {
                $setParts[] = 'updated_at = NOW()';
            }

            $sql = 'UPDATE post_reports SET ' . implode(', ', $setParts) . ' WHERE id = :id';
            $this->db->query($sql);
            $this->db->bind(':status', $status);
            $this->db->bind(':id', $reportId);
            if (!$this->db->execute()) {
                return false;
            }

            if ($this->db->rowCount() > 0) {
                return true;
            }

            $this->db->query('SELECT 1 AS found_row FROM post_reports WHERE id = :id LIMIT 1');
            $this->db->bind(':id', $reportId);
            return $this->db->single() ? true : false;
        } catch (Throwable $e) {
            error_log('Error updating post_reports status: ' . $e->getMessage());
            return false;
        }
    }

    private function tableExists(string $table): bool {
        try {
            $safeTable = preg_replace('/[^A-Za-z0-9_]/', '', $table);
            if ($safeTable === '') {
                return false;
            }

            $this->db->query("SHOW TABLES LIKE '$safeTable'");
            $row = $this->db->single();
            return $row ? true : false;
        } catch (Throwable $e) {
            return false;
        }
    }

    private function columnExists(string $table, string $column): bool {
        try {
            $safeTable = preg_replace('/[^A-Za-z0-9_]/', '', $table);
            $safeColumn = preg_replace('/[^A-Za-z0-9_]/', '', $column);
            if ($safeTable === '' || $safeColumn === '') {
                return false;
            }

            $this->db->query("SHOW COLUMNS FROM `$safeTable` LIKE '$safeColumn'");
            $row = $this->db->single();
            return $row ? true : false;
        } catch (Throwable $e) {
            return false;
        }
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

    // ==================== USER SUSPENSION METHODS ====================

    private function ensureSuspendedUsersTable(): void {
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
    }

    public function isUserActivelySuspended(int $userId): bool {
        try {
            $this->ensureSuspendedUsersTable();
            $this->db->query("SELECT 1 FROM suspended_users WHERE user_id = :user_id AND status = 'active' LIMIT 1");
            $this->db->bind(':user_id', $userId);
            return (bool)$this->db->single();
        } catch (Exception $e) {
            return false;
        }
    }

    public function getActiveSuspendedUsers(): array {
        try {
            $this->ensureSuspendedUsersTable();
            $this->db->query("SELECT
                    su.id AS suspension_id,
                    su.user_id,
                    COALESCE(u.name, su.snapshot_name, 'Unknown User') AS name,
                    COALESCE(u.email, su.snapshot_email, '-') AS email,
                    COALESCE(u.role, su.snapshot_role, '-') AS role,
                    su.reason,
                    su.suspended_at,
                    sb.name AS suspended_by_name
                FROM suspended_users su
                LEFT JOIN users u ON u.id = su.user_id
                LEFT JOIN users sb ON sb.id = su.suspended_by
                WHERE su.status = 'active'
                ORDER BY su.suspended_at DESC");
            return $this->db->resultSet();
        } catch (Exception $e) {
            return [];
        }
    }

    /**
     * Get user locations (countries) for map visualization
     * @param string|null $role - Filter by role (admin, alumni, undergrad)
     * @param string|null $batch - Filter by batch number
     * @param string|null $country - Filter by country
     * @return array - Array of location data with user counts
     */
    public function getUserLocations(?string $role = null, ?string $batch = null, ?string $country = null): array {
        try {
            // Build dynamic query
            $sql = "SELECT 
                        ul.country,
                        COUNT(DISTINCT ul.user_id) AS user_count,
                        GROUP_CONCAT(DISTINCT u.role ORDER BY u.role SEPARATOR ', ') AS roles,
                        GROUP_CONCAT(DISTINCT u.batch_no ORDER BY u.batch_no SEPARATOR ', ') AS batches
                    FROM user_locations ul
                    INNER JOIN users u ON ul.user_id = u.id
                    WHERE 1=1";
            
            // Add filters
            $params = [];
            if ($role !== null && $role !== '') {
                $sql .= " AND u.role = :role";
                $params[':role'] = $role;
            }
            if ($batch !== null && $batch !== '') {
                $sql .= " AND u.batch_no = :batch";
                $params[':batch'] = $batch;
            }
            if ($country !== null && $country !== '') {
                $sql .= " AND ul.country = :country";
                $params[':country'] = $country;
            }
            
            $sql .= " GROUP BY ul.country
                      ORDER BY user_count DESC";
            
            $this->db->query($sql);
            foreach ($params as $param => $value) {
                $this->db->bind($param, $value);
            }
            
            return $this->db->resultSet();
        } catch (Exception $e) {
            return [];
        }
    }

    public function getSuspensionHistory(int $limit = 100): array {
        try {
            $this->ensureSuspendedUsersTable();
            $this->db->query("SELECT
                    su.id AS suspension_id,
                    su.user_id,
                    COALESCE(u.name, su.snapshot_name, 'Removed User') AS name,
                    COALESCE(u.email, su.snapshot_email, '-') AS email,
                    COALESCE(u.role, su.snapshot_role, '-') AS role,
                    su.reason,
                    su.status,
                    su.suspended_at,
                    su.lifted_at,
                    su.removed_at,
                    sb.name AS suspended_by_name,
                    lb.name AS lifted_by_name,
                    rb.name AS removed_by_name
                FROM suspended_users su
                LEFT JOIN users u ON u.id = su.user_id
                LEFT JOIN users sb ON sb.id = su.suspended_by
                LEFT JOIN users lb ON lb.id = su.lifted_by
                LEFT JOIN users rb ON rb.id = su.removed_by
                WHERE su.status IN ('lifted', 'removed')
                ORDER BY su.suspended_at DESC
                LIMIT :limit");
            $this->db->bind(':limit', max(1, (int)$limit), PDO::PARAM_INT);
            return $this->db->resultSet();
        } catch (Exception $e) {
            return [];
        }
    }

    public function getSuspensionById(int $suspensionId): ?object {
        try {
            $this->ensureSuspendedUsersTable();
            $this->db->query("SELECT * FROM suspended_users WHERE id = :id LIMIT 1");
            $this->db->bind(':id', $suspensionId);
            $row = $this->db->single();
            return $row ?: null;
        } catch (Exception $e) {
            return null;
        }
    }

    public function suspendUser(int $userId, int $adminId, string $reason = ''): array {
        try {
            $this->ensureSuspendedUsersTable();

            $this->db->query("SELECT id, name, email, role FROM users WHERE id = :id LIMIT 1");
            $this->db->bind(':id', $userId);
            $user = $this->db->single();

            if (!$user) {
                return ['ok' => false, 'message' => 'User not found'];
            }

            if ($this->isProtectedAdminAccount($user)) {
                return ['ok' => false, 'message' => 'System administrator accounts cannot be suspended'];
            }

            $this->db->query("SELECT id FROM suspended_users WHERE user_id = :user_id AND status = 'active' LIMIT 1");
            $this->db->bind(':user_id', $userId);
            $active = $this->db->single();

            if ($active) {
                $this->db->query("UPDATE suspended_users
                                SET suspended_by = :admin_id,
                                    reason = :reason,
                                    suspended_at = NOW(),
                                    snapshot_name = :snapshot_name,
                                    snapshot_email = :snapshot_email,
                                    snapshot_role = :snapshot_role
                                WHERE id = :id");
                $this->db->bind(':admin_id', $adminId);
                $this->db->bind(':reason', $reason !== '' ? $reason : null);
                $this->db->bind(':snapshot_name', $user->name ?? null);
                $this->db->bind(':snapshot_email', $user->email ?? null);
                $this->db->bind(':snapshot_role', $user->role ?? null);
                $this->db->bind(':id', (int)$active->id);
                $this->db->execute();

                return ['ok' => true, 'message' => 'User suspension was refreshed'];
            }

            $this->db->query("INSERT INTO suspended_users
                            (user_id, suspended_by, reason, status, suspended_at, snapshot_name, snapshot_email, snapshot_role)
                            VALUES
                            (:user_id, :suspended_by, :reason, 'active', NOW(), :snapshot_name, :snapshot_email, :snapshot_role)");
            $this->db->bind(':user_id', $userId);
            $this->db->bind(':suspended_by', $adminId);
            $this->db->bind(':reason', $reason !== '' ? $reason : null);
            $this->db->bind(':snapshot_name', $user->name ?? null);
            $this->db->bind(':snapshot_email', $user->email ?? null);
            $this->db->bind(':snapshot_role', $user->role ?? null);
            $this->db->execute();

            return ['ok' => true, 'message' => 'User suspended successfully'];
        } catch (Exception $e) {
            return ['ok' => false, 'message' => 'Failed to suspend user'];
        }
    }

    private function isProtectedAdminAccount(object $user): bool {
        $role = strtolower(trim((string)($user->role ?? '')));
        $name = strtolower(trim((string)($user->name ?? '')));
        $email = strtolower(trim((string)($user->email ?? '')));

        if (
            $role === 'admin' ||
            $role === 'administrator' ||
            $role === 'system_admin' ||
            $role === 'system-administrator' ||
            $role === 'super_admin' ||
            strpos($role, 'admin') !== false
        ) {
            return true;
        }

        if ($name === 'system administrator' || $email === 'admin@gradlink.com') {
            return true;
        }

        return false;
    }

    public function liftSuspension(int $suspensionId, int $adminId): array {
        try {
            $this->ensureSuspendedUsersTable();
            $this->db->query("SELECT id FROM suspended_users WHERE id = :id AND status = 'active' LIMIT 1");
            $this->db->bind(':id', $suspensionId);
            $row = $this->db->single();

            if (!$row) {
                return ['ok' => false, 'message' => 'Active suspension not found'];
            }

            $this->db->query("UPDATE suspended_users
                            SET status = 'lifted',
                                lifted_at = NOW(),
                                lifted_by = :lifted_by
                            WHERE id = :id");
            $this->db->bind(':lifted_by', $adminId);
            $this->db->bind(':id', $suspensionId);
            $this->db->execute();

            return ['ok' => true, 'message' => 'Suspension removed'];
        } catch (Exception $e) {
            return ['ok' => false, 'message' => 'Failed to remove suspension'];
        }
    }

    public function markSuspensionRemoved(int $suspensionId, int $adminId): array {
        try {
            $this->ensureSuspendedUsersTable();
            $this->db->query("UPDATE suspended_users
                            SET status = 'removed',
                                removed_at = NOW(),
                                removed_by = :removed_by,
                                lifted_at = IFNULL(lifted_at, NOW()),
                                lifted_by = IFNULL(lifted_by, :removed_by_2)
                            WHERE id = :id");
            $this->db->bind(':removed_by', $adminId);
            $this->db->bind(':removed_by_2', $adminId);
            $this->db->bind(':id', $suspensionId);
            $this->db->execute();

            if ($this->db->rowCount() < 1) {
                return ['ok' => false, 'message' => 'Suspension record not found'];
            }

            return ['ok' => true, 'message' => 'Suspension updated as removed'];
        } catch (Exception $e) {
            return ['ok' => false, 'message' => 'Failed to update suspension status'];
        }
    }

    public function deleteUserCompletely(int $userId): array {
        if ($userId <= 0) {
            return ['ok' => false, 'message' => 'Invalid user ID'];
        }

        try {
            $this->db->beginTransaction();

            $cleanupQueries = [
                "DELETE FROM certificates WHERE user_id = :user_id",
                "DELETE FROM comments WHERE user_id = :user_id",
                "DELETE FROM post_likes WHERE user_id = :user_id",
                "DELETE FROM posts WHERE user_id = :user_id",
                "DELETE FROM notifications WHERE receiver_id = :user_id OR sender_id = :user_id",
                "DELETE FROM follow_requests WHERE requester_id = :user_id OR target_id = :user_id",
                "DELETE FROM followers WHERE follower_id = :user_id OR followed_id = :user_id",
                "DELETE FROM message_unread_tracker WHERE sender_id = :user_id OR receiver_id = :user_id",
                "DELETE FROM messages WHERE sender_id = :user_id OR receiver_id = :user_id",
                "DELETE FROM event_attendees WHERE user_id = :user_id",
                "DELETE FROM event_bookmarks WHERE user_id = :user_id",
                "DELETE FROM bookmarks WHERE user_id = :user_id",
                "DELETE FROM event_images WHERE event_id IN (SELECT id FROM events WHERE organizer_id = :user_id)",
                "DELETE FROM event_requests WHERE user_id = :user_id",
                "DELETE FROM events WHERE organizer_id = :user_id",
                "DELETE FROM fundraising_team_members WHERE user_id = :user_id",
                "DELETE FROM fundraising_bank_details WHERE request_id IN (SELECT id FROM fundraising_requests WHERE user_id = :user_id)",
                "DELETE FROM fundraising_donations WHERE donor_user_id = :user_id OR request_id IN (SELECT id FROM fundraising_requests WHERE user_id = :user_id)",
                "DELETE FROM fundraising_requests WHERE user_id = :user_id OR advisor_id = :user_id",
                "DELETE FROM user_profiles_visibility WHERE user_id = :user_id",
                "DELETE FROM online_users WHERE user_id = :user_id",
                "DELETE FROM access_logs WHERE user_id = :user_id"
            ];

            foreach ($cleanupQueries as $sql) {
                try {
                    $this->db->query($sql);
                    $this->db->bind(':user_id', $userId);
                    $this->db->execute();
                } catch (Exception $ignored) {
                    // Tolerate missing optional tables to keep removal resilient.
                }
            }

            $this->db->query("DELETE FROM users WHERE id = :user_id");
            $this->db->bind(':user_id', $userId);
            $this->db->execute();

            if ($this->db->rowCount() < 1) {
                $this->db->rollBack();
                return ['ok' => false, 'message' => 'User was already removed or not found'];
            }

            $this->db->commit();
            return ['ok' => true, 'message' => 'User removed from system'];
        } catch (Exception $e) {
            try {
                $this->db->rollBack();
            } catch (Exception $ignored) {
            }
            return ['ok' => false, 'message' => 'Failed to remove user from system'];
        }
    }

    // ==================== FUNDRAISER ADMIN METHODS ====================

    /**
     * Get all fundraisers for admin panel (all statuses)
     */
    public function getAllFundraisersForAdmin() {
        $this->db->query("
            SELECT 
                fr.id as req_id,
                fr.user_id,
                fr.club_name,
                fr.requester_position,
                fr.requester_phone,
                fr.title,
                fr.headline,
                fr.description,
                fr.project_poster,
                fr.goal_amount as target_amount,
                fr.collected_amount as raised_amount,
                fr.objective,
                fr.start_date,
                fr.end_date as deadline,
                fr.fund_manager_name,
                fr.fund_manager_contact,
                fr.advisor_id,
                fr.status,
                fr.rejection_reason,
                fr.created_at,
                fr.updated_at,
                u.name as user_name,
                u.display_name,
                u.email as user_email,
                u.profile_image
            FROM fundraising_requests fr
            JOIN users u ON fr.user_id = u.id
            ORDER BY fr.created_at DESC
        ");
        
        return $this->db->resultSet();
    }

    /**
     * Get fundraiser statistics for KPI dashboard
     */
    public function getFundraiserStats() {
        $stats = [
            'open_campaigns' => 0,
            'total_raised' => 0,
            'active_clubs' => 0,
            'expiring_soon' => 0,
            'pending_count' => 0,
            'approved_count' => 0,
            'rejected_count' => 0
        ];

        try {
            // Open campaigns (Approved/Active)
            $this->db->query("SELECT COUNT(*) as c FROM fundraising_requests WHERE status IN ('Approved', 'Active')");
            $stats['open_campaigns'] = (int)($this->db->single()->c ?? 0);

            // Total raised from successful donations
            $this->db->query("SELECT COALESCE(SUM(amount), 0) as total FROM fundraising_donations WHERE status = 'Successful'");
            $stats['total_raised'] = (float)($this->db->single()->total ?? 0);

            // Active clubs (unique clubs with approved campaigns)
            $this->db->query("SELECT COUNT(DISTINCT club_name) as c FROM fundraising_requests WHERE status IN ('Approved', 'Active')");
            $stats['active_clubs'] = (int)($this->db->single()->c ?? 0);

            // Expiring soon (within 7 days)
            $this->db->query("SELECT COUNT(*) as c FROM fundraising_requests WHERE status IN ('Approved', 'Active') AND end_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 7 DAY)");
            $stats['expiring_soon'] = (int)($this->db->single()->c ?? 0);

            // Pending count
            $this->db->query("SELECT COUNT(*) as c FROM fundraising_requests WHERE status = 'Pending'");
            $stats['pending_count'] = (int)($this->db->single()->c ?? 0);

            // Approved count
            $this->db->query("SELECT COUNT(*) as c FROM fundraising_requests WHERE status IN ('Approved', 'Active')");
            $stats['approved_count'] = (int)($this->db->single()->c ?? 0);

            // Rejected count
            $this->db->query("SELECT COUNT(*) as c FROM fundraising_requests WHERE status = 'Rejected'");
            $stats['rejected_count'] = (int)($this->db->single()->c ?? 0);

        } catch (Exception $e) {
            error_log("Error getting fundraiser stats: " . $e->getMessage());
        }

        return $stats;
    }

    /**
     * Get unique clubs for filter dropdown
     */
    public function getUniqueClubs() {
        $this->db->query("SELECT DISTINCT club_name FROM fundraising_requests ORDER BY club_name ASC");
        return $this->db->resultSet();
    }

    /**
     * Get full fundraiser details including bank and team
     */
    public function getFundraiserFullDetails($id) {
        // Get main fundraiser data
        $this->db->query("
            SELECT 
                fr.*,
                fr.goal_amount as target_amount,
                fr.collected_amount as raised_amount,
                fr.end_date as deadline,
                u.name as user_name,
                u.display_name,
                u.email as user_email,
                u.profile_image,
                advisor.name as advisor_name,
                advisor.email as advisor_email
            FROM fundraising_requests fr
            JOIN users u ON fr.user_id = u.id
            LEFT JOIN users advisor ON fr.advisor_id = advisor.id
            WHERE fr.id = :id
        ");
        $this->db->bind(':id', $id);
        $fundraiser = $this->db->single();

        if (!$fundraiser) {
            return null;
        }

        // Get bank details
        $this->db->query("SELECT * FROM fundraising_bank_details WHERE request_id = :id");
        $this->db->bind(':id', $id);
        $fundraiser->bank_details = $this->db->single();

        // Get team members
        $this->db->query("
            SELECT u.id as user_id, u.name, u.display_name, u.email, u.profile_image
            FROM fundraising_team_members ftm
            JOIN users u ON ftm.user_id = u.id
            WHERE ftm.request_id = :id
        ");
        $this->db->bind(':id', $id);
        $fundraiser->team_members = $this->db->resultSet();

        // Get donations
        $this->db->query("
            SELECT fd.*, u.name as donor_user_name, u.display_name as donor_display_name
            FROM fundraising_donations fd
            LEFT JOIN users u ON fd.donor_user_id = u.id
            WHERE fd.request_id = :id
            ORDER BY fd.created_at DESC
        ");
        $this->db->bind(':id', $id);
        $fundraiser->donations = $this->db->resultSet();

        // Calculate stats
        $this->db->query("SELECT COUNT(*) as c FROM fundraising_donations WHERE request_id = :id AND status = 'Successful'");
        $this->db->bind(':id', $id);
        $fundraiser->donor_count = (int)($this->db->single()->c ?? 0);

        if ($fundraiser->donor_count > 0 && $fundraiser->raised_amount > 0) {
            $fundraiser->avg_donation = $fundraiser->raised_amount / $fundraiser->donor_count;
        } else {
            $fundraiser->avg_donation = 0;
        }

        return $fundraiser;
    }

    /**
     * Approve a fundraiser request
     */
    public function approveFundraiser($id) {
        $this->db->query("
            UPDATE fundraising_requests 
            SET status = 'Approved', 
                rejection_reason = NULL,
                updated_at = NOW()
            WHERE id = :id
        ");
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    /**
     * Reject a fundraiser request with reason
     */
    public function rejectFundraiser($id, $reason) {
        $this->db->query("
            UPDATE fundraising_requests 
            SET status = 'Rejected', 
                rejection_reason = :reason,
                updated_at = NOW()
            WHERE id = :id
        ");
        $this->db->bind(':id', $id);
        $this->db->bind(':reason', $reason);
        return $this->db->execute();
    }

    /**
     * Hold/Pause a fundraiser campaign
     */
    public function holdFundraiser($id) {
        $this->db->query("
            UPDATE fundraising_requests 
            SET status = 'Cancelled',
                updated_at = NOW()
            WHERE id = :id
        ");
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    /**
     * Mark a fundraiser as completed
     */
    public function completeFundraiser($id) {
        $this->db->query("
            UPDATE fundraising_requests 
            SET status = 'Completed',
                updated_at = NOW()
            WHERE id = :id
        ");
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    /**
     * Remove/Delete a fundraiser
     */
    public function removeFundraiser($id) {
        // First delete related records
        $this->db->query("DELETE FROM fundraising_team_members WHERE request_id = :id");
        $this->db->bind(':id', $id);
        $this->db->execute();

        $this->db->query("DELETE FROM fundraising_bank_details WHERE request_id = :id");
        $this->db->bind(':id', $id);
        $this->db->execute();

        $this->db->query("DELETE FROM fundraising_donations WHERE request_id = :id");
        $this->db->bind(':id', $id);
        $this->db->execute();

        // Then delete main record
        $this->db->query("DELETE FROM fundraising_requests WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    /**
     * Create a new fundraiser as admin
     */
    public function createAdminFundraiser($data) {
        try {
            $this->db->beginTransaction();

            // Insert main request (auto-approved since admin creates it)
            $this->db->query("
                INSERT INTO fundraising_requests 
                (user_id, club_name, requester_position, requester_phone, title, headline, 
                description, project_poster, goal_amount, objective, start_date, end_date, 
                fund_manager_name, fund_manager_contact, status)
                VALUES 
                (:user_id, :club_name, :position, :phone, :title, :headline, 
                :description, :poster, :amount, :objective, :start_date, :end_date, 
                :fund_manager, :fund_manager_contact, 'Approved')
            ");

            $this->db->bind(':user_id', $data['user_id']);
            $this->db->bind(':club_name', $data['club_name']);
            $this->db->bind(':position', $data['position'] ?? 'Admin');
            $this->db->bind(':phone', $data['phone'] ?? '');
            $this->db->bind(':title', $data['title']);
            $this->db->bind(':headline', $data['headline'] ?? $data['title']);
            $this->db->bind(':description', $data['description']);
            $this->db->bind(':poster', $data['project_poster'] ?? null);
            $this->db->bind(':amount', $data['target_amount']);
            $this->db->bind(':objective', $data['objective'] ?? $data['description']);
            $this->db->bind(':start_date', $data['start_date']);
            $this->db->bind(':end_date', $data['end_date']);
            $this->db->bind(':fund_manager', $data['fund_manager'] ?? 'Admin');
            $this->db->bind(':fund_manager_contact', $data['fund_manager_contact'] ?? '');

            $this->db->execute();
            $requestId = $this->db->lastInsertId();

            // Insert bank details
            $this->db->query("
                INSERT INTO fundraising_bank_details 
                (request_id, bank_name, account_number, branch, account_holder)
                VALUES (:request_id, :bank_name, :account_number, :branch, :account_holder)
            ");

            $this->db->bind(':request_id', $requestId);
            $this->db->bind(':bank_name', $data['bank_name']);
            $this->db->bind(':account_number', $data['account_number']);
            $this->db->bind(':branch', $data['branch']);
            $this->db->bind(':account_holder', $data['account_holder']);

            $this->db->execute();
            $this->db->commit();

            return $requestId;
        } catch (Exception $e) {
            $this->db->rollback();
            error_log("Error creating admin fundraiser: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Search fundraisers with filters
     */
    public function searchFundraisers($query = '', $status = '', $club = '') {
        $sql = "
            SELECT 
                fr.id as req_id,
                fr.user_id,
                fr.club_name,
                fr.title,
                fr.headline,
                fr.goal_amount as target_amount,
                fr.collected_amount as raised_amount,
                fr.end_date as deadline,
                fr.status,
                fr.created_at,
                u.name as user_name,
                u.display_name
            FROM fundraising_requests fr
            JOIN users u ON fr.user_id = u.id
            WHERE 1=1
        ";

        if (!empty($query)) {
            $sql .= " AND (fr.title LIKE :query OR fr.club_name LIKE :query OR u.name LIKE :query OR u.display_name LIKE :query)";
        }
        if (!empty($status)) {
            $sql .= " AND fr.status = :status";
        }
        if (!empty($club)) {
            $sql .= " AND fr.club_name = :club";
        }

        $sql .= " ORDER BY fr.created_at DESC";

        $this->db->query($sql);

        if (!empty($query)) {
            $this->db->bind(':query', '%' . $query . '%');
        }
        if (!empty($status)) {
            $this->db->bind(':status', $status);
        }
        if (!empty($club)) {
            $this->db->bind(':club', $club);
        }

        return $this->db->resultSet();
    }

    // ==================== ACTIVITY MONITORING METHODS ====================

    /**
     * Get currently online users (active in last 5 minutes)
     */
    public function getOnlineUsers() {
        try {
            $this->db->query("
                SELECT
                    u.id,
                    COALESCE(NULLIF(u.display_name, ''), u.name) AS display_name,
                    u.profile_image,
                    a.last_activity,
                    COUNT(*) OVER() AS online_count
                FROM user_activity a
                JOIN users u ON u.id = a.user_id
                WHERE a.last_activity > DATE_SUB(NOW(), INTERVAL 5 MINUTE)
                ORDER BY a.last_activity DESC
            ");

            $rows = $this->db->resultSet();
            $onlineCount = !empty($rows) ? (int)($rows[0]->online_count ?? count($rows)) : 0;
            $users = [];

            foreach ($rows as $row) {
                $users[] = (object) [
                    'id' => (int)($row->id ?? 0),
                    'display_name' => (string)($row->display_name ?? ''),
                    'profile_image' => $row->profile_image ?? null,
                    'last_activity' => $row->last_activity ?? null,
                ];
            }

            return [
                'online_count' => $onlineCount,
                'users' => $users,
            ];
        } catch (Exception $e) {
            error_log("Error getting online users: " . $e->getMessage());
            return [
                'online_count' => 0,
                'users' => [],
            ];
        }
    }

    /**
     * Get location distribution summary
     * @param string|null $role - Filter by role
     * @return array - Summary stats
     */
    public function getLocationSummary(?string $role = null): array {
        try {
            $sql = "SELECT 
                        COUNT(DISTINCT ul.country) AS total_countries,
                        COUNT(DISTINCT ul.user_id) AS total_users_with_location
                    FROM user_locations ul
                    INNER JOIN users u ON ul.user_id = u.id
                    WHERE 1=1";
            
            if ($role !== null && $role !== '') {
                $sql .= " AND u.role = :role";
            }
            
            $this->db->query($sql);
            if ($role !== null && $role !== '') {
                $this->db->bind(':role', $role);
            }
            
            $result = $this->db->single();
            
            // Get most common country separately
            $mostCommonSql = "SELECT ul.country
                              FROM user_locations ul
                              INNER JOIN users u ON ul.user_id = u.id
                              WHERE 1=1";
            if ($role !== null && $role !== '') {
                $mostCommonSql .= " AND u.role = :role";
            }
            $mostCommonSql .= " GROUP BY ul.country
                                ORDER BY COUNT(*) DESC
                                LIMIT 1";
            
            $this->db->query($mostCommonSql);
            if ($role !== null && $role !== '') {
                $this->db->bind(':role', $role);
            }
            $mostCommon = $this->db->single();
            
            return [
                'total_countries' => $result->total_countries ?? 0,
                'total_users_with_location' => $result->total_users_with_location ?? 0,
                'most_common_country' => $mostCommon->country ?? 'N/A'
            ];
        } catch (Exception $e) {
            return [
                'total_countries' => 0,
                'total_users_with_location' => 0,
                'most_common_country' => 'N/A'
            ];
        }
    }

    /**
     * Get count of currently online users
     */
    public function getOnlineUsersCount() {
        try {
            $this->db->query("
                SELECT COUNT(*) as count
                FROM user_activity
                WHERE last_activity > DATE_SUB(NOW(), INTERVAL 5 MINUTE)
            ");
            return (int)($this->db->single()->count ?? 0);
        } catch (Exception $e) {
            return 0;
        }
    }

    /**
     * Get recent access logs
     */
    public function getRecentAccessLogs($limit = 50) {
        try {
            $this->db->query("
                SELECT 
                    al.id,
                    al.user_id,
                    al.user_name,
                    al.user_role,
                    al.url,
                    al.method,
                    al.controller,
                    al.action,
                    al.ip_address,
                    al.created_at
                FROM access_logs al
                ORDER BY al.created_at DESC
                LIMIT :limit
            ");
            $this->db->bind(':limit', $limit);
            return $this->db->resultSet();
        } catch (Exception $e) {
            error_log("Error getting access logs: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get access log statistics (page views today, unique visitors, etc.)
     */
    public function getAccessLogStats() {
        $stats = [
            'page_views_today' => 0,
            'unique_visitors_today' => 0,
            'page_views_hour' => 0,
            'most_active_page' => null
        ];

        try {
            // Page views today
            $this->db->query("SELECT COUNT(*) as c FROM access_logs WHERE DATE(created_at) = CURDATE()");
            $stats['page_views_today'] = (int)($this->db->single()->c ?? 0);

            // Unique visitors today (by IP or user_id)
            $this->db->query("
                SELECT COUNT(DISTINCT COALESCE(user_id, ip_address)) as c 
                FROM access_logs 
                WHERE DATE(created_at) = CURDATE()
            ");
            $stats['unique_visitors_today'] = (int)($this->db->single()->c ?? 0);

            // Page views last hour
            $this->db->query("
                SELECT COUNT(*) as c 
                FROM access_logs 
                WHERE created_at > DATE_SUB(NOW(), INTERVAL 1 HOUR)
            ");
            $stats['page_views_hour'] = (int)($this->db->single()->c ?? 0);

            // Most active page today
            $this->db->query("
                SELECT url, COUNT(*) as visits 
                FROM access_logs 
                WHERE DATE(created_at) = CURDATE()
                GROUP BY url 
                ORDER BY visits DESC 
                LIMIT 1
            ");
            $topPage = $this->db->single();
            $stats['most_active_page'] = $topPage ? $topPage->url : null;

        } catch (Exception $e) {
            error_log("Error getting access log stats: " . $e->getMessage());
        }

        return $stats;
    }

    /**
     * Get hourly activity chart data (last 24 hours)
     */
    public function getHourlyActivity() {
        try {
            $this->db->query("
                SELECT 
                    DATE_FORMAT(created_at, '%Y-%m-%d %H:00:00') as hour,
                    COUNT(*) as requests
                FROM access_logs
                WHERE created_at > DATE_SUB(NOW(), INTERVAL 24 HOUR)
                GROUP BY hour
                ORDER BY hour ASC
            ");
            return $this->db->resultSet();
        } catch (Exception $e) {
            error_log("Error getting hourly activity: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get countries list for filter dropdown
     * @return array - List of countries with user counts
     */
    public function getCountriesWithUsers(): array {
        try {
            $this->db->query("SELECT 
                                country,
                                COUNT(DISTINCT user_id) AS user_count
                              FROM user_locations
                              GROUP BY country
                              ORDER BY user_count DESC");
            return $this->db->resultSet();
        } catch (Exception $e) {
            return [];
        }
    }

    // ==================== SUPPORT MANAGEMENT METHODS ====================

    /**
     * Get all support tickets with user info
     */
    public function getSupportTickets() {
        try {
            $this->db->query("
                SELECT 
                    st.*,
                    u.name AS user_name,
                    u.display_name,
                    u.email AS user_email,
                    u.profile_image,
                    u.role AS user_role
                FROM support_tickets st
                JOIN users u ON st.user_id = u.id
                ORDER BY st.created_at DESC
            ");
            return $this->db->resultSet();
        } catch (Exception $e) {
            error_log("Error getting support tickets: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get batches list for filter dropdown
     * @return array - List of batches
     */
    public function getBatches(): array {
        try {
            $this->db->query("SELECT DISTINCT batch_no 
                              FROM users 
                              WHERE batch_no IS NOT NULL 
                              ORDER BY batch_no DESC");
            return $this->db->resultSet();
        } catch (Exception $e) {
            return [];
        }
    }

    /**
     * Get a single support ticket by ID
     */
    public function getSupportTicketById($id) {
        try {
            $this->db->query("
                SELECT 
                    st.*,
                    u.name AS user_name,
                    u.display_name,
                    u.email AS user_email,
                    u.profile_image,
                    u.role AS user_role
                FROM support_tickets st
                JOIN users u ON st.user_id = u.id
                WHERE st.id = :id
            ");
            $this->db->bind(':id', $id);
            return $this->db->single();
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * Update support ticket status
     */
    public function updateSupportTicketStatus($id, $status) {
        try {
            $this->db->query("
                UPDATE support_tickets 
                SET status = :status, updated_at = NOW()
                WHERE id = :id
            ");
            $this->db->bind(':id', $id);
            $this->db->bind(':status', $status);
            return $this->db->execute();
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Admin reply to a support ticket
     */
    public function replySupportTicket($id, $reply) {
        try {
            $this->db->query("
                UPDATE support_tickets 
                SET admin_reply = :reply,
                    admin_replied_at = NOW(),
                    status = 'in_progress',
                    updated_at = NOW()
                WHERE id = :id
            ");
            $this->db->bind(':id', $id);
            $this->db->bind(':reply', $reply);
            return $this->db->execute();
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Get all problem reports with user info
     */
    public function getProblemReports() {
        try {
            $this->db->query("
                SELECT 
                    spr.*,
                    u.name AS user_name,
                    u.display_name,
                    u.email AS user_email,
                    u.profile_image,
                    u.role AS user_role
                FROM support_problem_reports spr
                JOIN users u ON spr.user_id = u.id
                ORDER BY spr.created_at DESC
            ");
            return $this->db->resultSet();
        } catch (Exception $e) {
            error_log("Error getting problem reports: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get a single problem report by ID
     */
    public function getProblemReportById($id) {
        try {
            $this->db->query(" 
                SELECT 
                    spr.*,
                    u.name AS user_name,
                    u.display_name,
                    u.email AS user_email,
                    u.profile_image,
                    u.role AS user_role
                FROM support_problem_reports spr
                JOIN users u ON spr.user_id = u.id
                WHERE spr.id = :id
                LIMIT 1
            ");
            $this->db->bind(':id', $id);
            return $this->db->single();
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * Update problem report status
     */
    public function updateProblemReportStatus($id, $status) {
        try {
            $this->db->query("
                UPDATE support_problem_reports 
                SET status = :status, updated_at = NOW()
                WHERE id = :id
            ");
            $this->db->bind(':id', $id);
            $this->db->bind(':status', $status);
            return $this->db->execute();
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Admin reply to a problem report
     */
    public function replyProblemReport($id, $reply) {
        try {
            $this->db->query("
                UPDATE support_problem_reports 
                SET admin_reply = :reply,
                    admin_replied_at = NOW(),
                    status = 'triaged',
                    updated_at = NOW()
                WHERE id = :id
            ");
            $this->db->bind(':id', $id);
            $this->db->bind(':reply', $reply);
            return $this->db->execute();
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Get all feedback with user info
     */
    public function getSupportFeedback() {
        try {
            $this->db->query("
                SELECT 
                    sf.*,
                    u.name AS user_name,
                    u.display_name,
                    u.email AS user_email,
                    u.profile_image,
                    u.role AS user_role
                FROM support_feedback sf
                JOIN users u ON sf.user_id = u.id
                ORDER BY sf.created_at DESC
            ");
            return $this->db->resultSet();
        } catch (Exception $e) {
            error_log("Error getting feedback: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Delete feedback entry
     */
    public function deleteSupportFeedback($id) {
        try {
            $this->db->query("DELETE FROM support_feedback WHERE id = :id");
            $this->db->bind(':id', $id);
            return $this->db->execute();
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Get support overview stats for KPI cards
     */
    public function getSupportStats() {
        $stats = [
            'open_tickets' => 0,
            'pending_reports' => 0,
            'total_feedback' => 0,
            'resolved_total' => 0
        ];

        try {
            $this->db->query("SELECT COUNT(*) as c FROM support_tickets WHERE status IN ('open','in_progress')");
            $stats['open_tickets'] = (int)($this->db->single()->c ?? 0);

            $this->db->query("SELECT COUNT(*) as c FROM support_problem_reports WHERE status IN ('pending','triaged')");
            $stats['pending_reports'] = (int)($this->db->single()->c ?? 0);

            $this->db->query("SELECT COUNT(*) as c FROM support_feedback");
            $stats['total_feedback'] = (int)($this->db->single()->c ?? 0);

            $this->db->query("
                SELECT 
                    (SELECT COUNT(*) FROM support_tickets WHERE status IN ('resolved','closed')) +
                    (SELECT COUNT(*) FROM support_problem_reports WHERE status IN ('resolved','rejected'))
                AS c
            ");
            $stats['resolved_total'] = (int)($this->db->single()->c ?? 0);
        } catch (Exception $e) {
            error_log("Error getting support stats: " . $e->getMessage());
        }

        return $stats;
    }

    /**
     * Get country-level data (for chart visualization)
     * @param string|null $role - Filter by role
     * @return array - Country data with user counts
     */
    public function getLocationHeatmapData(?string $role = null): array {
        // For simplified version, just return country counts
        return $this->getUserLocations($role, null, null);
    }
}
?>
