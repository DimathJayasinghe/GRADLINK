<?php 
    // Database configuration
    // Raw host only (no protocol, no port punctuation); port defined separately
    
    // For local hosted databse config

    // define('DB_HOST', 'localhost');
    // define('DB_PORT', 3306);
    // define('DB_CHARSET', 'utf8mb4');
    // define('DB_USER', 'root');
    // define('DB_PASSWORD', '1234');
    // define('DB_NAME', 'gl_db');
    
    // Main database

    // define('DB_HOST', 'mysql-gradlink.alwaysdata.net');
    // define('DB_PORT', 3306);
    // define('DB_CHARSET', 'utf8mb4');
    // define('DB_USER', 'gradlink');
    // define('DB_PASSWORD', '!G1rA2dL3iN4k');
    // define('DB_NAME', 'gradlink_main');
    
    // development database
    define('DB_HOST', 'mysql-gradlink.alwaysdata.net');
    define('DB_PORT', 3306);
    define('DB_CHARSET', 'utf8mb4');
    define('DB_USER', 'gradlink');
    define('DB_PASSWORD', '!G1rA2dL3iN4k');
    define('DB_NAME', 'gradlink_dev');
    

    
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
    if ($gl_basePath === '/' || $gl_basePath === '/') { $gl_basePath = ''; }
    define('URLROOT', $gl_scheme . '://' . $gl_host . $gl_basePath);

    // WEBSITE_NAME
    define('SITENAME', 'GRADLINK');
?>