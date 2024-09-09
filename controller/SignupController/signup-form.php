<?php
include "../../database/database.php";
require "../../database/config.php";

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
        echo "Passwords do not match.";
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
        // Redirect to the login page if successful
        header("Location: /school-management/login.php");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }

    // Close the statement and connection
    $stmt->close();
    $conn->close();
}
?>