<?php
    class Core {
        // URL format --> /controller/method/params
        protected $currentContoller ="Hero"; // Default controller
        protected $currentMethod = "index";
        protected $params = [];

        public function __construct() {
            // print_r($this->getUrl());

            $url = $this->getUrl();
            
            // Check if URL exists and has controller
            if($url && isset($url[0])) {
                // Check if controller exists
                if(file_exists('../app/controllers/'.ucwords($url[0]).'.php')) {
                    // If exists, set as current controller
                    $this->currentContoller = ucwords($url[0]);
                    
                    // Unset the controller from the URL
                    unset($url[0]);
                } else {
                    $this->show404();
                    return; // Stop further execution if controller not found
                }
            }

            // Call the controller
            require_once '../app/controllers/' . $this->currentContoller . '.php';

            // Instantiate the controller class
            $this->currentContoller = new $this->currentContoller;

            // Check whether the method exists in the controller or not
            if ($url && isset($url[1])){
                if (method_exists($this->currentContoller, $url[1])) {
                    // If method exists, set as current method
                    $this->currentMethod = $url[1];

                    // Unset the method from the URL
                    unset($url[1]);
                } else {
                    // Method not found, show 404
                    $this->show404();
                    return;
                }
            }

            // Get the parameters
            $this->params = $url ? array_values($url) : [];

            // Call the current method with the parameters
            call_user_func_array([$this->currentContoller, $this->currentMethod], $this->params);
        }


        public function getUrl() {
            if (isset($_GET['url'])){
                $url = rtrim($_GET['url'], '/');
                $url = filter_var($url, FILTER_SANITIZE_URL);
                $url = explode('/', $url);

                return $url;
            }
            return null; // Explicitly return null when no URL parameter
        }
        private function show404() {
            // Set HTTP 404 status code
            http_response_code(404);
            
            // Check if 404 view file exists
            if(file_exists('../app/views/errors/_404.php')) {
                require_once '../app/views/errors/_404.php';
            } else {
                // Fallback if 404 view doesn't exist
                echo "<h1>404 - Page Not Found</h1>";
                echo "<p>The page you are looking for could not be found.</p>";
            }
            
            // Stop execution
            exit();
        }
    }
?>