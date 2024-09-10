<?php
include "../../database/database.php";
require "../../database/config.php";
session_start(); // Ensure sessions are started

// Get form data
$firstName = $_POST['firstName'] ?? '';
$lastName = $_POST['lastName'] ?? '';
$email = $_POST['email'] ?? '';
$phone = $_POST['phone'] ?? '';
$userRole = $_POST['userRole'] ?? '';
$password = $firstName . $lastName; // Concatenate first name and last name
$lrn = isset($_POST['lrn']) ? $_POST['lrn'] : null;

// Validate required fields
if (empty($firstName) || empty($lastName) || empty($email) || empty($phone) || empty($userRole)) {
    $_SESSION['error'] = 'All fields are required.';
    header("Location: /school-management/admin/account-approval.php");
    exit();
}

// Hash the password
$hashedPassword = password_hash($password, PASSWORD_BCRYPT);

// Check if the email already exists
$emailCheckQuery = "SELECT id FROM users WHERE email = ?";
$emailCheckStmt = $conn->prepare($emailCheckQuery);
$emailCheckStmt->bind_param("s", $email);
$emailCheckStmt->execute();
$emailCheckStmt->store_result();

if ($emailCheckStmt->num_rows > 0) {
    // Email already exists
    $_SESSION['error'] = 'The email address is already taken.';
    $emailCheckStmt->close();
    $conn->close();
    header("Location: /school-management/admin/account-approval.php");
    exit();
}

// Email does not exist, proceed with insertion
$emailCheckStmt->close();
$query = "INSERT INTO users (firstName, lastName, email, phone, userRole, password) VALUES (?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($query);
if (!$stmt) {
    $_SESSION['error'] = 'Failed to prepare statement: ' . $conn->error;
    $conn->close();
    header("Location: /school-management/admin/account-approval.php");
    exit();
}

$stmt->bind_param("ssssss", $firstName, $lastName, $email, $phone, $userRole, $hashedPassword);

if ($stmt->execute()) {
    $userId = $stmt->insert_id; // Get the ID of the newly inserted user

    // If user is a student, insert LRN into studentLrn table
    if ($userRole === 'student' && !empty($lrn)) {
        $lrnQuery = "INSERT INTO studentLrn (user_id, lrn) VALUES (?, ?)";
        $lrnStmt = $conn->prepare($lrnQuery);
        if (!$lrnStmt) {
            $_SESSION['error'] = 'Failed to prepare LRN statement: ' . $conn->error;
            $conn->close();
            header("Location: /school-management/admin/account-approval.php");
            exit();
        }
        $lrnStmt->bind_param("is", $userId, $lrn);
        $lrnStmt->execute();
        $lrnStmt->close();
    }

    $_SESSION['success'] = 'Registration successful!';
    header("Location: /school-management/admin/account-approval.php");
    exit();
} else {
    $_SESSION['error'] = 'Failed to create User: ' . $stmt->error;
    $stmt->close();
    $conn->close();
    header("Location: /school-management/admin/account-approval.php");
    exit();
}

$stmt->close();
$conn->close();
?>