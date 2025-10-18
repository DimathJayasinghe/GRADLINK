<?php
class eventrequest extends Controller{
    private $model = null;
    public function __construct(){
        SessionManager::redirectToAuthIfNotLoggedIn();
        $this->model = $this->model('M_eventrequest');
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
        $this->view("/request_dashboards/eventreq/v_eventrequest",$data);
    }
    
    public function all(){
        // In a real app, we would filter requests by the current user ID
        // For now, let's use mock data
        $current_user_id = 101; // Assuming this is the logged in user's ID
        
        // Get all requests that match the current user ID
        $allRequests = [
            (object)[
                'req_id'=>1,
                'status'=>'Approved',
                'created_at'=>'2025-10-01 10:00:00',
                'user_id'=>101,
                'user_name'=>'D. Jayasinghe',
                'Position'=>'Design Lead, IEEE CS Chapter',
                'title'=>'IEEE CS Annual General Meeting 2025',
                'description'=>'We are conducting our annual general meeting of the IEEE CS Chapter 2025. And we need to publish this event to invite all members and interested students to join us for this important gathering. The AGM will include presentations on our activities, elections for new committee members, and discussions on future plans for the chapter.',
                'attachment_image'=>'IEEE_CS_AGM_25.png',
                'club_name'=>'IEEE CS Chapter',
                'event_date'=>'2024-11-15',
                'event_time'=>'8:00',
                'event_venue'=>'@S104, Main Building',
                'views' => 328,
                'unique_viewers' => 215,
                'interested_count' => 45,
                'going_count' => 23
            ],
            (object)[
                'req_id'=>4,
                'status'=>'Pending',
                'created_at'=>'2025-10-05 11:20:00',
                'user_id'=>101,
                'user_name'=>'D. Jayasinghe',
                'Position'=>'Member, Drama Club',
                'title'=>'Drama Club Annual Performance: Shakespeare Reimagined',
                'description'=>'The Drama Club presents its annual performance, featuring modern interpretations of classic Shakespeare plays. Don\'t miss this creative showcase of student talent!',
                'attachment_image'=>'Drama_Performance.png',
                'club_name'=>'Drama Club',
                'event_date'=>'2025-12-10',
                'event_time'=>'18:30',
                'event_venue'=>'University Theater',
            ],
        ];
        
        $myRequests = [];
        foreach ($allRequests as $request) {
            if ($request->user_id === $current_user_id) {
                $myRequests[] = $request;
            }
        }
        
        $data = [
            'myrequests' => $myRequests
        ];
        
        $this->view("/request_dashboards/eventreq/v_myrequests", $data);
    }

    public function show($id = null) {
        // If no id is provided, redirect to the all page
        if($id === null) {
            header('Location: ' . URLROOT . '/eventrequest/all');
            exit();
        }

        // In a real app, we would fetch the request from the database
        // For now, we'll use mock data
        $mockRequests = [
            1=>(object)[
                'req_id'=>1,
                'status'=>'Approved',
                'created_at'=>'2025-10-01 10:00:00',
                'user_id'=>101,
                'user_name'=>'D. Jayasinghe',
                'Position'=>'Design Lead, IEEE CS Chapter',
                'title'=>'IEEE CS Annual General Meeting 2025',
                'description'=>'We are conducting our annual general meeting of the IEEE CS Chapter 2025. And we need to publish this event to invite all members and interested students to join us for this important gathering. The AGM will include presentations on our activities, elections for new committee members, and discussions on future plans for the chapter.',
                'attachment_image'=>'IEEE_CS_AGM_25.png',
                'club_name'=>'IEEE CS Chapter',
                'event_date'=>'2024-11-15',
                'event_time'=>'8:00',
                'event_venue'=>'@S104, Main Building',
                'views' => 328,
                'unique_viewers' => 215,
                'interested_count' => 45,
                'going_count' => 23
            ],
            4=>(object)[
                'req_id'=>4,
                'status'=>'Pending',
                'created_at'=>'2025-10-05 11:20:00',
                'user_id'=>101,
                'user_name'=>'D. Jayasinghe',
                'Position'=>'Member, Drama Club',
                'title'=>'Drama Club Annual Performance: Shakespeare Reimagined',
                'description'=>'The Drama Club presents its annual performance, featuring modern interpretations of classic Shakespeare plays. Don\'t miss this creative showcase of student talent!',
                'attachment_image'=>'Drama_Performance.png',
                'club_name'=>'Drama Club',
                'event_date'=>'2025-12-10',
                'event_time'=>'18:30',
                'event_venue'=>'University Theater',
            ],
        ];

        // Check if the request exists
        if(isset($mockRequests[$id])) {
            $data = [
                'request' => $mockRequests[$id]
            ];
            $this->view("/request_dashboards/eventreq/v_vieweventrequest", $data);
        } else {
            // If the request doesn't exist, show a blank page with not found message
            $data = [
                'request' => null
            ];
            $this->view("/request_dashboards/eventreq/v_vieweventrequest", $data);
        }
    }
    
    public function analytics($id = null) {
        // If no id is provided, redirect to my requests
        if($id === null) {
            header('Location: ' . URLROOT . '/eventrequest/myrequests');
            exit();
        }
        
        // Fetch the request data
        $mockRequests = [
            1=>(object)[
                'req_id'=>1,
                'status'=>'Approved',
                'created_at'=>'2025-10-01 10:00:00',
                'user_id'=>101,
                'user_name'=>'D. Jayasinghe',
                'Position'=>'Design Lead, IEEE CS Chapter',
                'title'=>'IEEE CS Annual General Meeting 2025',
                'description'=>'We are conducting our annual general meeting of the IEEE CS Chapter 2025. And we need to publish this event to invite all members and interested students to join us for this important gathering. The AGM will include presentations on our activities, elections for new committee members, and discussions on future plans for the chapter.',
                'attachment_image'=>'IEEE_CS_AGM_25.png',
                'club_name'=>'IEEE CS Chapter',
                'event_date'=>'2024-11-15',
                'event_time'=>'8:00',
                'event_venue'=>'@S104, Main Building',
                'views' => 328,
                'unique_viewers' => 215,
                'interested_count' => 45,
                'going_count' => 23
            ],
            4=>(object)[
                'req_id'=>4,
                'status'=>'Pending',
                'created_at'=>'2025-10-05 11:20:00',
                'user_id'=>101,
                'user_name'=>'D. Jayasinghe',
                'Position'=>'Member, Drama Club',
                'title'=>'Drama Club Annual Performance: Shakespeare Reimagined',
                'description'=>'The Drama Club presents its annual performance, featuring modern interpretations of classic Shakespeare plays. Don\'t miss this creative showcase of student talent!',
                'attachment_image'=>'Drama_Performance.png',
                'club_name'=>'Drama Club',
                'event_date'=>'2025-12-10',
                'event_time'=>'18:30',
                'event_venue'=>'University Theater',
            ],
        ];
        
        // Check if the request exists and is approved (only approved requests have analytics)
        if(isset($mockRequests[$id]) && $mockRequests[$id]->status === 'Approved') {
            $data = [
                'request' => $mockRequests[$id],
                'engagement' => [
                    // You could add detailed engagement data here if needed
                ]
            ];
            $this->view("/request_dashboards/eventreq/v_eventrequest_analytics", $data);
        } else {
            // If the request doesn't exist or isn't approved, redirect to my requests
            header('Location: ' . URLROOT . '/eventrequest/myrequests');
            exit();
        }
    }
}
?>