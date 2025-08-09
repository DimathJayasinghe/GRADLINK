<?php
    class Users extends Controller {
        protected $usersModel;
        public function __construct() {
            $this->usersModel = $this->model('M_users');
        }
        public function index(){
            $users = $this->usersModel->getUsers();
            $data = [
                'users' => $users
            ];
            $this->view('v_about', $data);
        }

        public function signin(){
            $data = [];
            $this->view('users/v_signin', $data);
        }
    }
?>