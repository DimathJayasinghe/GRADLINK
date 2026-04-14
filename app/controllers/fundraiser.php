<?php
class fundraiser extends Controller
{
    private $model = null;
    private $mediaHandler = null;
    public function __construct()
    {
        // Check if it's the search API endpoint, skip auth
        $uri = $_SERVER['REQUEST_URI'];
        if (strpos($uri, '/fundraiser/search') === false && strpos($uri, '/fundraiser/getActiveCampaigns') === false) {
            SessionManager::redirectToAuthIfNotLoggedIn();
        }
        $this->model = $this->model('M_fundraiser');
        $this->mediaHandler = new MediaFilesHandler();
    }

    public function index()
    {
        // Redirect to all fundraisers
        header('Location: ' . URLROOT . '/fundraiser/all');
        exit;
    }
    
    public function all()
    {
        // Get all approved fundraisers
        $fundraisers = $this->model->getAllFundraisers();
        
        $data = [
            'fundraise_reqs' => $fundraisers
        ];
        
        $this->view("/request_dashboards/fundraise/v_view_all_fundraise_req", $data);
    }
    public function show($req_id)
    {
        // Get specific fundraiser by ID
        $fundraiser = $this->model->getFundraiserById($req_id);
        
        if (!$fundraiser) {
            // Fundraiser not found, redirect to all
            header('Location: ' . URLROOT . '/fundraiser/all');
            exit;
        }
        
        // Get additional data
        $bankDetails = $this->model->getBankDetails($req_id);
        $teamMembers = $this->model->getTeamMembers($req_id);
        $donations = $this->model->getDonations($req_id);
        
        // Get current user's public contribution (excluding anonymous donations)
        $userPublicContribution = 0;
        $userPublicPercentage = 0;
        if (isset($_SESSION['user_id'])) {
            $userPublicContribution = $this->model->getUserPublicContribution($req_id, $_SESSION['user_id']);
            if ($fundraiser->target_amount > 0) {
                $userPublicPercentage = ($userPublicContribution / $fundraiser->target_amount) * 100;
            }
        }
        
        $data = [
            'req_id' => $req_id,
            'fundraise_reqs' => [$fundraiser], // Analytics view expects an array
            'fundraiser' => $fundraiser,
            'bank_details' => $bankDetails,
            'team_members' => $teamMembers,
            'donations' => $donations,
            'user_public_contribution' => $userPublicContribution,
            'user_public_percentage' => $userPublicPercentage
        ];
        
        $this->view("/request_dashboards/fundraise/v_analytics_for_fundraise_req", $data);
    }
    public function request()
    {
        $this->view("/request_dashboards/fundraise/v_create_fundraise_req");
    }
    public function myrequests()
    {
        // Get current user's fundraisers
        $user_id = $_SESSION['user_id'];
        $fundraisers = $this->model->getMyFundraisers($user_id);
        
        $data = [
            'fundraise_reqs' => $fundraisers
        ];
        
        $this->view("/request_dashboards/fundraise/v_my_fundraize_req", $data);
    }

