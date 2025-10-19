<?php
class Profile extends Controller{
    protected $Model;
   
    public function __construct() {
        SessionManager:: redirectToAuthIfNotLoggedIn();
        $this->Model = $this->model('M_Profile');
    }

    public function index(){
        $user_id = $this->getQueryParam('userid', null);
        if (!$user_id){
            $user_id = $_SESSION['user_id'];
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

