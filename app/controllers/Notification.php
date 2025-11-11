<?php
class Notification extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function count()
    {
        header('Content-Type: application/json');
        $userId = SessionManager::getUserId();
        if ($userId === null || !$this->notificationModel) {
            echo json_encode(['count' => 0, 'success' => true]);
            return;
        }
        $count = $this->notificationModel->getUserNotificationsCount($userId);
        echo json_encode(['count' => $count, 'success' => true]);
    }

    public function index()
    {
        $userId = SessionManager::getUserId();
        if ($userId === null) {
            header('Location: ' . URLROOT . '/login');
            return;
        }
        
        if (!$this->notificationModel) {
            $notifications = [];
        } else {
            $notifications = $this->notificationModel->getUserNotifications($userId);
        }
        
        // Render the notifications page view
        $this->view('notifications/v_notifications', ['notifications' => $notifications]);
    }

    public function fetchNewNotifications()
    {
        header('Content-Type: application/json');
        $userId = SessionManager::getUserId();
        if ($userId === null || !$this->notificationModel) {
            echo json_encode(['notifications' => [], 'success' => true]);
            return;
        }
        $notifications = $this->notificationModel->getUserNotifications($userId);
        echo json_encode(['notifications' => $notifications, 'success' => true]);
    }

    public function markAsRead()
    {
        header('Content-Type: application/json');
        $notificationId = Sanitizer::cleanInput($this->getQueryParam('nId'));
        if (Sanitizer::isEmpty($notificationId) || !$this->notificationModel) {
            echo json_encode(['success' => false, 'message' => 'Invalid notification ID']);
            return;
        }
        $result = $this->notificationModel->markNotificationAsRead($notificationId);
        echo json_encode(['success' => $result]);
    }

    public function markAllAsRead()
    {
        header('Content-Type: application/json');
        $userId = SessionManager::getUserId();
        if ($userId === null || !$this->notificationModel) {
            echo json_encode(['success' => false]);
            return;
        }
        $result = $this->notificationModel->markAllAsRead($userId);
        echo json_encode(['success' => $result]);
    }
}
