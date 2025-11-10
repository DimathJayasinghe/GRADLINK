<?php 
    // Database configuration
    define('DB_HOST', 'localhost');
    define('DB_USER', 'root');
    define('DB_PASSWORD', '');
    define('DB_NAME', 'gl_db');
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
    $gl_basePath = rtrim(str_replace('\\', '/', dirname($gl_script)), '/');
    if ($gl_basePath === '/' || $gl_basePath === '\\') { $gl_basePath = ''; }
    define('URLROOT', $gl_scheme . '://' . $gl_host . $gl_basePath);

    // WEBSITE_NAME
    define('SITENAME', 'GRADLINK');


?>