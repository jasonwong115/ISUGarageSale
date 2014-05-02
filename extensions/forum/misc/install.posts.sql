/* Install the posts table */
CREATE TABLE IF NOT EXISTS %table_prefix%forum_posts (
    id int NOT NULL AUTO_INCREMENT,
    name VARCHAR(511) NOT NULL,
    post TEXT,
    date_created TIMESTAMP NOT NULL DEFAULT 0,
    date_edited TIMESTAMP DEFAULT CURRENT_TIMESTAMP 
        ON UPDATE CURRENT_TIMESTAMP,
    status int DEFAULT 0,
    flags int DEFAULT 0,
    creator_id int NOT NULL,
    thread_id int NOT NULL,
    
    PRIMARY KEY (id),
    FOREIGN KEY (creator_id) REFERENCES %table_prefix%users (id),
    FOREIGN KEY (thread_id) REFERENCES %table_prefix%forum_threads (id)
);
