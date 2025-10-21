<?php ob_start(); ?>
<!-- Additional styles for the dashboard layout -->
<style>
.details-header {
    margin-bottom: 1.5rem;
}

.details-container h2 {
    font-size: 1.6rem;
    font-weight: 700;
    margin-bottom: 0.75rem;
    color: var(--text);
}

.details-container .description {
    margin: 0.5rem 0 1rem;
    color: var(--muted);
    font-size: 0.9rem;
    line-height: 1.5;
    font-weight: normal;
}

.details-info {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: 1rem;
    margin-bottom: 1.5rem;
}

.details-info-item {
    background: rgba(255, 255, 255, 0.05);
    border-radius: var(--radius-md);
    padding: 1rem;
}

.info-label {
    font-size: 0.8rem;
    color: var(--muted);
    margin-bottom: 0.5rem;
    display: block;
}

.info-value {
    font-size: 1.2rem;
    font-weight: 600;
    color: var(--text);
}

.details-container p {
    margin: 0.35rem 0;
    font-size: 0.95rem;
    color: var(--text);
}

.event-info {
    margin-top: 1.5rem;
    background: rgba(255, 255, 255, 0.03);
    border-radius: var(--radius-md);
    padding: 1.5rem;
    border: 1px solid var(--border);
}

.event-info h3 {
    margin-top: 0;
    font-size: 1.2rem;
    font-weight: 600;
    color: var(--text);
}

