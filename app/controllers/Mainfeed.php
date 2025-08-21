<?php
class Mainfeed extends Controller {
    protected $pagesModel;

    public function __construct() {
        SessionManager::redirectToAuthIfNotLoggedIn();
        $this->pagesModel = $this->model('M_mainfeed');
    }

    public function index() {
        // View the main feed page
        SessionManager::redirectToAuthIfNotLoggedIn();
        $data = [];
        $this->view('v_mainfeed', $data);
    }

}
?>