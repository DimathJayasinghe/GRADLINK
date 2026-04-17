<?php
class settings extends Controller{
    private $model;
    private $signupModel;
    public function __construct(){
        SessionManager:: redirectToAuthIfNotLoggedIn();
        $this->model = $this->model('M_settings');
        $this->signupModel = $this->model('M_signup');
    }

    public function index(){
        $data = [

        ];
        $this->redirect('/settings/account');
    }
    public function account(){
        $data = [
            'section' => 'account'
        ];
        $this->view('settings/v_settings', $data);
    }

    public function privacyandsafety(){
        $this->redirect('/settings/account');
    }
    public function notifications(){
        $data = [
            'section' => 'notifications'
        ];
        $this->view('settings/v_settings', $data);

    }
    public function appearance(){
        $data = [
            'section' => 'appearance'
        ];
        $this->view('settings/v_settings', $data);
    }
    public function helpandsupport(){
        $userId = (int)($_SESSION['user_id'] ?? 0);
        $data = [
            'section' => 'helpandsupport',
            'myTickets' => $userId ? $this->model->getSupportTicketsByUser($userId, 10) : [],
            'myReports' => $userId ? $this->model->getProblemReportsByUser($userId, 10) : []
        ];
        $this->view('settings/v_settings', $data);
    }

    private function jsonResponse($payload, $statusCode = 200) {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($payload);
    }

    private function getJsonInput() {
        $raw = file_get_contents('php://input');
        if (!$raw) {
            return [];
        }

        $decoded = json_decode($raw, true);
        return is_array($decoded) ? $decoded : [];
    }

