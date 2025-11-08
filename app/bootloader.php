<?php
    // Load configuration (absolute path safe)
    require_once __DIR__ . '/config/config.php';

    // Load helpers / libraries needed before Controller (trait must precede usage)
    require_once __DIR__ . '/helpers/Notifiable.php';
    require_once __DIR__ . '/libraries/Database.php';
    require_once __DIR__ . '/libraries/Sanitizer.php';
    require_once __DIR__ . '/helpers/SessionManager.php';
    require_once __DIR__ . '/libraries/Cookie.php';

    // Controller depends on Notifiable trait
    require_once __DIR__ . '/libraries/Controller.php';

    // Core comes last (it instantiates controllers)
    require_once __DIR__ . '/libraries/Core.php';
?>