<?php
class Notification extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function Count()
    {
        // $this->requireAjaxRequest();
        $userId = SessionManager::getUserId();
        if ($userId === null || !$this->notificationModel) {
            return 0;
        }
        return $this->notificationModel->getUserNotificationsCount($userId);
    }

    public function index()
    {
        // $this->requireAjaxRequest();
        $userId = SessionManager::getUserId();
        if ($userId === null || !$this->notificationModel) {
            return [];
        }
        return $this->notificationModel->getUserNotifications($userId);
    }

    public function markAsRead()
    {
        // Implement logic to mark notification as read
        // $this->requireAjaxRequest();
        $notificationId = Sanitizer::cleanInput($this->getQueryParam('nId'));
        if (Sanitizer::isEmpty($notificationId) || !$this->notificationModel) {
            return false;
        }
        // Call model method to mark as read
        return $this->notificationModel->markNotificationAsRead($notificationId);
    }
}
