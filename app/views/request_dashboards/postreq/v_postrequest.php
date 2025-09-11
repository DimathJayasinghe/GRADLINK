<?php require APPROOT . '/views/inc/header.php';?>
<?php require APPROOT . '/views/inc/commponents/topnavbar.php';?>

<style>
    .postrequest-container {display: flex;min-height: 80vh;background-color: #0f1518;
    }
    .postrequest-leftsidebar {
        width: 220px;
        background-color: #1c1f23;
        color: #ffffff;
        padding: 20px;
        border-right: 1px solid #333;
    }
    .postrequest-body{
        flex: 1;
        padding: 20px;
        color: #ffffff;
        border-left: 1px solid #333;
        display: flex;
        flex-direction: column;
    } 
    .form-group{
        display:flex;
        flex-direction: column;
    }

    .menu {
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        list-style-type: none;
        padding: 0;
    }
    .menu-item {
        margin-bottom: 10px;
    }
    .menu-item a {
        color: #ffffff;
        text-decoration: none;
        font-weight: bold;
    }
    .menu-item a:hover, .menu-item.active a {
        color: #1e90ff;
    }
    .menu div {
        margin-top: 20px;
        border-top: 1px solid #333;
        padding-top: 10px;
        width: 100%;
    }
    .menu div .menu-item {
        align-items: end;
        margin-bottom: 0;
    }
</style>


<div class="postrequest-container">
    <div class="postrequest-leftsidebar">
        <ul class="menu">
            <li class="menu-item active"><a href="<?php echo URLROOT; ?>/postrequest/v_postrequest">Post Request</a></li>
            <li class="menu-item"><a href="<?php echo URLROOT; ?>/postrequest/v_viewpostrequests">View Requests</a></li>
            <li class="menu-item"><a href="<?php echo URLROOT; ?>/postrequest/v_postrequeststatus">Request Status</a></li>
            <div>
                <li class="menu-item"><a href="<?php echo URLROOT; ?>/mainfeed">Back to Main Feed</a></li>
            </div>
        </ul>
    </div>
    <div class="postrequest-body">
        <form action="<?php echo URLROOT; ?>/postrequest/v_postrequest" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="username">Username: </label><input type="text" id="username" disabled value="<?php echo $_SESSION['user_name'];?>" placeholder="<?php echo $_SESSION['user_name'];?>">
                <label for="userid">Userid: </label><input type="text" id="userid" disabled value="<?php echo $_SESSION['user_id'];?>" placeholder="<?php echo $_SESSION['user_id'];?>">
                <label for="title">Title:</label><input type="text" id="title" name="title" required>
                <label for="postcaption">post Caption:</label><textarea id="postcaption" name="postcaption" required></textarea>
                <label for="attachimage">Attach Image</label> <input type="file" id="attachimage" name="attachimage" accept="image/*">
                <label for="accepted-clubs-dropdown">Select your club/sociaty:</label> <select name="accepted-clubs-dropdown" id="accepted-clubs-dropdown">
                    <option value="none" selected disabled hidden>Select an Option</option>
                    <?php foreach($data['clubs'] as $club): ?>
                        <option value="<?php echo $club->id; ?>"><?php echo $club->name; ?></option>
                    <?php endforeach; ?>
                </select>
                <div>
                    <label for="is-a-event-checkbox">Is a event</label><input type="checkbox" id="is-a-event-checkbox" name="is_a_event"><br>
                </div>
                <div class="event-details" style="display:none;">
                    <h3>Event Details</h3>
                    <label for="eventdate">Event Date:</label><input type="date" id="eventdate" name="eventdate">
                    <label for="eventtime">Event Time:</label><input type="time" id="eventtime" name="eventtime">
                    <label for="eventvenue">Event Venue: </label> <input type="text" id="eventvenue" name="eventvenue">
                </div>
                <button type="submit">Submit Request</button>
            </div>
    </div>
</div>

<script>
    document.getElementById('is-a-event-checkbox').addEventListener('change', function() {
        var eventDetails = document.querySelector('.event-details');
        if(this.checked) {
            eventDetails.style.display = 'block';
        } else {
            eventDetails.style.display = 'none';
        }
    });
</script>