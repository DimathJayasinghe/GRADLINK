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
            
            // Online users and activity monitoring
            $onlineUsers = $this->adminModel->getOnlineUsers();
            $onlineCount = count($onlineUsers);
            $accessStats = $this->adminModel->getAccessLogStats();
            $recentLogs = $this->adminModel->getRecentAccessLogs(20);
            
            $data = [
                'metrics' => $metrics,
                'detailed' => $detailed,
                'activity' => $activity,
                'users' => $users,
                'engagement' => $engagement,
                'online_users' => $onlineUsers,
                'online_count' => $onlineCount,
                'access_stats' => $accessStats,
                'recent_logs' => $recentLogs,
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
                $metrics = $this->adminModel->getOverviewMetrics();
                $charts = $this->adminModel->getChartData();
                $data = [
                    'engagement' => $engagement,
                    'metrics' => $metrics,
                    'charts' => $charts,
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
            $stats = $this->adminModel->getFundraiserStats();
            $fundraisers = $this->adminModel->getAllFundraisersForAdmin();
            $clubs = $this->adminModel->getUniqueClubs();
            
            $data = [
                'stats' => $stats,
                'fundraisers' => $fundraisers,
                'clubs' => $clubs,
                'activeTab' => 'fundraisers'
            ];
            $this->view('admin/v_fundraiser', $data);
        }

        /**
         * View full fundraiser details
         */
        public function fundraiserDetails($id = null) {
            if (!$id) {
                $this->redirect('/admin/fundraisers');
                return;
            }
            
            $fundraiser = $this->adminModel->getFundraiserFullDetails($id);
            
            if (!$fundraiser) {
                SessionManager::setFlash('error', 'Fundraiser not found.');
                $this->redirect('/admin/fundraisers');
                return;
            }
            
            $data = [
                'fundraiser' => $fundraiser,
                'activeTab' => 'fundraisers'
            ];
            $this->view('admin/v_fundraiser_detail', $data);
        }

        /**
         * Approve a fundraiser request
         */
        public function approveFundraiser() {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                $this->redirect('/admin/fundraisers');
                return;
            }

            $id = $_POST['id'] ?? null;
            if (!$id) {
                SessionManager::setFlash('error', 'Invalid fundraiser ID.');
                $this->redirect('/admin/fundraisers');
                return;
            }

            if ($this->adminModel->approveFundraiser($id)) {
                SessionManager::setFlash('success', 'Fundraiser approved successfully.');
            } else {
                SessionManager::setFlash('error', 'Failed to approve fundraiser.');
            }
            
            // Return to detail page if came from there
            $returnTo = $_POST['return_to'] ?? 'list';
            if ($returnTo === 'detail') {
                $this->redirect('/admin/fundraiserDetails/' . $id);
            } else {
                $this->redirect('/admin/fundraisers');
            }
        }

        /**
         * Reject a fundraiser request with reason
         */
        public function rejectFundraiser() {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                $this->redirect('/admin/fundraisers');
                return;
            }

            $id = $_POST['id'] ?? null;
            $reason = trim($_POST['reason'] ?? '');

            if (!$id) {
                SessionManager::setFlash('error', 'Invalid fundraiser ID.');
                $this->redirect('/admin/fundraisers');
                return;
            }

            if (empty($reason)) {
                SessionManager::setFlash('error', 'Please provide a rejection reason.');
                $this->redirect('/admin/fundraiserDetails/' . $id);
                return;
            }

            if ($this->adminModel->rejectFundraiser($id, $reason)) {
                SessionManager::setFlash('success', 'Fundraiser rejected.');
            } else {
                SessionManager::setFlash('error', 'Failed to reject fundraiser.');
            }
            
            $returnTo = $_POST['return_to'] ?? 'list';
            if ($returnTo === 'detail') {
                $this->redirect('/admin/fundraiserDetails/' . $id);
            } else {
                $this->redirect('/admin/fundraisers');
            }
        }

        /**
         * Hold/Pause a fundraiser campaign
         */
        public function holdFundraiser() {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                $this->redirect('/admin/fundraisers');
                return;
            }

            $id = $_POST['id'] ?? null;
            if (!$id) {
                SessionManager::setFlash('error', 'Invalid fundraiser ID.');
                $this->redirect('/admin/fundraisers');
                return;
            }

            if ($this->adminModel->holdFundraiser($id)) {
                SessionManager::setFlash('warning', 'Fundraiser has been put on hold.');
            } else {
                SessionManager::setFlash('error', 'Failed to hold fundraiser.');
            }
            
            $returnTo = $_POST['return_to'] ?? 'list';
            if ($returnTo === 'detail') {
                $this->redirect('/admin/fundraiserDetails/' . $id);
            } else {
                $this->redirect('/admin/fundraisers');
            }
        }

        /**
         * Remove/Delete a fundraiser
         */
        public function removeFundraiser() {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                $this->redirect('/admin/fundraisers');
                return;
            }

            $id = $_POST['id'] ?? null;
            if (!$id) {
                SessionManager::setFlash('error', 'Invalid fundraiser ID.');
                $this->redirect('/admin/fundraisers');
                return;
            }

            if ($this->adminModel->removeFundraiser($id)) {
                SessionManager::setFlash('success', 'Fundraiser removed successfully.');
            } else {
                SessionManager::setFlash('error', 'Failed to remove fundraiser.');
            }
            
            $this->redirect('/admin/fundraisers');
        }

        /**
         * Mark a fundraiser as completed
         */
        public function completeFundraiser() {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                $this->redirect('/admin/fundraisers');
                return;
            }

            $id = $_POST['id'] ?? null;
            if (!$id) {
                SessionManager::setFlash('error', 'Invalid fundraiser ID.');
                $this->redirect('/admin/fundraisers');
                return;
            }

            if ($this->adminModel->completeFundraiser($id)) {
                SessionManager::setFlash('success', 'Fundraiser marked as completed.');
            } else {
                SessionManager::setFlash('error', 'Failed to mark fundraiser as completed.');
            }
            
            $returnTo = $_POST['return_to'] ?? 'list';
            if ($returnTo === 'detail') {
                $this->redirect('/admin/fundraiserDetails/' . $id);
            } else {
                $this->redirect('/admin/fundraisers');
            }
        }

        /**
         * Create a new fundraiser as admin
         */
        public function createFundraiser() {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $errors = [];

                // Validate required fields
                if (empty($_POST['title'])) $errors[] = 'Title is required';
                if (empty($_POST['description'])) $errors[] = 'Description is required';
                if (empty($_POST['club_name'])) $errors[] = 'Club/Organization name is required';
                if (empty($_POST['target_amount']) || !is_numeric($_POST['target_amount'])) $errors[] = 'Valid target amount is required';
                if (empty($_POST['start_date'])) $errors[] = 'Start date is required';
                if (empty($_POST['end_date'])) $errors[] = 'End date is required';
                if (empty($_POST['bank_name'])) $errors[] = 'Bank name is required';
                if (empty($_POST['account_number'])) $errors[] = 'Account number is required';
                if (empty($_POST['branch'])) $errors[] = 'Branch is required';
                if (empty($_POST['account_holder'])) $errors[] = 'Account holder name is required';

                if (!empty($errors)) {
                    SessionManager::setFlash('error', implode(', ', $errors));
                    $this->redirect('/admin/fundraisers');
                    return;
                }

                $data = [
                    'user_id' => $_SESSION['user_id'], // Admin creates on their behalf
                    'club_name' => $_POST['club_name'],
                    'position' => $_POST['position'] ?? 'Administrator',
                    'phone' => $_POST['phone'] ?? '',
                    'title' => $_POST['title'],
                    'headline' => $_POST['headline'] ?? $_POST['title'],
                    'description' => $_POST['description'],
                    'target_amount' => $_POST['target_amount'],
                    'objective' => $_POST['objective'] ?? $_POST['description'],
                    'start_date' => $_POST['start_date'],
                    'end_date' => $_POST['end_date'],
                    'fund_manager' => $_POST['fund_manager'] ?? 'Admin',
                    'fund_manager_contact' => $_POST['fund_manager_contact'] ?? '',
                    'bank_name' => $_POST['bank_name'],
                    'account_number' => $_POST['account_number'],
                    'branch' => $_POST['branch'],
                    'account_holder' => $_POST['account_holder'],
                    'project_poster' => null
                ];

                // Handle file upload if present
                if (!empty($_FILES['project_poster']['tmp_name'])) {
                    $mediaHandler = new MediaFilesHandler();
                    $ext = pathinfo($_FILES['project_poster']['name'], PATHINFO_EXTENSION);
                    $desiredName = 'admin_fund_' . microtime(true) . '_' . bin2hex(random_bytes(4)) . ($ext ? '.' . $ext : '');
                    $upload = $mediaHandler->save($_FILES['project_poster']['tmp_name'], 'fundraisers', $desiredName);
                    // Use the filename from the upload result, not the whole array
                    if ($upload['success']) {
                        $data['project_poster'] = $upload['filename'];
                    }
                }

                $result = $this->adminModel->createAdminFundraiser($data);
                
                if ($result) {
                    SessionManager::setFlash('success', 'Fundraiser created successfully!');
                } else {
                    SessionManager::setFlash('error', 'Failed to create fundraiser.');
                }
                
                $this->redirect('/admin/fundraisers');
            } else {
                // GET request - show create form (handled via modal in main view)
                $this->redirect('/admin/fundraisers');
            }
        }

        /**
         * API endpoint for searching fundraisers
         */
        public function searchFundraisersApi() {
            header('Content-Type: application/json');
            
            $query = $_GET['q'] ?? '';
            $status = $_GET['status'] ?? '';
            $club = $_GET['club'] ?? '';
            
            $fundraisers = $this->adminModel->searchFundraisers($query, $status, $club);
            
            // Calculate additional fields
            $processed = [];
            foreach ($fundraisers as $f) {
                $percentage = $f->target_amount > 0 ? round(($f->raised_amount / $f->target_amount) * 100, 1) : 0;
                $daysLeft = null;
                
                if ($f->deadline) {
                    $now = new DateTime();
                    $deadline = new DateTime($f->deadline);
                    if ($deadline > $now) {
                        $interval = $now->diff($deadline);
                        $daysLeft = $interval->days;
                    }
                }
                
                $processed[] = [
                    'req_id' => $f->req_id,
                    'title' => $f->title,
                    'club_name' => $f->club_name,
                    'target_amount' => $f->target_amount,
                    'raised_amount' => $f->raised_amount,
                    'percentage' => $percentage,
                    'deadline' => $f->deadline,
                    'days_left' => $daysLeft,
                    'status' => $f->status,
                    'created_at' => $f->created_at,
                    'user_name' => $f->user_name ?? $f->display_name
                ];
            }
            
            echo json_encode([
                'success' => true,
                'data' => $processed,
                'count' => count($processed)
            ]);
            exit;
        }

        /**
         * Bulk approve fundraisers
         */
        public function bulkApproveFundraisers() {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                http_response_code(405);
                exit('Method not allowed');
            }

            $ids = $_POST['ids'] ?? [];
            if (!is_array($ids) || empty($ids)) {
                SessionManager::setFlash('error', 'No fundraisers selected.');
                $this->redirect('/admin/fundraisers');
                return;
            }

            $successCount = 0;
            foreach ($ids as $id) {
                if ($this->adminModel->approveFundraiser((int)$id)) {
                    $successCount++;
                }
            }

            if ($successCount > 0) {
                SessionManager::setFlash('success', "Approved $successCount fundraiser(s).");
            }
            $this->redirect('/admin/fundraisers');
        }

        /**
         * Bulk reject fundraisers
         */
        public function bulkRejectFundraisers() {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                http_response_code(405);
                exit('Method not allowed');
            }

            $ids = $_POST['ids'] ?? [];
            $reason = $_POST['reason'] ?? 'Rejected by admin';
            
            if (!is_array($ids) || empty($ids)) {
                SessionManager::setFlash('error', 'No fundraisers selected.');
                $this->redirect('/admin/fundraisers');
                return;
            }

            $successCount = 0;
            foreach ($ids as $id) {
                if ($this->adminModel->rejectFundraiser((int)$id, $reason)) {
                    $successCount++;
                }
            }

            if ($successCount > 0) {
                SessionManager::setFlash('warning', "Rejected $successCount fundraiser(s).");
            }
            $this->redirect('/admin/fundraisers');
        }

        // ==================== VERIFICATIONS & ALUMNI ====================

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
        }
    }
?>