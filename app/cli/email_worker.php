<?php
// Background worker for queued notification emails.
// Run from project root:
// php app/cli/email_worker.php

if (php_sapi_name() !== 'cli') {
    echo "This script must be run from the command line.\n";
    exit(1);
}

require_once dirname(__DIR__) . '/helpers/env.php';
gl_bootstrap_env();
require_once dirname(__DIR__) . '/config/config.php';
require_once dirname(__DIR__) . '/libraries/Database.php';
require_once dirname(__DIR__) . '/../vendor/autoload.php';
require_once dirname(__DIR__) . '/helpers/EmailHandler.php';
require_once dirname(__DIR__) . '/models/M_email_job.php';

$pollIntervalSeconds = 1;
$worker = new M_email_job();

if (!$worker->isDatabaseAvailable()) {
    echo "[email-worker] Database connection is unavailable. Fix DB/PDO config and retry.\n";
    exit(1);
}

echo "[email-worker] Started. Polling every {$pollIntervalSeconds}s. Press Ctrl+C to stop.\n";

while (true) {
    $job = $worker->claimNextPendingJob();

    if ($job) {
        $jobId = (int)($job->id ?? 0);
        $sent = EmailHandler::send(
            (string)($job->to_email ?? ''),
            (string)($job->subject ?? ''),
            (string)($job->html_body ?? ''),
            (string)($job->to_name ?? ''),
            isset($job->plain_body) ? (string)$job->plain_body : null
        );

        if ($sent) {
            $worker->deleteJob($jobId);
            echo '[email-worker] Sent and removed job #' . $jobId . PHP_EOL;
        } else {
            $worker->markJobFailed($jobId, 'Email sending failed in worker');
            echo '[email-worker] Marked failed job #' . $jobId . PHP_EOL;
        }
    }

    sleep($pollIntervalSeconds);
}