.status-badge {
    display: inline-block;
    padding: 0.25rem 0.75rem;
    border-radius: var(--radius-full);
    font-weight: 600;
    font-size: 0.85rem;
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

.image-container {
    margin: 1.5rem 0;
    text-align: center;
}

.request-image {
    max-width: 100%;
    max-height: 400px;
    border-radius: var(--radius-md);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.no-image {
    background: rgba(255, 255, 255, 0.05);
    padding: 2rem;
    border-radius: var(--radius-md);
    text-align: center;
    color: var(--muted);
}

.action-buttons {
    display: flex;
    gap: 1rem;
    margin-top: 2rem;
}

.btn {
    padding: 0.5rem 1.5rem;
    border-radius: var(--radius-sm);
    font-weight: 600;
    cursor: pointer;
    transition: opacity 0.2s ease;
    text-decoration: none;
    text-align: center;
}

.btn:hover {
    opacity: 0.9;
}

.btn-primary {
    background: var(--link);
    color: #fff;
}

.btn-danger {
    background: var(--danger);
    color: #fff;
}

.btn-back {
    background: var(--card);
    color: var(--text);
    border: 1px solid var(--border);
}

@media (max-width: 768px) {
    .details-info {
        grid-template-columns: 1fr;
    }
    
    .details-container {
        padding: 1rem 0.5rem;
    }
    
    .action-buttons {
        flex-direction: column;
        gap: 0.5rem;
    }
}
</style>
<?php $styles = ob_get_clean(); ?>

<?php
    $sidebar_left = [
        ['label' => 'View Calendar', 'url'=>'/calender', 'active'=>false, 'icon'=>'calendar'],
        ['label'=>'Bookmarked Events', 'url'=>'/calender/bookmarks', 'active'=>false, 'icon'=>'bookmark'],
        ['label' => 'Details', 'url'=>'/calender/show/'.$data['event']->event_id, 'active'=>true, 'icon'=>'info-circle']
    ]
?>

<?php ob_start(); ?>
<div class="details-container">
    <?php
        $request = $data['event'];
    ?>

    <?php if($request): ?>
        <div class="details-header">
            <h2><?php echo htmlspecialchars($request->title); ?></h2>
            <p class="description"><?php echo htmlspecialchars($request->description); ?></p>
        </div>

        <div class="details-info">
            <div class="details-info-item">
                <span class="info-label">Club</span>
                <span class="info-value"><?php echo htmlspecialchars($request->club_name); ?></span>
            </div>
            
            <div class="details-info-item">
                <span class="info-label">Venue</span>
                <span class="info-value"><?php echo htmlspecialchars($request->event_venue); ?></span>
            </div>
            
            <div class="details-info-item">
                <span class="info-label">Date</span>
                <span class="info-value"><?php echo date('M d, Y', strtotime($request->event_date)); ?></span>
            </div>
            <div class="details-info-item">
                <span class="info-label">Time</span>
                <span class="info-value"><?php echo date('h:i A', strtotime($request->event_time)); ?></span>
            </div>
            
            <div class="details-info-item" style="padding: 0px;display: flex; align-items: center; justify-content: center;">
                <?php
                    $isBookmarked = !empty($request->bookmarked);
                ?>
                <button id="bookmark-btn" style="background-color:<?php echo $isBookmarked ? '#ec2424ff' : '#4caf50'; ?>" style="margin: 0px; " data-event-id="<?php echo htmlspecialchars($request->event_id); ?>">
                    <span class="btn" style="color: ffffff;" id="bookmark-label"><?php echo $isBookmarked ? 'Remove Bookmark' : 'Add Bookmark'; ?></span>
                </button>
            </div>
            <div class="details-info-item" style="padding: 0px;display: flex; align-items: center; justify-content: center;">
                <button id="detail-rsvp-btn" style="background-color:#4caf50; padding: 13px 60px" style="margin: 0px; " data-event-id="<?php echo htmlspecialchars($request->event_id); ?>">
                    <span class="btn" style="color:#ffffff">RSVP</span>
                </button>
            </div>
        </div>
        
        <div class="event-info">
            <!-- <h3>Event Details</h3> -->
            <?php if($request->attachment_image): ?>
                <div class="image-container">
                    <img src="<?php echo M_event_image::getUrl($request->attachment_image); ?>" alt="Event Image" class="request-image">
                </div>
            <?php else: ?>
                <div class="image-container">
                    <div class="no-image">No image attached to this request</div>
                </div>
            <?php endif; ?>
        </div>

        <!-- Attendees list -->
        <div class="event-info" style="margin-top:12px;">
            <h3 style="margin-top:0;">Attendees</h3>
            <?php
                $attendees = isset($data['attendees']) ? $data['attendees'] : [];
                if(!$attendees) {
                    echo '<p class="no-events">No attendees yet.</p>';
                } else {
                    echo '<ul style="list-style:none;padding:0;margin:0;">';
                    foreach($attendees as $a){
                        $name = htmlspecialchars($a->name ?? ($a->email ?? 'User'));
                        $guests = (int)($a->guests ?? 0);
                        echo '<li style="padding:8px 0;border-bottom:1px solid rgba(255,255,255,0.03);">';
                        echo '<strong>' . $name . '</strong>';
                        if($guests > 0) echo ' <span style="color:var(--muted);">(' . $guests . ' guests)</span>';
                        echo '</li>';
                    }
                    echo '</ul>';
                }
            ?>
        </div>


        <div class="action-buttons">
            <a href="<?php echo URLROOT; ?>/calender/" class="btn btn-back">Back to All Event Requests</a>
            <!-- <button id="detail-cancel-rsvp-btn" class="btn btn-danger" style="display:none;">Cancel My RSVP</button> -->
        </div>
        
    <?php else: ?>
        <p>Event request not found.</p>
        <div class="action-buttons">
            <a href="<?php echo URLROOT; ?>/calender/" class="btn btn-back">Back to All Event Requests</a>
        </div>
    <?php endif; ?>
</div>

<?php $content = ob_get_clean(); ?>
<?php
// Provide a small script to handle bookmark add/remove via AJAX
$scripts = <<<'JS'
document.addEventListener('DOMContentLoaded', function(){
    var btn = document.getElementById('bookmark-btn');
    if(!btn) return;
    var label = document.getElementById('bookmark-label');
    var eventId = btn.getAttribute('data-event-id');

    function postJson(path, body){
        return fetch(path, {
            method: 'POST',
            credentials: 'same-origin',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-Token': window.GL_CSRF_TOKEN || ''
            },
            body: JSON.stringify(body)
        }).then(function(resp){ return resp.json(); });
    }

    btn.addEventListener('click', function(e){
        e.preventDefault();
        var currently = btn.classList.contains('btn-primary');
        var target = currently ? '/calender/removeBookmarkAjax' : '/calender/addBookmark';
        postJson(target, { event_id: parseInt(eventId), csrf_token: window.GL_CSRF_TOKEN })
            .then(function(data){
                if(data && data.ok){
                    if(currently){
                        btn.classList.remove('btn-primary');
                        btn.classList.add('btn-danger');
                        label.textContent = 'Add Bookmark';
                    } else {
                        btn.classList.remove('btn-danger');
                        btn.classList.add('btn-primary');
                        label.textContent = 'Bookmarked';
                    }
                } else {
                    console.error('Bookmark action failed', data);
                    alert('Could not update bookmark: ' + (data && data.error ? data.error : 'Unknown error'));
                }
            }).catch(function(err){
                console.error('Request failed', err);
                alert('Network error while updating bookmark');
            });
    });
});
JS;

require APPROOT . '/views/calender/v_layout_adapter.php';
?>
<script>
document.addEventListener('DOMContentLoaded', function(){
    var detailRsvp = document.getElementById('detail-rsvp-btn');
    var detailCancel = document.getElementById('detail-cancel-rsvp-btn');
    var evtId = detailRsvp ? detailRsvp.getAttribute('data-event-id') : null;
    if(detailRsvp){
        detailRsvp.addEventListener('click', function(e){
            // open global modal
            window.__GL_openRsvpModal && window.__GL_openRsvpModal(Number(evtId), '<?php echo addslashes(htmlspecialchars($request->title)); ?>');
        });
    }

    // Expose handler to update attendees after RSVP completes
    window.__GL_onRsvpConfirmed = function(ev){
        // refresh attendees list via AJAX
        fetch('<?php echo URLROOT; ?>/calender/attendees?event_id=' + encodeURIComponent(ev), { credentials: 'same-origin' }).then(r=>r.json()).then(function(data){
            if(data && data.ok){
                // update attendees list in DOM
                var container = document.querySelector('.event-info + .event-info');
                if(container){
                    var html = '<h3 style="margin-top:0;">Attendees</h3><ul style="list-style:none;padding:0;margin:0;">';
                    data.attendees.forEach(function(a){
                        var name = a.name || a.email || 'User';
                        html += '<li style="padding:8px 0;border-bottom:1px solid rgba(255,255,255,0.03);"><strong>'+name+'</strong>' + (a.guests>0?(' <span style="color:var(--muted);">('+a.guests+' guests)</span>'):'') + '</li>';
                    });
                    html += '</ul>';
                    container.innerHTML = html;
                }
                // show/hide cancel button if current user is in the list
                fetch('<?php echo URLROOT; ?>/auth/current_user_id.json', { credentials: 'same-origin' }).then(r=>r.json()).then(function(j){
                    var uid = j && j.user_id;
                    var me = data.attendees.find(function(a){ return Number(a.user_id) === Number(uid); });
                    if(me){ detailCancel.style.display = 'inline-flex'; } else { detailCancel.style.display = 'none'; }
                }).catch(()=>{});
            }
        }).catch(err=>console.error('attendees fetch failed',err));
    };

    // Wire cancel button to call cancel endpoint
    if(detailCancel){
        detailCancel.addEventListener('click', function(){
            if(!confirm('Cancel your RSVP?')) return;
            fetch('<?php echo URLROOT; ?>/calender/cancelRsvp', { method: 'POST', credentials: 'same-origin', headers: Object.assign({'Content-Type':'application/json'}, (window.GL_CSRF_TOKEN?{'X-CSRF-Token':window.GL_CSRF_TOKEN}:{})), body: JSON.stringify({ event_id: Number(evtId), csrf_token: (window.GL_CSRF_TOKEN||null) }) }).then(r=>r.json()).then(function(data){
                if(data && data.ok){
                    // refresh attendees UI
                    window.__GL_onRsvpConfirmed && window.__GL_onRsvpConfirmed(evtId);
                } else { alert('Cancel failed'); }
            }).catch(err=>{ console.error(err); alert('Cancel failed'); });
        });
    }

    // Initial check: show cancel button if current user already RSVP'd
    fetch('<?php echo URLROOT; ?>/calender/attendees?event_id=' + encodeURIComponent(evtId), { credentials: 'same-origin' }).then(r=>r.json()).then(function(data){
        if(data && data.ok){
            // render initial attendees into the list container
            var container = document.querySelector('.event-info + .event-info');
            if(container){
                if(data.attendees.length === 0){ container.innerHTML = '<p class="no-events">No attendees yet.</p>'; }
                else {
                    var html = '<h3 style="margin-top:0;">Attendees</h3><ul style="list-style:none;padding:0;margin:0;">';
                    data.attendees.forEach(function(a){ html += '<li style="padding:8px 0;border-bottom:1px solid rgba(255,255,255,0.03);"><strong>'+ (a.name||a.email||'User') +'</strong>' + (a.guests>0?(' <span style="color:var(--muted);">('+a.guests+' guests)</span>'):'') + '</li>'; });
                    html += '</ul>';
                    container.innerHTML = html;
                }
            }
            // show cancel button if current user is present
            fetch('<?php echo URLROOT; ?>/auth/current_user_id.json', { credentials: 'same-origin' }).then(r=>r.json()).then(function(j){
                var uid = j && j.user_id;
                var me = data.attendees.find(function(a){ return Number(a.user_id) === Number(uid); });
                if(me){ detailCancel.style.display = 'inline-flex'; } else { detailCancel.style.display = 'none'; }
            }).catch(()=>{});
        }
    }).catch(()=>{});

});
</script>