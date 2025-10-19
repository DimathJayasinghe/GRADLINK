<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/components/messages_categories.css">

<!-- Horizontal Messages Navigation -->
<div class="messages-nav">
    <div class="messages-nav-container">
        <?php $center_topic = "Messages"?>
        
        <!-- Search Messages -->
        <div class="messages-search">
            <div class="search-input-container">
                <i class="fas fa-search search-icon"></i>
                <input type="text" placeholder="Search messages..." class="search-input">
            </div>
        </div>
        
        <!-- Horizontal Category Icons -->
        <!-- <div class="messages-categories-row">
            <?php foreach($message_categories as $category): ?>
            <a href="<?php echo $category['link'] ?>" class="category-icon-btn <?php if($category['active']){echo "active";}?>" 
               data-category="<?php echo $category['label'] ?>">
                <i class="fas fa-<?php echo isset($category['icon']) ? $category['icon'] : 'envelope'; ?>"></i>
            </a>
            <?php endforeach; ?>
        </div> -->
        
        <!-- Conversations List -->
        <div class="conversations-section">
            <div class="conversations-header">
                <h3>Conversations</h3>
                <button class="start-conversation-btn" onclick="openNewConversationModal()">
                    <i class="fas fa-plus"></i>
                </button>
            </div>
            
            <!-- Conversations List -->
            <div class="conversations-list">
                <?php
                // Get real conversations from database
                try {
                    $messageModel = new M_message();
                    $conversations = $messageModel->getUserConversations($_SESSION['user_id']);
                } catch (Exception $e) {
                    $conversations = [];
                }
                
                // If no conversations, use sample data for demo
                if (empty($conversations)) {
                    $conversations = [
                        (object)[
                            'conversation_id' => 1,
                            'other_full_name' => 'Alice Johnson',
                            'other_avatar' => 'alice.jpg',
                            'last_message' => 'Hey! Are you coming to the meeting?',
                            'last_message_time' => '2024-01-15 14:30:00',
                            'unread_count' => 2
                        ],
                        (object)[
                            'conversation_id' => 2,
                            'other_full_name' => 'Bob Smith',
                            'other_avatar' => 'bob.jpg',
                            'last_message' => 'Thanks for the help yesterday',
                            'last_message_time' => '2024-01-15 13:15:00',
                            'unread_count' => 0
                        ],
                        (object)[
                            'conversation_id' => 3,
                            'other_full_name' => 'Sarah Williams',
                            'other_avatar' => 'sarah.jpg',
                            'last_message' => 'Can you share the project files?',
                            'last_message_time' => '2024-01-15 12:45:00',
                            'unread_count' => 1
                        ]
                    ];
                }
                
                foreach($conversations as $conversation):
                    $userName = isset($conversation->other_full_name) ? $conversation->other_full_name : $conversation['user_name'];
                    $nameParts = explode(' ', trim($userName));
                    if (strlen($userName) > 15 && count($nameParts) > 1) {
                        $displayName = $nameParts[0] . ' ' . strtoupper(substr($nameParts[count($nameParts)-1], 0, 1)) . '.';
                    } else {
                        $displayName = $userName;
                    }
                    
                    // Format time
                    $lastTime = isset($conversation->last_message_time) 
                        ? date('g:i A', strtotime($conversation->last_message_time)) 
                        : (isset($conversation['last_time']) ? $conversation['last_time'] : '');
                    
                    $conversationId = isset($conversation->conversation_id) ? $conversation->conversation_id : $conversation['id'];
                    $lastMessage = isset($conversation->last_message) ? $conversation->last_message : $conversation['last_message'];
                    $unreadCount = isset($conversation->unread_count) ? $conversation->unread_count : $conversation['unread_count'];
                    $userAvatar = isset($conversation->other_avatar) ? $conversation->other_avatar : $conversation['user_avatar'];
                ?>
                <div class="conversation-item" onclick="openConversation(<?php echo $conversationId; ?>)">
                    <div class="user-avatar">
                        <img src="<?php echo URLROOT; ?>/media/profile/<?php echo $userAvatar; ?>" 
                             alt="<?php echo $userName; ?>" class="avatar-img" 
                             onerror="this.src='<?php echo URLROOT; ?>/img/default-avatar.png'">
                    </div>
                    <div class="user-info">
                        <h4 class="user-name"><?php echo $displayName; ?></h4>
                        <p class="last-message"><?php echo $lastMessage; ?></p>
                    </div>
                    <div class="message-meta">
                        <span class="time-text"><?php echo $lastTime; ?></span>
                        <?php if($unreadCount > 0): ?>
                        <div class="unread-badge"><?php echo $unreadCount; ?></div>
                        <?php endif; ?>
                        <div class="conversation-options">
                            <button class="options-btn" onclick="event.stopPropagation(); toggleConversationDropdown(<?php echo $conversationId; ?>)">
                                <i class="fas fa-ellipsis-v"></i>
                            </button>
                            <div class="conversation-dropdown" id="dropdown-<?php echo $conversationId; ?>" style="display: none;">
                                <div class="dropdown-item" onclick="event.stopPropagation(); reportConversation(<?php echo $conversationId; ?>, '<?php echo htmlspecialchars($userName); ?>')">
                                    <i class="fas fa-flag"></i>
                                    <span>Report conversation</span>
                                </div>
                                <div class="dropdown-item danger" onclick="event.stopPropagation(); deleteConversation(<?php echo $conversationId; ?>, '<?php echo htmlspecialchars($userName); ?>')">
                                    <i class="fas fa-trash"></i>
                                    <span>Delete conversation</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<!-- New Conversation Modal -->
<div id="newConversationModal" class="modal" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Start New Conversation</h3>
            <button class="close-btn" onclick="closeNewConversationModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="modal-body">
            <div class="search-users">
                <input type="text" placeholder="Search users..." class="user-search-input" oninput="searchUsers(this.value)">
            </div>
            <div class="users-list" id="usersList">
                <!-- Users will be loaded here -->
            </div>
        </div>
    </div>
</div>