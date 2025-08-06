<?php
    class Core {
        // URL format --> /controller/method/params
        protected $currentContoller ="Pages";
        protected $currentMethod = "index";
        protected $params = [];

        public function __construct() {
            // print_r($this->getUrl());

            $url = $this->getUrl();
            // Check if controller exists
            if(file_exists('../app/controllers/'.ucwords($url[0]).'.php')) {
                // If exists, set as current controller
                $this->currentContoller = ucwords($url[0]);
                
                // Unset the controller from the URL
                unset($url[0]);

                // Call the controller
                require_once '../app/controllers/' . $this->currentContoller . '.php';

                // Instantiate the controller class
                $this->currentContoller = new $this->currentContoller;
            }
        }

        public function getUrl() {
            if (isset($_GET['url'])){
                $url = rtrim($_GET['url'], '/');
                $url = filter_var($url, FILTER_SANITIZE_URL);
                $url = explode('/', $url);

                return $url;
            }
        }
    }
?>