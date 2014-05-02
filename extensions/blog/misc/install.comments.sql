/* create the actual boards table */
CREATE TABLE IF NOT EXISTS %table_prefix%blog_comments (
    id int NOT NULL AUTO_INCREMENT,
    name CHAR(255) NOT NULL,
    comment TEXT,
    date_created TIMESTAMP,
    date_edited TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,
    status int DEFAULT 0,
    post_id int DEFAULT 0,
    user_id int NOT NULL,
    PRIMARY KEY(id),
    FOREIGN KEY (post_id) 
        REFERENCES %table_prefix%blog_posts (id),
    FOREIGN KEY (user_id) 
        REFERENCES %table_prefix%users (id)
);
