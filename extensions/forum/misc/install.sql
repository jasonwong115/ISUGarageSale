
/* create the boards group table */
CREATE TABLE IF NOT EXISTS %table_prefix%forum_groups (
    id int NOT NULL AUTO_INCREMENT,
    name CHAR(255) NOT NULL,
    description TEXT,
    date_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    group_order int DEFAULT 0,
    status int DEFAULT 0,
    PRIMARY KEY (id)
);

/* create the actual boards table */
CREATE TABLE IF NOT EXISTS %table_prefix%forum_boards (
    id int NOT NULL AUTO_INCREMENT,
    name CHAR(255) NOT NULL,
    description TEXT,
    date_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    board_order int DEFAULT 0,
    status int DEFAULT 0,
    group_id int NOT NULL,
    PRIMARY KEY(id),
    FOREIGN KEY(group_id) REFERENCES %table_prefix%forum_groups (id)
        ON DELETE CASCADE
);

/* create threads table */
CREATE TABLE IF NOT EXISTS %table_prefix%forum_threads (
    id int NOT NULL AUTO_INCREMENT,
    name CHAR(511) NOT NULL,
    description TEXT,
    date_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_edited TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status int DEFAULT 0,
    creator_id int NOT NULL,
    board_id int NOT NULL,
    reply_count int DEFAULT 0, /* Count the number of replies */
    
    PRIMARY KEY (id),
    FOREIGN KEY (creator_id) REFERENCES %table_prefix%users (id),
    FOREIGN KEY (board_id) REFERENCES %table_prefix%form_boards (id)
);

CREATE TABLE IF NOT EXISTS %table_prefix%forum_posts (
    id int NOT NULL AUTO_INCREMENT,
    name CHAR(511) NOT NULL,
    post TEXT,
    date_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_edited TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status int DEFAULT 0,
    flags int DEFAULT 0,
    creator_id int NOT NULL,
    thread_id int NOT NULL,
    
    PRIMARY KEY (id),
    FOREIGN KEY (creator_id) REFERENCES %table_prefix%users (id),
    FOREIGN KEY (thread_id) REFERENCES %table_prefix%form_threads (id)
);

/* Table to hold the list of users and their status as moderators of the
 * forum board.
 */
CREATE TABLE IF NOT EXISTS %table_prefix%forum_moderators (
    id int NOT NULL AUTO_INCREMENT,
    user_id int NOT NULL,
    board_id int NOT NULL,
    status int NOT NULL DEFAULT 0,
    flags int NOT NULL DEFAULT 0
    
    PRIMARY KEY (id),
    FOREIGN KEY (user_id) REFERENCES %table_prefix%users (id),
    FOREIGN KEY (board_id) REFERENCES %table_prefix%form_boards (id)
);
