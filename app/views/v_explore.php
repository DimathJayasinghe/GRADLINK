<?php ob_start() ?>
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/style.css">
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/components/postCardStyles.css">
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/components/postCardShowMore.css">
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/mainfeed_styles.css">
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/explore_styles.css">
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/components/rightSidebarStyles.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<?php $styles = ob_get_clean(); ?>


<?php ob_start() ?>
<?php
$leftside_buttons = [
    ['icon' => 'home', 'label' => 'Home', 'onclick' => "window.location.href='" . URLROOT . "/mainfeed'"],
    ['icon' => 'search', 'label' => 'Explore', 'onclick' => "window.location.href='" . URLROOT . "/explore'", 'active' => true],
    ['icon' => 'bell', 'label' => 'Notifications', 'onclick' => "NotificationModal()", 'require' => APPROOT . '/views/inc/commponents/notification_pop_up.php', 'badge' => true],
    ['icon' => 'envelope', 'label' => 'Messages', 'onclick' => "window.location.href='" . URLROOT . "/messages'"],
    ['icon' => 'user', 'label' => 'Profile', 'onclick' => "window.location.href='" . URLROOT . "/profile?userid=" . $_SESSION['user_id'] . "'"],
    ['icon' => 'hand-holding-heart', 'label' => 'Fundraisers', 'onclick' => "window.location.href='" . URLROOT . "/fundraiser'"],
    ['icon' => 'clipboard-list', 'label' => 'Event Requests', 'onclick' => "window.location.href='" . URLROOT . "/eventrequest/'"],
    ['icon' => 'calendar-alt', 'label' => 'Calender', 'onclick' => "window.location.href='" . URLROOT . "/calender'"],
];
//  new portal to approve new alumnis only available for special alumnis
if ($_SESSION['special_alumni']) {
    $leftside_buttons[] = [
        'icon' => 'user-check',
        'label' => 'Approve Alumni',
        'onclick' => "window.location.href='" . URLROOT . "/alumni/approve'"
    ];
};
$leftside_buttons[] = ['icon' => 'cog', 'label' => 'Settings', 'onclick' => "window.location.href='" . URLROOT . "/settings'"];
require APPROOT . '/views/inc/commponents/leftSideBar.php'; ?>
require APPROOT . '/views/inc/commponents/leftSideBar.php';
?>
<?php $leftsidebar = ob_get_clean(); ?>

