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
        $data = [
            'posts',
        ];
        $data['posts'] = $this->postModel->getFeed();
        // annotate liked by current user
        $uid = $_SESSION['user_id'];
        foreach ($data['posts'] as $p) {
            $p->liked = $this->postModel->isLiked($p->id, $uid);
        }
        $this->view('v_mainfeed', $data);
    }
}
