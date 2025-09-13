<?php
class Explore extends Controller
{
    private $m;
    
    public function __construct()
    {
        SessionManager::redirectToAuthIfNotLoggedIn();
        $this->m = $this->model('M_explore');
    }
    
    /**
     * Main explore page - shows trending content or search results
     */
    public function index()
    {
        $data = [
            'query' => '',
            'filter' => 'all',
            'results' => []
        ];
        $data['query'] = $this->getQueryParam('q', '');
        $this->view('v_explore', $data);
    }
    
    /**
     * AJAX search endpoint
     */
    public function search()
    {
    }
}
?>