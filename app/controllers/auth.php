<?php
class auth extends Controller
{
    protected $authModel;
    protected $sessionHandler;
    // Constructor to initialize the model and session
    public function __construct()
    {
        $this->authModel = $this->model('M_auth');
        // Start session if not already started
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }
    public function index()
    {
        // Redirect to main feed if already logged in
        SessionManager::redirectIfLoggedIn("/mainfeed");
        $data = [];
        $this->view('auth/v_auth', $data);
    }
}
