/* create the actual boards table */
CREATE TABLE IF NOT EXISTS %table_prefix%blog_posts (
    id int NOT NULL AUTO_INCREMENT,
    name CHAR(255) NOT NULL,
    slug CHAR(255) NOT NULL,
    post TEXT,
    tags TEXT,
    date_created TIMESTAMP,
    date_edited TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,
    status int DEFAULT 0,
    category_id int DEFAULT 0,
    user_id int NOT NULL,
    PRIMARY KEY(id),
    FOREIGN KEY (category_id) 
        REFERENCES %table_prefix%blog_categories (id),
    FOREIGN KEY (user_id) 
        REFERENCES %table_prefix%users (id)
);
