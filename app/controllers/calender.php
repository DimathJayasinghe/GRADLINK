<?php
class calender extends Controller{
    public function __construct() {
        SessionManager::redirectToAuthIfNotLoggedIn();
    }
    public function index() {
        // Load events for the current month to populate the calendar JS
        $eventModel = $this->model('M_event');

        // Default: current month range (but show only upcoming events)
        $start = date('Y-m-01');
        $end = date('Y-m-t');

        // Compute effective start as the later of the month start and "now"
        $nowTs = time();
        $rangeStartTs = strtotime($start . ' 00:00:00');
        $rangeEndTs = strtotime($end . ' 23:59:59');
        $effectiveStart = date('Y-m-d H:i:s', max($rangeStartTs, $nowTs));
        $effectiveEnd = date('Y-m-d H:i:s', $rangeEndTs);

        $events = $eventModel->findList([
            'start' => $effectiveStart,
            'end' => $effectiveEnd,
            'visibility' => 'public'
        ]);

        // Normalize events into the lightweight JS-friendly payload
        $payload = [];
        // Attempt to determine current user so we can mark bookmarked state
        SessionManager::ensureStarted();
        $currentUserId = SessionManager::getUserId();
        $bmModel = $this->model('M_event_bookmark');
        foreach($events as $e){
            $date = date('Y-m-d', strtotime($e->start_datetime));
            $time = date('H:i', strtotime($e->start_datetime));
            $isBookmarked = false;
            if($currentUserId){
                $isBookmarked = $bmModel->isBookmarked($currentUserId, (int)$e->id);
            }
            $payload[$date][] = [
                'id' => $e->id,
                'title' => $e->title,
                'time' => $time,
                'description' => $e->description,
                'attachment_image' => $e->attachment_image ?? null,
                'club_name' => $e->organizer_name ?? null,
                'event_venue' => $e->venue ?? null,
                'bookmarked' => $isBookmarked
            ];
        }

        $data = ['events_payload' => $payload];
        $this->view("/calender/v_calender", $data);
    }
    public function show($id = null) {
        // If no id is provided, redirect to the all page
        if($id === null) {
            header('Location: ' . URLROOT . '/calender');
            exit();
        }

        // Load real event by id
        $eventModel = $this->model('M_event');
        $event = null;
        if(ctype_digit((string)$id)){
            $event = $eventModel->findById((int)$id);
        }

        if(!$event){
            $data = ['event' => null];
            $this->view("/calender/v_vieweventdetails", $data);
            return;
        }

        // normalize to view expected property names (backwards-compatible)
        // Determine bookmark status for this user
        SessionManager::ensureStarted();
        $currentUserId = SessionManager::getUserId();
        $bmModel = $this->model('M_event_bookmark');
        $isBookmarked = false;
        if($currentUserId){
            $isBookmarked = $bmModel->isBookmarked($currentUserId, (int)$event->id);
        }

        $normalized = (object)[
            'event_id' => $event->id,
            'title' => $event->title,
            'description' => $event->description,
            'attachment_image' => $event->attachment_image ?? null,
            'club_name' => $event->organizer_name ?? null,
            'event_date' => date('Y-m-d', strtotime($event->start_datetime)),
            'event_time' => date('H:i', strtotime($event->start_datetime)),
            'event_venue' => $event->venue ?? null,
            'bookmarked' => $isBookmarked
        ];

        // Ensure the event image helper/model is loaded so the view can call M_event_image::getUrl()
        // This avoids a fatal 'Class not found' if the view references the helper.
        $this->model('M_event_image');
        // Fetch current attendee snapshot for this event so the view can render attendees
        $attModel = $this->model('M_attendee');
        $attendees = $attModel->getAttendees((int)$event->id);

        $data = ['event' => $normalized, 'attendees' => $attendees];
        $this->view("/calender/v_vieweventdetails", $data);
    }

    public function bookmarks() {
        // In a real app, we would fetch the bookmarked events from the database
        // For now, we'll use mock data
            // Ensure user is logged in
            SessionManager::ensureStarted();
            $userId = SessionManager::getUserId();
            if(!$userId){
                // Redirect to calendar if not authenticated
                header('Location: ' . URLROOT . '/calender');
                exit();
            }
        
            $bm = $this->model('M_event_bookmark');
            $bookmarks = $bm->getBookmarksForUser($userId);
        
            // Normalize rows to match the view's expectations
            $normalized = [];
            foreach($bookmarks as $b){
                $normalized[] = (object)[
                    'event_id' => $b->event_id,
                    'title' => $b->title,
                    'description' => $b->description,
                    'attachment_image' => $b->attachment_image ?? null,
                    'club_name' => $b->club_name ?? null,
                    'event_date' => date('Y-m-d', strtotime($b->start_datetime)),
                    'event_time' => date('H:i', strtotime($b->start_datetime)),
                    'event_venue' => $b->event_venue ?? null,
                    'created_at' => $b->created_at,
                    'bookmarked' => true
                ];
            }
        
            $data = ['bookmarked_events' => $normalized];
            $this->view("/calender/v_bookmarked_events", $data);
        }
    
