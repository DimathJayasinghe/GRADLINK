<script>
    class ChatWindow {
        constructor(options) {
            this.apiRoot = options.apiRoot;
            this.baseUrl = options.baseUrl;
            this.currentUserId = options.currentUserId;
            this.pollInterval = options.pollInterval || 1000;

            this.messageInterval = null;
            this.lastMessagePollTime = null;
            this.initialFetch = true;
            this.pendingDeleteId = null;
            this.activeUserId = null;
            this.dom = {};

            this.chatRoom = document.getElementById('chatRoom');

            this.toggleMsgDropdown = this.toggleMsgDropdown.bind(this);
            this.handleEscape = this.handleEscape.bind(this);
            this.handleOutsideActionClick = this.handleOutsideActionClick.bind(this);

            document.addEventListener('keydown', this.handleEscape);
            document.addEventListener('click', this.handleOutsideActionClick);
        }

        async startConversation(userId) {
            if (userId === null || userId === undefined) {
                return;
            }

            this.stopPolling();
            this.activeUserId = userId;
            this.dom = {};

            this.initialFetch = true;
            this.lastMessagePollTime = null;

            this.chatRoom.innerHTML = this.loadingView();

            try {
                await this.callJson('/messages/markAsRead', { userId }, 'POST');
                const conversation = await this.callJson(`/messages/getConversation?userId=${userId}`);
                this.chatRoom.innerHTML = this.conversationTemplate(conversation.partner, userId);
                this.cacheDomRefs();
                this.bindComposerEvents(userId);

                await this.loadMessages(userId);
                this.startPolling(userId);
                this.setupChatScroll();
            } catch (error) {
                console.error('Error starting conversation:', error);
                this.chatRoom.innerHTML = `<div class="chat_error">Failed to load conversation. Check console for details.</div>`;
            }
        }

        startPolling(userId = this.activeUserId) {
            if (!userId) {
                return;
            }
            this.stopPolling();
            this.messageInterval = setInterval(() => this.loadMessages(userId), this.pollInterval);
        }

        stopPolling() {
            if (this.messageInterval) {
                clearInterval(this.messageInterval);
                this.messageInterval = null;
            }
        }

        async loadMessages(userId = this.activeUserId) {
            if (!userId) {
                return;
            }

            const chatMessages = this.getChatMessagesEl();
            if (!chatMessages) {
                return;
            }

            const wasAtBottom = this.isAtBottom(chatMessages);

            if (this.initialFetch) {
                this.lastMessagePollTime = null;
                chatMessages.innerHTML = '';
            }

            try {
                const payload = {
                    userId,
                    since: this.lastMessagePollTime
                };
                const data = await this.callJson('/messages/getMessages', payload, 'POST');

                if (data.lastPollTime) {
                    this.updateLastPollTime(data.lastPollTime);
                }

                const messages = Array.isArray(data.messages) ? data.messages : [];
                let appendedNew = false;

                messages.forEach((message) => {
                    const created = this.applyMessageUpdate(message);
                    appendedNew = appendedNew || created;
                    const candidate = message?.modified_time || message?.message_time || null;
                    if (candidate) {
                        this.updateLastPollTime(candidate);
                    }
                });

                if (this.initialFetch) {
                    this.initialFetch = false;
                    chatMessages.scrollTop = chatMessages.scrollHeight;
                } else if (appendedNew && wasAtBottom) {
                    chatMessages.scrollTop = chatMessages.scrollHeight;
                }
            } catch (error) {
                console.error('Error loading messages:', error);
                if (this.initialFetch) {
                    chatMessages.innerHTML = '<div class="error">Failed to load messages</div>';
                }
            }
        }

        async sendMessage(userId = this.activeUserId) {
            const messageInput = this.getMessageInputEl();
            if (!messageInput) {
                return;
            }

            const content = messageInput.value.trim();
            if (content === '') {
                return;
            }

            const chatMessages = this.getChatMessagesEl();
            const stickToBottom = this.isAtBottom(chatMessages);

            try {
                const data = await this.callJson('/messages/sendMessage', {
                    recipientId: userId,
                    content
                }, 'POST');

                messageInput.value = '';
                if (data.messageRecord) {
                    const record = data.messageRecord;
                    const candidateTime = record.modified_time || record.message_time || null;
                    this.updateLastPollTime(candidateTime);
                    this.applyMessageUpdate(record);
                    if (stickToBottom && chatMessages) {
                        chatMessages.scrollTop = chatMessages.scrollHeight;
                    }
                } else {
                    await this.loadMessages(userId);
                }
            } catch (error) {
                console.error('Error sending message:', error);
                alert(error.message || 'Error sending message. Check console for details.');
            }
        }

        async submitEditMessage(messageId, inputId) {
            const input = document.getElementById(inputId);
            const newContent = (input?.value || '').trim();
            if (newContent === '') {
                alert('Message cannot be empty');
                return;
            }

            try {
                const data = await this.callJson('/messages/editMessage', { messageId, content: newContent }, 'POST');
                if (data.updatedMessage) {
                    this.applyMessageUpdate(data.updatedMessage);
                }
            } catch (error) {
                console.error('Error updating message', error);
                alert(error.message || 'Error updating message. Check console for details.');
            }
        }

        async performDeleteMessage(messageId) {
            this.setDeleteModalBusy(true);
            try {
                const data = await this.callJson(`/messages/deleteMessage?messageId=${messageId}`);
                if (data.updatedMessage) {
                    this.applyMessageUpdate(data.updatedMessage);
                } else {
                    this.applyDeletedFallback(messageId);
                }
                this.closeDeleteModal();
            } catch (error) {
                console.error('Error deleting message:', error);
                this.closeDeleteModal();
                alert(error.message || 'Error deleting message. Check console for details.');
            } finally {
                this.setDeleteModalBusy(false);
            }
        }

        async searchUsers(query) {
            const usersList = document.getElementById('usersList');

            if (query.length < 2) {
                if (typeof loadAvailableUsers === 'function') {
                    loadAvailableUsers();
                }
                return;
            }

            usersList.innerHTML = '<div class="loading">Searching...</div>';

            try {
                const data = await this.callJson(`/messages/getAvailableUsers?search=${encodeURIComponent(query)}`);
                usersList.innerHTML = '';

                if (data?.success && data.users?.length) {
                    data.users.forEach((user) => {
                        const userItem = typeof createUserItem === 'function' ? createUserItem(user) : null;
                        if (userItem) {
                            usersList.appendChild(userItem);
                        }
                    });
                } else {
                    usersList.innerHTML = '<div class="no-users">No users found matching your search</div>';
                }
            } catch (error) {
                console.error('Error searching users:', error);
                usersList.innerHTML = '<div class="error">Failed to search users</div>';
            }
        }

        async markAsRead(userId) {
            return this.callJson('/messages/markAsRead', { userId }, 'POST');
        }

        conversationTemplate(partner, userId) {
            const avatar = partner?.profile_picture ?
                `${this.baseUrl}/media/profile/${partner.profile_picture}` :
                `${this.baseUrl}/media/profile/default.jpg`;
            const name = partner?.display_name || partner?.name || partner?.username || 'User';
            return `
            <div class="message-section" id="conversationSection" data-user-id="${userId}">
            <div class="message-section-header">
                <div class="conversation-partner-info">
                    <img src="${avatar}"
                         alt="User" class="partner-avatar" id="partnerAvatar"
                         onclick="window.location.href='${this.baseUrl}/profile?userid=${userId}'"
                         style="cursor: pointer;" title="View profile">
                    <div class="partner-details">
                        <h3 class="partner-name"
                            onclick="window.location.href='${this.baseUrl}/profile?userid=${userId}'"
                            style="cursor: pointer;" title="View profile">
                            ${name}
                        </h3>
                    </div>
                </div>
            </div>

            <div class="conversation-content">
                <div class="chat-messages" id="chatMessages"></div>

                <button id="scrollToLatestBtn" class="scroll-to-latest-btn" style="display:none;" title="Jump to latest">
                    <i class="fas fa-arrow-down"></i>
                </button>

                <div class="message-input-container">
                    <div class="input-wrapper">
                        <input type="text" placeholder="Type a message..." class="message-input" id="messageInput">
                    </div>
                    <button class="send-btn" id="sendMessageBtn">
                        <i class="fas fa-paper-plane"></i>
                    </button>
                </div>
            </div>
        </div>
        `;
        }

        loadingView() {
            return `
            <div class="conversation-content">
                <div class="chat-messages loading" id="chatMessages">
                    <div class="chat-loading-state">
                        <div class="chat-loader" aria-hidden="true"></div>
                        <p class="chat-loading-text">Loading conversation...</p>
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
            .conversation-partner-info.loading { align-items: center; gap: 12px; }
            .partner-avatar-skeleton { flex-shrink: 0; width: 40px; height: 40px; border-radius: 50%; background: linear-gradient(90deg, #1a1f27 0%, #252b35 50%, #1a1f27 100%); background-size: 200% 100%; animation: skeletonLoading 1.4s ease infinite; }
            .partner-name-skeleton, .partner-meta-skeleton { height: 12px; border-radius: 6px; background: linear-gradient(90deg, #1a1f27 0%, #252b35 50%, #1a1f27 100%); background-size: 200% 100%; animation: skeletonLoading 1.4s ease infinite; }
            .partner-name-skeleton { width: 150px; margin-bottom: 8px; }
            .partner-meta-skeleton { width: 110px; }
            .conversation-partner-info.loading .partner-details { min-height: 40px; justify-content: center; gap: 6px; }
            .chat-messages.loading { display: flex; align-items: center; justify-content: center; background: #111518; border: 1px solid #1c212a; }
            .chat-loading-state { display: flex; flex-direction: column; align-items: center; gap: 14px; color: #cfd5df; font-size: 14px; }
            .chat-loader { width: 32px; height: 32px; border-radius: 50%; border: 3px solid rgba(29, 161, 242, 0.16); border-top-color: #1da1f2; animation: spin 0.8s linear infinite; }
            .chat-loading-text { margin: 0; font-weight: 500; letter-spacing: 0.01em; }
            .message-input-container .message-input[disabled] { background: #0d1118; border: 1px solid #1c212a; color: #5a667a; cursor: not-allowed; }
            .message-input-container .send-btn[disabled] { background: #1c212a; color: #3f4b5e; cursor: not-allowed; }
            @keyframes spin { to { transform: rotate(360deg); } }
            @keyframes skeletonLoading { 0% { background-position: 200% 0; } 100% { background-position: -200% 0; } }
        </style>
    `;
        }

        applyMessageUpdate(message) {
            const chatMessages = this.getChatMessagesEl();
            if (!chatMessages) {
                return false;
            }

            const messageId = String(message?.message_id ?? '');
            if (!messageId) {
                return false;
            }

            const status = (message?.status || '').toLowerCase();
            const isSent = Number(message?.sender_id) === this.currentUserId;
            const existing = chatMessages.querySelector(`.single-message-container[data-message-id="${messageId}"]`);

            if (existing) {
                this.applyExistingUpdate(existing, message, status, isSent);
                return false;
            }

            const newBubble = this.createMessageBubble(message);
            const targetDateString = message?.message_time || message?.modified_time || null;
            const targetTime = targetDateString ? new Date(targetDateString.replace(' ', 'T')) : null;
            let inserted = false;

            if (targetTime) {
                const children = Array.from(chatMessages.children);
                for (const child of children) {
                    const childTimeStr = child.dataset.messageTime || child.dataset.modifiedTime || '';
                    const childTime = childTimeStr ? new Date(childTimeStr.replace(' ', 'T')) : null;
                    if (childTime && childTime > targetTime) {
                        chatMessages.insertBefore(newBubble, child);
                        inserted = true;
                        break;
                    }
                }
            }

            if (!inserted) {
                chatMessages.appendChild(newBubble);
            }

            return true;
        }

        applyExistingUpdate(existing, message, status, isSent) {
            existing.dataset.status = status;
            if (message?.modified_time) {
                existing.dataset.modifiedTime = message.modified_time;
            } else {
                delete existing.dataset.modifiedTime;
            }
            if (message?.message_time) {
                existing.dataset.messageTime = message.message_time;
            } else if (message?.modified_time) {
                existing.dataset.messageTime = message.modified_time;
            }

            const messageDiv = existing.querySelector('.message');
            if (messageDiv) {
                delete messageDiv.dataset.original;
                delete messageDiv.dataset.originalTime;
                this.applyMessageContent(messageDiv, message);
            }

            const actions = existing.querySelector('.msg-actions');
            if (actions) {
                if (isSent && status !== 'deleted') {
                    this.buildSentMessageActions(actions, message);
                } else {
                    actions.innerHTML = '';
                }
            }
        }

        createMessageBubble(message) {
            const status = (message?.status || '').toLowerCase();
            const isSent = Number(message?.sender_id) === this.currentUserId;
            const container = document.createElement('div');
            container.className = `single-message-container ${isSent ? 'sent' : 'received'}`;

            if (message?.message_id !== undefined && message?.message_id !== null) {
                container.dataset.messageId = String(message.message_id);
            }
            container.dataset.status = status;

            if (message?.message_time) {
                container.dataset.messageTime = message.message_time;
            } else if (message?.modified_time) {
                container.dataset.messageTime = message.modified_time;
            }

            if (message?.modified_time) {
                container.dataset.modifiedTime = message.modified_time;
            }

            const actions = document.createElement('div');
            actions.className = 'msg-actions';

            const messageDiv = document.createElement('div');
            messageDiv.className = `message ${isSent ? 'sent' : 'received'}`;
            this.applyMessageContent(messageDiv, message);

            if (isSent && status !== 'deleted') {
                this.buildSentMessageActions(actions, message);
            }

            if (isSent) {
                container.appendChild(actions);
                container.appendChild(messageDiv);
            } else {
                const avatar = document.createElement('div');
                avatar.className = 'message-avatar';
                const profileSrc = message?.sender_picture ?
                    `${this.baseUrl}/media/profile/${message.sender_picture}` :
                    `${this.baseUrl}/media/profile/default.jpg`;
                avatar.innerHTML = `<img src="${profileSrc}" alt="User" class="avatar-small" onerror="this.src='${this.baseUrl}/media/profile/default.jpg'">`;

                container.appendChild(avatar);
                container.appendChild(messageDiv);
                container.appendChild(actions);
            }

            return container;
        }

        applyMessageContent(messageDiv, message) {
            this.ensureMessageHelperStyles();

            const status = (message?.status || '').toLowerCase();
            const safeText = this.escapeHtml(String(message?.content ?? ''));
            const timestamp = message?.timestamp || '';
            const modifiedDisplay = message?.modified_timestamp || '';
            const isEdited = status === 'edited-read' || status === 'edited-unread';

            messageDiv.classList.toggle('message-deleted', status === 'deleted');

            if (status === 'deleted') {
                messageDiv.innerHTML = `
                <p class="message-text deleted-text">This message was deleted</p>
                <span class="message-time">${timestamp}</span>
            `;
                return;
            }

            const effectiveTime = isEdited && modifiedDisplay ? `${modifiedDisplay}` : timestamp;
            const timeLabel = isEdited && effectiveTime ? `${effectiveTime} (edited)` : effectiveTime;

            messageDiv.innerHTML = `
            <p class="message-text">${safeText}</p>
            <span class="message-time">${timeLabel}</span>
        `;
        }

        buildSentMessageActions(actionsContainer, message) {
            actionsContainer.innerHTML = '';

            const actionsBtn = document.createElement('button');
            actionsBtn.className = 'msg-actions-btn';
            actionsBtn.innerHTML = '<i class="fas fa-ellipsis-v"></i>';
            actionsBtn.addEventListener('click', this.toggleMsgDropdown);

            const dropdown = document.createElement('div');
            dropdown.className = 'msg-dropdown';
            dropdown.style.display = 'none';

            const originalText = String(message?.content || '');

            const editItem = document.createElement('div');
            editItem.className = 'msg-dropdown-item';
            editItem.innerHTML = '<i class="fas fa-edit"></i> Edit';
            editItem.addEventListener('click', (event) => {
                event.stopPropagation();
                this.editMessagePrompt(editItem, message.message_id || '', originalText);
            });

            const deleteItem = document.createElement('div');
            deleteItem.className = 'msg-dropdown-item danger';
            deleteItem.innerHTML = '<i class="fas fa-trash"></i> Delete';
            deleteItem.addEventListener('click', (event) => {
                event.stopPropagation();
                this.deleteMessageConfirm(message.message_id || '');
            });

            dropdown.appendChild(editItem);
            dropdown.appendChild(deleteItem);

            actionsContainer.appendChild(actionsBtn);
            actionsContainer.appendChild(dropdown);
        }

        editMessagePrompt(triggerEl, messageId, currentText) {
            this.hideAllDropdowns();

            const targetContainer = triggerEl.closest('.single-message-container');
            if (!targetContainer) {
                return;
            }

            const msgDiv = targetContainer.querySelector('.message');
            if (!msgDiv) {
                return;
            }

            if (!msgDiv.dataset.original) {
                const originalHtml = msgDiv.innerHTML;
                const temp = document.createElement('div');
                temp.innerHTML = originalHtml;
                const timeNode = temp.querySelector('.message-time');
                msgDiv.dataset.original = originalHtml;
                msgDiv.dataset.originalTime = timeNode ? (timeNode.textContent || '') : '';
            }

            msgDiv.innerHTML = '';

            const wrapper = document.createElement('div');
            wrapper.className = 'edit-message-wrapper';

            const inputId = `edit_${messageId}`;
            const input = document.createElement('input');
            input.id = inputId;
            input.type = 'text';
            input.className = 'message-input';
            input.value = currentText || '';
            wrapper.appendChild(input);

            const actions = document.createElement('div');
            actions.className = 'edit-actions';
            actions.style.marginTop = '6px';
            actions.style.display = 'flex';
            actions.style.gap = '8px';

            const saveBtn = document.createElement('button');
            saveBtn.className = 'send-btn';
            saveBtn.title = 'Save';
            saveBtn.innerHTML = '<i class="fas fa-check"></i>';
            saveBtn.addEventListener('click', () => this.submitEditMessage(messageId, inputId));

            const cancelBtn = document.createElement('button');
            cancelBtn.className = 'send-btn';
            cancelBtn.title = 'Cancel';
            cancelBtn.innerHTML = '<i class="fas fa-times"></i>';
            cancelBtn.addEventListener('click', () => this.cancelEditMessage(messageId));

            actions.appendChild(saveBtn);
            actions.appendChild(cancelBtn);

            wrapper.appendChild(actions);
            msgDiv.appendChild(wrapper);

            input.focus();
        }

        cancelEditMessage(messageId) {
            const container = document.querySelector(`.single-message-container[data-message-id="${messageId}"]`);
            const msgDiv = container?.querySelector('.message');
            if (msgDiv && msgDiv.dataset.original !== undefined) {
                msgDiv.innerHTML = msgDiv.dataset.original;
                delete msgDiv.dataset.original;
                delete msgDiv.dataset.originalTime;
            }
        }

        deleteMessageConfirm(messageId) {
            this.hideAllDropdowns();
            this.pendingDeleteId = messageId;
            this.openDeleteModal();
        }

        ensureDeleteModal() {
            let modal = document.getElementById('chatDeleteModal');
            if (modal) {
                return modal;
            }

            if (!document.getElementById('chatModalStyles')) {
                const styleEl = document.createElement('style');
                styleEl.id = 'chatModalStyles';
                styleEl.textContent = `
                .chat-modal-overlay { position: fixed; inset: 0; background: rgba(7, 12, 18, 0.7); display: none; align-items: center; justify-content: center; z-index: 1100; padding: 16px; }
                .chat-modal { width: 100%; max-width: 360px; background: #11161d; border: 1px solid #1c242f; border-radius: 12px; box-shadow: 0 18px 48px rgba(0, 0, 0, 0.45); padding: 24px; color: #dde4f0; display: flex; flex-direction: column; gap: 18px; }
                .chat-modal h4 { margin: 0; font-size: 18px; font-weight: 600; color: #f5f7fb; }
                .chat-modal p { margin: 0; font-size: 14px; line-height: 1.5; color: #a1aec4; }
                .chat-modal-actions { display: flex; justify-content: flex-end; gap: 10px; }
                .chat-modal-btn { border: none; border-radius: 8px; padding: 10px 18px; font-size: 14px; cursor: pointer; transition: background 0.15s ease, color 0.15s ease, transform 0.15s ease; }
                .chat-modal-btn.cancel { background: #1c242f; color: #c2ccda; }
                .chat-modal-btn.cancel:hover { background: #232f3d; }
                .chat-modal-btn.confirm { background: #d64045; color: #ffffff; }
                .chat-modal-btn.confirm:hover { background: #bb2f33; }
                .chat-modal-btn:focus { outline: none; box-shadow: 0 0 0 2px rgba(91, 155, 245, 0.35); }
            `;
                document.head.appendChild(styleEl);
            }

            modal = document.createElement('div');
            modal.id = 'chatDeleteModal';
            modal.className = 'chat-modal-overlay';
            modal.setAttribute('aria-hidden', 'true');
            modal.innerHTML = `
            <div class="chat-modal" role="dialog" aria-modal="true" aria-labelledby="deleteModalTitle">
                <h4 id="deleteModalTitle">Delete message?</h4>
                <p>This will permanently remove the message from the conversation. You cannot undo this action.</p>
                <div class="chat-modal-actions">
                    <button type="button" class="chat-modal-btn cancel" id="deleteModalCancel">Cancel</button>
                    <button type="button" class="chat-modal-btn confirm" id="deleteModalConfirm">Delete</button>
                </div>
            </div>
        `;

            modal.addEventListener('click', (event) => {
                if (event.target === modal) {
                    this.closeDeleteModal();
                }
            });

            document.body.appendChild(modal);

            document.getElementById('deleteModalCancel').addEventListener('click', () => this.closeDeleteModal());
            document.getElementById('deleteModalConfirm').addEventListener('click', () => {
                if (this.pendingDeleteId) {
                    this.performDeleteMessage(this.pendingDeleteId);
                }
            });

            return modal;
        }

        openDeleteModal() {
            const modal = this.ensureDeleteModal();
            modal.style.display = 'flex';
            modal.setAttribute('aria-hidden', 'false');
            this.setDeleteModalBusy(false);
            const confirmBtn = document.getElementById('deleteModalConfirm');
            confirmBtn?.focus();
        }

        closeDeleteModal() {
            const modal = document.getElementById('chatDeleteModal');
            if (modal) {
                modal.style.display = 'none';
                modal.setAttribute('aria-hidden', 'true');
            }
            this.pendingDeleteId = null;
        }

        setDeleteModalBusy(isBusy) {
            const confirmBtn = document.getElementById('deleteModalConfirm');
            if (!confirmBtn) {
                return;
            }
            confirmBtn.disabled = isBusy;
            confirmBtn.textContent = isBusy ? 'Deleting...' : 'Delete';
        }

        applyDeletedFallback(messageId) {
            const msgEl = document.querySelector(`.single-message-container[data-message-id="${messageId}"]`);
            if (!msgEl) {
                return;
            }
            msgEl.dataset.status = 'deleted';
            const messageDiv = msgEl.querySelector('.message');
            if (messageDiv) {
                messageDiv.classList.add('message-deleted');
                messageDiv.innerHTML = `
                <p class="message-text deleted-text">This message was deleted</p>
                <span class="message-time"></span>
            `;
            }
            const actions = msgEl.querySelector('.msg-actions');
            if (actions) {
                actions.innerHTML = '';
            }
        }

        setupChatScroll() {
            const chatMessages = this.getChatMessagesEl();
            const scrollBtn = this.getScrollButtonEl();
            if (!chatMessages || !scrollBtn) {
                return;
            }

            const onScroll = () => {
                scrollBtn.style.display = this.isAtBottom(chatMessages) ? 'none' : 'flex';
            };

            chatMessages.removeEventListener('scroll', chatMessages._onScrollHandler || (() => {}));
            chatMessages.addEventListener('scroll', onScroll);
            chatMessages._onScrollHandler = onScroll;

            scrollBtn.addEventListener('click', () => this.scrollToLatest(this.currentConversationUserId()));

            onScroll();
        }

        currentConversationUserId() {
            if (this.activeUserId) {
                return this.activeUserId;
            }
            const section = document.getElementById('conversationSection');
            return section ? Number(section.dataset.userId) : null;
        }

        async scrollToLatest(userId = this.activeUserId) {
            const chatMessages = this.getChatMessagesEl();
            if (chatMessages) {
                chatMessages.scrollTo({
                    top: chatMessages.scrollHeight,
                    behavior: 'smooth'
                });
            }

            const scrollBtn = this.getScrollButtonEl();
            if (scrollBtn) {
                scrollBtn.style.display = 'none';
            }

            await this.loadMessages(userId);
        }

        bindComposerEvents(userId) {
            const sendBtn = this.getSendButtonEl();
            if (sendBtn) {
                sendBtn.onclick = () => this.sendMessage(userId);
            }

            const inputEl = this.getMessageInputEl();
            if (inputEl) {
                inputEl.addEventListener('keydown', (e) => {
                    if (e.key === 'Enter' && !e.shiftKey) {
                        e.preventDefault();
                        this.sendMessage(userId);
                    }
                });
            }

            const scrollBtn = this.getScrollButtonEl();
            if (scrollBtn) {
                scrollBtn.onclick = () => this.scrollToLatest(userId);
            }
        }

        ensureMessageHelperStyles() {
            if (document.getElementById('messageHelperStyles')) {
                return;
            }

            const style = document.createElement('style');
            style.id = 'messageHelperStyles';
            style.textContent = `
            .message.message-deleted { opacity: 0.8; }
            .message.message-deleted .message-text,
            .message-text.deleted-text { font-style: italic; color: #9aa3b7; }
        `;
            document.head.appendChild(style);
        }

        toggleMsgDropdown(event) {
            const button = event.currentTarget;
            const dropdown = button.nextElementSibling;
            this.hideAllDropdowns(dropdown);
            dropdown.style.display = dropdown.style.display === 'block' ? 'none' : 'block';
        }

        hideAllDropdowns(except) {
            document.querySelectorAll('.msg-dropdown').forEach((dd) => {
                if (dd !== except) {
                    dd.style.display = 'none';
                }
            });
        }

        handleOutsideActionClick(event) {
            if (!event.target.closest('.msg-actions')) {
                this.hideAllDropdowns();
            }
        }

        handleEscape(event) {
            if (event.key === 'Escape') {
                const modal = document.getElementById('chatDeleteModal');
                if (modal && modal.style.display !== 'none') {
                    this.closeDeleteModal();
                }
            }
        }

        escapeHtml(str) {
            return str.replace(/[&<>"']/g, (m) => ({
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            } [m]));
        }

        isAtBottom(el, threshold = 8) {
            if (!el) {
                return true;
            }
            return (el.scrollHeight - el.scrollTop - el.clientHeight) <= threshold;
        }

        parseServerDate(dateString) {
            if (!dateString) {
                return null;
            }
            const normalized = dateString.replace(' ', 'T');
            const parsed = new Date(normalized);
            return Number.isNaN(parsed.getTime()) ? null : parsed;
        }

        cacheDomRefs() {
            this.dom.chatMessages = document.getElementById('chatMessages');
            this.dom.messageInput = document.getElementById('messageInput');
            this.dom.sendButton = document.getElementById('sendMessageBtn');
            this.dom.scrollButton = document.getElementById('scrollToLatestBtn');
        }

        getChatMessagesEl() {
            return this.dom.chatMessages || document.getElementById('chatMessages');
        }

        getMessageInputEl() {
            return this.dom.messageInput || document.getElementById('messageInput');
        }

        getSendButtonEl() {
            return this.dom.sendButton || document.getElementById('sendMessageBtn');
        }

        getScrollButtonEl() {
            return this.dom.scrollButton || document.getElementById('scrollToLatestBtn');
        }

        updateLastPollTime(candidateTime) {
            if (!candidateTime) {
                return;
            }
            if (!this.lastMessagePollTime) {
                this.lastMessagePollTime = candidateTime;
                return;
            }

            const currentDate = this.parseServerDate(this.lastMessagePollTime);
            const candidateDate = this.parseServerDate(candidateTime);

            if (!currentDate || (candidateDate && candidateDate > currentDate)) {
                this.lastMessagePollTime = candidateTime;
            }
        }

        async callJson(path, body = null, method = 'GET') {
            const data = await this.fetchJson(path, body, method);
            if (data?.success === false) {
                throw new Error(data?.error || 'Request failed');
            }
            return data || {};
        }

        async fetchJson(path, body = null, method = 'GET') {
            const opts = {
                method,
                headers: {
                    'Content-Type': 'application/json'
                }
            };
            if (body && method !== 'GET') {
                opts.body = JSON.stringify(body);
            }
            const response = await fetch(`${this.apiRoot}${path}`, opts);
            return response.json();
        }
    }

    const chatWindow = new ChatWindow({
        apiRoot: <?php echo json_encode(URLROOT); ?>,
        baseUrl: <?php echo json_encode(URLROOT); ?>,
        currentUserId: Number(<?php echo (int)($_SESSION['user_id'] ?? 0); ?>),
        pollInterval: 1000
    });

    // Public API consumed by other scripts / inline handlers
    window.startConversation = (userId) => chatWindow.startConversation(userId);
    window.scrollToLatest = (userId) => chatWindow.scrollToLatest(userId);
    window.searchUsers = (query) => chatWindow.searchUsers(query);
</script>