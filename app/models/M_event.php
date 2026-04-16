<?php
class M_event {
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

    public function __construct(){
        $this->db = new Database();
        $this->ensureSuspendedUsersTable();
    }

    /** Create event
     * $data: associative array of event fields
     * returns inserted id or false
     */
    public function create(array $data){
        $sql = "INSERT INTO events (slug,title,description,start_datetime,end_datetime,all_day,timezone,venue,capacity,organizer_id,status,visibility,series_id)
                VALUES (:slug,:title,:description,:start_datetime,:end_datetime,:all_day,:timezone,:venue,:capacity,:organizer_id,:status,:visibility,:series_id)";
        $this->db->query($sql);
        $this->db->bind(':slug', $data['slug'] ?? null);
        $this->db->bind(':title', $data['title']);
        $this->db->bind(':description', $data['description'] ?? null);
        $this->db->bind(':start_datetime', $data['start_datetime']);
        $this->db->bind(':end_datetime', $data['end_datetime'] ?? null);
        $this->db->bind(':all_day', isset($data['all_day']) ? (int)$data['all_day'] : 0);
        $this->db->bind(':timezone', $data['timezone'] ?? 'UTC');
        $this->db->bind(':venue', $data['venue'] ?? null);
        $this->db->bind(':capacity', $data['capacity'] ?? null);
        $this->db->bind(':organizer_id', $data['organizer_id']);
        $this->db->bind(':status', $data['status'] ?? 'published');
        $this->db->bind(':visibility', $data['visibility'] ?? 'public');
        $this->db->bind(':series_id', $data['series_id'] ?? null);

        try{
            $this->db->execute();
            $lastId = $this->db->lastInsertId();
            return $lastId ? (int)$lastId : ($this->db->rowCount() ? $this->db->rowCount() : false);
        } catch (Exception $e){
            return false;
        }
    }

    public function update(int $id, array $data){
        $sql = "UPDATE events SET slug=:slug,title=:title,description=:description,start_datetime=:start_datetime,end_datetime=:end_datetime,all_day=:all_day,timezone=:timezone,venue=:venue,capacity=:capacity,status=:status,visibility=:visibility,series_id=:series_id WHERE id=:id";
        $this->db->query($sql);
        $this->db->bind(':slug', $data['slug'] ?? null);
        $this->db->bind(':title', $data['title']);
        $this->db->bind(':description', $data['description'] ?? null);
        $this->db->bind(':start_datetime', $data['start_datetime']);
        $this->db->bind(':end_datetime', $data['end_datetime'] ?? null);
        $this->db->bind(':all_day', isset($data['all_day']) ? (int)$data['all_day'] : 0);
        $this->db->bind(':timezone', $data['timezone'] ?? 'UTC');
        $this->db->bind(':venue', $data['venue'] ?? null);
        $this->db->bind(':capacity', $data['capacity'] ?? null);
        $this->db->bind(':status', $data['status'] ?? 'published');
        $this->db->bind(':visibility', $data['visibility'] ?? 'public');
        $this->db->bind(':series_id', $data['series_id'] ?? null);
        $this->db->bind(':id', $id);
        try{
            $this->db->execute();
            return $this->db->rowCount();
        } catch (Exception $e){
            return false;
        }
    }

    public function delete(int $id){
        $this->db->query('DELETE FROM events WHERE id = :id');
        $this->db->bind(':id', $id);
        try{
            $this->db->execute();
            return $this->db->rowCount();
        }catch(Exception $e){
            return false;
        }
    }

