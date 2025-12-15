<?php 
    // Database configuration
    // Raw host only (no protocol, no port punctuation); port defined separately

    require_once dirname(__DIR__) . '/helpers/env.php';
    gl_bootstrap_env();

    //DB_Main_Config_Variables (all sourced from env)
    define('DB_HOST', gl_env('DB_HOST'));
    define('DB_PORT', (int) gl_env('DB_PORT'));
    define('DB_CHARSET', gl_env('DB_CHARSET'));
    define('DB_USER', gl_env('DB_USER'));
    define('DB_PASSWORD', gl_env('DB_PASSWORD'));
    define('DB_NAME', gl_env('DB_NAME'));

    
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