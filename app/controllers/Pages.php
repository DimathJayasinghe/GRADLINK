<?php
    class Pages extends Controller{
        protected $pagesModel;
        public function __construct() {
            $this->pagesModel = $this->model('M_pages');
        }

        public function index() {
            // View the hero page
            $data = [];
            $this->view('v_hero', $data);
        }

        public function about() {
            $users = $this->pagesModel->getUsers();
            $data = [
                'users' => $users
            ];
            $this->view('v_about',$data);
        }   
    }
?>