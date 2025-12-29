<?php
class M_fundraiser{
    private $db = null;
    public function __construct(){
        $this->db = new Database();
    }
    
    public function searchUsers($query){
        $this->db->query("SELECT id, display_name, name, email FROM users WHERE name LIKE :query OR display_name LIKE :query");
        $this->db->bind(':query', '%' . $query . '%');
        return $this->db->resultSet();
    }

    /**
     * Create a new fundraiser request with bank details and team members
     */
    public function createNewFundraiserRequest($data){
        try {
            // Start transaction
            $this->db->beginTransaction();
            
            // Insert main fundraising request
            $this->db->query("
                INSERT INTO fundraising_requests 
                (user_id, club_name, requester_position, requester_phone, title, headline, 
                description, project_poster, goal_amount, objective, start_date, end_date, 
                fund_manager_name, fund_manager_contact, advisor_id) 
                VALUES 
                (:user_id, :club_name, :position, :phone, :title, :headline, 
                :description, :poster, :amount, :objective, :start_date, :end_date, 
                :fund_manager, :fund_manager_contact, :advisor_id)
            ");
            
            // Bind parameters
            $this->db->bind(':user_id', $_SESSION['user_id']);
            $this->db->bind(':club_name', $data['club_name']);
            $this->db->bind(':position', $data['position']);
            $this->db->bind(':phone', $data['phone']);
            $this->db->bind(':title', $data['project_title']);
            $this->db->bind(':headline', $data['headline']);
            $this->db->bind(':description', $data['description']);
            $this->db->bind(':poster', $data['project_poster'] ?? null);
            $this->db->bind(':amount', $data['amount_needed']);
            $this->db->bind(':objective', $data['objective']);
            $this->db->bind(':start_date', $data['start_date']);
            $this->db->bind(':end_date', $data['end_date']);
            $this->db->bind(':fund_manager', $data['fund_manager']);
            $this->db->bind(':fund_manager_contact', $data['fund_manager_contact']);
            $this->db->bind(':advisor_id', !empty($data['advisor_id']) ? $data['advisor_id'] : null);
            
            $this->db->execute();
            $request_id = $this->db->lastInsertId();
            
            // Insert bank details
            $this->db->query("
                INSERT INTO fundraising_bank_details 
                (request_id, bank_name, account_number, branch, account_holder)
                VALUES
                (:request_id, :bank_name, :account_number, :branch, :account_holder)
            ");
            
            $this->db->bind(':request_id', $request_id);
            $this->db->bind(':bank_name', $data['bank_name']);
            $this->db->bind(':account_number', $data['account_number']);
            $this->db->bind(':branch', $data['branch']);
            $this->db->bind(':account_holder', $data['account_holder']);
            
            $this->db->execute();
            
            // Insert team members if provided
            if (!empty($data['team_members']) && is_array($data['team_members'])) {
                foreach ($data['team_members'] as $member_id) {
                    $this->db->query("
                        INSERT INTO fundraising_team_members (request_id, user_id)
                        VALUES (:request_id, :user_id)
                    ");
                    $this->db->bind(':request_id', $request_id);
                    $this->db->bind(':user_id', $member_id);
                    $this->db->execute();
                }
            }
            
            // Commit transaction
            $this->db->commit();
            
            return $request_id;
        } catch (Exception $e) {
            // Rollback on error
            $this->db->rollback();
            error_log("Error creating fundraiser request: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get all approved fundraising requests
     */
    public function getAllFundraisers(){
        $this->db->query("
            SELECT 
                fr.id as req_id,
                fr.user_id,
                fr.club_name,
                fr.title,
                fr.headline,
                fr.description,
                fr.project_poster,
                fr.goal_amount as target_amount,
                fr.collected_amount as raised_amount,
                fr.objective,
                fr.start_date,
                fr.end_date as deadline,
                fr.fund_manager_name,
                fr.fund_manager_contact,
                fr.advisor_id,
                fr.status,
                fr.created_at,
                fr.updated_at,
                u.name as user_name,
                u.display_name,
                u.profile_image
            FROM fundraising_requests fr
            JOIN users u ON fr.user_id = u.id
            WHERE fr.status IN ('Approved', 'Active')
            ORDER BY fr.created_at DESC
        ");
        
        return $this->db->resultSet();
    }
    
    /**
     * Get fundraising requests for a specific user
     */
    public function getMyFundraisers($user_id){
        $this->db->query("
            SELECT 
                fr.id as req_id,
                fr.user_id,
                fr.club_name,
                fr.title,
                fr.headline,
                fr.description,
                fr.project_poster,
                fr.goal_amount as target_amount,
                fr.collected_amount as raised_amount,
                fr.objective,
                fr.start_date,
                fr.end_date as deadline,
                fr.fund_manager_name,
                fr.fund_manager_contact,
                fr.advisor_id,
                fr.status,
                fr.rejection_reason,
                fr.created_at,
                fr.updated_at
            FROM fundraising_requests fr
            WHERE fr.user_id = :user_id
            ORDER BY fr.created_at DESC
        ");
        
        $this->db->bind(':user_id', $user_id);
        return $this->db->resultSet();
    }
    
    /**
     * Get a single fundraiser by ID with all related data
     */
    public function getFundraiserById($id){
        $this->db->query("
            SELECT 
                fr.id as req_id,
                fr.user_id,
                fr.club_name,
                fr.requester_position,
                fr.requester_phone,
                fr.title,
                fr.headline,
                fr.description,
                fr.project_poster,
                fr.goal_amount as target_amount,
                fr.collected_amount as raised_amount,
                fr.objective,
                fr.start_date,
                fr.end_date as deadline,
                fr.fund_manager_name,
                fr.fund_manager_contact,
                fr.advisor_id,
                fr.status,
                fr.rejection_reason,
                fr.created_at,
                fr.updated_at,
                u.name as user_name,
                u.display_name,
                u.profile_image,
                u.email,
                advisor.name as advisor_name
            FROM fundraising_requests fr
            JOIN users u ON fr.user_id = u.id
            LEFT JOIN users advisor ON fr.advisor_id = advisor.id
            WHERE fr.id = :id
        ");
        
        $this->db->bind(':id', $id);
        return $this->db->single();
    }
    
    /**
     * Get bank details for a fundraiser
     */
    public function getBankDetails($request_id){
        $this->db->query("
            SELECT * FROM fundraising_bank_details
            WHERE request_id = :request_id
        ");
        
        $this->db->bind(':request_id', $request_id);
        return $this->db->single();
    }
    
    /**
     * Get team members for a fundraiser
     */
    public function getTeamMembers($request_id){
        $this->db->query("
            SELECT 
                u.id,
                u.name,
                u.display_name,
                u.profile_image,
                u.email
            FROM fundraising_team_members ftm
            JOIN users u ON ftm.user_id = u.id
            WHERE ftm.request_id = :request_id
        ");
        
        $this->db->bind(':request_id', $request_id);
        return $this->db->resultSet();
    }
    
    /**
     * Get donations for a fundraiser
     */
    public function getDonations($request_id){
        $this->db->query("
            SELECT 
                fd.id,
                fd.amount,
                fd.transaction_reference,
                fd.donor_name,
                fd.message,
                fd.is_anonymous,
                fd.status,
                fd.created_at,
                u.name as user_name,
                u.display_name,
                u.profile_image
            FROM fundraising_donations fd
            LEFT JOIN users u ON fd.donor_user_id = u.id
            WHERE fd.request_id = :request_id AND fd.status = 'Successful'
            ORDER BY fd.created_at DESC
        ");
        
        $this->db->bind(':request_id', $request_id);
        return $this->db->resultSet();
    }
    
    /**
     * Get total collected amount for a fundraiser
     */
    public function getCollectedAmount($request_id){
        $this->db->query("
            SELECT SUM(amount) as total
            FROM fundraising_donations
            WHERE request_id = :request_id AND status = 'Successful'
        ");
        
        $this->db->bind(':request_id', $request_id);
        $result = $this->db->single();
        return $result->total ?? 0;
    }
    
    /**
     * Update fundraiser status
     */
    public function updateStatus($id, $status, $rejection_reason = null){
        $this->db->query("
            UPDATE fundraising_requests 
            SET status = :status, 
                rejection_reason = :rejection_reason,
                updated_at = NOW()
            WHERE id = :id
        ");
        
        $this->db->bind(':id', $id);
        $this->db->bind(':status', $status);
        $this->db->bind(':rejection_reason', $rejection_reason);
        
        return $this->db->execute();
    }
}
?>
