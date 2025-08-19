<?php
class Cookie
{
    public static function exists(string $name): bool
    {
        return isset($_COOKIE[$name]);
    }

    public static function get(string $name): ?string
    {
        return $_COOKIE[$name] ?? null;
    }

    public static function set(
        string $name,
        string $value,
        int $ttl = 0,
        string $path = '/',
        ?string $domain = null,
        bool $secure = null,
        bool $httpOnly = true,
        string $sameSite = 'Lax'
    ): bool {
        $expire = $ttl > 0 ? time() + $ttl : 0;
        $secure = $secure ?? (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');

        if (PHP_VERSION_ID >= 70300) {
            $opts = [
                'expires'  => $expire,
                'path'     => $path,
                'domain'   => $domain, // Remove the fallback to HTTP_HOST
                'secure'   => $secure,
                'httponly' => $httpOnly,
                'samesite' => $sameSite,
            ];
            $result = setcookie($name, $value, $opts);
        } else {
            // Fallback (no SameSite)
            $result = setcookie($name, $value, $expire, $path, $domain, $secure, $httpOnly);
        }

        if ($result) {
            $_COOKIE[$name] = $value;
        }
        return $result;
    }

    public static function setSecure(string $name, string $value, int $ttl): bool
    {
        return self::set($name, $value, $ttl, '/', null, true, true, 'Lax');
    }

    public static function delete(string $name, string $path = '/'): bool
    {
        unset($_COOKIE[$name]);
        return setcookie($name, '', time() - 3600, $path);
    }
}
?>