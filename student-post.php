<?php
// Database connection details
include 'db_connect.php';

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if form data is set
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $email = trim($_POST['email']);
    $parent_name = trim($_POST['parent_name']);
    $parent_email = trim($_POST['parent_email']);
    $parent_contact = trim($_POST['parent_contact']);

    // Check for duplicate email
    $check_sql = "SELECT id FROM students WHERE email = ? OR parent_email = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("ss", $email, $parent_email);
    $check_stmt->execute();
    $check_stmt->store_result();

    if ($check_stmt->num_rows > 0) {
        // Email already exists
        echo "duplicate";
        exit();
    }

    $check_stmt->close();

    // Prepare and execute insert statement
    $sql = "INSERT INTO students (first_name, last_name, email, parent_name, parent_email, parent_contact) VALUES (?,?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);

    if ($stmt === false) {
        die("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param("ssssss", $first_name, $last_name, $email, $parent_name, $parent_email,$parent_contact);
    $result = $stmt->execute();

    if ($result) {
        // Success
        echo "success";
    } else {
        // Error
        echo "error";
    }

    $stmt->close();
    $conn->close();
}

