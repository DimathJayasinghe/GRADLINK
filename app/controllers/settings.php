<?php
class settings extends Controller{
    private $model;
    public function __construct(){
        SessionManager:: redirectToAuthIfNotLoggedIn();
        $this->model = $this->model('M_settings');
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
        $data = [
            'section' => 'privacyandsafety'
        ];
        $this->view('settings/v_settings', $data);
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
        $data = [
            'section' => 'helpandsupport'
        ];
        $this->view('settings/v_settings', $data);
    }

    /**
     * API: Update user name and display name
     * POST /settings/updateName
     * Body: {"name": "Full Name", "display_name": "Display Name"}
     */
    public function updateName() {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'error' => 'Invalid request method']);
            return;
        }

        try {
            $input = json_decode(file_get_contents('php://input'), true);
            $userId = $_SESSION['user_id'];
            
            $name = trim($input['name'] ?? '');
            $displayName = trim($input['display_name'] ?? '');
            
            if (empty($name)) {
                echo json_encode(['success' => false, 'error' => 'Name cannot be empty']);
                return;
            }
            
            $result = $this->model->updateName($userId, $name, $displayName);
            
            if ($result) {
                // Update session
                $_SESSION['user_name'] = $name;
                if (!empty($displayName)) {
                    $_SESSION['display_name'] = $displayName;
                }
                
                echo json_encode(['success' => true, 'message' => 'Name updated successfully']);
            } else {
                echo json_encode(['success' => false, 'error' => 'Failed to update name']);
            }
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    /**
     * API: Update user bio
     * POST /settings/updateBio
     * Body: {"bio": "User bio text"}
     */
    public function updateBio() {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'error' => 'Invalid request method']);
            return;
        }

        try {
            $input = json_decode(file_get_contents('php://input'), true);
            $userId = $_SESSION['user_id'];
            
            $bio = trim($input['bio'] ?? '');
            
            $result = $this->model->updateBio($userId, $bio);
            
            if ($result) {
                echo json_encode(['success' => true, 'message' => 'Bio updated successfully']);
            } else {
                echo json_encode(['success' => false, 'error' => 'Failed to update bio']);
            }
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    /**
     * API: Update user email
     * POST /settings/updateEmail
     * Body: {"current_email": "current@email.com", "new_email": "new@email.com", "password": "password"}
     */
    public function updateEmail() {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'error' => 'Invalid request method']);
            return;
        }

        try {
            $input = json_decode(file_get_contents('php://input'), true);
            $userId = $_SESSION['user_id'];
            
            $currentEmail = trim($input['current_email'] ?? '');
            $newEmail = trim($input['new_email'] ?? '');
            $password = $input['password'] ?? '';
            
            // Validate inputs
            if (empty($newEmail) || empty($password)) {
                echo json_encode(['success' => false, 'error' => 'New email and password are required']);
                return;
            }
            
            if (!filter_var($newEmail, FILTER_VALIDATE_EMAIL)) {
                echo json_encode(['success' => false, 'error' => 'Invalid email format']);
                return;
            }
            
            // Verify password
            $user = $this->model->getUserById($userId);
            if (!$user || !password_verify($password, $user->password)) {
                echo json_encode(['success' => false, 'error' => 'Incorrect password']);
                return;
            }
            
            // Check if email already exists
            if ($this->model->emailExists($newEmail, $userId)) {
                echo json_encode(['success' => false, 'error' => 'Email already in use']);
                return;
            }
            
            $result = $this->model->updateEmail($userId, $newEmail);
            
            if ($result) {
                echo json_encode(['success' => true, 'message' => 'Email updated successfully']);
            } else {
                echo json_encode(['success' => false, 'error' => 'Failed to update email']);
            }
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    /**
     * API: Change user password
     * POST /settings/changePassword
     * Body: {"current_password": "old", "new_password": "new", "confirm_password": "new"}
     */
    public function changePassword() {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'error' => 'Invalid request method']);
            return;
        }

        try {
            $input = json_decode(file_get_contents('php://input'), true);
            $userId = $_SESSION['user_id'];
            
            $currentPassword = $input['current_password'] ?? '';
            $newPassword = $input['new_password'] ?? '';
            $confirmPassword = $input['confirm_password'] ?? '';
            
            // Validate inputs
            if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
                echo json_encode(['success' => false, 'error' => 'All fields are required']);
                return;
            }
            
            if ($newPassword !== $confirmPassword) {
                echo json_encode(['success' => false, 'error' => 'New passwords do not match']);
                return;
            }
            
            if (strlen($newPassword) < 6) {
                echo json_encode(['success' => false, 'error' => 'New password must be at least 6 characters']);
                return;
            }
            
            // Verify current password
            $user = $this->model->getUserById($userId);
            if (!$user || !password_verify($currentPassword, $user->password)) {
                echo json_encode(['success' => false, 'error' => 'Current password is incorrect']);
                return;
            }
            
            // Hash new password
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            
            $result = $this->model->updatePassword($userId, $hashedPassword);
            
            if ($result) {
                echo json_encode(['success' => true, 'message' => 'Password changed successfully']);
            } else {
                echo json_encode(['success' => false, 'error' => 'Failed to change password']);
            }
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    /**
     * API: Delete user account permanently
     * POST /settings/deleteAccount
     * Body: {"password": "password", "confirmation": "DELETE"}
     */
    public function deleteAccount() {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'error' => 'Invalid request method']);
            return;
        }

        try {
            SessionManager::ensureStarted();
            $input = json_decode(file_get_contents('php://input'), true);
            
            if (!isset($_SESSION['user_id'])) {
                echo json_encode(['success' => false, 'error' => 'User not logged in']);
                return;
            }
            
            $userId = $_SESSION['user_id'];
            $password = $input['password'] ?? '';
            $confirmation = trim($input['confirmation'] ?? '');
            
            // Validate inputs
            if (empty($password)) {
                echo json_encode(['success' => false, 'error' => 'Password is required']);
                return;
            }
            
            if ($confirmation !== 'DELETE') {
                echo json_encode(['success' => false, 'error' => 'Please type DELETE to confirm']);
                return;
            }
            
            // Verify password
            $user = $this->model->getUserById($userId);
            
            if (!$user) {
                echo json_encode(['success' => false, 'error' => 'User not found']);
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
                echo json_encode(['success' => false, 'error' => 'Incorrect password']);
                return;
            }
            
            // Delete account
            $result = $this->model->deleteAccount($userId);
            
            if ($result) {
                // Destroy session and logout
                SessionManager::destroySession();
                
                echo json_encode(['success' => true, 'message' => 'Account deleted successfully']);
            } else {
                echo json_encode(['success' => false, 'error' => 'Failed to delete account']);
            }
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    /**
     * API: Get current user profile data
     * GET /settings/getUserData
     */
    public function getUserData() {
        header('Content-Type: application/json');
        
        try {
            $userId = $_SESSION['user_id'];
            $user = $this->model->getUserById($userId);
            
            if ($user) {
                // Remove sensitive data
                unset($user->password);
                
                echo json_encode(['success' => true, 'user' => $user]);
            } else {
                echo json_encode(['success' => false, 'error' => 'User not found']);
            }
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }
}
?>