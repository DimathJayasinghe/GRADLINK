<?php // vim: ft=php
ob_start(); ?>
<style>
    /* Center column: event / request list */
    .event-card { /* new event-friendly class */
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px 14px;
        border-bottom: 1px solid var(--border);
        cursor: pointer;
        transition: background 0.2s ease;
        text-decoration: none;
        color: inherit;
    }
    .event-card:hover {
        background: rgba(255, 255, 255, 0.04);
    }
    .event-card.active-selected-event {
        background: rgba(158, 212, 220, 0.1);
    }

    .profile-pic-new-event img,
    .profile-pic-event img {
        width: 44px;
        height: 44px;
        border-radius: 50%;
        object-fit: cover;
    }

    .event-details h3 {
        margin: 0;
        font-size: 15px;
        color: var(--text);
    }

    .event-details p {
        margin: 2px 0 6px;
        font-size: 12px;
        color: var(--muted);
    }

    .status {
        display: inline-block;
        padding: 2px 8px;
        border-radius: var(--radius-full);
        font-size: 11px;
        font-weight: 600;
    }

    .status-pending,
    .status-event-pending {
        background: rgba(255, 193, 7, 0.15);
        color: #ffc107;
    }
    .status-approved,
    .status-event-approved {
        background: rgba(40, 167, 69, 0.15);
        color: #28a745;
    }
    .status-rejected,
    .status-event-rejected {
        background: rgba(220, 53, 69, 0.15);
        color: #dc3545;
    }

    /* Make the center column a flex container so the list can scroll */
    .template-center {
        display: flex;
        flex-direction: column;
    }
    .template-center .center-topic { flex: 0 0 auto; }
    .event-list {
        flex: 1 1 auto;
        overflow-y: auto;
        min-height: 0; /* allow flex item to shrink for overflow */
    }

    /* Right column: detail panel (event / request details) */
    .rightsidebar_content,
    .rightsidebar_event_content {
        padding: 20px 30px 30px;
    }


    .name-of-the-request-holder .description {
        color: var(--muted);
        font-size: 13px;
    }

    .profile-image img,
    .event-image img {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        object-fit: cover;
        margin: 16px 0;
    }

    .detail-grid {
        display: grid;
        grid-template-columns: 160px 1fr;
        gap: 8px 12px;
        margin: 16px 0 20px;
    }

    .detail-label {
        color: var(--muted);
        font-size: 13px;
    }

    .detail-value {
        color: var(--text);
        font-weight: 500;
        font-size: 14px;
    }

    .action-row {
        display: flex;
        gap: 10px;
        margin-top: 8px;
    }

    .btn-approve,
    .btn-reject {
        border: none;
        padding: 10px 14px;
        border-radius: var(--radius-sm);
        cursor: pointer;
        font-weight: 600;
    }

    .btn-approve {
        background: #28a745;
        color: #fff;
    }

    .btn-reject {
        background: #dc3545;
        color: #fff;
    }

    .empty-state {
        color: var(--muted);
        margin-top: 20px;
    }
    .Empty-section{
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-align: center;
        min-height: 77vh;
    }
    .Empty-section h2{
        margin: 0 0 0px;
        font-size: 20px;
        font-weight: 600;
        /* color: var(--text); */
        /* color: rgba(237, 237, 237, 0.74); */
        color: rgba(255, 255, 255, 0.12);
    }
    .Empty-section .empty-state{
        margin: 0px;
        margin-top: 5px;
        font-size: 15px;
        /* color: var(--muted); */
        color: rgba(255, 255, 255, 0.12);
        max-width: 520px;
    }
    .section-topic{
        margin-top: 8px;
        margin-bottom: 10px;
        font-size: 24px;
        font-weight: 600;
        color: var(--text);
    }

    /* Calendar Styles */
    .calendar-container {
        background-color: rgba(255, 255, 255, 0.03);
        border-radius: var(--radius-md);
        padding: 16px;
        margin-top: 20px;
        margin-bottom: 20px;
    }

    .calendar-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 12px;
    }

    .calendar-header h3 {
        margin: 0;
        font-size: 16px;
        color: var(--text);
    }

    .calendar-nav {
        background: rgba(255, 255, 255, 0.08);
        border: none;
        color: var(--text);
        width: 30px;
        height: 30px;
        border-radius: 50%;
        cursor: pointer;
        font-weight: bold;
    }

    .calendar-nav:hover {
        background: rgba(255, 255, 255, 0.15);
    }

    .calendar-weekdays {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        text-align: center;
        color: var(--muted);
        font-size: 12px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        padding-bottom: 8px;
        margin-bottom: 8px;
    }

    .calendar-days {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        grid-gap: 2px;
    }

    .calendar-day {
        aspect-ratio: 1/1;
        display: flex;
        justify-content: center;
        align-items: center;
        cursor: pointer;
        border-radius: var(--radius-sm);
        position: relative;
        font-size: 14px;
    }

    .calendar-day:hover {
        background-color: rgba(255, 255, 255, 0.1);
    }

    .calendar-day.other-month {
        color: rgba(255, 255, 255, 0.3);
    }

    .calendar-day.today {
        background-color: rgba(158, 212, 220, 0.2);
        font-weight: bold;
    }

    .calendar-day.selected {
        /* background-color: var(--accent-light); */
        background-color: #7216c853;
        border: 1px solid #d5c6e2a4;
        color: var(--text-invert);
    }

    .calendar-day.has-event::after {
        content: '';
        position: absolute;
        bottom: 4px;
        left: 50%;
        transform: translateX(-50%);
        width: 4px;
        height: 4px;
        border-radius: 50%;
        background-color: var(--accent);
    }

    /* If a day has events, give it a subtle background so it's easy to spot
       Avoid overriding the styles for the currently selected day or today */
    .calendar-day.has-event:not(.selected):not(.today) {
        background-color: rgba(179, 124, 29, 0.49);
    }

    /* Event details panel */
    .event-details-panel {
        margin-top: 20px;
    }

    .selected-date {
        margin: 0 0 12px;
        font-size: 16px;
        color: var(--text);
        font-weight: 600;
    }

    .events-list {
        /* max-height: 250px; */
        overflow-y: auto;
    }

    .event-item {
        padding: 10px;
        background: rgba(255, 255, 255, 0.05);
        border-radius: var(--radius-sm);
        margin-bottom: 8px;
    }

    .event-item h4 {
        margin: 0 0 5px;
        font-size: 14px;
    }

    .event-item p {
        margin: 0;
        font-size: 12px;
        color: var(--muted);
    }

    .event-time {
        font-weight: 500;
        color: var(--accent-light);
    }

    .no-events {
        color: var(--muted);
        font-size: 13px;
        text-align: center;
        padding: 10px;
    }
    
    /* Bookmark button styles */
    .bookmark-btn {
        width: 100%;
        margin-top: 8px;
        padding: 8px 12px;
        border: none;
        border-radius: var(--radius-sm);
        cursor: pointer;
        display: flex;
        justify-content: center;
        font-size: 13px;
        font-weight: 500;
        color: #ffffff;
    }
    
    .bookmark-btn.bookmarked {
        background-color:#ec2424ff;
    }
    
    .bookmark-btn.not-bookmarked {
        background-color: #4caf50;
    }

    /* Event actions for items in the details panel */
    .event-actions {
        display: flex;
        gap: 8px;
        align-items: center;
        margin-top: 6px;
    }

    .event-actions .btn {
        padding: 8px 10px;
        border-radius: 6px;
        border: none;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        text-decoration: none;
        color: #fff;
    }

    .btn-rsvp {
        background-color: #007bff;
        color: #303030ff;
    }

    .btn-view {
        background-color: #6c757d;
    }

    .bookmark-btn {
        min-width: 140px;
    }
