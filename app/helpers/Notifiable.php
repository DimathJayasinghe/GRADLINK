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
        'new_message',
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
        $receiverId = (int)$receiverId;
        $type = (string)$type;

        if (!$this->notificationModel) {
            error_log('[Notifiable::notify] ERROR: notificationModel is null!');
            return false;
        }

        if (!in_array($type, $this->AllowedTypes)) {
            error_log('[Notifiable::notify] ERROR: type not allowed: ' . $type);
            throw new Exception("Notification type '$type' is not allowed.");
        }

        $isTypeEnabled = true;
        if (method_exists($this->notificationModel, 'isNotificationTypeEnabledForUser')) {
            $isTypeEnabled = $this->notificationModel->isNotificationTypeEnabledForUser($receiverId, $type);
        }

        $result = $this->notificationModel->createNotification($receiverId, $type, $referenceId, $content);

        if ($result && $isTypeEnabled) {
            $this->sendNotificationEmailIfEnabled($receiverId, $type, $content);
        }

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

    private function sendNotificationEmailIfEnabled(int $receiverId, string $type, $content): void
    {
        if ($receiverId <= 0) {
            return;
        }

        try {
            if (!$this->isEmailNotificationEnabled($receiverId)) {
                return;
            }

            $recipient = $this->getNotificationEmailRecipient($receiverId);
            if (!$recipient || empty($recipient['email'])) {
                return;
            }

            $messageText = $this->extractNotificationText($content, $type);
            $link = $this->extractNotificationLink($content);

            $sent = EmailHandler::sendNotificationEmail(
                $recipient['email'],
                $recipient['name'],
                $type,
                $messageText,
                $link
            );

            if (!$sent) {
                error_log('[Notifiable::notify] Notification email sending failed for user ' . $receiverId . ' type=' . $type);
            }
        } catch (Throwable $e) {
            error_log('[Notifiable::notify] Notification email error: ' . $e->getMessage());
        }
    }

    private function isEmailNotificationEnabled(int $receiverId): bool
    {
        try {
            $settingsModel = $this->model('M_settings');
            if (!$settingsModel || !method_exists($settingsModel, 'getNotificationSettings')) {
                return false;
            }

            $settings = $settingsModel->getNotificationSettings($receiverId);
            if (!$settings) {
                return true;
            }

            return (int)($settings->email_enabled ?? 1) === 1;
        } catch (Throwable $e) {
            error_log('[Notifiable::notify] Failed to read email notification setting: ' . $e->getMessage());
            return false;
        }
    }

    private function getNotificationEmailRecipient(int $receiverId): ?array
    {
        try {
            $settingsModel = $this->model('M_settings');
            if (!$settingsModel || !method_exists($settingsModel, 'getUserById')) {
                return null;
            }

            $user = $settingsModel->getUserById($receiverId);
            if (!$user || empty($user->email)) {
                return null;
            }

            return [
                'email' => (string)$user->email,
                'name' => (string)($user->display_name ?? $user->name ?? '')
            ];
        } catch (Throwable $e) {
            error_log('[Notifiable::notify] Failed to load recipient details: ' . $e->getMessage());
            return null;
        }
    }

    private function extractNotificationText($content, string $type): string
    {
        if (is_string($content) && trim($content) !== '') {
            return trim($content);
        }

        if (is_object($content)) {
            $content = (array)$content;
        }

        if (is_array($content)) {
            foreach (['text', 'message', 'body', 'title'] as $key) {
                if (isset($content[$key]) && is_string($content[$key]) && trim($content[$key]) !== '') {
                    return trim($content[$key]);
                }
            }
        }

        return 'You have a new ' . str_replace('_', ' ', $type) . ' notification on Gradlink.';
    }

    private function extractNotificationLink($content): ?string
    {
        if (is_object($content)) {
            $content = (array)$content;
        }

        if (!is_array($content) || !isset($content['link']) || !is_string($content['link'])) {
            return URLROOT . '/settings/notifications';
        }

        $link = trim($content['link']);
        if ($link === '') {
            return URLROOT . '/settings/notifications';
        }

        if (preg_match('#^https?://#i', $link)) {
            return $link;
        }

        if ($link[0] === '/') {
            return URLROOT . $link;
        }

        return URLROOT . '/' . ltrim($link, '/');
    }
}
