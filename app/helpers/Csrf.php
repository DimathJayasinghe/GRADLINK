<?php
class Csrf {
    public static function ensureStarted(){
        if(session_status() === PHP_SESSION_NONE) session_start();
    }

    public static function getToken(): string{
        self::ensureStarted();
        if(empty($_SESSION['csrf_token'])){
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    public static function validate(string $token): bool{
        self::ensureStarted();
        if(empty($token)) return false;
        return hash_equals($_SESSION['csrf_token'] ?? '', $token);
    }

    // Read token from headers, POST field or JSON body
    public static function getTokenFromRequest(){
        // Header check: X-CSRF-Token
        if(!empty($_SERVER['HTTP_X_CSRF_TOKEN'])) return $_SERVER['HTTP_X_CSRF_TOKEN'];
        // form POST
        if(!empty($_POST['csrf_token'])) return $_POST['csrf_token'];
        // JSON body
        $body = file_get_contents('php://input');
        if($body){
            $json = json_decode($body, true);
            if(is_array($json) && isset($json['csrf_token'])) return $json['csrf_token'];
        }
        return null;
    }

    public static function validateRequest(): bool{
        $token = self::getTokenFromRequest();
        return $token ? self::validate($token) : false;
    }
}

// Intentionally no closing PHP tag to avoid accidental output
