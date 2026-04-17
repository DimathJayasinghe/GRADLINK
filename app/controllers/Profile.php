<?php
class Profile extends Controller{
    protected $Model;
   
    public function __construct() {
        // Initialize parent constructor to set up notificationModel
        parent::__construct();
        
        // If it's an API/AJAX call to certificate endpoints, return JSON on unauthenticated
        $isApi = (
            (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false)
            || (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest')
        );
        $isProfileApiPath = isset($_SERVER['REQUEST_URI']) && preg_match('#/profile/(addCertificate|updateCertificate|deleteCertificate|addWorkExperience|updateWorkExperience|deleteWorkExperience|addProjects|updateProjects|deleteProjects)#i', $_SERVER['REQUEST_URI']);

        if (!isset($_SESSION)) { @session_start(); }

        if (!isset($_SESSION['user_id'])) {
            if ($isApi && $isProfileApiPath) {
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

    private function respondJson(array $payload, int $statusCode = 200): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($payload);
    }

    private function ensureJsonMethod(string $method): bool
    {
        if (strtoupper($_SERVER['REQUEST_METHOD'] ?? '') !== strtoupper($method)) {
            $this->respondJson(['success' => false, 'error' => 'Method not allowed'], 405);
            return false;
        }

        return true;
    }

    private function getSupportedCountries(): array
    {
        $file = APPROOT . '/data/countries_data.php';
        if (is_file($file)) {
            $countries = require $file;
            if (is_array($countries) && !empty($countries)) {
                return array_values(array_filter($countries, 'is_string'));
            }
        }

        return ['Sri Lanka'];
    }

    public function index(){
        $user_id = $this->getQueryParam('userid', null);
        if (!$user_id){
            $user_id = $_SESSION['user_id'];
        }
        // $isBlocked = $this->Model->isBlocked($_SESSION['user_id'], $user_id);
        // if ($isBlocked){
        //     header("Location: " . URLROOT . "/profile?userid=" . $_SESSION['user_id']);
        //     exit;
        // }


        // handle other user profile view
        $user = $this->Model->getUser($user_id);
        if ($user == 1) {
            $data['userDetails'] = $this->Model->getUserDetails($user_id);
            $data['work_experiences'] = $this->Model->getWorkExperiences($user_id); 
            $data['certificates'] = $this->Model->getCertificates($user_id);
            $data['projects'] = $this->Model->getProjects($user_id);
            $isFollwed = $this->Model->isFollowed($_SESSION['user_id'], $user_id);
            $hasPending = $this->Model->hasPendingFollowRequest($_SESSION['user_id'], $user_id);
            // Set for view logic
            $data['isfollowed'] = $isFollwed;
            $data['has_pending_request'] = $hasPending;
            
            // Always load posts for any profile
            $data['posts'] = $this->Model->getPosts($user_id);
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

    public function follow(){
        if ($_SERVER['REQUEST_METHOD'] === 'POST'){
            header('Content-Type: application/json');
            // Accept both form-encoded and JSON bodies
            $payload = null;
            $ct = isset($_SERVER['CONTENT_TYPE']) ? strtolower($_SERVER['CONTENT_TYPE']) : '';
            if (strpos($ct, 'application/json') !== false) {
                $raw = file_get_contents('php://input');
                if ($raw) {
                    $json = json_decode($raw, true);
                    if (is_array($json)) $payload = $json;
                }
            }
            $profile_user_id = 0;
            if (is_array($payload)) {
                $profile_user_id = intval($payload['profile_user_id'] ?? ($payload['target_id'] ?? 0));
            } else {
                $profile_user_id = intval($_POST['profile_user_id'] ?? ($_POST['target_id'] ?? 0));
            }
            $current_user_id = $_SESSION['user_id'] ?? 0;

            if ($profile_user_id <= 0 || $current_user_id <= 0) {
                echo json_encode(['success' => false, 'error' => 'Invalid user id']);
                return;
            }

            if ($profile_user_id === $current_user_id) {
                echo json_encode(['success' => false, 'error' => 'Cannot follow/unfollow yourself']);
                return;
            }

            // Check current follow status
            $isFollowed = $this->Model->isFollowed($current_user_id, $profile_user_id);
            $hasPending = $this->Model->hasPendingFollowRequest($current_user_id, $profile_user_id);
            $result = false;
            $action = '';
            
            if ($isFollowed) {
                // Unfollow
                $result = $this->Model->unfollowUser($current_user_id, $profile_user_id);
                $action = 'unfollowed';
            } elseif ($hasPending) {
                // Cancel pending request
                $result = $this->Model->cancelFollowRequest($current_user_id, $profile_user_id);
                $action = 'cancelled';
            } else {
                // Create follow request
                $requestId = $this->Model->createFollowRequest($current_user_id, $profile_user_id);
                $result = $requestId ? true : false;
                $action = 'requested';
                
                // Send notification to target user
                if ($result) {
                    try {
                        error_log('[Profile::follow] Sending follow request notification to user: ' . $profile_user_id);
                        
                        if ($profile_user_id && (int)$profile_user_id > 0 && (int)$profile_user_id !== (int)$current_user_id) {
                            $requester = $this->Model->getUserDetails($current_user_id);
                            $notification_type = 'follow_request';
                            // Use requester's user_id as reference_id for easy lookup
                            $reference_id = $current_user_id;
                            $content = [
                                'requester_name' => $requester->display_name ?? $_SESSION['user_name'] ?? 'Someone',
                                'requester_id' => $current_user_id,
                                'requester_image' => $requester->profile_image ?? '',
                                'text' => ($requester->display_name ?? $_SESSION['user_name'] ?? 'Someone') . ' wants to follow you',
                                'link' => '/profile?userid=' . $current_user_id,
                                'request_id' => $requestId
                            ];
                            error_log('[Profile::follow] Notification content: ' . json_encode($content));
                            $notifyResult = $this->notify($profile_user_id, $notification_type, $reference_id, $content);
                            error_log('[Profile::follow] notify() returned: ' . var_export($notifyResult, true));
                        }
                    } catch (Throwable $e) {
                        error_log("[Profile::follow] EXCEPTION in notification: " . $e->getMessage() . "\n" . $e->getTraceAsString());
                    }
                }
            }
            
            if ($result) {
                echo json_encode([
                    'success' => true, 
                    'action' => $action,
                    'connected' => $isFollowed ? false : ($action === 'requested' ? 'pending' : true)
                ]);
            } else {
                echo json_encode(['success' => false, 'error' => 'Failed to update follow status']);
            }
        }else{
            header('Content-Type: application/json');
            http_response_code(405);
            echo json_encode(['success' => false, 'error' => 'Method not allowed']);
            return;
        }
    }

    public function blockProfile()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            header('Content-Type: application/json');

            $target_id = intval($_POST['target_id']);
            $current_user_id = $_SESSION['user_id'];

            if($target_id == $current_user_id) {
                echo json_encode(['success' => false, 'error' => 'Cannot block yourself']);
                return;
            }

            $isBlocked = $this->Model->isBlocked($current_user_id, $target_id);

            if($isBlocked) {
                $this->Model->unblockUser($current_user_id, $target_id);
                echo json_encode(['success' => true, 'blocked' => false]);
            }else{
                $this->Model->blockUser($current_user_id, $target_id);
                echo json_encode(['success' => true, 'blocked' => true]);
            }
        }   

    }

    public function updateProfileBioImage()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Content-Type: application/json');
            http_response_code(405);
            echo json_encode(['success' => false, 'error' => 'Method not allowed']);
            return;
        }
        header('Content-Type: application/json');

        $bio = trim($_POST['profileBioInput'] ?? '');
        $batch_no = trim($_POST['profileBatchNoInput'] ?? '');
        $country = trim((string)($_POST['profileCountryInput'] ?? ''));
        

        // Validation
        if ($bio === '') {
            echo json_encode(['success' => false, 'error' => 'Bio cannot be empty']);
            return;
        }
        if ($batch_no === '') {
            echo json_encode(['success' => false, 'error' => 'Batch number cannot be empty']);
            return;
        }

        if ($country === '') {
            echo json_encode(['success' => false, 'error' => 'Country cannot be empty']);
            return;
        }

        $allowedCountries = $this->getSupportedCountries();
        if (!in_array($country, $allowedCountries, true)) {
            echo json_encode(['success' => false, 'error' => 'Please select a valid country']);
            return;
        }

        // Preserve current image if no new upload
       $current = $this->Model->getUserDetails($_SESSION['user_id']);
       $currentImage = $current->profile_image ?? null;
       $profile_image = $currentImage;

        //Handle profile image upload
        if (!empty($_FILES['profileImageInput']['name']) && is_uploaded_file($_FILES['profileImageInput']['tmp_name'])) {
            $original_name = basename($_FILES['profileImageInput']['name']);
            $fileTmp = $_FILES['profileImageInput']['tmp_name'];
            $fileSize = $_FILES['profileImageInput']['size'];
            $mime = mime_content_type($fileTmp);
            $allowedMimes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];

            if (!in_array($mime, $allowedMimes, true)) {
                echo json_encode(['success' => false, 'error' => 'Invalid file format. Only JPG, PNG, GIF, WEBP allowed.']);
                return;
            }
            if ($fileSize > 5 * 1024 * 1024) {
                echo json_encode(['success' => false, 'error' => 'File too large. Max 5MB allowed.']);
                return;
            }

            $safeBase = preg_replace('/[^A-Za-z0-9._-]/', '_', pathinfo($original_name, PATHINFO_FILENAME));
            if ($safeBase === '') $safeBase = 'profile';
            $ext = pathinfo($original_name, PATHINFO_EXTENSION);
            if (!$ext) $ext = 'jpg';
            
            $profile_image = time() . '_' . substr(sha1($safeBase . random_bytes(4)), 0, 8) . '.' . $ext;

            $targetDir = APPROOT . '/storage/profile_pic';
            if (!is_dir($targetDir)) {
                if (!@mkdir($targetDir, 0755, true)) {
                    echo json_encode(['success' => false, 'error' => 'Server error: cannot create storage directory.']);
                    return;
                }
            }

            $dest = $targetDir . '/' . $profile_image;
            if (!@move_uploaded_file($fileTmp, $dest)) {
                echo json_encode(['success' => false, 'error' => 'Failed to save uploaded file on server.']);
                return;
            }
            @chmod($dest, 0644);
        }

        // Update DB record via model
        if($this->Model->updateProfileBioImage($_SESSION['user_id'], $profile_image, $bio, $batch_no, $country)) {
            $current = $this->Model->getUserDetails($_SESSION['user_id']);
            $_SESSION['profile_image'] = $current->profile_image ?? null;
            // $_SESSION['bio'] = $current->bio ?? null;
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Failed to update profile image and bio']);
            return;
        }

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

    public function addWorkExperience()
    {
        if (!$this->ensureJsonMethod('POST')) {
            return;
        }

        $userId = (int)($_SESSION['user_id'] ?? 0);
        if ($userId <= 0) {
            $this->respondJson(['success' => false, 'error' => 'Not authenticated'], 401);
            return;
        }

        $position = trim((string)($_POST['position'] ?? ''));
        $company = trim((string)($_POST['company'] ?? ''));
        $period = trim((string)($_POST['period'] ?? ''));

        if ($position === '' || $company === '' || $period === '') {
            $this->respondJson(['success' => false, 'error' => 'Position, company, and period are required'], 422);
            return;
        }

        $ok = $this->Model->createWorkExperience($userId, $position, $company, $period);
        if (!$ok) {
            $this->respondJson(['success' => false, 'error' => 'Failed to create work experience'], 500);
            return;
        }

        $this->respondJson(['success' => true]);
    }

    public function updateWorkExperience()
    {
        if (!$this->ensureJsonMethod('POST')) {
            return;
        }

        $userId = (int)($_SESSION['user_id'] ?? 0);
        if ($userId <= 0) {
            $this->respondJson(['success' => false, 'error' => 'Not authenticated'], 401);
            return;
        }

        $workId = (int)($_POST['work_id'] ?? 0);
        $position = trim((string)($_POST['position'] ?? ''));
        $company = trim((string)($_POST['company'] ?? ''));
        $period = trim((string)($_POST['period'] ?? ''));

        if ($workId <= 0) {
            $this->respondJson(['success' => false, 'error' => 'Invalid work experience id'], 422);
            return;
        }

        if ($position === '' || $company === '' || $period === '') {
            $this->respondJson(['success' => false, 'error' => 'Position, company, and period are required'], 422);
            return;
        }

        $ok = $this->Model->updateWorkExperience($userId, $workId, $position, $company, $period);
        if (!$ok) {
            $this->respondJson(['success' => false, 'error' => 'Failed to update work experience'], 500);
            return;
        }

        $this->respondJson(['success' => true]);
    }

    public function deleteWorkExperience()
    {
        if (!$this->ensureJsonMethod('DELETE')) {
            return;
        }

        $userId = (int)($_SESSION['user_id'] ?? 0);
        if ($userId <= 0) {
            $this->respondJson(['success' => false, 'error' => 'Not authenticated'], 401);
            return;
        }

        $workId = (int)$this->getQueryParam('id', 0);
        if ($workId <= 0) {
            $this->respondJson(['success' => false, 'error' => 'Invalid work experience id'], 422);
            return;
        }

        $ok = $this->Model->deleteWorkExperience($userId, $workId);
        if (!$ok) {
            $this->respondJson(['success' => false, 'error' => 'Failed to delete work experience'], 500);
            return;
        }

        $this->respondJson(['success' => true]);
    }

    public function addProjects()
    {
        if (!$this->ensureJsonMethod('POST')) {
            return;
        }

        $userId = (int)($_SESSION['user_id'] ?? 0);
        if ($userId <= 0) {
            $this->respondJson(['success' => false, 'error' => 'Not authenticated'], 401);
            return;
        }

        $title = trim((string)($_POST['project_title'] ?? ''));
        $description = trim((string)($_POST['project_description'] ?? ''));
        $skills = trim((string)($_POST['project_skills'] ?? ''));
        $startDate = trim((string)($_POST['project_start_date'] ?? ''));
        $endDate = trim((string)($_POST['project_end_date'] ?? ''));

        if ($title === '') {
            $this->respondJson(['success' => false, 'error' => 'Project title is required'], 422);
            return;
        }

        $ok = $this->Model->createProject(
            $userId,
            $title,
            $description !== '' ? $description : null,
            $skills !== '' ? $skills : null,
            $startDate !== '' ? $startDate : null,
            $endDate !== '' ? $endDate : null
        );

        if (!$ok) {
            $this->respondJson(['success' => false, 'error' => 'Failed to create project'], 500);
            return;
        }

        $this->respondJson(['success' => true]);
    }

    public function updateProjects()
    {
        if (!$this->ensureJsonMethod('POST')) {
            return;
        }

        $userId = (int)($_SESSION['user_id'] ?? 0);
        if ($userId <= 0) {
            $this->respondJson(['success' => false, 'error' => 'Not authenticated'], 401);
            return;
        }

        $projectId = (int)($_POST['project_id'] ?? 0);
        $title = trim((string)($_POST['project_title'] ?? ''));
        $description = trim((string)($_POST['project_description'] ?? ''));
        $skills = trim((string)($_POST['project_skills'] ?? ''));
        $startDate = trim((string)($_POST['project_start_date'] ?? ''));
        $endDate = trim((string)($_POST['project_end_date'] ?? ''));

        if ($projectId <= 0) {
            $this->respondJson(['success' => false, 'error' => 'Invalid project id'], 422);
            return;
        }

        if ($title === '') {
            $this->respondJson(['success' => false, 'error' => 'Project title is required'], 422);
            return;
        }

        $ok = $this->Model->updateProject(
            $userId,
            $projectId,
            $title,
            $description !== '' ? $description : null,
            $skills !== '' ? $skills : null,
            $startDate !== '' ? $startDate : null,
            $endDate !== '' ? $endDate : null
        );

        if (!$ok) {
            $this->respondJson(['success' => false, 'error' => 'Failed to update project'], 500);
            return;
        }

        $this->respondJson(['success' => true]);
    }

    public function deleteProjects()
    {
        if (!$this->ensureJsonMethod('DELETE')) {
            return;
        }

        $userId = (int)($_SESSION['user_id'] ?? 0);
        if ($userId <= 0) {
            $this->respondJson(['success' => false, 'error' => 'Not authenticated'], 401);
            return;
        }

        $projectId = (int)$this->getQueryParam('id', 0);
        if ($projectId <= 0) {
            $this->respondJson(['success' => false, 'error' => 'Invalid project id'], 422);
            return;
        }

        $ok = $this->Model->deleteProject($userId, $projectId);
        if (!$ok) {
            $this->respondJson(['success' => false, 'error' => 'Failed to delete project'], 500);
            return;
        }

        $this->respondJson(['success' => true]);
    }

    public function approveFollowRequest(){
        if ($_SERVER['REQUEST_METHOD'] !== 'POST'){
            header('Content-Type: application/json');
            http_response_code(405);
            echo json_encode(['success' => false, 'error' => 'Method not allowed']);
            return;
        }

        header('Content-Type: application/json');

        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['success' => false, 'error' => 'Unauthorized']);
            return;
        }

