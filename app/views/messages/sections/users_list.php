<script>
    // Track currently active user ID
    let currentActiveUserId = null;

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
                    
                    // Re-apply active class if this is the currently active user
                    if (currentActiveUserId && user.user_id == currentActiveUserId) {
                        userItem.classList.add('active');
                    }
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
        div.dataset.userId = user.user_id;
        div.onclick = () => {
            // Remove active class from all items
            document.querySelectorAll('.conversation-item').forEach(item => {
                item.classList.remove('active');
            });
            // Add active class to clicked item
            div.classList.add('active');
            // Store the active user ID
            currentActiveUserId = user.user_id;
            startConversation(user.user_id);
        };

        const displayName = user.display_name || user.name || user.username || 'User';
        const avatarSrc = user.profile_picture ?
            `<?php echo URLROOT; ?>/media/profile/${user.profile_picture}` :
            `<?php echo URLROOT; ?>/media/profile/default.jpg`;

        const unreadCount = user.unread_count || 0;
        const unreadBadge = unreadCount > 0 ? `<span class="unread-badge">${unreadCount}</span>` : '';

        div.innerHTML = `
            <div class="user-avatar">
                <img src="${avatarSrc}" 
                     alt="${displayName}" class="avatar-img"
                     onerror="this.src='<?php echo URLROOT; ?>/media/profile/default.jpg'">
            </div>
            <div class="user-info">
                <h4 class="user-name">${displayName}</h4>
            </div>
            ${unreadBadge}
        `;

        return div;
    }

    // Auto-load available users and refresh periodically
    document.addEventListener('DOMContentLoaded', () => {
        loadAvailableUsers();
        setInterval(loadAvailableUsers, 5000);
        const openUserId = <?php echo json_encode($data['openChatUserId'] ?? null); ?>;
        if (openUserId) {
            // Store the active user ID
            currentActiveUserId = openUserId;
            // Wait a bit for users list to load, then mark active
            setTimeout(() => {
                const targetItem = document.querySelector(`.conversation-item[data-user-id="${openUserId}"]`);
                if (targetItem) {
                    document.querySelectorAll('.conversation-item').forEach(item => item.classList.remove('active'));
                    targetItem.classList.add('active');
                }
            }, 500);
            startConversation(openUserId);
        }
    });
</script>

<!-- Users List Section -->
<div>
    <style>
        .user-search-input:focus {
            outline: none;
            border-bottom: 2px solid #1da1f2;
        }
    </style>
    <div class="search-bar-message" style="width: 100%; height: 40px;box-sizing: border-box;">
        <input style="width: 100%; padding:5px 10px;;height:100%; background:#111518;border: 0px;color:white;border-bottom:1px solid #3a3a3a;" type="text" id="userSearchInput" class="user-search-input" placeholder="Search users..." oninput="searchUsers(this.value)">
    </div>
    <div id="usersList" class="usersList"></div>
</div>