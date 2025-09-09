<?php ob_start() ?>
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/css/style.css">
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/css/components/postCardStyles.css">
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/css/components/postCardShowMore.css">
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/css/mainfeed_styles.css">
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/css/explore_styles.css">
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/css/components/rightSidebarStyles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<?php $styles = ob_get_clean(); ?>

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
]
?>

<?php ob_start() ?>
<?php
$leftside_buttons = [
    ['icon' => 'home', 'label' => 'Home', 'onclick' => "window.location.href='" . URLROOT . "/mainfeed'"],
    ['icon' => 'search', 'label' => 'Explore', 'onclick' => "window.location.href='" . URLROOT . "/explore'", 'active' => true],
    ['icon' => 'bell', 'label' => 'Notifications', 'onclick' => "NotificationModal()", 'require' => APPROOT . '/views/inc/commponents/notification_pop_up.php', 'notifications' => $notifications],
    ['icon' => 'envelope', 'label' => 'Messages', 'onclick' => "window.location.href='" . URLROOT . "/messages'"],
    ['icon' => 'hand-holding-heart', 'label' => 'Fundraisers', 'onclick' => "window.location.href='" . URLROOT . "/fundraiser'"],
    ['icon' => 'clipboard-list', 'label' => 'Post Requests', 'onclick' => "window.location.href='" . URLROOT . "/postrequest/'"],
    ['icon' => 'calendar-alt', 'label' => 'Calender', 'onclick' => "window.location.href='" . URLROOT . "/calender'"],
    ['icon' => 'cog', 'label' => 'Settings', 'onclick' => "window.location.href='" . URLROOT . "/settings'"]
];
require APPROOT . '/views/inc/commponents/leftSideBar.php'; 
?>
<?php $leftsidebar = ob_get_clean(); ?>

