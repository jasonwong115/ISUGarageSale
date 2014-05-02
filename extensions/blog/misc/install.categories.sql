/* create the actual boards table */
CREATE TABLE IF NOT EXISTS %table_prefix%blog_categories (
    id int NOT NULL AUTO_INCREMENT,
    name CHAR(255) NOT NULL,
    display_name CHAR(255) NOT NULL,
    description TEXT,
    date_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    category_order int DEFAULT 0,
    status int DEFAULT 0,
    PRIMARY KEY(id)
);
