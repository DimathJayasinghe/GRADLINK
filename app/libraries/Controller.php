<?php
    class Controller{
        // To load the model
        public function model($model){
            require_once '../app/models/' . $model . '.php';
            
            // Instantiate the model and pass it to the controller member variable
            return new $model();
        }

        // To load the view
        public function view($view, $data =[]){
            if(file_exists('../app/views/' . $view . '.php')){
                require_once '../app/views/' . $view . '.php';
            } else {
                // If the view does not exist, throw an error
                die('View does not exist: ' . $view);
            }
        }

        public function redirect($to){
            // Redirect to auth page after logout
            header('Location: ' . URLROOT . $to);
            exit();
        }
        
        // Get query parameters (GET parameters excluding 'url')
        public function getQueryParam($key = null, $default = null) {
            // Get all GET parameters except 'url'
            $params = $_GET;
            if(isset($params['url'])) {
                unset($params['url']);
            }
            
            // If a specific key is requested
            if($key !== null) {
                return isset($params[$key]) ? $params[$key] : $default;
            }
            
            // Return all query parameters
            return $params;
        }
    }
?>