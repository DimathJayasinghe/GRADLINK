<?php
    class Admin extends Controller {
        protected $adminModel;

        public function __construct() {
            SessionManager::requireRole('admin');
            $this->adminModel = $this->model('M_admin');
        }

        public function index() {
            $metrics = $this->adminModel->getOverviewMetrics();
            $charts = $this->adminModel->getChartData();
            $data = [
                'metrics' => $metrics,
                'charts' => $charts,
                'activeTab' => 'overview',
            ];
            $this->view('admin/v_dashboard', $data);
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


