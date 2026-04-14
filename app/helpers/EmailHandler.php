<?php

use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;

class EmailHandler
{
    public static function send(string $toEmail, string $subject, string $htmlBody, string $toName = '', ?string $plainBody = null): bool
    {
        if (trim($toEmail) === '' || trim($subject) === '') {
            return false;
        }

        $mail = new PHPMailer(true);

        try {
            if (defined('MAIL_MAILER') && strtolower((string) MAIL_MAILER) === 'smtp') {
                $mail->isSMTP();
                $mail->Host = MAIL_HOST;
                $mail->SMTPAuth = true;
                $mail->Username = MAIL_USERNAME;
                $mail->Password = MAIL_PASSWORD;
                $mail->Port = (int) MAIL_PORT;
                $mail->SMTPSecure = self::resolveEncryptionMode();
            } else {
                $mail->isMail();
            }

            $mail->CharSet = 'UTF-8';
            $mail->setFrom(MAIL_FROM_ADDRESS, MAIL_FROM_NAME);
            $mail->addAddress($toEmail, $toName);
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body = $htmlBody;
            $mail->AltBody = $plainBody ?: strip_tags(str_replace(['<br>', '<br/>', '<br />'], PHP_EOL, $htmlBody));

            return $mail->send();
        } catch (Exception $e) {
            error_log('[EmailHandler] send failed: ' . $e->getMessage());
            return false;
        } catch (Throwable $e) {
            error_log('[EmailHandler] unexpected error: ' . $e->getMessage());
            return false;
        }
    }

    public static function sendOtpEmail(string $email, string $purpose, int $otp): bool
    {
        $purposeLabel = trim(str_replace('_', ' ', $purpose));
        if ($purposeLabel === '') {
            $purposeLabel = 'verification';
        }

        $subject = 'Your Gradlink OTP';
        $htmlBody = '
            <h2>Email Verification</h2>
            <p>Your OTP for ' . htmlspecialchars($purposeLabel, ENT_QUOTES, 'UTF-8') . ' is:</p>
            <h1 style="letter-spacing: 2px;">' . (int) $otp . '</h1>
            <p>This code expires in 5 minutes.</p>
        ';

        $plainBody = "Email Verification\n"
            . "Your OTP for " . $purposeLabel . " is: " . (int) $otp . "\n"
            . "This code expires in 5 minutes.";

        return self::send($email, $subject, $htmlBody, '', $plainBody);
    }

    public static function sendNotificationEmail(string $email, string $recipientName, string $type, string $messageText, ?string $link = null): bool
    {
        $typeLabel = ucwords(str_replace('_', ' ', trim($type)));
        $safeName = trim($recipientName) !== '' ? trim($recipientName) : 'there';
        $safeText = trim($messageText) !== '' ? trim($messageText) : 'You have a new notification.';

        $subject = 'New ' . $typeLabel . ' notification on Gradlink';

        $htmlBody = '
            <h2>Hi ' . htmlspecialchars($safeName, ENT_QUOTES, 'UTF-8') . ',</h2>
            <p>You have a new <strong>' . htmlspecialchars($typeLabel, ENT_QUOTES, 'UTF-8') . '</strong> notification on Gradlink.</p>
            <p>' . htmlspecialchars($safeText, ENT_QUOTES, 'UTF-8') . '</p>
        ';

        $plainBody = "Hi " . $safeName . ",\n"
            . "You have a new " . $typeLabel . " notification on Gradlink.\n"
            . $safeText;

        if ($link) {
            $safeLink = htmlspecialchars($link, ENT_QUOTES, 'UTF-8');
            $htmlBody .= '<p><a href="' . $safeLink . '">View on Gradlink</a></p>';
            $plainBody .= "\nView on Gradlink: " . $link;
        }

        return self::send($email, $subject, $htmlBody, $safeName, $plainBody);
    }

    private static function resolveEncryptionMode(): string
    {
        $mode = strtolower(trim((string) (defined('MAIL_ENCRYPTION') ? MAIL_ENCRYPTION : 'tls')));

        if ($mode === 'ssl' || $mode === 'smtps') {
            return PHPMailer::ENCRYPTION_SMTPS;
        }

        if ($mode === 'tls' || $mode === 'starttls') {
            return PHPMailer::ENCRYPTION_STARTTLS;
        }

        return '';
    }
}