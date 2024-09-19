<?php
include "../../database/database.php";
require "../../database/config.php";

if (isset($_POST['submit'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

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
        $changePassword = $user['changePassword'];  // This will be 'yes' or 'no'
        $userId = $user['id']; 

        // Verify the password
        if (password_verify($password, $hashed_password)) {
            // Set session variables
            $_SESSION['userRole'] = $role;
            $_SESSION['userId'] = $userId;
            $_SESSION['email'] = $email; 
            $_SESSION['changePassword'] = $changePassword; 

            // If password change is required (changePassword is 'no'), redirect to change_password page
            if ($changePassword === 'no') {
                $_SESSION['success'] = "Change the password first before you proceed.";
                header("Location: change_password.php"); // Adjust the path if needed
                exit();
            }

            // If the password has been changed (changePassword is 'yes'), redirect to the respective dashboard
            if ($changePassword === 'yes') {
                if ($role == 'admin') {
                    header("Location: ../../admin/account-approval.php");
                    exit();
                } elseif ($role == 'student') {
                    header("Location: ../../student/announcement.php");
                    exit();
                } elseif ($role == 'teacher') {
                    header("Location: ../../teacher/announcement.php");
                    exit();
                } else {
                    echo "Invalid user role!";
                }
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