    private function ensurePostMethod() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->jsonResponse(['success' => false, 'error' => 'Invalid request method'], 405);
            return false;
        }
        return true;
    }

    private function isStudentEmail(string $email): bool {
        return (bool)preg_match('/^[0-9]{4}(?:cs|is)[0-9]{3}@stu\.ucsc\.cmb\.ac\.lk$/i', $email);
    }
    public function bookmarks(){
        $bookmarkModel = $this->model('M_bookmark');
        $data = [
            'section' => 'bookmark',
            'bookmarks' => $bookmarkModel->getBookmarksByUserId((int)($_SESSION['user_id'] ?? 0))
        ];
        $this->view('settings/v_settings', $data);
    }

    /**
     * API: Update user name and display name
     * POST /settings/updateName
     * Body: {"name": "Full Name", "display_name": "Display Name"}
     */
    public function updateName() {
        if (!$this->ensurePostMethod()) {
            return;
        }

        try {
            $input = $this->getJsonInput();
            $userId = $_SESSION['user_id'];
            
            $name = trim($input['name'] ?? '');
            $displayName = trim($input['display_name'] ?? '');
            
            if (empty($name)) {
                $this->jsonResponse(['success' => false, 'error' => 'Name cannot be empty'], 422);
                return;
            }
            
            $result = $this->model->updateName($userId, $name, $displayName);
            
            if ($result) {
                // Update session
                $_SESSION['user_name'] = $name;
                if (!empty($displayName)) {
                    $_SESSION['display_name'] = $displayName;
                }
                
                $this->jsonResponse(['success' => true, 'message' => 'Name updated successfully']);
            } else {
                $this->jsonResponse(['success' => false, 'error' => 'Failed to update name'], 500);
            }
        } catch (Throwable $e) {
            $this->jsonResponse(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * API: Update user bio
     * POST /settings/updateBio
     * Body: {"bio": "User bio text"}
     */
    public function updateBio() {
        if (!$this->ensurePostMethod()) {
            return;
        }

        try {
            $input = $this->getJsonInput();
            $userId = $_SESSION['user_id'];
            
            $bio = trim($input['bio'] ?? '');
            
            $result = $this->model->updateBio($userId, $bio);
            
            if ($result) {
                $this->jsonResponse(['success' => true, 'message' => 'Bio updated successfully']);
            } else {
                $this->jsonResponse(['success' => false, 'error' => 'Failed to update bio'], 500);
            }
        } catch (Throwable $e) {
            $this->jsonResponse(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * API: Send OTP for email change
     * POST /settings/sendEmailChangeOtp
     * Body: {"email": "new@email.com"}
     */
    public function sendEmailChangeOtp() {
        if (!$this->ensurePostMethod()) {
            return;
        }

        try {
            $input = $this->getJsonInput();
            $userId = (int)($_SESSION['user_id'] ?? 0);
            $newEmail = trim((string)($input['email'] ?? ''));

            if ($userId <= 0) {
                $this->jsonResponse(['success' => false, 'error' => 'Unauthorized'], 401);
                return;
            }

            if (empty($newEmail) || !filter_var($newEmail, FILTER_VALIDATE_EMAIL)) {
                $this->jsonResponse(['success' => false, 'error' => 'Please provide a valid email'], 422);
                return;
            }

            $user = $this->model->getUserById($userId);
            if (!$user) {
                $this->jsonResponse(['success' => false, 'error' => 'User not found'], 404);
                return;
            }

            $role = strtolower((string)($user->role ?? ''));
            if ($role === 'undergrad' && !$this->isStudentEmail($newEmail)) {
                $this->jsonResponse(['success' => false, 'error' => 'Undergraduate accounts must use a student email (e.g. 20XXcsXXX@stu.ucsc.cmb.ac.lk)'], 422);
                return;
            }

            if ($this->model->emailExists($newEmail, $userId)) {
                $this->jsonResponse(['success' => false, 'error' => 'Email already in use'], 409);
                return;
            }

            $otp = random_int(100000, 999999);
            $sent = EmailHandler::sendOtpEmail($newEmail, 'email_change', $otp);
            if (!$sent) {
                $this->jsonResponse(['success' => false, 'error' => 'Failed to send OTP'], 500);
                return;
            }

            if (!$this->signupModel || !method_exists($this->signupModel, 'saveOTP')) {
                $this->jsonResponse(['success' => false, 'error' => 'OTP service unavailable'], 500);
                return;
            }

            $saved = $this->signupModel->saveOTP($newEmail, (string)$otp, 'email_change');
            if (!$saved) {
                $this->jsonResponse(['success' => false, 'error' => 'Failed to store OTP'], 500);
                return;
            }

            $this->jsonResponse(['success' => true, 'message' => 'OTP sent to your new email']);
        } catch (Throwable $e) {
            $this->jsonResponse(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * API: Update user email
     * POST /settings/updateEmail
     * Body: {"current_email": "current@email.com", "new_email": "new@email.com", "password": "password", "otp": "123456"}
     */
    public function updateEmail() {
        if (!$this->ensurePostMethod()) {
            return;
        }

        try {
            $input = $this->getJsonInput();
            $userId = $_SESSION['user_id'];
            
            $currentEmail = trim($input['current_email'] ?? '');
            $newEmail = trim($input['new_email'] ?? '');
            $password = $input['password'] ?? '';
            $otp = trim((string)($input['otp'] ?? ''));
            
            // Validate inputs
            if (empty($newEmail) || empty($password) || empty($otp)) {
                $this->jsonResponse(['success' => false, 'error' => 'New email, password, and OTP are required'], 422);
                return;
            }

            if (!preg_match('/^\d{6}$/', $otp)) {
                $this->jsonResponse(['success' => false, 'error' => 'OTP must be a 6-digit code'], 422);
                return;
            }
            
            if (!filter_var($newEmail, FILTER_VALIDATE_EMAIL)) {
                $this->jsonResponse(['success' => false, 'error' => 'Invalid email format'], 422);
                return;
            }
            
            // Verify password
            $user = $this->model->getUserById($userId);
            if (!$user) {
                $this->jsonResponse(['success' => false, 'error' => 'User not found'], 404);
                return;
            }

            $role = strtolower((string)($user->role ?? ''));
            if ($role === 'undergrad' && !$this->isStudentEmail($newEmail)) {
                $this->jsonResponse(['success' => false, 'error' => 'Undergraduate accounts must use a student email (e.g. 20XXcsXXX@stu.ucsc.cmb.ac.lk)'], 422);
                return;
            }

            if (!empty($currentEmail) && strcasecmp($currentEmail, (string)$user->email) !== 0) {
                $this->jsonResponse(['success' => false, 'error' => 'Current email does not match your account'], 422);
                return;
            }

            if (strcasecmp($newEmail, (string)$user->email) === 0) {
                $this->jsonResponse(['success' => false, 'error' => 'New email must be different from current email'], 422);
                return;
            }

            $passwordValid = (strpos($user->password, '$2y$') === 0)
                ? password_verify($password, $user->password)
                : ($password === $user->password);

            if (!$passwordValid) {
                $this->jsonResponse(['success' => false, 'error' => 'Incorrect password'], 401);
                return;
            }
            
            // Check if email already exists
            if ($this->model->emailExists($newEmail, $userId)) {
                $this->jsonResponse(['success' => false, 'error' => 'Email already in use'], 409);
                return;
            }

            if (!$this->signupModel || !method_exists($this->signupModel, 'verifyOTP')) {
                $this->jsonResponse(['success' => false, 'error' => 'OTP service unavailable'], 500);
                return;
            }

            $otpResult = $this->signupModel->verifyOTP($newEmail, $otp, 'email_change');
            if (empty($otpResult['success'])) {
                $this->jsonResponse(['success' => false, 'error' => $otpResult['message'] ?? 'Invalid OTP'], 422);
                return;
            }
            
            $result = $this->model->updateEmail($userId, $newEmail);
            
            if ($result) {
                $_SESSION['user_email'] = $newEmail;
                $this->jsonResponse(['success' => true, 'message' => 'Email updated successfully']);
            } else {
                $this->jsonResponse(['success' => false, 'error' => 'Failed to update email'], 500);
            }
        } catch (Throwable $e) {
            $this->jsonResponse(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * API: Convert account role from undergrad to alumni
     * POST /settings/convertToAlumni
     * Body: {"password": "..."}
     */
    public function convertToAlumni() {
        if (!$this->ensurePostMethod()) {
            return;
        }

        try {
            $input = $this->getJsonInput();
            $userId = (int)($_SESSION['user_id'] ?? 0);
            $password = (string)($input['password'] ?? '');

            if ($userId <= 0) {
                $this->jsonResponse(['success' => false, 'error' => 'Unauthorized'], 401);
                return;
            }

            if (trim($password) === '') {
                $this->jsonResponse(['success' => false, 'error' => 'Password is required'], 422);
                return;
            }

            $user = $this->model->getUserById($userId);
            if (!$user) {
                $this->jsonResponse(['success' => false, 'error' => 'User not found'], 404);
                return;
            }

            if (strtolower((string)($user->role ?? '')) !== 'undergrad') {
                $this->jsonResponse(['success' => false, 'error' => 'Only undergraduate accounts can be converted'], 403);
                return;
            }

            $passwordValid = (strpos((string)$user->password, '$2y$') === 0)
                ? password_verify($password, (string)$user->password)
                : ($password === (string)$user->password);

            if (!$passwordValid) {
                $this->jsonResponse(['success' => false, 'error' => 'Incorrect password'], 401);
                return;
            }

            $converted = $this->model->convertUndergradToAlumni($userId);
            if (!$converted) {
                $this->jsonResponse(['success' => false, 'error' => 'Failed to convert account type'], 500);
                return;
            }

            $_SESSION['user_role'] = 'alumni';
            $_SESSION['special_alumni'] = false;
            $this->jsonResponse(['success' => true, 'message' => 'Account type updated to alumni']);
        } catch (Throwable $e) {
            $this->jsonResponse(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * API: Change user password
     * POST /settings/changePassword
     * Body: {"current_password": "old", "new_password": "new", "confirm_password": "new"}
     */
    public function changePassword() {
        if (!$this->ensurePostMethod()) {
            return;
        }

        try {
            $input = $this->getJsonInput();
            $userId = $_SESSION['user_id'];
            
            $currentPassword = $input['current_password'] ?? '';
            $newPassword = $input['new_password'] ?? '';
            $confirmPassword = $input['confirm_password'] ?? '';
            
            // Validate inputs
            if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
                $this->jsonResponse(['success' => false, 'error' => 'All fields are required'], 422);
                return;
            }
            
            if ($newPassword !== $confirmPassword) {
                $this->jsonResponse(['success' => false, 'error' => 'New passwords do not match'], 422);
                return;
            }
            
            if (strlen($newPassword) < 8) {
                $this->jsonResponse(['success' => false, 'error' => 'New password must be at least 8 characters'], 422);
                return;
            }
            
            // Verify current password
            $user = $this->model->getUserById($userId);
            if (!$user) {
                $this->jsonResponse(['success' => false, 'error' => 'User not found'], 404);
                return;
            }

            $passwordValid = (strpos($user->password, '$2y$') === 0)
                ? password_verify($currentPassword, $user->password)
                : ($currentPassword === $user->password);

            if (!$passwordValid) {
                $this->jsonResponse(['success' => false, 'error' => 'Current password is incorrect'], 401);
                return;
            }
            
            // Hash new password
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            
            $result = $this->model->updatePassword($userId, $hashedPassword);
            
            if ($result) {
                $this->jsonResponse(['success' => true, 'message' => 'Password changed successfully']);
            } else {
                $this->jsonResponse(['success' => false, 'error' => 'Failed to change password'], 500);
            }
        } catch (Exception $e) {
            $this->jsonResponse(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * API: Permanently delete account and related user data.
     * POST /settings/deleteAccount
     * Body: {
     *   "password": "password",
     *   "confirmation": "CONFIRM"
     * }
     */
    public function deleteAccount() {
        if (!$this->ensurePostMethod()) {
            return;
        }

        try {
            SessionManager::ensureStarted();
            $input = $this->getJsonInput();
            
            if (!isset($_SESSION['user_id'])) {
                $this->jsonResponse(['success' => false, 'error' => 'User not logged in'], 401);
                return;
            }
            
            $userId = $_SESSION['user_id'];
            $password = $input['password'] ?? '';
            $confirmation = strtoupper(trim($input['confirmation'] ?? ''));
            
            // Validate inputs
            if (empty($password)) {
                $this->jsonResponse(['success' => false, 'error' => 'Password is required'], 422);
                return;
            }

            if ($confirmation !== 'CONFIRM') {
                $this->jsonResponse(['success' => false, 'error' => 'Please type CONFIRM to proceed'], 422);
                return;
            }
            
            // Verify password
            $user = $this->model->getUserById($userId);
            
            if (!$user) {
                $this->jsonResponse(['success' => false, 'error' => 'User not found'], 404);
                return;
            }
            
            // Check if password is hashed (starts with $2y$ for bcrypt) or plain text
            $passwordValid = false;
            if (strpos($user->password, '$2y$') === 0) {
                // Hashed password - use password_verify
                $passwordValid = password_verify($password, $user->password);
            } else {
                // Plain text password (legacy/admin accounts) - direct comparison
                $passwordValid = ($password === $user->password);
            }
            
            if (!$passwordValid) {
                $this->jsonResponse(['success' => false, 'error' => 'Incorrect password'], 401);
                return;
            }
            
            // Permanently remove user account and related records.
            $result = $this->model->deleteAccount($userId);
            
            if ($result) {
                // Destroy session and logout
                SessionManager::destroySession();

                $this->jsonResponse([
                    'success' => true,
                    'message' => 'Account permanently deleted'
                ]);
            } else {
                $this->jsonResponse(['success' => false, 'error' => 'Failed to delete account'], 500);
            }
        } catch (Throwable $e) {
            $this->jsonResponse(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * API: Get current user profile data
     * GET /settings/getUserData
     */
    public function getUserData() {
        try {
            $userId = $_SESSION['user_id'];
            $user = $this->model->getUserById($userId);
            
            if ($user) {
                // Remove sensitive data
                unset($user->password);
                
                $this->jsonResponse(['success' => true, 'user' => $user]);
            } else {
                $this->jsonResponse(['success' => false, 'error' => 'User not found'], 404);
            }
        } catch (Exception $e) {
            $this->jsonResponse(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * API: Get notification settings
     * GET /settings/getNotificationSettings
     */
    public function getNotificationSettings() {
        try {
            $userId = $_SESSION['user_id'];
            $settings = $this->model->getNotificationSettings($userId);

            if (!$settings) {
                $settings = (object)[
                    'email_enabled' => 0
                ];
            } else {
                // Ensure consistent scalar types for the frontend (PDO may return strings)
                $settings->email_enabled = (int)($settings->email_enabled ?? 0);
            }

            $this->jsonResponse(['success' => true, 'settings' => $settings]);
        } catch (Throwable $e) {
            $this->jsonResponse(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * API: Update notification settings
     * POST /settings/updateNotificationSettings
     */
    public function updateNotificationSettings() {
        if (!$this->ensurePostMethod()) {
            return;
        }

        try {
            $userId = $_SESSION['user_id'];
            $input = $this->getJsonInput();

            $payload = [
                'email_enabled' => !empty($input['email_enabled']) ? 1 : 0
            ];

            $saved = $this->model->upsertNotificationSettings($userId, $payload);

            if (!$saved) {
                $this->jsonResponse(['success' => false, 'error' => 'Failed to save notification settings'], 500);
                return;
            }

            $this->jsonResponse(['success' => true, 'message' => 'Notification settings saved']);
        } catch (Throwable $e) {
            $this->jsonResponse(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * API: Get blocked users list
     * GET /settings/getBlockedUsers
     */
    public function getBlockedUsers() {
        try {
            $userId = $_SESSION['user_id'];
            $blockedUsers = $this->model->getBlockedUsers($userId);

            $this->jsonResponse(['success' => true, 'blocked_users' => $blockedUsers]);
        } catch (Exception $e) {
            $this->jsonResponse(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * API: Block a user
     * POST /settings/blockUser
     */
    public function blockUser() {
        if (!$this->ensurePostMethod()) {
            return;
        }

        try {
            $userId = $_SESSION['user_id'];
            $input = $this->getJsonInput();
            $blockedUserId = (int)($input['blocked_user_id'] ?? 0);

            if ($blockedUserId <= 0) {
                $this->jsonResponse(['success' => false, 'error' => 'Invalid target user id'], 422);
                return;
            }

            if ($blockedUserId === (int)$userId) {
                $this->jsonResponse(['success' => false, 'error' => 'You cannot block yourself'], 422);
                return;
            }

            $targetUser = $this->model->getUserById($blockedUserId);
            if (!$targetUser) {
                $this->jsonResponse(['success' => false, 'error' => 'Target user not found'], 404);
                return;
            }

            $ok = $this->model->blockUser($userId, $blockedUserId);
            if (!$ok) {
                $this->jsonResponse(['success' => false, 'error' => 'Failed to block user'], 500);
                return;
            }

            $this->jsonResponse(['success' => true, 'message' => 'User blocked successfully']);
        } catch (Exception $e) {
            $this->jsonResponse(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * API: Unblock a user
     * POST /settings/unblockUser
     */
    public function unblockUser() {
        if (!$this->ensurePostMethod()) {
            return;
        }

        try {
            $userId = $_SESSION['user_id'];
            $input = $this->getJsonInput();
            $blockedUserId = (int)($input['blocked_user_id'] ?? 0);

            if ($blockedUserId <= 0) {
                $this->jsonResponse(['success' => false, 'error' => 'Invalid blocked user id'], 422);
                return;
            }

            $ok = $this->model->unblockUser($userId, $blockedUserId);
            if (!$ok) {
                $this->jsonResponse(['success' => false, 'error' => 'Failed to unblock user'], 500);
                return;
            }

            $this->jsonResponse(['success' => true, 'message' => 'User unblocked successfully']);
        } catch (Exception $e) {
            $this->jsonResponse(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * API: Get privacy/security settings
     * GET /settings/getPrivacySettings
     */
    public function getPrivacySettings() {
        try {
            $userId = $_SESSION['user_id'];
            $settings = $this->model->getPrivacySettings($userId);

            if (!$settings) {
                $settings = (object)[
                    'is_public' => 1,
                    'two_factor_enabled' => 0,
                    'two_factor_method' => null,
                    'two_factor_phone' => null,
                    'login_alerts_enabled' => 1
                ];
            }

            $this->jsonResponse(['success' => true, 'settings' => $settings]);
        } catch (Exception $e) {
            $this->jsonResponse(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * API: Update privacy/security settings
     * POST /settings/updatePrivacySettings
     */
    public function updatePrivacySettings() {
        if (!$this->ensurePostMethod()) {
            return;
        }

        try {
            $userId = $_SESSION['user_id'];
            $input = $this->getJsonInput();

            $payload = [
                'is_public' => !empty($input['is_public']) ? 1 : 0,
                'two_factor_enabled' => !empty($input['two_factor_enabled']) ? 1 : 0,
                'two_factor_method' => isset($input['two_factor_method']) ? trim((string)$input['two_factor_method']) : null,
                'two_factor_phone' => isset($input['two_factor_phone']) ? trim((string)$input['two_factor_phone']) : null,
                'login_alerts_enabled' => !empty($input['login_alerts_enabled']) ? 1 : 0
            ];

            if ($payload['two_factor_enabled'] === 1) {
                $validMethod = in_array($payload['two_factor_method'], ['app', 'sms'], true);
                if (!$validMethod) {
                    $this->jsonResponse(['success' => false, 'error' => 'Invalid two-factor method'], 422);
                    return;
                }

                if ($payload['two_factor_method'] === 'sms' && empty($payload['two_factor_phone'])) {
                    $this->jsonResponse(['success' => false, 'error' => 'Phone number is required for SMS 2FA'], 422);
                    return;
                }
            }

            $saved = $this->model->upsertPrivacySettings($userId, $payload);
            if (!$saved) {
                $this->jsonResponse(['success' => false, 'error' => 'Failed to save privacy settings'], 500);
                return;
            }

            $this->jsonResponse(['success' => true, 'message' => 'Privacy settings saved']);
        } catch (Exception $e) {
            $this->jsonResponse(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * API: Submit support request
     * POST /settings/submitSupportRequest
     */
    public function submitSupportRequest() {
        if (!$this->ensurePostMethod()) {
            return;
        }

        try {
            $userId = $_SESSION['user_id'];
            $input = $this->getJsonInput();

            $email = trim($input['email'] ?? '');
            $topic = trim($input['topic'] ?? 'technical');
            $message = trim($input['message'] ?? '');

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $this->jsonResponse(['success' => false, 'error' => 'Valid email is required'], 422);
                return;
            }

            if ($message === '') {
                $this->jsonResponse(['success' => false, 'error' => 'Support message is required'], 422);
                return;
            }

            $ticketId = $this->model->createSupportTicket($userId, $email, $topic, $message);
            if (!$ticketId) {
                $this->jsonResponse(['success' => false, 'error' => 'Failed to create support ticket'], 500);
                return;
            }

            $this->jsonResponse(['success' => true, 'message' => 'Support request submitted', 'ticket_id' => $ticketId]);
        } catch (Exception $e) {
            $this->jsonResponse(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * API: Update support ticket (only while editable)
     * POST /settings/updateSupportTicket
     */
    public function updateSupportTicket() {
        if (!$this->ensurePostMethod()) {
            return;
        }

        try {
            $userId = (int)($_SESSION['user_id'] ?? 0);
            if ($userId <= 0) {
                $this->jsonResponse(['success' => false, 'error' => 'Unauthorized'], 401);
                return;
            }

            $input = $this->getJsonInput();

            $ticketId = (int)($input['id'] ?? 0);
            $email = trim($input['email'] ?? '');
            $topic = trim($input['topic'] ?? 'technical');
            $message = trim($input['message'] ?? '');

            if ($ticketId <= 0) {
                $this->jsonResponse(['success' => false, 'error' => 'Valid ticket id is required'], 422);
                return;
            }

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $this->jsonResponse(['success' => false, 'error' => 'Valid email is required'], 422);
                return;
            }

            $allowedTopics = ['account', 'technical', 'billing', 'other'];
            if (!in_array($topic, $allowedTopics, true)) {
                $this->jsonResponse(['success' => false, 'error' => 'Invalid topic'], 422);
                return;
            }

            if ($message === '') {
                $this->jsonResponse(['success' => false, 'error' => 'Support message is required'], 422);
                return;
            }

            $updated = $this->model->updateSupportTicket($userId, $ticketId, $email, $topic, $message);
            if (!$updated) {
                $this->jsonResponse(['success' => false, 'error' => 'This ticket can no longer be edited'], 409);
                return;
            }

            $this->jsonResponse(['success' => true, 'message' => 'Support ticket updated']);
        } catch (Exception $e) {
            $this->jsonResponse(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * API: Submit problem report
     * POST /settings/submitProblemReport
     */
    public function submitProblemReport() {
        if (!$this->ensurePostMethod()) {
            return;
        }

        try {
            $userId = $_SESSION['user_id'];
            $input = $this->getJsonInput();

            $reportType = trim($input['report_type'] ?? 'bug');
            $details = trim($input['details'] ?? '');

            if ($details === '') {
                $this->jsonResponse(['success' => false, 'error' => 'Report details are required'], 422);
                return;
            }

            $allowed = ['bug', 'abuse', 'policy'];
            if (!in_array($reportType, $allowed, true)) {
                $this->jsonResponse(['success' => false, 'error' => 'Invalid report type'], 422);
                return;
            }

            $reportId = $this->model->createProblemReport($userId, $reportType, $details);
            if (!$reportId) {
                $this->jsonResponse(['success' => false, 'error' => 'Failed to submit problem report'], 500);
                return;
            }

            $this->jsonResponse(['success' => true, 'message' => 'Problem report submitted', 'report_id' => $reportId]);
        } catch (Exception $e) {
            $this->jsonResponse(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * API: Update problem report (only while editable)
     * POST /settings/updateProblemReport
     */
    public function updateProblemReport() {
        if (!$this->ensurePostMethod()) {
            return;
        }

        try {
            $userId = (int)($_SESSION['user_id'] ?? 0);
            if ($userId <= 0) {
                $this->jsonResponse(['success' => false, 'error' => 'Unauthorized'], 401);
                return;
            }

            $input = $this->getJsonInput();

            $reportId = (int)($input['id'] ?? 0);
            $reportType = trim($input['report_type'] ?? 'bug');
            $details = trim($input['details'] ?? '');

            if ($reportId <= 0) {
                $this->jsonResponse(['success' => false, 'error' => 'Valid report id is required'], 422);
                return;
            }

            $allowed = ['bug', 'abuse', 'policy'];
            if (!in_array($reportType, $allowed, true)) {
                $this->jsonResponse(['success' => false, 'error' => 'Invalid report type'], 422);
                return;
            }

            if ($details === '') {
                $this->jsonResponse(['success' => false, 'error' => 'Report details are required'], 422);
                return;
            }

            $updated = $this->model->updateProblemReport($userId, $reportId, $reportType, $details);
            if (!$updated) {
                $this->jsonResponse(['success' => false, 'error' => 'This report can no longer be edited'], 409);
                return;
            }

            $this->jsonResponse(['success' => true, 'message' => 'Problem report updated']);
        } catch (Exception $e) {
            $this->jsonResponse(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * API: Submit product feedback
     * POST /settings/submitFeedback
     */
    public function submitFeedback() {
        if (!$this->ensurePostMethod()) {
            return;
        }

        try {
            $userId = $_SESSION['user_id'];
            $input = $this->getJsonInput();

            $feedbackType = trim($input['feedback_type'] ?? 'other');
            $message = trim($input['message'] ?? '');

            if ($message === '') {
                $this->jsonResponse(['success' => false, 'error' => 'Feedback message is required'], 422);
                return;
            }

            $allowed = ['feature', 'ux', 'other'];
            if (!in_array($feedbackType, $allowed, true)) {
                $this->jsonResponse(['success' => false, 'error' => 'Invalid feedback type'], 422);
                return;
            }

            $feedbackId = $this->model->createFeedback($userId, $feedbackType, $message);
            if (!$feedbackId) {
                $this->jsonResponse(['success' => false, 'error' => 'Failed to submit feedback'], 500);
                return;
            }

            $this->jsonResponse(['success' => true, 'message' => 'Feedback submitted', 'feedback_id' => $feedbackId]);
        } catch (Exception $e) {
            $this->jsonResponse(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }
}
?>