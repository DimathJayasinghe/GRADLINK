<?php
class Chat extends Controller {
    private $chatModel;

    public function __construct() {
        $this->chatModel = $this->model('M_chat');
    }

    // Default chat page
    public function index() {
        $chatId = 1; // example chat room
        $messages = $this->chatModel->getMessages($chatId);
        
        // Mock chat users data for now
        $chatUsers = [
            ['id' => 1, 'name' => 'John Doe', 'profile_image' => 'default.jpg', 'avatar' => 'JD', 'role' => 'Alumni'],
            ['id' => 2, 'name' => 'Jane Smith', 'profile_image' => 'default.jpg', 'avatar' => 'JS', 'role' => 'Undergraduate']
        ];
        
        // Mock profile data
        $selectedUserId = 2;
        $profileData = [
            'id' => $selectedUserId,
            'name' => 'Jane Smith',
            'profile_image' => 'default.jpg',
            'bio' => 'Software Developer',
            'role' => 'Undergraduate',
            'avatar' => 'JS'
        ];

        $data = [
            'messages' => $messages,
            'chatId' => $chatId,
            'chatUsers' => $chatUsers,
            'profile' => $profileData
        ];
        
        $this->view('chat/v_chat', $data);
    }

    // API endpoint for sending a message
    public function send() {
        $chatId = $_POST['chat_id'] ?? 1;
        $userId = $_POST['user_id'] ?? 1;
        $message = $_POST['message'] ?? '';

        if (!empty($message)) {
            $this->chatModel->addMessage($chatId, $userId, $message);
            echo json_encode(["status" => "success"]);
        } else {
            echo json_encode(["status" => "error", "msg" => "Message empty"]);
        }
    }
    
    // API endpoint to get user profile
    public function getProfile($userId) {
        // Mock profile data for now
        $profileData = [
            'id' => $userId,
            'name' => 'User ' . $userId,
            'profile_image' => 'default.jpg',
            'bio' => 'Mock user profile',
            'role' => 'Undergraduate',
            'avatar' => 'U' . $userId
        ];
        
        header('Content-Type: application/json');
        echo json_encode($profileData);
    }
}
