<?php
class postrequest extends Controller{
    private $model = null;
    public function __construct(){
        SessionManager::redirectToAuthIfNotLoggedIn();
        $this->model = $this->model('M_postrequest');
    }
    public function index(){
        $data = [
            // 'clubs' => $this->model->getAllClubs()
            'clubs' => [
                (object)['id'=>1,'name'=>'IEEE CS Chapter'],
                (object)['id'=>2,'name'=>'ACM Student Chapter'],
                (object)['id'=>3,'name'=>'Robotics Club'],
                (object)['id'=>4,'name'=>'Debating Society'],
                (object)['id'=>5,'name'=>'Drama Club'],
                (object)['id'=>6,'name'=>'Music Club'],
                (object)['id'=>7,'name'=>'Art Society'],
                (object)['id'=>8,'name'=>'Photography Club'],
                (object)['id'=>9,'name'=>'Environmental Club'],
                (object)['id'=>10,'name'=>'Literature Club']
            ]
            ];
        $this->view("/request_dashboards/postreq/v_postrequest",$data);
    }
    public function viewpostrequests(){
        $data = [
            'requests' => [
                (object)[
                    'req_id'=>1,
                    'status'=>'Pending',
                    'created_at'=>'2024-10-01 10:00:00',

                    'user_id'=>101,
                    'user_name'=>'john_doe',
                    'title'=>'Request for New Laptops',
                    'description'=>'We need new laptops for our coding workshops and hackathons.',
                    'attachment_image'=>null,
                    'club_name'=>'IEEE CS Chapter',
                    
                    'is_event'=>true,
                    'event_date'=>'2024-11-15',
                    'event_time'=>'14:00',
                    'event_venue'=>'Room 101, Tech Building',
                ],
                (object)[
                    'req_id'=>2,
                    'status'=>'Approved',
                    'created_at'=>'2024-09-20 14:30:00',

                    'user_id'=>102,
                    'user_name'=>'jane_smith',
                    'title'=>'Request for Art Supplies',
                    'description'=>'We need art supplies for our upcoming art exhibition.',
                    'attachment_image'=>null,
                    'club_name'=>'Art Society',
                    
                    'is_event'=>false,
                    'event_date'=>null,
                    'event_time'=>null,
                    'event_venue'=>null,
                ],
                (object)[
                    'req_id'=>3,
                    'status'=>'Rejected',
                    'created_at'=>'2024-08-15 09:15:00',

                    'user_id'=>103,
                    'user_name'=>'alice_wonder',
                    'title'=>'Request for Photography Equipment',
                    'description'=>'We need new cameras and lenses for our photography club activities.',
                    'attachment_image'=>null,
                    'club_name'=>'Photography Club',
                    
                    'is_event'=>true,
                    'event_date'=>'2024-12-05',
                    'event_time'=>'10:00',
                    'event_venue'=>'Auditorium',
                ],
                (object)[
                    'req_id'=>4,
                    'status'=>'Pending',
                    'created_at'=>'2024-10-05 11:45:00',

                    'user_id'=>104,
                    'user_name'=>'bob_builder',
                    'title'=>'Request for Robotics Kits',
                    'description'=>'We need robotics kits for our upcoming robotics competition.',
                    'attachment_image'=>null,
                    'club_name'=>'Robotics Club',
                    
                    'is_event'=>false,
                    'event_date'=>null,
                    'event_time'=>null,
                    'event_venue'=>null,
                ],
                (object)[
                    'req_id'=>5,
                    'status'=>'Approved',
                    'created_at'=>'2024-09-25 16:20:00',

                    'user_id'=>105,
                    'user_name'=>'charlie_chaplin',
                    'title'=>'Request for Drama Costumes',
                    'description'=>'We need costumes for our upcoming drama performance.',
                    'attachment_image'=>null,
                    'club_name'=>'Drama Club',
                    
                    'is_event'=>true,
                    'event_date'=>'2024-11-30',
                    'event_time'=>'18:00',
                    'event_venue'=>'Main Stage, Arts Building',
                ],
            ]
            ];
        $this->view("/request_dashboards/postreq/v_viewpostrequests",$data);
    }
}
?>