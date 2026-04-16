<?php
class M_fundraiser{
    private $db = null;

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
                        LEFT JOIN suspended_users su ON su.user_id = fr.user_id AND su.status = 'active'
            WHERE fr.status IN ('Approved', 'Active')
                            AND su.id IS NULL
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
                        LEFT JOIN suspended_users su ON su.user_id = fr.user_id AND su.status = 'active'
            WHERE fr.id = :id
                            AND su.id IS NULL
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
     * Get donations for a fundraiser with anonymous donations grouped
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
        $donations = $this->db->resultSet();
        
        // Group anonymous donations
        $publicDonations = [];
        $anonymousTotal = 0;
        $anonymousCount = 0;
        $latestAnonymousDate = null;
        
        foreach ($donations as $donation) {
            if ($donation->is_anonymous) {
                $anonymousTotal += $donation->amount;
                $anonymousCount++;
                if (!$latestAnonymousDate || strtotime($donation->created_at) > strtotime($latestAnonymousDate)) {
                    $latestAnonymousDate = $donation->created_at;
                }
            } else {
                $publicDonations[] = $donation;
            }
        }
        
        // Add grouped anonymous donation if any exist
        if ($anonymousCount > 0) {
            $anonymousDonation = (object)[
                'id' => 0,
                'amount' => $anonymousTotal,
                'transaction_reference' => 'anonymous_grouped',
                'donor_name' => 'Anonymous',
                'message' => $anonymousCount > 1 ? "$anonymousCount anonymous donors" : null,
                'is_anonymous' => true,
                'status' => 'Successful',
                'created_at' => $latestAnonymousDate,
                'user_name' => null,
                'display_name' => null,
                'profile_image' => null
            ];
            $publicDonations[] = $anonymousDonation;
        }
        
        // Sort by date again
        usort($publicDonations, function($a, $b) {
            return strtotime($b->created_at) - strtotime($a->created_at);
        });
        
