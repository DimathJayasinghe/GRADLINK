<script>
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
        const avatarSrc = user.profile_picture ?
            `<?php echo URLROOT; ?>/media/profile/${user.profile_picture}` :
            `<?php echo URLROOT; ?>/media/profile/default.jpg`;

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

    // Auto-load available users and refresh periodically
    document.addEventListener('DOMContentLoaded', () => {
        loadAvailableUsers();
        setInterval(loadAvailableUsers, 15000);
        startConversation(<?php echo json_encode($data['openChatUserId'] ?? null); ?>);
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