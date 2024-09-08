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
    $contact_number = trim($_POST['contact_number']);

    // Generate default password
    $default_password = $last_name;

    // Hash the password using bcrypt
    $hashed_password = password_hash($default_password, PASSWORD_BCRYPT);

    // Check for duplicate email
    $check_sql = "SELECT id FROM teachers WHERE email = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("s", $email);
    $check_stmt->execute();
    $check_stmt->store_result();

    if ($check_stmt->num_rows > 0) {
        // Email already exists
        echo "duplicate";
        exit();
    }

    $check_stmt->close();

    // Prepare and execute insert statement
    $sql = "INSERT INTO teachers (first_name, last_name, email, contact_number, password) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);

    if ($stmt === false) {
        die("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param("sssss", $first_name, $last_name, $email, $contact_number, $hashed_password);
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
?>
