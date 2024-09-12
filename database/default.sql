CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    firstName VARCHAR(50) NOT NULL,
    lastName VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    phone VARCHAR(20),
    password VARCHAR(255) NOT NULL,
    userRole ENUM('admin', 'teacher', 'student') NOT NULL
);

CREATE TABLE announcement (
    id INT AUTO_INCREMENT PRIMARY KEY,
    announcement VARCHAR(255) NOT NULL,
    view ENUM('student', 'teacher', 'studentTeacher') NOT NULL
);


CREATE TABLE studentLrn (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    lrn VARCHAR(20) UNIQUE NOT NULL,
    parent VARCHAR(50) NOT NULL,
    address VARCHAR(50) NOT NULL,
    number VARCHAR(50) NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);


INSERT INTO users (firstName, lastName, email, phone, password, userRole)
VALUES ('admin', 'admin', 'admin@gmail.com', '09123456789', '$2y$12$8qGbpTMe/NFXUMNZbMB5Gu0SFlp/hOcbGb6yyhSdn6MxedBmK7Eta', 'admin');

CREATE TABLE section (
    id INT AUTO_INCREMENT PRIMARY KEY,
    section VARCHAR(255) NOT NULL
);

CREATE TABLE subject (
    id INT AUTO_INCREMENT PRIMARY KEY,
    section_id INT,
    subject VARCHAR(255) NOT NULL,
    FOREIGN KEY (section_id) REFERENCES section(id) ON DELETE CASCADE
);

CREATE TABLE subject_images (
    id INT AUTO_INCREMENT PRIMARY KEY,
    subject_id INT NOT NULL,
    week ENUM('week1', 'week2', 'week3', 'week4') NOT NULL,
    image_url VARCHAR(255) NOT NULL,  -- This will store the path to the image file
    FOREIGN KEY (subject_id) REFERENCES subject(id) ON DELETE CASCADE
);