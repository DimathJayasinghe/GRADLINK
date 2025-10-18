<?php
class calender extends Controller{
    public function __construct() {
        SessionManager::redirectToAuthIfNotLoggedIn();
    }
    public function index() {
        $this->view("/calender/v_calender");
    }
}
?>