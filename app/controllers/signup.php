<?php

class Signup extends Controller
{
    protected $signupModel;


    public function __construct()
    {
        $this->signupModel = $this->model('M_signup');
    }

    public function index()
    {
        SessionManager::redirectIfLoggedIn("/mainfeed");
        $this->redirect("/auth");
    }

    public function alumni()
    {
        // Admin/special-alumni approval via GET /signup/alumni?id={pendingId}
        if ($_SERVER['REQUEST_METHOD'] === 'GET' && $this->getQueryParam('id', null) !== null) {
            $pendingIdRaw = $this->getQueryParam('id', null);
            $pendingId = is_numeric($pendingIdRaw) ? (int)$pendingIdRaw : null;
            if (!$pendingId) {
                SessionManager::setFlash('error', 'Invalid approval request ID.');
                // Decide redirect target
                $target = (SessionManager::hasRole('admin') ? '/admin/verifications' : '/alumni/approve');
                $this->redirect($target);
                return;
            }

            // Ensure privileges: admin OR special alumni
            if (!SessionManager::isLoggedIn() || (!SessionManager::hasRole('admin') && !SessionManager::isSpecialAlumni())) {
                // Not authorized
                $this->redirect('/auth');
                return;
            }

            $newUserId = $this->signupModel->approveAlumni($pendingId);
            if ($newUserId) {
                SessionManager::setFlash('success', 'Alumni approved successfully. New User ID: ' . $newUserId);
            } else {
                SessionManager::setFlash('error', 'Approval failed. It may already be approved or invalid.');
            }
            // Redirect back depending on actor
            $redirectTo = SessionManager::hasRole('admin') ? '/admin/verifications' : '/alumni/approve';
            $this->redirect($redirectTo);
            return;
        }
        // Admin/special-alumni rejection via GET /signup/alumni?reject_id={pendingId}
        if ($_SERVER['REQUEST_METHOD'] === 'GET' && $this->getQueryParam('reject_id', null) !== null) {
            $pendingIdRaw = $this->getQueryParam('reject_id', null);
            $pendingId = is_numeric($pendingIdRaw) ? (int)$pendingIdRaw : null;
            if (!$pendingId) {
                SessionManager::setFlash('error', 'Invalid rejection request ID.');
                $target = (SessionManager::hasRole('admin') ? '/admin/verifications' : '/alumni/approve');
                $this->redirect($target);
                return;
            }

            if (!SessionManager::isLoggedIn() || (!SessionManager::hasRole('admin') && !SessionManager::isSpecialAlumni())) {
                $this->redirect('/auth');
                return;
            }

            $ok = $this->signupModel->rejectPendingAlumni($pendingId);
            if ($ok) {
                SessionManager::setFlash('warning', 'Pending alumni request rejected.');
            } else {
                SessionManager::setFlash('error', 'Rejection failed. Please try again.');
            }
            $redirectTo = SessionManager::hasRole('admin') ? '/admin/verifications' : '/alumni/approve';
            $this->redirect($redirectTo);
            return;
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->signupAlumniHandler();
            return;
        }
        SessionManager::redirectIfLoggedIn("/mainfeed");
        $data = [
            'email' => '',
            'password' => '',
            'confirm_password' => '',
            'full_name' => '',
            'graduation_year' => '',
            'errors' => []
        ];
        $this->view('auth/signup/v_signup_alumni', $data);
    }

