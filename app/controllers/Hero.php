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
            // Terms & Conditions page
            $data = [];
            $this->view('static_pages/v_termsAndConditions', $data);
        }

        public function privacypolicy(){
            // Privacy Policy page
            $data = [];
            $this->view('static_pages/v_privacyAndPolicy', $data);
        }

        public function support(){
            // Support page
            $data = [];
            $this->view('static_pages/v_support', $data);
        }

        public function contactus(){
            // Contact Us page
            $data = [];
            $this->view('static_pages/v_contactus', $data);
        }
    }
?>