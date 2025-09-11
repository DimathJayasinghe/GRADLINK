<?php
class Profile extends Controller{
    protected $Model;
   
    public function __construct() {
        SessionManager:: redirectToAuthIfNotLoggedIn();
        $this->Model = $this->model('M_Profile');
    }
    
    public function index() {
        if (SessionManager::hasRole('undergraduate')) {
            $this->watch($_SESSION['user_id']);
            return;
        } else if (SessionManager::hasRole('alumni')) {
            $this->watch($_SESSION['user_id']);
            return;
        }
        $this->view('errors/_404', []);
    }

    public function watch($user_id){
        if (!$user_id){
            SessionManager:: redirectIfLoggedIn('/profile/'. $_SESSION['user_id']);
        }
        // handle other user profile view
        $user = $this->Model->getUser($user_id);
        if ($user == 1) {
            $data['userDetails'] = $this->Model->getUserDetails($user_id);
            $data['certificates'] = $this->Model->getCertificates($user_id);
            $data['posts'] = $this->Model->getPosts($user_id);
            $data['projects'] = $this->Model->getProjects($user_id);
            
            // Add liked status to posts - same as in mainfeed
            $postModel = $this->model('M_post');
            $current_user_id = $_SESSION['user_id'];
            foreach ($data['posts'] as $p) {
                $p->liked = $postModel->isLiked($p->id, $current_user_id);
            }

            $this->view('profiles/v_profile', $data);
            return;
        }
        $this->view('errors/_404', []);
    }
}

