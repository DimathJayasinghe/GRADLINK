<?php
class GetSkeleton extends Controller
{
    public function __construct()
    {
        // No authentication required for skeleton loading
    }

    /**
     * GET /GetSkeleton?require=postSkeleton
     * Returns the requested skeleton HTML from app/views/inc/skeleton/{name}.php
     */
    public function index()
    {
        $name = $this->getQueryParam('require', null);
        if ($name === null) {
            http_response_code(400);
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'error' => 'missing_param',
                'message' => 'Pass ?require=<skeleton_name>'
            ]);
            return;
        }

        // Sanitize using Sanitizer (trim + null byte removal), then allow-list characters
        $cleanName = Sanitizer::sanitizeString($name);
        $safeName = preg_replace('/[^a-zA-Z0-9_-]/', '', $cleanName);
        if ($safeName === '') {
            http_response_code(400);
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'error'   => 'invalid_name',
                'message' => 'Invalid skeleton name'
            ]);
            return;
        }

        $base = APPROOT . '/views/inc/skeleton';
        $file = $base . '/' . $safeName . '.php';

        if (!file_exists($file)) {
            http_response_code(404);
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'error'   => 'not_found',
                'message' => 'Skeleton not found'
            ]);
            return;
        }

        header('Content-Type: text/html; charset=utf-8');
        include $file; // Output the skeleton HTML directly
    }
}
