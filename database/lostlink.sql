CREATE DATABASE IF NOT EXISTS lostlink 
    CHARACTER SET utf8mb4 
    COLLATE utf8mb4_unicode_ci;

USE lostlink;

CREATE TABLE users (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(60) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    department VARCHAR(100) NOT NULL,
    semester VARCHAR(20) NOT NULL,
    batch VARCHAR(20) NOT NULL,
    enrollment_no VARCHAR(50) NOT NULL UNIQUE,
    phone VARCHAR(25) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE items (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    iu_number VARCHAR(20) NOT NULL UNIQUE,
    user_id INT UNSIGNED NOT NULL,
    type ENUM('LOST', 'FOUND') NOT NULL,
    item_name VARCHAR(150) NOT NULL,
    category VARCHAR(100) NOT NULL,
    description TEXT NOT NULL,
    location VARCHAR(200) NOT NULL,
    event_date DATE NOT NULL,
    event_time TIME NULL,
    phone VARCHAR(25) NOT NULL,
    image_path VARCHAR(255) NULL,
    status ENUM('OPEN', 'RESOLVED') NOT NULL DEFAULT 'OPEN',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_item_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_items_type (type),
    INDEX idx_items_search (item_name, location, category)
) ENGINE=InnoDB;

CREATE TABLE messages (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    item_id INT UNSIGNED NOT NULL,
    sender_id INT UNSIGNED NOT NULL,
    receiver_id INT UNSIGNED NOT NULL,
    message TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    is_read TINYINT(1) NOT NULL DEFAULT 0,
    CONSTRAINT fk_message_item FOREIGN KEY (item_id) REFERENCES items(id) ON DELETE CASCADE,
    CONSTRAINT fk_message_sender FOREIGN KEY (sender_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_message_receiver FOREIGN KEY (receiver_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_message_thread (item_id, sender_id, receiver_id)
) ENGINE=InnoDB;

CREATE TABLE notifications (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    title VARCHAR(150) NOT NULL,
    message VARCHAR(255) NOT NULL,
    related_item_id INT UNSIGNED NULL,
    is_read TINYINT(1) NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_notification_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_notification_item FOREIGN KEY (related_item_id) REFERENCES items(id) ON DELETE SET NULL
) ENGINE=InnoDB;
