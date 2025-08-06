<!-- Default controller -->
<?php
    class Pages extends Controller{
        public function __construct() {
        }

        public function index() {
        }

        public function about() {
            $this->view('v_about');
        }   
    }
?>