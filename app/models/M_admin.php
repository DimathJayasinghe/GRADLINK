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


