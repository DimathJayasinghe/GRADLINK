
<?php ob_start(); ?>
    <style>
        h1 {
            /* color: #9ed4dc; */
            font-size: 23px;
            /* margin-bottom: 24px; */
            text-align: left;
        }
        .event-request-form .form-section {
            display: flex;
            flex-direction: column;
            gap: 24px;
            width: 100%;
            margin: 0 auto;
        }
        .event-request-form .form-group {
            width: 100%;
        }
        .event-request-form .form-label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #e0e0e0;
        }
        .event-request-form .form-control {
            width: 100%;
            padding: 12px;
            border-radius: 8px;
            border: none;
            background: #1e1e1e;
            color: #e0e0e0;
            font-size: 1rem;
        }
        /* minimal checkbox styling to match theme */
        .event-request-form .form-check {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .event-request-form .form-check input[type="checkbox"] {
            width: 18px;
            height: 18px;
            accent-color: #9ed4dc;
        }
        .event-request-form .form-check label {
            color: #e0e0e0;
        }
        .upload-area {
            width: 350px;
            height: 350px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #1e1e1e;
            border: 2px dashed #9e9e9e;
            border-radius: 12px;
            cursor: pointer;
            position: relative;
            overflow: hidden;
            margin-top: 8px;
        }
        .upload-area span {
            color: #9e9e9e;
            font-size: 1.1rem;
        }
        .next-btn {
            background-color: #9ed4dc;
            color: #121212;
            border: none;
            padding: 12px 30px;
            border-radius: 4px;
            cursor: pointer;
            font-family: 'Poppins', sans-serif;
            font-weight: 600;
            margin-top: 24px;
            font-size: 1rem;
        }
        .next-btn:hover {
            opacity: 0.9;
        }
        @media (max-width: 800px) {
            .container {
                max-width: 98vw;
                padding: 16px 4vw;
            }
            .upload-area {
                width: 90vw;
                max-width: 350px;
                height: 250px;
            }
        }
    </style>
<?php $styles = ob_get_clean(); ?>

<?php
    $sidebar_left = [
        ['label'=>'My Event Requests', 'url'=>'/eventrequest/all','active'=>false ,'icon'=>'user'],
        ['label'=>'Create Event Request', 'url'=>'/eventrequest','active'=>true ,'icon'=>'plus-circle'],
    ]
?>

<?php ob_start(); ?>

    <div >
        <?php
            $isEdit = isset($data['event']) && $data['event'];
        ?>
        <h2><?php echo $isEdit ? 'Edit Event Request' : 'Create a New Event Request'; ?></h2>
    <?php require_once APPROOT . '/helpers/Csrf.php'; ?>
    <form method="post" action="<?php echo URLROOT; ?><?php echo $isEdit ? '/eventrequest/update/'.$data['event']->req_id : '/eventrequest/create'; ?>" enctype="multipart/form-data" class="event-request-form">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(Csrf::getToken(), ENT_QUOTES); ?>">
            <?php if($isEdit): ?>
                <input type="hidden" name="req_id" value="<?php echo (int)$data['event']->req_id; ?>">
            <?php endif; ?>
            <div class="form-section">
                <div class="form-group">
                    <label for="event_title" class="form-label">Event Title:</label>
                    <input type="text" id="event_title" name="event_title" class="form-control" required value="<?php echo $isEdit ? htmlspecialchars($data['event']->title, ENT_QUOTES) : ''; ?>">
                </div>
                <div class="form-group">
                    <label for="short_tagline" class="form-label">Short Tagline:</label>
                    <input type="text" id="short_tagline" name="short_tagline" class="form-control" placeholder="A short catchy line about the event" value="<?php echo $isEdit && isset($data['event']->short_tagline) ? htmlspecialchars($data['event']->short_tagline, ENT_QUOTES) : ''; ?>">
                </div>
                <div class="form-group">
                    <label for="organizer" class="form-label">Organizer (Club/Society Name):</label>
                    <input type="text" id="organizer" name="organizer" class="form-control" required value="<?php echo $isEdit ? htmlspecialchars($data['event']->club_name ?? $data['event']->organizer ?? '', ENT_QUOTES) : ''; ?>">
                </div>
                <div class="form-group">
                    <label for="requester_position" class="form-label">Requesting Person's Position in Club:</label>
                    <input type="text" id="requester_position" name="requester_position" class="form-control" placeholder="e.g., President, Secretary, Event Lead" value="<?php echo $isEdit ? htmlspecialchars($data['event']->position ?? '', ENT_QUOTES) : ''; ?>">
                </div>
                <div class="form-group">
                    <label for="event_date" class="form-label">Date of Event:</label>
                    <input type="date" id="event_date" name="event_date" class="form-control" required value="<?php echo $isEdit ? htmlspecialchars($data['event']->event_date ?? '', ENT_QUOTES) : ''; ?>">
                </div>
                <div class="form-group">
                    <label for="event_time" class="form-label">Time of Event:</label>
                    <input type="time" id="event_time" name="event_time" class="form-control" required value="<?php echo $isEdit ? htmlspecialchars($data['event']->event_time ?? '', ENT_QUOTES) : ''; ?>">
                </div>
                <div class="form-group">
                    <label for="venue" class="form-label">Venue/Location:</label>
                    <input type="text" id="venue" name="venue" class="form-control" required value="<?php echo $isEdit ? htmlspecialchars($data['event']->event_venue ?? '', ENT_QUOTES) : ''; ?>">
                </div>
                <div class="form-group">
                    <label for="event_type" class="form-label">Event Type:</label>
                    <select id="event_type" name="event_type" class="form-control" required>
                        <option value="" disabled <?php echo !$isEdit ? 'selected' : ''; ?>>Select Event Type</option>
                        <?php
                            $types = ['Workshop','Seminar','Competition','Cultural Event','Fundraiser','Other'];
                            $currentType = $isEdit ? ($data['event']->event_type ?? '') : '';
                            foreach($types as $t){
                                $sel = ($currentType === $t) ? 'selected' : '';
                                echo "<option value=\"".htmlspecialchars($t,ENT_QUOTES)."\" $sel>".htmlspecialchars($t)."</option>";
                            }
                        ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="description" class="form-label">Purpose/Objective of Event:</label>
                    <textarea id="description" name="description" class="form-control" rows="4"><?php echo $isEdit ? htmlspecialchars($data['event']->description ?? '', ENT_QUOTES) : ''; ?></textarea>
                </div>
                <div class="form-group">
                    <label for="post_caption" class="form-label">Caption for the Post:</label>
                    <textarea id="post_caption" name="post_caption" class="form-control" rows="3" placeholder="Optional caption to use when posting about this event"><?php echo $isEdit && isset($data['event']->post_caption) ? htmlspecialchars($data['event']->post_caption, ENT_QUOTES) : ''; ?></textarea>
                </div>
                <div class="form-group">
                    <div class="form-check">
                        <input type="hidden" name="add_to_calendar" value="0">
                        <input type="checkbox" id="add_to_calendar" name="add_to_calendar" value="1" <?php echo $isEdit && !empty($data['event']->add_to_calendar) ? 'checked' : ''; ?>>
                        <label for="add_to_calendar">Add this event to the calendar</label>
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Approval</label>
                    <div style="display:flex; gap:16px; flex-wrap:wrap;">
                        <div style="flex:1; min-width:240px;">
                            <label for="president_name" class="form-label">President's Name:</label>
                            <input type="text" id="president_name" name="president_name" class="form-control" value="<?php echo $isEdit && isset($data['event']->president_name) ? htmlspecialchars($data['event']->president_name, ENT_QUOTES) : ''; ?>">
                        </div>
                        <div style="flex:1; min-width:240px;">
                            <label for="approval_date" class="form-label">Approval Date:</label>
                            <input type="date" id="approval_date" name="approval_date" class="form-control" value="<?php echo $isEdit && isset($data['event']->approval_date) ? htmlspecialchars($data['event']->approval_date, ENT_QUOTES) : ''; ?>">
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label for="event_image" class="form-label">Add event (image):</label>
                    <div class="upload-area" id="uploadArea">
                        <span <?php echo ($isEdit && !empty($data['event']->attachment_image)) ? 'style="display:none;"' : ''; ?>>Click to upload or drag and drop</span>
                        <input type="file" id="event_image" name="event_image" accept="image/*" style="display:none;">
                        <?php if($isEdit && !empty($data['event']->attachment_image)): ?>
                            <?php $imgUrl = URLROOT . '/storage/posts/' . $data['event']->attachment_image; ?>
                            <script>document.addEventListener('DOMContentLoaded', function(){ var ua = document.getElementById('uploadArea'); if(ua){ ua.style.backgroundImage = 'url(<?php echo $imgUrl; ?>)'; ua.style.backgroundSize='cover'; ua.style.backgroundPosition='center'; } });</script>
                        <?php endif; ?>
                </div>
            </div>
            <button type="submit" class="next-btn"><?php echo $isEdit ? 'Update Event Request' : 'Request Event'; ?></button>
        </form>
    </div>
    <script>
        document.getElementById('uploadArea').addEventListener('click', function() {
            document.getElementById('event_image').click();
        });
        document.getElementById('event_image').addEventListener('change', function(e) {
            if (this.files && this.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('uploadArea').style.backgroundImage = `url(${e.target.result})`;
                    document.getElementById('uploadArea').style.backgroundSize = 'cover';
                    document.getElementById('uploadArea').style.backgroundPosition = 'center';
                    document.getElementById('uploadArea').querySelector('span').style.display = 'none';
                }
                reader.readAsDataURL(this.files[0]);
            }
        });
        // Drag and drop support
        const uploadArea = document.getElementById('uploadArea');
        uploadArea.addEventListener('dragover', function(e) {
            e.preventDefault();
            uploadArea.style.backgroundColor = '#222';
        });
        uploadArea.addEventListener('dragleave', function(e) {
            e.preventDefault();
            uploadArea.style.backgroundColor = '#1e1e1e';
        });
        uploadArea.addEventListener('drop', function(e) {
            e.preventDefault();
            uploadArea.style.backgroundColor = '#1e1e1e';
            if (e.dataTransfer.files && e.dataTransfer.files[0]) {
                document.getElementById('event_image').files = e.dataTransfer.files;
                const event = new Event('change');
                document.getElementById('event_image').dispatchEvent(event);
            }
        });
    </script>

<?php $content = ob_get_clean(); ?>
<?php require APPROOT . '/views/request_dashboards/request_dashboard_layout_adapter.php';?>