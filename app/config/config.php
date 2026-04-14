<?php
// Database Configuration - using environment variables
define('DB_HOST', gl_env('DB_HOST', 'localhost'));
define('DB_PORT', gl_env('DB_PORT', 3306)); 
define('DB_CHARSET', gl_env('DB_CHARSET', 'utf8mb4'));
define('DB_USER', gl_env('DB_USER', 'root'));
define('DB_PASSWORD', gl_env('DB_PASS', '1234'));
define('DB_NAME', gl_env('DB_NAME', 'gl_db'));

// Email Configuration (for OTP and notifications)
define('MAIL_MAILER', gl_env('MAIL_MAILER', 'smtp'));
define('MAIL_HOST', gl_env('MAIL_HOST', 'smtp.gmail.com'));
define('MAIL_PORT', gl_env('MAIL_PORT', 587));
define('MAIL_USERNAME', gl_env('MAIL_USERNAME', ''));
define('MAIL_PASSWORD', gl_env('MAIL_PASSWORD', ''));
define('MAIL_ENCRYPTION', gl_env('MAIL_ENCRYPTION', 'tls'));
define('MAIL_FROM_ADDRESS', gl_env('MAIL_FROM_ADDRESS', 'noreply@gradlink.com'));
define('MAIL_FROM_NAME', gl_env('MAIL_FROM_NAME', 'GRADLINK'));


define('APPROOT', dirname(dirname(__FILE__)));

// URL_ROOT (dynamic: works for localhost, vhost, or LAN IP)
$gl_scheme = 'http';
if (
    (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ||
    (isset($_SERVER['SERVER_PORT']) && (int)$_SERVER['SERVER_PORT'] === 443)
) {
    $gl_scheme = 'https';
}
$gl_host = $_SERVER['HTTP_HOST'] ?? ($_SERVER['SERVER_NAME'] ?? 'localhost');
$gl_script = $_SERVER['SCRIPT_NAME'] ?? '';
$gl_basePath = rtrim(str_replace('/', '/', dirname($gl_script)), '/');
// Remove /public from path since .htaccess handles internal routing
$gl_basePath = str_replace('/public', '', $gl_basePath);
if ($gl_basePath === '/' || $gl_basePath === '/') {
    $gl_basePath = '';
}
define('URLROOT', $gl_scheme . '://' . $gl_host . $gl_basePath);
// WEBSITE_NAME
define('SITENAME', 'GRADLINK');
