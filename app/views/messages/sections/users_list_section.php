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

    const displayName = user.display_name || user.name || user.username || 'User';
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
                    <img src="${data.partner && data.partner.profile_picture 
                        ? `<?php echo URLROOT; ?>/media/profile/${data.partner.profile_picture}` 
                        : `<?php echo URLROOT; ?>/media/profile/default.jpg`}" 
                         alt="User" class="partner-avatar" id="partnerAvatar">
                    <div class="partner-details">
                        <h3 class="partner-name">${(data.partner && (data.partner.display_name || data.partner.name || data.partner.username)) || 'User'}</h3>
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

        // allow Enter to send
        const inputEl = document.getElementById('messageInput');
        if (inputEl) {
            inputEl.addEventListener('keydown', (e) => {
                if (e.key === 'Enter' && !e.shiftKey) {
                    e.preventDefault();
                    sendMessage(userId);
                }
            });
        }

    } catch (error) {
        console.error('Error starting conversation:', error);
        chatRoom.innerHTML = `<div class="chat_error">Failed to load conversation. Check console for details.</div>`;
    }
}

async function sendMessage(userId) {
    if (messageInterval) clearInterval(messageInterval);
    const messageInput = document.getElementById('messageInput');
    const content = messageInput.value.trim();

    if (content === '') return; // don't send empty messages

    try {
        const response = await fetch(`<?php echo URLROOT; ?>/messages/sendMessage`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ recipientId: userId, content })
        });
        const data = await response.json();

        if (data.success) {
            messageInput.value = ''; // clear input
            // Immediately refresh messages and resume periodic loading
            await loadMessages(userId);
            messageInterval = setInterval(() => loadMessages(userId), 1000);
        } else {
            alert('Failed to send message: ' + data.error);
            // restart periodic loading to continue updates
            messageInterval = setInterval(() => loadMessages(userId), 1000);
        }
    } catch (error) {
        console.error('Error sending message:', error);
        alert('Error sending message. Check console for details.');
        // restart periodic loading to continue updates
        messageInterval = setInterval(() => loadMessages(userId), 1000);
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
                const isSent = Number(message.sender_id) === Number(<?php echo (int)($_SESSION['user_id'] ?? 0); ?>);
                const container = document.createElement('div');
                container.className = 'single-message-container ' + (isSent ? 'sent' : 'received');

                const avatar = document.createElement('div');
                avatar.className = 'message-avatar';
                avatar.innerHTML = `<img src="<?php echo URLROOT; ?>/media/profile/${message.sender_picture || 'default.jpg'}" alt="User" class="avatar-small">`;

                const messageDiv = document.createElement('div');
                messageDiv.className = 'message ' + (isSent ? 'sent' : 'received');
                const safeText = escapeHtml(String(message.content || ''));
                messageDiv.innerHTML = `
                    <p class="message-text">${safeText}</p>
                    <span class="message-time">${message.timestamp || ''}</span>
                `;

                const actions = document.createElement('div');
                actions.className = 'msg-actions';
                actions.innerHTML = `
                    <button class="msg-actions-btn" onclick="toggleMsgDropdown(event)">
                        <i class="fas fa-ellipsis-v"></i>
                    </button>
                    <div class="msg-dropdown" style="display:none;">
                        ${isSent ? `<div class=\"msg-dropdown-item\" onclick=\"event.stopPropagation(); editMessagePrompt('${message.message_id || ''}', '${safeText.replace(/'/g, "\\'")}')\"><i class=\"fas fa-edit\"></i> Edit</div>` : ''}
                        <div class=\"msg-dropdown-item danger\" onclick=\"event.stopPropagation(); deleteMessageConfirm('${message.message_id || ''}', ${userId})\"><i class=\"fas fa-trash\"></i> Delete</div>
                    </div>
                `;

                if (isSent) {
                    // Sent: actions > message (right side), no avatar
                    container.appendChild(actions);
                    container.appendChild(messageDiv);
                } else {
                    // Received: avatar > message > actions
                    container.appendChild(avatar);
                    container.appendChild(messageDiv);
                    container.appendChild(actions);
                }

                chatMessages.appendChild(container);
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
    
    // Close dropdown
    document.querySelectorAll('.msg-dropdown').forEach(dd => {
        dd.style.display = 'none';
    });
    
    if (confirm('Are you sure you want to delete this message?')) {
        fetch(`<?php echo URLROOT; ?>/messages/deleteMessage?messageId=${messageId}`)
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    loadMessages(userId);
                    messageInterval = setInterval(() => loadMessages(userId), 1000);
                } else {
                    alert('Failed to delete message: ' + (data.error || 'Unknown error'));
                    messageInterval = setInterval(() => loadMessages(userId), 1000);
                }
            })
            .catch(error => {
                console.error('Error deleting message:', error);
                alert('Error deleting message. Check console for details.');
                messageInterval = setInterval(() => loadMessages(userId), 1000);
            });
    } else {
        // restart the message refresh interval if user cancelled
        messageInterval = setInterval(() => loadMessages(userId), 1000);
    }
}

// simple HTML escape to prevent XSS in message content
function escapeHtml(str){
    return str.replace(/[&<>"']/g, function(m){
        return ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;','\'':'&#039;'}[m]);
    });
}

// Toggle message dropdown menu
function toggleMsgDropdown(event) {
    if (messageInterval) clearInterval(messageInterval);
    const button = event.currentTarget;
    const dropdown = button.nextElementSibling;
    
    // Close all other dropdowns first
    document.querySelectorAll('.msg-dropdown').forEach(dd => {
        if (dd !== dropdown) {
            dd.style.display = 'none';
        }
    });
    
    // Toggle current dropdown
    if (dropdown.style.display === 'none' || dropdown.style.display === '') {
        dropdown.style.display = 'block';
    } else {
        dropdown.style.display = 'none';
    }
}

// Close dropdowns when clicking outside
document.addEventListener('click', function(event) {
    if (!event.target.closest('.msg-actions')) {
        document.querySelectorAll('.msg-dropdown').forEach(dd => {
            dd.style.display = 'none';
        });
    }
});

// Edit message prompt (placeholder function)
function editMessagePrompt(messageId, currentText) {
    // Close dropdowns
    document.querySelectorAll('.msg-dropdown').forEach(dd => dd.style.display = 'none');

    // Pause refresh while editing
    if (messageInterval) clearInterval(messageInterval);

    // Find the message container by traversing from the clicked dropdown
    // We'll look for the nearest .single-message-container preceding the open dropdown
    // Simpler approach: find all message containers and replace the first one whose dropdown was just closed
    const containers = document.querySelectorAll('.single-message-container');
    let targetContainer = null;
    for (const c of containers) {
        // heuristic: the container having a msg-actions with a dropdown hidden most recently
        const dd = c.querySelector('.msg-dropdown');
        if (dd) { targetContainer = c; }
    }
    // Fallback: use last message container
    if (!targetContainer && containers.length) targetContainer = containers[containers.length - 1];

    if (!targetContainer) return;

    const msgDiv = targetContainer.querySelector('.message');
    if (!msgDiv) return;

    const originalHtml = msgDiv.innerHTML;

    // Build inline editor UI
    const inputId = `edit_${messageId}`;
    msgDiv.innerHTML = `
        <div class="edit-message-wrapper">
            <input id="${inputId}" type="text" class="message-input" value="${currentText || ''}" />
            <div class="edit-actions" style="margin-top:6px; display:flex; gap:8px;">
                <button class="send-btn" title="Save" onclick="submitEditMessage('${messageId}', '${inputId}')"><i class=\"fas fa-check\"></i></button>
                <button class="send-btn" title="Cancel" onclick="cancelEditMessage('${messageId}')"><i class=\"fas fa-times\"></i></button>
            </div>
        </div>
    `;

    // Store a way to restore if cancel
    msgDiv.dataset.original = originalHtml;
}

async function submitEditMessage(messageId, inputId){
    const input = document.getElementById(inputId);
    const newContent = (input?.value || '').trim();
    if (newContent === '') { alert('Message cannot be empty'); return; }

    try {
        const res = await fetch(`<?php echo URLROOT; ?>/messages/editMessage`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ messageId, content: newContent })
        });
        const data = await res.json();
        if (!data.success) {
            alert('Failed to update message: ' + (data.error || 'Unknown error'));
        }
    } catch (e) {
        console.error('Error updating message', e);
        alert('Error updating message. Check console for details.');
    } finally {
        // Resume interval by reloading the current conversation
        const openHeader = document.querySelector('.partner-name');
        const userIdAttr = document.querySelector('.send-btn[onclick^="sendMessage("]')?.getAttribute('onclick');
        const match = userIdAttr && userIdAttr.match(/sendMessage\((\d+)\)/);
        const userId = match ? Number(match[1]) : null;
        if (userId) {
            await loadMessages(userId);
            messageInterval = setInterval(() => loadMessages(userId), 1000);
        }
    }
}

function cancelEditMessage(messageId){
    // Restore original content and resume interval
    const containers = document.querySelectorAll('.single-message-container');
    for (const c of containers) {
        const msgDiv = c.querySelector('.message');
        if (msgDiv && msgDiv.dataset.original) {
            msgDiv.innerHTML = msgDiv.dataset.original;
            delete msgDiv.dataset.original;
            break;
        }
    }

    const userIdAttr = document.querySelector('.send-btn[onclick^="sendMessage("]')?.getAttribute('onclick');
    const match = userIdAttr && userIdAttr.match(/sendMessage\((\d+)\)/);
    const userId = match ? Number(match[1]) : null;
    if (userId) {
        messageInterval = setInterval(() => loadMessages(userId), 1000);
    }
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
