<?php
class Mainfeed extends Controller
{
    protected $pagesModel;
    protected $postModel;

    public function __construct()
    {
        SessionManager::redirectToAuthIfNotLoggedIn();
        $this->pagesModel = $this->model('M_mainfeed');
        $this->postModel = $this->model('M_post');
    }

    public function index()
    {
        // View the main feed page
        SessionManager::redirectToAuthIfNotLoggedIn();
        $feed_type = strtolower($this->getQueryParam('feed_type', 'for_you')); // Default to 'for_you' feed

        // If a feed_type is requested via query param, return JSON for client-side rendering
        if ($this->getQueryParam('feed_type', null) !== null) {
            header('Content-Type: application/json');
            // Use pagesModel for both 'for_you' and 'following' feeds to avoid invalid arg to getFeed
            $offsetRound = $this->getQueryParam('offsetRound', 1);
            $posts = $this->pagesModel->getPosts($feed_type, $offsetRound);
            $uid = $_SESSION['user_id'];
            foreach ($posts as $p) {
                $p->liked = $this->postModel->isLiked($p->id, $uid);
            }
            echo json_encode(['success' => true, 'posts' => $posts]);
            return;
        }

        // Otherwise render the page with initial data
        $data = [];
        $this->view('v_mainfeed', $data);
    }

    public function newPosts(){
        // Return new posts since a given timestamp
        SessionManager::redirectToAuthIfNotLoggedIn();
        header('Content-Type: application/json');

        $feed_type = strtolower($this->getQueryParam('feed_type', 'for_you'));
        $since = $this->getQueryParam('since', null);
        if ($since === null) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'missing_param', 'message' => 'Missing ?since=<timestamp> parameter']);
            return;
        }

        // Fetch posts newer than the given timestamp
        $allPosts = $this->pagesModel->getPosts($feed_type, 1); // Fetch first round of posts
        $data = ['succcess' => false, 'count' => 0];
        foreach ($allPosts as $p) {
            if (strtotime($p->created_at) > strtotime($since)) {
                $data['succcess'] = true;
                $data['count'] += 1;
            }
        }

        echo json_encode($data);
    }
}
