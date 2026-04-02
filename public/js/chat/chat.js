// Switch between Direct, Batch, and Admin tabs
function switchTab(tabElement, tabType) {
  // Remove active class from all tabs
  document.querySelectorAll(".tab").forEach(tab => tab.classList.remove("active"));
  
  // Add active class to clicked tab
  tabElement.classList.add("active");
  
  // Update search placeholder based on tab
  const searchInput = document.querySelector(".search");
  switch(tabType) {
    case 'direct':
      searchInput.placeholder = "Search direct chats";
      break;
    case 'batch':
      searchInput.placeholder = "Search batch chats";
      break;
    case 'admin':
      searchInput.placeholder = "Search admin chats";
      break;
  }
  
  // Optional: Filter chat items based on tab type
  // This could be expanded later to show different chat lists per tab
}

// Select a chat from sidebar
function selectChat(el) {
  document.querySelectorAll(".chat-item").forEach(item => item.classList.remove("active"));
  el.classList.add("active");

  // Update chat title
  const name = el.querySelector(".name").innerText;
  document.getElementById("chat-title").innerText = name;
  
  // Update chat role
  const role = el.querySelector(".role").innerText;
  document.querySelector(".chat-header .subtitle").innerText = role;
  
  // Get user ID from data attribute
  const userId = el.getAttribute('data-user-id');
  
  if (userId) {
    // Load profile data via AJAX
    loadUserProfile(userId);
  }
}

// Send message
function sendMessage() {
  const input = document.getElementById("msgInput");
  const text = input.value.trim();
  if (!text) return;

  const msgContainer = document.createElement("div");
  msgContainer.className = "message me";
  
  let messageHTML = '';
  
  // Check if this is a reply
  if (window.replyContext) {
    messageHTML = `
      <div class="bubble">
        <div class="reply-container">
          <div class="replied-message">
            <div class="replied-to">${window.replyContext.senderName}</div>
            <div class="replied-text">${window.replyContext.originalMessage}</div>
          </div>
        </div>
        <span class="bubble-text">${text}</span>
        <span class="menu-trigger" onclick="showMessageMenu(event)">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512">
            <path d="M352 160c12.9 0 24.6 7.8 29.6 19.8s2.2 25.7-6.9 34.9l-160 160c-12.5 12.5-32.8 12.5-45.3 0l-160-160c-9.2-9.2-11.9-22.9-6.9-34.9S19.1 160 32 160l320 0z"/>
          </svg>
        </span>
      </div>
    `;
    
    // Clear reply context after sending
    cancelReply();
  } else {
    messageHTML = `
      <div class="bubble">
        <span class="bubble-text">${text}</span>
        <span class="menu-trigger" onclick="showMessageMenu(event)">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512">
            <path d="M352 160c12.9 0 24.6 7.8 29.6 19.8s2.2 25.7-6.9 34.9l-160 160c-12.5 12.5-32.8 12.5-45.3 0l-160-160c-9.2-9.2-11.9-22.9-6.9-34.9S19.1 160 32 160l320 0z"/>
          </svg>
        </span>
      </div>
    `;
  }
  
  msgContainer.innerHTML = messageHTML;
  document.getElementById("messages").appendChild(msgContainer);
  input.value = "";

  // Auto-scroll to bottom
  const messages = document.getElementById("messages");
  messages.scrollTop = messages.scrollHeight;
}

// Add Enter key functionality
document.getElementById("msgInput").addEventListener("keypress", function(event) {
  if (event.key === "Enter") {
    event.preventDefault();
    sendMessage();
  }
});

// Function to load user profile data
function loadUserProfile(userId) {
  console.log('Loading profile for user ID:', userId);
  fetch(`/gradlink/chat/getProfile/${userId}`)
    .then(response => {
      console.log('Response status:', response.status);
      return response.json();
    })
    .then(data => {
      console.log('Profile data received:', data);
      if (data) {
        // Update profile information
        document.getElementById('profile-avatar').innerText = data.avatar;
        document.getElementById('profile-name').innerText = data.name;
        document.getElementById('profile-role').innerHTML = data.role + '<br><span id="profile-title">Professional</span>';
        document.getElementById('profile-bio').innerText = data.bio;
        
        // Update skills
        const skillsContainer = document.getElementById('profile-skills');
        skillsContainer.innerHTML = '';
        if (data.skills && data.skills.length > 0) {
          data.skills.forEach(skill => {
            const skillSpan = document.createElement('span');
            skillSpan.textContent = skill;
            skillsContainer.appendChild(skillSpan);
          });
        } else {
          skillsContainer.innerHTML = '<span>No skills listed</span>';
        }
      }
    })
    .catch(error => {
      console.error('Error loading profile:', error);
    });
}

