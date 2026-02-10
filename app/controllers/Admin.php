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
                // Get role filter from query string (null = all, 'admin', 'alumni', 'undergrad')
                $roleFilter = $_GET['role'] ?? null;
                if ($roleFilter && !in_array($roleFilter, ['admin', 'alumni', 'undergrad'])) {
                    $roleFilter = null;
                }

                // Get metrics and charts based on role filter
                $engagement = $this->adminModel->getEngagementMetricsByRole($roleFilter);
                $metrics = $this->adminModel->getOverviewMetrics();
                $charts = $this->adminModel->getChartDataByRole($roleFilter);
                
                // Get user counts by role for filter display
                $usersByRole = [
                    'all' => $this->adminModel->countUsersByRole(null),
                    'admin' => $this->adminModel->countUsersByRole('admin'),
                    'alumni' => $this->adminModel->countUsersByRole('alumni'),
                    'undergrad' => $this->adminModel->countUsersByRole('undergrad'),
                ];

                // Get location data for map
                $locations = $this->adminModel->getUserLocations($roleFilter);
                $locationSummary = $this->adminModel->getLocationSummary($roleFilter);
                $countries = $this->adminModel->getCountriesWithUsers();
                $batches = $this->adminModel->getBatches();
                $heatmapData = $this->adminModel->getLocationHeatmapData($roleFilter);
                
                $data = [
                    'engagement' => $engagement,
                    'metrics' => $metrics,
                    'charts' => $charts,
                    'activeTab' => 'engagement',
                    'roleFilter' => $roleFilter,
                    'usersByRole' => $usersByRole,
                    'locations' => $locations,
                    'locationSummary' => $locationSummary,
                    'countries' => $countries,
                    'batches' => $batches,
                    'heatmapData' => $heatmapData,
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
            $pendingModel = $this->model('M_alumniapprove');
            $requests = $pendingModel->getPendingRequests();
            $data = [
                'requests' => $requests,
            ];
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

        public function eventrequests(){
            $data = [];
            $this->view('admin/v_eventrequests', $data);
        }

        public function bulkVerifyAlumni() {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                http_response_code(405);
                exit('Method not allowed');
            }

            $ids = $_POST['ids'] ?? [];
            if (!is_array($ids) || empty($ids)) {
                SessionManager::setFlash('error', 'No alumni selected.');
                $this->redirect('/admin/verifications');
                return;
            }

            $signupModel = $this->model('M_signup');
            $successCount = 0;
            $failCount = 0;

            foreach ($ids as $id) {
                $id = is_numeric($id) ? (int)$id : null;
                if (!$id) continue;
                $result = $signupModel->approveAlumni($id);
                if ($result) {
                    $successCount++;
                } else {
                    $failCount++;
                }
            }

            if ($successCount > 0) {
                SessionManager::setFlash('success', "Verified $successCount alumni successfully.");
            }
            if ($failCount > 0) {
                SessionManager::setFlash('warning', "$failCount alumni could not be verified.");
            }
            $this->redirect('/admin/verifications');
        }

        public function bulkRejectAlumni() {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                http_response_code(405);
                exit('Method not allowed');
            }

            $ids = $_POST['ids'] ?? [];
            if (!is_array($ids) || empty($ids)) {
                SessionManager::setFlash('error', 'No alumni selected.');
                $this->redirect('/admin/verifications');
                return;
            }

            $signupModel = $this->model('M_signup');
            $successCount = 0;
            $failCount = 0;

            foreach ($ids as $id) {
                $id = is_numeric($id) ? (int)$id : null;
                if (!$id) continue;
                $result = $signupModel->rejectPendingAlumni($id);
                if ($result) {
                    $successCount++;
                } else {
                    $failCount++;
                }
            }

            if ($successCount > 0) {
                SessionManager::setFlash('warning', "Rejected $successCount alumni.");
            }
            if ($failCount > 0) {
                SessionManager::setFlash('error', "$failCount alumni could not be rejected.");
            }
            $this->redirect('/admin/verifications');
        }    }
?>