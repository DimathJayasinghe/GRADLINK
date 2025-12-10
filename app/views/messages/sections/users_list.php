<script>
    // Track currently active user ID
    let currentActiveUserId = null;
    let initialLoad = true;
    let lastPollTime = null;
    let loadedUsers = [];

    let availableUsersPollingTime = 1000;  // 1second

    // Load available users asynchronously
    async function loadAvailableUsers() {
        const usersList = document.getElementById('usersList');
        if (initialLoad) {
            usersList.innerHTML = '<div class="loading_users">Loading users...</div>';
        }

        try {
            const response = await fetch('<?php echo URLROOT; ?>/messages/getAvailableUsers', {
                method: "POST",
                headers: {
                    "Content-Type": "application/json"
                },
                body: JSON.stringify({
                    lastPoll: lastPollTime
                })
            });
            const data = await response.json();
            lastPollTime = data.lastPollTime;

            if (data.success && data.users && data.users.length > 0) {
                if (initialLoad) {
                    usersList.innerHTML = '';
                    data.users.forEach(user => {
                        loadedUsers.push(user.user_id);
                        const userItem = createUserItem(user);
                        if (user.unread_count > 0) {
                            injectUnreadCount(userItem, user.unread_count);
                        }
                        usersList.appendChild(userItem);

                        // Re-apply active class if this is the currently active user
                        if (currentActiveUserId && user.user_id == currentActiveUserId) {
                            userItem.classList.add('active');
                        }
                    });
                    initialLoad = false;
                } else {
                    data.users.forEach(async (user) => {
                        if (!loadedUsers.includes(user.user_id)) {
                            const userItem = createUserItem(user);
                            usersList.appendChild(userItem);
                            if (user.unread_count > 0) {
                                // If this is the active chat, mark as read and do not show badge
                                if (user.user_id === currentActiveUserId) {
                                    await fetch(`<?php echo URLROOT; ?>/messages/markAsRead`, {
                                        method: 'POST',
                                        headers: { 'Content-Type': 'application/json' },
                                        body: JSON.stringify({ userId: currentActiveUserId })
                                    });
                                } else {
                                    injectUnreadCount(userItem, user.unread_count);
                                }
                            }
                            loadedUsers.push(user.user_id);
                        } else {
                            const userDiv = usersList.querySelector(`.conversation-item[data-user-id="${user.user_id}"]`);
                            if (!userDiv) return;
                            if (user.user_id === currentActiveUserId) {
                                // Active chat: ensure server row cleared and remove badge
                                await fetch(`<?php echo URLROOT; ?>/messages/markAsRead`, {
                                    method: 'POST',
                                    headers: { 'Content-Type': 'application/json' },
                                    body: JSON.stringify({ userId: currentActiveUserId })
                                });
                                removeUnreadCount(userDiv);
                            } else if (user.unread_count > 0) {
                                injectUnreadCount(userDiv, user.unread_count);
                                // Move to top on new unread activity
                                const wasActive = userDiv.classList.contains('active');
                                usersList.insertBefore(userDiv, usersList.firstChild);
                                if (wasActive) userDiv.classList.add('active');
                            } else {
                                removeUnreadCount(userDiv);
                            }
                        }
                    });
                }

            } else {
                if (initialLoad || usersList.children.length === 0) {
                    usersList.innerHTML = '<div class="no-users">No available users found.</div>';
                    initialLoad = false;
                }
                // Nothing new to render; keep currently displayed conversations
            }
        } catch (error) {
            console.error('Error loading users:', error);
            usersList.innerHTML = `
                <div class="error">
                    Failed to load users.<br>
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


    function injectUnreadCount(div, count) {
        const unreadCount = count || 0;
        let badge = div.querySelector('.unread-badge');

        if (unreadCount > 0) {
            if (!badge) {
                badge = document.createElement('span');
                badge.className = 'unread-badge';
                div.appendChild(badge);
            }
            badge.textContent = unreadCount;
        } else if (badge) {
            badge.remove();
        }
        return div;
    }

    function removeUnreadCount(div) {
        if (!div) return;
        const badges = div.querySelectorAll('.unread-badge');
        badges.forEach(b => b.remove());
    }

    // Auto-load available users and refresh periodically
    document.addEventListener('DOMContentLoaded', () => {
        loadAvailableUsers();
        setInterval(loadAvailableUsers, availableUsersPollingTime);
        currentActiveUserId = <?php echo json_encode($data['openChatUserId'] ?? null); ?>;
        currentActiveUserId && startConversation(currentActiveUserId);
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