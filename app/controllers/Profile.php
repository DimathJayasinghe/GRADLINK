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

        if($_SERVER['REQUEST_METHOD'] == "POST") {
            var_dump($_POST);
            echo "POST";
            return;
        }

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

  
    public function addCertificate()
{
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); return; }
        $name = trim($_POST['certificate_name'] ?? '');
        $issuer = trim($_POST['certificate_issuer'] ?? '');
        $issued_date = trim($_POST['certificate_date'] ?? '');
        $certificate_file = null;
        if (!empty($_FILES['certificate_file']['name']) && is_uploaded_file($_FILES['certificate_file']['tmp_name'])) {
            $ext = pathinfo($_FILES['certificate_file']['name'], PATHINFO_EXTENSION);
            $certificate_file = time() . '_' . substr(sha1($_FILES['certificate_file']['name'].random_bytes(4)),0,8) . '.' . $ext;
            $targetDir = APPROOT . '/storage/certificates';
            if(!is_dir($targetDir)) @mkdir($targetDir,0775,true);
            $dest = $targetDir . '/' . $certificate_file;
            if(!move_uploaded_file($_FILES['certificate_file']['tmp_name'],$dest)) $certificate_file=null;
        }
        if ($name !== '' && $issuer !== '' && $issued_date !== '' && $certificate_file !== null) {
            $this->Model->createCertificate($_SESSION['user_id'], $name, $issuer, $issued_date, $certificate_file);
        }
        $this->redirect('/profile');
}
}

