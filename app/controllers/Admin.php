<?php
    class Admin extends Controller {
        protected $adminModel;

            public function __construct() {
        // Check if user is logged in and has admin role
        if (!SessionManager::isLoggedIn() || !SessionManager::hasRole('admin')) {
            // Redirect to admin login if not authenticated
            header('Location: ' . URLROOT . '/adminlogin');
            exit();
        }
        $this->adminModel = $this->model('M_admin');
    }

    public function dashboard() {
        // Redirect to index method for dashboard
        $this->index();
    }

        public function index() {
            $metrics = $this->adminModel->getOverviewMetrics();
            $detailed = $this->adminModel->getDetailedOverview();
            $activity = $this->adminModel->getRecentActivity();
            $users = $this->adminModel->getAllUsers();
            $engagement = $this->adminModel->getEngagementMetrics();
            $data = [
                'metrics' => $metrics,
                'detailed' => $detailed,
                'activity' => $activity,
                'users' => $users,
                'engagement' => $engagement,
            ];
            $this->view('admin/v_admin_dashboard', $data);
        }

        public function users() {
            $users = $this->adminModel->getAllUsers();
            $data = [
                'users' => $users,
                'activeTab' => 'users',
            ];
            $this->view('admin/v_users', $data);
        }

        public function engagement() {
            $engagement = $this->adminModel->getEngagementMetrics();
            $data = [
                'engagement' => $engagement,
                'activeTab' => 'engagement',
            ];
            $this->view('admin/v_engagement', $data);
        }
    }
?>


