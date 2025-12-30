<?php
class fundraiser extends Controller
{
    private $model = null;
    private $mediaHandler = null;
    public function __construct()
    {
        // Check if it's the search API endpoint, skip auth
        $uri = $_SERVER['REQUEST_URI'];
        if (strpos($uri, '/fundraiser/search') === false) {
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
        
        $data = [
            'req_id' => $req_id,
            'fundraise_reqs' => [$fundraiser], // Analytics view expects an array
            'fundraiser' => $fundraiser,
            'bank_details' => $bankDetails,
            'team_members' => $teamMembers,
            'donations' => $donations
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
                $queryValues['project_poster'] = $upload;
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
            $percentage = ($campaign->raised_amount / $campaign->target_amount) * 100;
            $daysLeft = null;
            
            // Calculate days left
            $now = new DateTime();
            $deadline = new DateTime($campaign->deadline);
            if ($deadline > $now) {
                $interval = $now->diff($deadline);
                $daysLeft = $interval->days;
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
     * Get Stripe publishable key for frontend
     */
    public function getStripeKey()
    {
        header('Content-Type: application/json');
        
        try {
            require_once APPROOT . '/libraries/StripeGateway.php';
            $stripe = new StripeGateway();
            
            echo json_encode([
                'success' => true,
                'publishable_key' => $stripe->getPublicKey()
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'error' => 'Failed to load Stripe configuration'
            ]);
        }
        exit;
    }

    
    /**
     * Create Stripe payment intent for donation  
     */
    public function createPaymentIntent()
    {
        header('Content-Type: application/json');
        
        try {
            $input = json_decode(file_get_contents('php://input'), true);
            
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
            
            require_once APPROOT . '/libraries/StripeGateway.php';
            $stripe = new StripeGateway();
            
            $metadata = [
                'fundraiser_id' => $fundraiserId,
                'fundraiser_title' => $fundraiser->title,
                'donor_name' => $input['donor_name'] ?? 'Anonymous',
                'donor_email' => $input['donor_email'] ?? '',
                'is_anonymous' => $input['is_anonymous'] ?? false,
                'description' => 'Donation to ' . $fundraiser->title,
                'receipt_email' => $input['donor_email'] ?? null
            ];
            
            $result = $stripe->createPaymentIntent($amount, $metadata);
            
            if ($result['success']) {
                $donationData = [
                    'request_id' => $fundraiserId,
                    'donor_user_id' => $_SESSION['user_id'] ?? null,
                    'amount' => $amount,
                    'transaction_reference' => $result['payment_intent_id'],
                    'donor_name' => $input['donor_name'] ?? 'Anonymous',
                    'donor_email' => $input['donor_email'] ?? '',
                    'message' => $input['message'] ?? '',
                    'is_anonymous' => $input['is_anonymous'] ?? false,
                    'status' => 'Pending'
                ];
                
                $this->model->createDonation($donationData);
                echo json_encode($result);
            } else {
                echo json_encode($result);
            }
            
        } catch (Exception $e) {
            error_log('Payment Intent Error: ' . $e->getMessage());
            echo json_encode(['success' => false, 'error' => 'Payment processing error']);
        }
        exit;
    }
    
    /**
     * Process successful donation
     */
    public function processDonation()
    {
        header('Content-Type: application/json');
        
        try {
            $input = json_decode(file_get_contents('php://input'), true);
            
            if (empty($input['payment_intent_id'])) {
                echo json_encode(['success' => false, 'error' => 'Missing payment intent ID']);
                exit;
            }
            
            $paymentIntentId = $input['payment_intent_id'];
            
            require_once APPROOT . '/libraries/StripeGateway.php';
            $stripe = new StripeGateway();
            
            $status = $stripe->getPaymentStatus($paymentIntentId);
            
            if ($status['success'] && $status['status'] === 'succeeded') {
                $this->model->updateDonationStatus($paymentIntentId, 'Successful');
                
                $donation = $this->model->getDonationByTransaction($paymentIntentId);
                if ($donation) {
                    $this->model->updateCollectedAmount($donation->request_id);
                }
                
                echo json_encode(['success' => true, 'message' => 'Donation processed successfully']);
            } else {
                echo json_encode(['success' => false, 'error' => 'Payment not completed']);
            }
            
        } catch (Exception $e) {
            error_log('Process Donation Error: ' . $e->getMessage());
            echo json_encode(['success' => false, 'error' => 'Processing error']);
        }
        exit;
    }
    
    /**
     * Stripe webhook endpoint
     */
    public function webhook()
    {
        $payload = @file_get_contents('php://input');
        $sigHeader = $_SERVER['HTTP_STRIPE_SIGNATURE'] ?? '';
        
        try {
            require_once APPROOT . '/libraries/StripeGateway.php';
            $stripe = new StripeGateway();
            
            $result = $stripe->handleWebhook($payload, $sigHeader);
            
            if ($result['success']) {
                if ($result['event_type'] === 'payment_succeeded') {
                    $this->model->updateDonationStatus($result['payment_intent_id'], 'Successful');
                    
                    if (isset($result['metadata']['fundraiser_id'])) {
                        $this->model->updateCollectedAmount($result['metadata']['fundraiser_id']);
                    }
                    
                } elseif ($result['event_type'] === 'payment_failed') {
                    $this->model->updateDonationStatus($result['payment_intent_id'], 'Failed');
                }
                
                http_response_code(200);
                echo json_encode(['received' => true]);
            } else {
                http_response_code(400);
                echo json_encode(['error' => $result['error']]);
            }
            
        } catch (Exception $e) {
            error_log('Webhook Error: ' . $e->getMessage());
            http_response_code(500);
            echo json_encode(['error' => 'Webhook processing error']);
        }
        exit;
    }

}
