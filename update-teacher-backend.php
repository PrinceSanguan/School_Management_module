<?php
// Database connection details
include 'db_connect.php';

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = intval($_POST['id']);
    $first_name = $conn->real_escape_string(trim($_POST['first_name']));
    $last_name = $conn->real_escape_string(trim($_POST['last_name']));
    $email = $conn->real_escape_string(trim($_POST['email']));
    $contact_number = $conn->real_escape_string(trim($_POST['contact_number']));
    $password = trim($_POST['password']); // New password field

    // Check if email already exists for another teacher
    $email_check_sql = "SELECT id FROM teachers WHERE email = ? AND id != ?";
    $email_stmt = $conn->prepare($email_check_sql);
    $email_stmt->bind_param("si", $email, $id);
    $email_stmt->execute();
    $email_stmt->store_result();

    if ($email_stmt->num_rows > 0) {
        // Email exists
        header("Location: update-teacher.php?id=$id&status=email_exists");
        exit();
    } else {
        // Prepare the update statement
        $sql = "UPDATE teachers SET first_name = ?, last_name = ?, email = ?, contact_number = ?";
        
        // Add password update conditionally
        if (!empty($password)) {
            // Hash the new password
            $hashed_password = password_hash($password, PASSWORD_BCRYPT);
            $sql .= ", password = ?";
        }

        $sql .= " WHERE id = ?";
        $stmt = $conn->prepare($sql);

        if ($stmt === false) {
            die("Prepare failed: " . $conn->error);
        }

        // Bind parameters
        if (!empty($password)) {
            $stmt->bind_param("sssssi", $first_name, $last_name, $email, $contact_number, $hashed_password, $id);
        } else {
            $stmt->bind_param("ssssi", $first_name, $last_name, $email, $contact_number, $id);
        }

        if ($stmt->execute()) {
            // Redirect with success message
            header("Location: teachers-admin.php?status=updated");
        } else {
            // Redirect with error message
            header("Location: teachers-admin.php?status=error");
        }

        $stmt->close();
    }

    $email_stmt->close();
}

$conn->close();
?>