<?php ob_start() ?>
<div class="main-content">
    <div class="explore-container">
        <div class="search-section">
            <form method="GET" action="<?php echo URLROOT; ?>/explore" class="search-bar">
                <input 
                    type="text" 
                    name="q" 
                    class="search-input" 
                    placeholder="Search for posts, people, events..." 
                    value="<?php echo $data['query']; ?>"
                    autocomplete="off"
                >
                <button type="submit" class="search-button">
                    <i class="fas fa-search"></i> Search
                </button>
            </form>

            <div class="filter-tabs">
                <a href="<?php echo URLROOT; ?>/explore<?php echo !empty($data['query']) ? '?q=' . urlencode($data['query']) . '&filter=all' : ''; ?>" 
                    class="filter-tab <?php echo ($data['filter'] === 'all') ? 'active' : ''; ?>">
                    All
                </a>
                <a href="<?php echo URLROOT; ?>/explore<?php echo !empty($data['query']) ? '?q=' . urlencode($data['query']) . '&filter=posts' : ''; ?>" 
                    class="filter-tab <?php echo ($data['filter'] === 'posts') ? 'active' : ''; ?>">
                    Posts
                </a>
                <a href="<?php echo URLROOT; ?>/explore<?php echo !empty($data['query']) ? '?q=' . urlencode($data['query']) . '&filter=users' : ''; ?>" 
                    class="filter-tab <?php echo ($data['filter'] === 'users') ? 'active' : ''; ?>">
                    All Users
                </a>
                <a href="<?php echo URLROOT; ?>/explore<?php echo !empty($data['query']) ? '?q=' . urlencode($data['query']) . '&filter=alumni' : ''; ?>" 
                    class="filter-tab <?php echo ($data['filter'] === 'alumni') ? 'active' : ''; ?>">
                    Alumni
                </a>
                <a href="<?php echo URLROOT; ?>/explore<?php echo !empty($data['query']) ? '?q=' . urlencode($data['query']) . '&filter=undergrad' : ''; ?>" 
                    class="filter-tab <?php echo ($data['filter'] === 'undergrad') ? 'active' : ''; ?>">
                    Undergrads
                </a>
                <a href="<?php echo URLROOT; ?>/explore<?php echo !empty($data['query']) ? '?q=' . urlencode($data['query']) . '&filter=events' : ''; ?>" 
                    class="filter-tab <?php echo ($data['filter'] === 'events') ? 'active' : ''; ?>">
                    Events
                </a>
            </div>
        </div>

        <div class="search-results">
            <?php if (empty($data['query'])): ?>
                <div class="no-results">
                    <i class="fas fa-search" style="font-size: 48px; margin-bottom: 15px; opacity: 0.3;"></i>
                    <h3>Start exploring by searching for something</h3>
                    <p class="search-tip">
                        <i class="fas fa-lightbulb"></i> Try searching for posts, people, or events
                    </p>
                </div>
            <?php elseif (
                (isset($data['results']['posts']) && empty($data['results']['posts'])) && 
                (
                    (isset($data['results']['users']) && (
                        (is_array($data['results']['users']) && empty($data['results']['users'])) || 
                        (isset($data['results']['users']['all']) && empty($data['results']['users']['all']))
                    )) || 
                    !isset($data['results']['users'])
                ) && 
                (isset($data['results']['events']) && empty($data['results']['events'])) || 
                empty($data['results'])
            ): ?>
                <div class="no-results">
                    <i class="fas fa-search" style="font-size: 48px; margin-bottom: 15px; opacity: 0.3;"></i>
                    <h3>No results found for "<?php echo htmlspecialchars($data['query']); ?>"</h3>
                    <p class="search-tip">
                        <i class="fas fa-lightbulb"></i> Try different keywords or check different filters
                    </p>
                </div>
            <?php else: ?>
                <?php if ($data['filter'] === 'all' || $data['filter'] === 'posts'): ?>
                    <?php if (isset($data['results']['posts']) && !empty($data['results']['posts'])): ?>
                        <div class="results-section">
                            <div class="results-header">
                                <h2>Posts</h2>
                                <?php if ($data['filter'] === 'all'): ?>
                                    <a href="<?php echo URLROOT; ?>/explore?q=<?php echo urlencode($data['query']); ?>&filter=posts" class="view-all">View all</a>
                                <?php endif; ?>
                            </div>
                            <div class="feed" id="posts-feed">
                                <?php foreach($data['results']['posts'] as $p): ?>
                                    <post-card
                                        profile-img="<?php echo htmlspecialchars($p->profile_image); ?>"
                                        user-name="<?php echo htmlspecialchars($p->name . (isset($p->role) && $p->role==='alumni' ? ' ★' : '')); ?>"
                                        tag="@user<?php echo $p->user_id; ?>"
                                        post-time="<?php echo date('M d', strtotime($p->created_at)); ?>"
                                        post-content="<?php echo htmlspecialchars($p->content); ?>"
                                        post-img="<?php echo htmlspecialchars($p->image ?? ''); ?>"
                                        like-count="<?php echo $p->likes; ?>"
                                        cmnt-count="<?php echo $p->comments; ?>"
                                        liked="<?php echo !empty($p->liked)?1:0; ?>"
                                        post-id="<?php echo $p->id; ?>">
                                    </post-card>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>

                <?php if ($data['filter'] === 'all' || $data['filter'] === 'users' || $data['filter'] === 'alumni' || $data['filter'] === 'undergrad'): ?>
                    <?php 
                        $users = [];
                        if ($data['filter'] === 'all' && isset($data['results']['users']['all'])) {
                            $users = $data['results']['users']['all'];
                        } elseif ($data['filter'] === 'alumni' && isset($data['results']['users']['alumni'])) {
                            $users = $data['results']['users']['alumni'];
                        } elseif ($data['filter'] === 'undergrad' && isset($data['results']['users']['undergrad'])) {
                            $users = $data['results']['users']['undergrad'];
                        } elseif ($data['filter'] === 'users' && isset($data['results']['users'])) {
                            $users = $data['results']['users'];
                        }
                    ?>
                    
                    <?php if (!empty($users)): ?>
                        <div class="results-section">
                            <div class="results-header">
                                <h2>
                                    <?php 
                                        if ($data['filter'] === 'alumni') {
                                            echo 'Alumni';
                                        } elseif ($data['filter'] === 'undergrad') {
                                            echo 'Undergraduates';
                                        } else {
                                            echo 'People';
                                        }
                                    ?>
                                </h2>
                                <?php if ($data['filter'] === 'all'): ?>
                                    <a href="<?php echo URLROOT; ?>/explore?q=<?php echo urlencode($data['query']); ?>&filter=users" class="view-all">View all</a>
                                <?php endif; ?>
                            </div>
                            <div class="user-cards">
                                <?php foreach($users as $user): ?>
                                    <div class="user-card">
                                        <div class="user-card-header"></div>
                                        <div class="user-card-body">
                                            <img 
                                                src="<?php echo URLROOT; ?>/media/profile/<?php echo htmlspecialchars($user->profile_image ?? 'default.jpg'); ?>" 
                                                alt="<?php echo htmlspecialchars($user->name); ?>"
                                                class="user-avatar"
                                                onerror="this.onerror=null;this.src='<?php echo URLROOT; ?>/media/profile/default.jpg';"
                                            >
                                            <h3 class="user-name"><?php echo htmlspecialchars($user->name); ?></h3>
                                            <p class="user-handle">@user<?php echo $user->id; ?></p>
                                            <span class="user-role <?php echo $user->role; ?>">
                                                <?php echo ucfirst($user->role); ?>
                                                <?php echo $user->role === 'alumni' ? ' ★' : ''; ?>
                                            </span>
                                            <?php if (!empty($user->bio)): ?>
                                                <p class="user-bio"><?php echo htmlspecialchars($user->bio); ?></p>
                                            <?php endif; ?>
                                            <button class="connect-btn">Connect</button>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>

                <?php if ($data['filter'] === 'all' || $data['filter'] === 'events'): ?>
                    <?php if (isset($data['results']['events']) && !empty($data['results']['events'])): ?>
                        <div class="results-section">
                            <div class="results-header">
                                <h2>Events</h2>
                                <?php if ($data['filter'] === 'all'): ?>
                                    <a href="<?php echo URLROOT; ?>/explore?q=<?php echo urlencode($data['query']); ?>&filter=events" class="view-all">View all</a>
                                <?php endif; ?>
                            </div>
                            <div class="event-cards">
                                <?php foreach($data['results']['events'] as $event): ?>
                                    <div class="event-card">
                                        <div class="event-image" style="background-image: url('<?php echo URLROOT; ?>/media/events/<?php echo htmlspecialchars($event->image ?? 'default-event.jpg'); ?>')">
                                            <div class="event-date">
                                                <?php echo date('M d, Y', strtotime($event->event_date)); ?>
                                            </div>
                                        </div>
                                        <div class="event-content">
                                            <h3 class="event-title"><?php echo htmlspecialchars($event->title); ?></h3>
                                            <p class="event-location">
                                                <i class="fas fa-map-marker-alt"></i>
                                                <?php echo htmlspecialchars($event->location); ?>
                                            </p>
                                            <p class="event-description"><?php echo htmlspecialchars($event->description); ?></p>
                                        </div>
                                        <div class="event-footer">
                                            <div class="event-organizer">
                                                <img 
                                                    src="<?php echo URLROOT; ?>/media/profile/<?php echo htmlspecialchars($event->organizer_image ?? 'default.jpg'); ?>" 
                                                    alt="<?php echo htmlspecialchars($event->organizer_name); ?>"
                                                    class="organizer-avatar"
                                                >
                                                <span class="organizer-name">By <?php echo htmlspecialchars($event->organizer_name); ?></span>
                                            </div>
                                            <div class="event-action">Details</div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php $center_content = ob_get_clean(); ?>

