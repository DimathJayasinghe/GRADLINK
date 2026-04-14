<?php
class Report extends Controller
{
    protected $reportModel;

    public function __construct()
    {
        SessionManager::redirectToAuthIfNotLoggedIn();
        $this->reportModel = $this->model('M_report');
    }

    public function index()
    {
        // View the report page
        SessionManager::redirectToAuthIfNotLoggedIn();
        $userId = $_SESSION['user_id'];
        $userRole = $_SESSION['role'] ?? $_SESSION['user_role'] ?? '';
        if(strtolower($userRole) === 'admin') {
            $reports = $this->reportModel->getAllReports();
        } else {
            $reports = $this->reportModel->getReportsByUser($userId);
        }
        $data = [];
    }

    function submitReport($report_type){
        SessionManager::redirectToAuthIfNotLoggedIn();
        if($_SERVER['REQUEST_METHOD'] !== 'POST'){
            http_response_code(405);
            echo json_encode(['success' => false, 'error' => 'method_not_allowed', 'message' => 'Only POST requests are allowed']);
            return;
        }
        header('Content-Type: application/json');
        $this->_hydrateJsonPost();
        switch($report_type){
            case 'post':
                $this->_report_post();
                break;
            case 'comment':
                $this->_report_comment();
                break;
            case 'profile':
                $this->_report_profile();
                break;
            case 'event':
                $this->_report_event();
                break;
            case 'fundraiser':
                $this->_report_fundraiser();
                break;
            default:
                http_response_code(400);
                echo json_encode(['success' => false, 'error' => 'invalid_report_type', 'message' => 'Unsupported report type']);
                break;
        }
    }

    function _report_post(){
        $userId = $_SESSION['user_id'];
        $postId = $_POST['post_id'] ?? $_POST['reported_item_id'] ?? null;
        $category = $_POST['category'] ?? null;
        $details = $_POST['details'] ?? null;
        $link = $_POST['link'] ?? null;

        $this->_submitGenericReport($userId, 'post', $postId, $category, $details, $link);
    }

    function _report_comment(){
        $userId = $_SESSION['user_id'];
        $commentId = $_POST['comment_id'] ?? $_POST['reported_item_id'] ?? null;
        $category = $_POST['category'] ?? null;
        $details = $_POST['details'] ?? null;
        $link = $_POST['link'] ?? null;

        $this->_submitGenericReport($userId, 'comment', $commentId, $category, $details, $link);
    }

    function _report_profile(){
        $userId = $_SESSION['user_id'];
        $profileId = $_POST['profile_id'] ?? $_POST['reported_item_id'] ?? null;
        $category = $_POST['category'] ?? null;
        $details = $_POST['details'] ?? null;
        $link = $_POST['link'] ?? null;

        $this->_submitGenericReport($userId, 'profile', $profileId, $category, $details, $link);
    }

    function _report_event(){
        $userId = $_SESSION['user_id'];
        $eventId = $_POST['event_id'] ?? $_POST['reported_item_id'] ?? null;
        $category = $_POST['category'] ?? null;
        $details = $_POST['details'] ?? null;
        $link = $_POST['link'] ?? null;

        $this->_submitGenericReport($userId, 'event', $eventId, $category, $details, $link);
    }

    function _report_fundraiser(){
        $userId = $_SESSION['user_id'];
        $fundraiserId = $_POST['fundraiser_id'] ?? $_POST['reported_item_id'] ?? null;
        $category = $_POST['category'] ?? null;
        $details = $_POST['details'] ?? null;
        $link = $_POST['link'] ?? null;

        $this->_submitGenericReport($userId, 'fundraiser', $fundraiserId, $category, $details, $link);
    }

    private function _submitGenericReport($userId, $type, $itemId, $category, $details, $link){
        if (!is_numeric($itemId) || (int)$itemId <= 0) {
            http_response_code(422);
            echo json_encode(['success' => false, 'error' => 'invalid_item_id', 'message' => 'A valid item id is required']);
            return;
        }

        if (!$category || trim((string)$category) === '') {
            http_response_code(422);
            echo json_encode(['success' => false, 'error' => 'missing_category', 'message' => 'Category is required']);
            return;
        }

        $ok = $this->reportModel->submitReport(
            (int)$userId,
            (string)$type,
            (int)$itemId,
            trim((string)$category),
            $details !== null ? trim((string)$details) : null,
            $link !== null && trim((string)$link) !== '' ? trim((string)$link) : null
        );

        if (!$ok) {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => 'insert_failed', 'message' => 'Failed to submit report']);
            return;
        }

        echo json_encode(['success' => true, 'status' => 'success', 'message' => 'Report submitted successfully']);
    }

    private function _hydrateJsonPost(){
        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
        if (stripos($contentType, 'application/json') === false) {
            return;
        }
        $raw = file_get_contents('php://input');
        if (!$raw) {
            return;
        }
        $decoded = json_decode($raw, true);
        if (is_array($decoded)) {
            $_POST = array_merge($_POST, $decoded);
        }
    }
}
?>