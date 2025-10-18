<?php
class alumni extends Controller{
    protected $m;
    public function __construct() {
        $m = $this->model("M_alumniapprove");
        SessionManager::redirectToAuthIfNotLoggedIn();
    }
    public function index() {
        if (SessionManager::isSpecialAlumni()){
            $this->redirect("/alumni/approve");
        }else{
            $this->redirect("/mainfeed");
        }
    }
    public function approve(){
        if (SessionManager::isSpecialAlumni()){
            $data = [];
            $this->view("/alumni_approval/approval_dashboard",$data);
        }else{
            $this->redirect("/mainfeed");
        }
    }
}

?>