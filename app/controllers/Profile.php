<?php
class Profile extends Controller{
    protected $Model;
   
    public function __construct() {
        // If it's an API/AJAX call to certificate endpoints, return JSON on unauthenticated
        $isApi = (
            (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false)
            || (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest')
        );
        $isCertApiPath = isset($_SERVER['REQUEST_URI']) && preg_match('#/profile/(addCertificate|updateCertificate|deleteCertificate)#i', $_SERVER['REQUEST_URI']);

        if (!isset($_SESSION)) { @session_start(); }

        if (!isset($_SESSION['user_id'])) {
            if ($isApi && $isCertApiPath) {
                header('Content-Type: application/json');
                http_response_code(401);
                echo json_encode(['success' => false, 'error' => 'Not authenticated']);
                exit;
            }
        }

        // For normal page routes, keep default redirect behavior  
        // json_encode() → PHP → JSON
        // json_decode() → JSON → PHP
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
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Content-Type: application/json');
            http_response_code(405);
            echo json_encode(['success' => false, 'error' => 'Method not allowed']);
            return;
        }

        header('Content-Type: application/json');

        $name = trim($_POST['certificate_name'] ?? '');
        $issuer = trim($_POST['certificate_issuer'] ?? '');
        $issued_date = trim($_POST['certificate_date'] ?? '');
        $certificate_file = null;
        $original_name = null;

        // Basic validation
        if ($name === '' || $issuer === '' || $issued_date === '') {
            echo json_encode(['success' => false, 'error' => 'Please provide name, issuer and issue date']);
            return;
        }

        // File upload handling (modeled after Post::create)
        if (!empty($_FILES['certificate_file']['name']) && is_uploaded_file($_FILES['certificate_file']['tmp_name'])) {
            $original_name = basename($_FILES['certificate_file']['name']);
            $allowed = ['application/pdf' => 'pdf'];
            $mime = mime_content_type($_FILES['certificate_file']['tmp_name']);
            if (isset($allowed[$mime])) {
                // 5MB limit
                if ($_FILES['certificate_file']['size'] <= 5 * 1024 * 1024) {
                    $cleanBase = preg_replace('/[^A-Za-z0-9._-]/', '', $_FILES['certificate_file']['name']);
                    $ext = pathinfo($cleanBase, PATHINFO_EXTENSION);
                    if (!$ext) $ext = $allowed[$mime];
                    $certificate_file = time() . '_' . substr(sha1($cleanBase . random_bytes(4)), 0, 8) . '.' . $ext;
                    $targetDir = APPROOT . '/storage/certificates';
                    if (!is_dir($targetDir)) @mkdir($targetDir, 0775, true);
                    $dest = $targetDir . '/' . $certificate_file;
                    if (!@move_uploaded_file($_FILES['certificate_file']['tmp_name'], $dest)) {
                        // move failed => treat as no file
                        error_log('move_uploaded_file failed during addCertificate: tmp=' . $_FILES['certificate_file']['tmp_name'] . ' dest=' . $dest);
                        $certificate_file = null;
                    } else {
                        @chmod($dest, 0644);
                    }
                } else {
                    echo json_encode(['success' => false, 'error' => 'File too large. Max 5MB allowed.']);
                    return;
                }
            } else {
                echo json_encode(['success' => false, 'error' => 'Invalid file format. Only PDF allowed.']);
                return;
            }
        } else {
            echo json_encode(['success' => false, 'error' => 'No certificate file uploaded']);
            return;
        }

        // Persist DB record via model (only if file saved)
        if ($certificate_file !== null && $this->Model->createCertificate($_SESSION['user_id'], $name, $issuer, $issued_date, $certificate_file)) {
            echo json_encode(['success' => true, 'file' => $certificate_file, 'original_name' => $original_name]);
        } else {
            // If DB insert failed, remove uploaded file to avoid orphan files
            if ($certificate_file) {
                @unlink(APPROOT . '/storage/certificates/' . $certificate_file);
            }
            echo json_encode(['success' => false, 'error' => 'Failed to save certificate record.']);
        }
    }

