<?php
class Sanitizer {
    // For database storage - minimal sanitization
    public static function sanitizeForDatabase(array $input): array {
        foreach ($input as $key => $value) {
            if (is_string($value)) {
                $input[$key] = trim(str_replace("\0", "", $value));
            }
        }
        return $input;
    }

    // For HTML output - aggressive sanitization
    public static function sanitizeForOutput(string $input): string {
        return htmlspecialchars(strip_tags(trim($input)), ENT_QUOTES, 'UTF-8');
    }

    public static function sanitizeArray(array $input): array {
        return self::sanitizeForDatabase($input);
    }

    public static function sanitizeString(string $input): string {
        return trim(str_replace("\0", "", $input));
    }

    public static function cleanInput(string $input): string {
        return trim(str_replace("\0", "", $input));
    }

    public static function isEmpty(string $value): bool {
        return empty(trim($value));
    }
}
?>