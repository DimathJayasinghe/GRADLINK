<?php
class Notification extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    private function respondJson(array $payload, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($payload);
    }

    public function count()
    {
        $userId = SessionManager::getUserId();
        if ($userId === null || !$this->notificationModel) {
            $this->respondJson(['count' => 0, 'success' => true]);
            return;
        }
        $count = $this->notificationModel->getUserNotificationsCount($userId);
        $this->respondJson(['count' => $count, 'success' => true]);
    }

    public function index()
    {
        $userId = SessionManager::getUserId();
        if ($userId === null) {
            header('Location: ' . URLROOT . '/login');
            return;
        }
    }

    public function fetchNewNotifications()
    {
        $userId = SessionManager::getUserId();
        if ($userId === null || !$this->notificationModel) {
            $this->respondJson(['notifications' => [], 'success' => true]);
            return;
        }
        $notifications = $this->notificationModel->getUserNotifications($userId);
        $this->respondJson(['notifications' => $notifications, 'success' => true]);
    }

    public function markAsRead()
    {
        $userId = SessionManager::getUserId();
        if ($userId === null || !$this->notificationModel) {
            $this->respondJson(['success' => false, 'message' => 'Unauthorized'], 401);
            return;
        }

        $notificationIdRaw = Sanitizer::cleanInput($this->getQueryParam('nId'));
        if (Sanitizer::isEmpty($notificationIdRaw) || !ctype_digit((string)$notificationIdRaw)) {
            $this->respondJson(['success' => false, 'message' => 'Invalid notification ID'], 400);
            return;
        }

        $notificationId = (int)$notificationIdRaw;
        $result = $this->notificationModel->markNotificationAsReadForUser($notificationId, $userId);
        $this->respondJson(['success' => (bool)$result]);
    }

    public function markAllAsRead()
    {
        $userId = SessionManager::getUserId();
        if ($userId === null || !$this->notificationModel) {
            $this->respondJson(['success' => false], 401);
            return;
        }
        $result = $this->notificationModel->markAllAsRead($userId);
        $this->respondJson(['success' => (bool)$result]);
    }
}
