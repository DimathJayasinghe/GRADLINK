<?php
class Profile extends Controller{
    Protected $undergradModel;
    public function __construct() {
        $this->undergradModel = $this->model('M_Undergrad');
    }
    public function index() {
        $data = [];
        $this->view('_404', $data);
    }

    public function undergraduateProfile() {
        $data = [];
        $this->view('Undergraduate/v_profile', $data);
    }
}
?>