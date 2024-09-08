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
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id'])) {
    $id = intval($_POST['id']);
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $email = trim($_POST['email']);
    $lrn = trim($_POST['lrn']);
    $parent_name = trim($_POST['parent_name']);
    $parent_email = trim($_POST['parent_email']);
    $parent_contact = trim($_POST['parent_contact']);
    $section = trim($_POST['section']);
    $password = trim($_POST['password']);

    // Check for duplicate email
    $email_check_sql = "SELECT id FROM users WHERE email = ? AND id != ?";
    $email_stmt = $conn->prepare($email_check_sql);
    $email_stmt->bind_param("si", $email, $id);
    $email_stmt->execute();
    $email_result = $email_stmt->get_result();

    if ($email_result->num_rows > 0) {
        // Email already exists
        $email_stmt->close();
        $conn->close();
        header("Location: update-users.php?id=$id&status=email_exists");
        exit();
    }
    $email_stmt->close();

    // Prepare the update statement
    if (!empty($password)) {
        // Hash the new password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $update_sql = "UPDATE users SET first_name = ?, last_name = ?, email = ?, lrn = ?, parent_name = ?, parent_email = ?, parent_contact = ?, section = ?, password = ? WHERE id = ?";
        $stmt = $conn->prepare($update_sql);
        $stmt->bind_param("sssssssssi", $first_name, $last_name, $email, $lrn, $parent_name, $parent_email, $parent_contact, $section, $hashed_password, $id);
    } else {
        // If no password is provided, exclude it from the update
        $update_sql = "UPDATE users SET first_name = ?, last_name = ?, email = ?, lrn = ?, parent_name = ?, parent_email = ?, parent_contact = ?, section = ? WHERE id = ?";
        $stmt = $conn->prepare($update_sql);
        $stmt->bind_param("ssssssssi", $first_name, $last_name, $email, $lrn, $parent_name, $parent_email, $parent_contact, $section, $id);
    }

    // Execute the update statement
    if ($stmt->execute()) {
        // Redirect with success status
        header("Location: users-admin.php?status=updated");
    } else {
        // Redirect with error status
        header("Location: update-users.php?id=$id&status=error");
    }

    $stmt->close();
} else {
    // Redirect if ID not set or method is not POST
    header("Location: users-admin.php?status=error");
}

$conn->close();
?>
