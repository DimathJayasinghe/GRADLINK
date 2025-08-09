<?php
class Cookie {
    public static function set($name, $value, $expiry = 3600, $options = []) {
        // Validate cookie name
        if (empty($name) || !is_string($name)) {
            return false;
        }

        // Handle array values
        if (is_array($value)) {
            $value = json_encode($value);
        }

        // Default security options
        $defaults = [
            'expires' => time() + $expiry,
            'path' => '/',
            'domain' => '',
            'secure' => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on',
            'httponly' => true,
            'samesite' => 'Strict'
        ];

        $options = array_merge($defaults, $options);

        // Check cookie size (4KB limit)
        if (strlen($value) > 4096) {
            return false;
        }

        return setcookie($name, $value, $options);
    }

    public static function get($name) {
        if (!isset($_COOKIE[$name]) || empty($name)) {
            return null;
        }

        $value = $_COOKIE[$name];
        
        // Try to decode JSON, return original if not JSON
        $decoded = json_decode($value, true);
        return (json_last_error() === JSON_ERROR_NONE) ? $decoded : $value;
    }

    public static function delete($name) {
        if (!isset($_COOKIE[$name])) {
            return false;
        }

        return setcookie($name, '', [
            'expires' => time() - 3600,
            'path' => '/',
            'httponly' => true,
            'secure' => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on',
            'samesite' => 'Strict'
        ]);
    }

    public static function exists($name) {
        return isset($_COOKIE[$name]);
    }

    // Secure method for sensitive data
    public static function setSecure($name, $value, $expiry = 3600) {
        return self::set($name, $value, $expiry, [
            'secure' => true,
            'httponly' => true,
            'samesite' => 'Strict'
        ]);
    }
}
?>