<?php
class M_eventrequest{
    private $db = null;
    public function __construct(){
        $this->db = new Database();
    }

    public function getById(int $id){
        // Include a req_id alias for compatibility with views expecting req_id
        $this->db->query('SELECT er.*, er.id AS req_id, u.name AS user_name FROM event_requests er LEFT JOIN users u ON u.id = er.user_id WHERE er.id = :id LIMIT 1');
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    public function getAllForUser(int $userId){
        // Alias id as req_id for backward compatibility with views
        $this->db->query('SELECT er.*, er.id AS req_id FROM event_requests er WHERE er.user_id = :uid ORDER BY er.created_at DESC');
        $this->db->bind(':uid', $userId);
        return $this->db->resultSet();
    }

    public function getAll(array $filters = []){
        // Select req_id alias so callers can reference req_id consistently
        $sql = 'SELECT er.*, er.id AS req_id, u.name AS user_name FROM event_requests er LEFT JOIN users u ON u.id = er.user_id';
        $where = [];
        $params = [];
        if(!empty($filters['status'])){ $where[] = 'er.status = :status'; $params[':status'] = $filters['status']; }
        if(!empty($filters['user_id'])){ $where[] = 'er.user_id = :user_id'; $params[':user_id'] = $filters['user_id']; }
        if($where) $sql .= ' WHERE ' . implode(' AND ', $where);
        $sql .= ' ORDER BY er.created_at DESC';
        $this->db->query($sql);
        foreach($params as $k=>$v) $this->db->bind($k,$v);
        return $this->db->resultSet();
    }

    public function create(array $data){
        $sql = 'INSERT INTO event_requests (user_id,title,description,club_name,position,attachment_image,event_date,event_time,event_venue,status) VALUES (:user_id,:title,:description,:club_name,:position,:attachment_image,:event_date,:event_time,:event_venue,:status)';
        $this->db->query($sql);
        $this->db->bind(':user_id', $data['user_id']);
        $this->db->bind(':title', $data['title']);
        $this->db->bind(':description', $data['description'] ?? null);
        $this->db->bind(':club_name', $data['club_name'] ?? null);
        $this->db->bind(':position', $data['position'] ?? null);
        $this->db->bind(':attachment_image', $data['attachment_image'] ?? null);
        $this->db->bind(':event_date', $data['event_date'] ?? null);
        $this->db->bind(':event_time', $data['event_time'] ?? null);
        $this->db->bind(':event_venue', $data['event_venue'] ?? null);
        $this->db->bind(':status', $data['status'] ?? 'Pending');
        try{
            $this->db->execute();
            return (int)$this->db->lastInsertId();
        }catch(Exception $e){
            return false;
        }
    }

    public function update(int $id, array $data){
        $sql = 'UPDATE event_requests SET title=:title,description=:description,club_name=:club_name,position=:position,attachment_image=:attachment_image,event_date=:event_date,event_time=:event_time,event_venue=:event_venue,status=:status WHERE id = :id';
        $this->db->query($sql);
        $this->db->bind(':title', $data['title']);
        $this->db->bind(':description', $data['description'] ?? null);
        $this->db->bind(':club_name', $data['club_name'] ?? null);
        $this->db->bind(':position', $data['position'] ?? null);
        $this->db->bind(':attachment_image', $data['attachment_image'] ?? null);
        $this->db->bind(':event_date', $data['event_date'] ?? null);
        $this->db->bind(':event_time', $data['event_time'] ?? null);
        $this->db->bind(':event_venue', $data['event_venue'] ?? null);
        $this->db->bind(':status', $data['status'] ?? 'Pending');
        $this->db->bind(':id', $id);
        try{
            $this->db->execute();
            return $this->db->rowCount();
        }catch(Exception $e){
            return false;
        }
    }

    public function delete(int $id){
        $this->db->query('DELETE FROM event_requests WHERE id = :id');
        $this->db->bind(':id', $id);
        try{
            $this->db->execute();
            return $this->db->rowCount();
        }catch(Exception $e){
            return false;
        }
    }

    public function incrementView(int $id){
        $this->db->query('UPDATE event_requests SET views = views + 1 WHERE id = :id');
        $this->db->bind(':id', $id);
        $this->db->execute();
    }

    public function getAnalytics(int $id){
        $this->db->query('SELECT views, unique_viewers, interested_count, going_count FROM event_requests WHERE id = :id');
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    public function setStatus(int $id, string $status){
        $this->db->query('UPDATE event_requests SET status = :status WHERE id = :id');
        $this->db->bind(':status', $status);
        $this->db->bind(':id', $id);
        try{
            $this->db->execute();
            return $this->db->rowCount();
        }catch(Exception $e){
            return false;
        }
    }
}
?>