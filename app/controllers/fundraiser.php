<?php
    class fundraiser extends Controller{
        private $model = null;
        public function __construct(){
            SessionManager::redirectToAuthIfNotLoggedIn();
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
                        'description'=>'We need new laptops for our coding workshops and hackathons.',
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
                        'description'=>'We need new laptops for our coding workshops and hackathons.',
                        'attachment_image'=>null,
                        'club_name'=>'IEEE CS Chapter',
                        
                        'target_amount'=>5000,
                        'raised_amount'=>1500,
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
                        'raised_amount'=>500,
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
                        'raised_amount'=>1000,
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
                        'description'=>'We need new laptops for our coding workshops and hackathons.',
                        'attachment_image'=>null,
                        'club_name'=>'IEEE CS Chapter',
                        
                        'target_amount'=>5000,
                        'raised_amount'=>1500,
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
    }
?>