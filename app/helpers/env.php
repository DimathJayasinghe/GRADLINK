<?php
/**
 * Environment Variable Loader
 * Minimal dotenv-style loader for loading .env files
 */

if (!function_exists('gl_bootstrap_env')) {
    /**
     * Bootstrap environment variables from .env files
     * @param array|null $paths Custom paths to .env files (optional)
     */
    function gl_bootstrap_env(array $paths = null): void {
        $projectRoot = dirname(__DIR__, 2);
        
        // Default paths: check .env.local first, then .env
        $paths = $paths ?? [
            $projectRoot . '/.env.local',
            $projectRoot . '/.env',
        ];
        
        foreach ($paths as $path) {
            gl_load_env_file($path);
        }
    }
}

if (!function_exists('gl_load_env_file')) {
    /**
     * Load a single .env file and set environment variables
     * @param string $path Path to .env file
     */
    function gl_load_env_file(string $path): void {
        if (!is_readable($path)) {
            return;
        }
        
        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        if ($lines === false) {
            return;
        }
        
        foreach ($lines as $line) {
            $line = trim($line);
            
            // Skip empty lines and comments
            if ($line === '' || $line[0] === '#') {
                continue;
            }
            
            // Skip lines without '='
            if (strpos($line, '=') === false) {
                continue;
            }
            
            // Parse KEY=VALUE
            [$key, $value] = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value);
            
            // Remove surrounding quotes
            $len = strlen($value);
            if ($len >= 2 && (
                ($value[0] === '"' && $value[$len - 1] === '"') || 
                ($value[0] === "'" && $value[$len - 1] === "'")
            )) {
                $value = substr($value, 1, -1);
            }
            
            // Set environment variable
            putenv("$key=$value");
            $_ENV[$key] = $value;
        }
    }
}

if (!function_exists('gl_env')) {
    /**
     * Get an environment variable with optional default
     * @param string $key Environment variable key
     * @param mixed $default Default value if not found
     * @return mixed
     */
    function gl_env(string $key, $default = null) {
        $val = getenv($key);
        if ($val === false || $val === '') {
            return $default;
        }
        return $val;
    }
}
?>
