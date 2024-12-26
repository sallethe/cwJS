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
    width INT,
    height INT,
    grid JSON,
    words JSON,
    FOREIGN KEY (creator) REFERENCES USERS(id)
) ENGINE = InnoDB;