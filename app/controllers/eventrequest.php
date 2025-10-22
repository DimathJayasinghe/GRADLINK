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
        // Use DB-backed model to fetch user's event requests
        SessionManager::ensureStarted();
        $current_user_id = SessionManager::getUserId();
        $rows = $this->model->getAllForUser($current_user_id);
        $data = ['myrequests' => $rows];
        $this->view("/request_dashboards/eventreq/v_myrequests", $data);
    }

    public function show($id = null) {
        // If no id is provided, redirect to the all page
        if($id === null) {
            header('Location: ' . URLROOT . '/eventrequest/all');
            exit();
        }
        // Fetch from DB
        $req = $this->model->getById((int)$id);
        if($req){
            $data = ['request' => $req];
            $this->view("/request_dashboards/eventreq/v_vieweventrequest", $data);
        } else {
            $data = ['request' => null];
            $this->view("/request_dashboards/eventreq/v_vieweventrequest", $data);
        }
    }
    
    public function analytics($id = null) {
        // If no id is provided, redirect to my requests
        if($id === null) {
            header('Location: ' . URLROOT . '/eventrequest/myrequests');
            exit();
        }
        $req = $this->model->getById((int)$id);
        if($req && $req->status === 'Approved'){
            $data = ['request' => $req, 'engagement' => $this->model->getAnalytics((int)$id)];
            $this->view("/request_dashboards/eventreq/v_eventrequest_analytics", $data);
        } else {
            header('Location: ' . URLROOT . '/eventrequest/myrequests');
            exit();
        }
    }

    public function edit($id = null) {
        // $event = $this->model->getEventRequestById($id);
        $event = $this->model->getById((int)$id);
        $data = ['event' => $event];
        $this->view("/request_dashboards/eventreq/v_eventrequest", $data);
    }

    // Handle update of an existing event request (POST)
    public function update($id = null){
        SessionManager::ensureStarted();
        if($_SERVER['REQUEST_METHOD'] !== 'POST' || $id === null){
            header('Location: ' . URLROOT . '/eventrequest/all'); exit();
        }

        require_once APPROOT . '/helpers/Csrf.php';
        if(!Csrf::validateRequest()){
            SessionManager::setFlash('error','Invalid CSRF token');
            header('Location: ' . URLROOT . '/eventrequest/all'); exit();
        }

        $row = $this->model->getById((int)$id);
        if(!$row){ SessionManager::setFlash('error','Event request not found'); header('Location: ' . URLROOT . '/eventrequest/all'); exit(); }

        $userId = SessionManager::getUserId();
        if($row->user_id !== $userId && !SessionManager::hasRole('admin')){
            SessionManager::setFlash('error','Permission denied'); header('Location: ' . URLROOT . '/eventrequest/all'); exit();
        }

        // Build update payload
        $data = [];
        $data['title'] = $_POST['event_title'] ?? '';
        $data['description'] = $_POST['description'] ?? null;
        $data['club_name'] = $_POST['organizer'] ?? null;
        $data['position'] = $_POST['requester_position'] ?? null;
        $data['event_date'] = $_POST['event_date'] ?? null;
        $data['event_time'] = $_POST['event_time'] ?? null;
        $data['event_venue'] = $_POST['venue'] ?? null;
        $data['status'] = $row->status ?? 'Pending';

        // handle uploaded image (optional)
        $imgName = $row->attachment_image ?? null;
        if(!empty($_FILES['event_image']) && !empty($_FILES['event_image']['tmp_name'])){
            $tmp = $_FILES['event_image']['tmp_name'];
            $orig = basename($_FILES['event_image']['name']);
            $safe = time() . '_' . preg_replace('/[^a-zA-Z0-9_\.-]/','_', $orig);
            $destDir = APPROOT . '/storage/posts';
            if(!is_dir($destDir)) @mkdir($destDir, 0755, true);
            $dest = $destDir . '/' . $safe;
            if(@move_uploaded_file($tmp, $dest)){
                $imgName = $safe;
            }
        }
        $data['attachment_image'] = $imgName;

        $res = $this->model->update((int)$id, $data);
        if($res !== false){
            SessionManager::setFlash('success','Event request updated');
            header('Location: ' . URLROOT . '/eventrequest/show/' . (int)$id);
            exit();
        } else {
            SessionManager::setFlash('error','Could not update event request');
            header('Location: ' . URLROOT . '/eventrequest/edit/' . (int)$id);
            exit();
        }
    }

    // Handle creation of a new event request (POST)
    public function create(){
        SessionManager::ensureStarted();
        // Only accept POST
        if($_SERVER['REQUEST_METHOD'] !== 'POST'){
            header('Location: ' . URLROOT . '/eventrequest');
            exit();
        }

        require_once APPROOT . '/helpers/Csrf.php';
        if(!Csrf::validateRequest()){
            SessionManager::setFlash('error','Invalid CSRF token');
            header('Location: ' . URLROOT . '/eventrequest');
            exit();
        }

        $userId = SessionManager::getUserId();
        if(!$userId){
            SessionManager::setFlash('error','Please login to request events');
            header('Location: ' . URLROOT . '/auth');
            exit();
        }

        $data = [];
        $data['user_id'] = $userId;
        $data['title'] = $_POST['event_title'] ?? '';
        $data['description'] = $_POST['description'] ?? '';
        $data['club_name'] = $_POST['organizer'] ?? '';
        $data['position'] = $_POST['requester_position'] ?? '';
        $data['event_date'] = $_POST['event_date'] ?? null;
        $data['event_time'] = $_POST['event_time'] ?? null;
        $data['event_venue'] = $_POST['venue'] ?? null;
        $data['status'] = 'Pending';

        // handle uploaded image
        $imgName = null;
        if(!empty($_FILES['event_image']) && !empty($_FILES['event_image']['tmp_name'])){
            $tmp = $_FILES['event_image']['tmp_name'];
            $orig = basename($_FILES['event_image']['name']);
            $safe = time() . '_' . preg_replace('/[^a-zA-Z0-9_\.-]/','_', $orig);
            $destDir = APPROOT . '/storage/posts';
            if(!is_dir($destDir)) @mkdir($destDir, 0755, true);
            $dest = $destDir . '/' . $safe;
            if(@move_uploaded_file($tmp, $dest)){
                $imgName = $safe;
            }
        }
        $data['attachment_image'] = $imgName;

        $newId = $this->model->create($data);
        if($newId){
            SessionManager::setFlash('success','Event request submitted');
            header('Location: ' . URLROOT . '/eventrequest/all');
            exit();
        } else {
            SessionManager::setFlash('error','Could not submit event request');
            header('Location: ' . URLROOT . '/eventrequest');
            exit();
        }
    }

    // Delete an event request (POST) - user or admin
    public function delete($id = null){
        SessionManager::ensureStarted();
        if($_SERVER['REQUEST_METHOD'] !== 'POST' || $id === null){
            header('Location: ' . URLROOT . '/eventrequest/all');
            exit();
        }
        require_once APPROOT . '/helpers/Csrf.php';
        if(!Csrf::validateRequest()){
            header('Location: ' . URLROOT . '/eventrequest/all');
            exit();
        }
        $row = $this->model->getById((int)$id);
        if(!$row){ header('Location: ' . URLROOT . '/eventrequest/all'); exit(); }
        $userId = SessionManager::getUserId();
        // allow owner or admin
        if($row->user_id !== $userId && !SessionManager::hasRole('admin')){
            header('Location: ' . URLROOT . '/eventrequest/all'); exit();
        }
        $this->model->delete((int)$id);
        SessionManager::setFlash('success','Event request deleted');
        header('Location: ' . URLROOT . '/eventrequest/all');
        exit();
    }

    // Approve an event request (admin) - toggles status to Approved
    public function approve($id = null){
        SessionManager::ensureStarted();
        SessionManager::requireRole('admin');
        if($id === null){ header('Location: ' . URLROOT . '/eventrequest/all'); exit(); }
        // Fetch request
        $req = $this->model->getById((int)$id);
        if(!$req){
            SessionManager::setFlash('error','Request not found');
            header('Location: ' . URLROOT . '/eventrequest/all'); exit();
        }

        // If already approved, skip conversion
        if($req->status === 'Approved'){
            SessionManager::setFlash('info','Request already approved');
            header('Location: ' . URLROOT . '/eventrequest/all'); exit();
        }

        // Prepare event payload
        $eventModel = $this->model('M_event');
        $startDatetime = null;
        if(!empty($req->event_date)){
            $time = !empty($req->event_time) ? $req->event_time : '00:00:00';
            $startDatetime = $req->event_date . ' ' . $time;
        }

        $eventData = [
            'slug' => null,
            'title' => $req->title,
            'description' => $req->description ?? null,
            'start_datetime' => $startDatetime ?? date('Y-m-d H:i:s'),
            'end_datetime' => null,
            'all_day' => 0,
            'timezone' => 'UTC',
            'venue' => $req->event_venue ?? null,
            'capacity' => null,
            'organizer_id' => $req->user_id ?? 0,
            'status' => 'published',
            'visibility' => 'public',
            'series_id' => null
        ];

        $newEventId = $eventModel->create($eventData);
        if($newEventId){
            // attach image if present
            if(!empty($req->attachment_image)){
                $eiModel = $this->model('M_event_image');
                // we stored files as safe filename in storage/posts
                $eiModel->addForEvent((int)$newEventId, $req->attachment_image, 1);
            }
            // mark request approved
            $this->model->setStatus((int)$id, 'Approved');
            SessionManager::setFlash('success','Event request approved and published');
        } else {
            SessionManager::setFlash('error','Could not create event from request');
        }

        header('Location: ' . URLROOT . '/eventrequest/all');
        exit();
    }

    /**
     * Create an event from a request object. Returns new event id or false.
     */
    protected function createEventFromRequest($req){
        if(!$req) return false;
        $eventModel = $this->model('M_event');
        $startDatetime = null;
        if(!empty($req->event_date)){
            $time = !empty($req->event_time) ? $req->event_time : '00:00:00';
            $startDatetime = $req->event_date . ' ' . $time;
        }
        $eventData = [
            'slug' => null,
            'title' => $req->title,
            'description' => $req->description ?? null,
            'start_datetime' => $startDatetime ?? date('Y-m-d H:i:s'),
            'end_datetime' => null,
            'all_day' => 0,
            'timezone' => 'UTC',
            'venue' => $req->event_venue ?? null,
            'capacity' => null,
            'organizer_id' => $req->user_id ?? 0,
            'status' => 'published',
            'visibility' => 'public',
            'series_id' => null
        ];
        $newEventId = $eventModel->create($eventData);
        if($newEventId){
            if(!empty($req->attachment_image)){
                $eiModel = $this->model('M_event_image');
                $eiModel->addForEvent((int)$newEventId, $req->attachment_image, 1);
            }
            $this->model->setStatus((int)$req->id, 'Approved');
            return $newEventId;
        }
        return false;
    }

    // ADMIN: JSON listing for event requests with filters
    public function admin_list(){
        SessionManager::ensureStarted();
        if(!SessionManager::hasRole('admin')){ http_response_code(403); echo 'Forbidden'; return; }
        $status = strtolower($_GET['status'] ?? 'all');
        $search = trim($_GET['search'] ?? '');
        $filters = [];
        if($status !== 'all') $filters['status'] = ucfirst($status);
        $rows = $this->model->getAll($filters);
        // simple search filter in PHP for title/description/club_name/user_name
        if($search){
            $s = strtolower($search);
            $rows = array_filter($rows, function($r) use ($s){
                foreach(['title','description','club_name','user_name'] as $k){ if(!empty($r->{$k}) && strpos(strtolower($r->{$k}), $s) !== false) return true; }
                return false;
            });
            $rows = array_values($rows);
        }
        header('Content-Type: application/json');
        echo json_encode(['requests' => $rows]);
    }

    // ADMIN: approve request (create event + mark approved) via AJAX
    public function admin_approve_ajax($id = null){
        SessionManager::ensureStarted();
        if(!SessionManager::hasRole('admin')){ http_response_code(403); echo json_encode(['ok'=>false,'error'=>'forbidden']); return; }
        if($id === null){ http_response_code(400); echo json_encode(['ok'=>false,'error'=>'missing id']); return; }
        $req = $this->model->getById((int)$id);
        if(!$req){ http_response_code(404); echo json_encode(['ok'=>false,'error'=>'not found']); return; }
        $newId = $this->createEventFromRequest($req);
        header('Content-Type: application/json');
        echo json_encode(['ok' => $newId ? true : false, 'new_event_id' => $newId]);
    }

    // ADMIN: mark request as rejected via AJAX
    public function admin_reject_ajax($id = null){
        SessionManager::ensureStarted();
        if(!SessionManager::hasRole('admin')){ http_response_code(403); echo json_encode(['ok'=>false,'error'=>'forbidden']); return; }
        if($id === null){ http_response_code(400); echo json_encode(['ok'=>false,'error'=>'missing id']); return; }
        $ok = $this->model->setStatus((int)$id, 'Rejected');
        header('Content-Type: application/json');
        echo json_encode(['ok' => $ok ? true : false]);
    }

    // ADMIN: generic set status endpoint (e.g., Pending, Approved, Rejected)
    public function admin_set_status($id = null, $status = null){
        SessionManager::ensureStarted();
        if(!SessionManager::hasRole('admin')){ http_response_code(403); echo json_encode(['ok'=>false,'error'=>'forbidden']); return; }
        if($id === null || $status === null){ http_response_code(400); echo json_encode(['ok'=>false,'error'=>'missing parameters']); return; }
        $allowed = ['Pending','Approved','Rejected'];
        $status = ucfirst(strtolower($status));
        if(!in_array($status, $allowed)){ http_response_code(400); echo json_encode(['ok'=>false,'error'=>'invalid status']); return; }
        $ok = $this->model->setStatus((int)$id, $status);
        header('Content-Type: application/json');
        echo json_encode(['ok' => $ok ? true : false]);
    }

    // Reject an event request (admin)
    public function reject($id = null){
        SessionManager::ensureStarted();
        SessionManager::requireRole('admin');
        if($id === null){ header('Location: ' . URLROOT . '/eventrequest/all'); exit(); }
    $this->model->setStatus((int)$id, 'Rejected');
        SessionManager::setFlash('success','Event request rejected');
        header('Location: ' . URLROOT . '/eventrequest/all');
        exit();
    }
}
?>