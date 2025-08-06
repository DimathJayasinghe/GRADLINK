<?php
    class Users extends Controller {
        public function __construct() {
            
        }

        public function register(){
            $data = [];
            $this->view('users/v_register', $data);
        }
    }
?>