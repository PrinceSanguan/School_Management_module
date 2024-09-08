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
    // Collect and sanitize form data
    $first_name = $conn->real_escape_string(trim($_POST['first_name']));
    $last_name = $conn->real_escape_string(trim($_POST['last_name']));
    $email = $conn->real_escape_string(trim($_POST['email']));
    $lrn = $conn->real_escape_string(trim($_POST['lrn']));
    $parent_name = $conn->real_escape_string(trim($_POST['parent_name']));
    $parent_email = $conn->real_escape_string(trim($_POST['parent_email']));
    $parent_contact = $conn->real_escape_string(trim($_POST['parent_contact']));
    
    // Default password is last name
    $password = $last_name;
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Check if email already exists
    $email_check_sql = "SELECT id FROM users WHERE email = ?";
    if ($email_stmt = $conn->prepare($email_check_sql)) {
        $email_stmt->bind_param("s", $email);
        $email_stmt->execute();
        $email_stmt->store_result();

        if ($email_stmt->num_rows > 0) {
            // Email exists
            echo 'duplicate';
        } else {
            // Prepare and execute insert statement
            $sql = "INSERT INTO users (first_name, last_name, email, lrn, parent_name, parent_email, parent_contact, password) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            if ($stmt = $conn->prepare($sql)) {
                $stmt->bind_param("ssssssss", $first_name, $last_name, $email, $lrn, $parent_name, $parent_email, $parent_contact, $hashed_password);

                if ($stmt->execute()) {
                    // Insert successful
                    echo 'success';
                } else {
                    // Error occurred during insert
                    echo 'error: ' . $stmt->error;
                }

                $stmt->close();
            } else {
                // Error preparing the SQL statement
                echo 'error: ' . $conn->error;
            }
        }

        $email_stmt->close();
    } else {
        // Error preparing the SQL statement
        echo 'error: ' . $conn->error;
    }

    $conn->close();
}
?>
