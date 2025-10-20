<?php ob_start(); ?>
<!-- Additional styles for the dashboard layout -->
<style>
    .cards-container {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 20px;
        padding-bottom: 20px;
    }
    
    .card {
        background: var(--card);
        border: 1px solid var(--border);
        border-radius: 5px;
        padding: 1.5rem;
        transition: transform 0.2s ease, box-shadow 0.2s ease, background 0.2s ease;
        text-decoration: none;
        color: var(--text);
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }
    
    .card:hover {
        box-shadow: 0 8px 18px rgba(0, 0, 0, 0.12);
        background: rgba(15, 21, 24, 0.5);
    }
    
    .card h3 {
        margin: 0 0 0.75rem;
        color: var(--link);
        font-size: 1.2rem;
        font-weight: 600;
    }
    
    .card p {
        margin: 0.25rem 0;
        font-size: 0.95rem;
        color: var(--muted);
    }
    
    .card .event-details {
        margin-top: 10px;
        padding-top: 10px;
        border-top: 1px solid var(--border);
    }
    
    .card .event-date-time {
        display: flex;
        justify-content: space-between;
        margin: 8px 0;
    }
    
    .card a {
        margin-top: auto;
        padding: 0.5rem 1rem;
        background: var(--link);
        color: #fff;
        border-radius: var(--radius-sm);
        text-align: center;
        text-decoration: none;
        font-weight: 600;
        transition: background 0.2s ease;
    }
    
    .card a:hover {
        background: #2563eb;
    }
    
    .rejected {
        color: var(--danger);
    }
    
    .pending {
        color: var(--warning);
    }
    
    .approved {
        color: var(--success);
    }
    
    .status-badge {
        display: inline-block;
        padding: 0.25rem 0.75rem;
        border-radius: var(--radius-full);
        font-weight: 600;
        font-size: 0.85rem;
        margin-left: 8px;
    }
    
    .status-pending {
        background: rgba(255, 193, 7, 0.2);
        color: #ffc107;
    }
    
    .status-approved {
        background: rgba(40, 167, 69, 0.2);
        color: #28a745;
    }
    
    .status-rejected {
        background: rgba(220, 53, 69, 0.2);
        color: #dc3545;
    }
    
    .event-icon {
        background: rgba(79, 70, 229, 0.1);
        color: #4f46e5;
        padding: 4px 8px;
        border-radius: 4px;
        font-size: 0.8rem;
        font-weight: 600;
        display: inline-block;
        margin-right: 5px;
    }
    
    .empty-state {
        text-align: center;
        padding: 3rem;
        background: rgba(255, 255, 255, 0.03);
        border-radius: var(--radius-md);
        margin: 2rem auto;
        max-width: 600px;
    }
    
    .empty-state p {
        color: var(--muted);
        margin-bottom: 1.5rem;
    }
    
    .empty-state .btn {
        display: inline-block;
        padding: 0.75rem 1.5rem;
        background: var(--link);
        color: #fff;
        border-radius: var(--radius-md);
        text-decoration: none;
        font-weight: 600;
    }
</style>
<?php $styles = ob_get_clean(); ?>

<?php
    $sidebar_left = [
        ['label' => 'View Calendar', 'url'=>'/calender', 'active'=>false, 'icon'=>'calendar'],
        ['label'=>'Bookmarked Events', 'url'=>'/calender/bookmarks', 'active'=>true, 'icon'=>'bookmark'],
    ]
?>

<?php ob_start(); ?>
<!-- Main content goes here -->
<div>
    <h2>Bookmarked Events</h2>
    <?php if(!empty($data['bookmarked_events'])): ?>
        <div class="cards-container">
            <?php foreach($data['bookmarked_events'] as $request): ?>
                <div class="card">
                    <h3>
                        <?php echo htmlspecialchars($request->title); ?>
                    </h3>
                    <p class="club-name">Club: <?php echo htmlspecialchars($request->club_name); ?></p>
                    <p class="description"><?php echo htmlspecialchars($request->description); ?></p>
                    <div class="event-details">
                        <p><span class="event-icon">EVENT Details:</span></p>
                        <!-- <p><span class="event-icon">EVENT</span> Details:</p> -->
                        <p class="event-date-time">
                            <span>Date: <?php echo date('M d, Y', strtotime($request->event_date)); ?></span>
                            <span>Time: <?php echo date('h:i A', strtotime($request->event_time)); ?></span>
                        </p>
                        <p class="event-venue">Venue: <?php echo htmlspecialchars($request->event_venue); ?></p>
                    </div>
                    
                    <div style="display: flex; gap: 10px; margin-top: 15px;">
                        <div style="flex:1; display:flex; align-items:center;">
                            <a href="<?php echo URLROOT; ?>/calender/show/<?php echo $request->event_id; ?>">View Details</a>
                        </div>
                        <div style="flex:1; display:flex; gap:8px;">
                            <button class="gl-remove-bookmark" data-event-id="<?php echo $request->event_id; ?>" style="flex:1; padding:0.5rem 1rem; background: #ec2424ff; color: #fff; border: none; border-radius: 4px; cursor: pointer;">Remove</button>
                                <noscript>
                                    <?php require_once APPROOT . '/helpers/Csrf.php'; ?>
                                    <form action="<?php echo URLROOT; ?>/calender/removeBookmark/<?php echo $request->event_id; ?>" method="post" style="flex:1; margin:0;">
                                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(Csrf::getToken(), ENT_QUOTES); ?>">
                                        <button type="submit" style="width:100%; padding:0.5rem 1rem; background: #ec2424ff; color: #fff; border: none; border-radius: 4px;">Remove</button>
                                    </form>
                                </noscript>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="empty-state">
            <h3>You haven't added any events to bookmarks!</h3>
            <p>Add your first event to here.</p>
        </div>
    <?php endif; ?>
</div>

<?php $content = ob_get_clean(); ?>
<?php require APPROOT . '/views/calender/v_layout_adapter.php';?>
<script>
document.addEventListener('click', function(e){
    var btn = e.target.closest && e.target.closest('.gl-remove-bookmark');
    if(!btn) return;
    e.preventDefault();
    var eventId = btn.getAttribute('data-event-id');
    if(!eventId) return;
    btn.disabled = true;
    btn.textContent = 'Removing...';

    var __gl_headers = { 'Content-Type': 'application/json' };
    if(typeof window !== 'undefined' && window.GL_CSRF_TOKEN){
        __gl_headers['X-CSRF-Token'] = window.GL_CSRF_TOKEN;
    }

    fetch('<?php echo URLROOT; ?>/calender/toggleBookmark', {
        method: 'POST',
        headers: __gl_headers,
        body: JSON.stringify({ event_id: parseInt(eventId,10) })
    }).then(function(res){
        return res.json();
    }).then(function(json){
        if(json && json.ok){
            // remove containing card
            var card = btn.closest('.card');
            if(card) card.remove();
            // if no cards left, reload the page to show empty state
            if(!document.querySelector('.cards-container .card')){
                window.location.reload();
            }
        } else {
            btn.disabled = false;
            btn.textContent = 'Remove';
            alert(json && json.error ? json.error : 'Could not remove bookmark');
        }
    }).catch(function(){
        btn.disabled = false;
        btn.textContent = 'Remove';
        alert('Network error while removing bookmark');
    });
});
</script>