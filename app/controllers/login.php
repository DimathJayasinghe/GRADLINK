<?php
class login extends Controller{
    protected $loginModel = null;
    public function __construct(){
        SessionManager::redirectIfLoggedIn("/mainfeed");
        $this->loginModel = $this->Model('M_login');
        SessionManager::ensureStarted();
    }

    public function index(){
        $this->redirect("/auth");
    }

    public function alumni(){
        if ($_SERVER['REQUEST_METHOD'] === 'POST'){
            $this->loginAlumniHandler();
            return;
        }
        SessionManager::redirectIfLoggedIn("/mainfeed");
        $data = [
            'email' =>'',
            'password' => '',
            'errors' => []
        ];
        $this->view('auth/login/v_login_alumni',$data);
    }

    public function undergrad(){
        if ($_SERVER['REQUEST_METHOD'] === 'POST'){
            $this->loginUndergradHandler();
            return;
        }
        SessionManager::redirectIfLoggedIn("/mainfeed");
        $data = [
            'email' =>'',
            'password' => '',
            'errors' => []
        ];
        $this->view('auth/login/v_login_undergrad',$data);
    }

    private function loginAlumniHandler(){
        $_POST = Sanitizer::sanitizeArray($_POST);

        // Prepare data and error array
        $data = [
            'email' => $_POST['email'] ?? '',
            'password' => $_POST['password'] ?? '',
            'errors' => []
        ];

        // Validation
        $this->validateLogin($data);
        if (empty($data['errors'])){
            // call model method to verify alumni credentialas
            $user = $this->loginModel->loginAlumni($data['email'],$data['password']);

            if($user){
                // Create session and redirect to main feed
                SessionManager::createUserSession($user);
                SessionManager::setFlash('success', 'Welcome back, ' . ($user->name ?? 'Alumni') . '!');
                SessionManager::redirectIfLoggedIn("/mainfeed");
            }else{
                 // Invalid credentials
                $data['errors'][] = 'Invalid email or password';
            }
        }
        $this->view('auth/login/v_login_alumni', $data);
    }
    
    private function loginUndergradHandler(){
        SessionManager::redirectIfLoggedIn("/mainfeed");
        $_POST = Sanitizer::sanitizeArray($_POST);

        // Prepare data and error array
        $data = [
            'email' => $_POST['email'] ?? '',
            'password' => $_POST['password'] ?? '',
            'errors' => []
        ];

        // Validation
        $this->validateLogin($data);
        if (empty($data['errors'])){
            // Call model method to verify undergrad credentials
            $user = $this->loginModel->loginUndergrad($data['email'], $data['password']);

            if($user){
                // Create session and redirect to main feed
                SessionManager::createUserSession($user);
                SessionManager::setFlash('success', 'Welcome back, ' . ($user->name ?? 'Student') . '!');
                SessionManager::redirectIfLoggedIn("/mainfeed");
            }else{
                // Invalid credentials
                $data['errors'][] = 'Invalid email or password';
            }
        }
        $this->view('auth/login/v_login_undergrad', $data);
        
    }

    private function validateLogin(&$data){
        if (Sanitizer::isEmpty($data['email'])) {
            $data['errors'][] = 'Email is required';
        } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $data['errors'][] = 'Invalid email format';
        }
        if (Sanitizer::isEmpty($data['password'])) {
            $data['errors'][] = 'Password is required';
        }
    }
   
}
?>