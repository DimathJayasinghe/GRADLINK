<?php
class M_attendee {
    private $db;
    public function __construct(){
        $this->db = new Database();
    }

    public function rsvp(int $event_id, int $user_id, string $status = 'attending', int $guests = 0){
        // upsert simple implementation
        $this->db->query('SELECT id FROM event_attendees WHERE event_id = :event_id AND user_id = :user_id');
        $this->db->bind(':event_id',$event_id);
        $this->db->bind(':user_id',$user_id);
        $existing = $this->db->single();
        if($existing){
            $this->db->query('UPDATE event_attendees SET status=:status, guests=:guests, updated_at = CURRENT_TIMESTAMP WHERE id = :id');
            $this->db->bind(':status',$status);
            $this->db->bind(':guests',$guests);
            $this->db->bind(':id',$existing->id);
            $this->db->execute();
            return $existing->id;
        } else {
            $this->db->query('INSERT INTO event_attendees (event_id,user_id,status,guests) VALUES (:event_id,:user_id,:status,:guests)');
            $this->db->bind(':event_id',$event_id);
            $this->db->bind(':user_id',$user_id);
            $this->db->bind(':status',$status);
            $this->db->bind(':guests',$guests);
            $this->db->execute();
            return $this->db->rowCount() ? $this->db->rowCount() : null;
        }
    }

    public function getAttendees(int $event_id){
        $this->db->query('SELECT ea.*, u.name, u.email FROM event_attendees ea JOIN users u ON u.id = ea.user_id WHERE ea.event_id = :event_id');
        $this->db->bind(':event_id',$event_id);
        return $this->db->resultSet();
    }

    public function cancel(int $event_id, int $user_id){
        $this->db->query('DELETE FROM event_attendees WHERE event_id = :event_id AND user_id = :user_id');
        $this->db->bind(':event_id',$event_id);
        $this->db->bind(':user_id',$user_id);
        $this->db->execute();
        return $this->db->rowCount();
    }
}

?>