    public function undergrad()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->signupUndergradHandler();
            return;
        }
        SessionManager::redirectIfLoggedIn("/mainfeed");
        $data = [
            'email' => '',
            'password' => '',
            'confirm_password' => '',
            'full_name' => '',
            'graduation_year' => '',
            'errors' => []
        ];
        $this->view('auth/signup/v_signup_undergrad', $data);
    }

    private function signupAlumniHandler()
    {
        // Redirect if already logged in
        SessionManager::redirectIfLoggedIn("/mainfeed");

        // Sanitize POST data
        $_POST = Sanitizer::sanitizeArray($_POST);

        // Initialize data array with form values
        $data = [
            'name' => $_POST['full_name'] ?? '',
            'email' => $_POST['email'] ?? '',
            'password' => $_POST['password'] ?? '',
            'confirm_password' => $_POST['confirm_password'] ?? '',
            'display_name' => $_POST['display_name'] ?? '',
            'gender' => isset($_POST['gender']) ? strtolower(trim($_POST['gender'])) : null,
            'batch_no' => $_POST['graduation_year'] ?? '',
            'nic' => $_POST['nic'] ?? '',
            'bio' => $_POST['bio'] ?? '',
            'explain_yourself' => $_POST['explain_yourself'] ?? '',
            'skills' => $_POST['skills'] ?? [],
            'errors' => []
        ];

        $this->validateSignup($data);
        // Gender validation for alumni
        if (empty($data['gender']) || !in_array($data['gender'], ['male', 'female'], true)) {
            $data['errors'][] = 'Please select your gender';
        }
        // Also prevent duplicate pending requests
        if (empty($data['errors']) && $this->signupModel->findPendingAlumniByEmail($data['email'])) {
            $data['errors'][] = 'An approval request for this email is already pending.';
        }

        // If no errors, register the user
        if (empty($data['errors'])) {
            // Hash password
            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);

            // Store skills as JSON string
            $data['skills_json'] = !empty($data['skills']) ? json_encode($data['skills']) : null;

            // Create a pending alumni record instead of direct user
            $pendingId = $this->signupModel->registerPendingAlumni($data);

            if ($pendingId) {
                // Handle profile pic upload if a file was submitted and save to pending table
                $profileImage = $this->saveProfilePic($pendingId, true);

                // Show success message and redirect to alumni login
                $viewData = [
                    'pending_success' => true,
                    'login_url' => URLROOT . '/login/alumni',
                    'errors' => []
                ];
                $this->view('auth/signup/v_signup_alumni', $viewData);
            } else {
                $data['errors'][] = 'Something went wrong. Please try again.';
                $this->view('auth/signup/v_signup_alumni', $data);
            }
        } else {
            // Load the view with errors
            $this->view('auth/signup/v_signup_alumni', $data);
        }
    }

    private function signupUndergradHandler()
    {
        // Redirect if already logged in
        SessionManager::redirectIfLoggedIn("/mainfeed");

        // Sanitize POST data
        $_POST = Sanitizer::sanitizeArray($_POST);

        // Initialize data array with form values
        $data = [
            'name' => $_POST['full_name'] ?? '',
            'email' => $_POST['email'] ?? '',
            'password' => $_POST['password'] ?? '',
            'confirm_password' => $_POST['confirm_password'] ?? '',
            'display_name' => $_POST['display_name'] ?? '',
            'gender' => isset($_POST['gender']) ? strtolower(trim($_POST['gender'])) : null,
            'batch_no' => $_POST['batch_no'] ?? '',
            'student_id' => $_POST['student_id'] ?? '',
            'bio' => $_POST['bio'] ?? '',
            'skills' => $_POST['skills'] ?? [],
            'errors' => []
        ];

        // Validate inputs
        $this->validateSignup($data);

        // Additional validation for student ID (specific to undergrads)
        if (empty($data['student_id'])) {
            $data['errors'][] = 'Please enter your student ID';
        } else if (!preg_match('/^\d{4}\/(?:cs|is)\/\d{3}$/i', $data['student_id'])) {
            $data['errors'][] = 'Student ID must be in the format YYYY/cs/XXX or YYYY/is/XXX';
        }

        // Gender validation for undergrads
        if (empty($data['gender']) || !in_array($data['gender'], ['male', 'female'], true)) {
            $data['errors'][] = 'Please select your gender';
        }

        // Student email format validation: <year><cs|is><xxx>@stu.ucsc.cmb.ac.lk
        if (empty($data['email'])) {
            // already handled in validateSignup but keeping structure consistent
        } else if (!preg_match('/^[0-9]{4}(?:cs|is)[0-9]{3}@stu\.ucsc\.cmb\.ac\.lk$/i', $data['email'])) {
            $data['errors'][] = 'Student email must match e.g. 20XXcsXXXX@stu.ucsc.cmb.ac.lk';
        }

        // If no errors, register the user
        if (empty($data['errors'])) {
            // Hash password
            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);

            // Set role for undergrad
            $data['role'] = 'undergrad';

            // Store skills as JSON string
            $data['skills_json'] = !empty($data['skills']) ? json_encode($data['skills']) : null;

            // Register the user
            $userId = $this->signupModel->registerUndergrad($data);

            if ($userId) {
                // Handle profile pic upload if a file was submitted
                $profileImage = $this->saveProfilePic($userId);

                // Create user session and redirect
                $user = $this->signupModel->getUserById($userId);
                SessionManager::createUserSession($user);

                // Redirect to main feed
                $this->redirect("/mainfeed");
            } else {
                $data['errors'][] = 'Something went wrong. Please try again.';
                $this->view('auth/signup/v_signup_undergrad', $data);
            }
        } else {
            // Load the view with errors
            $this->view('auth/signup/v_signup_undergrad', $data);
        }
    }

    private function saveProfilePic($entityId, $isPending = false)
    {
        // Initialize return value
        $newFilename = false;

        if (!empty($_FILES['profile_image']['name'])) {
            $file = $_FILES['profile_image'];

            // Check for errors
            if ($file['error'] === 0) {
                // Get file extension
                $fileExt = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

                // Only allow certain image formats
                $allowed = ['jpg', 'jpeg', 'png'];

                if (in_array($fileExt, $allowed)) {
                    // Create unique filename
                    $newFilename = $entityId . '_' . time() . '.' . $fileExt;
                    $destination = APPROOT . '/storage/profile_pic/' . $newFilename;

                    // Create directory if it doesn't exist
                    if (!is_dir(dirname($destination))) {
                        mkdir(dirname($destination), 0755, true);
                    }

                    // Verify it's actually an image
                    $imageInfo = getimagesize($file['tmp_name']);
                    if ($imageInfo !== false) {
                        // Move the uploaded file to destination
                        if (move_uploaded_file($file['tmp_name'], $destination)) {
                            // Update profile_image in the appropriate table
                            if ($isPending) {
                                if ($this->signupModel->updatePendingProfileImage($entityId, $newFilename)) {
                                    return $newFilename;
                                }
                            } else {
                                if ($this->signupModel->updateProfileImage($entityId, $newFilename)) {
                                    return $newFilename;
                                }
                            }
                        }
                    }
                }
            }
        }

        return false;
    }

    private function validateSignup(&$data)
    {
        // Validate inputs
        // Email validation
        if (empty($data['email'])) {
            $data['errors'][] = 'Please enter an email address';
        } else if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $data['errors'][] = 'Please enter a valid email address';
        } else if (($suspension = $this->signupModel->getActiveSuspensionByEmail($data['email']))) {
            $message = 'This account is suspended and cannot be used for registration.';
            $reason = trim((string)($suspension->reason ?? ''));
            if ($reason !== '') {
                $message .= ' Reason: ' . $reason;
            }
            $data['errors'][] = $message;
        } else if ($this->signupModel->findUserByEmail($data['email'])) {
            $data['errors'][] = 'Email already in use';
        }

        // Name validation
        if (empty($data['name'])) {
            $data['errors'][] = 'Please enter your full name';
        }

        // Password validation
        if (empty($data['password'])) {
            $data['errors'][] = 'Please enter a password';
        } else if (strlen($data['password']) < 6) {
            $data['errors'][] = 'Password must be at least 6 characters';
        }

        // Confirm password
        if ($data['password'] !== $data['confirm_password']) {
            $data['errors'][] = 'Passwords do not match';
        }

        // NIC format validation (if provided)
        if (isset($data['nic']) && $data['nic'] !== '') {
            if (!preg_match('/^\d{12}$/', $data['nic'])) {
                $data['errors'][] = 'NIC must be a 12-digit number';
            }
        }

        // Batch number validation
        if (empty($data['batch_no'])) {
            $data['errors'][] = 'Please select your batch';
        }
    }


    public function sendOTP()
    {
        // Implement your OTP sending logic here
        // For example, you can send an email or SMS with the OTP
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode([
                "success" => false,
                "message" => "Invalid request method"
            ]);
            exit;
        }
        if (!isset($_POST['email']) || !isset($_POST['purpose'])) {
            http_response_code(400);
            echo json_encode([
                "success" => false,
                "message" => "Missing required fields"
            ]);
            exit;
        }

        $email = $_POST['email'];
        $purpose = $_POST['purpose'];
        $otp = random_int(100000, 999999);

        $done = $this->sendOTPEmail($email, $purpose, $otp);

        if ($done) {
            $this->signupModel->saveOTP($email, $otp, $purpose);
            http_response_code(200);
            echo json_encode([
                "success" => true,
                "message" => "OTP sent to your email"
            ]);
            exit;
        } else {
            http_response_code(500);
            echo json_encode([
                "success" => false,
                "message" => "Error while sending OTP"
            ]);
            exit;
        }
    }

    public function verifyOTP()
    {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode([
                "success" => false,
                "message" => "Invalid request method"
            ]);
            exit;
        }

        if (!isset($_POST['email']) || !isset($_POST['otp']) || !isset($_POST['purpose'])) {
            http_response_code(400);
            echo json_encode([
                "success" => false,
                "message" => "Missing required fields"
            ]);
            exit;
        }

        $email = trim($_POST['email']);
        $otp = trim($_POST['otp']);
        $purpose = trim($_POST['purpose']);

        // Validate OTP format (6 digits)
        if (!preg_match('/^\d{6}$/', $otp)) {
            http_response_code(400);
            echo json_encode([
                "success" => false,
                "message" => "Invalid OTP format"
            ]);
            exit;
        }

        // Verify OTP using model
        $result = $this->signupModel->verifyOTP($email, $otp, $purpose);

        if ($result['success']) {
            http_response_code(200);
        } else {
            http_response_code(400);
        }

        echo json_encode($result);
        exit;
    }

    private function sendOTPEmail($email, $purpose, $otp)
    {
        return EmailHandler::sendOtpEmail((string)$email, (string)$purpose, (int)$otp);
    }
}
