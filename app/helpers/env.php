<?php
// Minimal dotenv-style loader for local/dev usage
if (!function_exists('gl_bootstrap_env')) {
    function gl_bootstrap_env(array $paths = null): void {
        $projectRoot = dirname(__DIR__, 2);
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
            if ($line === '' || $line[0] === '#') {
                continue;
            }
            if (strpos($line, '=') === false) {
                continue;
            }
            [$key, $value] = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value);
            $len = strlen($value);
            if ($len >= 2 && (($value[0] === '"' && $value[$len - 1] === '"') || ($value[0] === "'" && $value[$len - 1] === "'"))) {
                $value = substr($value, 1, -1);
            }
            putenv("$key=$value");
            $_ENV[$key] = $value;
        }
    }
}

if (!function_exists('gl_env')) {
    function gl_env(string $key, $default = null) {
        $val = getenv($key);
        if ($val === false || $val === '') {
            return $default;
        }
        return $val;
    }
}
