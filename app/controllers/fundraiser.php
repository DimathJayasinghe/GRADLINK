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
        // Skip session check for API endpoint
        // Get search query from URL parameter
        $query = isset($_GET['q']) ? trim($_GET['q']) : '';
        $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 20;

        // Dummy fundraiser data
        $allFundraisers = [
            (object)[
                'id' => 1,
                'title' => 'IEEE Student Branch Technology Fund',
                'description' => 'We\'re raising funds to get new laptops for our coding workshops and hackathons. Right now, many of our participants share or borrow devices, which limits how much they can create and learn.',
                'club_name' => 'IEEE Student Branch',
                'club_id' => 1,
                'target_amount' => 150000,
                'raised_amount' => 89000,
                'deadline' => '2025-12-31',
                'status' => 'Approved',
                'created_at' => '2024-10-01 10:00:00',
                'days_left' => 30
            ],
            (object)[
                'id' => 2,
                'title' => 'Robotics Club Equipment Drive',
                'description' => 'Help us purchase essential robotics equipment and components for our annual robotics competition and weekly workshops.',
                'club_name' => 'Robotics Club',
                'club_id' => 2,
                'target_amount' => 200000,
                'raised_amount' => 145000,
                'deadline' => '2025-11-30',
                'status' => 'Approved',
                'created_at' => '2024-09-15 14:30:00',
                'days_left' => 60
            ],
            (object)[
                'id' => 3,
                'title' => 'Music Society Instrument Fund',
                'description' => 'We need new music instruments for our college band and orchestra. Support us in bringing more music to campus events.',
                'club_name' => 'Music Society',
                'club_id' => 3,
                'target_amount' => 180000,
                'raised_amount' => 72000,
                'deadline' => '2025-12-20',
                'status' => 'Approved',
                'created_at' => '2024-09-10 13:20:00',
                'days_left' => 49
            ],
            (object)[
                'id' => 4,
                'title' => 'Environmental Club Green Campus Initiative',
                'description' => 'Join us in creating a sustainable campus environment with solar panels, recycling stations, and a community garden.',
                'club_name' => 'Environmental Club',
                'club_id' => 4,
                'target_amount' => 250000,
                'raised_amount' => 98000,
                'deadline' => '2026-01-31',
                'status' => 'Approved',
                'created_at' => '2024-10-05 11:45:00',
                'days_left' => 91
            ],
            (object)[
                'id' => 5,
                'title' => 'Drama Society Stage Equipment',
                'description' => 'Help us upgrade our stage lighting and sound equipment for better theatrical productions and performances.',
                'club_name' => 'Drama Society',
                'club_id' => 5,
                'target_amount' => 120000,
                'raised_amount' => 55000,
                'deadline' => '2025-12-15',
                'status' => 'Approved',
                'created_at' => '2024-08-20 09:00:00',
                'days_left' => 44
            ],
            (object)[
                'id' => 6,
                'title' => 'Sports Club Athletic Equipment',
                'description' => 'Support our athletes by helping us purchase new sports equipment for cricket, basketball, and badminton teams.',
                'club_name' => 'Sports Club',
                'club_id' => 6,
                'target_amount' => 175000,
                'raised_amount' => 131000,
                'deadline' => '2025-11-25',
                'status' => 'Approved',
                'created_at' => '2024-08-15 09:15:00',
                'days_left' => 55
            ]
        ];

        // Filter by search query if provided
        $filtered = [];
        if (!empty($query)) {
            $queryLower = strtolower($query);
            foreach ($allFundraisers as $fundraiser) {
                if (
                    stripos($fundraiser->title, $query) !== false ||
                    stripos($fundraiser->description, $query) !== false ||
                    stripos($fundraiser->club_name, $query) !== false
                ) {
                    $filtered[] = $fundraiser;
                }
            }
        } else {
            $filtered = $allFundraisers;
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
}
