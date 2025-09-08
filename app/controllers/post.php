<?php
class Post extends Controller
{
    private $m;
    public function __construct()
    {
        SessionManager::redirectToAuthIfNotLoggedIn();
        $this->m = $this->model('M_post');
    }
    public function index()
    {
        $this->redirect("/mainfeed");
    }
    public function create()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); return; }
        $c = trim($_POST['content'] ?? '');
        $imgName = null;
        if (!empty($_FILES['image']['name']) && is_uploaded_file($_FILES['image']['tmp_name'])) {
            $allowed = ['image/jpeg'=>'jpg','image/png'=>'png','image/gif'=>'gif','image/webp'=>'webp'];
            $mime = mime_content_type($_FILES['image']['tmp_name']);
            if(isset($allowed[$mime])){
                if($_FILES['image']['size'] <= 5*1024*1024){ // 5MB limit
                    $cleanBase = preg_replace('/[^A-Za-z0-9._-]/','', $_FILES['image']['name']);
                    $ext = pathinfo($cleanBase, PATHINFO_EXTENSION);
                    if(!$ext) $ext = $allowed[$mime];
                    $imgName = time() . '_' . substr(sha1($cleanBase.random_bytes(4)),0,8) . '.' . $ext;
                    $targetDir = APPROOT . '/storage/posts';
                    if(!is_dir($targetDir)) @mkdir($targetDir,0775,true);
                    $dest = $targetDir . '/' . $imgName;
                    if(!move_uploaded_file($_FILES['image']['tmp_name'],$dest)) $imgName=null;
                }
            }
        }
        if ($c !== '') { $this->m->createPost($_SESSION['user_id'], $c, $imgName); }
        $this->redirect('/post');
    }
    public function comment($pid)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            return;
        }
        $c = trim($_POST['content'] ?? '');
        if ($c !== '') {
            $this->m->addComment($pid, $_SESSION['user_id'], $c);
        }
        header('Content-Type: application/json');
        echo json_encode(['ok' => true, 'comments' => $this->m->getComments($pid)]);
    }
    public function comments($pid)
    {
        header('Content-Type: application/json');
        echo json_encode($this->m->getComments($pid));
    }
    public function like($pid)
    {
        if (!is_numeric($pid) || (int)$pid <= 0) {
            header('Content-Type: application/json');
            echo json_encode(['status' => 'error', 'message' => 'Invalid post ID']);
            return;
        }
        
        if (!isset($_SESSION['user_id'])) {
            header('Content-Type: application/json');
            echo json_encode(['status' => 'error', 'message' => 'User not logged in']);
            return;
        }
        
        $result = $this->m->toggleLike($pid, $_SESSION['user_id']);
        
        // Check for error status
        if (strpos($result, 'error_') === 0) {
            header('Content-Type: application/json');
            echo json_encode(['status' => 'error', 'message' => str_replace('error_', '', $result)]);
            return;
        }
        
        header('Content-Type: application/json');
        echo json_encode(['status' => $result]);
    }
    // --- ADMIN CONTENT MANAGEMENT ENDPOINTS ---
    // Only allow access if user is admin (add your own admin check logic)
    public function admin_list() {
        if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'admin') {
            http_response_code(403); echo 'Forbidden'; return;
        }
        $status = $_GET['status'] ?? 'all';
        $search = $_GET['search'] ?? '';
        $posts = $this->m->adminGetPosts($status, $search);
        header('Content-Type: application/json');
        echo json_encode(['posts' => $posts]);
    }

    public function admin_approve($id) {
        if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'admin') {
            http_response_code(403); echo 'Forbidden'; return;
        }
        $ok = $this->m->adminApprovePost($id);
        header('Content-Type: application/json');
        echo json_encode(['ok' => $ok]);
    }

    public function admin_reject($id) {
        if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'admin') {
            http_response_code(403); echo 'Forbidden'; return;
        }
        $ok = $this->m->adminRejectPost($id);
        header('Content-Type: application/json');
        echo json_encode(['ok' => $ok]);
    }

    public function admin_delete($id) {
        if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'admin') {
            http_response_code(403); echo 'Forbidden'; return;
        }
        $ok = $this->m->adminDeletePost($id);
        header('Content-Type: application/json');
        echo json_encode(['ok' => $ok]);
    }
}