// Message menu functions
let currentMessage = null;

function showMessageMenu(event) {
  // Prevent click from bubbling to message element
  event.stopPropagation();

  // Get the bubble element
  const bubble = event.target.closest('.bubble');
  currentMessage = event.target.closest('.message');

  // Check if this is the current user's message
  const isMyMessage = currentMessage.classList.contains('me');

  // Position the menu below the bubble
  const menu = document.getElementById('message-menu');
  const rect = bubble.getBoundingClientRect();

  // Clear existing menu items
  menu.innerHTML = '';

  // Add Reply option for all messages
  const replyItem = document.createElement('div');
  replyItem.className = 'menu-item';
  replyItem.textContent = 'Reply';
  replyItem.onclick = replyToMessage;
  menu.appendChild(replyItem);

  // Add Edit option only for my messages
  if (isMyMessage) {
    const editItem = document.createElement('div');
    editItem.className = 'menu-item';
    editItem.textContent = 'Edit';
    editItem.onclick = editMessage;
    menu.appendChild(editItem);
  }

  // Add Delete option for all messages
  const deleteItem = document.createElement('div');
  deleteItem.className = 'menu-item delete';
  deleteItem.textContent = 'Delete';
  deleteItem.onclick = deleteMessage;
  menu.appendChild(deleteItem);

  menu.style.display = 'flex';
  menu.style.top = `${rect.bottom + window.scrollY + 6}px`;
  menu.style.left = `${rect.left}px`;

  // Add click event to close menu when clicking elsewhere
  setTimeout(() => {
    document.addEventListener('click', closeMessageMenu);
  }, 0);
}

function closeMessageMenu() {
  const menu = document.getElementById('message-menu');
  menu.style.display = 'none';
  document.removeEventListener('click', closeMessageMenu);
}

function replyToMessage() {
  if (!currentMessage) return;
  
  const bubble = currentMessage.querySelector('.bubble');
  const bubbleText = bubble.querySelector('.bubble-text');
  const messageText = bubbleText ? bubbleText.textContent : bubble.textContent.replace('⋯', '').trim();
  
  // Get the sender's name (you can customize this based on your data)
  const isMyMessage = currentMessage.classList.contains('me');
  const senderName = isMyMessage ? 'You' : document.getElementById('chat-title').innerText;
  
  // Store the reply context globally so sendMessage can use it
  window.replyContext = {
    originalMessage: messageText,
    senderName: senderName
  };
  
  // Update the input area to show we're replying
  const chatInput = document.querySelector('.chat-input');
  const existingReplyBar = chatInput.querySelector('.reply-bar');
  
  // Remove existing reply bar if any
  if (existingReplyBar) {
    existingReplyBar.remove();
  }
  
  // Create reply bar
  const replyBar = document.createElement('div');
  replyBar.className = 'reply-bar';
  replyBar.innerHTML = `
    <div class="reply-info">
      <div class="reply-to">${senderName}</div>
      <div class="reply-preview">${messageText.length > 40 ? messageText.substring(0, 40) + '...' : messageText}</div>
    </div>
    <button class="cancel-reply" onclick="cancelReply()">✕</button>
  `;
  
  // Insert reply bar before the input
  chatInput.insertBefore(replyBar, chatInput.firstChild);
  
  // Focus the input
  const input = document.getElementById('msgInput');
  input.focus();
  input.placeholder = "Type your reply...";
  
  closeMessageMenu();
}

