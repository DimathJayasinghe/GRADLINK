<?php
    class Core {
        // URL format --> /controller/method/params
        protected $currentContoller ="Hero"; // Default controller
        protected $currentMethod = "index";
        protected $params = [];

        public function __construct() {
            // print_r($this->getUrl());

            $url = $this->getUrl();
            
            // Check if URL exists and has controller
            if($url && isset($url[0])) {
                // Check if controller exists
                if(file_exists('../app/controllers/'.ucwords($url[0]).'.php')) {
                    // If exists, set as current controller
                    $this->currentContoller = ucwords($url[0]);
                    
                    // Unset the controller from the URL
                    unset($url[0]);
                } else {
                    $this->show404();
                    return; // Stop further execution if controller not found
                }
            }

            // Call the controller
            require_once '../app/controllers/' . $this->currentContoller . '.php';

            // Canonicalize URL: if request included 'index.php' in the path (e.g. /index.php/controller/method)
            // redirect to the pretty URL form to keep routing consistent (and keep query params except 'url')
            $requestUri = $_SERVER['REQUEST_URI'] ?? '';
            $pathInfo = $_SERVER['PATH_INFO'] ?? '';
            $usedIndexPhp = (strpos($requestUri, 'index.php') !== false) || (strpos($pathInfo, 'index.php') === 0);
            if ($usedIndexPhp && is_array($url) && count($url) > 0) {
                // Build canonical path
                $canonicalPath = '/' . implode('/', $url);
                // Rebuild query string excluding the internal 'url' param
                $qsArr = $_GET;
                if (isset($qsArr['url'])) unset($qsArr['url']);
                $qs = http_build_query($qsArr);
                $target = rtrim(URLROOT, '/') . $canonicalPath;
                if ($qs) $target .= '?' . $qs;
                // Avoid redirect loops: only redirect if current request URI is not already the canonical target
                $currentFull = (isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '');
                if (strpos($currentFull, $canonicalPath) === false) {
                    header('Location: ' . $target, true, 301);
                    exit();
                }
            }

            // Instantiate the controller class
            $this->currentContoller = new $this->currentContoller;

            // Check whether the method exists in the controller or not
            if ($url && isset($url[1])){
                if (method_exists($this->currentContoller, $url[1])) {
                    // If method exists, set as current method
                    $this->currentMethod = $url[1];

                    // Unset the method from the URL
                    unset($url[1]);
                } else {
                    // Method not found, show 404
                    $this->show404();
                    return;
                }
            }

            // Get the parameters
            $this->params = $url ? array_values($url) : [];

            // Call the current method with the parameters
            call_user_func_array([$this->currentContoller, $this->currentMethod], $this->params);
        }


        public function getUrl() {
            // 1) Prefer explicit 'url' GET param (used by RewriteRule index.php?url=...)
            if (isset($_GET['url']) && $_GET['url'] !== '') {
                $url = rtrim($_GET['url'], '/');
                $url = filter_var($url, FILTER_SANITIZE_URL);
                return explode('/', $url);
            }

            // 2) PATH_INFO (when calling index.php/controller/method)
            if (!empty($_SERVER['PATH_INFO'])) {
                $path = trim($_SERVER['PATH_INFO'], '/');
                $path = filter_var($path, FILTER_SANITIZE_URL);
                return explode('/', $path);
            }

            // 3) As a final fallback, parse REQUEST_URI and strip script/base path
            $requestUri = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH);
            $scriptName = $_SERVER['SCRIPT_NAME'] ?? '';

            // Remove script directory portion (e.g. /GRADLINK/public)
            $base = rtrim(str_replace('\\', '/', dirname($scriptName)), '/');
            if ($base && strpos($requestUri, $base) === 0) {
                $requestUri = substr($requestUri, strlen($base));
            }

            // Remove leading /index.php if present
            $requestUri = preg_replace('#^/index\\.php#', '', $requestUri);
            $requestUri = trim($requestUri, '/');
            if ($requestUri === '') return null;
            $requestUri = filter_var($requestUri, FILTER_SANITIZE_URL);
            return explode('/', $requestUri);
        }
        private function show404() {
            // Set HTTP 404 status code
            http_response_code(404);
            
            // Check if 404 view file exists
            if(file_exists('../app/views/errors/_404.php')) {
                require_once '../app/views/errors/_404.php';
            } else {
                // Fallback if 404 view doesn't exist
                echo "<h1>404 - Page Not Found</h1>";
                echo "<p>The page you are looking for could not be found.</p>";
            }
            
            // Stop execution
            exit();
        }
    }
?>