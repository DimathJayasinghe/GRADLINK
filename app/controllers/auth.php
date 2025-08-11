<?php
    class auth extends Controller{
        protected $authModel;
        // Constructor to initialize the model and session
        public function __construct(){
            $this->authModel = $this->model('M_auth');
            // Start session if not already started
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }
            
            // Check for remembered user on page load
            $this->checkRememberedUser();
        }
        public function index(){
            // Redirect to main feed if already logged in
            if (isset($_SESSION['user_id'])) {
                header('Location: ' . URLROOT . '/mainfeed');
                exit();
            }
            $data = [];
            // Load _404 error view if no specific action is defined
            $this->view('errors/_404', $data);
        }

        /////////////////////////////////////////////////////////////
        private function checkRememberedUser(){
            // Skip if user already logged in
            if (isset($_SESSION['user_id'])) {
                return;
            }

            // skip check for login/signup related pages to avoid redirect loops
            $currentUrl = $_GET['url'] ?? '';
            if (strpos($currentUrl, 'users/login') !== false || strpos($currentUrl, 'users/signup') !== false) {
                return;
            }

            // Check if remember me cookie exists
            if (Cookie::exists('remember_token')) {
                $rememberToken = Cookie::get('remember_token');
                if ($rememberToken && strpos($rememberToken, ':') !== false){
                    list($userId, $token) = explode(':', $rememberToken);
                    // Validate the remember token
                    $user = $this->authModel->verifyRememberToken($userId,hash('sha256', $token));
                    if ($user) {
                        $this->createUserSession($user);
                        // Regenerate token for security
                        $this->setRememberMeCookie($user);
                        // Redirect to main feed
                        header('Location: ' . URLROOT . '/mainfeed');
                        exit();
                    } else {
                        // If token is invalid, delete the cookie
                        Cookie::delete('remember_token');
                    }
                }
            }
        }

        private function createUserSession($user) {
            $_SESSION['user_id'] = $user->id;
            $_SESSION['user_name'] = $user->name;
            $_SESSION['user_email'] = $user->email;
        }

        private function setRememberMeCookie($user) {
            // Generate a unique token
            $token = bin2hex(random_bytes(32));
            
            // Store token in database (you'll need to add this method to M_auth)
            $this->authModel->saveRememberToken($user->id, hash('sha256', $token));
            
            // Set secure cookie for 30 days
            Cookie::setSecure('remember_token', $user->id . ':' . $token, 30 * 24 * 3600);
        }

        private function redirectIfLoggedIn() {
            // Redirect to main feed if user is already logged in
            if (isset($_SESSION['user_id'])) {
                header('Location: ' . URLROOT . '/mainfeed');
                exit();
            }
        }

        /////////////////////////////////////////////////////////////
        // LOGIN HANDLERS
        // Login method > for load views
        public function login() {
            // Redirect to main feed if already logged in
            $this->redirectIfLoggedIn();

            $data = [];
            // Load login view
            $this->view('auth/v_login', $data);
        }

        // Login handler method > for processing login
        public function loginHandler(){
            if ($_SERVER['REQUEST_METHOD'] === 'POST'){
                $_POST = Sanitizer::sanitizeArray($_POST);

                $allowedRoles = ['undergrad','alumni'];
                $role = $_POST['role'] ?? '';

                $data = [
                    'userRole'   => $role,
                    'email'      => $_POST['email'] ?? '',
                    'password'   => $_POST['password'] ?? '',
                    'remember_me'=> isset($_POST['remember_me']),
                    'errors'     => []
                ];

                // Validate input
                if (Sanitizer::isEmpty($data['userRole'])) {
                    $data['errors'][] = 'Role is required';
                } elseif (!in_array($data['userRole'], $allowedRoles, true)) {
                    $data['errors'][] = 'Invalid user role';
                }
                if (Sanitizer::isEmpty($data['email'])) {
                    $data['errors'][] = 'Email is required';
                }
                if (Sanitizer::isEmpty($data['password'])) {
                    $data['errors'][] = 'Password is required';
                }

                // If no errors, attempt login
                if (empty($data['errors'])) {
                    $user = $this->authModel->login($data['email'], $data['password']);
                    
                    if ($user) {
                        $this->createUserSession($user);
                        
                        // Handle remember me
                        if ($data['remember_me']) {
                            $this->setRememberMeCookie($user);
                        }
                        
                        // Redirect to main feed if login successful
                        header('Location: ' . URLROOT . '/mainfeed');
                        exit();
                    } else {
                        $data['errors'][] = 'Invalid email or password';
                    }
                }

                // Load view with errors
                $this->view('auth/v_login', $data);

            }else{
                // Redirect to login page if not a POST request
                header('Location: ' . URLROOT . '/auth/login');
                exit();
            }
        }
        /////////////////////////////////////////////////////////////

        public function logout(){
            // Clear session data
            session_unset();
            session_destroy();

            // Delete remember me cookie if it exists
            Cookie::delete('remember_token');

            // Redirect to login page after logout
            header('Location: ' . URLROOT . '/auth/login');
            exit();
        }
    }
?>