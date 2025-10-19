<?php ob_start();?>
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/messages/messages.css">
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/messages/messages_sections.css">
<script>
// Define functions globally to ensure they're available when buttons are clicked
window.openNewConversationModal = function() {
    console.log('Opening modal...');
    document.getElementById('newConversationModal').style.display = 'flex';
    loadAvailableUsers();
}

window.closeNewConversationModal = function() {
    document.getElementById('newConversationModal').style.display = 'none';
}

// Load available users for new conversation
function loadAvailableUsers() {
    const usersList = document.getElementById('usersList');
    usersList.innerHTML = '<div class="loading">Loading users...</div>';
    
    // Fetch real users from database only
    fetch('<?php echo URLROOT; ?>/messages/getAvailableUsers')
        .then(response => {
            console.log('Response status:', response.status);
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }
            return response.json();
        })
        .then(data => {
            console.log('API Response:', data);
            usersList.innerHTML = '';
            
            if (data.success && data.users && data.users.length > 0) {
                console.log(`Found ${data.users.length} users`);
                data.users.forEach(user => {
                    console.log('Creating user item for:', user);
                    const userItem = createUserItem(user);
                    usersList.appendChild(userItem);
                });
            } else {
                usersList.innerHTML = '<div class="no-users">No users found in database.<br>' + 
                    (data.message || 'Make sure you have users in your database.') + '</div>';
            }
        })
        .catch(error => {
            console.error('Error loading users:', error);
            usersList.innerHTML = '<div class="error">Failed to load users from database.<br>' + 
                'Error: ' + error.message + '<br>' +
                'Check console for details.</div>';
        });
}

// Create user item element
function createUserItem(user) {
    const div = document.createElement('div');
    div.className = 'conversation-item';
    div.onclick = () => startConversation(user.user_id, user.full_name || user.username);
    
    // Handle name display
    const displayName = user.full_name || user.username;
    const nameParts = displayName.split(' ');
    const shortName = displayName.length > 15 && nameParts.length > 1 
        ? nameParts[0] + ' ' + nameParts[nameParts.length - 1].charAt(0).toUpperCase() + '.'
        : displayName;
    
    // Handle avatar
    const avatarSrc = user.profile_picture 
        ? `<?php echo URLROOT; ?>/media/profile/${user.profile_picture}`
        : `<?php echo URLROOT; ?>/img/default-avatar.png`;
    
    // Show if user already has conversation
    const statusText = user.has_conversation == 1 
        ? 'Continue conversation' 
        : 'Click to start conversation';
    
    div.innerHTML = `
        <div class="user-avatar">
            <img src="${avatarSrc}" 
                 alt="${displayName}" class="avatar-img" 
                 onerror="this.src='<?php echo URLROOT; ?>/img/default-avatar.png'">
        </div>
        <div class="user-info">
            <h4 class="user-name">${shortName}</h4>
            <p class="last-message">${statusText}</p>
        </div>
        ${user.has_conversation == 1 ? '<div class="conversation-indicator"><i class="fas fa-comment"></i></div>' : ''}
    `;
    
    return div;
}

// Start new conversation
function startConversation(userId, userName) {
    closeNewConversationModal();
    // Use the conversation section instead of redirecting
    if (typeof openConversation === 'function') {
        openConversation(userId, userName);
    } else {
        console.error('openConversation function not found');
    }
}

// Search users function
function searchUsers(query) {
    if (query.length < 2) {
        loadAvailableUsers(); // Load all users if query too short
        return;
    }
    
    const usersList = document.getElementById('usersList');
    usersList.innerHTML = '<div class="loading">Searching...</div>';
    
    // Fetch filtered users from database
    fetch(`<?php echo URLROOT; ?>/messages/getAvailableUsers?search=${encodeURIComponent(query)}`)
        .then(response => response.json())
        .then(data => {
            usersList.innerHTML = '';
            
            if (data.success && data.users.length > 0) {
                data.users.forEach(user => {
                    const userItem = createUserItem(user);
                    usersList.appendChild(userItem);
                });
            } else {
                usersList.innerHTML = '<div class="no-users">No users found matching your search</div>';
            }
        })
        .catch(error => {
            console.error('Error searching users:', error);
            usersList.innerHTML = '<div class="error">Failed to search users</div>';
        });
}
</script>
<?php $styles = ob_get_clean();?>

