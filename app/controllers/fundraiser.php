<?php
    class fundraiser extends Controller{
        private $model = null;
        public function __construct(){
            // Check if it's the search API endpoint, skip auth
            $uri = $_SERVER['REQUEST_URI'];
            if (strpos($uri, '/fundraiser/search') === false) {
                SessionManager::redirectToAuthIfNotLoggedIn();
            }
            $this->model = $this->model('M_fundraiser');
        }

        public function index(){
            $data = [
                'fundraise_reqs' => [
                    (object)[
                        'req_id'=>1,
                        'status'=>'Approved',
                        'created_at'=>'2024-10-01 10:00:00',

                        'user_id'=>101,
                        'user_name'=>'john_doe',
                        'title'=>'Fundraiser for New Laptops',
                        'description'=>'We’re raising funds to get new laptops for our coding workshops and hackathons. Right now, many of our participants share or borrow devices, which limits how much they can create and learn. With your support, we can provide everyone the tools they need to code freely, innovate boldly, and build amazing projects together.',
                        'attachment_image'=>null,
                        'club_name'=>'IEEE CS Chapter',
                        
                        'target_amount'=>5000,
                        'raised_amount'=>1500,
                        'deadline'=>'2025-12-31',
                    ],
                    
                ]
            ];
            $this->view("/request_dashboards/fundraise/v_view_all_fundraise_req",$data);
        }
        public function all(){
            SessionManager::redirectIfLoggedIn("/fundraiser");
        }
        public function show($req_id){
            // show the analytics for the perticuler fundraiser request
            // $this->model->getFundraiseRequestById($req_id);
            $data = [
                'req_id'=>$req_id,
                'fundraise_reqs' => [
                    (object)[
                        'req_id'=>1,
                        'status'=>'Pending',
                        'created_at'=>'2024-10-01 10:00:00',

                        'user_id'=>101,
                        'user_name'=>'john_doe',
                        'title'=>'Fundraiser for New Laptops',
                        'description'=>'We’re raising funds to get new laptops for our coding workshops and hackathons. Right now, many of our participants share or borrow devices, which limits how much they can create and learn. With your support, we can provide everyone the tools they need to code freely, innovate boldly, and build amazing projects together.',
                        'attachment_image'=>null,
                        'club_name'=>'IEEE CS Chapter',
                        
                        'target_amount'=>5000,
                        'raised_amount'=>0,
                        'deadline'=>'2025-12-31',
                    ],
                    (object)[
                        'req_id'=>2,
                        'status'=>'Approved',
                        'created_at'=>'2024-09-20 14:30:00',

                        'user_id'=>102,
                        'user_name'=>'jane_smith',
                        'title'=>'Fundraiser for Art Supplies',
                        'description'=>'We need art supplies for our upcoming art exhibition.',
                        'attachment_image'=>null,
                        'club_name'=>'Art Society',
                        
                        'target_amount'=>2000,
                        'raised_amount'=>2000,
                        'deadline'=>'2024-11-30',
                    ],
                    (object)[
                        'req_id'=>3,
                        'status'=>'Rejected',
                        'created_at'=>'2024-08-15 09:15:00',

                        'user_id'=>103,
                        'user_name'=>'alice_wonder',
                        'title'=>'Fundraiser for Sports Equipment',
                        'description'=>'We need new sports equipment for our college teams.',
                        'attachment_image'=>null,
                        'club_name'=>'Sports Club',
                        
                        'target_amount'=>3000,
                        'raised_amount'=>0,
                        'deadline'=>'2025-12-15',
                    ],
                    (object)[
                        'req_id'=>4,
                        'status'=>'Pending',
                        'created_at'=>'2024-10-05 11:45:00',

                        'user_id'=>104,
                        'user_name'=>'bob_builder',
                        'title'=>'Fundraiser for Community Garden',
                        'description'=>'We need funds to start a community garden on campus.',
                        'attachment_image'=>null,
                        'club_name'=>'Environmental Club',
                        
                        'target_amount'=>4000,
                        'raised_amount'=>0,
                        'deadline'=>'2025-01-31',
                    ],
                    (object)[
                        'req_id'=>5,
                        'status'=>'Approved',
                        'created_at'=>'2024-09-10 13:20:00',

                        'user_id'=>105,
                        'user_name'=>'charlie_chaplin',
                        'title'=>'Fundraiser for Music Instruments',
                        'description'=>'We need new music instruments for our college band.',
                        'attachment_image'=>null,
                        'club_name'=>'Music Club',
                        
                        'target_amount'=>6000,
                        'raised_amount'=>4500,
                        'deadline'=>'2025-12-20',
                    ],
                ]
            ];
            $this->view("/request_dashboards/fundraise/v_analytics_for_fundraise_req",$data);
        }
        public function request(){
            $this->view("/request_dashboards/fundraise/v_create_fundraise_req");
        }
        public function myrequests(){
            $data = [
                'fundraise_reqs' => [
                    (object)[
                        'req_id'=>1,
                        'status'=>'Pending',
                        'created_at'=>'2024-10-01 10:00:00',

                        'user_id'=>101,
                        'user_name'=>'john_doe',
                        'title'=>'Fundraiser for New Laptops',
                        'description'=>'We’re raising funds to get new laptops for our coding workshops and hackathons. Right now, many of our participants share or borrow devices, which limits how much they can create and learn. With your support, we can provide everyone the tools they need to code freely, innovate boldly, and build amazing projects together.',
                        'attachment_image'=>null,
                        'club_name'=>'IEEE CS Chapter',
                        
                        'target_amount'=>5000,
                        'raised_amount'=>0,
                        'deadline'=>'2025-12-31',
                    ],
                    (object)[
                        'req_id'=>2,
                        'status'=>'Approved',
                        'created_at'=>'2024-09-20 14:30:00',

                        'user_id'=>102,
                        'user_name'=>'jane_smith',
                        'title'=>'Fundraiser for Art Supplies',
                        'description'=>'We need art supplies for our upcoming art exhibition.',
                        'attachment_image'=>null,
                        'club_name'=>'Art Society',
                        
                        'target_amount'=>2000,
                        'raised_amount'=>2000,
                        'deadline'=>'2024-11-30',
                    ],
                ]
            ];
            $this->view("/request_dashboards/fundraise/v_my_fundraize_req",$data);
        }

        /**
         * API endpoint for searching fundraisers
         * Returns JSON data for explore page
         * Note: This is a public API endpoint, no authentication required
         */
        public function search() {
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
                    if (stripos($fundraiser->title, $query) !== false ||
                        stripos($fundraiser->description, $query) !== false ||
                        stripos($fundraiser->club_name, $query) !== false) {
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
    }
    
?>