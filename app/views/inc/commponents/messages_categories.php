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
            <div class="conversations-list" id="conversationsList"></div>
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

<!-- Fallback shim: ensure openExistingConversation exists globally for inline onclicks -->
<script>
// Load conversations via secure AJAX so only this user's threads are shown
document.addEventListener('DOMContentLoaded', function() {
    loadConversationsList();
});

function loadConversationsList() {
    const list = document.getElementById('conversationsList');
    if (!list) return;
    list.innerHTML = '<div class="loading">Loading conversations...</div>';
    fetch('<?php echo URLROOT; ?>/messages/getConversations')
        .then(res => res.json())
        .then(data => {
            list.innerHTML = '';
            if (!data || !data.success) {
                list.innerHTML = '<div class="error">Failed to load conversations</div>';
                return;
            }
            const conversations = Array.isArray(data.conversations) ? data.conversations : [];
            if (conversations.length === 0) {
                list.innerHTML = '<div class="no-conversations">No conversations yet. Click the + button to start a new one.</div>';
                return;
            }
            conversations.forEach(c => list.appendChild(createConversationItem(c)));
        })
        .catch(err => {
            console.error('loadConversationsList error', err);
            list.innerHTML = '<div class="error">Failed to load conversations</div>';
        });
}

function createConversationItem(conversation) {
    const conversationId = conversation.conversation_id || 0;
    const userName = conversation.other_full_name || 'User';
    const userAvatar = conversation.other_avatar || 'default-avatar.png';
    const otherUserId = conversation.other_user_id || null;
    const lastMessage = conversation.last_message || '';
    const lastTimeRaw = conversation.last_message_time || null;
    const lastTime = lastTimeRaw ? new Date(lastTimeRaw.replace(' ', 'T')).toLocaleTimeString([], {hour:'numeric', minute:'2-digit'}) : '';
    const unreadCount = Number(conversation.unread_count || 0);

    // Name shortening
    let displayName = userName;
    const parts = userName.trim().split(/\s+/);
    if (userName.length > 15 && parts.length > 1) {
        displayName = parts[0] + ' ' + (parts[parts.length - 1].charAt(0).toUpperCase()) + '.';
    }

    const div = document.createElement('div');
    div.className = 'conversation-item';
    div.onclick = function() { openExistingConversation(div, conversationId, userName, userAvatar, otherUserId); };

    div.innerHTML = `
        <div class="user-avatar">
            <img src="<?php echo URLROOT; ?>/media/profile/${userAvatar}"
                 alt="${userName}" class="avatar-img"
                 onerror="this.src='<?php echo URLROOT; ?>/img/default-avatar.png'">
        </div>
        <div class="user-info">
            <h4 class="user-name">${displayName}</h4>
            <p class="last-message">${escapeHtml(lastMessage)}</p>
        </div>
        <div class="message-meta">
            <span class="time-text">${lastTime}</span>
            ${unreadCount > 0 ? `<div class="unread-badge">${unreadCount}</div>` : ''}
            <div class="conversation-options">
                <button class="options-btn" onclick="event.stopPropagation(); toggleConversationDropdown(${conversationId})">
                    <i class="fas fa-ellipsis-v"></i>
                </button>
                <div class="conversation-dropdown" id="dropdown-${conversationId}" style="display: none;">
                    <div class="dropdown-item" onclick="event.stopPropagation(); reportConversation(${conversationId}, ${JSON.stringify(userName)})">
                        <i class="fas fa-flag"></i>
                        <span>Report conversation</span>
                    </div>
                    <div class="dropdown-item danger" onclick="event.stopPropagation(); deleteConversation(${conversationId}, ${JSON.stringify(userName)})">
                        <i class="fas fa-trash"></i>
                        <span>Delete conversation</span>
                    </div>
                </div>
            </div>
        </div>`;
    return div;
}

function escapeHtml(text) {
    if (text == null) return '';
    return String(text)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}

// Define a minimal handler if not already defined by v_messages.php
if (typeof window.openExistingConversation !== 'function') {
    window.openExistingConversation = function(el, conversationId, userName, userAvatar, partnerUserId) {
        try {
            // Visual active state
            document.querySelectorAll('.conversation-item').forEach(function(i){ i.classList.remove('active'); });
            if (el && el.classList) el.classList.add('active');

            // If partner id known and opener exists
            if (partnerUserId && typeof window.openUserConversation === 'function') {
                window.openUserConversation(partnerUserId, userName, userAvatar);
                return;
            }

            // Try to resolve partner via conversation details
            fetch('<?php echo URLROOT; ?>/messages/getMessages/' + conversationId)
                .then(function(res){ return res.ok ? res.json() : Promise.reject(); })
                .then(function(data){
                    if (data && data.success && data.conversation && data.conversation.other_user_id && typeof window.openUserConversation === 'function') {
                        window.openUserConversation(data.conversation.other_user_id, userName, userAvatar);
                    } else if (typeof window.openUserConversation !== 'function') {
                        console.warn('Conversation opener not loaded yet.');
                    }
                })
                .catch(function(){ console.warn('Failed to resolve conversation details'); });
        } catch (e) {
            console.error('openExistingConversation shim error:', e);
        }
    };
}
</script>