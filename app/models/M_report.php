<?php
class M_report{
    private $db;

    public function __construct()
    {
        $this->db = new Database;
    }

    public function getAllReports(){
        $this->db->query("SELECT * FROM reports ORDER BY created_at DESC");
        return $this->db->resultSet();
    }

    public function getReportsByUser($userId){
        $this->db->query("SELECT * FROM reports WHERE reporter_id = :userId ORDER BY created_at DESC");
        $this->db->bind(':userId', $userId);
        return $this->db->resultSet();
    }

    public function submitReport($reporterId, $reportType, $reportedItemId, $category, $details, $link = null){
        $this->db->query("INSERT INTO reports (reporter_id, report_type, reported_item_id, category, details, link) VALUES (:reporterId, :reportType, :reportedItemId, :category, :details, :link)");
        $this->db->bind(':reporterId', $reporterId);
        $this->db->bind(':reportType', $reportType);
        $this->db->bind(':reportedItemId', $reportedItemId);
        $this->db->bind(':category', $category);
        $this->db->bind(':details', $details);
        $this->db->bind(':link', $link);
        return $this->db->execute();
    }
}
?>