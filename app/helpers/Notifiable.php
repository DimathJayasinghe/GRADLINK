<?php
trait Notifiable
{
    protected $AllowedTypes = [
        'welcome',
        'info',
        'warning',
        'alert',
        'message',
        'follow_request',
        'started_following',
        'like',
        'comment',
        'event_update',
        'post_approval',
        'fundraiser_update',
        'system_announcement',
        'admin_message',
    ];

    protected function notify($receiverId, $type, $referenceId, $content)
    {
        error_log('[Notifiable::notify] Called with: receiverId=' . var_export($receiverId, true) . ', type=' . $type . ', refId=' . $referenceId);
        
        if (!$this->notificationModel) {
            error_log('[Notifiable::notify] ERROR: notificationModel is null!');
            return false;
        }
        
        if (!in_array($type, $this->AllowedTypes)) {
            error_log('[Notifiable::notify] ERROR: type not allowed: ' . $type);
            throw new Exception("Notification type '$type' is not allowed.");
        }
        
        $result = $this->notificationModel->createNotification($receiverId, $type, $referenceId, $content);
        error_log('[Notifiable::notify] createNotification returned: ' . var_export($result, true));
        return $result;
    }

    protected function updateNotification($receiverId, $referenceId, $type, $content)
    {
        if (!$this->notificationModel) return false;
        if (!in_array($type, $this->AllowedTypes)) {
            throw new Exception("Notification type '$type' is not allowed.");
        }
        return $this->notificationModel->updateNotification($receiverId, $referenceId, $type, $content);
    }
}
