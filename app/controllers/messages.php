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
    
    /**
     * API: Get available users for messaging
     * GET /messages/getAvailableUsers?search=query
     */
    public function getAvailableUsers() {
        header('Content-Type: application/json');
        
        try {
            $currentUserId = $_SESSION['user_id'];
            $searchTerm = $this->getQueryParam('search', null);
            
            $users = $this->message_model->getAvailableUsers($currentUserId, $searchTerm);
            
            echo json_encode([
                'success' => true,
                'users' => $users
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ]);
        }
    }
    
    /**
     * API: Get conversation with a specific user
     * GET /messages/getConversation?userId=123
     */
    public function getConversation() {
        header('Content-Type: application/json');
        
        try {
            $userId = $this->getQueryParam('userId', null);
            
            if (!$userId) {
                echo json_encode([
                    'success' => false,
                    'error' => 'User ID is required'
                ]);
                return;
            }
            
            $partner = $this->message_model->getConversationPartner($userId);
            
            if (!$partner) {
                echo json_encode([
                    'success' => false,
                    'error' => 'User not found'
                ]);
                return;
            }
            
            echo json_encode([
                'success' => true,
                'partner' => $partner
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ]);
        }
    }
    
    /**
     * API: Get messages between current user and another user
     * GET /messages/getMessages?userId=123
     */
    public function getMessages() {
        header('Content-Type: application/json');
        
        try {
            $currentUserId = $_SESSION['user_id'];
            $otherUserId = $this->getQueryParam('userId', null);
            
            if (!$otherUserId) {
                echo json_encode([
                    'success' => false,
                    'error' => 'User ID is required'
                ]);
                return;
            }
            
            $messages = $this->message_model->getMessages($currentUserId, $otherUserId);
            
            echo json_encode([
                'success' => true,
                'messages' => $messages
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ]);
        }
    }
    
    /**
     * API: Send a message
     * POST /messages/sendMessage
     * Body: {"recipientId": 123, "content": "message text"}
     */
    public function sendMessage() {
        header('Content-Type: application/json');
        
        try {
            // Get JSON input
            $input = json_decode(file_get_contents('php://input'), true);
            
            $currentUserId = $_SESSION['user_id'];
            $recipientId = $input['recipientId'] ?? null;
            $content = $input['content'] ?? null;
            
            if (!$recipientId || !$content) {
                echo json_encode([
                    'success' => false,
                    'error' => 'Recipient ID and content are required'
                ]);
                return;
            }
            
            if (trim($content) === '') {
                echo json_encode([
                    'success' => false,
                    'error' => 'Message content cannot be empty'
                ]);
                return;
            }
            
            $result = $this->message_model->sendMessage($currentUserId, $recipientId, $content);
            
            if ($result) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Message sent successfully'
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'error' => 'Failed to send message'
                ]);
            }
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ]);
        }
    }
    
    /**
     * API: Delete a message
     * GET /messages/deleteMessage?messageId=123
     */
    public function deleteMessage() {
        header('Content-Type: application/json');
        
        try {
            $currentUserId = $_SESSION['user_id'];
            $messageId = $this->getQueryParam('messageId', null);
            
            if (!$messageId) {
                echo json_encode([
                    'success' => false,
                    'error' => 'Message ID is required'
                ]);
                return;
            }
            
            $result = $this->message_model->deleteMessage($messageId, $currentUserId);
            
            if ($result) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Message deleted successfully'
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'error' => 'Failed to delete message or message not found'
                ]);
            }
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * API: Edit a message
     * POST /messages/editMessage
     * Body: {"messageId": 123, "content": "updated text"}
     */
    public function editMessage() {
        header('Content-Type: application/json');

        try {
            // Get JSON input
            $input = json_decode(file_get_contents('php://input'), true);

            $currentUserId = $_SESSION['user_id'];
            $messageId = $input['messageId'] ?? null;
            $content = $input['content'] ?? null;

            if (!$messageId || $content === null) {
                echo json_encode([
                    'success' => false,
                    'error' => 'Message ID and content are required'
                ]);
                return;
            }

            if (trim($content) === '') {
                echo json_encode([
                    'success' => false,
                    'error' => 'Message content cannot be empty'
                ]);
                return;
            }

            $updated = $this->message_model->updateMessage($messageId, $currentUserId, $content);

            if ($updated) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Message updated successfully'
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'error' => 'Failed to update message or permission denied'
                ]);
            }
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ]);
        }
    }
}    
?>