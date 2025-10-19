<?php
class M_event {
    private $db;

    public function __construct(){
        $this->db = new Database();
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
            return $this->db->rowCount() ? $this->db->stmt->rowCount() : $this->db->stmt->rowCount();
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
        $this->db->query('SELECT e.*, u.name AS organizer_name, u.email AS organizer_email FROM events e LEFT JOIN users u ON u.id = e.organizer_id WHERE e.id = :id LIMIT 1');
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    /** Find events within a date range or by filters
     * $filters: ['start'=>..., 'end'=>..., 'limit'=>.., 'page'=>.., 'visibility'=>'public']
     */
    public function findList(array $filters = []){
        $where = [];
        $params = [];
        if(!empty($filters['start'])){ $where[] = 'e.start_datetime >= :start'; $params[':start'] = $filters['start']; }
        if(!empty($filters['end'])){ $where[] = 'e.start_datetime <= :end'; $params[':end'] = $filters['end']; }
        if(!empty($filters['visibility'])){ $where[] = 'e.visibility = :visibility'; $params[':visibility'] = $filters['visibility']; }
        $sql = 'SELECT e.*, u.name AS organizer_name FROM events e LEFT JOIN users u ON u.id = e.organizer_id';
        if($where) $sql .= ' WHERE ' . implode(' AND ', $where);
        $sql .= ' ORDER BY e.start_datetime ASC';
        if(!empty($filters['limit'])){ $sql .= ' LIMIT ' . (int)$filters['limit']; }
        if(!empty($filters['page']) && empty($filters['limit'])===false){ $offset = ((int)$filters['page'] -1) * (int)$filters['limit']; $sql .= ' OFFSET ' . (int)$offset; }
        $this->db->query($sql);
        foreach($params as $k=>$v) $this->db->bind($k,$v);
        return $this->db->resultSet();
    }

    public function search(string $q, int $limit = 20, int $offset = 0){
        $sql = "SELECT e.*, u.name AS organizer_name FROM events e LEFT JOIN users u ON u.id = e.organizer_id WHERE MATCH(e.title,e.description) AGAINST(:q IN NATURAL LANGUAGE MODE) LIMIT :limit OFFSET :offset";
        $this->db->query($sql);
        $this->db->bind(':q',$q);
        $this->db->bind(':limit',$limit);
        $this->db->bind(':offset',$offset);
        return $this->db->resultSet();
    }
}

?>