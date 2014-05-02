/* Table to hold the list of users and their status as moderators of the
 * forum board.
 */
CREATE TABLE IF NOT EXISTS %table_prefix%forum_moderators (
    id int NOT NULL AUTO_INCREMENT,
    user_id int NOT NULL,
    board_id int NOT NULL,
    status int NOT NULL DEFAULT 0,
    flags int NOT NULL DEFAULT 0,
    
    PRIMARY KEY (id),
    FOREIGN KEY (user_id) REFERENCES %table_prefix%users (id),
    FOREIGN KEY (board_id) REFERENCES %table_prefix%forum_boards (id)
);
