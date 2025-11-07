<?php 
    // Database configuration
    // Raw host only (no protocol, no port punctuation); port defined separately
    define('DB_HOST', 'sql12.freesqldatabase.com');
    define('DB_PORT', 3306);
    define('DB_CHARSET', 'utf8mb4');
    define('DB_USER', 'sql12806532');
    define('DB_PASSWORD', '32ShNEdnS3');
    define('DB_NAME', 'sql12806532');
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