CREATE DATABASE ecard_system;
USE ecard_system;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'moderator') NOT NULL,
    reset_token VARCHAR(255) DEFAULT NULL,
    reset_expiry DATETIME DEFAULT NULL
);

CREATE TABLE cards (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    image VARCHAR(255) NOT NULL,
    tags VARCHAR(255) NOT NULL,
    send_count INT DEFAULT 0,
    rating_total INT DEFAULT 0,
    rating_count INT DEFAULT 0
);

CREATE TABLE logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    card_id INT,
    sender_ip VARCHAR(45),
    recipient_email VARCHAR(255),
    send_date DATETIME,
    FOREIGN KEY (card_id) REFERENCES cards(id)
);

CREATE TABLE ideas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    content TEXT NOT NULL,
    submitter_email VARCHAR(255),
    submit_date DATETIME,
    reviewed TINYINT DEFAULT 0
);

INSERT INTO users (email, password, role) VALUES ('admin@example.com', '$2y$10$examplehashedpassword', 'admin');
INSERT INTO cards (title, image, tags) VALUES ('Happy Birthday', 'birthday.jpg', 'birthday,celebration');