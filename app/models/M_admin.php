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

        return [
            'roles' => $roles,
            'batches' => $batches,
            'skills' => $skillCounts,
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
        $posts = 0;
        try {
            $this->db->query('SELECT COUNT(*) AS c FROM Posts');
            $posts = $this->db->single()->c ?? 0;
        } catch (Exception $e) {
            // Posts table doesn't exist, set to 0
            $posts = 0;
        }

        // Placeholders for comments/reactions
        $comments = 0;
        $reactions = 0;

        // Activity over time (active users per month based on created_at for now)
        $overTime = [];
        try {
            $this->db->query("SELECT DATE_FORMAT(created_at, '%Y-%m') AS ym, COUNT(*) AS c FROM users GROUP BY ym ORDER BY ym ASC");
            $overTime = $this->db->resultSet();
        } catch (Exception $e) {
            // created_at column might not exist
            $overTime = [];
        }

        return [
            'posts' => (int)$posts,
            'comments' => (int)$comments,
            'reactions' => (int)$reactions,
            'active_over_time' => $overTime,
        ];
    }

    public function getPostReports(): array {
        try {
            $this->db->query('
                SELECT
                    r.id,
                    r.post_id,
                    r.post_owner_id,
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
                LEFT JOIN users owner ON owner.id = r.post_owner_id
                ORDER BY r.created_at DESC
            ');
            return $this->db->resultSet();
        } catch (Throwable $e) {
            return [];
        }
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

            if (strtolower((string)($user->role ?? '')) === 'admin') {
                return ['ok' => false, 'message' => 'Admin accounts cannot be suspended'];
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
                    ou.user_id,
                    ou.session_id,
                    ou.ip_address,
                    ou.current_url,
                    ou.last_activity,
                    u.name,
                    u.display_name,
                    u.email,
                    u.profile_image,
                    u.role
                FROM online_users ou
                JOIN users u ON ou.user_id = u.id
                WHERE ou.last_activity > DATE_SUB(NOW(), INTERVAL 5 MINUTE)
                ORDER BY ou.last_activity DESC
            ");
            return $this->db->resultSet();
        } catch (Exception $e) {
            error_log("Error getting online users: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get count of currently online users
     */
    public function getOnlineUsersCount() {
        try {
            $this->db->query("
                SELECT COUNT(*) as count 
                FROM online_users 
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
}
?>
