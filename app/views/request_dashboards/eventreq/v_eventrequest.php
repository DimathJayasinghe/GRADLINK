
<?php ob_start(); ?>
    <style>
        h1 {
            color: #9ed4dc;
            font-size: 24px;
            margin-bottom: 24px;
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
        <h1>Create a New Event Request</h1>
        <form method="event" action="/events/request" enctype="multipart/form-data" class="event-request-form">
            <div class="form-section">
                <div class="form-group">
                    <label for="event_title" class="form-label">Event Title:</label>
                    <input type="text" id="event_title" name="event_title" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="organizer" class="form-label">Organizer (Club/Society Name):</label>
                    <input type="text" id="organizer" name="organizer" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="event_date" class="form-label">Date of Event:</label>
                    <input type="date" id="event_date" name="event_date" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="event_time" class="form-label">Time of Event:</label>
                    <input type="time" id="event_time" name="event_time" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="venue" class="form-label">Venue/Location:</label>
                    <input type="text" id="venue" name="venue" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="event_type" class="form-label">Event Type:</label>
                    <select id="event_type" name="event_type" class="form-control" required>
                        <option value="" disabled selected>Select Event Type</option>
                        <option value="Workshop">Workshop</option>
                        <option value="Seminar">Seminar</option>
                        <option value="Competition">Competition</option>
                        <option value="Cultural Event">Cultural Event</option>
                        <option value="Fundraiser">Fundraiser</option>
                        <option value="Other">Other</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="description" class="form-label">Purpose/Objective of Event:</label>
                    <textarea id="description" name="description" class="form-control" rows="4"></textarea>
                </div>
                <div class="form-group">
                    <label for="event_image" class="form-label">Add event (image):</label>
                    <div class="upload-area" id="uploadArea">
                        <span>Click to upload or drag and drop</span>
                        <input type="file" id="event_image" name="event_image" accept="image/*" style="display:none;">
                    </div>
                </div>
            </div>
            <button type="submit" class="next-btn">Request Event</button>
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