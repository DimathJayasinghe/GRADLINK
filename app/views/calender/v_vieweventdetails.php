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

.btn-report {
    border: 1px solid rgba(220, 53, 69, 0.45);
}

.report-overlay {
    position: fixed;
    inset: 0;
    background: rgba(0, 0, 0, 0.62);
    display: none;
    align-items: center;
    justify-content: center;
    z-index: 1300;
    padding: 1rem;
}

.report-modal {
    width: min(540px, 100%);
    background: var(--bg-alt, #161b22);
    border: 1px solid var(--border);
    border-radius: var(--radius-md);
    padding: 1rem;
}

.report-modal-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 0.75rem;
}

.report-modal-header h3 {
    margin: 0;
    color: var(--text);
}

.report-close {
    background: transparent;
    border: 1px solid var(--border);
    color: var(--muted);
    border-radius: 8px;
    cursor: pointer;
    padding: 0.3rem 0.55rem;
}

.report-form-group {
    display: flex;
    flex-direction: column;
    gap: 0.35rem;
    margin-bottom: 0.75rem;
}

.report-form-group label {
    font-size: 0.86rem;
    color: var(--muted);
}

.report-form-group select,
.report-form-group textarea,
.report-form-group input {
    width: 100%;
    border-radius: var(--radius-sm);
    border: 1px solid var(--border);
    background: var(--input, #0f141a);
    color: var(--text);
    padding: 0.55rem 0.7rem;
}

.report-form-actions {
    display: flex;
    justify-content: flex-end;
    gap: 0.6rem;
    margin-top: 0.6rem;
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
            
            <?php
                $isBookmarked = !empty($request->bookmarked);
            ?>
            <button
                id="bookmark-btn"
                style="background-color:<?php echo $isBookmarked ? '#ec2424ff' : '#4caf50'; ?>; margin:0;"
                data-bookmarked="<?php echo $isBookmarked ? '1' : '0'; ?>"
                data-event-id="<?php echo htmlspecialchars($request->event_id); ?>"
            >
                <span class="btn" style="color: ffffff;" id="bookmark-label"><?php echo $isBookmarked ? 'Remove Bookmark' : 'Add Bookmark'; ?></span>
            </button>
            <!-- <div class="details-info-item" style="padding: 0px;display: flex; align-items: center; justify-content: center;">
            </div> -->
        </div>
        
        <div class="event-info">
            <!-- <h3>Event Details</h3> -->
            <?php if($request->attachment_image): ?>
                <div class="image-container">
                    <img src="<?php echo htmlspecialchars(URLROOT . '/media/post/' . $request->attachment_image, ENT_QUOTES, 'UTF-8'); ?>" alt="Event Image" class="request-image">
                </div>
            <?php else: ?>
                <div class="image-container">
                    <div class="no-image">No image attached to this request</div>
                </div>
            <?php endif; ?>
        </div>

        <div class="action-buttons">
            <a href="<?php echo URLROOT; ?>/calender/" class="btn btn-back">Back to All Event Requests</a>
            <button
                type="button"
                id="open-event-report-modal"
                class="btn btn-danger btn-report"
                data-event-id="<?php echo (int)$request->event_id; ?>"
                data-report-endpoint="<?php echo URLROOT; ?>/report/submitReport/event"
            >Report Event</button>
            <!-- <button id="detail-cancel-rsvp-btn" class="btn btn-danger" style="display:none;">Cancel My RSVP</button> -->
        </div>

        <div id="eventReportModal" class="report-overlay" style="display:none;">
            <div class="report-modal">
                <div class="report-modal-header">
                    <h3>Report Event</h3>
                    <button type="button" class="report-close" data-action="close">X</button>
                </div>
                <form id="event-report-form" novalidate>
                    <div class="report-form-group">
                        <label for="eventReportCategory">Category</label>
                        <select id="eventReportCategory" required>
                            <option value="" disabled selected>Select a category</option>
                            <option>Spam</option>
                            <option>Harassment or bullying</option>
                            <option>Hate or abusive content</option>
                            <option>Misinformation</option>
                            <option>Illegal or dangerous acts</option>
                            <option>Sexual content</option>
                            <option>Other</option>
                        </select>
                    </div>
                    <div class="report-form-group">
                        <label for="eventReportDetails">Details (optional)</label>
                        <textarea id="eventReportDetails" rows="4" placeholder="Add any details or context..."></textarea>
                    </div>
                    <div class="report-form-group">
                        <label for="eventReportLink">Reference link (optional)</label>
                        <input type="url" id="eventReportLink" placeholder="https://..." />
                    </div>
                    <div class="report-form-actions">
                        <button type="button" class="btn btn-back" data-action="cancel">Cancel</button>
                        <button type="submit" class="btn btn-primary">Submit Report</button>
                    </div>
                </form>
            </div>
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
    function notify(message){
        if(typeof show_popup === 'function'){
            show_popup(message);
            return;
        }
        alert(message);
    }

    var btn = document.getElementById('bookmark-btn');
    var label = document.getElementById('bookmark-label');
    var eventId = btn ? btn.getAttribute('data-event-id') : null;

    function postJson(path, body){
        return fetch(path, {
            method: 'POST',
            credentials: 'same-origin',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(body)
        }).then(function(resp){ return resp.json(); });
    }

    if(btn && eventId){
        btn.addEventListener('click', function(e){
            e.preventDefault();
            var currently = String(btn.getAttribute('data-bookmarked') || '0') === '1';
            postJson('/bookmark/update', {
                type: 'events',
                reference_id: parseInt(eventId, 10),
                bookmarked: !currently
            })
                .then(function(data){
                    if(data && data.ok){
                        var nextState = !!data.bookmarked;
                        btn.setAttribute('data-bookmarked', nextState ? '1' : '0');
                        btn.style.backgroundColor = nextState ? '#ec2424ff' : '#4caf50';
                        if(label){
                            label.textContent = nextState ? 'Remove Bookmark' : 'Add Bookmark';
                        }
                    } else {
                        console.error('Bookmark action failed', data);
                        notify('Could not update bookmark: ' + (data && data.error ? data.error : 'Unknown error'));
                    }
                }).catch(function(err){
                    console.error('Request failed', err);
                    notify('Network error while updating bookmark');
                });
            });

    }

    var reportOpenBtn = document.getElementById('open-event-report-modal');
    var reportModal = document.getElementById('eventReportModal');
    var reportForm = document.getElementById('event-report-form');
    if(!reportOpenBtn || !reportModal || !reportForm){
        return;
    }

    var reportCategory = document.getElementById('eventReportCategory');
    var reportDetails = document.getElementById('eventReportDetails');
    var reportLink = document.getElementById('eventReportLink');
    var reportEventId = Number(reportOpenBtn.getAttribute('data-event-id') || 0);
    var reportEndpoint = reportOpenBtn.getAttribute('data-report-endpoint') || '/report/submitReport/event';

    function closeReportModal(){
        reportModal.style.display = 'none';
    }

    reportOpenBtn.addEventListener('click', function(){
        reportModal.style.display = 'flex';
    });

    reportModal.querySelector('[data-action="close"]')?.addEventListener('click', closeReportModal);
    reportModal.querySelector('[data-action="cancel"]')?.addEventListener('click', closeReportModal);
    reportModal.addEventListener('click', function(e){
        if(e.target === reportModal){
            closeReportModal();
        }
    });

    reportForm.addEventListener('submit', async function(e){
        e.preventDefault();

        if(!reportEventId){
            notify('Invalid event id for report');
            return;
        }

        var category = reportCategory ? reportCategory.value : '';
        if(!category){
            notify('Please select a report category');
            return;
        }

        var submitBtn = reportForm.querySelector('button[type="submit"]');
        var previousText = submitBtn ? submitBtn.textContent : 'Submit Report';
        if(submitBtn){
            submitBtn.disabled = true;
            submitBtn.textContent = 'Submitting...';
        }

        try {
            var fd = new FormData();
            fd.append('event_id', String(reportEventId));
            fd.append('category', category);
            fd.append('details', reportDetails ? reportDetails.value.trim() : '');

            var linkValue = reportLink ? reportLink.value.trim() : '';
            if(linkValue){
                fd.append('link', linkValue);
            }

            var response = await fetch(reportEndpoint, {
                method: 'POST',
                body: fd
            });

            var json = await response.json().catch(function(){ return null; });
            if(!response.ok || !json || (json.success !== true && json.status !== 'success')){
                throw new Error((json && json.message) ? json.message : 'Failed to submit event report');
            }

            notify('Thanks for your report. Our team will review this event.');
            reportForm.reset();
            closeReportModal();
        } catch (err) {
            console.error('Event report submission failed', err);
            notify(err && err.message ? err.message : 'Failed to submit event report');
        } finally {
            if(submitBtn){
                submitBtn.disabled = false;
                submitBtn.textContent = previousText;
            }
        }
    });
});
JS;

require APPROOT . '/views/calender/v_layout_adapter.php';
?>
