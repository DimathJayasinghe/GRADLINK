<?php ob_start();?>
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/messages/messages.css">
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/messages/messages_sections.css">

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
            ['icon' => 'inbox', 'label' => 'All Messages', 'link' => URLROOT . '/messages/all', 'active' => $data['section'] === 'all'? true : false],
            ['icon' => 'users', 'label' => 'Groups', 'link' => URLROOT . '/messages/groups','active' => $data['section'] === 'groups'? true : false],
            ['icon' => 'graduation-cap', 'label' => 'Batch', 'link' => URLROOT . '/messages/batch','active' => $data['section'] === 'batch'? true : false],
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
    // New Conversation Modal Functions
    function openNewConversationModal() {
        document.getElementById('newConversationModal').style.display = 'flex';
        loadAvailableUsers();
    }
    
    function closeNewConversationModal() {
        document.getElementById('newConversationModal').style.display = 'none';
    }
    
    // Load available users for new conversation
    function loadAvailableUsers() {
        const usersList = document.getElementById('usersList');
        // Sample users - this would come from an API call
        const sampleUsers = [
            { id: 4, name: 'John Doe', avatar: 'john.jpg', is_online: true },
            { id: 5, name: 'Emma Wilson', avatar: 'emma.jpg', is_online: false },
            { id: 6, name: 'Michael Brown', avatar: 'michael.jpg', is_online: true },
            { id: 7, name: 'Lisa Davis', avatar: 'lisa.jpg', is_online: false }
        ];
        
        usersList.innerHTML = '';
        sampleUsers.forEach(user => {
            const userItem = createUserItem(user);
            usersList.appendChild(userItem);
        });
    }
    
    // Create user item element
    function createUserItem(user) {
        const div = document.createElement('div');
        div.className = 'conversation-item';
        div.onclick = () => startConversation(user.id, user.name);
        
        const nameParts = user.name.split(' ');
        const displayName = user.name.length > 15 && nameParts.length > 1 
            ? nameParts[0] + ' ' + nameParts[nameParts.length - 1].charAt(0).toUpperCase() + '.'
            : user.name;
        
        div.innerHTML = `
            <div class="user-avatar">
                <img src="<?php echo URLROOT; ?>/media/profile/${user.avatar}" 
                     alt="${user.name}" class="avatar-img" 
                     onerror="this.src='<?php echo URLROOT; ?>/img/default-avatar.png'">
            </div>
            <div class="user-info">
                <h4 class="user-name">${displayName}</h4>
                <p class="last-message">Click to start conversation</p>
            </div>
        `;
        
        return div;
    }
    
    // Start new conversation
    function startConversation(userId, userName) {
        closeNewConversationModal();
        // Redirect to conversation or update current view
        window.location.href = `<?php echo URLROOT; ?>/messages/conversation/${userId}`;
    }
    
    // Open existing conversation
    function openConversation(conversationId) {
        // Update active conversation
        document.querySelectorAll('.conversation-item').forEach(item => {
            item.classList.remove('active');
        });
        event.currentTarget.classList.add('active');
        
        // Load conversation in right sidebar (you can implement this)
        console.log('Opening conversation:', conversationId);
    }
    
    // Search users function
    function searchUsers(query) {
        const userItems = document.querySelectorAll('#usersList .conversation-item');
        userItems.forEach(item => {
            const userName = item.querySelector('.user-name').textContent.toLowerCase();
            if (userName.includes(query.toLowerCase())) {
                item.style.display = 'flex';
            } else {
                item.style.display = 'none';
            }
        });
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
