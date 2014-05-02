
/* create threads table */
CREATE TABLE IF NOT EXISTS %table_prefix%forum_threads (
    id int NOT NULL AUTO_INCREMENT,
    name VARCHAR(511) NOT NULL,
    description TEXT,
    date_created TIMESTAMP NOT NULL DEFAULT 0,
    date_edited TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    status int DEFAULT 0,
    flags int DEFAULT 0,
    creator_id int NOT NULL,
    board_id int NOT NULL,
    reply_count int DEFAULT 0, /* Count the number of replies */
    
    PRIMARY KEY (id),
    FOREIGN KEY (creator_id) REFERENCES %table_prefix%users (id),
    FOREIGN KEY (board_id) REFERENCES %table_prefix%form_boards (id)
);
