<link rel="shortcut icon" href="<?php echo URLROOT ?>/img/favicon_white.png" type="image/x-icon">
<?php
// This is a template adapter for request dashboards to use threeColumnMiniLayout
// It helps maintain the original sidebar structure while integrating with the three-column layout
// Start capturing leftsidebar content for standard sidebar
ob_start();
?>
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

    // Define which sidebar item should be active based on current page
    $is_fundraiser = strpos($_SERVER['REQUEST_URI'], 'fundraiser') !== false;
    $is_eventrequest = strpos($_SERVER['REQUEST_URI'], 'eventrequest') !== false;

    $leftside_buttons = [
        ['icon' => 'home', 'label' => 'Home', 'onclick' => "window.location.href='" . URLROOT . "/mainfeed'" ],
        ['icon' => 'search', 'label' => 'Explore', 'onclick' => "window.location.href='" . URLROOT . "/explore'"],
        ['icon' => 'bell', 'label' => 'Notifications', 'onclick' => "NotificationModal()", 'require' => APPROOT . '/views/inc/commponents/notification_pop_up.php', 'notifications' => $notifications],
        ['icon' => 'envelope', 'label' => 'Messages', 'onclick' => "window.location.href='" . URLROOT . "/messages'"],
        ['icon' => 'user', 'label' => 'Profile' , 'onclick' => "window.location.href='" . URLROOT . "/profile?userid=".$_SESSION['user_id'] . "'"],
        // icon for fundraiser
        ['icon' => 'hand-holding-heart', 'label' => 'Fundraisers', 'onclick' => "window.location.href='" . URLROOT . "/fundraiser'"],
        //icon for event requests
        // ['icon' => 'clipboard-list', 'label' => 'event Requests', 'onclick' => "window.location.href='" . URLROOT . "/eventrequest/'", 'active' => $is_eventrequest],
        ['icon' => 'clipboard-list', 'label' => 'Event Requests', 'onclick' => "window.location.href='" . URLROOT . "/eventrequest/'"],
        ['icon' => 'calendar-alt', 'label' => 'Calender', 'onclick' => "window.location.href='" . URLROOT . "/calender'",'active' => true],
    ];
    //  new portal to approve new alumnis only available for special alumnis
    if ($_SESSION['special_alumni']){
        $leftside_buttons[] = [
            'icon'=>'user-check','label'=>'Approve Alumni','onclick'=>"window.location.href='".URLROOT."/alumni/approve'"
        ];
    };
    $leftside_buttons[] = ['icon' => 'cog', 'label' => 'Settings', 'onclick' => "window.location.href='" . URLROOT . "/settings'"];
    require APPROOT . '/views/inc/commponents/leftSideBar.php'; ?>
    
?>
<?php
// Save leftsidebar content
$leftsidebar = ob_get_clean();

// Start capturing center content for dashboard navigation options
ob_start();
?>
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/req_dashboard/dashboard_layout.css">
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/req_dashboard/adapter_layout.css">
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/req_dashboard/fundraise_form_fixes.css">

<style>
    /* Style the dashboard navigation similar to settings categories */
    .dashboard-categories {
        display: flex;
        flex-direction: column;
        height: 100%;
        /* border-right: 1px solid var(--border); */
        background-color: var(--bg-alt);
    }
    
    .dashboard-categories .category-title {
        padding: 20px 16px 10px;
        font-weight: 600;
        color: var(--text);
        font-size: 18px;
    }
    
    .dashboard-categories ul {
        list-style: none;
        /* padding: 20px 0 0 0; Added top padding for headroom */
        margin: 0;
    }
    
    .dashboard-categories li {
        padding: 10px 16px;
        display: flex;
        align-items: center;
        cursor: pointer;
        color: var(--muted);
        transition: all 0.2s ease;
    }
    
    .dashboard-categories li:hover {
        background: rgba(255, 255, 255, 0.05);
        color: var(--text);
    }
    
    .dashboard-categories li.active {
        background: rgba(158, 212, 220, 0.1);
        color: var(--link);
        border-left: 3px solid var(--link);
    }
    
    .dashboard-categories li i {
        margin-right: 10px;
        width: 20px;
        text-align: center;
    }
    
    .dashboard-categories li span {
        font-size: 14px;
    }