    public function updateCertificate()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Content-Type: application/json');
            http_response_code(405);
            echo json_encode(['success' => false, 'error' => 'Method not allowed']);
            return;
        }
        header('Content-Type: application/json');

        $cert_id = intval($_POST['certificate_id'] ?? 0);
        $name = trim($_POST['certificate_name'] ?? '');
        $issuer = trim($_POST['certificate_issuer'] ?? '');
        $issued_date = trim($_POST['certificate_date'] ?? '');
        $remove_file = !empty($_POST['remove_certificate_file']) ? true : false;
        $new_file = null;
        $original_name = null;

        if ($cert_id <= 0) {
            echo json_encode(['success' => false, 'error' => 'Invalid certificate id']);
            return;
        }

        if ($name === '' || $issuer === '' || $issued_date === '') {
            echo json_encode(['success' => false, 'error' => 'Please provide name, issuer and issue date']);
            return;
        }

        // Try to fetch existing certificate record to know current filename
        $existingCert = null;
        if (method_exists($this->Model, 'getCertificateById')) {
            $existingCert = $this->Model->getCertificateById($cert_id);
        } else {
            $certs = $this->Model->getCertificates($_SESSION['user_id'] ?? null);
            if (is_array($certs)) {
                foreach ($certs as $c) {
                    if (isset($c->id) && intval($c->id) === $cert_id) {
                        $existingCert = $c;
                        break;
                    }
                }
            }
        }
        $oldFilename = ($existingCert && !empty($existingCert->certificate_file)) ? $existingCert->certificate_file : null;

        // Handle uploaded replacement file
        if (!empty($_FILES['certificate_file']['name']) && is_uploaded_file($_FILES['certificate_file']['tmp_name'])) {
            $original_name = basename($_FILES['certificate_file']['name']);
            $fileTmp = $_FILES['certificate_file']['tmp_name'];
            $fileSize = $_FILES['certificate_file']['size'];
            $mime = mime_content_type($fileTmp);
            $allowedMimes = ['application/pdf', 'application/x-pdf'];

            if (!in_array($mime, $allowedMimes, true)) {
                error_log("Certificate update rejected: invalid mime type {$mime}");
                echo json_encode(['success' => false, 'error' => 'Invalid file format. Only PDF allowed.']);
                return;
            }
            if ($fileSize > 5 * 1024 * 1024) {
                echo json_encode(['success' => false, 'error' => 'File too large. Max 5MB allowed.']);
                return;
            }

            $origName = basename($_FILES['certificate_file']['name']);
            $safeBase = preg_replace('/[^A-Za-z0-9._-]/', '_', pathinfo($origName, PATHINFO_FILENAME));
            if ($safeBase === '') $safeBase = 'certificate';
            $ext = 'pdf';
            $new_file = time() . '_' . substr(sha1($safeBase . random_bytes(4)), 0, 8) . '.' . $ext;

            $targetDir = APPROOT . '/storage/certificates';
            if (!is_dir($targetDir)) {
                if (!@mkdir($targetDir, 0755, true)) {
                    error_log("Failed to create certificates directory: {$targetDir}");
                    echo json_encode(['success' => false, 'error' => 'Server error: cannot create storage directory.']);
                    return;
                }
            }

            $dest = $targetDir . '/' . $new_file;
            if (!@move_uploaded_file($fileTmp, $dest)) {
                $err = error_get_last();
                error_log("move_uploaded_file failed for update certificate: tmp={$fileTmp} dest={$dest} error=" . json_encode($err));
                echo json_encode(['success' => false, 'error' => 'Failed to save uploaded file on server.']);
                return;
            }
            @chmod($dest, 0644);
            // new upload overrides a "remove" flag
            $remove_file = false;
        }

        // Prevent update that would result in no certificate PDF
        $resultingFile = $new_file ?? $oldFilename;
        if (empty($resultingFile)) {
            echo json_encode(['success' => false, 'error' => 'Certificate must have a PDF file. Upload a PDF before saving.']);
            // Clean up new file if uploaded
            if (!empty($new_file)) {
                @unlink(APPROOT . '/storage/certificates/' . basename($new_file));
            }
            return;
        }

        // Call model to update record. Model expected to accept $new_file and $remove_file flag.
        $ok = false;
        if ($cert_id > 0) {
            $ok = $this->Model->updateCertificate($_SESSION['user_id'], $cert_id, $name, $issuer, $issued_date, $new_file, $remove_file);
        }

        if ($ok) {
            // If update succeeded and we replaced existing file, unlink the old file
            if (!empty($new_file) && !empty($oldFilename) && $oldFilename !== $new_file) {
                $oldPath = APPROOT . '/storage/certificates/' . basename($oldFilename);
                if (is_file($oldPath)) @unlink($oldPath);
            }
            echo json_encode(['success' => true, 'file' => ($new_file ?? $oldFilename), 'original_name' => $original_name ?? null]);
        } else {
            // If model failed but we uploaded a new file above, remove the uploaded new file to avoid orphan
            if (!empty($new_file)) {
                $newPath = APPROOT . '/storage/certificates/' . basename($new_file);
                if (is_file($newPath)) @unlink($newPath);
            }
            echo json_encode(['success' => false, 'error' => 'Failed to update certificate record.']);
        }
    }

    public function deleteCertificate()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
            header('Content-Type: application/json');
            http_response_code(405);
            echo json_encode(['success' => false, 'error' => 'Method not allowed']);
            return;
        }
        header('Content-Type: application/json');

        // Ensure user logged in
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['success' => false, 'error' => 'Not authenticated']);
            return;
        }

        $cert_id = $this->getQueryParam('id', null);
        if ($cert_id <= 0) {
            echo json_encode(['success' => false, 'error' => 'Invalid certificate id']);
            return;
        }

        // Fetch certificate record (model should provide getCertificateById)
        $cert = null;
        if (method_exists($this->Model, 'getCertificateById')) {
            $cert = $this->Model->getCertificateById($cert_id);
        } else {
            // fallback: search user's certificates
            $certs = $this->Model->getCertificates($_SESSION['user_id']);
            if (is_array($certs)) {
                foreach ($certs as $c) {
                    if (isset($c->id) && intval($c->id) === $cert_id) { $cert = $c; break; }
                }
            }
        }

        if (!$cert) {
            echo json_encode(['success' => false, 'error' => 'Certificate not found']);
            return;
        }

        // Only owner can delete
        $ownerId = $cert->user_id ?? ($cert->userId ?? null);
        if ($ownerId == null || intval($ownerId) !== intval($_SESSION['user_id'])) {
            echo json_encode(['success' => false, 'error' => 'Permission denied']);
            return;
        }

        // Delete DB row via model. Expect model->deleteCertificate(user_id, cert_id) or deleteCertificateById(cert_id)
        $deleted = false;
        if (method_exists($this->Model, 'deleteCertificate')) {
            $deleted = $this->Model->deleteCertificate($_SESSION['user_id'], $cert_id);
        } elseif (method_exists($this->Model, 'deleteCertificateById')) {
            $deleted = $this->Model->deleteCertificateById($cert_id);
        } else {
            // No model method found
            error_log("Profile::deleteCertificate - model method deleteCertificate not found");
            echo json_encode(['success' => false, 'error' => 'Server not configured to delete certificate']);
            return;
        }

        if ($deleted) {
            // remove file if present
            $filename = $cert->certificate_file ?? $cert->file ?? null;
            if (!empty($filename)) {
                $path = APPROOT . '/storage/certificates/' . basename($filename);
                if (is_file($path)) @unlink($path);
            }
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Failed to delete certificate']);
        }
    }

}

