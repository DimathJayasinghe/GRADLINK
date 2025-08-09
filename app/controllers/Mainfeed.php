<?php
class Mainfeed extends Controller {
    protected $pagesModel;

    public function __construct() {
        $this->pagesModel = $this->model('M_mainfeed');
    }

    public function index() {
        // View the main feed page
        $data = [];
        $this->view('v_mainfeed', $data);
    }
    public function getPosts(){
        // Fetch posts for the main feed
        $posts = $this->pagesModel->getPosts();
        $data = ['posts' => $posts];
        $this->view('v_mainfeed_posts', $data);
    }
}
?>