<?php
class AdminLogin extends Controller {
    protected $adminModel;

    public function __construct() {
        $this->adminModel = $this->model('M_admin');
    }

    public function index() {
        // If already logged in as admin, redirect to dashboard
        if (SessionManager::isLoggedIn() && SessionManager::hasRole('admin')) {
            header('Location: ' . URLROOT . '/admin/dashboard');
            exit();
        }

        // Handle login form submission
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $this->handleLogin();
        } else {
            // Show login form
            $this->view('admin/v_admin_login');
        }
    }

    private function handleLogin() {
        $email = trim($_POST['email']);
        $password = $_POST['password'];

        // Validate input
        if (empty($email) || empty($password)) {
            SessionManager::setFlash('error', 'Please fill in all fields');
            $this->view('admin/v_admin_login');
            return;
        }

        // Sanitize email
        $email = filter_var($email, FILTER_SANITIZE_EMAIL);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            SessionManager::setFlash('error', 'Please enter a valid email address');
            $this->view('admin/v_admin_login');
            return;
        }

        // Attempt admin login
        $admin = $this->adminModel->authenticateAdmin($email, $password);
        
        if ($admin) {
            // Create admin session
            SessionManager::createUserSession($admin);
            SessionManager::setFlash('success', 'Welcome back, Admin!');
            
            // Redirect to admin dashboard
            header('Location: ' . URLROOT . '/admin');
            exit();
        } else {
            SessionManager::setFlash('error', 'Invalid admin credentials');
            $this->view('admin/v_admin_login');
        }
    }

    public function logout() {
        SessionManager::destroySession();
        SessionManager::setFlash('success', 'You have been logged out successfully');
        header('Location: ' . URLROOT . '/adminlogin');
        exit();
    }
}
?>
