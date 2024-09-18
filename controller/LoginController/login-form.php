<?php
include "../../database/database.php";
require "../../database/config.php";

// Start the session
session_start();

if (isset($_POST['submit'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Create connection
    $conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Query to find the user
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();

    // Get result
    $result = $stmt->get_result();

    // Check if user exists
    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();
        $hashed_password = $user['password'];
        $role = $user['userRole'];
        $userId = $user['id']; // Assuming you might want to store the user ID as well

        // Verify the password
        if (password_verify($password, $hashed_password)) {
            // Set session variables
            $_SESSION['userRole'] = $role;
            $_SESSION['userId'] = $userId;
            $_SESSION['email'] = $email; // Optional: store the email if needed

            // Redirect based on user role
            if ($role == 'admin') {
                header("Location: ../../admin/account-approval.php");
                exit;
            } elseif ($role == 'student') {
                header("Location: ../../student/dashboard.php");
                exit;
            } elseif ($role == 'teacher') {
                header("Location: ../../teacher/dashboard.php");
                exit;
            } else {
                echo "Invalid user role!";
            }
        } else {
            $_SESSION['error'] = 'Invalid Email or Password';
            header("Location: ../../login.php");
        }
    } else {
        $_SESSION['error'] = 'Invalid Email or Password';
        header("Location: ../../login.php");
    }

    $stmt->close();
    $conn->close();
}
?>