<?php
class M_event_image {
    private $db;
    public function __construct(){
        $this->db = new Database();
    }

    /** Add an image record for an event */
    public function addForEvent(int $eventId, string $filePath, int $isPrimary = 1){
        $this->db->query('INSERT INTO event_images (event_id, file_path, is_primary) VALUES (:event_id, :file_path, :is_primary)');
        $this->db->bind(':event_id', $eventId);
        $this->db->bind(':file_path', $filePath);
        $this->db->bind(':is_primary', $isPrimary);
        try{
            $this->db->execute();
            return (int)$this->db->lastInsertId();
        }catch(Exception $e){
            return false;
        }
    }

    public function getPrimaryForEvent(int $eventId){
        $this->db->query('SELECT file_path, caption FROM event_images WHERE event_id = :eid AND is_primary = 1 LIMIT 1');
        $this->db->bind(':eid', $eventId);
        return $this->db->single();
    }

    public function getForEvents(array $eventIds){
        if(empty($eventIds)) return [];
        $placeholders = implode(',', array_fill(0, count($eventIds), '?'));
        $sql = "SELECT event_id, file_path, caption, is_primary FROM event_images WHERE event_id IN ($placeholders)";
        $this->db->query($sql);

        // bind by numeric index (Database wrapper supports bind with named parameters only; fallback to execute raw)
        try {
            $stmt = $this->db->dbh->prepare($sql);
            foreach ($eventIds as $i => $id) {
                $stmt->bindValue($i+1, $id, PDO::PARAM_INT);
            }
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_OBJ);
        } catch (Exception $e) {
            return [];
        }
    }

    /** Return full public URL for an event media filename */
    public static function getUrl(?string $file){
        if(!$file) return '';
        $safe = rawurlencode(basename($file));
        return rtrim(URLROOT, '/') . '/Media/event/' . $safe;
    }

}

?>

