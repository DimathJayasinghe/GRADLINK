<script>
    let messageInterval = null; // store the chat refresh interval
    let pausedByScroll = false; // track if polling paused due to user scrolling up
    let lastMessagePollTime = null;
    let messageBuffer = [];
    let messagePollingInterval = 1000;
    let initialMessageFetch = true;



    // Start new conversation
    async function startConversation(userId) {
        if (userId === null || userId === undefined) return;
        // Stop any old message refresh interval
        if (messageInterval) clearInterval(messageInterval);
        initialMessageFetch = true;

        const chatRoom = document.getElementById('chatRoom');
        chatRoom.innerHTML = loadingMessageView();

        try {
            // Mark conversation as read
            await fetch(`<?php echo URLROOT; ?>/messages/markAsRead`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    userId: userId
                })
            });

            const response = await fetch(`<?php echo URLROOT; ?>/messages/getConversation?userId=${userId}`);
            const data = await response.json();

            if (!data.success) {
                chatRoom.innerHTML = `<div class="chat_error">Failed to load conversation: ${data.error || 'Unknown error'}</div>`;
                return;
            }


            chatRoom.innerHTML = `
        <div class="message-section" id="conversationSection">
            <div class="message-section-header">
                <div class="conversation-partner-info">
                    <img src="${data.partner && data.partner.profile_picture 
                        ? `<?php echo URLROOT; ?>/media/profile/${data.partner.profile_picture}` 
                        : `<?php echo URLROOT; ?>/media/profile/default.jpg`}" 
                         alt="User" class="partner-avatar" id="partnerAvatar" onclick="window.location.href='<?php echo URLROOT; ?>/profile?userid=${userId}'" style="cursor: pointer;" title="View profile">
                    <div class="partner-details">
                        <h3 class="partner-name" onclick="window.location.href='<?php echo URLROOT; ?>/profile?userid=${userId}'" style="cursor: pointer;" title="View profile">${(data.partner && (data.partner.display_name || data.partner.name || data.partner.username)) || 'User'}</h3>
                    </div>
                </div>
            </div>

            <div class="conversation-content">
                <div class="chat-messages" id="chatMessages">
                    <!-- Messages will be loaded here -->
                </div>

                <button id="scrollToLatestBtn" class="scroll-to-latest-btn" style="display:none;" title="Jump to latest" onclick="scrollToLatest(${userId})">
                    <i class="fas fa-arrow-down"></i>
                </button>

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
            messageInterval = setInterval(() => loadMessages(userId), messagePollingInterval);

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

            // Setup scroll listener to pause/resume polling based on position
            setupChatScroll(userId);

        } catch (error) {
            console.error('Error starting conversation:', error);
            chatRoom.innerHTML = `<div class="chat_error">Failed to load conversation. Check console for details.</div>`;
        }
    }

    // Load chat messages periodically
    async function loadMessages(userId) {
        const chatMessages = document.getElementById('chatMessages');
        if (!chatMessages) return;
        try {
            // Determine if user was at bottom before re-render
            const wasAtBottom = isAtBottom(chatMessages);
            if (initialMessageFetch) {
                lastMessagePollTime = null;
                chatMessages.innerHTML = ''; // clear old messages
            }
            const response = await fetch(`<?php echo URLROOT; ?>/messages/getMessages`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    userId: userId,
                    since: lastMessagePollTime
                })
            });
            const data = await response.json();

            if (data.success) {
                if (data.lastPollTime) {
                    lastMessagePollTime = data.lastPollTime;
                }

                const messages = Array.isArray(data.messages) ? data.messages : [];
                if (messages.length > 0) {
                    const fragment = document.createDocumentFragment();
                    messages.forEach(message => {
                        const messageId = String(message.message_id ?? '');
                        if (messageId && chatMessages.querySelector(`.single-message-container[data-message-id="${messageId}"]`)) {
                            return;
                        }
                        const container = createMessageBubble(message, userId);
                        if (container) {
                            fragment.appendChild(container);
                        }
                    });

                    if (fragment.childNodes.length > 0) {
                        chatMessages.appendChild(fragment);
                        if (!pausedByScroll && wasAtBottom) {
                            chatMessages.scrollTop = chatMessages.scrollHeight;
                        }
                    }
                }

                if (initialMessageFetch) {
                    initialMessageFetch = false;
                }
            } else if (initialMessageFetch) {
                chatMessages.innerHTML = '<div class="error">Failed to load messages</div>';
            }

        } catch (error) {
            console.error('Error loading messages:', error);
            chatMessages.innerHTML = '<div class="error">Failed to load messages</div>';
        }
    }

    function createMessageBubble(message, userId) {
        const isSent = Number(message.sender_id) === Number(<?php echo (int)($_SESSION['user_id'] ?? 0); ?>);
        const container = document.createElement('div');
        container.className = 'single-message-container ' + (isSent ? 'sent' : 'received');
        if (message.message_id !== undefined && message.message_id !== null) {
            container.dataset.messageId = String(message.message_id);
        }

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
        if (isSent) {
            const actionsBtn = document.createElement('button');
            actionsBtn.className = 'msg-actions-btn';
            actionsBtn.innerHTML = '<i class="fas fa-ellipsis-v"></i>';
            actionsBtn.addEventListener('click', toggleMsgDropdown);

            const dropdown = document.createElement('div');
            dropdown.className = 'msg-dropdown';
            dropdown.style.display = 'none';

            const originalText = String(message.content || '');

            const editItem = document.createElement('div');
            editItem.className = 'msg-dropdown-item';
            editItem.innerHTML = '<i class="fas fa-edit"></i> Edit';
            editItem.addEventListener('click', (event) => {
                event.stopPropagation();
                editMessagePrompt(editItem, message.message_id || '', originalText);
            });

            const deleteItem = document.createElement('div');
            deleteItem.className = 'msg-dropdown-item danger';
            deleteItem.innerHTML = '<i class="fas fa-trash"></i> Delete';
            deleteItem.addEventListener('click', (event) => {
                event.stopPropagation();
                deleteMessageConfirm(message.message_id || '', userId);
            });

            dropdown.appendChild(editItem);
            dropdown.appendChild(deleteItem);
            actions.appendChild(actionsBtn);
            actions.appendChild(dropdown);
        }

        if (isSent) {
            container.appendChild(actions);
            container.appendChild(messageDiv);
        } else {
            container.appendChild(avatar);
            container.appendChild(messageDiv);
            if (actions.childNodes.length > 0) {
                container.appendChild(actions);
            }
        }

        return container;
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
                body: JSON.stringify({
                    recipientId: userId,
                    content
                })
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



    // Determine if the user is at (or near) the bottom of the chat
    function isAtBottom(el, threshold = 8) {
        if (!el) return true;
        return (el.scrollHeight - el.scrollTop - el.clientHeight) <= threshold;
    }

    // Setup scroll listener for pausing/resuming polling
    function setupChatScroll(userId) {
        const chatMessages = document.getElementById('chatMessages');
        const scrollBtn = document.getElementById('scrollToLatestBtn');
        if (!chatMessages) return;

        const onScroll = () => {
            const atBottom = isAtBottom(chatMessages);
            if (!atBottom) {
                // User scrolled up: pause polling and show button
                if (messageInterval) {
                    clearInterval(messageInterval);
                    messageInterval = null;
                }
                pausedByScroll = true;
                if (scrollBtn) scrollBtn.style.display = 'flex';
            } else {
                // Back at bottom: resume polling and hide button
                if (pausedByScroll && !messageInterval) {
                    messageInterval = setInterval(() => loadMessages(userId), 1000);
                }
                pausedByScroll = false;
                if (scrollBtn) scrollBtn.style.display = 'none';
            }
        };

        chatMessages.removeEventListener('scroll', chatMessages._onScrollHandler || (() => {}));
        chatMessages.addEventListener('scroll', onScroll);
        chatMessages._onScrollHandler = onScroll;
    }

    // Scroll to bottom and resume polling
    async function scrollToLatest(userId) {
        const chatMessages = document.getElementById('chatMessages');
        const scrollBtn = document.getElementById('scrollToLatestBtn');
        if (chatMessages) {
            chatMessages.scrollTo({
                top: chatMessages.scrollHeight,
                behavior: 'smooth'
            });
        }
        if (!messageInterval) {
            messageInterval = setInterval(() => loadMessages(userId), 1000);
        }
        pausedByScroll = false;
        if (scrollBtn) scrollBtn.style.display = 'none';
        // Optionally refresh once immediately
        await loadMessages(userId);
    }

    function deleteMessageConfirm(messageId, userId) {
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
    function escapeHtml(str) {
        return str.replace(/[&<>"']/g, function(m) {
            return ({
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                '\'': '&#039;'
            } [m]);
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
    function editMessagePrompt(triggerEl, messageId, currentText) {
        // Close dropdowns
        document.querySelectorAll('.msg-dropdown').forEach(dd => dd.style.display = 'none');

        // Pause refresh while editing
        if (messageInterval) clearInterval(messageInterval);

        // Find the correct message container from the clicked element
        const targetContainer = triggerEl.closest('.single-message-container');
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

        // Store a way to restore if cancel (on the specific element)
        msgDiv.dataset.original = originalHtml;
    }

    function loadingMessageView() {
        return `
        <div class="message-section" id="conversationSection">
            <div class="message-section-header">
                <div class="conversation-partner-info loading">
                    <div class="partner-avatar partner-avatar-skeleton" aria-hidden="true"></div>
                    <div class="partner-details">
                        <div class="partner-name partner-name-skeleton" aria-hidden="true"></div>
                        <div class="partner-meta partner-meta-skeleton" aria-hidden="true"></div>
                    </div>
                </div>
            </div>
            <div class="conversation-content">
                <div class="chat-messages loading" id="chatMessages">
                    <div class="chat-loading-state">
                        <div class="chat-loader" aria-hidden="true"></div>
                        <p class="chat-loading-text">Loading conversation…</p>
                    </div>
                </div>
                <div class="message-input-container">
                    <div class="input-wrapper">
                        <input type="text" placeholder="Type a message..." class="message-input" id="messageInput" disabled>
                    </div>
                    <button class="send-btn" disabled>
                        <i class="fas fa-paper-plane"></i>
                    </button>
                </div>
            </div>
        </div>
        <style>
            .conversation-partner-info.loading {
                align-items: center;
                gap: 12px;
            }

            .partner-avatar-skeleton {
                flex-shrink: 0;
                width: 40px;
                height: 40px;
                border-radius: 50%;
                background: linear-gradient(90deg, #1a1f27 0%, #252b35 50%, #1a1f27 100%);
                background-size: 200% 100%;
                animation: skeletonLoading 1.4s ease infinite;
            }

            .partner-name-skeleton,
            .partner-meta-skeleton {
                height: 12px;
                border-radius: 6px;
                background: linear-gradient(90deg, #1a1f27 0%, #252b35 50%, #1a1f27 100%);
                background-size: 200% 100%;
                animation: skeletonLoading 1.4s ease infinite;
            }

            .partner-name-skeleton {
                width: 150px;
                margin-bottom: 8px;
            }

            .partner-meta-skeleton {
                width: 110px;
            }

            .conversation-partner-info.loading .partner-details {
                min-height: 40px;
                justify-content: center;
                gap: 6px;
            }

            .chat-messages.loading {
                display: flex;
                align-items: center;
                justify-content: center;
                background: #111518;
                border: 1px solid #1c212a;
            }

            .chat-loading-state {
                display: flex;
                flex-direction: column;
                align-items: center;
                gap: 14px;
                color: #cfd5df;
                font-size: 14px;
            }

            .chat-loader {
                width: 32px;
                height: 32px;
                border-radius: 50%;
                border: 3px solid rgba(29, 161, 242, 0.16);
                border-top-color: #1da1f2;
                animation: spin 0.8s linear infinite;
            }

            .chat-loading-text {
                margin: 0;
                font-weight: 500;
                letter-spacing: 0.01em;
            }

            .message-input-container .message-input[disabled] {
                background: #0d1118;
                border: 1px solid #1c212a;
                color: #5a667a;
                cursor: not-allowed;
            }

            .message-input-container .send-btn[disabled] {
                background: #1c212a;
                color: #3f4b5e;
                cursor: not-allowed;
            }

            @keyframes spin {
                to { transform: rotate(360deg); }
            }

            @keyframes skeletonLoading {
                0% { background-position: 200% 0; }
                100% { background-position: -200% 0; }
            }
        </style>
    `;
    }


    async function submitEditMessage(messageId, inputId) {
        const input = document.getElementById(inputId);
        const newContent = (input?.value || '').trim();
        if (newContent === '') {
            alert('Message cannot be empty');
            return;
        }

        try {
            const res = await fetch(`<?php echo URLROOT; ?>/messages/editMessage`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    messageId,
                    content: newContent
                })
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

    function cancelEditMessage(messageId) {
        // Restore original content and resume interval for the edited message
        const edited = document.querySelector('.message[data-original]');
        if (edited) {
            edited.innerHTML = edited.dataset.original;
            delete edited.dataset.original;
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
</script>