</style>

<div class="dashboard-categories">
    <ul>
        <?php foreach($sidebar_left as $link): ?>
        <li class="<?php if(isset($link['active']) && $link['active']){echo "active";}?>">
            <a href="<?php echo URLROOT.$link['url'] ?>">
                <i class="fas fa-<?php echo isset($link['icon']) ? $link['icon'] : 'layer-group'; ?>"></i>
                <span><?php echo $link['label'] ?></span>
            </a>
        </li>
        <?php endforeach; ?>
    </ul>
</div>
<?php
$center_topic = "Events";
// Save center content
$center_content = ob_get_clean();

// If no scripts are defined, create empty scripts
if (!isset($scripts)) {
    $scripts = '';
}

// The $content variable from dashboard views becomes the $rightsidebar in three-column layout
$rightsidebar = $content;
// Expose CSRF token for client-side JS
require_once APPROOT . '/helpers/Csrf.php';
$__gl_csrf = Csrf::getToken();
echo "<script>window.GL_CSRF_TOKEN = '" . htmlspecialchars($__gl_csrf, ENT_QUOTES) . "';</script>\n";
// Expose current user id for client-side checks
\SessionManager::ensureStarted();
echo "<script>window.GL_CURRENT_USER_ID = " . (isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : 'null') . ";</script>\n";

// Include reusable RSVP modal so it's available on calendar and detail pages
require_once APPROOT . '/views/inc/commponents/rsvp_modal.php';

// Expose a global helper to open the RSVP modal and handle confirm/cancel centrally
echo "<script>\n";
echo "(function(){\n";
echo "  window.__GL_openRsvpModal = function(eventId, title){\n";
echo "    var modal = document.getElementById('rsvp-modal'); if(!modal) return;\n";
echo "    var titleEl = document.getElementById('rsvp-event-title'); if(titleEl) titleEl.textContent = title || 'Event';\n";
echo "    modal.style.display = 'flex';\n";
echo "    window.__GL_rsvp_current = { eventId: eventId };\n";
echo "  };\n";
echo "  document.addEventListener('click', function(e){\n";
echo "    if(!document.getElementById('rsvp-modal')) return;\n";
echo "    if(e.target && e.target.id === 'rsvp-cancel'){ document.getElementById('rsvp-modal').style.display='none'; return; }\n";
echo "    if(e.target && e.target.id === 'rsvp-confirm'){\n";
echo "      var statusEl = document.querySelector('input[name=/'rsvp-status/']:checked');\n";
echo "      var status = statusEl ? statusEl.value : 'attending';\n";
echo "      var ev = window.__GL_rsvp_current && window.__GL_rsvp_current.eventId; if(!ev){ alert('Missing event id'); return; }\n";
echo "      fetch('" . URLROOT . "/calender/rsvp', { method: 'POST', credentials: 'same-origin', headers: Object.assign({'Content-Type':'application/json'}, (window.GL_CSRF_TOKEN?{'X-CSRF-Token':window.GL_CSRF_TOKEN}:{})), body: JSON.stringify({ event_id: Number(ev), status: status, guests: 0, csrf_token: (window.GL_CSRF_TOKEN||null) }) }).then(r=>r.json()).then(function(data){ if(data && data.ok){ document.getElementById('rsvp-modal').style.display='none'; if(window.__GL_onRsvpConfirmed) window.__GL_onRsvpConfirmed(ev); } else { alert('RSVP failed: ' + (data && data.error?data.error:'unknown')); } }).catch(function(err){ console.error('RSVP request failed', err); alert('Network error'); });\n";
echo "    }\n";
echo "  });\n";
echo "})();\n";
echo "</script>\n";

// Include the three-column layout template
require APPROOT . '/views/layouts/threeColumnMiniLayout.php';
?>