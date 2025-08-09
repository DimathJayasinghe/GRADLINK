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
    }
?>