CREATE TABLE IF NOT EXISTS reports (
    report_id INT AUTO_INCREMENT PRIMARY KEY,

    reporter_id INTEGER NOT NULL,
    report_type VARCHAR(20) NOT NULL,
    reported_item_id INTEGER NOT NULL,

    category VARCHAR(50) NOT NULL,
    details TEXT,
    link TEXT,

    status VARCHAR(20) DEFAULT 'pending',

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    reviewed_by INT,
    reviewed_at TIMESTAMP,

    FOREIGN KEY (reporter_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (reviewed_by) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_reporter (reporter_id),
    INDEX idx_report_type_item (report_type, reported_item_id),
    INDEX idx_status (status)
);