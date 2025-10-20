<?php
// Smoke test for event lifecycle
// Run from project root via: php dev/smoke_event_lifecycle.php

require_once __DIR__ . '/../app/config/config.php';
require_once __DIR__ . '/../app/libraries/Database.php';
require_once __DIR__ . '/../app/models/M_eventrequest.php';
require_once __DIR__ . '/../app/models/M_event.php';
require_once __DIR__ . '/../app/models/M_event_image.php';

echo "Starting smoke test: event lifecycle\n";

$db = new Database();

try{
    // Create a temporary test user if none exists with email smoke_test@example.com
    $db->query("SELECT id FROM users WHERE email = :email LIMIT 1");
    $db->bind(':email', 'smoke_test+1@example.com');
    $row = $db->single();
    if($row && isset($row->id)){
        $testUserId = (int)$row->id;
        echo "Using existing test user id={$testUserId}\n";
    } else {
        // insert minimal user (adjust to your users table schema)
        $db->query("INSERT INTO users (name,email,password,created_at) VALUES (:name,:email,:pw,NOW())");
        $db->bind(':name','Smoke Tester');
        $db->bind(':email','smoke_test+1@example.com');
        $db->bind(':pw', password_hash('smoke-password', PASSWORD_DEFAULT));
        $db->execute();
        $testUserId = (int)$db->lastInsertId();
        echo "Created test user id={$testUserId}\n";
    }

    // Create an event request
    $reqModel = new M_eventrequest();
    $reqData = [
        'user_id' => $testUserId,
        'title' => 'Smoke Test Event ' . time(),
        'description' => 'This event was created by smoke test script.',
        'club_name' => 'Smoke Club',
        'position' => 'Tester',
        'attachment_image' => null,
        'event_date' => date('Y-m-d', strtotime('+10 days')),
        'event_time' => '15:30:00',
        'event_venue' => 'Test Hall',
        'status' => 'Pending'
    ];
    $reqId = $reqModel->create($reqData);
    if(!$reqId){ throw new Exception('Failed to create event request'); }
    echo "Created event_request id={$reqId}\n";

    // Approve (convert) the request -> create event
    // emulate admin approval by creating the event via M_event
    $eventModel = new M_event();
    $startDatetime = $reqData['event_date'] . ' ' . $reqData['event_time'];
    $eventPayload = [
        'slug' => null,
        'title' => $reqData['title'],
        'description' => $reqData['description'],
        'start_datetime' => $startDatetime,
        'end_datetime' => null,
        'all_day' => 0,
        'timezone' => 'UTC',
        'venue' => $reqData['event_venue'],
        'capacity' => null,
        'organizer_id' => $testUserId,
        'status' => 'published',
        'visibility' => 'public',
        'series_id' => null
    ];
    $eventId = $eventModel->create($eventPayload);
    if(!$eventId) throw new Exception('Failed to create event from request');
    echo "Created event id={$eventId}\n";

    // Optionally attach an image record
    $eiModel = new M_event_image();
    if(!empty($reqData['attachment_image'])){
        $res = $eiModel->addForEvent($eventId, $reqData['attachment_image'], 1);
        echo "Created event_image record id=" . ($res ? $res : 'NULL or false') . "\n";
    } else {
        echo "No attachment to add for event_image\n";
    }

    // Fetch the event via findById
    $fetched = $eventModel->findById($eventId);
    if(!$fetched) throw new Exception('Could not fetch created event');
    echo "Fetched event: id={$fetched->id}, title='{$fetched->title}', start={$fetched->start_datetime}\n";

    // Update the event (edit)
    $updateData = [
        'slug' => null,
        'title' => $fetched->title . ' (edited)',
        'description' => $fetched->description . ' [edited]',
        'start_datetime' => $fetched->start_datetime,
        'end_datetime' => null,
        'all_day' => 0,
        'timezone' => $fetched->timezone ?? 'UTC',
        'venue' => $fetched->venue,
        'capacity' => $fetched->capacity ?? null,
        'status' => $fetched->status ?? 'published',
        'visibility' => $fetched->visibility ?? 'public',
        'series_id' => $fetched->series_id ?? null
    ];
    $updated = $eventModel->update((int)$eventId, $updateData);
    echo "Event update rowCount={$updated}\n";

    // Re-fetch to confirm edit
    $re = $eventModel->findById($eventId);
    echo "After edit: title='{$re->title}'\n";

    // Clean up: delete event, delete event_images, delete event_request
    $deletedEventImages = 0;
    $db->query('DELETE FROM event_images WHERE event_id = :eid');
    $db->bind(':eid', $eventId);
    $db->execute();
    $deletedEventImages = $db->rowCount();

    $db->query('DELETE FROM events WHERE id = :id');
    $db->bind(':id', $eventId);
    $db->execute();
    $deletedEvents = $db->rowCount();

    $db->query('DELETE FROM event_requests WHERE id = :id');
    $db->bind(':id', $reqId);
    $db->execute();
    $deletedRequests = $db->rowCount();

    echo "Cleanup: deleted_event_images={$deletedEventImages}, deleted_events={$deletedEvents}, deleted_requests={$deletedRequests}\n";

    // Optionally remove test user created earlier (commented out - you can enable)
    // $db->query('DELETE FROM users WHERE id = :id'); $db->bind(':id', $testUserId); $db->execute();

    echo "Smoke test completed OK\n";

} catch(Exception $e){
    echo "Smoke test failed: " . $e->getMessage() . "\n";
    exit(1);
}

?>