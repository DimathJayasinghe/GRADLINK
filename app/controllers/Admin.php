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
            $this->view('admin/v_overview', $data);
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

        public function reports() {
            $data = [];
            $this->view('admin/v_reports', $data);
        }
        public function posts() {
            $data = [];
            $this->view('admin/v_posts', $data);
        }
        public function fundraisers() {
            $data = [];
            $this->view('admin/v_fundraiser', $data);
        }
        public function verifications() {
            $data = [];
            $this->view('admin/v_verifications', $data);
        }

            // POST: Approve a pending alumni signup
            public function approvePendingAlumni() {
                // Ensure admin auth via constructor
                if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                    header('Location: ' . URLROOT . '/admin/verifications');
                    exit();
                }

                $pendingId = $_POST['pending_id'] ?? null;
                $pendingId = is_numeric($pendingId) ? (int)$pendingId : null;
                if (!$pendingId) {
                    SessionManager::setFlash('error', 'Invalid approval request.');
                    header('Location: ' . URLROOT . '/admin/verifications');
                    exit();
                }

                // Use signup model to approve
                $signupModel = $this->model('M_signup');
                $newUserId = $signupModel->approveAlumni($pendingId);
                if ($newUserId) {
                    SessionManager::setFlash('success', 'Alumni approved and activated (User ID: ' . $newUserId . ').');
                } else {
                    SessionManager::setFlash('error', 'Approval failed. Please try again.');
                }
                header('Location: ' . URLROOT . '/admin/verifications');
                exit();
            }
    }
?>


