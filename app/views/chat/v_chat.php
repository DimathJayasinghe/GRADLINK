<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>GRADLINK</title>
    <link rel="icon" type="image/x-icon" href="<?php echo URLROOT?>/img/favicon_white.png" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="<?php echo URLROOT; ?>/css/chat/chat.css">
</head>
<body>
  <div class="x-app">
    
    <!-- Left Navigation Sidebar -->
    <nav class="x-nav">
      <!-- GRADLINK Logo -->
      <div class="x-logo">
        <img src="<?php echo URLROOT; ?>/img/logo_white.png" alt="GRADLINK" class="gradlink-logo">
        <span class="gradlink-text">GRADLINK</span>
      </div>

      <!-- Navigation Items -->
      <div class="x-nav-items">
        <a href="<?php echo URLROOT; ?>" class="x-nav-item">
          <i class="fas fa-home x-nav-icon"></i>
          <span>Home</span>
        </a>
        
        <a href="#" class="x-nav-item">
          <i class="fas fa-search x-nav-icon"></i>
          <span>Explore</span>
        </a>

        <a href="#" class="x-nav-item">
          <i class="fas fa-bell x-nav-icon"></i>
          <span>Notifications</span>
        </a>

        <a href="#" class="x-nav-item x-nav-active">
          <i class="fas fa-envelope x-nav-icon"></i>
          <span>Messages</span>
        </a>

        <a href="#" class="x-nav-item">
          <i class="fas fa-user x-nav-icon"></i>
          <span>Profile</span>
        </a>

        <a href="#" class="x-nav-item">
          <i class="fas fa-hand-holding-heart x-nav-icon"></i>
          <span>Fundraisers</span>
        </a>

        <a href="#" class="x-nav-item">
          <i class="fas fa-clipboard-list x-nav-icon"></i>
          <span>Post Requests</span>
        </a>

        <a href="#" class="x-nav-item">
          <i class="fas fa-calendar-alt x-nav-icon"></i>
          <span>Calender</span>
        </a>

        <a href="#" class="x-nav-item">
          <i class="fas fa-cog x-nav-icon"></i>
          <span>Settings</span>
        </a>

        <a href="#" class="x-nav-item">
          <i class="fas fa-sign-out-alt x-nav-icon"></i>
          <span>Logout</span>
        </a>
      </div>
    </nav>

    <!-- Chat Section -->
    <main class="x-chat-main">
      <!-- Chat Header -->
      <header class="x-chat-header">
        <h1>Chat</h1>
        <div class="x-chat-controls">
          <button class="x-chat-btn">
            <i class="fas fa-cog x-icon-small"></i>
          </button>
        </div>
      </header>

      <!-- Chat Content -->
      <div class="x-chat-content">
        <!-- Search Bar -->
        <div class="x-search-container">
          <div class="x-search-input">
            <i class="fas fa-search x-search-icon"></i>
            <input type="text" placeholder="Search" />
          </div>
        </div>

        <!-- Filter Tabs -->
        <div class="x-filter-tabs">
          <button class="x-filter-tab x-filter-active">All</button>
          <button class="x-filter-tab">Groups</button>
          <button class="x-filter-tab">Batch</button>
        </div>

        <!-- Empty State -->
        <div class="x-empty-state">
          <div class="x-empty-icon">
            <i class="fas fa-envelope x-empty-mail-icon"></i>
          </div>
          <h2 class="x-empty-title">Empty inbox</h2>
          <p class="x-empty-subtitle">Message someone</p>
        </div>
      </div>
    </main>

    <!-- Chat Selection Area -->
    <div class="x-chat-selection">
      <div class="x-select-container">
        <div class="x-select-icon">
          <i class="fas fa-comments x-select-chat-icon"></i>
        </div>
        <h2 class="x-select-title">Select a chat</h2>
        <p class="x-select-subtitle">Choose from your existing conversations,<br>or start a new one.</p>
        <button class="x-new-chat-btn">New chat</button>
      </div>
    </div>
  </div>

<script src="<?php echo URLROOT; ?>/js/chat/chat.js"></script>
</body>
</html>