        /**
         * Remove a bookmarked event for the current user.
         * Called as: /calender/removeBookmark/{eventId}
         */
        public function removeBookmark($eventId = null){
            SessionManager::ensureStarted();
            $userId = SessionManager::getUserId();
            // Only accept POST to delete a bookmark
            if($_SERVER['REQUEST_METHOD'] !== 'POST'){
                header('Location: ' . URLROOT . '/calender/bookmarks');
                exit();
            }

            // Validate CSRF
            require_once APPROOT . '/helpers/Csrf.php';
            if(!Csrf::validateRequest()){
                header('Location: ' . URLROOT . '/calender/bookmarks');
                exit();
            }

            if(!$userId || $eventId === null){
                header('Location: ' . URLROOT . '/calender/bookmarks');
                exit();
            }

            $bm = $this->model('M_event_bookmark');
            $bm->remove($userId, (int)$eventId);
            header('Location: ' . URLROOT . '/calender/bookmarks');
            exit();
        }

        // JSON endpoint: return events grouped by date for a start/end range
    public function events(){
        $start = isset($_GET['start']) ? $_GET['start'] : null;
        $end = isset($_GET['end']) ? $_GET['end'] : null;

        header('Content-Type: application/json; charset=utf-8');
        $payload = [];

        if(!$start || !$end){
            echo json_encode((object)[]);
            return;
        }

    $eventModel = $this->model('M_event');
    // Only return events ahead of "now" even if the requested window is in the past
    $nowTs = time();
    $rangeStartTs = strtotime($start . ' 00:00:00');
    $rangeEndTs = strtotime($end . ' 23:59:59');
    $effectiveStart = date('Y-m-d H:i:s', max($rangeStartTs, $nowTs));
    $effectiveEnd = date('Y-m-d H:i:s', $rangeEndTs);
    $events = $eventModel->findList(['start' => $effectiveStart, 'end' => $effectiveEnd, 'visibility' => 'public']);

        // Determine current user so we can include bookmark state
        SessionManager::ensureStarted();
        $currentUserId = SessionManager::getUserId();
        $bmModel = $this->model('M_event_bookmark');

        foreach($events as $e){
            $date = date('Y-m-d', strtotime($e->start_datetime));
            $time = date('H:i', strtotime($e->start_datetime));
            $isBookmarked = false;
            if($currentUserId){
                $isBookmarked = $bmModel->isBookmarked($currentUserId, (int)$e->id);
            }
            $payload[$date][] = [
                'id' => $e->id,
                'title' => $e->title,
                'time' => $time,
                'description' => $e->description,
                'attachment_image' => property_exists($e,'attachment_image') ? $e->attachment_image : null,
                'club_name' => property_exists($e,'organizer_name') ? $e->organizer_name : null,
                'event_venue' => $e->venue ?? null,
                'bookmarked' => $isBookmarked
            ];
        }

        echo json_encode($payload);
        return;
    }

    // Toggle bookmark for the current user and given event_id (POST { event_id })
    public function toggleBookmark(){
        SessionManager::ensureStarted();
        header('Content-Type: application/json; charset=utf-8');
        $userId = SessionManager::getUserId();
        if(!$userId){
            echo json_encode(['ok'=>false,'error'=>'Not authenticated']);
            return;
        }
        // Validate CSRF token for AJAX POSTs
        require_once APPROOT . '/helpers/Csrf.php';
        if(!Csrf::validateRequest()){
            echo json_encode(['ok'=>false,'error'=>'Invalid CSRF token']);
            return;
        }

        $input = json_decode(file_get_contents('php://input'), true);
        $eventId = isset($input['event_id']) ? (int)$input['event_id'] : 0;
        if(!$eventId){
            echo json_encode(['ok'=>false,'error'=>'Missing event_id']);
            return;
        }

        $bmModel = $this->model('M_event_bookmark');
        $newState = $bmModel->toggle($userId, $eventId);
        echo json_encode(['ok'=>true,'bookmarked'=>$newState]);
        return;
    }

