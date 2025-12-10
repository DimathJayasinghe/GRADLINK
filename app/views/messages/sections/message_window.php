<script>
let messageInterval = null;
let lastMessagePollTime = null;
const messagePollingInterval = 1000;
let initialMessageFetch = true;
let pendingDeleteMessageId = null;
const currentUserId = Number(<?php echo (int)($_SESSION['user_id'] ?? 0); ?>);
const baseUrl = <?php echo json_encode(URLROOT); ?>;

function parseServerDate(dateString) {
    if (!dateString) {
        return null;
    }
    const normalized = dateString.replace(' ', 'T');
    const parsed = new Date(normalized);
    return Number.isNaN(parsed.getTime()) ? null : parsed;
}

function updateLastPollTime(candidateTime) {
    if (!candidateTime) {
        return;
    }
    if (!lastMessagePollTime) {
        lastMessagePollTime = candidateTime;
        return;
    }

    const currentDate = parseServerDate(lastMessagePollTime);
    const candidateDate = parseServerDate(candidateTime);

    if (!currentDate || (candidateDate && candidateDate > currentDate)) {
        lastMessagePollTime = candidateTime;
    }
}

async function startConversation(userId) {
    if (userId === null || userId === undefined) {
        return;
    }

    if (messageInterval) {
        clearInterval(messageInterval);
        messageInterval = null;
    }

    initialMessageFetch = true;
    lastMessagePollTime = null;

    const chatRoom = document.getElementById('chatRoom');
    chatRoom.innerHTML = loadingMessageView();

    try {
        await fetch(`<?php echo URLROOT; ?>/messages/markAsRead`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ userId })
        });

        const response = await fetch(`<?php echo URLROOT; ?>/messages/getConversation?userId=${userId}`);
        const data = await response.json();

        if (!data.success) {
            chatRoom.innerHTML = `<div class="chat_error">Failed to load conversation: ${data.error || 'Unknown error'}</div>`;
            return;
        }

        chatRoom.innerHTML = `
        <div class="message-section" id="conversationSection" data-user-id="${userId}">
            <div class="message-section-header">
                <div class="conversation-partner-info">
                    <img src="${data.partner && data.partner.profile_picture
                        ? `<?php echo URLROOT; ?>/media/profile/${data.partner.profile_picture}`
                        : `<?php echo URLROOT; ?>/media/profile/default.jpg`}"
                         alt="User" class="partner-avatar" id="partnerAvatar"
                         onclick="window.location.href='<?php echo URLROOT; ?>/profile?userid=${userId}'"
                         style="cursor: pointer;" title="View profile">
                    <div class="partner-details">
                        <h3 class="partner-name"
                            onclick="window.location.href='<?php echo URLROOT; ?>/profile?userid=${userId}'"
                            style="cursor: pointer;" title="View profile">
                            ${(data.partner && (data.partner.display_name || data.partner.name || data.partner.username)) || 'User'}
                        </h3>
                    </div>
                </div>
            </div>

            <div class="conversation-content">
                <div class="chat-messages" id="chatMessages"></div>

                <button id="scrollToLatestBtn" class="scroll-to-latest-btn" style="display:none;" title="Jump to latest"
                        onclick="scrollToLatest(${userId})">
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

        const sendBtn = document.getElementById('sendMessageBtn');
        if (sendBtn) {
            sendBtn.addEventListener('click', () => sendMessage(userId));
        }

        const inputEl = document.getElementById('messageInput');
        if (inputEl) {
            inputEl.addEventListener('keydown', (e) => {
                if (e.key === 'Enter' && !e.shiftKey) {
                    e.preventDefault();
                    sendMessage(userId);
                }
            });
        }

        await loadMessages(userId);
        messageInterval = setInterval(() => loadMessages(userId), messagePollingInterval);
        setupChatScroll();
    } catch (error) {
        console.error('Error starting conversation:', error);
        chatRoom.innerHTML = `<div class="chat_error">Failed to load conversation. Check console for details.</div>`;
    }
}

function ensureMessageHelperStyles() {
    if (document.getElementById('messageHelperStyles')) {
        return;
    }

    const style = document.createElement('style');
    style.id = 'messageHelperStyles';
    style.textContent = `
        .message.message-deleted {
            opacity: 0.8;
        }
        .message.message-deleted .message-text,
        .message-text.deleted-text {
            font-style: italic;
            color: #9aa3b7;
        }
    `;
    document.head.appendChild(style);
}

function applyMessageContent(messageDiv, message) {
    ensureMessageHelperStyles();

    const status = (message?.status || '').toLowerCase();
    const safeText = escapeHtml(String(message?.content ?? ''));
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

function buildSentMessageActions(actionsContainer, message) {
    actionsContainer.innerHTML = '';

    const actionsBtn = document.createElement('button');
    actionsBtn.className = 'msg-actions-btn';
    actionsBtn.innerHTML = '<i class="fas fa-ellipsis-v"></i>';
    actionsBtn.addEventListener('click', toggleMsgDropdown);

    const dropdown = document.createElement('div');
    dropdown.className = 'msg-dropdown';
    dropdown.style.display = 'none';

    const originalText = String(message?.content || '');

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
        deleteMessageConfirm(message.message_id || '');
    });

    dropdown.appendChild(editItem);
    dropdown.appendChild(deleteItem);

    actionsContainer.appendChild(actionsBtn);
    actionsContainer.appendChild(dropdown);
}

function applyMessageUpdate(message) {
    const chatMessages = document.getElementById('chatMessages');
    if (!chatMessages) {
        return false;
    }

    const messageId = String(message?.message_id ?? '');
    if (!messageId) {
        return false;
    }

    const status = (message?.status || '').toLowerCase();
    const isSent = Number(message?.sender_id) === currentUserId;

    const existing = chatMessages.querySelector(`.single-message-container[data-message-id="${messageId}"]`);

    if (existing) {
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
            applyMessageContent(messageDiv, message);
        }

        const actions = existing.querySelector('.msg-actions');
        if (actions) {
            if (isSent && status !== 'deleted') {
                buildSentMessageActions(actions, message);
            } else {
                actions.innerHTML = '';
            }
        }

        return false;
    }

    const newBubble = createMessageBubble(message);

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

async function loadMessages(userId) {
    const chatMessages = document.getElementById('chatMessages');
    if (!chatMessages) {
        return;
    }

    const wasAtBottom = isAtBottom(chatMessages);

    if (initialMessageFetch) {
        lastMessagePollTime = null;
        chatMessages.innerHTML = '';
    }

    try {
        const response = await fetch(`<?php echo URLROOT; ?>/messages/getMessages`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ userId, since: lastMessagePollTime })
        });
        const data = await response.json();

        if (!data.success) {
            if (initialMessageFetch) {
                chatMessages.innerHTML = '<div class="error">Failed to load messages</div>';
            }
            return;
        }

        if (data.lastPollTime) {
            updateLastPollTime(data.lastPollTime);
        }

        const messages = Array.isArray(data.messages) ? data.messages : [];
        let appendedNew = false;

        messages.forEach((message) => {
            const created = applyMessageUpdate(message);
            appendedNew = appendedNew || created;
            const candidate = message?.modified_time || message?.message_time || null;
            if (candidate) {
                updateLastPollTime(candidate);
            }
        });

        if (initialMessageFetch) {
            initialMessageFetch = false;
            chatMessages.scrollTop = chatMessages.scrollHeight;
        } else if (appendedNew && wasAtBottom) {
            chatMessages.scrollTop = chatMessages.scrollHeight;
        }
    } catch (error) {
        console.error('Error loading messages:', error);
        if (initialMessageFetch) {
            chatMessages.innerHTML = '<div class="error">Failed to load messages</div>';
        }
    }
}

function createMessageBubble(message) {
    const status = (message?.status || '').toLowerCase();
    const isSent = Number(message?.sender_id) === currentUserId;
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
    applyMessageContent(messageDiv, message);

    if (isSent && status !== 'deleted') {
        buildSentMessageActions(actions, message);
    }

    if (isSent) {
        container.appendChild(actions);
        container.appendChild(messageDiv);
    } else {
        const avatar = document.createElement('div');
        avatar.className = 'message-avatar';
        const profileSrc = message?.sender_picture
            ? `${baseUrl}/media/profile/${message.sender_picture}`
            : `${baseUrl}/media/profile/default.jpg`;
        avatar.innerHTML = `<img src="${profileSrc}" alt="User" class="avatar-small" onerror="this.src='${baseUrl}/media/profile/default.jpg'">`;

        container.appendChild(avatar);
        container.appendChild(messageDiv);
        container.appendChild(actions);
    }

    return container;
}

async function sendMessage(userId) {
    const messageInput = document.getElementById('messageInput');
    if (!messageInput) {
        return;
    }

    const content = messageInput.value.trim();
    if (content === '') {
        return;
    }

    const chatMessages = document.getElementById('chatMessages');
    const stickToBottom = isAtBottom(chatMessages);

    try {
        const response = await fetch(`<?php echo URLROOT; ?>/messages/sendMessage`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ recipientId: userId, content })
        });
        const data = await response.json();

        if (data.success) {
            messageInput.value = '';
            if (data.messageRecord) {
                const record = data.messageRecord;
                const candidateTime = record.modified_time || record.message_time || null;
                updateLastPollTime(candidateTime);
                applyMessageUpdate(record);
                if (stickToBottom && chatMessages) {
                    chatMessages.scrollTop = chatMessages.scrollHeight;
                }
            } else {
                await loadMessages(userId);
            }
        } else {
            alert('Failed to send message: ' + data.error);
        }
    } catch (error) {
        console.error('Error sending message:', error);
        alert('Error sending message. Check console for details.');
    }
}

function isAtBottom(el, threshold = 8) {
    if (!el) {
        return true;
    }
    return (el.scrollHeight - el.scrollTop - el.clientHeight) <= threshold;
}

function setupChatScroll() {
    const chatMessages = document.getElementById('chatMessages');
    const scrollBtn = document.getElementById('scrollToLatestBtn');
    if (!chatMessages || !scrollBtn) {
        return;
    }

    const onScroll = () => {
        scrollBtn.style.display = isAtBottom(chatMessages) ? 'none' : 'flex';
    };

    chatMessages.removeEventListener('scroll', chatMessages._onScrollHandler || (() => {}));
    chatMessages.addEventListener('scroll', onScroll);
    chatMessages._onScrollHandler = onScroll;

    onScroll();
}

async function scrollToLatest(userId) {
    const chatMessages = document.getElementById('chatMessages');
    if (chatMessages) {
        chatMessages.scrollTo({ top: chatMessages.scrollHeight, behavior: 'smooth' });
    }

    const scrollBtn = document.getElementById('scrollToLatestBtn');
    if (scrollBtn) {
        scrollBtn.style.display = 'none';
    }

    await loadMessages(userId);
}

function deleteMessageConfirm(messageId) {
    document.querySelectorAll('.msg-dropdown').forEach((dd) => {
        dd.style.display = 'none';
    });

    pendingDeleteMessageId = messageId;
    openDeleteModal();
}

function ensureDeleteModal() {
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
            closeDeleteModal();
        }
    });

    document.body.appendChild(modal);

    document.getElementById('deleteModalCancel').addEventListener('click', () => closeDeleteModal());
    document.getElementById('deleteModalConfirm').addEventListener('click', () => {
        if (pendingDeleteMessageId) {
            performDeleteMessage(pendingDeleteMessageId);
        }
    });

    return modal;
}

function openDeleteModal() {
    const modal = ensureDeleteModal();
    modal.style.display = 'flex';
    modal.setAttribute('aria-hidden', 'false');
    setDeleteModalBusy(false);
    const confirmBtn = document.getElementById('deleteModalConfirm');
    confirmBtn?.focus();
}

function closeDeleteModal() {
    const modal = document.getElementById('chatDeleteModal');
    if (modal) {
        modal.style.display = 'none';
        modal.setAttribute('aria-hidden', 'true');
    }
    pendingDeleteMessageId = null;
}

function performDeleteMessage(messageId) {
    setDeleteModalBusy(true);
    fetch(`<?php echo URLROOT; ?>/messages/deleteMessage?messageId=${messageId}`)
        .then((r) => r.json())
        .then((data) => {
            if (data.success) {
                if (data.updatedMessage) {
                    applyMessageUpdate(data.updatedMessage);
                } else {
                    const msgEl = document.querySelector(`.single-message-container[data-message-id="${messageId}"]`);
                    if (msgEl) {
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
                }
                closeDeleteModal();
            } else {
                closeDeleteModal();
                alert('Failed to delete message: ' + (data.error || 'Unknown error'));
            }
        })
        .catch((error) => {
            console.error('Error deleting message:', error);
            closeDeleteModal();
            alert('Error deleting message. Check console for details.');
        })
        .finally(() => {
            setDeleteModalBusy(false);
        });
}

function setDeleteModalBusy(isBusy) {
    const confirmBtn = document.getElementById('deleteModalConfirm');
    if (!confirmBtn) {
        return;
    }
    confirmBtn.disabled = isBusy;
    confirmBtn.textContent = isBusy ? 'Deleting...' : 'Delete';
}

document.addEventListener('keydown', (event) => {
    if (event.key === 'Escape') {
        const modal = document.getElementById('chatDeleteModal');
        if (modal && modal.style.display !== 'none') {
            closeDeleteModal();
        }
    }
});

function escapeHtml(str) {
    return str.replace(/[&<>"']/g, (m) => ({
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    }[m]));
}

function toggleMsgDropdown(event) {
    const button = event.currentTarget;
    const dropdown = button.nextElementSibling;

    document.querySelectorAll('.msg-dropdown').forEach((dd) => {
        if (dd !== dropdown) {
            dd.style.display = 'none';
        }
    });

    if (dropdown.style.display === 'none' || dropdown.style.display === '') {
        dropdown.style.display = 'block';
    } else {
        dropdown.style.display = 'none';
    }
}

document.addEventListener('click', (event) => {
    if (!event.target.closest('.msg-actions')) {
        document.querySelectorAll('.msg-dropdown').forEach((dd) => {
            dd.style.display = 'none';
        });
    }
});

function editMessagePrompt(triggerEl, messageId, currentText) {
    document.querySelectorAll('.msg-dropdown').forEach((dd) => dd.style.display = 'none');

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
    saveBtn.addEventListener('click', () => submitEditMessage(messageId, inputId));

    const cancelBtn = document.createElement('button');
    cancelBtn.className = 'send-btn';
    cancelBtn.title = 'Cancel';
    cancelBtn.innerHTML = '<i class="fas fa-times"></i>';
    cancelBtn.addEventListener('click', () => cancelEditMessage(messageId));

    actions.appendChild(saveBtn);
    actions.appendChild(cancelBtn);

    wrapper.appendChild(actions);
    msgDiv.appendChild(wrapper);

    input.focus();
}

function loadingMessageView() {
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
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ messageId, content: newContent })
        });
        const data = await res.json();

        if (!data.success) {
            alert('Failed to update message: ' + (data.error || 'Unknown error'));
            return;
        }
        if (data.updatedMessage) {
            applyMessageUpdate(data.updatedMessage);
        }
    } catch (error) {
        console.error('Error updating message', error);
        alert('Error updating message. Check console for details.');
    }
}

function cancelEditMessage(messageId) {
    const container = document.querySelector(`.single-message-container[data-message-id="${messageId}"]`);
    const msgDiv = container?.querySelector('.message');
    if (msgDiv && msgDiv.dataset.original !== undefined) {
        msgDiv.innerHTML = msgDiv.dataset.original;
        delete msgDiv.dataset.original;
        delete msgDiv.dataset.originalTime;
    }
}

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
            data.users.forEach((user) => {
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
