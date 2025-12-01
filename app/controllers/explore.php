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
        $query = isset($_GET['q']) ? trim($_GET['q']) : '';
        $filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';
        
        $data = [
            'query' => $query,
            'filter' => $filter,
            'results' => []
        ];
        
        // If there's a search query, perform the search
        if (!empty($query)) {
            $data['results'] = $this->performSearch($query, $filter);
            // Enrich results with user-specific data
            $currentUserId = $_SESSION['user_id'];
            $data['results'] = $this->enrichResults($data['results'], $currentUserId);
        }
        
        $this->view('v_explore', $data);
    }
    
    /**
     * AJAX search endpoint
     */
    public function search()
    {
        // Ensure this is an AJAX request
        if (!isset($_SERVER['HTTP_X_REQUESTED_WITH']) || 
            strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) !== 'xmlhttprequest') {
            http_response_code(403);
            echo json_encode(['error' => 'Invalid request']);
            exit;
        }
        
        $query = isset($_GET['q']) ? trim($_GET['q']) : '';
        $filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';
        
        if (empty($query)) {
            echo json_encode(['success' => true, 'results' => []]);
            exit;
        }
        
        $results = $this->performSearch($query, $filter);
        
        // Add user-specific data to results
        $currentUserId = $_SESSION['user_id'];
        $results = $this->enrichResults($results, $currentUserId);
        
        echo json_encode([
            'success' => true,
            'results' => $results,
            'query' => $query,
            'filter' => $filter
        ]);
        exit;
    }
    
    /**
     * Perform the actual search based on filter
     */
    private function performSearch($query, $filter, $limit = 20, $offset = 0)
    {
        $results = [];
        
        switch ($filter) {
            case 'posts':
                $results['posts'] = $this->m->searchPosts($query, $limit, $offset);
                break;
                
            case 'users':
                $results['users'] = $this->m->searchUsers($query, $limit, $offset);
                break;
                
            case 'alumni':
                $results['users'] = ['alumni' => $this->m->searchAlumni($query, $limit, $offset)];
                break;
                
            case 'undergrad':
                $results['users'] = ['undergrad' => $this->m->searchUndergrads($query, $limit, $offset)];
                break;
                
            case 'events':
                $results['events'] = $this->m->searchEvents($query, $limit, $offset);
                break;
                
            case 'all':
            default:
                // Search everything - show only 2 results per section, users first
                $results['users'] = [
                    'all' => $this->m->searchUsers($query, 2, 0)
                ];
                $results['events'] = $this->m->searchEvents($query, 2, 0);
                $results['posts'] = $this->m->searchPosts($query, 2, 0);
                break;
        }
        
        return $results;
    }
    
    /**
     * Enrich results with user-specific data (likes, follows, bookmarks, etc.)
     */
    private function enrichResults($results, $currentUserId)
    {
        // Add is_liked to posts
        if (isset($results['posts']) && !empty($results['posts'])) {
            foreach ($results['posts'] as &$post) {
                $post->is_liked = $this->m->isPostLiked($post->id, $currentUserId);
            }
        }
        
        // Add follow status to users
        if (isset($results['users'])) {
            $users = isset($results['users']['all']) ? $results['users']['all'] : 
                     (isset($results['users']['alumni']) ? $results['users']['alumni'] : 
                     (isset($results['users']['undergrad']) ? $results['users']['undergrad'] : 
                     $results['users']));
            
            if (is_array($users)) {
                foreach ($users as &$user) {
                    if ($user->id != $currentUserId) {
                        $user->is_following = $this->m->isFollowing($currentUserId, $user->id);
                        $user->has_pending_request = $this->m->hasPendingRequest($currentUserId, $user->id);
                    } else {
                        $user->is_self = true;
                    }
                }
            }
        }
        
        // Add bookmark status to events
        if (isset($results['events']) && !empty($results['events'])) {
            foreach ($results['events'] as &$event) {
                $event->is_bookmarked = $this->m->hasBookmarkedEvent($currentUserId, $event->id);
            }
        }
        
        return $results;
    }
}
?>