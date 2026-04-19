<?php
class calender extends Controller{
    public function __construct() {
        $uri = $_SERVER['REQUEST_URI'] ?? '';
        if (strpos($uri, '/calender/getUpcomingEvents') === false) {
            SessionManager::redirectToAuthIfNotLoggedIn();
        }
    }
    public function index() {
        $eventModel = $this->model('M_event');

        $start = date('Y-m-01');
        $end = date('Y-m-t');

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

        $payload = [];
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


    public function getUpcomingEvents()
    {
        header('Content-Type: application/json; charset=utf-8');

        $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 3;
        if ($limit < 1) {
            $limit = 3;
        }
        if ($limit > 20) {
            $limit = 20;
        }

        $eventModel = $this->model('M_event');
        $events = $eventModel->findList([
            'start' => date('Y-m-d H:i:s'),
            'visibility' => 'public',
            'limit' => $limit
        ]);

        $payload = [];
        foreach ($events as $event) {
            $startTs = strtotime((string)($event->start_datetime ?? ''));
            if ($startTs === false) {
                continue;
            }
            $payload[] = [
                'id' => (int)$event->id,
                'title' => (string)$event->title,
                'club_name' => (string)($event->organizer_name ?? 'Campus Event'),
                'event_date' => date('Y-m-d', $startTs),
                'event_date_display' => date('d M', $startTs),
                'event_time' => date('H:i', $startTs),
                'event_time_display' => date('h:i A', $startTs),
                'event_venue' => (string)($event->venue ?? 'TBA')
            ];
        }

        echo json_encode([
            'success' => true,
            'events' => $payload,
            'count' => count($payload)
        ]);
        return;
    }

    public function show($id = null) {
        if($id === null) {
            header('Location: ' . URLROOT . '/calender');
            exit();
        }

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


        SessionManager::ensureStarted();
        $currentUserId = SessionManager::getUserId();
        $bmModel = $this->model('M_event_bookmark');
        $isBookmarked = false;
        if($currentUserId){
            $isBookmarked = $bmModel->isBookmarked($currentUserId, (int)$event->id);
        }

        $normalized = (object)[
            'event_id' => $event->id,
            'organizer_id' => $event->organizer_id,
            'title' => $event->title,
            'description' => $event->description,
            'attachment_image' => $event->attachment_image ?? null,
            'club_name' => $event->organizer_name ?? null,
            'event_date' => date('Y-m-d', strtotime($event->start_datetime)),
            'event_time' => date('H:i', strtotime($event->start_datetime)),
            'event_venue' => $event->venue ?? null,
            'bookmarked' => $isBookmarked
        ];

        $this->model('M_event_image');

        $data = ['event' => $normalized];
        $this->view("/calender/v_vieweventdetails", $data);
    }

    public function bookmarks() {

            SessionManager::ensureStarted();
            $userId = SessionManager::getUserId();
            if(!$userId){
                header('Location: ' . URLROOT . '/calender');
                exit();
            }
        
            $bm = $this->model('M_event_bookmark');
            $bookmarks = $bm->getBookmarksForUser($userId);
        
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
    

        public function removeBookmark($eventId = null){
            SessionManager::ensureStarted();
            $userId = SessionManager::getUserId();
            if($_SERVER['REQUEST_METHOD'] !== 'POST'){
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

}
?>