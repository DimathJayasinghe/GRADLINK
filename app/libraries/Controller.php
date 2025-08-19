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
    }
?>