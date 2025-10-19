<!-- Conversation Section -->
<div class="message-section">
    <div class="message-section-header">
        <div class="section-title-container">
            <i class="fas fa-comments section-icon"></i>
            <h3>Conversation</h3>
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
            <!-- Sample messages -->
            <div class="message-group received">
                <div class="message-avatar">
                    <img src="<?php echo URLROOT; ?>/media/profile/default.jpg" alt="User" class="avatar-small">
                </div>
                <div class="messages-container">
                    <div class="message-bubble">
                        Hey! How are you doing?
                    </div>
                    <div class="message-bubble">
                        Are you free for the meeting today?
                    </div>
                    <div class="message-time">2:30 PM</div>
                </div>
            </div>
            
            <div class="message-group sent">
                <div class="messages-container">
                    <div class="message-bubble">
                        I'm doing great, thanks!
                    </div>
                    <div class="message-bubble">
                        Yes, I'll be there at 3 PM
                    </div>
                    <div class="message-time">2:32 PM</div>
                </div>
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

<style>
.section-title-container {
    display: flex;
    align-items: center;
    gap: 12px;
}

.section-icon {
    font-size: 18px;
    color: var(--primary, #9ed4dc);
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
function sendMessage() {
    const input = document.getElementById('messageInput');
    const message = input.value.trim();
    
    if (message) {
        // Add message to chat (this would normally send to server)
        addMessageToChat(message, true);
        input.value = '';
        
        // Scroll to bottom
        const chatMessages = document.getElementById('chatMessages');
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }
}

function addMessageToChat(message, isSent) {
    const chatMessages = document.getElementById('chatMessages');
    const messageGroup = document.createElement('div');
    messageGroup.className = `message-group ${isSent ? 'sent' : 'received'}`;
    
    const currentTime = new Date().toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
    
    messageGroup.innerHTML = `
        ${!isSent ? '<div class="message-avatar"><img src="<?php echo URLROOT; ?>/media/profile/default.jpg" alt="User" class="avatar-small"></div>' : ''}
        <div class="messages-container">
            <div class="message-bubble">${message}</div>
            <div class="message-time">${currentTime}</div>
        </div>
    `;
    
    chatMessages.appendChild(messageGroup);
}

// Send message on Enter key
document.getElementById('messageInput').addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        sendMessage();
    }
});
</script>