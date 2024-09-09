<?php
include "../../database/database.php";
require "../../database/config.php";

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

        // Verify the password
        if (password_verify($password, $hashed_password)) {
            // Redirect based on user role
            if ($role == 'admin') {
                header("Location: /school-management/admin/account-approval.php");
                exit;
            } elseif ($role == 'student') {
                header("Location: /school-management/student/dashboard.php");
                exit;
            } elseif ($role == 'teacher') {
                header("Location: /school-management/teacher/dashboard.php");
                exit;
            } else {
                echo "Invalid user role!";
            }
        } else {
            echo "Invalid email or password!";
        }
    } else {
        echo "Invalid email or password!";
    }

    $stmt->close();
    $conn->close();
}
?>