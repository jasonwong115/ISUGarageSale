/* create the actual boards table */
CREATE TABLE IF NOT EXISTS %table_prefix%forum_boards (
    id int NOT NULL AUTO_INCREMENT,
    name CHAR(255) NOT NULL,
    description TEXT,
    date_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    board_order int DEFAULT 0,
    status int DEFAULT 0,
    thread_count INT UNSIGNED DEFAULT 0,
    post_count INT UNSIGNED DEFAULT 0,
    group_id int NOT NULL,
    PRIMARY KEY(id),
    FOREIGN KEY(group_id) REFERENCES %table_prefix%forum_groups (id)
        ON DELETE CASCADE
);