        return $publicDonations;
    }
    
    /**
     * Get user's public (non-anonymous) contribution to a fundraiser
     */
    public function getUserPublicContribution($request_id, $user_id) {
        $this->db->query("
            SELECT COALESCE(SUM(amount), 0) as total_contribution
            FROM fundraising_donations
            WHERE request_id = :request_id 
              AND donor_user_id = :user_id 
              AND status = 'Successful'
              AND is_anonymous = 0
        ");
        
        $this->db->bind(':request_id', $request_id);
        $this->db->bind(':user_id', $user_id);
        
        $result = $this->db->single();
        return $result ? $result->total_contribution : 0;
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
    
    /**
     * Create a new donation record
     */
    public function createDonation($data) {
        $this->db->query("
            INSERT INTO fundraising_donations
            (request_id, donor_user_id, amount, transaction_reference, donor_name, donor_email, message, is_anonymous, status)
            VALUES
            (:request_id, :donor_user_id, :amount, :transaction_reference, :donor_name, :donor_email, :message, :is_anonymous, :status)
        ");
        
        $this->db->bind(':request_id', $data['request_id']);
        $this->db->bind(':donor_user_id', $data['donor_user_id']);
        $this->db->bind(':amount', $data['amount']);
        $this->db->bind(':transaction_reference', $data['transaction_reference']);
        $this->db->bind(':donor_name', $data['donor_name']);
        $this->db->bind(':donor_email', $data['donor_email']);
        $this->db->bind(':message', $data['message'] ?? '');
        $this->db->bind(':is_anonymous', $data['is_anonymous'] ? 1 : 0);
        $this->db->bind(':status', $data['status']);
        
        return $this->db->execute();
    }
    
    /**
     * Update donation status by transaction reference
     */
    public function updateDonationStatus($transactionRef, $status) {
        $this->db->query("
            UPDATE fundraising_donations
            SET status = :status
            WHERE transaction_reference = :transaction_ref
        ");
        
        $this->db->bind(':status', $status);
        $this->db->bind(':transaction_ref', $transactionRef);
        
        return $this->db->execute();
    }
    
    /**
     * Get donation by transaction reference
     */
    public function getDonationByTransaction($transactionRef) {
        $this->db->query("
            SELECT * FROM fundraising_donations
            WHERE transaction_reference = :transaction_ref
        ");
        
        $this->db->bind(':transaction_ref', $transactionRef);
        return $this->db->single();
    }
    
    /**
     * Update collected amount for a fundraiser (recalculate from successful donations)
     */
    public function updateCollectedAmount($requestId) {
        $this->db->query("
            UPDATE fundraising_requests
            SET collected_amount = (
                SELECT COALESCE(SUM(amount), 0)
                FROM fundraising_donations
                WHERE request_id = :request_id AND status = 'Successful'
            ),
            updated_at = NOW()
            WHERE id = :request_id
        ");
        
        $this->db->bind(':request_id', $requestId);
        
        return $this->db->execute();
    }
    
    /**
     * Get complete fundraiser request data including bank details and team members
     */
    public function getFullRequestData($req_id) {
        // Get main fundraiser data
        $fundraiser = $this->getFundraiserById($req_id);
        
        if (!$fundraiser) {
            return null;
        }
        
        // Get bank details
        $fundraiser->bank_details = $this->getBankDetails($req_id);
        
        // Get team members
        $fundraiser->team_members = $this->getTeamMembers($req_id);
        
        return $fundraiser;
    }
    
    /**
     * Update an existing fundraiser request with bank details and team members
     */
    public function updateFundraiserRequest($req_id, $data) {
        try {
            // Start transaction
            $this->db->beginTransaction();
            
            // Update main fundraising request
            $this->db->query("
                UPDATE fundraising_requests 
                SET club_name = :club_name,
                    requester_position = :position,
                    requester_phone = :phone,
                    title = :title,
                    headline = :headline,
                    description = :description,
                    project_poster = :poster,
                    goal_amount = :amount,
                    objective = :objective,
                    start_date = :start_date,
                    end_date = :end_date,
                    fund_manager_name = :fund_manager,
                    fund_manager_contact = :fund_manager_contact,
                    advisor_id = :advisor_id,
                    status = 'Pending',
                    updated_at = NOW()
                WHERE id = :req_id
            ");
            
            // Bind parameters
            $this->db->bind(':req_id', $req_id);
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
            
            // Update bank details
            $this->db->query("
                UPDATE fundraising_bank_details 
                SET bank_name = :bank_name,
                    account_number = :account_number,
                    branch = :branch,
                    account_holder = :account_holder
                WHERE request_id = :request_id
            ");
            
            $this->db->bind(':request_id', $req_id);
            $this->db->bind(':bank_name', $data['bank_name']);
            $this->db->bind(':account_number', $data['account_number']);
            $this->db->bind(':branch', $data['branch']);
            $this->db->bind(':account_holder', $data['account_holder']);
            
            $this->db->execute();
            
            // Delete existing team members
            $this->db->query("DELETE FROM fundraising_team_members WHERE request_id = :request_id");
            $this->db->bind(':request_id', $req_id);
            $this->db->execute();
            
            // Insert new team members if provided
            if (!empty($data['team_members']) && is_array($data['team_members'])) {
                foreach ($data['team_members'] as $member_id) {
                    $this->db->query("
                        INSERT INTO fundraising_team_members (request_id, user_id)
                        VALUES (:request_id, :user_id)
                    ");
                    $this->db->bind(':request_id', $req_id);
                    $this->db->bind(':user_id', $member_id);
                    $this->db->execute();
                }
            }
            
            // Commit transaction
            $this->db->commit();
            
            return true;
        } catch (Exception $e) {
            // Rollback on error
            $this->db->rollback();
            error_log("Error updating fundraiser request: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Check if campaign has reached target and mark as Completed
     */
    public function checkAndCompleteCampaign($requestId) {
        // Get fundraiser data
        $fundraiser = $this->getFundraiserById($requestId);
        
        if (!$fundraiser) {
            return false;
        }
        
        // Only check Approved campaigns
        if ($fundraiser->status !== 'Approved') {
            return false;
        }
        
        // Check if raised amount meets or exceeds target
        if ($fundraiser->raised_amount >= $fundraiser->target_amount) {
            $this->db->query("
                UPDATE fundraising_requests
                SET status = 'Completed',
                    updated_at = NOW()
                WHERE id = :request_id
            ");
            
            $this->db->bind(':request_id', $requestId);
            return $this->db->execute();
        }
        
        return false;
    }
}
?>
