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
        parent::__construct();
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
            
            // Online users and system update monitoring
            $onlineData = $this->adminModel->getOnlineUsers();
            $onlineUsers = $onlineData['users'] ?? [];
            $onlineCount = (int)($onlineData['online_count'] ?? count($onlineUsers));
            $systemUpdatesData = $this->getRecentSystemUpdates(20);
            
            $data = [
                'metrics' => $metrics,
                'detailed' => $detailed,
                'activity' => $activity,
                'users' => $users,
                'engagement' => $engagement,
                'online_users' => $onlineUsers,
                'online_count' => $onlineCount,
                'system_updates' => $systemUpdatesData['updates'],
                'system_updates_ref' => $systemUpdatesData['source_ref'],
                'system_updates_error' => $systemUpdatesData['error'],
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

        public function toggleSpecialAlumni() {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                $this->redirect('/admin/users');
                return;
            }

            $userId = (int)($_POST['user_id'] ?? 0);
            $isSpecial = (int)($_POST['special_alumni'] ?? 0) === 1;

            if ($userId <= 0) {
                SessionManager::setFlash('error', 'Invalid user ID.');
                $this->redirect('/admin/users');
                return;
            }

            $result = $this->adminModel->setSpecialAlumniStatus($userId, $isSpecial);
            if (!empty($result['ok'])) {
                if ((int)($_SESSION['user_id'] ?? 0) === $userId) {
                    $_SESSION['special_alumni'] = $isSpecial;
                }
                SessionManager::setFlash('success', $result['message'] ?? 'Special alumni status updated.');
            } else {
                SessionManager::setFlash('error', $result['message'] ?? 'Failed to update special alumni status.');
            }

            $this->redirect('/admin/users');
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

        public function exportUsersCsv() {
            $roleFilter = $this->resolveExportRoleFilter();
            $rows = $this->adminModel->getUsersExportRows($roleFilter);

            $csvRows = [];
            foreach ($rows as $row) {
                $csvRows[] = [
                    (int)($row->id ?? 0),
                    (string)($row->name ?? ''),
                    (string)($row->email ?? ''),
                    (string)($row->role ?? ''),
                    (string)($row->batch ?? ''),
                    (string)($row->gender ?? ''),
                    !empty($row->special_alumni) ? 'yes' : 'no',
                    (string)($row->created_at ?? ''),
                ];
            }

            $suffix = $roleFilter ?: 'all';
            $filename = 'users_export_' . $suffix . '_' . date('Ymd_His') . '.csv';
            $this->streamCsvDownload($filename, ['id', 'name', 'email', 'role', 'batch', 'gender', 'special_alumni', 'created_at'], $csvRows);
        }

        public function exportContentCsv() {
            $roleFilter = $this->resolveExportRoleFilter();
            $rows = $this->adminModel->getContentExportRows($roleFilter);

            $csvRows = [];
            foreach ($rows as $row) {
                $csvRows[] = [
                    (int)($row->content_id ?? 0),
                    (string)($row->content_type ?? 'post'),
                    (string)($row->title ?? ''),
                    (string)($row->status ?? ''),
                    (string)($row->author_name ?? ''),
                    (string)($row->author_email ?? ''),
                    (string)($row->author_role ?? ''),
                    (string)($row->created_at ?? ''),
                    (string)($row->body ?? ''),
                ];
            }

            $suffix = $roleFilter ?: 'all';
            $filename = 'content_export_' . $suffix . '_' . date('Ymd_His') . '.csv';
            $this->streamCsvDownload($filename, ['content_id', 'type', 'title', 'status', 'author_name', 'author_email', 'author_role', 'created_at', 'body'], $csvRows);
        }

        public function exportEventsCsv() {
            $roleFilter = $this->resolveExportRoleFilter();
            $rows = $this->adminModel->getEventsExportRows($roleFilter);

            $csvRows = [];
            foreach ($rows as $row) {
                $csvRows[] = [
                    (int)($row->event_id ?? 0),
                    (string)($row->title ?? ''),
                    (string)($row->status ?? ''),
                    (string)($row->start_at ?? ''),
                    (string)($row->venue ?? ''),
                    (string)($row->organizer_name ?? ''),
                    (string)($row->organizer_email ?? ''),
                    (string)($row->organizer_role ?? ''),
                    (string)($row->created_at ?? ''),
                ];
            }

            $suffix = $roleFilter ?: 'all';
            $filename = 'events_export_' . $suffix . '_' . date('Ymd_His') . '.csv';
            $this->streamCsvDownload($filename, ['event_id', 'title', 'status', 'start_at', 'venue', 'organizer_name', 'organizer_email', 'organizer_role', 'created_at'], $csvRows);
        }

        public function reports() {
            $postReports = $this->adminModel->getPostReports();
            $profileReports = $this->adminModel->getProfileReports();
            $eventReports = $this->adminModel->getEventReports();
            $fundraiserReports = $this->adminModel->getFundraiserReports();
            $data = [
                'reports' => $postReports,
                'postReports' => $postReports,
                'profileReports' => $profileReports,
                'eventReports' => $eventReports,
                'fundraiserReports' => $fundraiserReports,
            ];
            $this->view('admin/v_reports', $data);
        }

        public function updateContentReportStatus() {
            $accept = strtolower((string)($_SERVER['HTTP_ACCEPT'] ?? ''));
            $requestedWith = strtolower((string)($_SERVER['HTTP_X_REQUESTED_WITH'] ?? ''));
            $isJsonRequest = (strpos($accept, 'application/json') !== false) || ($requestedWith === 'xmlhttprequest');

            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                if ($isJsonRequest) {
                    $this->jsonResponse(['ok' => false, 'error' => 'Method not allowed'], 405);
                    return;
                }
                SessionManager::setFlash('error', 'Invalid request method.');
                $this->redirect('/admin/reports');
                return;
            }

            $reportId = (int)($_POST['report_id'] ?? 0);
            $status = trim((string)($_POST['status'] ?? ''));
            $source = trim((string)($_POST['source'] ?? 'reports'));
            $adminId = (int)($_SESSION['user_id'] ?? 0);

            $allowed = ['pending', 'resolved', 'rejected'];
            if ($reportId <= 0 || !in_array($status, $allowed, true)) {
                if ($isJsonRequest) {
                    $this->jsonResponse(['ok' => false, 'error' => 'Invalid report status update request.'], 422);
                    return;
                }
                SessionManager::setFlash('error', 'Invalid report status update request.');
                $this->redirect('/admin/reports');
                return;
            }

            $ok = $this->adminModel->updateContentReportStatus($reportId, $status, $adminId, $source);
            if ($isJsonRequest) {
                if ($ok) {
                    $this->jsonResponse(['ok' => true, 'message' => 'Report status updated successfully.', 'status' => $status]);
                } else {
                    $this->jsonResponse(['ok' => false, 'error' => 'Failed to update report status.'], 400);
                }
                return;
            }

            if ($ok) {
                SessionManager::setFlash('success', 'Report status updated successfully.');
            } else {
                SessionManager::setFlash('error', 'Failed to update report status.');
            }

            $this->redirect('/admin/reports');
        }

        public function removeReportedEvent() {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                $this->jsonResponse(['ok' => false, 'error' => 'Method not allowed'], 405);
                return;
            }

            $input = json_decode(file_get_contents('php://input'), true);
            if (!is_array($input)) {
                $input = $_POST;
            }

            $eventId = (int)($input['event_id'] ?? 0);
            $reportId = (int)($input['report_id'] ?? 0);
            $adminId = (int)($_SESSION['user_id'] ?? 0);

            if ($eventId <= 0) {
                $this->jsonResponse(['ok' => false, 'error' => 'Invalid event ID'], 400);
                return;
            }

            if ($adminId <= 0) {
                $this->jsonResponse(['ok' => false, 'error' => 'Unauthenticated'], 401);
                return;
            }

            $removed = $this->adminModel->removeEvent($eventId);
            if (!$removed) {
                $this->jsonResponse(['ok' => false, 'error' => 'Failed to remove event'], 400);
                return;
            }

            if ($reportId > 0) {
                $this->adminModel->updateContentReportStatus($reportId, 'resolved', $adminId, 'reports');
            }

            $this->jsonResponse(['ok' => true, 'message' => 'Event removed successfully']);
        }

        public function removeReportedFundraiser() {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                $this->jsonResponse(['ok' => false, 'error' => 'Method not allowed'], 405);
                return;
            }

            $input = json_decode(file_get_contents('php://input'), true);
            if (!is_array($input)) {
                $input = $_POST;
            }

            $fundraiserId = (int)($input['fundraiser_id'] ?? 0);
            $reportId = (int)($input['report_id'] ?? 0);
            $adminId = (int)($_SESSION['user_id'] ?? 0);

            if ($fundraiserId <= 0) {
                $this->jsonResponse(['ok' => false, 'error' => 'Invalid fundraiser ID'], 400);
                return;
            }

            if ($adminId <= 0) {
                $this->jsonResponse(['ok' => false, 'error' => 'Unauthenticated'], 401);
                return;
            }

            $removed = $this->adminModel->removeFundraiser($fundraiserId);
            if (!$removed) {
                $this->jsonResponse(['ok' => false, 'error' => 'Failed to remove fundraiser'], 400);
                return;
            }

            if ($reportId > 0) {
                $this->adminModel->updateContentReportStatus($reportId, 'resolved', $adminId, 'reports');
            }

            $this->jsonResponse(['ok' => true, 'message' => 'Fundraiser removed successfully']);
        }

        public function posts() {
            $data = [];
            $this->view('admin/v_posts', $data);
        }

        public function suspendedUsers() {
            $data = [
                'active_suspensions' => $this->adminModel->getActiveSuspendedUsers(),
                'suspension_history' => $this->adminModel->getSuspensionHistory(100),
            ];
            $this->view('admin/v_suspended_users', $data);
        }

        public function suspendUser() {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                $this->jsonResponse(['ok' => false, 'error' => 'Method not allowed'], 405);
                return;
            }

            $input = json_decode(file_get_contents('php://input'), true);
            if (!is_array($input)) {
                $input = $_POST;
            }

            $userId = (int)($input['user_id'] ?? 0);
            $reason = trim((string)($input['reason'] ?? ''));
            $adminId = (int)($_SESSION['user_id'] ?? 0);

            if ($userId <= 0) {
                $this->jsonResponse(['ok' => false, 'error' => 'Invalid user ID'], 400);
                return;
            }

            if ($adminId <= 0) {
                $this->jsonResponse(['ok' => false, 'error' => 'Unauthenticated'], 401);
                return;
            }

            if ($userId === $adminId) {
                $this->jsonResponse(['ok' => false, 'error' => 'You cannot suspend your own account'], 400);
                return;
            }

            $result = $this->adminModel->suspendUser($userId, $adminId, $reason);
            if (!empty($result['ok'])) {
                $this->jsonResponse(['ok' => true, 'message' => $result['message'] ?? 'User suspended']);
                return;
            }

            $this->jsonResponse(['ok' => false, 'error' => $result['message'] ?? 'Failed to suspend user'], 400);
        }

        public function deleteUser() {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                $this->jsonResponse(['ok' => false, 'error' => 'Method not allowed'], 405);
                return;
            }

            $input = json_decode(file_get_contents('php://input'), true);
            if (!is_array($input)) {
                $input = $_POST;
            }

            $userId = (int)($input['user_id'] ?? 0);
            $adminId = (int)($_SESSION['user_id'] ?? 0);

            if ($userId <= 0) {
                $this->jsonResponse(['ok' => false, 'error' => 'Invalid user ID'], 422);
                return;
            }

            if ($adminId <= 0) {
                $this->jsonResponse(['ok' => false, 'error' => 'Unauthenticated'], 401);
                return;
            }

            if ($userId === $adminId) {
                $this->jsonResponse(['ok' => false, 'error' => 'You cannot delete your own admin account from this screen'], 400);
                return;
            }

            $settingsModel = $this->model('M_settings');
            if (!$settingsModel || !method_exists($settingsModel, 'getUserById') || !method_exists($settingsModel, 'deleteAccount')) {
                $this->jsonResponse(['ok' => false, 'error' => 'Delete service unavailable'], 500);
                return;
            }

            $targetUser = $settingsModel->getUserById($userId);
            if (!$targetUser) {
                $this->jsonResponse(['ok' => false, 'error' => 'User not found'], 404);
                return;
            }

            $role = strtolower(trim((string)($targetUser->role ?? '')));
            $name = strtolower(trim((string)($targetUser->name ?? '')));
            $email = strtolower(trim((string)($targetUser->email ?? '')));
            $isProtected = (
                $role === 'admin' ||
                $role === 'administrator' ||
                $role === 'system_admin' ||
                $role === 'system-administrator' ||
                $role === 'super_admin' ||
                strpos($role, 'admin') !== false ||
                $name === 'system administrator' ||
                $email === 'admin@gradlink.com'
            );

            if ($isProtected) {
                $this->jsonResponse(['ok' => false, 'error' => 'Admin accounts cannot be deleted from this screen'], 403);
                return;
            }

            $deleted = $settingsModel->deleteAccount($userId);
            if (!$deleted) {
                $this->jsonResponse(['ok' => false, 'error' => 'Failed to delete user'], 400);
                return;
            }

            $this->jsonResponse(['ok' => true, 'message' => 'User deleted successfully']);
        }

        public function liftSuspension() {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                $this->redirect('/admin/suspendedUsers');
                return;
            }

            $suspensionId = (int)($_POST['suspension_id'] ?? 0);
            $adminId = (int)($_SESSION['user_id'] ?? 0);

            if ($suspensionId <= 0) {
                SessionManager::setFlash('error', 'Invalid suspension record.');
                $this->redirect('/admin/suspendedUsers');
                return;
            }

            $result = $this->adminModel->liftSuspension($suspensionId, $adminId);
            if (!empty($result['ok'])) {
                SessionManager::setFlash('success', $result['message'] ?? 'Suspension removed.');
            } else {
                SessionManager::setFlash('error', $result['message'] ?? 'Failed to remove suspension.');
            }

            $this->redirect('/admin/suspendedUsers');
        }

        public function removeSuspendedUser() {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                $this->redirect('/admin/suspendedUsers');
                return;
            }

            $suspensionId = (int)($_POST['suspension_id'] ?? 0);
            $adminId = (int)($_SESSION['user_id'] ?? 0);

            if ($suspensionId <= 0) {
                SessionManager::setFlash('error', 'Invalid suspension record.');
                $this->redirect('/admin/suspendedUsers');
                return;
            }

            $suspension = $this->adminModel->getSuspensionById($suspensionId);
            if (!$suspension || (int)($suspension->user_id ?? 0) <= 0) {
                SessionManager::setFlash('error', 'Suspended user not found.');
                $this->redirect('/admin/suspendedUsers');
                return;
            }

            $userId = (int)$suspension->user_id;

            $deleteResult = $this->adminModel->deleteUserCompletely($userId);
            if (!empty($deleteResult['ok'])) {
                $markResult = $this->adminModel->markSuspensionRemoved($suspensionId, $adminId);

                if (!empty($markResult['ok'])) {
                    SessionManager::setFlash('success', 'Suspended user was removed from the system.');
                } else {
                    SessionManager::setFlash('warning', 'User was removed, but suspension history could not be updated.');
                }
            } else {
                SessionManager::setFlash('error', $deleteResult['message'] ?? 'Failed to remove user from system.');
            }

            $this->redirect('/admin/suspendedUsers');
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

        private function jsonResponse(array $payload, int $status = 200): void {
            http_response_code($status);
            header('Content-Type: application/json');
            echo json_encode($payload);
        }

        private function resolveExportRoleFilter(): ?string {
            $role = strtolower(trim((string)($_GET['role'] ?? '')));
            if ($role === '' || $role === 'all') {
                return null;
            }

            if (!in_array($role, ['admin', 'alumni', 'undergrad'], true)) {
                return null;
            }

            return $role;
        }

        private function streamCsvDownload(string $filename, array $headers, array $rows): void {
            while (ob_get_level() > 0) {
                ob_end_clean();
            }

            header('Content-Type: text/csv; charset=UTF-8');
            header('Content-Disposition: attachment; filename="' . $filename . '"');
            header('Pragma: no-cache');
            header('Expires: 0');

            echo "\xEF\xBB\xBF";
            $output = fopen('php://output', 'w');

            if ($output === false) {
                exit;
            }

            fputcsv($output, $headers);
            foreach ($rows as $row) {
                fputcsv($output, $row);
            }

            fclose($output);
            exit;
        }

        private function getRecentSystemUpdates(int $limit = 20): array {
            $safeLimit = max(1, min(30, $limit));

            $apiResult = $this->getRecentSystemUpdatesFromGithub($safeLimit);
            if (!empty($apiResult['updates'])) {
                return $apiResult;
            }

            // Keep a safe fallback path to avoid breaking the dashboard if API config is missing.
            $fallbackResult = $this->getRecentSystemUpdatesFromLocalGit($safeLimit);
            if (!empty($fallbackResult['updates'])) {
                return $fallbackResult;
            }

            return [
                'updates' => [],
                'source_ref' => $apiResult['source_ref'] ?? null,
                'error' => $apiResult['error'] ?? ($fallbackResult['error'] ?? 'Unable to load system updates.'),
            ];
        }

        private function getRecentSystemUpdatesFromGithub(int $limit): array {
            // Get configuration from environment variables
            $owner = trim((string)gl_env('ADMIN_UPDATES_REPO_OWNER', 'KaveenAmarasekara'));
            $repo = trim((string)gl_env('ADMIN_UPDATES_REPO_NAME', 'v0-student-nic-collection'));
            $branch = trim((string)gl_env('ADMIN_UPDATES_BRANCH', 'main'));
            $token = trim((string)gl_env('GITHUB_TOKEN', ''));

            if ($owner === '' || $repo === '') {
                return [
                    'updates' => [],
                    'source_ref' => null,
                    'error' => 'GitHub repo owner/name is not configured.',
                ];
            }

            if ($token === '') {
                return [
                    'updates' => [],
                    'source_ref' => 'github:' . $owner . '/' . $repo . '@' . $branch,
                    'error' => 'GitHub token is missing for private repository access.',
                ];
            }

            $url = 'https://api.github.com/repos/'
                . rawurlencode($owner)
                . '/'
                . rawurlencode($repo)
                . '/commits?sha=' . rawurlencode($branch)
                . '&per_page=' . $limit;

            $headers = [
                'Accept: application/vnd.github+json',
                'Authorization: Bearer ' . $token,
                'X-GitHub-Api-Version: 2022-11-28',
                'User-Agent: GradlinkAdminDashboard/1.0',
            ];

            $responseBody = '';
            $statusCode = 0;

            if (function_exists('curl_init')) {
                $ch = curl_init($url);
                curl_setopt_array($ch, [
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_TIMEOUT => 10,
                    CURLOPT_HTTPHEADER => $headers,
                ]);
                $responseBody = (string)curl_exec($ch);
                $statusCode = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
                curl_close($ch);
            } else {
                $context = stream_context_create([
                    'http' => [
                        'method' => 'GET',
                        'header' => implode("\r\n", $headers) . "\r\n",
                        'timeout' => 10,
                    ],
                ]);
                $responseBody = (string)@file_get_contents($url, false, $context);
                $statusCode = 0;
                if (!empty($http_response_header[0]) && preg_match('/\s(\d{3})\s/', $http_response_header[0], $m)) {
                    $statusCode = (int)$m[1];
                }
            }

            if ($statusCode !== 200) {
                $errorText = 'GitHub API request failed.';
                if ($statusCode === 401 || $statusCode === 403) {
                    $errorText = 'GitHub token is invalid or lacks repo read permission.';
                } elseif ($statusCode === 404) {
                    $errorText = 'Repository or branch was not found on GitHub.';
                }

                return [
                    'updates' => [],
                    'source_ref' => 'github:' . $owner . '/' . $repo . '@' . $branch,
                    'error' => $errorText,
                ];
            }

            $json = json_decode($responseBody, true);
            if (!is_array($json)) {
                return [
                    'updates' => [],
                    'source_ref' => 'github:' . $owner . '/' . $repo . '@' . $branch,
                    'error' => 'GitHub API returned invalid JSON.',
                ];
            }

            $updates = [];
            foreach ($json as $commit) {
                $sha = (string)($commit['sha'] ?? '');
                $fullMessage = (string)($commit['commit']['message'] ?? '');
                $firstLineMessage = trim((string)strtok($fullMessage, "\n"));
                $authorName = (string)($commit['commit']['author']['name'] ?? ($commit['author']['login'] ?? 'Unknown'));
                $authorDate = (string)($commit['commit']['author']['date'] ?? '');
                $date = $authorDate !== '' ? date('Y-m-d', strtotime($authorDate)) : '';

                if ($sha === '') {
                    continue;
                }

                $updates[] = [
                    'hash' => substr($sha, 0, 7),
                    'date' => $date,
                    'author' => $authorName,
                    'message' => $firstLineMessage,
                ];
            }

            return [
                'updates' => $updates,
                'source_ref' => 'github:' . $owner . '/' . $repo . '@' . $branch,
                'error' => empty($updates) ? 'No commits returned by GitHub API.' : null,
            ];
        }

        private function getRecentSystemUpdatesFromLocalGit(int $limit): array {
            $result = [
                'updates' => [],
                'source_ref' => null,
                'error' => null,
            ];

            $repoRoot = realpath(APPROOT . '/..');
            if ($repoRoot === false) {
                $result['error'] = 'Repository path could not be resolved.';
                return $result;
            }

            if (!is_dir($repoRoot . DIRECTORY_SEPARATOR . '.git')) {
                $result['error'] = 'Git metadata (.git) was not found in deployment path.';
                return $result;
            }

            if (!function_exists('shell_exec')) {
                $result['error'] = 'Local git fallback is unavailable because shell execution is disabled.';
                return $result;
            }

            $configuredBranch = trim((string)gl_env('ADMIN_UPDATES_BRANCH', 'dev'));
            $refsToTry = array_values(array_unique(array_filter([
                $configuredBranch,
                'origin/' . $configuredBranch,
                'HEAD',
                'main',
                'origin/main',
            ])));

            $selectedRef = null;
            foreach ($refsToTry as $ref) {
                $verifyCmd = 'git -C ' . escapeshellarg($repoRoot) . ' rev-parse --verify --quiet ' . escapeshellarg($ref) . ' 2>&1';
                $verifyOutput = trim((string)@shell_exec($verifyCmd));
                if ($verifyOutput !== '') {
                    $selectedRef = $ref;
                    break;
                }
            }

            if ($selectedRef === null) {
                $result['error'] = 'No usable local git ref found.';
                return $result;
            }

            $logCmd = 'git -C ' . escapeshellarg($repoRoot)
                . ' log ' . escapeshellarg($selectedRef)
                . ' --date=short --pretty=format:%h%x09%ad%x09%an%x09%s -n ' . $limit
                . ' 2>&1';

            $rawOutput = (string)@shell_exec($logCmd);
            if (trim($rawOutput) === '') {
                $result['source_ref'] = 'local:' . $selectedRef;
                $result['error'] = 'No commits could be read from local ref ' . $selectedRef . '.';
                return $result;
            }

            $updates = [];
            $lines = preg_split('/\r\n|\r|\n/', trim($rawOutput)) ?: [];
            foreach ($lines as $line) {
                $parts = explode("\t", $line, 4);
                if (count($parts) < 4) {
                    continue;
                }
                $updates[] = [
                    'hash' => $parts[0],
                    'date' => $parts[1],
                    'author' => $parts[2],
                    'message' => $parts[3],
                ];
            }

            $result['updates'] = $updates;
            $result['source_ref'] = 'local:' . $selectedRef;
            if (empty($updates)) {
                $result['error'] = 'Commit output could not be parsed for local ref ' . $selectedRef . '.';
            }

            return $result;
        }
         // ==================== HELP & SUPPORT ====================

        /**
         * Support management page — tickets, reports, feedback
         */
        public function support() {
            $stats = $this->adminModel->getSupportStats();
            $tickets = $this->adminModel->getSupportTickets();
            $reports = $this->adminModel->getProblemReports();
            $feedback = $this->adminModel->getSupportFeedback();

            $data = [
                'stats' => $stats,
                'tickets' => $tickets,
                'reports' => $reports,
                'feedback' => $feedback,
                'activeTab' => 'support'
            ];
            $this->view('admin/v_support', $data);
        }

        /**
         * POST: Update support ticket status
         */
        public function updateTicketStatus() {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                $this->redirect('/admin/support');
                return;
            }

            $id = $_POST['id'] ?? null;
            $status = $_POST['status'] ?? null;
            $allowed = ['open', 'in_progress', 'resolved', 'closed'];

            if (!$id || !in_array($status, $allowed, true)) {
                SessionManager::setFlash('error', 'Invalid ticket or status.');
                $this->redirect('/admin/support');
                return;
            }

            if ($this->adminModel->updateSupportTicketStatus($id, $status)) {
                SessionManager::setFlash('success', 'Ticket #' . $id . ' status updated to ' . str_replace('_', ' ', $status) . '.');
            } else {
                SessionManager::setFlash('error', 'Failed to update ticket status.');
            }
            $this->redirect('/admin/support');
        }

        /**
         * POST: Admin reply to a support ticket
         */
        public function replyTicket() {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                $this->redirect('/admin/support');
                return;
            }

            $id = $_POST['id'] ?? null;
            $reply = trim($_POST['reply'] ?? '');
            $adminId = (int)($_SESSION['user_id'] ?? 0);

            if (!$id || empty($reply)) {
                SessionManager::setFlash('error', 'Ticket ID and reply are required.');
                $this->redirect('/admin/support');
                return;
            }

            $ticket = $this->adminModel->getSupportTicketById($id);
            if (!$ticket) {
                SessionManager::setFlash('error', 'Support ticket not found.');
                $this->redirect('/admin/support');
                return;
            }

            if ($this->adminModel->replySupportTicket($id, $reply)) {
                $delivered = $this->sendSupportReplyAsMessage((int)$ticket->user_id, $reply, 'support ticket', (int)$id, $adminId);
                if ($delivered) {
                    SessionManager::setFlash('success', 'Reply sent to ticket #' . $id . ' and delivered as a chat message.');
                } else {
                    SessionManager::setFlash('warning', 'Reply saved for ticket #' . $id . ', but chat delivery failed.');
                }
            } else {
                SessionManager::setFlash('error', 'Failed to send reply.');
            }
            $this->redirect('/admin/support');
        }

        /**
         * POST: Update problem report status
         */
        public function updateReportStatus() {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                $this->redirect('/admin/support');
                return;
            }

            $id = $_POST['id'] ?? null;
            $status = $_POST['status'] ?? null;
            $allowed = ['pending', 'triaged', 'resolved', 'rejected'];

            if (!$id || !in_array($status, $allowed, true)) {
                SessionManager::setFlash('error', 'Invalid report or status.');
                $this->redirect('/admin/support');
                return;
            }

            if ($this->adminModel->updateProblemReportStatus($id, $status)) {
                SessionManager::setFlash('success', 'Report #' . $id . ' status updated.');
            } else {
                SessionManager::setFlash('error', 'Failed to update report status.');
            }
            $this->redirect('/admin/support');
        }

        /**
         * POST: Admin reply to a problem report
         */
        public function replyReport() {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                $this->redirect('/admin/support');
                return;
            }

            $id = $_POST['id'] ?? null;
            $reply = trim($_POST['reply'] ?? '');
            $adminId = (int)($_SESSION['user_id'] ?? 0);

            if (!$id || empty($reply)) {
                SessionManager::setFlash('error', 'Report ID and reply are required.');
                $this->redirect('/admin/support');
                return;
            }

            $report = $this->adminModel->getProblemReportById($id);
            if (!$report) {
                SessionManager::setFlash('error', 'Problem report not found.');
                $this->redirect('/admin/support');
                return;
            }

            if ($this->adminModel->replyProblemReport($id, $reply)) {
                $delivered = $this->sendSupportReplyAsMessage((int)$report->user_id, $reply, 'problem report', (int)$id, $adminId);
                if ($delivered) {
                    SessionManager::setFlash('success', 'Reply sent to report #' . $id . ' and delivered as a chat message.');
                } else {
                    SessionManager::setFlash('warning', 'Reply saved for report #' . $id . ', but chat delivery failed.');
                }
            } else {
                SessionManager::setFlash('error', 'Failed to send reply.');
            }
            $this->redirect('/admin/support');
        }

        private function sendSupportReplyAsMessage(int $recipientId, string $reply, string $sourceType, int $sourceId, int $adminId): bool {
            if ($recipientId <= 0 || $adminId <= 0 || $recipientId === $adminId) {
                return false;
            }

            $messageModel = $this->model('M_message');
            if (!$messageModel || !method_exists($messageModel, 'sendMessage')) {
                return false;
            }

            $text = "Admin Support Reply (" . ucfirst($sourceType) . " #" . $sourceId . "):\n" . $reply;
            $messageId = $messageModel->sendMessage($adminId, $recipientId, $text);
            if (!$messageId) {
                return false;
            }

            try {
                if ($this->notificationModel && method_exists($this->notificationModel, 'hasUnreadMessageNotification')) {
                    $hasUnread = $this->notificationModel->hasUnreadMessageNotification($recipientId, $adminId);
                    if ($hasUnread && method_exists($this->notificationModel, 'updateMessageNotificationTime')) {
                        $this->notificationModel->updateMessageNotificationTime($recipientId, $adminId);
                    } else {
                        $this->notify(
                            $recipientId,
                            'new_message',
                            $adminId,
                            ['text' => 'Admin support sent you a message']
                        );
                    }
                }
            } catch (Exception $e) {
                error_log('Failed to create support reply notification: ' . $e->getMessage());
            }

            return true;
        }

        /**
         * POST: Delete a feedback entry
         */
        public function deleteFeedbackEntry() {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                $this->redirect('/admin/support');
                return;
            }

            $id = $_POST['id'] ?? null;

            if (!$id) {
                SessionManager::setFlash('error', 'Invalid feedback ID.');
                $this->redirect('/admin/support');
                return;
            }

            if ($this->adminModel->deleteSupportFeedback($id)) {
                SessionManager::setFlash('success', 'Feedback #' . $id . ' deleted.');
            } else {
                SessionManager::setFlash('error', 'Failed to delete feedback.');
            }
            $this->redirect('/admin/support');
        }
    }
?>