<?php
// Include database connection details
include 'db_connect.php';

// Create a new database connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['user_id'])) {
    // Sanitize and validate input
    $id = intval($_POST['user_id']);
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $email = trim($_POST['email']);
    $lrn = trim($_POST['lrn']);
    $parent_name = trim($_POST['parent_name']);
    $parent_email = trim($_POST['parent_email']);
    $parent_contact = trim($_POST['parent_contact']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);

    // Check for email duplication
    $email_check_sql = "SELECT id FROM users WHERE email = ? AND id != ?";
    $email_stmt = $conn->prepare($email_check_sql);
    $email_stmt->bind_param("si", $email, $id);
    $email_stmt->execute();
    $email_result = $email_stmt->get_result();
    
    if ($email_result->num_rows > 0) {
        // Email already exists
        $email_stmt->close();
        $conn->close();
        header("Location: main.php?id=$id&status=email_exists");
        exit();
    }
    $email_stmt->close();

    if (!empty($password)) {
        // If password is provided, hash it and include it in the update
        if ($password !== $confirm_password) {
            // Redirect if passwords do not match
            $conn->close();
            header("Location: main.php?id=$id&status=password_mismatch");
            exit();
        }
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $update_sql = "UPDATE users SET first_name = ?, last_name = ?, email = ?, lrn = ?, parent_name = ?, parent_email = ?, parent_contact = ?, password = ? WHERE id = ?";
        $stmt = $conn->prepare($update_sql);
        $stmt->bind_param("ssssssssi", $first_name, $last_name, $email, $lrn, $parent_name, $parent_email, $parent_contact, $hashed_password, $id);
    } else {
        // If no password is provided, exclude it from the update
        $update_sql = "UPDATE users SET first_name = ?, last_name = ?, email = ?, lrn = ?, parent_name = ?, parent_email = ?, parent_contact = ? WHERE id = ?";
        $stmt = $conn->prepare($update_sql);
        $stmt->bind_param("sssssssi", $first_name, $last_name, $email, $lrn, $parent_name, $parent_email, $parent_contact, $id);
    }

    // Execute the update statement
    if ($stmt->execute()) {
        // Redirect on successful update
        $stmt->close();
        $conn->close();
        header("Location: main.php?id=$id&status=updated");
    } else {
        // Redirect on failure
        $stmt->close();
        $conn->close();
        header("Location: main.php?id=$id&status=error");
    }
} else {
    // Redirect if the form was not submitted correctly
    header("Location: main.php?id=$id&status=error");
}
?>