function editMessage() {
  if (!currentMessage) return;
  
  const bubble = currentMessage.querySelector('.bubble');
  const bubbleText = bubble.querySelector('.bubble-text');
  const messageText = bubbleText ? bubbleText.textContent.trim() : bubble.textContent.replace('⋯', '').trim();
  
  // Replace message with input field
  bubble.innerHTML = `
    <input type="text" value="${messageText}" class="edit-input" autofocus>
    <div style="margin-top: 4px;">
      <button onclick="saveEdit(this)">Save</button>
      <button onclick="cancelEdit(this, '${messageText.replace(/'/g, "/'")}')">Cancel</button>
    </div>
  `;
  
  // Focus the input
  const input = bubble.querySelector('.edit-input');
  input.focus();
  input.setSelectionRange(input.value.length, input.value.length);
  
  // Handle Enter key to save
  input.addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
      saveEdit(bubble.querySelector('button'));
    }
  });
  
  // Handle Escape key to cancel
  input.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
      cancelEdit(bubble.querySelector('button:last-child'), messageText);
    }
  });
  
  closeMessageMenu();
}

function saveEdit(button) {
  const bubble = button.closest('.bubble');
  const input = bubble.querySelector('.edit-input');
  const newText = input.value.trim();
  
  if (newText) {
    // Check if this bubble has a reply container
    const replyContainer = bubble.querySelector('.reply-container');
    let replyHTML = '';
    
    if (replyContainer) {
      replyHTML = replyContainer.outerHTML;
    }
    
    bubble.innerHTML = `
      ${replyHTML}
      <span class="bubble-text">${newText}</span>
      <span class="menu-trigger" onclick="showMessageMenu(event)"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512"><!--!Font Awesome Free v7.0.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.--><path d="M352 160c12.9 0 24.6 7.8 29.6 19.8s2.2 25.7-6.9 34.9l-160 160c-12.5 12.5-32.8 12.5-45.3 0l-160-160c-9.2-9.2-11.9-22.9-6.9-34.9S19.1 160 32 160l320 0z"/></svg></span>
    `;
  }
}

function cancelEdit(button, originalText) {
  const bubble = button.closest('.bubble');
  
  // Check if this bubble has a reply container
  const replyContainer = bubble.querySelector('.reply-container');
  let replyHTML = '';
  
  if (replyContainer) {
    replyHTML = replyContainer.outerHTML;
  }
  
  bubble.innerHTML = `
    ${replyHTML}
    <span class="bubble-text">${originalText}</span>
    <span class="menu-trigger" onclick="showMessageMenu(event)"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512"><!--!Font Awesome Free v7.0.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.--><path d="M352 160c12.9 0 24.6 7.8 29.6 19.8s2.2 25.7-6.9 34.9l-160 160c-12.5 12.5-32.8 12.5-45.3 0l-160-160c-9.2-9.2-11.9-22.9-6.9-34.9S19.1 160 32 160l320 0z"/></svg></span>
  `;
}

function deleteMessage() {
  if (!currentMessage) return;
  
  currentMessage.remove();
  closeMessageMenu();
}

function cancelReply() {
  const replyBar = document.querySelector('.reply-bar');
  if (replyBar) {
    replyBar.remove();
  }
  
  // Clear reply context
  window.replyContext = null;
  
  // Reset input placeholder
  const input = document.getElementById('msgInput');
  input.placeholder = "Type a message...";
}

// Add menu trigger to existing messages on page load
document.addEventListener('DOMContentLoaded', function() {
  document.querySelectorAll('.message .bubble').forEach(bubble => {
    if (!bubble.querySelector('.menu-trigger')) {
      // Check if bubble already has the new structure
      if (!bubble.querySelector('.bubble-text')) {
        // Wrap existing content in bubble-text span
        const existingContent = bubble.innerHTML;
        bubble.innerHTML = `<span class="bubble-text">${existingContent}</span>`;
      }
      
      // Add menu trigger
      bubble.innerHTML += '<span class="menu-trigger" onclick="showMessageMenu(event)"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512"><path d="M352 160c12.9 0 24.6 7.8 29.6 19.8s2.2 25.7-6.9 34.9l-160 160c-12.5 12.5-32.8 12.5-45.3 0l-160-160c-9.2-9.2-11.9-22.9-6.9-34.9S19.1 160 32 160l320 0z"/></svg></span>';
    }
  });
});