</style>
<?php $styles = ob_get_clean(); ?>

<?php ob_start(); ?>
<?php
    $sidebar_left = [
        ['label' => 'View Calendar', 'url'=>'/calender', 'active'=>true, 'icon'=>'calendar'],
        ['label'=>'Bookmarked Events', 'url'=>'/calender/bookmarks', 'active'=>false, 'icon'=>'bookmark'],
    ]
?>

<?php $center_content = ob_get_clean(); ?>

<?php ob_start(); ?>
<!-- Right column: user-selected details -->
<div class="rightsidebar_content" style="padding:0px">
    <div class="section-topic">Calendar</div>
    
    <div class="calendar-container">
        <!-- Month navigation -->
        <div class="calendar-header">
            <button class="calendar-nav" id="prevMonth" style="background: rgba(255, 255, 255, 0.08);
        border: none;
        color: var(--text);
        width: 30px;
        height: 30px;
        border-radius: 50%;
        cursor: pointer;
        font-weight: bold;padding:2px">&lt;</button>
            <h3 id="currentMonthDisplay">October 2025</h3>
            <button class="calendar-nav" id="nextMonth" style="background: rgba(255, 255, 255, 0.08);
        border: none;
        color: var(--text);
        width: 30px;
        height: 30px;
        border-radius: 50%;
        cursor: pointer;
        font-weight: bold;padding:2px">&gt;</button>
        </div>
        
        <!-- Days of week header -->
        <div class="calendar-weekdays">
            <div>Sun</div>
            <div>Mon</div>
            <div>Tue</div>
            <div>Wed</div>
            <div>Thu</div>
            <div>Fri</div>
            <div>Sat</div>
        </div>
        
        <!-- Calendar grid (will be filled by JavaScript) -->
        <div class="calendar-days" id="calendarDays">
            <!-- Days will be added dynamically -->
        </div>
    </div>

    <!-- Event details section (appears when a date with events is clicked) -->
    <div class="event-details-panel" id="eventDetailsPanel">
        <h3 class="selected-date">No date selected</h3>
        <div id="eventsList" class="events-list">
            <!-- Event details will appear here when a date is clicked -->
        </div>
    </div>