<?php ob_start() ?>
<div class="main-content">
    <div class="explore-container">
        <div class="search-section">
            <form method="GET" action="<?php echo URLROOT; ?>/explore" class="search-bar">
                <input type="text" name="q" class="search-input" placeholder="Search for posts, people, events..."
                    value="<?php echo $data['query']; ?>" autocomplete="off">
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
                <a href="<?php echo URLROOT; ?>/explore<?php echo !empty($data['query']) ? '?q=' . urlencode($data['query']) . '&filter=fundraisers' : ''; ?>"
                    class="filter-tab <?php echo ($data['filter'] === 'fundraisers') ? 'active' : ''; ?>">
                    Fundraisers
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
                                    <a href="<?php echo URLROOT; ?>/explore?q=<?php echo urlencode($data['query']); ?>&filter=users"
                                        class="view-all">View all</a>
                                <?php endif; ?>
                            </div>
                            <div class="user-cards" id="users-list">
                                <?php foreach ($users as $user): ?>
                                    <div class="user-card" data-user-id="<?php echo $user->id; ?>" onclick="window.location.href='<?php echo URLROOT; ?>/profile?userid=<?php echo $user->id; ?>';">
                                        <img src="<?php echo URLROOT; ?>/media/profile/<?php echo htmlspecialchars($user->profile_image ?? 'default.jpg'); ?>"
                                            alt="<?php echo htmlspecialchars($user->name); ?>" 
                                            class="user-avatar"
                                            onerror="this.onerror=null;this.src='<?php echo URLROOT; ?>/media/profile/default.jpg';">
                                        <div class="user-info">
                                            <div class="user-name" title="<?php echo htmlspecialchars($user->name); ?>">
                                                <?php echo htmlspecialchars($user->name); ?>
                                            </div>
                                            <span class="user-role <?php echo $user->role; ?>">
                                                <?php echo ucfirst($user->role); ?>
                                                <?php echo $user->role === 'alumni' ? ' ★' : ''; ?>
                                            </span>
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
                                    <a href="<?php echo URLROOT; ?>/explore?q=<?php echo urlencode($data['query']); ?>&filter=events"
                                        class="view-all">View all</a>
                                <?php endif; ?>
                            </div>
                            <div class="event-cards" id="events-list">
                                <?php foreach ($data['results']['events'] as $event): ?>
                                    <div class="event-card" data-event-id="<?php echo $event->id; ?>">
                                        <?php if (!empty($event->attachment_image)): ?>
                                            <div class="event-image"
                                                style="background-image: url('<?php echo URLROOT; ?>/media/event/<?php echo htmlspecialchars($event->attachment_image); ?>')">
                                                <div class="event-date">
                                                    <div class="date-day"><?php echo date('d', strtotime($event->start_datetime)); ?></div>
                                                    <div class="date-month"><?php echo date('M', strtotime($event->start_datetime)); ?></div>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                        <div class="event-content">
                                            <h3 class="event-title">
                                                <a href="<?php echo URLROOT; ?>/calender/show/<?php echo $event->id; ?>">
                                                    <?php echo htmlspecialchars($event->title); ?>
                                                </a>
                                            </h3>
                                            <p class="event-datetime">
                                                <i class="fas fa-clock"></i>
                                                <?php echo date('M d, Y - g:i A', strtotime($event->start_datetime)); ?>
                                            </p>
                                            <?php if (!empty($event->venue)): ?>
                                                <p class="event-location">
                                                    <i class="fas fa-map-marker-alt"></i>
                                                    <?php echo htmlspecialchars($event->venue); ?>
                                                </p>
                                            <?php endif; ?>
                                            <?php if (!empty($event->description)): ?>
                                                <p class="event-description">
                                                    <?php echo htmlspecialchars(substr($event->description, 0, 150)) . (strlen($event->description) > 150 ? '...' : ''); ?>
                                                </p>
                                            <?php endif; ?>
                                        </div>
                                        <div class="event-footer">
                                            <div class="event-organizer">
                                                <span class="organizer-name">
                                                    <i class="fas fa-user"></i>
                                                    <?php echo htmlspecialchars($event->organizer_name ?? 'Unknown'); ?>
                                                </span>
                                            </div>
                                            <div class="event-actions">
                                                <?php if (isset($event->is_bookmarked) && $event->is_bookmarked): ?>
                                                    <button class="bookmark-btn bookmarked" data-event-id="<?php echo $event->id; ?>" onclick="explorer.toggleBookmark(<?php echo $event->id; ?>)">
                                                        <i class="fas fa-bookmark"></i>
                                                    </button>
                                                <?php else: ?>
                                                    <button class="bookmark-btn" data-event-id="<?php echo $event->id; ?>" onclick="explorer.toggleBookmark(<?php echo $event->id; ?>)">
                                                        <i class="far fa-bookmark"></i>
                                                    </button>
                                                <?php endif; ?>
                                                <a href="<?php echo URLROOT; ?>/calender/show/<?php echo $event->id; ?>" class="details-btn">
                                                    Details
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>

                <?php if ($data['filter'] === 'all' || $data['filter'] === 'fundraisers'): ?>
                    <?php if (isset($data['results']['fundraisers']) && !empty($data['results']['fundraisers'])): ?>
                        <div class="results-section">
                            <div class="results-header">
                                <h2>Fundraisers</h2>
                                <?php if ($data['filter'] === 'all'): ?>
                                    <a href="<?php echo URLROOT; ?>/explore?q=<?php echo urlencode($data['query']); ?>&filter=fundraisers"
                                        class="view-all">View all</a>
                                <?php endif; ?>
                            </div>
                            <div class="fundraiser-cards" id="fundraisers-list">
                                <?php foreach ($data['results']['fundraisers'] as $fundraiser): ?>
                                    <?php 
                                        $percentage = ($fundraiser->raised_amount / $fundraiser->target_amount) * 100;
                                        $daysLeft = $fundraiser->days_left;
                                    ?>
                                    <div class="fundraiser-card" data-fundraiser-id="<?php echo $fundraiser->id; ?>">
                                        <div class="fundraiser-content">
                                            <h3 class="fundraiser-title">
                                                <a href="<?php echo URLROOT; ?>/fundraiser/show/<?php echo $fundraiser->id; ?>">
                                                    <?php echo htmlspecialchars($fundraiser->title); ?>
                                                </a>
                                            </h3>
                                            <?php if (!empty($fundraiser->club_name)): ?>
                                                <p class="fundraiser-club">
                                                    <i class="fas fa-users"></i>
                                                    <?php echo htmlspecialchars($fundraiser->club_name); ?>
                                                </p>
                                            <?php endif; ?>
                                            <?php if (!empty($fundraiser->description)): ?>
                                                <p class="fundraiser-description">
                                                    <?php echo htmlspecialchars(substr($fundraiser->description, 0, 120)) . (strlen($fundraiser->description) > 120 ? '...' : ''); ?>
                                                </p>
                                            <?php endif; ?>
                                            <div class="fundraiser-progress">
                                                <div class="progress-info">
                                                    <span class="amount-raised">Rs.<?php echo number_format($fundraiser->raised_amount, 0); ?></span>
                                                    <span class="amount-target">of Rs.<?php echo number_format($fundraiser->target_amount, 0); ?></span>
                                                </div>
                                                <div class="progress-bar-container">
                                                    <div class="progress-bar-fill" style="width: <?php echo min($percentage, 100); ?>%"></div>
                                                </div>
                                                <div class="progress-stats">
                                                    <span class="progress-percent"><?php echo number_format($percentage, 1); ?>% funded</span>
                                                    <span class="days-left"><?php echo $daysLeft; ?> days left</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="fundraiser-footer">
                                            <a href="<?php echo URLROOT; ?>/fundraiser/show/<?php echo $fundraiser->id; ?>" class="donate-btn">
                                                <i class="fas fa-hand-holding-heart"></i> Donate Now
                                            </a>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>

                <?php if ($data['filter'] === 'all' || $data['filter'] === 'posts'): ?>
                    <?php if (isset($data['results']['posts']) && !empty($data['results']['posts'])): ?>
                        <div class="results-section">
                            <div class="results-header">
                                <h2>Posts</h2>
                                <?php if ($data['filter'] === 'all'): ?>
                                    <a href="<?php echo URLROOT; ?>/explore?q=<?php echo urlencode($data['query']); ?>&filter=posts"
                                        class="view-all">View all</a>
                                <?php endif; ?>
                            </div>
                            <div class="post-cards" id="posts-feed">
                                <?php foreach ($data['results']['posts'] as $p): ?>
                                    <div class="post-card" data-post-id="<?php echo $p->id; ?>">
                                        <div class="post-card-header">
                                            <img src="<?php echo URLROOT; ?>/media/profile/<?php echo htmlspecialchars($p->profile_image ?? 'default.jpg'); ?>"
                                                alt="<?php echo htmlspecialchars($p->name); ?>" 
                                                class="post-author-avatar"
                                                onerror="this.onerror=null;this.src='<?php echo URLROOT; ?>/media/profile/default.jpg';"
                                                onclick="window.location.href='<?php echo URLROOT; ?>/profile?userid=<?php echo $p->user_id; ?>';">
                                            <div class="post-author-info">
                                                <div class="post-author-name" onclick="window.location.href='<?php echo URLROOT; ?>/profile?userid=<?php echo $p->user_id; ?>';">
                                                    <?php echo htmlspecialchars($p->name); ?>
                                                    <?php if (isset($p->role) && $p->role === 'alumni'): ?>
                                                        <span class="alumni-badge">★</span>
                                                    <?php endif; ?>
                                                </div>
                                                <div class="post-meta">
                                                    <span class="post-handle">@user<?php echo $p->user_id; ?></span>
                                                    <span class="post-time"><?php echo date('M d, Y', strtotime($p->created_at)); ?></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="post-card-content">
                                            <p class="post-text"><?php echo nl2br(htmlspecialchars($p->content)); ?></p>
                                            <?php if (!empty($p->image)): ?>
                                                <div class="post-image-container">
                                                    <img src="<?php echo URLROOT; ?>/media/post/<?php echo htmlspecialchars($p->image); ?>"
                                                        alt="Post image" 
                                                        class="post-image"
                                                        onerror="this.parentElement.style.display='none';">
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                        <div class="post-card-footer">
                                            <div class="post-stats">
                                                <button class="post-action-btn like-btn <?php echo !empty($p->is_liked) ? 'liked' : ''; ?>" 
                                                        data-post-id="<?php echo $p->id; ?>">
                                                    <i class="<?php echo !empty($p->is_liked) ? 'fas' : 'far'; ?> fa-heart"></i>
                                                    <span class="count like-count"><?php echo $p->likes; ?></span>
                                                </button>
                                                <button class="post-action-btn comment-btn" data-post-id="<?php echo $p->id; ?>">
                                                    <i class="far fa-comment"></i>
                                                    <span class="count comment-count"><?php echo $p->comments; ?></span>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="pc-comments" style="display:none;border-top:1px solid var(--border);margin-top:10px;padding-top:8px">
                                            <div class="pc-comments-list" style="max-height:200px;overflow:auto;color:var(--text-secondary)"></div>
                                            <div style="display:flex;gap:6px;margin-top:6px">
                                                <input type="text" class="pc-comment-input" placeholder="Add a comment" style="flex:1;padding:6px;border:1px solid var(--border);background:var(--bg);color:var(--text);border-radius:4px" />
                                                <button class="pc-comment-send" style="padding:6px 10px;background:var(--link);color:var(--text);border:none;border-radius:4px;cursor:pointer">Send</button>
                                            </div>
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
<?php $scripts = ob_get_clean(); ?>
<script type="module" src="<?php echo URLROOT; ?>/js/explorer.js"></script>
<?php require APPROOT . '/views/layouts/threeColumnLayout.php'; ?>