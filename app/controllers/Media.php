<?php
class Media extends Controller {
    private string $baseDir;

    public function __construct() {
        $this->baseDir = realpath(__DIR__ . '/../storage') ?: __DIR__ . '/../storage';
    }

    // /media/profile/{filename}
    public function profile($filename = '') {
        $this->serve('profile_pic', $filename ?: 'default.jpg');
    }

    // /media/post/{filename}
    public function post($filename = '') {
        $this->serve('posts', $filename);
    }

    private function serve(string $folder, string $filename): void {
        if (!$filename) { http_response_code(404); exit; }
        // Allow only safe filenames
        if (!preg_match('/^[A-Za-z0-9._-]+$/', $filename)) { http_response_code(400); exit; }

        $path = $this->baseDir . DIRECTORY_SEPARATOR . $folder . DIRECTORY_SEPARATOR . $filename;
        if (!is_file($path)) {
            if ($folder === 'profile_pic') {
                $fallbackCandidates = ['default.jpg', 'default.png'];
                $resolvedFallback = null;
                foreach ($fallbackCandidates as $fallback) {
                    $candidate = $this->baseDir . DIRECTORY_SEPARATOR . 'profile_pic' . DIRECTORY_SEPARATOR . $fallback;
                    if (is_file($candidate)) {
                        $resolvedFallback = $candidate;
                        break;
                    }
                }
                if ($resolvedFallback === null) { http_response_code(404); exit; }
                $path = $resolvedFallback;
            } else {
                http_response_code(404); exit; }
        }
        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        $mime = match($ext) {
            'png' => 'image/png',
            'jpg', 'jpeg' => 'image/jpeg',
            'gif' => 'image/gif',
            'pdf' => 'application/pdf',
            default => 'application/octet-stream'
        };
        header('Content-Type: ' . $mime);
        if ($mime === 'application/pdf') {
            // Display PDFs inline with a friendly filename
            $disposition = 'inline; filename="' . basename($path) . '"';
            header('Content-Disposition: ' . $disposition);
        }
        header('Content-Length: ' . filesize($path));
        header('Cache-Control: public, max-age=86400');
        readfile($path);
        exit;
    }
    public function event($filename = '') {
        $this->serve('posts', $filename);
    }

    // /media/certificate/{filename}
    public function certificate($filename = '') {
        $this->serve('certificates', $filename);
    }

    // /media/fundraiser/{filename}
    public function fundraiser($filename = '') {
        $this->serve('fundraisers', $filename);
    }
}
?>
