<?php
class calender extends Controller{
    public function __construct() {
        SessionManager::redirectToAuthIfNotLoggedIn();
    }
    public function index() {
        $this->view("/calender/v_calender");
    }
    public function show($id = null) {
        // If no id is provided, redirect to the all page
        if($id === null) {
            header('Location: ' . URLROOT . '/calender');
            exit();
        }

        // In a real app, we would fetch the request from the database
        // For now, we'll use mock data
        $mockEvents = [
            1=>(object)[
                'event_id'=>1,
                'status'=>'Approved',
                'created_at'=>'2025-10-01 10:00:00',
                'user_id'=>3,
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
                'going_count' => 23,
                'bookmarked' => false
            ],
            4=>(object)[
                'event_id'=>4,
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
                'bookmarked' => false
            ],
        ];

        // Check if the request exists
        if(isset($mockEvents[$id])) {
            $data = [
                'event' => $mockEvents[$id]
            ];
            $this->view("/calender/v_vieweventdetails", $data);
        } else {
            // If the request doesn't exist, show a blank page with not found message
            $data = [
                'event' => null
            ];
            $this->view("/calender/v_vieweventdetails", $data);
        }
    }

    public function bookmarks() {
        // In a real app, we would fetch the bookmarked events from the database
        // For now, we'll use mock data
        $mockBookmarkedEvents = [
            (object)[
                'event_id'=>1,
                'status'=>'Approved',
                'created_at'=>'2025-10-01 10:00:00',
                'user_id'=>3,
                'user_name'=>'D. Jayasinghe',
                'Position'=>'Design Lead, IEEE CS Chapter',
                'title'=>'IEEE CS Annual General Meeting 2025',
                'description'=>'We are conducting our annual general meeting of the IEEE CS Chapter 2025. And we need to publish this event to invite all members and interested students to join us for this important gathering. The AGM will include presentations on our activities, elections for new committee members, and discussions on future plans for the chapter.',
                'attachment_image'=>'IEEE_CS_AGM_25.png',
                'club_name'=>'IEEE CS Chapter',
                'event_date'=>'2024-11-15',
                'event_time'=>'8:00',
                'event_venue'=>'@S104, Main Building',
                'bookmarked' => true
            ],
        ];

        $data = [
            'bookmarked_events' => $mockBookmarkedEvents
        ];

        $this->view("/calender/v_bookmarked_events", $data);
    }
}
?>