</div>

<?php $content = ob_get_clean(); ?>

<?php ob_start(); ?>
// Calendar initialization and event handling
document.addEventListener('DOMContentLoaded', () => {
    // Calendar elements
    const prevMonthBtn = document.getElementById('prevMonth');
    const nextMonthBtn = document.getElementById('nextMonth');
    const currentMonthDisplay = document.getElementById('currentMonthDisplay');
    const calendarDays = document.getElementById('calendarDays');
    const eventDetailsPanel = document.getElementById('eventDetailsPanel');
    const selectedDateElement = document.querySelector('.selected-date');
    const eventsList = document.getElementById('eventsList');

    // Keep track of the current date and selected date
    let currentDate = new Date();
    let selectedDate = null;

    // Events payload: prefer server-provided events_payload injected by controller (PHP), otherwise fall back to a small local sample
    <?php
        // Prepare a safe JSON representation of the events payload
        $events_json = '{}';
        if(!empty($data['events_payload']) && is_array($data['events_payload'])){
            $events_json = json_encode($data['events_payload'], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT);
        }
    ?>
    const events = <?php echo $events_json; ?> || {
        '2025-10-20': [
            { title: 'Alumni Networking Event', time: '14:00', description: 'Virtual networking session with industry professionals.' ,bookmarked: false,id:1},
            { title: 'Resume Workshop', time: '16:30', description: 'Learn how to create an effective resume.' ,bookmarked: true,id:2}
        ],
        '2025-10-25': [
            {title: 'Career Fair', time: '10:00', description: 'Annual career fair with top employers.',bookmarked: false ,id:3}
        ],
        '2025-10-28': [
            {title: 'Graduate Studies Info Session', time: '15:00', description: 'Information about graduate programs and opportunities.' ,bookmarked: true,id:4 }
        ],
        '2025-11-05': [
            {title: 'Tech Industry Panel', time: '18:00', description: 'Panel discussion with alumni working in technology.' ,bookmarked: false,id: 5}
        ]
    };

    // Initialize the calendar; renderCalendar is async now and fetches events for the month
    async function initCalendar() {
        await renderCalendar(currentDate);

        // Set up event listeners
        prevMonthBtn.addEventListener('click', async () => {
            currentDate.setMonth(currentDate.getMonth() - 1);
            await renderCalendar(currentDate);
        });

        nextMonthBtn.addEventListener('click', async () => {
            currentDate.setMonth(currentDate.getMonth() + 1);
            await renderCalendar(currentDate);
        });
    }

    // Render the calendar for a specific month
    async function renderCalendar(date) {
        const year = date.getFullYear();
        const month = date.getMonth();
        
        // Update the month display
        const monthNames = ['January', 'February', 'March', 'April', 'May', 'June', 
                           'July', 'August', 'September', 'October', 'November', 'December'];
        currentMonthDisplay.textContent = `${monthNames[month]} ${year}`;
        
        // Clear the calendar
        calendarDays.innerHTML = '';

        // Fetch events for this month from server (start..end)
        try {
            const start = `${year}-${String(month+1).padStart(2,'0')}-01`;
            const end = `${year}-${String(month+1).padStart(2,'0')}-${String(new Date(year, month+1, 0).getDate()).padStart(2,'0')}`;
            const resp = await fetch(`<?php echo URLROOT; ?>/calender/events?start=${encodeURIComponent(start)}&end=${encodeURIComponent(end)}`, { credentials: 'same-origin' });
            if(resp.ok){
                const json = await resp.json();
                // replace events payload with server data (fallback preserved)
                if(json && Object.keys(json).length){
                    // assign new object to events variable
                    for(const k in events) { if(Object.prototype.hasOwnProperty.call(events,k)) delete events[k]; }
                    Object.assign(events, json);
                }
            }
        } catch(err) {
            // network or server error — leave embedded events as fallback
            console.warn('Could not fetch events for month, using embedded payload', err);
        }
        
        // Get the first day of the month and the number of days in the month
        const firstDayOfMonth = new Date(year, month, 1);
        const lastDayOfMonth = new Date(year, month + 1, 0);
        
        const startDay = firstDayOfMonth.getDay(); // Day of week (0-6)
        const daysInMonth = lastDayOfMonth.getDate();
        
        // Get the last few days of the previous month to fill the first row
        const daysInPrevMonth = new Date(year, month, 0).getDate();
        
        // Add days from the previous month
        for (let i = startDay - 1; i >= 0; i--) {
            const day = daysInPrevMonth - i;
            const dayElement = createDayElement(day, true);
            calendarDays.appendChild(dayElement);
        }
        
        // Add days for the current month
        const today = new Date();
        const isCurrentMonth = today.getFullYear() === year && today.getMonth() === month;
        
        for (let i = 1; i <= daysInMonth; i++) {
            const isToday = isCurrentMonth && i === today.getDate();
            const dateString = `${year}-${String(month + 1).padStart(2, '0')}-${String(i).padStart(2, '0')}`;
            const hasEvent = events[dateString] ? true : false;
            
            const dayElement = createDayElement(i, false, isToday, hasEvent, dateString);
            calendarDays.appendChild(dayElement);
        }
        
        // Add days from the next month to fill the last row
        const totalCells = Math.ceil((startDay + daysInMonth) / 7) * 7;
        const remainingCells = totalCells - (startDay + daysInMonth);
        
        for (let i = 1; i <= remainingCells; i++) {
            const dayElement = createDayElement(i, true);
            calendarDays.appendChild(dayElement);
        }
    }

    // Create a day element for the calendar
    function createDayElement(day, isOtherMonth = false, isToday = false, hasEvent = false, dateString = null) {
        const dayElement = document.createElement('div');
        dayElement.classList.add('calendar-day');
        dayElement.textContent = day;
        
        if (isOtherMonth) {
            dayElement.classList.add('other-month');
        } else {
            if (isToday) {
                dayElement.classList.add('today');
            }
            
            if (hasEvent) {
                dayElement.classList.add('has-event');
            }
            
            if (dateString) {
                dayElement.setAttribute('data-date', dateString);
                
                dayElement.addEventListener('click', () => {
                    // Remove selected class from previously selected day
                    const prevSelected = document.querySelector('.calendar-day.selected');
                    if (prevSelected) {
                        prevSelected.classList.remove('selected');
                    }
                    
                    // Add selected class to this day
                    dayElement.classList.add('selected');
                    
                    // Update selected date and show events
                    selectedDate = dateString;
                    showEventsForDate(dateString);
                });
            }
        }
        
        return dayElement;
    }

    // Show events for a specific date
    function showEventsForDate(dateString) {
        // Update the selected date display
        const [year, month, day] = dateString.split('-');
        const monthNames = ['January', 'February', 'March', 'April', 'May', 'June', 
                           'July', 'August', 'September', 'October', 'November', 'December'];
        selectedDateElement.textContent = `${day} ${monthNames[parseInt(month) - 1]} ${year}`;
        
        // Clear previous events
        eventsList.innerHTML = '';
        
        // Check if there are events for this date
        if (events[dateString] && events[dateString].length > 0) {
            // Sort events by time
            const sortedEvents = [...events[dateString]].sort((a, b) => a.time.localeCompare(b.time));
            
            // Add each event to the list
            sortedEvents.forEach(event => {
                const eventItem = document.createElement('div');
                eventItem.classList.add('event-item');
                
                const formattedTime = formatTime(event.time);
                const html = `
                <div>
                        <h4>${event.title}</h4>
                        <p class="event-time">${formattedTime}</p>
                        <p>${event.description}</p>
                    </div>
                    <div class="event-actions">
                        <button class="bookmark-btn ${event.bookmarked ? 'bookmarked' : 'not-bookmarked'}" data-event-id="${event.id}">
                            ${event.bookmarked ? 'Remove Bookmark' : 'Add to Bookmarks'}
                        </button>
                        <button style="color: #303030ff;" class="btn btn-rsvp" data-event-id="${event.id}">RSVP <span class="rsvp-count" data-event-id="${event.id}">&nbsp;0</span></button>
                        <a class="btn btn-view" href="<?php echo URLROOT; ?>/calender/show/${encodeURIComponent(event.id)}" value=${event.id}>View Details</a>
                    </div>
                    `;

                // then attach to DOM
                eventItem.innerHTML = html;                
                eventsList.appendChild(eventItem);
            });
        } else {
            // No events for this date
            const noEvents = document.createElement('div');
            noEvents.classList.add('no-events');
            noEvents.textContent = 'No events scheduled for this date.';
            eventsList.appendChild(noEvents);
        }
    }

    // Format time from 24-hour to 12-hour format
    function formatTime(time) {
        const [hours, minutes] = time.split(':').map(Number);
        const period = hours >= 12 ? 'PM' : 'AM';
        const hour12 = hours % 12 || 12;
        return `${hour12}:${String(minutes).padStart(2, '0')} ${period}`;
    }

    // Initialize the calendar
    initCalendar();
    
    // Event listener for bookmark buttons
    document.addEventListener('click', function(e) {
            // Bookmark clicked
            if (e.target.closest('.bookmark-btn')) {
                const btn = e.target.closest('.bookmark-btn');
                const eventId = btn.getAttribute('data-event-id');
                if(!eventId) return;

                // Determine current bookmarked state for this event
                let idx = -1;
                if (selectedDate && events[selectedDate]) {
                    idx = events[selectedDate].findIndex(ev => String(ev.id) === String(eventId));
                }

                const currentlyBookmarked = (idx !== -1) ? !!events[selectedDate][idx].bookmarked : false;
                const endpoint = currentlyBookmarked ? '<?php echo URLROOT; ?>/calender/removeBookmarkAjax' : '<?php echo URLROOT; ?>/calender/addBookmark';
                const payload = { event_id: Number(eventId), csrf_token: (window.GL_CSRF_TOKEN || null) };

                fetch(endpoint, {
                    method: 'POST',
                    credentials: 'same-origin',
                    headers: Object.assign({ 'Content-Type': 'application/json' }, (window.GL_CSRF_TOKEN ? { 'X-CSRF-Token': window.GL_CSRF_TOKEN } : {})),
                    body: JSON.stringify(payload)
                }).then(r => r.json()).then(data => {
                    if (data && data.ok) {
                        // update local state from server response
                        if (selectedDate && idx !== -1) {
                            events[selectedDate][idx].bookmarked = !!data.bookmarked;
                            showEventsForDate(selectedDate);
                        } else {
                            // If we couldn't find it by selectedDate, try to update anywhere in events
                            for (const d in events) {
                                const i = events[d].findIndex(ev => String(ev.id) === String(eventId));
                                if (i !== -1) {
                                    events[d][i].bookmarked = !!data.bookmarked;
                                    if (d === selectedDate) showEventsForDate(selectedDate);
                                    break;
                                }
                            }
                        }
                    } else {
                        console.warn('Bookmark update failed', data);
                        // Optionally show an error to the user here
                    }
                }).catch(err => {
                    console.error('Bookmark request failed', err);
                });
                return;
            }

            // RSVP clicked
            if (e.target.closest('.btn-rsvp')){
                const btn = e.target.closest('.btn-rsvp');
                const eventId = btn.getAttribute('data-event-id');
                if(!eventId) return;
                // Open RSVP modal and set selected event id via global helper
                if(window.__GL_openRsvpModal){
                    window.__GL_openRsvpModal(Number(eventId));
                } else if(typeof openRsvpModal === 'function'){
                    openRsvpModal(Number(eventId));
                }
                return;
            }
    });
});

