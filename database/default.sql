CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    firstName VARCHAR(50) NOT NULL,
    lastName VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    phone VARCHAR(20),
    password VARCHAR(255) NOT NULL,
    userRole ENUM('admin', 'teacher', 'student') NOT NULL
);


INSERT INTO users (firstName, lastName, email, phone, password, userRole)
VALUES ('admin', 'admin', 'admin@gmail.com', '09123456789', '$2y$12$8qGbpTMe/NFXUMNZbMB5Gu0SFlp/hOcbGb6yyhSdn6MxedBmK7Eta', 'admin');