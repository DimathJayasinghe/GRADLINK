<?php
/**
 * Sanitizer - Input sanitization utility class
 * 
 * Provides methods to clean and sanitize user input for different contexts
 * like database storage or HTML output.
 */
class Sanitizer {
    /**
     * Sanitizes array data for database storage with minimal cleaning
     * 
     * Use this when preparing data for database operations, especially
     * when using prepared statements. This performs basic cleaning without
     * excessive escaping that would be handled by prepared statements.
     * 
     * @param array $input The input array to sanitize
     * @return array Sanitized array with trimmed values and null bytes removed
     */
    public static function sanitizeForDatabase(array $input): array {
        foreach ($input as $key => $value) {
            if (is_string($value)) {
                $input[$key] = trim(str_replace("\0", "", $value));
            }
        }
        return $input;
    }

    /**
     * Aggressively sanitizes a string for safe HTML output
     * 
     * Use this when outputting user-provided content directly to a webpage
     * to prevent XSS attacks. This method converts special characters to
     * HTML entities and removes all HTML tags.
     * 
     * @param string $input The input string to sanitize for HTML output
     * @return string Sanitized string safe for HTML display
     */
    public static function sanitizeForOutput(string $input): string {
        return htmlspecialchars(strip_tags(trim($input)), ENT_QUOTES, 'UTF-8');
    }

    /**
     * Sanitizes an entire array (alias for sanitizeForDatabase)
     * 
     * Use this for cleaning form submissions like $_POST or $_GET arrays
     * before processing the data.
     * 
     * @param array $input The input array to sanitize
     * @return array Sanitized array
     */
    public static function sanitizeArray(array $input): array {
        return self::sanitizeForDatabase($input);
    }

    /**
     * Sanitizes a single string with basic cleaning
     * 
     * Use this for individual string inputs that need basic sanitization
     * before validation or processing.
     * 
     * @param string $input The input string to sanitize
     * @return string Sanitized string with whitespace trimmed and null bytes removed
     */
    public static function sanitizeString(string $input): string {
        return trim(str_replace("\0", "", $input));
    }

    /**
     * Cleans a string input (alias for sanitizeString)
     * 
     * Use this for basic string cleaning before validation.
     * 
     * @param string $input The input string to clean
     * @return string Cleaned string with whitespace trimmed and null bytes removed
     */
    public static function cleanInput(string $input): string {
        return trim(str_replace("\0", "", $input));
    }

    /**
     * Checks if a string is empty after trimming whitespace
     * 
     * Use this for form validation to check if a required field
     * has actual content beyond just whitespace.
     * 
     * @param string $value The string to check
     * @return bool True if the string is empty after trimming, false otherwise
     */
    public static function isEmpty(string $value): bool {
        return empty(trim($value));
    }
}
?>