<?php
    class Controller{
        use Notifiable;
        protected $notificationModel = null;
        public function __construct(){
            // initialize the notification model safely
            try {
                $this->notificationModel = $this->model('M_notification');
            } catch (Throwable $e) {
                error_log('Failed to load M_notification model: ' . $e->getMessage());
                $this->notificationModel = null; // degrade gracefully
            }
        }

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

        protected function canNotify(): bool {
            return $this->notificationModel !== null && method_exists($this->notificationModel, 'createNotification');
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

        /**
         * Helpers to identify AJAX requests
         */
        public function isAjaxRequest(): bool {
            return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 
                   strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
        }

        public function requireAjaxRequest(): void {
            if (!$this->isAjaxRequest()) {
                http_response_code(400);
                die('Bad Request: This endpoint requires an AJAX request.');
            }
        }
    }
?>