<!-- Default controller -->
<?php
    class Pages extends Controller{
        public function __construct() {
        }

        public function index() {
        }

        public function about($name,$age) {
            $data = [
                'username' => $name,
                'userage' => $age
            ];
            $this->view('v_about',$data);
        }   
    }
?>