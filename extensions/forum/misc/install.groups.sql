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
