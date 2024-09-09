<?php
include "../../database/database.php";
require "../../database/config.php";

// Start the session
session_start();

if (isset($_POST['submit'])) {
    // Get and sanitize input
    $firstname = htmlspecialchars($_POST['firstName']);
    $lastname = htmlspecialchars($_POST['lastName']);
    $email = htmlspecialchars($_POST['email']);
    $password = htmlspecialchars($_POST['password']);
    $cpassword = htmlspecialchars($_POST['cpassword']);
    $userrole = htmlspecialchars($_POST['userrole']);
    
    // Validate passwords match
    if ($password !== $cpassword) {
        $_SESSION['error'] = 'Your passwords do not match.';
        header("Location: /school-management/signup.php");
        exit();
    }

    // Hash the password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Prepare and bind
    $stmt = $conn->prepare("INSERT INTO users (firstname, lastname, email, phone, password, userrole, status) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $status = 'inactive'; // Default status
    $phone = ''; // Optional, if you have a phone field in your form, update accordingly

    $stmt->bind_param("sssssss", $firstname, $lastname, $email, $phone, $hashedPassword, $userrole, $status);

    // Execute the statement
    if ($stmt->execute()) {
        $_SESSION['success'] = 'Registration successful! Please wait for your account activation.';
        header("Location: /school-management/login.php");
        exit();
    } else {
        $_SESSION['error'] = 'An error occurred: ' . $stmt->error;
        header("Location: /school-management/signup.php");
        exit();
    }

    // Close the statement and connection
    $stmt->close();
    $conn->close();
}
?>