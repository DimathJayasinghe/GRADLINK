<script>
let messageInterval = null; // store the chat refresh interval

// Load available users asynchronously
async function loadAvailableUsers() {
    const usersList = document.getElementById('usersList');
    usersList.innerHTML = '<div class="loading_users">Loading users...</div>';

    try {
        const response = await fetch('<?php echo URLROOT; ?>/messages/getAvailableUsers');
        const data = await response.json();

        usersList.innerHTML = '';  

        if (data.success && data.users && data.users.length > 0) {
            data.users.forEach(user => {
                const userItem = createUserItem(user);
                usersList.appendChild(userItem);
            });
        } else {
            usersList.innerHTML = '<div class="no-users">No available users found.</div>';
        }
    } catch (error) {
        console.error('Error loading users:', error);
        usersList.innerHTML = `
            <div class="error">
                Failed to load users from database.<br>
                Error: ${error.message}<br>
                Check console for details.
            </div>
        `;
    }
}

// Create user item element
function createUserItem(user) {
    const div = document.createElement('div');
    div.className = 'conversation-item';
    div.onclick = () => startConversation(user.user_id);

    const displayName = user.full_name || user.username;
    const avatarSrc = user.profile_picture 
        ? `<?php echo URLROOT; ?>/media/profile/${user.profile_picture}`
        : `<?php echo URLROOT; ?>/media/profile/default.jpg`;

    div.innerHTML = `
        <div class="user-avatar">
            <img src="${avatarSrc}" 
                 alt="${displayName}" class="avatar-img"
                 onerror="this.src='<?php echo URLROOT; ?>/media/profile/default.jpg'">
        </div>
        <div class="user-info">
            <h4 class="user-name">${displayName}</h4>
        </div>
    `;
    
    return div;
}

// Start new conversation
async function startConversation(userId) {
    // Stop any old message refresh interval
    if (messageInterval) clearInterval(messageInterval);

    const chatRoom = document.getElementById('chatRoom');
    chatRoom.innerHTML = `<div class="loading">Conversation Loading...</div>`;

    try {
        const response = await fetch(`<?php echo URLROOT; ?>/messages/getConversation?userId=${userId}`);
        const data = await response.json();

        chatRoom.innerHTML = `
        <div class="message-section" id="conversationSection">
            <div class="message-section-header">
                <div class="conversation-partner-info">
                    <img src="${data.partner.profile_picture 
                        ? `<?php echo URLROOT; ?>/media/profile/${data.partner.profile_picture}` 
                        : `<?php echo URLROOT; ?>/media/profile/default.jpg`}" 
                         alt="User" class="partner-avatar" id="partnerAvatar">
                    <div class="partner-details">
                        <h3 class="partner-name">${data.partner.full_name || data.partner.username}</h3>
                    </div>
                </div>
            </div>

            <div class="conversation-content">
                <div class="chat-messages" id="chatMessages">
                    <!-- Messages will be loaded here -->
                </div>

                <div class="message-input-container">
                    <div class="input-wrapper">
                        <input type="text" placeholder="Type a message..." class="message-input" id="messageInput">
                    </div>
                    <button class="send-btn" onclick="sendMessage(${userId})">
                        <i class="fas fa-paper-plane"></i>
                    </button>
                </div>
            </div>
        </div>
        `;

        // Load messages immediately and then every second
        await loadMessages(userId);
        messageInterval = setInterval(() => loadMessages(userId), 1000);

    } catch (error) {
        console.error('Error starting conversation:', error);
        chatRoom.innerHTML = `<div class="chat_error">Failed to load conversation. Check console for details.</div>`;
    }
}

// Load chat messages periodically
async function loadMessages(userId) {
    const chatMessages = document.getElementById('chatMessages');
    try {
        const response = await fetch(`<?php echo URLROOT; ?>/messages/getMessages?userId=${userId}`);
        const data = await response.json();

        if (data.messages) {
            chatMessages.innerHTML = ''; // clear old
            data.messages.forEach(message => {
                const isSent = message.sender_id === <?php echo $_SESSION['user_id']; ?>;
                const singleMessage = document.createElement('div');
                singleMessage.className = 'single-message-container';
                if(!isSent){
                    singleMessage.appendChild(
                        '<div class="message-avatar"><img src="<?php echo URLROOT; ?>/media/profile/default-profile.png" alt="User" class="avatar-small"></div>'
                    );
                }
                const messageDiv = document.createElement('div');
                messageDiv.className = isSent? 'message sent' : 'message received';
                messageDiv.innerHTML = `
                    <p class="message-text">${message.content}</p>
                    <span class="message-time">${message.timestamp}</span>
                    
                `;
                singleMessage.appendChild(messageDiv);
                singleMessage.appendChild(
                    `<div class="msg-actions">
                        <button class="msg-actions-btn" value="${message.id || ''}" onclick = "toggleMsgDropdown(event)">
                            <i class="fas fa-ellipsis-v"></i>
                        </button>
                        <div class="msg-dropdown" style="display:none;">
                            ${isSent ? `<div class="msg-dropdown-item" onclick="event.stopPropagation(); editMessagePrompt('${message.Id}')\"><i class=\"fas fa-edit\"></i> Edit</div>` : ''}
                            <div class="msg-dropdown-item danger" onclick=deleteMessageConfirm('${message.Id,userId}')\"><i class=\"fas fa-trash\"></i> Delete</div>
                        </div>
                    </div>`
                );
                chatMessages.appendChild(singleMessage);
            });

            // auto-scroll to bottom
            chatMessages.scrollTop = chatMessages.scrollHeight;
        }

    } catch (error) {
        console.error('Error loading messages:', error);
        chatMessages.innerHTML = '<div class="error">Failed to load messages</div>';
    }
}

function deleteMessageConfirm(messageId,userId) {
    if (messageInterval) clearInterval(messageInterval);
    if (confirm('Are you sure you want to delete this message?')) {
        fetch(`<?php echo URLROOT; ?>/messages/deleteMessages?userId=${messageId}`);
    }
    // start the interval again
    else {
        // restart the message refresh interval
        messageInterval = setInterval(() => loadMessages(userId), 1000);
    }
}


function confirm($alert){
    return window.confirm(String($alert));
}

// Search users
async function searchUsers(query) {
    const usersList = document.getElementById('usersList');
    
    if (query.length < 2) {
        loadAvailableUsers();
        return;
    }

    usersList.innerHTML = '<div class="loading">Searching...</div>';

    try {
        const response = await fetch(`<?php echo URLROOT; ?>/messages/getAvailableUsers?search=${encodeURIComponent(query)}`);
        const data = await response.json();

        usersList.innerHTML = '';

        if (data.success && data.users.length > 0) {
            data.users.forEach(user => {
                const userItem = createUserItem(user);
                usersList.appendChild(userItem);
            });
        } else {
            usersList.innerHTML = '<div class="no-users">No users found matching your search</div>';
        }
    } catch (error) {
        console.error('Error searching users:', error);
        usersList.innerHTML = '<div class="error">Failed to search users</div>';
    }
}

// Auto-load available users and refresh periodically
document.addEventListener('DOMContentLoaded', () => {
    loadAvailableUsers();
    setInterval(loadAvailableUsers, 15000);
});
</script>

<!-- Users List Section -->
<div id="usersList" class="usersList"></div>
