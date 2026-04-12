<?php

class M_bookmark {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    private function ensureGenericBookmarksTable() {
        $this->db->query(
            "CREATE TABLE IF NOT EXISTS bookmarks (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT NOT NULL,
                bookmark_type VARCHAR(20) NOT NULL,
                reference_id BIGINT NOT NULL,
                title VARCHAR(255) NULL,
                url VARCHAR(512) NULL,
                metadata TEXT NULL,
                created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                UNIQUE KEY uniq_user_type_ref (user_id, bookmark_type, reference_id),
                KEY idx_user_created (user_id, created_at)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4"
        );
        $this->db->execute();
    }

    public function createGenericBookmark($user_id, $type, $reference_id, $title = null, $url = null, $metadata = null) {
        $this->ensureGenericBookmarksTable();
        $metaJson = is_array($metadata) ? json_encode($metadata) : $metadata;

        $this->db->query(
            'INSERT INTO bookmarks (user_id, bookmark_type, reference_id, title, url, metadata, created_at)
             VALUES (:uid, :type, :rid, :title, :url, :meta, NOW())
             ON DUPLICATE KEY UPDATE title = VALUES(title), url = VALUES(url), metadata = VALUES(metadata), created_at = NOW()'
        );
        $this->db->bind(':uid', (int)$user_id);
        $this->db->bind(':type', (string)$type);
        $this->db->bind(':rid', (int)$reference_id);
        $this->db->bind(':title', $title);
        $this->db->bind(':url', $url);
        $this->db->bind(':meta', $metaJson);

        return $this->db->execute();
    }

    public function updateGenericBookmark($user_id, $type, $reference_id, $title = null, $url = null, $metadata = null) {
        $this->ensureGenericBookmarksTable();
        $metaJson = is_array($metadata) ? json_encode($metadata) : $metadata;

        $this->db->query(
            'UPDATE bookmarks
             SET title = COALESCE(:title, title),
                 url = COALESCE(:url, url),
                 metadata = COALESCE(:meta, metadata)
             WHERE user_id = :uid AND bookmark_type = :type AND reference_id = :rid'
        );
        $this->db->bind(':uid', (int)$user_id);
        $this->db->bind(':type', (string)$type);
        $this->db->bind(':rid', (int)$reference_id);
        $this->db->bind(':title', $title);
        $this->db->bind(':url', $url);
        $this->db->bind(':meta', $metaJson);
        $this->db->execute();

        return $this->db->rowCount() > 0;
    }

    public function deleteGenericBookmark($user_id, $type, $reference_id) {
        $this->ensureGenericBookmarksTable();
        $this->db->query('DELETE FROM bookmarks WHERE user_id = :uid AND bookmark_type = :type AND reference_id = :rid');
        $this->db->bind(':uid', (int)$user_id);
        $this->db->bind(':type', (string)$type);
        $this->db->bind(':rid', (int)$reference_id);
        $this->db->execute();

        return $this->db->rowCount() > 0;
    }

    public function hasGenericBookmark($user_id, $type, $reference_id) {
        $this->ensureGenericBookmarksTable();
        $this->db->query('SELECT 1 FROM bookmarks WHERE user_id = :uid AND bookmark_type = :type AND reference_id = :rid LIMIT 1');
        $this->db->bind(':uid', (int)$user_id);
        $this->db->bind(':type', (string)$type);
        $this->db->bind(':rid', (int)$reference_id);
        return (bool)$this->db->single();
    }

    public function getBookmarksByUserId($user_id) {
        $all = [];

        // Events (from dedicated event_bookmarks table)
        try {
            $this->db->query(
                'SELECT
                    "events" AS bookmark_type,
                    eb.event_id AS reference_id,
                    e.title AS title,
                    e.description AS description,
                    CONCAT("/calender/show/", e.id) AS url,
                    e.start_datetime AS event_date,
                    CONCAT(DATE_FORMAT(e.start_datetime, "%b %d, %Y"), " • ", DATE_FORMAT(e.start_datetime, "%h:%i %p")) AS subtitle,
                    eb.created_at AS created_at
                 FROM event_bookmarks eb
                 JOIN events e ON e.id = eb.event_id
                 WHERE eb.user_id = :uid
                 ORDER BY eb.created_at DESC'
            );
            $this->db->bind(':uid', (int)$user_id);
            $eventRows = $this->db->resultSet();
            foreach ($eventRows as $row) {
                $all[] = (object)[
                    'bookmark_type' => 'events',
                    'reference_id' => (int)$row->reference_id,
                    'title' => (string)($row->title ?? ''),
                    'description' => (string)($row->description ?? ''),
                    'subtitle' => (string)($row->subtitle ?? ''),
                    'url' => (string)($row->url ?? ''),
                    'event_date' => $row->event_date ?? null,
                    'created_at' => $row->created_at,
                ];
            }
        } catch (Throwable $e) {
            // If event tables are missing in a local setup, skip gracefully.
        }

        // Generic bookmarks: posts / messages / other
        $this->ensureGenericBookmarksTable();
        $this->db->query(
            'SELECT
                b.bookmark_type,
                b.reference_id,
                b.title,
                b.url,
                b.metadata,
                b.created_at
             FROM bookmarks b
             WHERE b.user_id = :uid
             ORDER BY b.created_at DESC'
        );
        $this->db->bind(':uid', (int)$user_id);
        $generic = $this->db->resultSet();

        foreach ($generic as $g) {
            $type = strtolower((string)$g->bookmark_type);
            $subtitle = '';
            $description = '';
            $title = $g->title ?: ucfirst(rtrim($type, 's'));
            $url = $g->url;

            if ($type === 'posts') {
                try {
                    $this->db->query(
                        'SELECT p.content, p.created_at, u.name
                         FROM posts p
                         LEFT JOIN users u ON u.id = p.user_id
                         WHERE p.id = :pid
                         LIMIT 1'
                    );
                    $this->db->bind(':pid', (int)$g->reference_id);
                    $p = $this->db->single();
                    if ($p) {
                        $title = $p->name ? ('Post by ' . $p->name) : $title;
                        $description = (string)($p->content ?? '');
                        $subtitle = date('M d, Y h:i A', strtotime($p->created_at));
                    }
                } catch (Throwable $e) {
                    // keep fallback values
                }
                if (!$url) {
                    $url = '/mainfeed?post_id=' . (int)$g->reference_id;
                }
            } elseif ($type === 'messages') {
                try {
                    $this->db->query(
                        'SELECT m.message_text, m.message_time, u.name
                         FROM messages m
                         LEFT JOIN users u ON u.id = m.sender_id
                         WHERE m.message_id = :mid
                         LIMIT 1'
                    );
                    $this->db->bind(':mid', (int)$g->reference_id);
                    $m = $this->db->single();
                    if ($m) {
                        $title = $m->name ? ('Message from ' . $m->name) : $title;
                        $description = (string)($m->message_text ?? '');
                        $subtitle = date('M d, Y h:i A', strtotime($m->message_time));
                    }
                } catch (Throwable $e) {
                    // keep fallback values
                }
                if (!$url) {
                    $url = '/messages';
                }
            } else {
                $description = '';
                $subtitle = ucfirst($type);
            }

            $all[] = (object)[
                'bookmark_type' => $type,
                'reference_id' => (int)$g->reference_id,
                'title' => $title,
                'description' => $description,
                'subtitle' => $subtitle,
                'url' => $url,
                'event_date' => null,
                'created_at' => $g->created_at,
            ];
        }

        usort($all, function($a, $b) {
            return strtotime($b->created_at) <=> strtotime($a->created_at);
        });

        return $all;
    }
}

?>