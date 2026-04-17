<?php

class M_email_job {
    private $db;

    public function __construct() {
        $this->db = new Database();
        $this->ensureTable();
    }

    public function isDatabaseAvailable(): bool {
        if (!method_exists($this->db, 'getError')) {
            return true;
        }

        return empty($this->db->getError());
    }

    private function ensureTable(): bool {
        try {
            $this->db->query("CREATE TABLE IF NOT EXISTS email_jobs (
                id BIGINT AUTO_INCREMENT PRIMARY KEY,
                to_email VARCHAR(255) NOT NULL,
                to_name VARCHAR(255) NULL,
                subject VARCHAR(255) NOT NULL,
                html_body MEDIUMTEXT NOT NULL,
                plain_body TEXT NULL,
                status ENUM('pending','processing','failed') NOT NULL DEFAULT 'pending',
                attempts INT NOT NULL DEFAULT 0,
                reserved_at DATETIME NULL,
                last_error TEXT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                KEY idx_email_jobs_status_id (status, id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

            return $this->db->execute();
        } catch (Throwable $e) {
            error_log('[email_jobs] ensureTable failed: ' . $e->getMessage());
            return false;
        }
    }

    public function enqueueNotificationEmailJob(string $toEmail, string $subject, string $htmlBody, ?string $plainBody = null, string $toName = ''): bool {
        if (!$this->ensureTable()) {
            return false;
        }

        $toEmail = trim($toEmail);
        $subject = trim($subject);

        if ($toEmail === '' || $subject === '') {
            return false;
        }

        try {
            $this->db->query('INSERT INTO email_jobs (to_email, to_name, subject, html_body, plain_body, status) VALUES (:to_email, :to_name, :subject, :html_body, :plain_body, :status)');
            $this->db->bind(':to_email', $toEmail);
            $this->db->bind(':to_name', trim($toName) !== '' ? trim($toName) : null);
            $this->db->bind(':subject', $subject);
            $this->db->bind(':html_body', $htmlBody);
            $this->db->bind(':plain_body', $plainBody);
            $this->db->bind(':status', 'pending');

            return $this->db->execute();
        } catch (Throwable $e) {
            error_log('[email_jobs] enqueue failed: ' . $e->getMessage());
            return false;
        }
    }

    public function claimNextPendingJob(): ?object {
        if (!$this->ensureTable()) {
            return null;
        }

        try {
            if (!$this->db->beginTransaction()) {
                return null;
            }

            $this->db->query("SELECT id, to_email, to_name, subject, html_body, plain_body, attempts
                              FROM email_jobs
                              WHERE status = 'pending'
                              ORDER BY id ASC
                              LIMIT 1
                              FOR UPDATE");
            $job = $this->db->single();

            if (!$job) {
                $this->db->commit();
                return null;
            }

            $this->db->query("UPDATE email_jobs
                              SET status = 'processing',
                                  attempts = attempts + 1,
                                  reserved_at = NOW(),
                                  last_error = NULL
                              WHERE id = :id AND status = 'pending'");
            $this->db->bind(':id', (int)$job->id);

            if (!$this->db->execute() || $this->db->rowCount() < 1) {
                $this->db->rollBack();
                return null;
            }

            $this->db->commit();
            $job->attempts = (int)($job->attempts ?? 0) + 1;

            return $job;
        } catch (Throwable $e) {
            try {
                $this->db->rollBack();
            } catch (Throwable $ignored) {
            }

            error_log('[email_jobs] claim failed: ' . $e->getMessage());
            return null;
        }
    }

    public function deleteJob(int $jobId): bool {
        if ($jobId <= 0) {
            return false;
        }

        try {
            $this->db->query('DELETE FROM email_jobs WHERE id = :id');
            $this->db->bind(':id', $jobId);
            return $this->db->execute();
        } catch (Throwable $e) {
            error_log('[email_jobs] delete failed: ' . $e->getMessage());
            return false;
        }
    }

    public function markJobFailed(int $jobId, string $error): bool {
        if ($jobId <= 0) {
            return false;
        }

        $error = trim($error);
        if ($error === '') {
            $error = 'Unknown error';
        }

        try {
            $this->db->query("UPDATE email_jobs
                              SET status = 'failed',
                                  last_error = :last_error,
                                  updated_at = CURRENT_TIMESTAMP
                              WHERE id = :id");
            $this->db->bind(':id', $jobId);
            $this->db->bind(':last_error', substr($error, 0, 1000));

            return $this->db->execute();
        } catch (Throwable $e) {
            error_log('[email_jobs] markJobFailed failed: ' . $e->getMessage());
            return false;
        }
    }
}
?>