    /**
     * API endpoint for searching fundraisers
     * Returns JSON data for explore page
     * Note: This is a public API endpoint, no authentication required
     */
    public function search()
    {
        // Get search query from URL parameter
        $query = isset($_GET['q']) ? trim($_GET['q']) : '';
        $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 20;

        // Get all approved/active fundraisers from database
        $allFundraisers = $this->model->getAllFundraisers();
        
        // Process each fundraiser to add calculated fields
        $processedFundraisers = [];
        foreach ($allFundraisers as $fundraiser) {
            // Calculate days left
            $daysLeft = null;
            $now = new DateTime();
            $deadline = new DateTime($fundraiser->deadline);
            if ($deadline > $now) {
                $interval = $now->diff($deadline);
                $daysLeft = $interval->days;
            }
            
            // Calculate percentage
            $percentage = ($fundraiser->raised_amount / $fundraiser->target_amount) * 100;
            
            // Create processed object
            $processed = (object)[
                'id' => $fundraiser->req_id,
                'title' => $fundraiser->title,
                'description' => $fundraiser->description,
                'club_name' => $fundraiser->club_name,
                'club_id' => $fundraiser->user_id, // Using user_id as club_id
                'target_amount' => $fundraiser->target_amount,
                'raised_amount' => $fundraiser->raised_amount,
                'deadline' => $fundraiser->deadline,
                'status' => $fundraiser->status,
                'created_at' => $fundraiser->created_at,
                'days_left' => $daysLeft,
                'percentage' => round($percentage, 1)
            ];
            
            $processedFundraisers[] = $processed;
        }

        // Filter by search query if provided
        $filtered = [];
        if (!empty($query)) {
            $queryLower = strtolower($query);
            foreach ($processedFundraisers as $fundraiser) {
                if (
                    stripos($fundraiser->title, $query) !== false ||
                    stripos($fundraiser->description, $query) !== false ||
                    stripos($fundraiser->club_name, $query) !== false
                ) {
                    $filtered[] = $fundraiser;
                }
            }
        } else {
            $filtered = $processedFundraisers;
        }

        // Apply limit
        $filtered = array_slice($filtered, 0, $limit);

        // Return JSON response
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'data' => $filtered,
            'count' => count($filtered)
        ]);
        exit;
    }

    public function create()
    {
        // Handle form submission for creating a fundraiser request
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Process form data here
            $queryValues = $_POST;
            $errors = [];
            
            // Page 1: Contact & Project Information
            if(empty($_POST['club_name'])){
                $errors[] = 'Club name is required';
            }
            
            if(empty($_POST['position'])){
                $errors[] = 'Position is required';
            }
            
            if(empty($_POST['phone'])){
                $errors[] = 'Phone number is required';
            } elseif(!preg_match('/^[0-9]{10}$/', $_POST['phone'])){
                $errors[] = 'Phone number must be 10 digits';
            }
            
            // Page 2: Campaign Details
            if(empty($_POST['project_title'])){
                $errors[] = 'Project title is required';
            }
            
            if(empty($_POST['headline'])){
                $errors[] = 'Headline is required';
            }
            
            if(empty($_POST['description'])){
                $errors[] = 'Project description is required';
            }
            
            // Page 3: Financial Details
            if(empty($_POST['amount_needed'])){
                $errors[] = 'Amount needed is required';
            } elseif(!is_numeric($_POST['amount_needed']) || $_POST['amount_needed'] <= 0){
                $errors[] = 'Amount needed must be a positive number';
            }
            
            if(empty($_POST['objective'])){
                $errors[] = 'Objective is required';
            }
            
            if(empty($_POST['start_date'])){
                $errors[] = 'Start date is required';
            }
            
            if(empty($_POST['end_date'])){
                $errors[] = 'End date is required';
            }
            
            // Validate dates
            if(!empty($_POST['start_date']) && !empty($_POST['end_date'])){
                $startDate = strtotime($_POST['start_date']);
                $endDate = strtotime($_POST['end_date']);
                
                if($endDate < $startDate){
                    $errors[] = 'End date cannot be before start date';
                }
                
                if($startDate < strtotime('today')){
                    $errors[] = 'Start date cannot be in the past';
                }
            }
            
            // Fund Manager
            if(empty($_POST['fund_manager'])){
                $errors[] = 'Fund manager name is required';
            }
            
            if(empty($_POST['fund_manager_contact'])){
                $errors[] = 'Fund manager contact is required';
            } elseif(!preg_match('/^[0-9]{10}$/', $_POST['fund_manager_contact'])){
                $errors[] = 'Fund manager contact must be 10 digits';
            }
            
            // Bank Details
            if(empty($_POST['bank_name'])){
                $errors[] = 'Bank name is required';
            }
            
            if(empty($_POST['account_number'])){
                $errors[] = 'Account number is required';
            } elseif(!preg_match('/^[0-9]+$/', $_POST['account_number'])){
                $errors[] = 'Account number must contain only digits';
            }
            
            if(empty($_POST['branch'])){
                $errors[] = 'Branch is required';
            }
            
            if(empty($_POST['account_holder'])){
                $errors[] = 'Account holder name is required';
            }
            
            // File upload validation
            if(!empty($_FILES['project_poster']['tmp_name'])){
                $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png'];
                $maxSize = 5 * 1024 * 1024; // 5MB
                
                if(!in_array($_FILES['project_poster']['type'], $allowedTypes)){
                    $errors[] = 'Project poster must be JPG or PNG format';
                }
                
                if($_FILES['project_poster']['size'] > $maxSize){
                    $errors[] = 'Project poster must be less than 5MB';
                }
            }
            
            // Check if there are any errors
            if(count($errors) > 0){
                $data = [
                    'success' => false,
                    'errors' => $errors
                ];                
                $this->view('fundraiser/request', $data);
                exit;
            }
            
            // If validation passes, proceed with file upload
            if (!empty($_FILES['project_poster']['tmp_name'])) {
                $ext = pathinfo($_FILES['project_poster']['name'], PATHINFO_EXTENSION);

                $desiredName ='file_' . microtime(true) . '_' . bin2hex(random_bytes(4)) .($ext ? '.' . $ext : '');
                $upload = $this->mediaHandler->save(
                    $_FILES['project_poster']['tmp_name'],
                    'fundraisers',
                    $desiredName
                );
                // Use the filename from the upload result, not the whole array
                if ($upload['success']) {
                    $queryValues['project_poster'] = $upload['filename'];
                }
            }

            $result = $this->model->createNewFundraiserRequest($queryValues);
            $this->redirect('/fundraiser/myrequests');
            exit;
        } else {
            // If not a POST request, redirect to the create request form
            $this->redirect('/fundraiser/request');
            exit;
        }
    }
    
    /**
     * Display edit form for a pending fundraiser request
     */
    public function edit($req_id)
    {
        // Verify request exists and belongs to user
        $fundraiser = $this->model->getFullRequestData($req_id);
        
        if (!$fundraiser) {
            // Fundraiser not found
            $this->redirect('/fundraiser/myrequests');
            exit;
        }
        
        // Verify user owns this fundraiser
        if ($fundraiser->user_id != $_SESSION['user_id']) {
            // Not authorized
            $this->redirect('/fundraiser/myrequests');
            exit;
        }
        
        // Only allow editing if status is Pending
        if ($fundraiser->status !== 'Pending') {
            // Cannot edit approved/rejected requests
            $this->redirect('/fundraiser/myrequests');
            exit;
        }
        
        $data = [
            'fundraiser' => $fundraiser,
            'is_edit' => true
        ];
        
        $this->view("/request_dashboards/fundraise/v_edit_fundraise_req", $data);
    }
    
    /**
     * Process update of a pending fundraiser request
     */
    public function update($req_id)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/fundraiser/myrequests');
            exit;
        }
        
        // Verify request exists and belongs to user
        $fundraiser = $this->model->getFundraiserById($req_id);
        
        if (!$fundraiser) {
            $this->redirect('/fundraiser/myrequests');
            exit;
        }
        
        // Verify user owns this fundraiser
        if ($fundraiser->user_id != $_SESSION['user_id']) {
            $this->redirect('/fundraiser/myrequests');
            exit;
        }
        
        // Only allow updating of pending requests
        if ($fundraiser->status !== 'Pending') {
            $this->redirect('/fundraiser/myrequests');
            exit;
        }
        
        // Process form data (same validation as create)
        $queryValues = $_POST;
        $errors = [];
        
        // Page 1: Contact & Project Information
        if(empty($_POST['club_name'])){
            $errors[] = 'Club name is required';
        }
        
        if(empty($_POST['position'])){
            $errors[] = 'Position is required';
        }
        
        if(empty($_POST['phone'])){
            $errors[] = 'Phone number is required';
        } elseif(!preg_match('/^[0-9]{10}$/', $_POST['phone'])){
            $errors[] = 'Phone number must be 10 digits';
        }
        
        // Page 2: Campaign Details
        if(empty($_POST['project_title'])){
            $errors[] = 'Project title is required';
        }
        
        if(empty($_POST['headline'])){
            $errors[] = 'Headline is required';
        }
        
        if(empty($_POST['description'])){
            $errors[] = 'Project description is required';
        }
        
        // Page 3: Financial Details
        if(empty($_POST['amount_needed'])){
            $errors[] = 'Amount needed is required';
        } elseif(!is_numeric($_POST['amount_needed']) || $_POST['amount_needed'] <= 0){
            $errors[] = 'Amount needed must be a positive number';
        }
        
        if(empty($_POST['objective'])){
            $errors[] = 'Objective is required';
        }
        
        if(empty($_POST['start_date'])){
            $errors[] = 'Start date is required';
        }
        
        if(empty($_POST['end_date'])){
            $errors[] = 'End date is required';
        }
        
        // Validate dates
        if(!empty($_POST['start_date']) && !empty($_POST['end_date'])){
            $startDate = strtotime($_POST['start_date']);
            $endDate = strtotime($_POST['end_date']);
            
            if($endDate < $startDate){
                $errors[] = 'End date cannot be before start date';
            }
        }
        
        // Fund Manager
        if(empty($_POST['fund_manager'])){
            $errors[] = 'Fund manager name is required';
        }
        
        if(empty($_POST['fund_manager_contact'])){
            $errors[] = 'Fund manager contact is required';
        } elseif(!preg_match('/^[0-9]{10}$/', $_POST['fund_manager_contact'])){
            $errors[] = 'Fund manager contact must be 10 digits';
        }
        
        // Bank Details
        if(empty($_POST['bank_name'])){
            $errors[] = 'Bank name is required';
        }
        
        if(empty($_POST['account_number'])){
            $errors[] = 'Account number is required';
        } elseif(!preg_match('/^[0-9]+$/', $_POST['account_number'])){
            $errors[] = 'Account number must contain only digits';
        }
        
        if(empty($_POST['branch'])){
            $errors[] = 'Branch is required';
        }
        
        if(empty($_POST['account_holder'])){
            $errors[] = 'Account holder name is required';
        }
        
        // File upload validation (optional on update)
        if(!empty($_FILES['project_poster']['tmp_name'])){
            $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png'];
            $maxSize = 5 * 1024 * 1024; // 5MB
            
            if(!in_array($_FILES['project_poster']['type'], $allowedTypes)){
                $errors[] = 'Project poster must be JPG or PNG format';
            }
            
            if($_FILES['project_poster']['size'] > $maxSize){
                $errors[] = 'Project poster must be less than 5MB';
            }
        }
        
        // Check if there are any errors
        if(count($errors) > 0){
            $fullData = $this->model->getFullRequestData($req_id);
            $data = [
                'fundraiser' => $fullData,
                'is_edit' => true,
                'success' => false,
                'errors' => $errors
            ];
            $this->view('/request_dashboards/fundraise/v_edit_fundraise_req', $data);
            exit;
        }
        
        // Handle file upload if new file provided
        if (!empty($_FILES['project_poster']['tmp_name'])) {
            $ext = pathinfo($_FILES['project_poster']['name'], PATHINFO_EXTENSION);
            $desiredName = 'file_' . microtime(true) . '_' . bin2hex(random_bytes(4)) . ($ext ? '.' . $ext : '');
            $upload = $this->mediaHandler->save(
                $_FILES['project_poster']['tmp_name'],
                'fundraisers',
                $desiredName
            );
            // Use the filename from the upload result, not the whole array
            if ($upload['success']) {
                $queryValues['project_poster'] = $upload['filename'];
            } else {
                $queryValues['project_poster'] = $fundraiser->project_poster;
            }
        } else {
            // Keep existing poster
            $queryValues['project_poster'] = $fundraiser->project_poster;
        }

        $result = $this->model->updateFundraiserRequest($req_id, $queryValues);
        $this->redirect('/fundraiser/myrequests');
        exit;
    }
    
    public function getAvailableUsers()
    {
        $query = $this->getQueryParam('search', '');
        $data = [];
        
        $data['success'] = true;
        $data['users'] = $this->model->searchUsers($query);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
    
    /**
     * API endpoint to get active fundraiser campaigns
     * Used for sidebar widget and other displays
     */
    public function getActiveCampaigns()
    {
        // Get limit from query parameter
        $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 5;
        
        // Get active campaigns from model
        $campaigns = $this->model->getAllFundraisers();
        
        // Limit results
        $campaigns = array_slice($campaigns, 0, $limit);
        
        // Calculate additional data for each campaign
        $processedCampaigns = [];
        foreach ($campaigns as $campaign) {
            $targetAmount = (float)($campaign->target_amount ?? 0);
            $percentage = $targetAmount > 0 ? (((float)($campaign->raised_amount ?? 0) / $targetAmount) * 100) : 0;
            $daysLeft = null;
            
            // Calculate days left
            $deadlineTs = strtotime((string)($campaign->deadline ?? ''));
            if ($deadlineTs !== false && $deadlineTs > time()) {
                $daysLeft = (int)floor(($deadlineTs - time()) / 86400);
            }
            
            $processedCampaigns[] = [
                'id' => $campaign->req_id,
                'title' => $campaign->title,
                'club_name' => $campaign->club_name,
                'target_amount' => $campaign->target_amount,
                'raised_amount' => $campaign->raised_amount,
                'percentage' => round($percentage, 1),
                'deadline' => $campaign->deadline,
                'days_left' => $daysLeft,
                'status' => $campaign->status
            ];
        }
        
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'campaigns' => $processedCampaigns,
            'count' => count($processedCampaigns)
        ]);
        exit;
    }
    
    /**
     * Process a simulated donation (no external payment gateway).
     */
    public function processDonation()
    {
        header('Content-Type: application/json');
        
        try {
            $input = json_decode(file_get_contents('php://input'), true);

            if (!is_array($input)) {
                echo json_encode(['success' => false, 'error' => 'Invalid request payload']);
                exit;
            }
            
            if (empty($input['amount']) || empty($input['fundraiser_id'])) {
                echo json_encode(['success' => false, 'error' => 'Missing required fields']);
                exit;
            }
            
            $amount = (float)$input['amount'];
            $fundraiserId = (int)$input['fundraiser_id'];
            
            if ($amount < 100) {
                echo json_encode(['success' => false, 'error' => 'Minimum donation is LKR 100']);
                exit;
            }

            $fundraiser = $this->model->getFundraiserById($fundraiserId);
            if (!$fundraiser) {
                echo json_encode(['success' => false, 'error' => 'Fundraiser not found']);
                exit;
            }

            if (!in_array($fundraiser->status, ['Approved', 'Active'], true)) {
                echo json_encode(['success' => false, 'error' => 'This campaign is not accepting donations']);
                exit;
            }

            $remainingAmount = (float)$fundraiser->target_amount - (float)$fundraiser->raised_amount;
            if ($remainingAmount <= 0) {
                echo json_encode(['success' => false, 'error' => 'This campaign has already reached its target']);
                exit;
            }

            if ($amount > $remainingAmount) {
                echo json_encode([
                    'success' => false,
                    'error' => 'Donation exceeds remaining amount',
                    'remaining_amount' => round($remainingAmount, 2)
                ]);
                exit;
            }

            $donorName = trim((string)($input['donor_name'] ?? ''));
            if ($donorName === '') {
                $donorName = 'Anonymous';
            }

            $transactionRef = $this->generateMockTransactionReference();

            $donationData = [
                'request_id' => $fundraiserId,
                'donor_user_id' => $_SESSION['user_id'] ?? null,
                'amount' => $amount,
                'transaction_reference' => $transactionRef,
                'donor_name' => $donorName,
                'donor_email' => (string)($input['donor_email'] ?? ''),
                'message' => (string)($input['message'] ?? ''),
                'is_anonymous' => !empty($input['is_anonymous']),
                'status' => 'Successful'
            ];

            $saved = $this->model->createDonation($donationData);
            if (!$saved) {
                throw new Exception('Failed to save donation record');
            }

            $this->model->updateCollectedAmount($fundraiserId);
            $this->model->checkAndCompleteCampaign($fundraiserId);
            
            echo json_encode([
                'success' => true,
                'message' => 'Donation completed (simulated payment)',
                'transaction_reference' => $transactionRef
            ]);
            
        } catch (Exception $e) {
            error_log('Process Donation Error: ' . $e->getMessage());
            echo json_encode(['success' => false, 'error' => 'Donation processing error']);
        }
        exit;
    }

    private function generateMockTransactionReference()
    {
        return 'MOCK-' . strtoupper(bin2hex(random_bytes(8)));
    }

}
