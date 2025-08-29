<?php
class messages extends Controller{
    protected $message_model;
    public function __construct() {
        SessionManager::redirectToAuthIfNotLoggedIn();
        $this->message_model = $this->model('M_message');
    }
    public function index(){

    }
}
?>