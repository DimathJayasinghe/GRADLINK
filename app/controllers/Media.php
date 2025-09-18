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
                $path = $this->baseDir . DIRECTORY_SEPARATOR . 'profile_pic' . DIRECTORY_SEPARATOR . 'default.jpg';
                if (!is_file($path)) { http_response_code(404); exit; }
            } else {
                http_response_code(404); exit; }
        }
        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        $mime = match($ext) {
            'png' => 'image/png',
            'jpg', 'jpeg' => 'image/jpeg',
            'gif' => 'image/gif',
            default => 'application/octet-stream'
        };
        header('Content-Type: ' . $mime);
        header('Content-Length: ' . filesize($path));
        header('Cache-Control: public, max-age=86400');
        readfile($path);
        exit;
    }
}
?>
