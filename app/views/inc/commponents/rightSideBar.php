<?php
// Check if URLROOT is defined, otherwise define it
if (!defined('URLROOT')) {
    // This is a fallback - ideally URLROOT should be defined in your config
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
    define('URLROOT', $protocol . $_SERVER['HTTP_HOST'] . '/GRADLINK');
}
?>

<!-- Right Sidebar Component -->
<div class="right-sidebar">
    <div class="search-container">
        <div class="calendar-search-icon">
            <i class="fas fa-calendar-alt"></i>
        </div>
        <input type="text" placeholder="Search Calendar">
    </div>

    <div class="card">
        <div class="card-title">Events ahead</div>

        <div class="event">
            <div class="event-category">Career Development · Trending </div>
            <div class="event-name">#ResumeWorkshop</div>
            <div class="event-date">Aug 23</div>
        </div>
        <div class="show-more">Show more</div>
    </div>

    <div class="card">
        <div class="card-title">Who to follow</div>

        
    </div>
</div>

<!-- Include required CSS if not already included -->
<?php if (!isset($rightSidebarStylesIncluded)): ?>
<style>
    /* Right Sidebar Styles */
    .right-sidebar {
        width: 350px;
        padding: 0 20px;
    }

    .search-container {
        background-color: var(--input);
        border-radius: 20px;
        padding: 10px 15px;
        margin-bottom: 15px;
        display: flex;
        align-items: center;
        position: relative;
    }

    .calendar-search-icon {
        position: relative;
        width: 18px;
        height: 18px;
        margin-right: 10px;
    }

    .calendar-search-icon i {
        color: var(--muted);
        font-size: 16px;
    }

    .search-container input {
        background: transparent;
        border: none;
        color: var(--text);
        width: 100%;
        outline: none;
        font-size: 15px;
    }

    .card {
        background-color: var(--card);
        border-radius: 15px;
        margin-bottom: 15px;
        overflow: hidden;
    }

    .card-title {
        font-size: 20px;
        font-weight: 700;
        padding: 15px;
        border-bottom: 1px solid var(--border);
    }

    .event {
        padding: 15px;
        border-bottom: 1px solid var(--border);
        cursor: pointer;
        transition: var(--transition);
    }

    .event:hover {
        background-color: rgba(15, 21, 24, 0.3);
    }

    .event-category {
        font-size: 13px;
        color: var(--muted);
    }

    .event-name{
        font-size: 15px;
        font-weight: 700;
        margin: 5px 0;
    }

    .event-date{
        font-size: 13px;
        color: var(--muted);
    }

    .show-more {
        color: var(--link);
        padding: 15px;
        cursor: pointer;
        transition: var(--transition);
    }

    .show-more:hover {
        background-color: rgba(158, 212, 220, 0.1);
    }

    .follow-card {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 15px;
        border-bottom: 1px solid var(--border);
        cursor: pointer;
        transition: var(--transition);
    }

    .follow-card:hover {
        background-color: rgba(15, 21, 24, 0.3);
    }

    .follow-info {
        display: flex;
        align-items: center;
    }

    .follow-details {
        margin-left: 10px;
    }

    .follow-name {
        font-weight: 700;
        font-size: 15px;
    }

    .follow-handle {
        color: var(--muted);
        font-size: 13px;
    }

    .follow-btn {
        background-color: var(--text);
        color: var(--bg);
        border: none;
        border-radius: 20px;
        padding: 5px 15px;
        font-weight: 700;
        cursor: pointer;
        transition: var(--transition);
    }

    .follow-btn:hover {
        background-color: var(--text-dark);
        transform: translateY(-2px);
    }
    
    /* Responsive styles for right sidebar */
    @media (max-width: 1200px) {
        .right-sidebar {
            width: 300px;
        }
    }
    
    @media (max-width: 1024px) {
        .right-sidebar {
            display: none; /* Hide on smaller screens */
        }
    }
</style>
<?php endif; ?>
