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
}

?>