<?php 
    $notification = []
?>

<?php ob_start();?>
<?php
    $notifications = [
        (object)[
            'type' => 'like',
            'user' => 'Alice',
            'content' => ' liked your post.',
            'time' => '2h ago',
            'userImg' => URLROOT . '/media/profile/alice.jpg'
        ],
        (object)[
            'type' => 'follow',
            'user' => 'Bob',
            'content' => ' started following you.',
            'time' => '3h ago',
            'userImg' => URLROOT . '/media/profile/bob.jpg'
        ]
    ];
?>
<?php ob_start();
    $leftside_buttons = [
        ['icon' => 'home', 'label' => 'Home', 'onclick' => "window.location.href='" . URLROOT . "/mainfeed'" , 'active' => true],
        ['icon' => 'search', 'label' => 'Explore', 'onclick' => "window.location.href='" . URLROOT . "/explore'"],
        ['icon' => 'bell', 'label' => 'Notifications', 'onclick' => "NotificationModal()", 'require' => APPROOT . '/views/inc/commponents/notification_pop_up.php', 'notifications' => $notifications],
        ['icon' => 'envelope', 'label' => 'Messages', 'onclick' => "window.location.href='" . URLROOT . "/messages'"],
        // ['icon' => 'user', 'label' => 'Profile' , 'onclick' => "window.location.href='" . URLROOT . "/profile/watch/".$_SESSION['user_id'] . "'"],
        ['icon' => 'user', 'label' => 'Profile' , 'onclick' => "window.location.href='" . URLROOT . "/profile?userid=".$_SESSION['user_id'] . "'"],
        // icon for fundraiser
        ['icon' => 'hand-holding-heart', 'label' => 'Fundraisers', 'onclick' => "window.location.href='" . URLROOT . "/fundraiser'"],
        // ['icon' => 'clipboard-list', 'label' => 'Post Requests', 'onclick' => "window.location.href='" . URLROOT . "/postrequest/'"],
        ['icon' => 'clipboard-list', 'label' => 'Event Requests', 'onclick' => "window.location.href='" . URLROOT . "/eventrequest/'"],
        ['icon' => 'calendar-alt', 'label' => 'Calender', 'onclick' => "window.location.href='" . URLROOT . "/calender'"],
    ];
    //  new portal to approve new alumnis only available for special alumnis
    if ($_SESSION['special_alumni']){
        $leftside_buttons[] = [
            'icon'=>'user-check','label'=>'Approve Alumni','onclick'=>"window.location.href='".URLROOT."/alumni/approve'"
        ];
    };
    $leftside_buttons[] = ['icon' => 'cog', 'label' => 'Settings', 'onclick' => "window.location.href='" . URLROOT . "/settings'"];
    require APPROOT . '/views/inc/commponents/leftSideBar.php'; ?>
<?php $leftsidebar = ob_get_clean(); ?>

<?php ob_start();?>
    <!-- Message categories -->
    <?php 
        $message_categories = [
            // ['icon' => 'inbox', 'label' => 'All Messages', 'link' => URLROOT . '/messages/all', 'active' => $data['section'] === 'all'? true : false],
            // ['icon' => 'users', 'label' => 'Groups', 'link' => URLROOT . '/messages/groups','active' => $data['section'] === 'groups'? true : false],
            // ['icon' => 'graduation-cap', 'label' => 'Batch', 'link' => URLROOT . '/messages/batch','active' => $data['section'] === 'batch'? true : false],
            //['icon' => 'star', 'label' => 'Starred', 'link' => URLROOT . '/messages/starred','active' => $data['section'] === 'starred'? true : false],
        ];
        require APPROOT . '/views/inc/commponents/messages_categories.php';
    ?>
<?php $center_content = ob_get_clean(); ?>

<?php ob_start();?>
    <?php require APPROOT . '/views/messages/sections/' . $data['section']. '_section.php'; ?>
<?php $rightsidebar = ob_get_clean(); ?>

