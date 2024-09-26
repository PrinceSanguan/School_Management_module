<?php
include "../../database/database.php";
require "../../database/config.php";
require '../../vendor/autoload.php'; // Assuming PHPMailer is located here

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Get form data
$firstName = $_POST['firstName'] ?? '';
$lastName = $_POST['lastName'] ?? '';
$email = $_POST['email'] ?? '';
$phone = $_POST['phone'] ?? '';
$userRole = $_POST['userRole'] ?? '';
$password = $firstName . $lastName; // Concatenate first name and last name
$lrn = isset($_POST['lrn']) ? $_POST['lrn'] : null;
$parent = isset($_POST['parent']) ? $_POST['parent'] : null;
$address = isset($_POST['address']) ? $_POST['address'] : null;
$number = isset($_POST['number']) ? $_POST['number'] : null;

// Validate required fields
if (empty($firstName) || empty($lastName) || empty($email) || empty($phone) || empty($userRole)) {
    $_SESSION['error'] = 'All fields are required.';
    header("Location: ../../admin/account-approval.php");
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
    header("Location: ../../admin/account-approval.php");
    exit();
}

// Email does not exist, proceed with insertion
$emailCheckStmt->close();
$query = "INSERT INTO users (firstName, lastName, email, phone, userRole, password) VALUES (?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($query);
if (!$stmt) {
    $_SESSION['error'] = 'Failed to prepare statement: ' . $conn->error;
    $conn->close();
    header("Location: ../../admin/account-approval.php");
    exit();
}

$stmt->bind_param("ssssss", $firstName, $lastName, $email, $phone, $userRole, $hashedPassword);

if ($stmt->execute()) {
    $userId = $stmt->insert_id; // Get the ID of the newly inserted user

    // If user is a student, insert LRN into studentLrn table
    if ($userRole === 'student' && !empty($lrn)) {
        $lrnQuery = "INSERT INTO studentLrn (user_id, lrn, parent, address, number) VALUES (?, ?, ?, ?, ?)";
        $lrnStmt = $conn->prepare($lrnQuery);
        if (!$lrnStmt) {
            $_SESSION['error'] = 'Failed to prepare LRN statement: ' . $conn->error;
            $conn->close();
            header("Location: ../../admin/account-approval.php");
            exit();
        }
        // The type definition string is 'issss' for integer and five strings
        $lrnStmt->bind_param("issss", $userId, $lrn, $parent, $address, $number);
        $lrnStmt->execute();
        $lrnStmt->close();
    }

    // Send email notification
    $mail = new PHPMailer(true);

    // Configure PHPMailer
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com'; // Assuming Gmail
    $mail->SMTPAuth = true;
    $mail->Username = '3870852@gmail.com'; // Replace with your Gmail address
    $mail->Password = 'hrnj rgts pqhe atpb'; // Replace with your Gmail app password
    $mail->SMTPSecure = 'tls';
    $mail->Port = 587;

    // Email content
    $mail->setFrom('3870852@gmail.com', 'SPED SCHOOL');
    $mail->addAddress($email, $firstName . ' ' . $lastName); // Send to user's email

    $mail->isHTML(true); // Set email format to HTML

    $mail->Subject = 'Your Account Has Been Created';
    $mail->Body = "
        <h1>Welcome, {$firstName} {$lastName}!</h1>
        <p>Your account has been successfully created. Below are your login details:</p>
        <p><strong>Email:</strong> {$email}</p>
        <p><strong>Temporary Password:</strong> {$password}</p>
        <p>Please change your password after your first login.</p>
        <br><br>
        <p>Best regards,</p>
        <p>SPED school</p>
    ";

    // Send the email
    if (!$mail->send()) {
        $_SESSION['error'] = 'Failed to send email: ' . $mail->ErrorInfo;
    } else {
        $_SESSION['success'] = 'Registration successful! An email with your credentials has been sent.';
    }

    header("Location: ../../admin/account-approval.php");
    exit();

} else {
    $_SESSION['error'] = 'Failed to create User: ' . $stmt->error;
    $stmt->close();
    $conn->close();
    header("Location: ../../admin/account-approval.php");
    exit();
}

$stmt->close();
$conn->close();
?>
