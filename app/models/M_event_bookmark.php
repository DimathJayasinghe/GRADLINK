<?php
class M_event_bookmark {
    private $db;
    public function __construct(){
        $this->db = new Database();
    }

    public function isBookmarked(int $userId, int $eventId): bool{
        $this->db->query('SELECT 1 FROM event_bookmarks WHERE user_id = :uid AND event_id = :eid');
        $this->db->bind(':uid',$userId);
        $this->db->bind(':eid',$eventId);
        $r = $this->db->single();
        return (bool)$r;
    }

    public function add(int $userId, int $eventId): bool{
        $this->db->query('INSERT INTO event_bookmarks (user_id,event_id,created_at) VALUES (:uid,:eid,NOW())');
        $this->db->bind(':uid',$userId);
        $this->db->bind(':eid',$eventId);
        $this->db->execute();
        return $this->db->rowCount() > 0;
    }

    public function remove(int $userId, int $eventId): bool{
        $this->db->query('DELETE FROM event_bookmarks WHERE user_id = :uid AND event_id = :eid');
        $this->db->bind(':uid',$userId);
        $this->db->bind(':eid',$eventId);
        $this->db->execute();
        return $this->db->rowCount() > 0;
    }

    public function toggle(int $userId, int $eventId): bool{
        if($this->isBookmarked($userId,$eventId)){
            $this->remove($userId,$eventId);
            return false;
        }
        $this->add($userId,$eventId);
        return true;
    }

    public function getBookmarksForUser(int $userId){
        // Return richer event info so views can render bookmarks without extra queries
        $this->db->query(
            'SELECT eb.event_id, e.title, e.description, e.start_datetime, e.venue AS event_venue, u.name AS club_name, ei.file_path AS attachment_image, eb.created_at
             FROM event_bookmarks eb
             JOIN events e ON e.id = eb.event_id
             LEFT JOIN users u ON u.id = e.organizer_id
             LEFT JOIN event_images ei ON ei.event_id = e.id AND ei.is_primary = 1
             WHERE eb.user_id = :uid
             ORDER BY eb.created_at DESC'
        );
        $this->db->bind(':uid',$userId);
        return $this->db->resultSet();
    }
}

?>