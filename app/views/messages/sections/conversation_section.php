<!-- Conversation Section -->
<div class="message-section" id="conversationSection" style="display: none;">
    <div class="message-section-header">
        <div class="section-title-container">
            <button class="back-btn" onclick="backToMessages()" title="Back to conversations">
                <i class="fas fa-arrow-left"></i>
            </button>
            <div class="conversation-partner-info">
                <img src="<?php echo URLROOT; ?>/public/img/default-profile.png" alt="User" class="partner-avatar" id="partnerAvatar">
                <div class="partner-details">
                    <h3 id="partnerName">Loading...</h3>
                    <span class="partner-status" id="partnerStatus">Online</span>
                </div>
            </div>
        </div>
        <div class="conversation-actions">
            <button class="message-options-btn" title="Call">
                <i class="fas fa-phone"></i>
            </button>
            <button class="message-options-btn" title="Video Call">
                <i class="fas fa-video"></i>
            </button>
            <button class="message-options-btn" title="More Options">
                <i class="fas fa-ellipsis-h"></i>
            </button>
        </div>
    </div>
    
    <div class="conversation-content">
        <!-- Chat Messages Area -->
        <div class="chat-messages" id="chatMessages">
            <!-- Messages will be loaded dynamically -->
            <div class="loading-messages" id="loadingMessages">
                <div class="spinner"></div>
                <span>Loading conversation...</span>
            </div>
        </div>
        
        <!-- Message Input Area -->
        <div class="message-input-container">
            <button class="attach-btn" title="Attach File">
                <i class="fas fa-paperclip"></i>
            </button>
            <div class="input-wrapper">
                <input type="text" placeholder="Type a message..." class="message-input" id="messageInput">
                <button class="emoji-btn" title="Add Emoji">
                    <i class="fas fa-smile"></i>
                </button>
            </div>
            <button class="send-btn" onclick="sendMessage()">
                <i class="fas fa-paper-plane"></i>
            </button>
        </div>
    </div>
</div>

<!-- Global conversation variables -->
<script>
let currentConversationId = null;
let currentPartnerId = null;
</script>

<style>
.section-title-container {
    display: flex;
    align-items: center;
    gap: 12px;
    flex: 1;
}