// Use the central RSVP modal included by the layout adapter. Register a handler
// that will be called by the adapter after a successful RSVP so we can refresh counts.
if(!window.__GL_onRsvpConfirmed){
    window.__GL_onRsvpConfirmed = function(ev){
        try{ refreshAttendeeCountForEvent(ev); }catch(e){console.error(e);} 
    };
}
function refreshAttendeeCountForEvent(eventId){
    fetch('<?php echo URLROOT; ?>/calender/attendees?event_id=' + encodeURIComponent(eventId), { credentials: 'same-origin' })
        .then(r=>r.json()).then(data=>{
            if(data && data.ok){
                const countEls = document.querySelectorAll('.rsvp-count[data-event-id="'+eventId+'"]');
                countEls.forEach(el=> el.textContent = data.attendees.length);
                // If on details page, update attendees list too (in that view we'll fetch attendees separately)
            }
        }).catch(err=>console.error('Could not refresh attendees',err));
}

// Initialize rsvp counts for visible events
document.addEventListener('DOMContentLoaded', function(){
    // for each unique event id in events, fetch attendees count
    const seen = new Set();
    for(const d in events){
        events[d].forEach(ev=>{
            if(!seen.has(String(ev.id))){
                seen.add(String(ev.id));
                refreshAttendeeCountForEvent(ev.id);
            }
        });
    }
});

