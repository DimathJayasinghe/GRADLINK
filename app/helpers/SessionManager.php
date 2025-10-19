<?php
class SessionManager
{
    /**
     * Ensure session is started - call this everywhere
     */
    public static function ensureStarted(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * Check if user is logged in - global check
     */
    public static function isLoggedIn(): bool
    {
        self::ensureStarted();
        return !empty($_SESSION['user_id']);
    }

    /**
     * Get current user ID - accessible anywhere
     */
    public static function getUserId(): ?int
    {
        return self::isLoggedIn() ? (int)$_SESSION['user_id'] : null;
    }

    /**
     * Get current user data - accessible anywhere
     */
    public static function getUser(): ?array
    {
        if (!self::isLoggedIn()) return null;
        
        return [
            'id' => $_SESSION['user_id'],
            'name' => $_SESSION['user_name'] ?? '',
            'email' => $_SESSION['user_email'] ?? '',
            'role' => $_SESSION['user_role'] ?? '',
        ];
    }

    /**
     * Redirect if already logged in - use in any controller
     */
    public static function redirectIfLoggedIn(string $to): void
    {
        if (self::isLoggedIn()) {
            header('Location: '.URLROOT . $to);
            exit();
        }
    }

    /**
     * Redirect to auth login if user not logged in - use in any controller
     */
    public static function redirectToAuthIfNotLoggedIn(): void{
        if(!self::isLoggedIn()){
            header('Location: '.URLROOT . '/auth');
            exit();
        }
    }

    /**
     * Require login - use in any protected controller/page
     */
    public static function requireAuth(string $redirectTo = null): void
    {
        if (!self::isLoggedIn()) {
            $redirectTo = $redirectTo ?? (URLROOT . '/auth/login');
            header('Location: ' . $redirectTo);
            exit();
        }
    }

    /**
     * Create user session
     */
    public static function createUserSession(object $user): void
    {
        self::ensureStarted();
        $_SESSION['user_id'] = $user->id;
        $_SESSION['user_name'] = $user->name ?? ($user->full_name ?? '');
        $_SESSION['user_email'] = $user->email ?? '';
        $_SESSION['user_role'] = $user->role ?? '';
        $_SESSION['special_alumni'] = $user->special_alumni?? false;
        $_SESSION['profile_image'] = $user->profile_image ?? 'default.jpg';
        $_SESSION['login_time'] = time();
    }

    /**
     * Destroy session completely
     */
    public static function destroySession(): void
    {
        self::ensureStarted();
        session_unset();
        session_destroy();
        // Remember token clearing removed
    }

    /**
     * Flash messages - set from anywhere
     */
    public static function setFlash(string $type, string $message): void
    {
        self::ensureStarted();
        $_SESSION['flash_messages'][] = ['type' => $type, 'message' => $message];
    }

    /**
     * Get flash messages - retrieve from anywhere
     */
    public static function getFlash(): array
    {
        self::ensureStarted();
        $messages = $_SESSION['flash_messages'] ?? [];
        unset($_SESSION['flash_messages']);
        return $messages;
    }

    /**
     * Check whether current user has a specific role
     */
    public static function hasRole(string $role): bool
    {
        self::ensureStarted();
        return isset($_SESSION['user_role']) && strtolower($_SESSION['user_role']) === strtolower($role);
    }

    /**
     * Require a specific role; redirect if not authorized
     */
    public static function requireRole(string $role, string $redirectTo = null): void
    {
        self::ensureStarted();
        if (!self::isLoggedIn() || !self::hasRole($role)) {
            $target = $redirectTo ?? (URLROOT . '/auth');
            header('Location: ' . $target);
            exit();
        }
    }

    public static function isSpecialAlumni(){
        self::ensureStarted();
        if (self::isLoggedIn()){
            return $_SESSION['special_alumni'];
        }
    }
}