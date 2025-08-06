<?php
    class Core {
        // URL format --> /controller/method/params
        protected $currentContoller ="Pages";
        protected $currentMethod = "index";
        protected $params = [];

        public function __construct() {
            $this->getUrl();
        }

        public function getUrl() {
            echo $_GET['url'];
        }
    }
?>