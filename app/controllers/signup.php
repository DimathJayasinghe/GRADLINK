<?php
class Signup extends Controller{
    protected $signupModel;
    
    public function __construct() {
        $this->signupModel = $this->model('M_signup');
    }

    public function index() {
        SessionManager::redirectIfLoggedIn("/mainfeed");
        $this->redirect("/auth");
    }

    public function alumni(){
        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            $this->signupAlumniHandler();
            return;
        }
        SessionManager::redirectIfLoggedIn("/mainfeed");
        $data = [
            'email' =>'',
            'password' => '',
            'confirm_password' => '',
            'full_name' => '',
            'graduation_year' => '',
            'errors' => []
        ];
        $this->view('auth/signup/v_signup_alumni',$data);
    }
    
    public function undergrad(){
        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            $this->signupUndergradHandler();
            return;
        }
        SessionManager::redirectIfLoggedIn("/mainfeed");
        $data = [
            'email' =>'',
            'password' => '',
            'confirm_password' => '',
            'full_name' => '',
            'graduation_year' => '',
            'errors' => []
        ];
        $this->view('auth/signup/v_signup_undergrad',$data);
    }

    private function signupAlumniHandler() {
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
            'batch_no' => $_POST['graduation_year'] ?? '',
            'nic' => $_POST['nic'] ?? '',
            'bio' => $_POST['bio'] ?? '',
            'skills' => $_POST['skills'] ?? [],
            'errors' => []
        ];
        
        $this->validateSignup($data);
        
        // If no errors, register the user
        if (empty($data['errors'])) {
            // Hash password
            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
            
            // Set role for alumni
            $data['role'] = 'alumni';
            
            // Store skills as JSON string
            $data['skills_json'] = !empty($data['skills']) ? json_encode($data['skills']) : null;
            
            // Register the user
            $userId = $this->signupModel->registerAlumni($data);
            
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
                $this->view('auth/signup/v_signup_alumni', $data);
            }
        } else {
            // Load the view with errors
            $this->view('auth/signup/v_signup_alumni', $data);
        }
    }
    
    private function signupUndergradHandler(){
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

    private function saveProfilePic($userId) {
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
                    $newFilename = $userId . '_' . time() . '.' . $fileExt;
                    $destination = APPROOT . '/../storage/profile_pic/' . $newFilename;
                    
                    // Create directory if it doesn't exist
                    if (!is_dir(dirname($destination))) {
                        mkdir(dirname($destination), 0755, true);
                    }
                    
                    // Verify it's actually an image
                    $imageInfo = getimagesize($file['tmp_name']);
                    if ($imageInfo !== false) {
                        // Move the uploaded file to destination
                        if (move_uploaded_file($file['tmp_name'], $destination)) {
                            // Update user's profile_image in database
                            if ($this->signupModel->updateProfileImage($userId, $newFilename)) {
                                return $newFilename;
                            }
                        }
                    }
                }
            }
        }
        
        return false;
    }

    private function validateSignup(&$data){
        // Validate inputs
        // Email validation
        if (empty($data['email'])) {
            $data['errors'][] = 'Please enter an email address';
        } else if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $data['errors'][] = 'Please enter a valid email address';
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
        
        // Batch number validation
        if (empty($data['batch_no'])) {
            $data['errors'][] = 'Please select your batch';
        }
    }
}
?>