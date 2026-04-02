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

        // Use a direct PDO statement for dynamic IN-list binding
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

    /**
     * Return a public URL for an event image.
     * Preference order:
     *  - if $file is already a full URL, return as-is
     *  - if a file exists under APPROOT/storage/posts/<basename>, return URLROOT/storage/posts/<basename>
     *  - otherwise fall back to URLROOT/Media/event/<basename>
     */
    public static function getUrl(?string $file){
        if(!$file) return '';
        // If absolute URL, return as-is
        if(strpos($file, 'http://') === 0 || strpos($file, 'https://') === 0) return $file;

        $name = basename($file);
        $encoded = rawurlencode($name);

        // Check local storage posts
        $localPath = APPROOT . '/storage/posts/' . $name;
        if(file_exists($localPath)){
            return rtrim(URLROOT, '/') . '/storage/posts/' . $encoded;
        }

        // Fallback path used elsewhere in the app
        return rtrim(URLROOT, '/') . '/Media/event/' . $encoded;
    }
}

?>