    // Add a bookmark for the current user via AJAX (POST { event_id })
    public function addBookmark(){
        SessionManager::ensureStarted();
        header('Content-Type: application/json; charset=utf-8');
        $userId = SessionManager::getUserId();
        if(!$userId){
            echo json_encode(['ok'=>false,'error'=>'Not authenticated']);
            return;
        }
        require_once APPROOT . '/helpers/Csrf.php';
        if(!Csrf::validateRequest()){
            echo json_encode(['ok'=>false,'error'=>'Invalid CSRF token']);
            return;
        }
        $input = json_decode(file_get_contents('php://input'), true);
        $eventId = isset($input['event_id']) ? (int)$input['event_id'] : 0;
        if(!$eventId){
            echo json_encode(['ok'=>false,'error'=>'Missing event_id']);
            return;
        }
        try {
            $bmModel = $this->model('M_event_bookmark');
            $added = $bmModel->add($userId, $eventId);
            echo json_encode(['ok'=> (bool)$added, 'bookmarked' => (bool)$added]);
        } catch(Exception $ex) {
            // Return error details in dev; still safe to expose a message for debugging
            echo json_encode(['ok' => false, 'error' => 'Exception: ' . $ex->getMessage()]);
        }
        return;
    }

    // Remove a bookmark via AJAX (POST { event_id })
    public function removeBookmarkAjax(){
        SessionManager::ensureStarted();
        header('Content-Type: application/json; charset=utf-8');
        $userId = SessionManager::getUserId();
        if(!$userId){
            echo json_encode(['ok'=>false,'error'=>'Not authenticated']);
            return;
        }
        require_once APPROOT . '/helpers/Csrf.php';
        if(!Csrf::validateRequest()){
            echo json_encode(['ok'=>false,'error'=>'Invalid CSRF token']);
            return;
        }
        $input = json_decode(file_get_contents('php://input'), true);
        $eventId = isset($input['event_id']) ? (int)$input['event_id'] : 0;
        if(!$eventId){
            echo json_encode(['ok'=>false,'error'=>'Missing event_id']);
            return;
        }
        try {
            $bmModel = $this->model('M_event_bookmark');
            $removed = $bmModel->remove($userId, $eventId);
            echo json_encode(['ok'=> (bool)$removed, 'bookmarked' => false]);
        } catch(Exception $ex) {
            echo json_encode(['ok' => false, 'error' => 'Exception: ' . $ex->getMessage()]);
        }
        return;
    }

    // RSVP endpoint (POST { event_id, status, guests }) - returns attendee record id or false
    public function rsvp(){
        SessionManager::ensureStarted();
        header('Content-Type: application/json; charset=utf-8');
        $userId = SessionManager::getUserId();
        if(!$userId){
            echo json_encode(['ok'=>false,'error'=>'Not authenticated']);
            return;
        }
        // Validate CSRF token for AJAX POSTs
        require_once APPROOT . '/helpers/Csrf.php';
        if(!Csrf::validateRequest()){
            echo json_encode(['ok'=>false,'error'=>'Invalid CSRF token']);
            return;
        }

        $input = json_decode(file_get_contents('php://input'), true);
        $eventId = isset($input['event_id']) ? (int)$input['event_id'] : 0;
        $status = isset($input['status']) ? $input['status'] : 'attending';
        $guests = isset($input['guests']) ? (int)$input['guests'] : 0;
        if(!$eventId){
            echo json_encode(['ok'=>false,'error'=>'Missing event_id']);
            return;
        }

        $attModel = $this->model('M_attendee');
        $attId = $attModel->rsvp($eventId, $userId, $status, $guests);
        if($attId){
            echo json_encode(['ok'=>true,'attendee_id'=>$attId]);
        } else {
            echo json_encode(['ok'=>false,'error'=>'Could not RSVP']);
        }
        return;
    }

    // Cancel an RSVP for the current user (POST { event_id })
    public function cancelRsvp(){
        SessionManager::ensureStarted();
        header('Content-Type: application/json; charset=utf-8');
        $userId = SessionManager::getUserId();
        if(!$userId){
            echo json_encode(['ok'=>false,'error'=>'Not authenticated']);
            return;
        }
        require_once APPROOT . '/helpers/Csrf.php';
        if(!Csrf::validateRequest()){
            echo json_encode(['ok'=>false,'error'=>'Invalid CSRF token']);
            return;
        }
        $input = json_decode(file_get_contents('php://input'), true);
        $eventId = isset($input['event_id']) ? (int)$input['event_id'] : 0;
        if(!$eventId){
            echo json_encode(['ok'=>false,'error'=>'Missing event_id']);
            return;
        }
        $attModel = $this->model('M_attendee');
        $count = $attModel->cancel($eventId, $userId);
        echo json_encode(['ok'=>true,'removed' => (bool)$count]);
        return;
    }

    /**
     * Return attendees for an event as JSON.
     * GET parameter: event_id
     */
    public function attendees(){
        header('Content-Type: application/json; charset=utf-8');
        $eventId = isset($_GET['event_id']) ? (int)$_GET['event_id'] : 0;
        if(!$eventId){
            echo json_encode(['ok'=>false,'error'=>'Missing event_id']);
            return;
        }

        $attModel = $this->model('M_attendee');
        $rows = $attModel->getAttendees($eventId);
        echo json_encode(['ok'=>true,'attendees'=>$rows]);
        return;
    }
}
?>