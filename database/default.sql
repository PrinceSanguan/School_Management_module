-- Users table: stores information about all types of users (admins, teachers, students)
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    firstName VARCHAR(50) NOT NULL,
    lastName VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    phone VARCHAR(20),
    password VARCHAR(255) NOT NULL,
    userRole ENUM('admin', 'teacher', 'student') NOT NULL,
    changePassword ENUM('yes', 'no') DEFAULT 'no'
);

-- Announcements table: stores announcements that can be viewed by different roles
CREATE TABLE announcement (
    id INT AUTO_INCREMENT PRIMARY KEY,
    announcement VARCHAR(255) NOT NULL,
    image_path VARCHAR(255) DEFAULT NULL,
    view ENUM('student', 'teacher', 'studentTeacher') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Student LRN table: stores unique student information
CREATE TABLE studentLrn (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    lrn VARCHAR(20) UNIQUE NOT NULL,
    parent VARCHAR(50) NOT NULL,
    address VARCHAR(50) NOT NULL,
    number VARCHAR(50) NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Inserting an admin user for system access
INSERT INTO users (firstName, lastName, email, phone, password, userRole, changePassword)
VALUES ('admin', 'admin', 'admin@gmail.com', '09123456789', '$2y$12$8qGbpTMe/NFXUMNZbMB5Gu0SFlp/hOcbGb6yyhSdn6MxedBmK7Eta', 'admin', 'yes');

-- Section table: stores different sections/classes
CREATE TABLE section (
    id INT AUTO_INCREMENT PRIMARY KEY,
    section VARCHAR(255) NOT NULL
);

-- Subject table: stores subjects taught within a section
CREATE TABLE subject (
    id INT AUTO_INCREMENT PRIMARY KEY,
    section_id INT,
    subject VARCHAR(255) NOT NULL,
    FOREIGN KEY (section_id) REFERENCES section(id) ON DELETE CASCADE
);

-- Task table: store the task that connecting to the subject
CREATE TABLE task (
    id INT AUTO_INCREMENT PRIMARY KEY,
    subject_id INT NOT NULL,
    task_title VARCHAR(100) NOT NULL,
    content VARCHAR(255) DEFAULT NULL,
    image_path VARCHAR(255) DEFAULT NULL,
    deadline DATE NOT NULL,
    status ENUM('active', 'inactive') DEFAULT 'active',

    FOREIGN KEY (subject_id) REFERENCES subject(id) ON DELETE CASCADE
);

-- Subject images table : store pdf file
CREATE TABLE subject_images (
    id INT AUTO_INCREMENT PRIMARY KEY,
    subject_id INT NOT NULL,
    week ENUM('week1', 'week2', 'week3', 'week4') NOT NULL,
    status ENUM('unpublish', 'publish') DEFAULT 'publish',
    image_url VARCHAR(255) NOT NULL,

    FOREIGN KEY (subject_id) REFERENCES subject(id) ON DELETE CASCADE
);

-- TeacherSubject table: links teachers to subjects they teach
CREATE TABLE teacherSubject (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    subject_id INT NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (subject_id) REFERENCES subject(id) ON DELETE CASCADE
);

-- Events table: stores events with their dates
CREATE TABLE events (
    id INT AUTO_INCREMENT PRIMARY KEY,
    date DATE NOT NULL,
    title VARCHAR(255) NOT NULL
);

-- New table to link students to sections (one student can only belong to one section)
CREATE TABLE studentSection (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    section_id INT NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (section_id) REFERENCES section(id) ON DELETE CASCADE
);