// Original event handling
document.addEventListener('click', (e) => {
    // Support both legacy approve/reject buttons and new add/remove calendar actions
    const approveBtn = e.target.closest('.btn-approve');
    const rejectBtn = e.target.closest('.btn-reject');
    const addCalBtn = e.target.closest('.btn-add-calendar');
    const removeEvtBtn = e.target.closest('.btn-remove-event');

    if (addCalBtn) {
        const id = addCalBtn.getAttribute('data-req-id');
        // TODO: POST to backend to add to calendar
        alert('Added request/event #' + id + ' to calendar (wire up backend)');
        return;
    }

    if (removeEvtBtn) {
        const id = removeEvtBtn.getAttribute('data-req-id');
        // TODO: POST to backend to remove from calendar
        alert('Removed request/event #' + id + ' from calendar (wire up backend)');
        return;
    }

    if (approveBtn) {
        const id = approveBtn.getAttribute('data-req-id');
        // TODO: POST to backend to approve
        alert('Approved request #' + id + ' (wire up backend)');
    }

    if (rejectBtn) {
        const id = rejectBtn.getAttribute('data-req-id');
        // TODO: POST to backend to reject
        alert('Rejected request #' + id + ' (wire up backend)');
    }
});
<?php $scripts = ob_get_clean(); ?>

<?php require APPROOT."/views/calender/v_layout_adapter.php"?>