CREATE TABLE poll (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(1000),
    description VARCHAR(1000),
    type INT REFERENCES poll_type(id),
    start_date DATETIME,
    end_date DATETIME,
    max_votes INT
);

CREATE TABLE user (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(20)
);

CREATE TABLE vote (
    id INT AUTO_INCREMENT PRIMARY KEY,
    poll INT REFERENCES poll(id),
    user INT REFERENCES user(id),
    date DATETIME,
    option INT REFERENCES option(id),
    value VARCHAR(10)
);

CREATE TABLE option (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100)
);

CREATE TABLE poll_option (
    id INT AUTO_INCREMENT PRIMARY KEY,
    poll INT REFERENCES poll(id),
    option INT REFERENCES option(id)
);

CREATE TABLE poll_type (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100)
);



    
