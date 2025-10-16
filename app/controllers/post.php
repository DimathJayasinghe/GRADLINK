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
                if($_FILES['image']['size'] <= 2*1024*1024){ // 5MB limit
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
    
    /**
     * Edit a post (content and/or image)
     */
    public function edit($id = null)
    {
        header('Content-Type: application/json');
        
        // Check if user is logged in
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['status' => 'error', 'message' => 'User not logged in']);
            return;
        }
        
        // Validate post ID
        if (!is_numeric($id) || (int)$id <= 0) {
            echo json_encode(['status' => 'error', 'message' => 'Invalid post ID']);
            return;
        }
        
        // Check if post exists and user owns it
        $post = $this->m->getPostById($id);
        if (!$post) {
            echo json_encode(['status' => 'error', 'message' => 'Post not found']);
            return;
        }
        
        // Check if user is the owner of the post
        if ($post->user_id != $_SESSION['user_id']) {
            echo json_encode(['status' => 'error', 'message' => 'You can only edit your own posts']);
            return;
        }
        
        // Get post content
        $content = isset($_POST['content']) ? trim($_POST['content']) : null;
        if (empty($content)) {
            echo json_encode(['status' => 'error', 'message' => 'Post content cannot be empty']);
            return;
        }
        
        // Process image if uploaded
        $imgName = null;
        if (!empty($_FILES['image']['name']) && is_uploaded_file($_FILES['image']['tmp_name'])) {
            $allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/gif' => 'gif', 'image/webp' => 'webp'];
            $mime = mime_content_type($_FILES['image']['tmp_name']);
            
            if (isset($allowed[$mime])) {
                if ($_FILES['image']['size'] <= 2 * 1024 * 1024) { // 2MB limit
                    $cleanBase = preg_replace('/[^A-Za-z0-9._-]/', '', $_FILES['image']['name']);
                    $ext = pathinfo($cleanBase, PATHINFO_EXTENSION);
                    if (!$ext) $ext = $allowed[$mime];
                    
                    $imgName = time() . '_' . substr(sha1($cleanBase . random_bytes(4)), 0, 8) . '.' . $ext;
                    $targetDir = APPROOT . '/storage/posts';
                    if (!is_dir($targetDir)) @mkdir($targetDir, 0775, true);
                    
                    $dest = $targetDir . '/' . $imgName;
                    if (!move_uploaded_file($_FILES['image']['tmp_name'], $dest)) {
                        $imgName = null;
                    }
                } else {
                    echo json_encode(['status' => 'error', 'message' => 'Image exceeds maximum size of 2MB']);
                    return;
                }
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Invalid image format']);
                return;
            }
        }
        
        // Update post in database
        if ($this->m->updatePost($id, $content, $imgName)) {
            echo json_encode([
                'status' => 'success',
                'message' => 'Post updated successfully',
                'data' => [
                    'imagePath' => $imgName
                ]
            ]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to update post']);
        }
    }


    // --- ADMIN CONTENT MANAGEMENT ENDPOINTS ---
    // Only allow access if user is admin (add your own admin check logic)
    public function admin_list() {
        // DEBUG: Output session and query info
        if (!isset($_SESSION['user_id']) || ($_SESSION['user_role'] ?? '') !== 'admin') {
            http_response_code(403); echo 'Forbidden'; return;
        }
        $status = $_GET['status'] ?? 'all';
        $search = $_GET['search'] ?? '';
        $posts = $this->m->adminGetPosts($status, $search);
        header('Content-Type: application/json');
        echo json_encode([
            'debug' => [
                'session' => $_SESSION,
                'status' => $status,
                'search' => $search,
                'count' => is_array($posts) ? count($posts) : 0,
            ],
            'posts' => $posts
        ]);
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
        if (!isset($_SESSION['user_id']) || ($_SESSION['user_role'] ?? '') !== 'admin') {
            http_response_code(403); echo 'Forbidden'; return;
        }
        $ok = $this->m->adminDeletePost($id);
        header('Content-Type: application/json');
        echo json_encode(['ok' => $ok]);
    }

    // Delete post by owner or admin
    public function delete(){
        if ($_SERVER['REQUEST_METHOD'] !== 'DELETE'){
            http_response_code(405);
            return;
        }
        
        $data = json_decode(file_get_contents('php://input'), true);
        $post_id = $data['post_id'] ?? null;
        // post_user_id is client-provided; do not trust it
        
        // Validate post ID
        if (!is_numeric($post_id) || (int)$post_id <= 0) {
            header('Content-Type: application/json');
            echo json_encode(['status' => 'error', 'message' => 'Invalid post ID']);
            return;
        }

        $post_id = (int)$post_id;
        $post = $this->m->getPostById($post_id);

        if (!$post) {
            header('Content-Type: application/json');
            echo json_encode(['status' => 'error', 'message' => 'Post not found']);
            return;
        }

        // Allow only admins or the actual post owner
        $isAdmin = (($_SESSION['user_role'] ?? '') === 'admin');
        $isOwner = ($post->user_id == ($_SESSION['user_id'] ?? null));

        if ($isAdmin || $isOwner) {
            $result = $this->m->adminDeletePost($post_id);
            header('Content-Type: application/json');
            if ($result) {
                echo json_encode(['status' => 'success', 'message' => 'Post deleted successfully']);
                // delete the post pic if exists
                // Note: Model also attempts to remove the image. This is a safety net.
                if (isset($post->image) && !empty($post->image)) {
                    $imgFile = basename($post->image); // sanitize
                    $imgPath = APPROOT . '/storage/posts/' . $imgFile;
                    if (is_file($imgPath)) {
                        @unlink($imgPath);
                    }
                }
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Failed to delete post']);
            }
        } else {
            header('Content-Type: application/json');
            echo json_encode(['status' => 'error', 'message' => 'You can only delete your own posts or you have to be an admin']);
            return;
        }
    }
}
