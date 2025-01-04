CREATE USER 'cwjs'@'localhost' IDENTIFIED BY 'wed0nt0wethemn0rmal';
CREATE DATABASE CWJS;
USE CWJS;
GRANT CREATE, ALTER, DROP, INSERT, UPDATE, DELETE, SELECT on CWJS.* TO 'cwjs'@'localhost' WITH GRANT OPTION;

CREATE TABLE USERS (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(32),
    first_name VARCHAR(32),
    last_name VARCHAR(32),
    pwd_hash VARCHAR(256) ,
    role SMALLINT
) ENGINE = InnoDB;

CREATE TABLE GRIDS (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(64),
    creator INT,
    dim INT,
    difficulty SMALLINT,
    grid TEXT,
    words TEXT,
    word_count INT,
    created DATE,
    FOREIGN KEY (creator) REFERENCES USERS(id)
) ENGINE = InnoDB;

INSERT INTO USERS (id, username, first_name, last_name, pwd_hash, role)
    VALUE (0, 'superuser', 'Florent', 'Nicart', '$2y$10$E8678n9tcpyWmYztgcxue.NIsgoUiX89iVkgeKChj9F9Dv1.Xo5WK', 1)

# root FUcbDXno1!9l!4