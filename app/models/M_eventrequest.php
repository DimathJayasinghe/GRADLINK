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
        $sql = 'INSERT INTO event_requests (user_id,title,description,club_name,position,attachment_image,event_date,event_time,event_venue,status,short_tagline,event_type,post_caption,add_to_calendar,president_name,approval_date,event_id) VALUES (:user_id,:title,:description,:club_name,:position,:attachment_image,:event_date,:event_time,:event_venue,:status,:short_tagline,:event_type,:post_caption,:add_to_calendar,:president_name,:approval_date,:event_id)';
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
        // additional optional fields
        $this->db->bind(':short_tagline', $data['short_tagline'] ?? null);
        $this->db->bind(':event_type', $data['event_type'] ?? null);
        $this->db->bind(':post_caption', $data['post_caption'] ?? null);
        $this->db->bind(':add_to_calendar', isset($data['add_to_calendar']) ? (int)$data['add_to_calendar'] : 0);
        $this->db->bind(':president_name', $data['president_name'] ?? null);
        $this->db->bind(':approval_date', $data['approval_date'] ?? null);
        $this->db->bind(':event_id', $data['event_id'] ?? null);
        try{
            $this->db->execute();
            return (int)$this->db->lastInsertId();
        }catch(Exception $e){
            return false;
        }
    }

    public function update(int $id, array $data){
        $sql = 'UPDATE event_requests SET title=:title,description=:description,club_name=:club_name,position=:position,attachment_image=:attachment_image,event_date=:event_date,event_time=:event_time,event_venue=:event_venue,status=:status,short_tagline=:short_tagline,event_type=:event_type,post_caption=:post_caption,add_to_calendar=:add_to_calendar,president_name=:president_name,approval_date=:approval_date,event_id=:event_id WHERE id = :id';
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
        // additional optional fields
        $this->db->bind(':short_tagline', $data['short_tagline'] ?? null);
        $this->db->bind(':event_type', $data['event_type'] ?? null);
        $this->db->bind(':post_caption', $data['post_caption'] ?? null);
        $this->db->bind(':add_to_calendar', isset($data['add_to_calendar']) ? (int)$data['add_to_calendar'] : 0);
        $this->db->bind(':president_name', $data['president_name'] ?? null);
        $this->db->bind(':approval_date', $data['approval_date'] ?? null);
        $this->db->bind(':event_id', $data['event_id'] ?? null);
        $this->db->bind(':id', $id);
        try{
            $this->db->execute();
            return $this->db->rowCount();
        }catch(Exception $e){
            return false;
        }
    }

    // Set or clear the linked published event id for a request
    public function setEventId(int $requestId, $eventId){
        $this->db->query('UPDATE event_requests SET event_id = :eid WHERE id = :id');
        if($eventId === null){
            $this->db->bind(':eid', null);
        } else {
            $this->db->bind(':eid', (int)$eventId);
        }
        $this->db->bind(':id', $requestId);
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