        $payload = null;
        $ct = isset($_SERVER['CONTENT_TYPE']) ? strtolower($_SERVER['CONTENT_TYPE']) : '';
        if (strpos($ct, 'application/json') !== false) {
            $raw = file_get_contents('php://input');
            if ($raw) {
                $json = json_decode($raw, true);
                if (is_array($json)) $payload = $json;
            }
        }
        
        $requester_id = 0;
        if (is_array($payload)) {
            $requester_id = intval($payload['requester_id'] ?? 0);
        } else {
            $requester_id = intval($_POST['requester_id'] ?? 0);
        }

        if ($requester_id <= 0) {
            echo json_encode(['success' => false, 'error' => 'Invalid requester id']);
            return;
        }

        $result = $this->Model->approveFollowRequest($requester_id, $_SESSION['user_id']);

        if ($result) {
            // Send "started_following" notification to the requester
            try {
                $approver = $this->Model->getUserDetails($_SESSION['user_id']);
                $notification_type = 'started_following';
                $reference_id = $_SESSION['user_id'];
                $content = [
                    'follower_name' => $approver->display_name ?? $_SESSION['user_name'] ?? 'Someone',
                    'follower_id' => $_SESSION['user_id'],
                    'text' => ($approver->display_name ?? $_SESSION['user_name'] ?? 'Someone') . ' accepted your follow request',
                    'link' => '/profile?userid=' . $_SESSION['user_id']
                ];
                $this->notify($requester_id, $notification_type, $reference_id, $content);
            } catch (Throwable $e) {
                error_log("[Profile::approveFollowRequest] EXCEPTION in notification: " . $e->getMessage());
            }
            
            echo json_encode(['success' => true, 'message' => 'Follow request approved']);
        } else {
            echo json_encode(['success' => false, 'error' => 'Failed to approve request']);
        }
    }

    public function rejectFollowRequest(){
        if ($_SERVER['REQUEST_METHOD'] !== 'POST'){
            header('Content-Type: application/json');
            http_response_code(405);
            echo json_encode(['success' => false, 'error' => 'Method not allowed']);
            return;
        }

        header('Content-Type: application/json');

        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['success' => false, 'error' => 'Unauthorized']);
            return;
        }

        $payload = null;
        $ct = isset($_SERVER['CONTENT_TYPE']) ? strtolower($_SERVER['CONTENT_TYPE']) : '';
        if (strpos($ct, 'application/json') !== false) {
            $raw = file_get_contents('php://input');
            if ($raw) {
                $json = json_decode($raw, true);
                if (is_array($json)) $payload = $json;
            }
        }
        
        $requester_id = 0;
        if (is_array($payload)) {
            $requester_id = intval($payload['requester_id'] ?? 0);
        } else {
            $requester_id = intval($_POST['requester_id'] ?? 0);
        }

        if ($requester_id <= 0) {
            echo json_encode(['success' => false, 'error' => 'Invalid requester id']);
            return;
        }

        $result = $this->Model->rejectFollowRequest($requester_id, $_SESSION['user_id']);

        if ($result) {
            echo json_encode(['success' => true, 'message' => 'Follow request rejected']);
        } else {
            echo json_encode(['success' => false, 'error' => 'Failed to reject request']);
        }
    }

}

