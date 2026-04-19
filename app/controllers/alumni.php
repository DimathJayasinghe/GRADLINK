<?php
class alumni extends Controller{
    protected $m;
    public function __construct() {
        $this->m = $this->model("M_alumniapprove");
        SessionManager::redirectToAuthIfNotLoggedIn();
    }
    public function index() {
        if (SessionManager::isSpecialAlumni()){
            $this->redirect("/alumni/approve");
        } else if (SessionManager::hasRole('admin')){
            $this->redirect("/admin/verifications");
        }else{
            $this->redirect("/mainfeed");
        }
    }
    public function approve(){
        if (SessionManager::isSpecialAlumni() || SessionManager::hasRole('admin')){
            // emulate Profile controller's pattern for GET params
            $selected_req_id_raw = $this->getQueryParam('req_id', null);
            $selected_req_id = null;
            if ($selected_req_id_raw !== null) {
                $selected_req_id = trim((string)$selected_req_id_raw);
                if ($selected_req_id === '') { $selected_req_id = null; }
            }

            $requests = $this->m->getPendingRequests();
            // Build associative array by req_id for quick lookup in view similar to how fundraise analytics selects target
            $requestsById = [];
            foreach ($requests as $r) {
                $requestsById[(string)$r->req_id] = $r;
            }

            $data = [
                'requests' => $requests,
                'requestsById' => $requestsById,
                'selected_req_id' => $selected_req_id,
            ];
            if (SessionManager::hasRole('admin')){
                $this->view("/admin/v_verifications", $data);
            }
            else if (SessionManager::isSpecialAlumni()){
                $this->view("/alumni_approval/approval_dashboard", $data);
            }
        }else{
            $this->redirect("/mainfeed");
        }
    }

    public function requestDetails(){
        header('Content-Type: application/json');

        if (!(SessionManager::isSpecialAlumni() || SessionManager::hasRole('admin'))) {
            http_response_code(403);
            echo json_encode([
                'success' => false,
                'error' => 'Unauthorized'
            ]);
            return;
        }

        $reqIdRaw = $this->getQueryParam('req_id', null);
        $reqId = is_numeric($reqIdRaw) ? (int)$reqIdRaw : 0;
        if ($reqId <= 0) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'error' => 'Invalid request id'
            ]);
            return;
        }

        $request = $this->m->getRequestById($reqId);
        if (!$request) {
            http_response_code(404);
            echo json_encode([
                'success' => false,
                'error' => 'Request not found'
            ]);
            return;
        }

        $profile = trim((string)($request->profile ?? ''));
        if ($profile === '') {
            $profile = 'default.jpg';
        }

        if (preg_match('/^https?:\/\//i', $profile) || strpos($profile, URLROOT . '/') === 0) {
            $profileUrl = $profile;
        } else {
            $profileUrl = URLROOT . '/media/profile/' . rawurlencode(ltrim($profile, '/'));
        }

        echo json_encode([
            'success' => true,
            'request' => [
                'req_id' => $request->req_id ?? $reqId,
                'name' => $request->Name ?? '',
                'email' => $request->email ?? '',
                'batch' => $request->Batch ?? '',
                'nic' => $request->nic ?? '',
                'student_no' => $request->student_no ?? '',
                'display_name' => $request->display_name ?? '',
                'bio' => $request->bio ?? '',
                'explain_yourself' => $request->explain_yourself ?? '',
                'status' => $request->status ?? 'Pending',
                'profile' => $profile,
                'profile_url' => $profileUrl,
            ]
        ]);
    }
}

?>