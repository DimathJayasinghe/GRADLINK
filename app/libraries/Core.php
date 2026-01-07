<?php
    class Core {
        // URL format --> /controller/method/params
        protected $currentContoller ="Hero"; // Default controller
        protected $currentMethod = "index";
        protected $params = [];
        private $db = null;

        public function __construct() {
            // print_r($this->getUrl());

            $url = $this->getUrl();
            
            // Log activity before processing the request
            $this->logActivity($url);
            
            if ($url && isset($url[0])) {
                $controllerSegment = $url[0];
                $resolvedFile = $this->resolveControllerFile($controllerSegment);
                if ($resolvedFile) {
                    // Set controller class name from filename and load file
                    $this->currentContoller = pathinfo($resolvedFile, PATHINFO_FILENAME);
                    require_once '../app/controllers/' . $resolvedFile;
                    
                    // Unset the controller from the URL
                    unset($url[0]);
                } else {
                    $this->show404();
                    return; // Stop further execution if controller not found
                }
            }else {
                // Default controller
                require_once '../app/controllers/' . $this->currentContoller . '.php';
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
            if (isset($_GET['url'])){
                $url = rtrim($_GET['url'], '/');
                $url = filter_var($url, FILTER_SANITIZE_URL);
                $url = explode('/', $url);

                return $url;
            }
            return null; // Explicitly return null when no URL parameter
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

        // Find the actual controller file name regardless of input case
        private function resolveControllerFile(string $name): ?string {
            $dir = '../app/controllers';
            if (!is_dir($dir)) return null;
            $target = strtolower($name);
            foreach (scandir($dir) as $file) {
                if (substr($file, -4) !== '.php') continue;
                if (strtolower(pathinfo($file, PATHINFO_FILENAME)) === $target) {
                    return $file; // Return exact-cased filename
                }
            }
            return null;
        }
        
        /**
         * Log user activity - tracks online users and logs URL access
         */
        private function logActivity($url) {
            try {
                // Skip static file requests (CSS, JS, images, fonts, etc.)
                $fullUrl = isset($_GET['url']) ? '/' . $_GET['url'] : '/';
                if ($this->isStaticFileRequest($fullUrl)) {
                    return;
                }
                
                // Ensure session is started before reading session data
                SessionManager::ensureStarted();
                
                // Get database connection
                $this->db = new Database();
                
                // Get request info from session
                $userId = $_SESSION['user_id'] ?? null;
                $userName = $_SESSION['user_name'] ?? null;
                $userRole = $_SESSION['user_role'] ?? null;
                $sessionId = session_id();
                $ipAddress = $this->getClientIp();
                $userAgent = substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 512);
                $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
                $referer = substr($_SERVER['HTTP_REFERER'] ?? '', 0, 512);
                $controller = $url[0] ?? 'Hero';
                $action = $url[1] ?? 'index';
                
                // 1. Update online users table (only for logged-in users)
                if ($userId) {
                    $this->updateOnlineUser($userId, $sessionId, $ipAddress, $userAgent, $fullUrl);
                }
                
                // 2. Log URL access (includes guest name/role from session if available)
                $this->logUrlAccess($userId, $userName, $userRole, $sessionId, $fullUrl, $method, $controller, $action, $ipAddress, $userAgent, $referer);
                
                // 3. Cleanup old online users (every 100th request to avoid overhead)
                if (rand(1, 100) === 1) {
                    $this->cleanupOnlineUsers();
                }
                
            } catch (Exception $e) {
                // Silently fail - logging should never break the app
                error_log("Activity logging error: " . $e->getMessage());
            }
        }
        
        /**
         * Check if request is for a static file (should not be logged)
         */
        private function isStaticFileRequest($url) {
            $staticExtensions = ['.css', '.js', '.jpg', '.jpeg', '.png', '.gif', '.svg', '.ico', '.woff', '.woff2', '.ttf', '.eot', '.map'];
            $staticPaths = ['/css/', '/js/', '/images/', '/fonts/', '/assets/', '/public/'];
            
            $urlLower = strtolower($url);
            
            // Check file extensions
            foreach ($staticExtensions as $ext) {
                if (substr($urlLower, -strlen($ext)) === $ext) {
                    return true;
                }
            }
            
            // Check static paths
            foreach ($staticPaths as $path) {
                if (strpos($urlLower, $path) !== false) {
                    return true;
                }
            }
            
            return false;
        }
        
        /**
         * Update online users table
         */
        private function updateOnlineUser($userId, $sessionId, $ipAddress, $userAgent, $currentUrl) {
            // Use INSERT ... ON DUPLICATE KEY UPDATE to avoid duplicates
            $this->db->query("
                INSERT INTO online_users (user_id, session_id, ip_address, user_agent, current_url, last_activity)
                VALUES (:user_id, :session_id, :ip_address, :user_agent, :current_url, NOW())
                ON DUPLICATE KEY UPDATE 
                    session_id = VALUES(session_id),
                    ip_address = VALUES(ip_address),
                    user_agent = VALUES(user_agent),
                    current_url = VALUES(current_url),
                    last_activity = NOW()
            ");
            
            $this->db->bind(':user_id', $userId);
            $this->db->bind(':session_id', $sessionId);
            $this->db->bind(':ip_address', $ipAddress);
            $this->db->bind(':user_agent', $userAgent);
            $this->db->bind(':current_url', $currentUrl);
            $this->db->execute();
        }
        
        /**
         * Log URL access to access_logs table
         */
        private function logUrlAccess($userId, $userName, $userRole, $sessionId, $url, $method, $controller, $action, $ipAddress, $userAgent, $referer) {
            $this->db->query("
                INSERT INTO access_logs (user_id, user_name, user_role, session_id, url, method, controller, action, ip_address, user_agent, referer)
                VALUES (:user_id, :user_name, :user_role, :session_id, :url, :method, :controller, :action, :ip_address, :user_agent, :referer)
            ");
            
            $this->db->bind(':user_id', $userId);
            $this->db->bind(':user_name', $userName);
            $this->db->bind(':user_role', $userRole);
            $this->db->bind(':session_id', $sessionId);
            $this->db->bind(':url', $url);
            $this->db->bind(':method', $method);
            $this->db->bind(':controller', $controller);
            $this->db->bind(':action', $action);
            $this->db->bind(':ip_address', $ipAddress);
            $this->db->bind(':user_agent', $userAgent);
            $this->db->bind(':referer', $referer);
            $this->db->execute();
        }
        
        /**
         * Cleanup online users older than 5 minutes
         */
        private function cleanupOnlineUsers() {
            $this->db->query("DELETE FROM online_users WHERE last_activity < DATE_SUB(NOW(), INTERVAL 5 MINUTE)");
            $this->db->execute();
        }
        
        /**
         * Get client IP address (handles proxies)
         */
        private function getClientIp() {
            $ip = '';
            if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
                $ip = $_SERVER['HTTP_CLIENT_IP'];
            } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                $ip = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])[0];
            } else {
                $ip = $_SERVER['REMOTE_ADDR'] ?? '';
            }
            return substr(trim($ip), 0, 45);
        }
    }
?>
