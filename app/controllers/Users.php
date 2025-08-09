<?php
    class Users extends Controller {
        protected $usersModel;
        
        public function __construct() {
            $this->usersModel = $this->model('M_users');
            // Start session if not already started
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }
            
            // Check for remembered user on page load
            $this->checkRememberedUser();
        }
        
        public function index(){
            $users = $this->usersModel->getUsers();
            $data = ['users' => $users];
            $this->view('v_about', $data);
        }

        private function redirectIfLoggedIn() {
            if (isset($_SESSION['user_id'])) {
                header('Location: ' . URLROOT . '/mainfeed');
                exit();
            }
        }

        public function signup(){
            $this->redirectIfLoggedIn();
            
            $data = [];
            $this->view('users/v_signup', $data);
        }
        
        public function login(){
            $this->redirectIfLoggedIn();
            
            $data = [];
            $this->view('users/v_login', $data);
        }
        
        public function logout(){
            // Clear session
            session_destroy();
            
            // Clear remember me cookie
            Cookie::delete('remember_token');
            
            header('Location: ' . URLROOT . '/users/login');
            exit();
        }
        
        // Separate methods for each form submission
        public function signupSubmit(){
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $this->handleSignup();
            } else {
                header('Location: ' . URLROOT . '/users/signup');
            }
        }
        
        public function loginSubmit(){
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $this->handleLogin();
            } else {
                header('Location: ' . URLROOT . '/users/login');
            }
        }

        private function handleSignup(){
            // Sanitize input data
                $_POST = Sanitizer::sanitizeArray($_POST);
                $data = [
                    'name' => trim($_POST['name']),
                    'password' => trim($_POST['password']),
                    'confirm_password' => trim($_POST['confirm_password']),
                    'email' => trim($_POST['email']),
                    'errors' => []
                ];

                // Validate input
                if (empty($data['name'])) {
                    $data['errors'][] = 'Username is required';
                }
                if (empty($data['password'])) {
                    $data['errors'][] = 'Password is required';
                }
                if ($data['password'] !== $data['confirm_password']) {
                    $data['errors'][] = 'Passwords do not match';
                }
                if (empty($data['email'])) {
                    $data['errors'][] = 'Email is required';
                } else {
                    // Check if email already exists
                    if ($this->usersModel->findUserByEmail($data['email'])) {
                        $data['errors'][] = 'Email is already taken';
                    }
                }

                // If no errors, proceed to save user
                if (empty($data['errors'])) {
                    try {
                        if ($this->usersModel->registerUser($data)) {
                            header('Location: ' . URLROOT . '/users/login');
                            exit();
                        } else {
                            $data['errors'][] = 'Registration failed, please try again.';
                        }
                    } catch (PDOException $e) {
                        // Handle specific PDO exceptions
                        if ($e->getCode() == 23000) { // Integrity constraint violation
                            $data['errors'][] = 'Email address is already registered';
                        } else {
                            $data['errors'][] = 'Database error occurred. Please try again.';
                        }
                    } catch (Exception $e) {
                        $data['errors'][] = 'An unexpected error occurred: ' . $e->getMessage();
                    }
                }

                // Load view with errors
                $this->view('users/v_signup', $data);
        }

        private function handleLogin(){
            $_POST = Sanitizer::sanitizeArray($_POST);
            $data = [
                'email' => $_POST['email'] ?? '',
                'password' => $_POST['password'] ?? '',
                'remember_me' => isset($_POST['remember_me']),
                'errors' => []
            ];

            // Validate input
            if (Sanitizer::isEmpty($data['email'])) {
                $data['errors'][] = 'Email is required';
            }
            if (Sanitizer::isEmpty($data['password'])) {
                $data['errors'][] = 'Password is required';
            }

            // If no errors, attempt login
            if (empty($data['errors'])) {
                $user = $this->usersModel->login($data['email'], $data['password']);
                
                if ($user) {
                    $this->createUserSession($user);
                    
                    // Handle remember me
                    if ($data['remember_me']) {
                        $this->setRememberMeCookie($user);
                    }
                    
                    header('Location: ' . URLROOT . '/mainfeed');
                    exit();
                } else {
                    $data['errors'][] = 'Invalid email or password';
                }
            }
            
            // Load view with errors
            $this->view('users/v_login', $data);
        }

        private function createUserSession($user) {
            $_SESSION['user_id'] = $user->id;
            $_SESSION['user_name'] = $user->name;
            $_SESSION['user_email'] = $user->email;
        }

        private function setRememberMeCookie($user) {
            // Generate a unique token
            $token = bin2hex(random_bytes(32));
            
            // Store token in database (you'll need to add this method to M_users)
            $this->usersModel->saveRememberToken($user->id, hash('sha256', $token));
            
            // Set secure cookie for 30 days
            Cookie::setSecure('remember_token', $user->id . ':' . $token, 30 * 24 * 3600);
        }

        private function checkRememberedUser() {
            // Skip if user is already logged in
            if (isset($_SESSION['user_id'])) {
                return;
            }

            // Skip check for login/signup related pages to avoid redirect loops
            $currentUrl = $_GET['url'] ?? '';
            if (strpos($currentUrl, 'users/') === 0) {
                return;
            }

            // Check for remember me cookie
            if (Cookie::exists('remember_token')) {
                $cookieValue = Cookie::get('remember_token');
                
                if ($cookieValue && strpos($cookieValue, ':') !== false) {
                    list($userId, $token) = explode(':', $cookieValue, 2);
                    
                    // Verify token with database
                    $user = $this->usersModel->verifyRememberToken($userId, hash('sha256', $token));
                    
                    if ($user) {
                        $this->createUserSession($user);
                        
                        // Regenerate token for security
                        $this->setRememberMeCookie($user);
                        
                        // Redirect to mainfeed for auto-login
                        header('Location: ' . URLROOT . '/mainfeed');
                        exit();
                    } else {
                        // Invalid token, delete cookie
                        Cookie::delete('remember_token');
                    }
                }
            }
        }
    }
?>