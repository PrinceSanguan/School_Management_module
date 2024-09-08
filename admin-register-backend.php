<?php
session_start();

include 'db_connect.php';

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize error variable
$error = "";

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve and sanitize form data
    $email = $conn->real_escape_string(trim($_POST['email']));
    $password = $conn->real_escape_string(trim($_POST['password']));
    $confirm_password = $conn->real_escape_string(trim($_POST['confirm_password']));
    $contact = $conn->real_escape_string(trim($_POST['contact']));
    $address = $conn->real_escape_string(trim($_POST['address']));

    // Validate form data
    if (empty($email) || empty($password) || empty($confirm_password) || empty($contact) || empty($address)) {
        $error = "All fields are required.";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match.";
    } else {
        // Check if an account already exists
        $sql = "SELECT * FROM admins";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            $error = "An account already exists.";
        } else {
            // Hash the password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // SQL query to insert data
            $sql = "INSERT INTO admins (email, password, contact, address) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssss", $email, $hashed_password, $contact, $address);

            // Execute query
            if ($stmt->execute()) {
                $_SESSION['register_success'] = "Registration successful!";
                header("Location: admin-login.php");
                exit();
            } else {
                $error = "Error: " . $stmt->error;
            }

            $stmt->close();
        }
    }

    $conn->close();
}

// Store the error message and form values in session for display
$_SESSION['register_error'] = $error;
$_SESSION['register_email'] = $email;
$_SESSION['register_contact'] = $contact;
$_SESSION['register_address'] = $address;

// Redirect back to registration page
header("Location: admin-register.php");
exit();

