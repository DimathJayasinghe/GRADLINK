<?php
class M_admin {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    public function getOverviewMetrics(): array {
        // Total users
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
        $this->db->query('SELECT batch_no AS batch, COUNT(*) AS count FROM users WHERE batch_no IS NOT NULL GROUP BY batch_no ORDER BY batch_no');
        $batches = $this->db->resultSet();

        // Skills distribution (skills stored as JSON/TEXT of ids)
        $this->db->query('SELECT skills FROM users WHERE skills IS NOT NULL');
        $rows = $this->db->resultSet();
        $skillCounts = [];
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

        return [
            'roles' => $roles,
            'batches' => $batches,
            'skills' => $skillCounts,
        ];
    }

    public function getAllUsers() {
        $this->db->query('SELECT id, name, email, role, batch_no, graduation_year, profile_image FROM users ORDER BY created_at DESC');
        return $this->db->resultSet();
    }

    public function getEngagementMetrics(): array {
        // Posts, comments, reactions — comments/reactions tables may not exist yet; compute what exists
        $this->db->query('SELECT COUNT(*) AS c FROM Posts');
        $posts = $this->db->single()->c ?? 0;

        // Placeholders for comments/reactions
        $comments = 0;
        $reactions = 0;

        // Activity over time (active users per month based on created_at for now)
        $this->db->query("SELECT DATE_FORMAT(created_at, '%Y-%m') AS ym, COUNT(*) AS c FROM users GROUP BY ym ORDER BY ym ASC");
        $overTime = $this->db->resultSet();

        return [
            'posts' => (int)$posts,
            'comments' => (int)$comments,
            'reactions' => (int)$reactions,
            'active_over_time' => $overTime,
        ];
    }
}
?>