    public function findById(int $id){
        // include primary image (if any) as attachment_image
                $this->db->query("SELECT e.*, u.name AS organizer_name, u.email AS organizer_email, ei.file_path AS attachment_image
                                                    FROM events e
                                                    LEFT JOIN users u ON u.id = e.organizer_id
                                                    LEFT JOIN event_images ei ON ei.event_id = e.id AND ei.is_primary = 1
                                                    LEFT JOIN suspended_users su ON su.user_id = e.organizer_id AND su.status = 'active'
                                                    WHERE e.id = :id
                                                        AND su.id IS NULL
                                                        AND (e.status IS NULL OR LOWER(e.status) <> 'cancelled')
                                                    LIMIT 1");
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    /** Find events within a date range or by filters
     * $filters: ['start'=>..., 'end'=>..., 'limit'=>.., 'page'=>.., 'visibility'=>'public']
     */
    public function findList(array $filters = []){
        $where = [];
        $params = [];
        $where[] = 'su.id IS NULL';
        $where[] = "(e.status IS NULL OR LOWER(e.status) <> 'cancelled')";
        if(!empty($filters['start'])){ $where[] = 'e.start_datetime >= :start'; $params[':start'] = $filters['start']; }
        if(!empty($filters['end'])){ $where[] = 'e.start_datetime <= :end'; $params[':end'] = $filters['end']; }
        if(!empty($filters['visibility'])){ $where[] = 'e.visibility = :visibility'; $params[':visibility'] = $filters['visibility']; }
    // include primary image (attachment_image) via left join to event_images
        $sql = "SELECT e.*, u.name AS organizer_name, ei.file_path AS attachment_image
                FROM events e
                LEFT JOIN users u ON u.id = e.organizer_id
                LEFT JOIN event_images ei ON ei.event_id = e.id AND ei.is_primary = 1
                LEFT JOIN suspended_users su ON su.user_id = e.organizer_id AND su.status = 'active'";
        if($where) $sql .= ' WHERE ' . implode(' AND ', $where);
        $sql .= ' ORDER BY e.start_datetime ASC';
        if(!empty($filters['limit'])){ $sql .= ' LIMIT ' . (int)$filters['limit']; }
        if(!empty($filters['page']) && empty($filters['limit'])===false){ $offset = ((int)$filters['page'] -1) * (int)$filters['limit']; $sql .= ' OFFSET ' . (int)$offset; }
        $this->db->query($sql);
        foreach($params as $k=>$v) $this->db->bind($k,$v);
        return $this->db->resultSet();
    }

    public function search(string $q, int $limit = 20, int $offset = 0){
        $sql = "SELECT e.*, u.name AS organizer_name, ei.file_path AS attachment_image
                        FROM events e
                        LEFT JOIN users u ON u.id = e.organizer_id
                        LEFT JOIN event_images ei ON ei.event_id = e.id AND ei.is_primary = 1
                        LEFT JOIN suspended_users su ON su.user_id = e.organizer_id AND su.status = 'active'
                        WHERE MATCH(e.title,e.description) AGAINST(:q IN NATURAL LANGUAGE MODE)
                            AND su.id IS NULL
                            AND (e.status IS NULL OR LOWER(e.status) <> 'cancelled')
                        LIMIT :limit OFFSET :offset";
        $this->db->query($sql);
        $this->db->bind(':q',$q);
        $this->db->bind(':limit',$limit);
        $this->db->bind(':offset',$offset);
        return $this->db->resultSet();
    }

    public function getOngoingForAdmin(string $search = ''): array {
        $sql = "SELECT e.*, u.name AS organizer_name, ei.file_path AS attachment_image
                FROM events e
                LEFT JOIN users u ON u.id = e.organizer_id
                LEFT JOIN event_images ei ON ei.event_id = e.id AND ei.is_primary = 1
                WHERE e.status = 'published'
                    AND e.visibility = 'public'";

        if ($search !== '') {
            $sql .= " AND (
                LOWER(COALESCE(e.title, '')) LIKE :search
                OR LOWER(COALESCE(e.description, '')) LIKE :search
                OR LOWER(COALESCE(e.venue, '')) LIKE :search
                OR LOWER(COALESCE(u.name, '')) LIKE :search
            )";
        }

        $sql .= " ORDER BY e.start_datetime DESC";

        $this->db->query($sql);
        if ($search !== '') {
            $this->db->bind(':search', '%' . strtolower($search) . '%');
        }

        return $this->db->resultSet();
    }
}

?>