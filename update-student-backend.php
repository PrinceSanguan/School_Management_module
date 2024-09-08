<?php
include 'db_connect.php';

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve and sanitize form data
    $id = intval($_POST['id']);
    $first_name = $conn->real_escape_string(trim($_POST['first_name']));
    $last_name = $conn->real_escape_string(trim($_POST['last_name']));
    $email = $conn->real_escape_string(trim($_POST['email']));
    $parent_name = $conn->real_escape_string(trim($_POST['parent_name']));
    $parent_email = $conn->real_escape_string(trim($_POST['parent_email']));
    $parent_contact = $conn->real_escape_string(trim($_POST['parent_contact']));
    // Check if email already exists
    $email_check_sql = "SELECT id FROM students WHERE email = ? AND id != ?";
    $email_stmt = $conn->prepare($email_check_sql);
    $email_stmt->bind_param("si", $email, $id);
    $email_stmt->execute();
    $email_stmt->store_result();

    if ($email_stmt->num_rows > 0) {
        // Email already exists
        header("Location: update-student.php?id=$id&status=email_exists");
    } else {
        // Prepare and execute update statement
        $sql = "UPDATE students SET first_name = ?, last_name = ?, email = ?, parent_name = ?, parent_email = ?, parent_contact = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssssi", $first_name, $last_name, $email, $parent_name, $parent_email, $parent_contact, $id);

        if ($stmt->execute()) {
            // Redirect with success message
            header("Location: student-admin.php?id=$id&status=updated");
        } else {
            // Redirect with error message
            header("Location: update-student.php?id=$id&status=error");
        }

        $stmt->close();
    }

    $email_stmt->close();
}

$conn->close();
