<?php
    class Hero extends Controller{
        protected $pagesModel;
        public function __construct() {
            $this->pagesModel = $this->model('M_hero');
        }

        public function index() {
            // View the hero page
            $data = [];
            $this->view('v_hero', $data);
        }

        public function learnmore(){

        }

        public function termsofservice(){

        }

        public function privacypolicy(){

        }

        public function support(){

        }

        public function contactus(){
            
        }
    }
?>