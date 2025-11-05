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
}
