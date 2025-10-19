<?php
class messages extends Controller{
    protected $message_model;
    public function __construct() {
        SessionManager::redirectToAuthIfNotLoggedIn();
        $this->message_model = $this->model('M_message');
    }
    
    public function index($section = 'all'){
        $data = [
            'section' => $section
        ];
        $this->view('messages/v_messages', $data);
    }
    
    public function all(){
        $this->index('all');
    }
    
    public function groups(){
        $this->index('groups');
    }
    
    public function batch(){
        $this->index('batch');
    }
    
    public function starred(){
        $this->index('starred');
    }
    
    public function conversation($userId = null){
        if($userId) {
            // Load conversation with specific user
            $data = [
                'section' => 'conversation',
                'user_id' => $userId,
                'conversation_data' => $this->message_model->getConversation($_SESSION['user_id'], $userId)
            ];
        } else {
            // No user specified, redirect to all messages
            $data = [
                'section' => 'all'
            ];
        }
        $this->view('messages/v_messages', $data);
    }
    
    // Send message
    public function sendMessage() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Get JSON input
            $input = json_decode(file_get_contents('php://input'), true);
            
            // Support both 'content' and 'message_text' parameters
            $messageText = '';
            if (isset($input['content'])) {
                $messageText = trim($input['content']);
            } elseif (isset($input['message_text'])) {
                $messageText = trim($input['message_text']);
            }
            
            if (!$input || !isset($input['receiver_id']) || empty($messageText)) {
                echo json_encode(['success' => false, 'message' => 'Invalid input data. Missing receiver_id or message content.']);
                return;
            }
            
            $senderId = $_SESSION['user_id'];
            $receiverId = filter_var($input['receiver_id'], FILTER_VALIDATE_INT);
            $conversationId = isset($input['conversation_id']) ? filter_var($input['conversation_id'], FILTER_VALIDATE_INT) : null;
            
            if (!$receiverId || empty($messageText)) {
                echo json_encode(['success' => false, 'message' => 'Invalid receiver or message']);
                return;
            }
            
            $result = $this->message_model->sendMessage($senderId, $receiverId, $messageText, $conversationId);
            
            if ($result && isset($result['message_id'])) {
                echo json_encode([
                    'success' => true, 
                    'message_id' => $result['message_id'],
                    'conversation_id' => $result['conversation_id'],
                    'message' => 'Message sent successfully'
                ]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to send message']);
            }
        }
    }
    
    // Delete conversation
    public function deleteConversation() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Get JSON input
            $input = json_decode(file_get_contents('php://input'), true);
            
            if (!$input || !isset($input['conversation_id'])) {
                echo json_encode(['success' => false, 'message' => 'Invalid input data']);
                return;
            }
            
            $conversationId = filter_var($input['conversation_id'], FILTER_VALIDATE_INT);
            $userId = $_SESSION['user_id'];
            
            if (!$conversationId) {
                echo json_encode(['success' => false, 'message' => 'Invalid conversation ID']);
                return;
            }
            
            $result = $this->message_model->deleteConversation($conversationId, $userId);
            
            if ($result) {
                echo json_encode(['success' => true, 'message' => 'Conversation deleted successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to delete conversation']);
            }
        }
    }
    
    // Report conversation
    public function reportConversation() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Get JSON input
            $input = json_decode(file_get_contents('php://input'), true);
            
            if (!$input || !isset($input['conversation_id']) || !isset($input['reason'])) {
                echo json_encode(['success' => false, 'message' => 'Invalid input data']);
                return;
            }
            
            $conversationId = filter_var($input['conversation_id'], FILTER_VALIDATE_INT);
            $reason = trim($input['reason']);
            $reporterId = $_SESSION['user_id'];
            
            if (!$conversationId || empty($reason)) {
                echo json_encode(['success' => false, 'message' => 'Invalid conversation ID or reason']);
                return;
            }
            
            $result = $this->message_model->reportConversation($conversationId, $reporterId, $reason);
            
            if ($result) {
                echo json_encode(['success' => true, 'message' => 'Conversation reported successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to report conversation']);
            }
        }
    }
    
    // Get conversations (AJAX)
    public function getConversations() {
        $userId = $_SESSION['user_id'];
        $conversations = $this->message_model->getUserConversations($userId);
        
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'conversations' => $conversations]);
    }
    
    // Get messages for a conversation (AJAX)
    public function getMessages($conversationId = null) {
        if (!$conversationId) {
            echo json_encode(['success' => false, 'message' => 'Conversation ID required']);
            return;
        }
        
        $userId = $_SESSION['user_id'];
        $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 50;
        $offset = isset($_GET['offset']) ? (int)$_GET['offset'] : 0;
        
        $messages = $this->message_model->getConversationMessages($conversationId, $userId, $limit, $offset);
        $conversationDetails = $this->message_model->getConversationDetails($conversationId, $userId);
        
        if ($conversationDetails) {
            // Mark messages as read
            $this->message_model->markAsRead($conversationId, $userId);
            
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true, 
                'messages' => $messages,
                'conversation' => $conversationDetails
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Conversation not found']);
        }
    }
    
    // Get available users for new conversation (AJAX)
    public function getAvailableUsers() {
        header('Content-Type: application/json');
        
        try {
            // Use 'id' from session to match your users table
            $userId = $_SESSION['user_id']; // This should be the user's id
            $searchQuery = isset($_GET['search']) ? trim($_GET['search']) : '';
            $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 20;
            
            // Get real users from database
            $users = $this->message_model->getAvailableUsers($userId, $searchQuery, $limit);
            
            if ($users && count($users) > 0) {
                echo json_encode([
                    'success' => true, 
                    'users' => $users, 
                    'count' => count($users),
                    'current_user_id' => $userId
                ]);
            } else {
                echo json_encode([
                    'success' => false, 
                    'message' => 'No users found in database',
                    'users' => [],
                    'count' => 0
                ]);
            }
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Database error: ' . $e->getMessage(),
                'users' => [],
                'error' => $e->getMessage()
            ]);
        }
    }
    
    // Get users by batch (AJAX)
    public function getUsersByBatch() {
        $userId = $_SESSION['user_id'];
        $batchYear = isset($_GET['batch_year']) ? (int)$_GET['batch_year'] : null;
        
        $users = $this->message_model->getUsersByBatch($userId, $batchYear);
        
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'users' => $users]);
    }
    
    // Get conversation with specific user (AJAX)
    public function getConversation() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            header('Content-Type: application/json');
            
            // Get JSON input
            $input = json_decode(file_get_contents('php://input'), true);
            
            if (!$input || !isset($input['partner_id'])) {
                echo json_encode(['success' => false, 'message' => 'Partner ID required']);
                return;
            }
            
            $userId = $_SESSION['user_id'];
            $partnerId = filter_var($input['partner_id'], FILTER_VALIDATE_INT);
            
            if (!$partnerId) {
                echo json_encode(['success' => false, 'message' => 'Invalid partner ID']);
                return;
            }
            
            try {
                $conversation = $this->message_model->getConversation($userId, $partnerId);
                
                if ($conversation) {
                    echo json_encode([
                        'success' => true,
                        'conversation_id' => $conversation['conversation_id'],
                        'messages' => $conversation['messages']
                    ]);
                } else {
                    // No existing conversation
                    echo json_encode([
                        'success' => false,
                        'message' => 'No existing conversation found',
                        'conversation_id' => null,
                        'messages' => []
                    ]);
                }
            } catch (Exception $e) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Database error: ' . $e->getMessage()
                ]);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'POST method required']);
        }
    }
}
?>