.section-icon {
    font-size: 18px;
    color: var(--primary, #9ed4dc);
}

.back-btn {
    width: 36px;
    height: 36px;
    border: none;
    border-radius: 50%;
    background-color: var(--surface-2, #1b2126);
    color: var(--muted, #cbb8a3);
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.2s ease;
    font-size: 16px;
}

.back-btn:hover {
    background-color: var(--surface-1, #151b1f);
    color: var(--text, #e8eef2);
}

.conversation-partner-info {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-left: 8px;
}

.partner-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    object-fit: cover;
}

.partner-details h3 {
    margin: 0;
    font-size: 16px;
    font-weight: 600;
    color: var(--text, #e8eef2);
}

.partner-status {
    font-size: 12px;
    color: var(--success, #4ade80);
}

.loading-messages {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    height: 200px;
    gap: 16px;
    color: var(--muted, #cbb8a3);
}

.spinner {
    width: 32px;
    height: 32px;
    border: 3px solid var(--surface-2, #1b2126);
    border-top: 3px solid var(--primary, #9ed4dc);
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

.conversation-actions {
    display: flex;
    gap: 8px;
}

.conversation-content {
    flex: 1;
    display: flex;
    flex-direction: column;
    height: calc(100vh - 100px);
}

.chat-messages {
    flex: 1;
    padding: 20px;
    overflow-y: auto;
    display: flex;
    flex-direction: column;
    gap: 16px;
}

.message-group {
    display: flex;
    gap: 12px;
    max-width: 70%;
}

.message-group.sent {
    align-self: flex-end;
    flex-direction: row-reverse;
}

.message-avatar {
    flex-shrink: 0;
}

.avatar-small {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    object-fit: cover;
}

.messages-container {
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.message-bubble {
    padding: 8px 12px;
    border-radius: 12px;
    font-size: 14px;
    line-height: 1.4;
    word-wrap: break-word;
}

.message-group.received .message-bubble {
    background-color: var(--surface-2, #1b2126);
    color: var(--text, #e8eef2);
    border-bottom-left-radius: 4px;
}

.message-group.sent .message-bubble {
    background-color: var(--primary, #9ed4dc);
    color: var(--bg, #0f1518);
    border-bottom-right-radius: 4px;
}

.message-time {
    font-size: 11px;
    color: var(--muted, #cbb8a3);
    text-align: center;
    margin-top: 4px;
}

.message-input-container {
    padding: 16px 20px;
    border-top: 1px solid var(--border, #3a3a3a);
    display: flex;
    align-items: center;
    gap: 12px;
    background-color: var(--bg, #0f1518);
}

.attach-btn, .send-btn, .emoji-btn {
    width: 36px;
    height: 36px;
    border: none;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.2s ease;
    font-size: 16px;
}

.attach-btn, .emoji-btn {
    background-color: var(--surface-2, #1b2126);
    color: var(--muted, #cbb8a3);
}

.attach-btn:hover, .emoji-btn:hover {
    background-color: var(--surface-1, #151b1f);
    color: var(--text, #e8eef2);
}

.send-btn {
    background-color: var(--primary, #9ed4dc);
    color: var(--bg, #0f1518);
}

.send-btn:hover {
    background-color: var(--primary-600, #8fcbd4);
    transform: scale(1.05);
}

.input-wrapper {
    flex: 1;
    position: relative;
    display: flex;
    align-items: center;
}

.message-input {
    width: 100%;
    padding: 10px 40px 10px 16px;
    background-color: var(--surface-2, #1b2126);
    border: 1px solid var(--border, #3a3a3a);
    border-radius: 20px;
    color: var(--text, #e8eef2);
    font-size: 14px;
    outline: none;
    resize: none;
}

.message-input::placeholder {
    color: var(--muted, #cbb8a3);
}

.message-input:focus {
    border-color: var(--primary, #9ed4dc);
}

.emoji-btn {
    position: absolute;
    right: 4px;
    width: 28px;
    height: 28px;
    font-size: 14px;
}

/* No messages state */
.no-messages, .error-message {
    display: flex;
    align-items: center;
    justify-content: center;
    height: 200px;
    color: var(--muted, #cbb8a3);
    text-align: center;
}

.error-message p {
    color: var(--error, #ef4444);
}

/* Scrollbar for chat messages */
.chat-messages::-webkit-scrollbar {
    width: 6px;
}

.chat-messages::-webkit-scrollbar-track {
    background: transparent;
}

.chat-messages::-webkit-scrollbar-thumb {
    background: var(--border, #3a3a3a);
    border-radius: 3px;
}

.chat-messages::-webkit-scrollbar-thumb:hover {
    background: var(--muted, #cbb8a3);
}
</style>

<script>
// Function to open conversation with a specific user
function openConversation(userId, userName) {
    currentPartnerId = userId;
    
    // Update partner info
    document.getElementById('partnerName').textContent = userName;
    document.getElementById('partnerAvatar').src = '<?php echo URLROOT; ?>/public/img/default-profile.png';
    
    // Show conversation section and hide messages list
    document.getElementById('conversationSection').style.display = 'flex';
    document.getElementById('messagesSection').style.display = 'none';
    
    // Load conversation
    loadConversation(userId);
}

// Function to go back to messages list
function backToMessages() {
    document.getElementById('conversationSection').style.display = 'none';
    document.getElementById('messagesSection').style.display = 'block';
    currentConversationId = null;
    currentPartnerId = null;
}

// Function to load conversation messages
function loadConversation(partnerId) {
    const chatMessages = document.getElementById('chatMessages');
    const loadingMessages = document.getElementById('loadingMessages');
    
    // Show loading
    loadingMessages.style.display = 'flex';
    chatMessages.innerHTML = '';
    chatMessages.appendChild(loadingMessages);
    
    fetch('<?php echo URLROOT; ?>/messages/getConversation', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            partner_id: partnerId
        })
    })
    .then(response => response.json())
    .then(data => {
        loadingMessages.style.display = 'none';
        
        if (data.success) {
            currentConversationId = data.conversation_id;
            displayMessages(data.messages);
        } else {
            // No existing conversation, ready to start new one
            currentConversationId = null;
            chatMessages.innerHTML = '<div class="no-messages"><p>No messages yet. Start the conversation!</p></div>';
        }
    })
    .catch(error => {
        console.error('Error loading conversation:', error);
        loadingMessages.style.display = 'none';
        chatMessages.innerHTML = '<div class="error-message"><p>Failed to load conversation. Please try again.</p></div>';
    });
}

// Function to display messages
function displayMessages(messages) {
    const chatMessages = document.getElementById('chatMessages');
    chatMessages.innerHTML = '';
    
    if (messages.length === 0) {
        chatMessages.innerHTML = '<div class="no-messages"><p>No messages yet. Start the conversation!</p></div>';
        return;
    }
    
    messages.forEach(message => {
        addMessageToChat(message.content, message.sender_id == <?php echo $_SESSION['user_id'] ?? 'null'; ?>, message.created_at);
    });
    
    // Scroll to bottom
    chatMessages.scrollTop = chatMessages.scrollHeight;
}

// Function to send message
function sendMessage() {
    const input = document.getElementById('messageInput');
    const message = input.value.trim();
    
    if (!message) return;
    
    if (!currentPartnerId) {
        alert('No conversation partner selected');
        return;
    }
    
    // Add message to UI immediately
    addMessageToChat(message, true);
    input.value = '';
    
    // Send to server
    fetch('<?php echo URLROOT; ?>/messages/sendMessage', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            receiver_id: currentPartnerId,
            content: message,
            conversation_id: currentConversationId
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update conversation ID if it was a new conversation
            if (!currentConversationId) {
                currentConversationId = data.conversation_id;
            }
        } else {
            alert('Failed to send message: ' + (data.message || 'Unknown error'));
            // Optionally remove the message from UI or mark it as failed
        }
    })
    .catch(error => {
        console.error('Error sending message:', error);
        alert('Failed to send message. Please try again.');
    });
    
    // Scroll to bottom
    const chatMessages = document.getElementById('chatMessages');
    chatMessages.scrollTop = chatMessages.scrollHeight;
}

// Function to add message to chat UI
function addMessageToChat(message, isSent, timestamp = null) {
    const chatMessages = document.getElementById('chatMessages');
    const messageGroup = document.createElement('div');
    messageGroup.className = `message-group ${isSent ? 'sent' : 'received'}`;
    
    let timeStr;
    if (timestamp) {
        const messageDate = new Date(timestamp);
        timeStr = messageDate.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
    } else {
        timeStr = new Date().toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
    }
    
    messageGroup.innerHTML = `
        ${!isSent ? '<div class="message-avatar"><img src="<?php echo URLROOT; ?>/public/img/default-profile.png" alt="User" class="avatar-small"></div>' : ''}
        <div class="messages-container">
            <div class="message-bubble">${message}</div>
            <div class="message-time">${timeStr}</div>
        </div>
    `;
    
    chatMessages.appendChild(messageGroup);
}

// Send message on Enter key
document.addEventListener('DOMContentLoaded', function() {
    const messageInput = document.getElementById('messageInput');
    if (messageInput) {
        messageInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                sendMessage();
            }
        });
    }
});
</script>