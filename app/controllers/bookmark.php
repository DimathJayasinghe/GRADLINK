<?php

class bookmark extends Controller
{
    protected $Model;
    public function __construct()
    {
        SessionManager::redirectToAuthIfNotLoggedIn();
        $this->Model = $this->model('M_bookmark');
    }

    private function jsonResponse($payload, $status = 200)
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($payload);
        return;
    }

    private function getRequestData()
    {
        $raw = file_get_contents('php://input');
        $json = json_decode($raw, true);
        if (is_array($json)) {
            return $json;
        }
        return $_POST;
    }

    private function normalizeType($type)
    {
        $type = strtolower(trim((string)$type));
        if ($type === 'post') $type = 'posts';
        if ($type === 'message') $type = 'messages';
        if ($type === 'event') $type = 'events';
        return $type;
    }

    private function resolveReferenceId($type, array $data)
    {
        if (isset($data['reference_id'])) return (int)$data['reference_id'];
        if ($type === 'events' && isset($data['event_id'])) return (int)$data['event_id'];
        if ($type === 'posts' && isset($data['post_id'])) return (int)$data['post_id'];
        if ($type === 'messages' && isset($data['message_id'])) return (int)$data['message_id'];
        return 0;
    }

    private function handleEventBookmarkState($userId, $referenceId, $bookmarked)
    {
        $bm = $this->model('M_event_bookmark');
        $current = $bm->isBookmarked((int)$userId, (int)$referenceId);
        $target = is_null($bookmarked) ? !$current : (bool)$bookmarked;

        if ($target) {
            if (!$current) {
                $bm->add((int)$userId, (int)$referenceId);
            }
        } else {
            if ($current) {
                $bm->remove((int)$userId, (int)$referenceId);
            }
        }

        return $target;
    }

    private function handleGenericBookmarkState($userId, $type, $referenceId, $bookmarked, $title, $url, $metadata)
    {
        $current = $this->Model->hasGenericBookmark((int)$userId, (string)$type, (int)$referenceId);
        $target = is_null($bookmarked) ? !$current : (bool)$bookmarked;

        if ($target) {
            $this->Model->createGenericBookmark((int)$userId, (string)$type, (int)$referenceId, $title, $url, $metadata);
        } else {
            $this->Model->deleteGenericBookmark((int)$userId, (string)$type, (int)$referenceId);
        }

        return $target;
    }

    public function index()
    {
        $this->redirect('/settings/bookmarks');
    }

    public function list()
    {
        $userId = $_SESSION['user_id'] ?? null;
        if (!$userId) {
            $this->jsonResponse(['ok' => false, 'error' => 'Not authenticated'], 401);
            return;
        }

        $type = isset($_GET['type']) ? $this->normalizeType($_GET['type']) : '';
        $items = $this->Model->getBookmarksByUserId((int)$userId);
        if ($type) {
            $items = array_values(array_filter($items, function($row) use ($type) {
                return strtolower((string)$row->bookmark_type) === $type;
            }));
        }

        $this->jsonResponse(['ok' => true, 'bookmarks' => $items]);
    }

    public function create()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->jsonResponse(['ok' => false, 'error' => 'Method not allowed'], 405);
            return;
        }

        $userId = $_SESSION['user_id'] ?? null;
        if (!$userId) {
            $this->jsonResponse(['ok' => false, 'error' => 'Not authenticated'], 401);
            return;
        }

        $data = $this->getRequestData();
        $type = $this->normalizeType($data['type'] ?? '');
        $referenceId = $this->resolveReferenceId($type, $data);
        $title = isset($data['title']) ? trim((string)$data['title']) : null;
        $url = isset($data['url']) ? trim((string)$data['url']) : null;
        $metadata = isset($data['metadata']) ? $data['metadata'] : null;

        if (!in_array($type, ['events', 'posts', 'messages', 'other'], true)) {
            $this->jsonResponse(['ok' => false, 'error' => 'Invalid bookmark type'], 400);
            return;
        }

        if ($referenceId <= 0 && $type !== 'other') {
            $this->jsonResponse(['ok' => false, 'error' => 'Invalid reference_id'], 400);
            return;
        }

        if ($type === 'events') {
            $state = $this->handleEventBookmarkState((int)$userId, (int)$referenceId, true);
            $this->jsonResponse(['ok' => true, 'bookmarked' => (bool)$state]);
            return;
        }

        $ok = $this->Model->createGenericBookmark((int)$userId, (string)$type, (int)$referenceId, $title, $url, $metadata);
        $this->jsonResponse(['ok' => (bool)$ok, 'bookmarked' => true]);
    }

    public function update()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->jsonResponse(['ok' => false, 'error' => 'Method not allowed'], 405);
            return;
        }

        $userId = $_SESSION['user_id'] ?? null;
        if (!$userId) {
            $this->jsonResponse(['ok' => false, 'error' => 'Not authenticated'], 401);
            return;
        }

        $data = $this->getRequestData();
        $type = $this->normalizeType($data['type'] ?? '');
        $referenceId = $this->resolveReferenceId($type, $data);
        $title = isset($data['title']) ? trim((string)$data['title']) : null;
        $url = isset($data['url']) ? trim((string)$data['url']) : null;
        $metadata = isset($data['metadata']) ? $data['metadata'] : null;

        $bookmarked = null;
        if (array_key_exists('bookmarked', $data)) {
            $raw = $data['bookmarked'];
            if ($raw === true || $raw === false) {
                $bookmarked = $raw;
            } else {
                $bookmarked = filter_var($raw, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
            }
        }

        if (!in_array($type, ['events', 'posts', 'messages', 'other'], true)) {
            $this->jsonResponse(['ok' => false, 'error' => 'Invalid bookmark type'], 400);
            return;
        }

        if ($referenceId <= 0 && $type !== 'other') {
            $this->jsonResponse(['ok' => false, 'error' => 'Invalid reference_id'], 400);
            return;
        }

        if ($type === 'events') {
            $state = $this->handleEventBookmarkState((int)$userId, (int)$referenceId, $bookmarked);
            $this->jsonResponse(['ok' => true, 'bookmarked' => (bool)$state]);
            return;
        }

        $state = $this->handleGenericBookmarkState((int)$userId, (string)$type, (int)$referenceId, $bookmarked, $title, $url, $metadata);
        $this->jsonResponse(['ok' => true, 'bookmarked' => (bool)$state]);
    }

    public function delete()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' && $_SERVER['REQUEST_METHOD'] !== 'DELETE') {
            $this->jsonResponse(['ok' => false, 'error' => 'Method not allowed'], 405);
            return;
        }

        $userId = $_SESSION['user_id'] ?? null;
        if (!$userId) {
            $this->jsonResponse(['ok' => false, 'error' => 'Not authenticated'], 401);
            return;
        }

        $data = $this->getRequestData();
        $type = $this->normalizeType($data['type'] ?? '');
        $referenceId = $this->resolveReferenceId($type, $data);

        if (!in_array($type, ['events', 'posts', 'messages', 'other'], true)) {
            $this->jsonResponse(['ok' => false, 'error' => 'Invalid bookmark type'], 400);
            return;
        }

        if ($referenceId <= 0 && $type !== 'other') {
            $this->jsonResponse(['ok' => false, 'error' => 'Invalid reference_id'], 400);
            return;
        }

        if ($type === 'events') {
            $bm = $this->model('M_event_bookmark');
            $bm->remove((int)$userId, (int)$referenceId);
            $this->jsonResponse(['ok' => true, 'bookmarked' => false]);
            return;
        }

        $this->Model->deleteGenericBookmark((int)$userId, (string)$type, (int)$referenceId);
        $this->jsonResponse(['ok' => true, 'bookmarked' => false]);
    }
    /**
     * API END POINTS FOR 
     * CREATING, DELETING, AND FETCHING BOOKMARKS
     * BOOKMARK TYPES:
     *  posts, messages, events, other
     */
}


?>