<?php ob_start() ?>
<!-- Include the right sidebar component -->
<?php
require APPROOT . '/views/inc/commponents/rightSideBar.php';
?>
<?php $rightsidebar = ob_get_clean(); ?>

<?php ob_start() ?>
window.URLROOT = "<?php echo URLROOT; ?>";

document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.querySelector('.search-input');
    const searchResults = document.querySelector('.search-results');
    
    // Focus on the search input when the page loads if no query exists
    if (searchInput.value === '') {
        searchInput.focus();
    }
    
    // Enable realtime search after a short delay (optional)
    let searchTimeout;
    
    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        if (this.value.length > 2) {
            searchTimeout = setTimeout(() => {
                // Update the URL with the new search query without reloading the page
                const newUrl = new URL(window.location.href);
                newUrl.searchParams.set('q', this.value);
                history.replaceState(null, '', newUrl.toString());
                
                // AJAX search implementation (optional)
                fetch(`${URLROOT}/explore/search?q=${encodeURIComponent(this.value)}&filter=${currentFilter}`)
                    .then(response => response.json())
                    .then(data => {
                        // Update the UI with the new search results
                        updateSearchResults(data);
                    })
                    .catch(error => {
                        console.error('Search error:', error);
                    });
            }, 500); // 500ms delay to reduce API calls
        }
    });
});
<?php $scripts = ob_get_clean(); ?>

<?php require APPROOT . '/views/layouts/threeColumnLayout.php'; ?>