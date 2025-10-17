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

  
    public function addCertificate()
{
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); return; }

        header('Content-Type: application/json');

        $name = trim($_POST['certificate_name'] ?? '');
        $issuer = trim($_POST['certificate_issuer'] ?? '');
        $issued_date = trim($_POST['certificate_date'] ?? '');
        $certificate_file = null;

        // Basic validation
        if ($name === '' || $issuer === '' || $issued_date === '') {
            echo json_encode(['success' => false, 'error' => 'Please provide name, issuer and issue date']);
            return;
        }

        // Check file upload presence
        if (empty($_FILES['certificate_file']['name']) || !is_uploaded_file($_FILES['certificate_file']['tmp_name'])) {
            echo json_encode(['success' => false, 'error' => 'No certificate file uploaded']);
            return;
        }

        // Validate file: only PDF, reasonable size (5MB)
        $fileTmp = $_FILES['certificate_file']['tmp_name'];
        $fileSize = $_FILES['certificate_file']['size'];
        $mime = mime_content_type($fileTmp);
        $allowedMimes = ['application/pdf', 'application/x-pdf'];

        if (!in_array($mime, $allowedMimes, true)) {
            error_log("Certificate upload rejected: invalid mime type {$mime}");
            echo json_encode(['success' => false, 'error' => 'Invalid file format. Only PDF allowed.']);
            return;
        }

        if ($fileSize > 5 * 1024 * 1024) {
            error_log("Certificate upload rejected: file too large ({$fileSize} bytes)");
            echo json_encode(['success' => false, 'error' => 'File too large. Max 5MB allowed.']);
            return;
        }

        // Prepare filename (safe)
        $origName = basename($_FILES['certificate_file']['name']);
        $safeBase = preg_replace('/[^A-Za-z0-9._-]/', '_', pathinfo($origName, PATHINFO_FILENAME));
        if ($safeBase === '') $safeBase = 'certificate';
        $ext = 'pdf';
        $certificate_file = time() . '_' . substr(sha1($safeBase . random_bytes(4)), 0, 8) . '.' . $ext;

        $targetDir = APPROOT . '/storage/certificates';
        if (!is_dir($targetDir)) {
            if (!@mkdir($targetDir, 0755, true)) {
                error_log("Failed to create certificates directory: {$targetDir}");
                echo json_encode(['success' => false, 'error' => 'Server error: cannot create storage directory.']);
                return;
            }
        }

        $dest = $targetDir . '/' . $certificate_file;
        if (!@move_uploaded_file($fileTmp, $dest)) {
            // Log full details for debugging
            $err = error_get_last();
            error_log("move_uploaded_file failed for certificate: tmp={$fileTmp} dest={$dest} error=" . json_encode($err));
            echo json_encode(['success' => false, 'error' => 'Failed to save uploaded file on server.']);
            return;
        }

        // Optionally set safe permissions (best effort)
        @chmod($dest, 0644);

        // Persist DB record via model
        $created = $this->Model->createCertificate($_SESSION['user_id'], $name, $issuer, $issued_date, $certificate_file);

        if ($created) {
            echo json_encode(['success' => true, 'file' => $certificate_file]);
        } else {
            // If DB insert failed, remove the uploaded file to avoid orphan files
            @unlink($dest);
            error_log("createCertificate failed for user {$_SESSION['user_id']} file={$certificate_file}");
            echo json_encode(['success' => false, 'error' => 'Failed to save certificate record.']);
        }
    }

    public function updateCertificate()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); return; }
        $cert_id = intval($_POST['certificate_id'] ?? 0);
        $name = trim($_POST['certificate_name'] ?? '');
        $issuer = trim($_POST['certificate_issuer'] ?? '');
        $issued_date = trim($_POST['certificate_date'] ?? '');
        $remove_file = !empty($_POST['remove_certificate_file']) ? true : false;
        $new_file = null;

        // Handle uploaded replacement file
        if (!empty($_FILES['certificate_file']['name']) && is_uploaded_file($_FILES['certificate_file']['tmp_name'])) {
            $ext = pathinfo($_FILES['certificate_file']['name'], PATHINFO_EXTENSION);
            $new_file = time() . '_' . substr(sha1($_FILES['certificate_file']['name'].random_bytes(4)),0,8) . '.' . $ext;
            $targetDir = APPROOT . '/storage/certificates';
            if(!is_dir($targetDir)) @mkdir($targetDir,0775,true);
            $dest = $targetDir . '/' . $new_file;
            if(!move_uploaded_file($_FILES['certificate_file']['tmp_name'],$dest)) $new_file=null;
        }

        $ok = false;
        if ($cert_id > 0 && $name !== '' && $issuer !== '' && $issued_date !== '') {
            $ok = $this->Model->updateCertificate($_SESSION['user_id'], $cert_id, $name, $issuer, $issued_date, $new_file, $remove_file);
        }
        // return JSON for AJAX usage
        header('Content-Type: application/json');
        echo json_encode(['success' => (bool)$ok]);
    }

}