<?php ob_start();?>
<script>
    // Open existing conversation (can handle both conversationId and userId)
    function openConversation(conversationIdOrUserId, userName = null) {
        // Update active conversation
        document.querySelectorAll('.conversation-item').forEach(item => {
            item.classList.remove('active');
        });
        if (event && event.currentTarget) {
            event.currentTarget.classList.add('active');
        }
        
        // Check if conversation section's openConversation function exists
        if (window.openConversation && typeof window.openConversation === 'function') {
            // This is a call from the conversation section
            if (userName) {
                // Starting new conversation with user
                window.openConversation(conversationIdOrUserId, userName);
            } else {
                // Opening existing conversation - need to get user info
                // For now, use conversationId to load the conversation
                console.log('Opening existing conversation:', conversationIdOrUserId);
                // You could fetch conversation details and then call window.openConversation
                // For now, we'll redirect or handle differently
            }
        } else {
            console.log('Opening conversation:', conversationIdOrUserId);
        }
    }
    
    // Close modal when clicking outside
    window.onclick = function(event) {
        const modal = document.getElementById('newConversationModal');
        if (event.target === modal) {
            closeNewConversationModal();
        }
        
        // Close all conversation dropdowns when clicking outside
        const dropdowns = document.querySelectorAll('.conversation-dropdown');
        dropdowns.forEach(dropdown => {
            if (!dropdown.contains(event.target) && !event.target.closest('.options-btn')) {
                dropdown.style.display = 'none';
            }
        });
    }
    
    // Toggle conversation dropdown
    function toggleConversationDropdown(conversationId) {
        const dropdown = document.getElementById(`dropdown-${conversationId}`);
        const isVisible = dropdown.style.display === 'block';
        
        // Hide all other dropdowns
        document.querySelectorAll('.conversation-dropdown').forEach(d => {
            d.style.display = 'none';
        });
        
        // Toggle current dropdown
        dropdown.style.display = isVisible ? 'none' : 'block';
    }
    
    // Report conversation
    function reportConversation(conversationId, userName) {
        const reasons = [
            'Spam or unwanted messages',
            'Inappropriate content',
            'Harassment or bullying',
            'Fake account',
            'Other'
        ];
        
        let reason = '';
        let reasonText = `Please select a reason for reporting conversation with ${userName}:\n\n`;
        reasons.forEach((r, index) => {
            reasonText += `${index + 1}. ${r}\n`;
        });
        
        const choice = prompt(reasonText + '\nEnter the number (1-5):');
        
        if (choice && choice >= 1 && choice <= 5) {
            reason = reasons[choice - 1];
            
            if (choice == 5) {
                const customReason = prompt('Please specify the reason:');
                if (customReason && customReason.trim()) {
                    reason = customReason.trim();
                } else {
                    return;
                }
            }
            
            // Send report to server
            fetch('<?php echo URLROOT; ?>/messages/reportConversation', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    conversation_id: conversationId,
                    reason: reason
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Conversation reported successfully. Our team will review it shortly.');
                } else {
                    alert('Failed to report conversation: ' + (data.message || 'Unknown error'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while reporting the conversation.');
            });
        }
        
        // Hide dropdown
        document.getElementById(`dropdown-${conversationId}`).style.display = 'none';
    }
    
    // Delete conversation
    function deleteConversation(conversationId, userName) {
        if (confirm(`Are you sure you want to delete your conversation with ${userName}? This action cannot be undone.`)) {
            // Send delete request to server
            fetch('<?php echo URLROOT; ?>/messages/deleteConversation', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    conversation_id: conversationId
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Remove conversation from UI
                    const conversationElement = document.querySelector(`[onclick*="openConversation(${conversationId})"]`);
                    if (conversationElement) {
                        conversationElement.remove();
                    }
                    alert('Conversation deleted successfully.');
                } else {
                    alert('Failed to delete conversation: ' + (data.message || 'Unknown error'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while deleting the conversation.');
            });
        }
        
        // Hide dropdown
        document.getElementById(`dropdown-${conversationId}`).style.display = 'none';
    }
</script>
<?php $scripts = ob_get_clean();?>

<?php require APPROOT . '/views/layouts/threeColumnMiniLayout.php'; ?>
