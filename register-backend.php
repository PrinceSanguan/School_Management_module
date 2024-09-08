<?php
session_start();
require_once 'db_connect.php';

// Get form data
$first_name = $_POST['first_name'];
$last_name = $_POST['last_name'];
$email = $_POST['email'];
$lrn = $_POST['lrn'];
$parent_name = $_POST['parent_name'];
$parent_email = $_POST['parent_email'];
$parent_contact = $_POST['parent_contact'];

// Use last name as the default password and hash it
$default_password = $last_name;
$hashed_password = password_hash($default_password, PASSWORD_DEFAULT);

// Check if email already exists
$query = "SELECT * FROM users WHERE email = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('s', $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $_SESSION['register_error'] = 'An account with this email already exists!';
    header('Location: register.php');
    exit();
}

// Insert new user into the database
$query = "INSERT INTO users (first_name, last_name, email, lrn, parent_name, parent_email, parent_contact, password) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($query);
$stmt->bind_param('ssssssss', $first_name, $last_name, $email, $lrn, $parent_name, $parent_email, $parent_contact, $hashed_password);

if ($stmt->execute()) {
    $_SESSION['register_success'] = "Registration successful! You can now log in. Your email is: $email and your default password is: $last_name";
    header('Location: register.php');
    exit();
} else {
    $_SESSION['register_error'] = 'Registration failed. Please try again.';
    header('Location: register.php');
    exit();
}
?>
