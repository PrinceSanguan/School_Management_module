<?php
session_start();

header('Content-Type: application/json');

// Database connection details
include 'db_connect.php';

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die(json_encode(['error' => 'Connection failed: ' . $conn->connect_error]));
}

// Initialize response array
$response = ['error' => '', 'success' => ''];

// Include PHPMailer
require 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve and sanitize form data
    $first_name = $conn->real_escape_string(trim($_POST['first_name']));
    $last_name = $conn->real_escape_string(trim($_POST['last_name']));
    $email = $conn->real_escape_string(trim($_POST['email']));
    $password = $conn->real_escape_string(trim($_POST['password']));
    $confirm_password = $conn->real_escape_string(trim($_POST['confirm_password']));
    $contact = $conn->real_escape_string(trim($_POST['contact']));
    $address = $conn->real_escape_string(trim($_POST['address']));

    // Check if passwords match
    if ($password !== $confirm_password) {
        $response['error'] = "Passwords do not match.";
    } else {
        // Hash the password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Check if email already exists
        $sql = "SELECT id FROM users WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $response['error'] = "Email is already registered.";
        } else {
            // SQL query to insert data
            $sql = "INSERT INTO users (first_name, last_name, email, password, contact, address) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssssss", $first_name, $last_name, $email, $hashed_password, $contact, $address);

            if ($stmt->execute()) {
                // Send confirmation email
                $mail = new PHPMailer(true);
                try {
                    // Server settings
                    $mail->isSMTP();
                    $mail->Host = 'smtp.gmail.com';
                    $mail->SMTPAuth = true;
                    $mail->Username = 'uzukealucard@gmail.com'; // Replace with your email
                    $mail->Password = 'bokl qqcu lppr itnn'; // Replace with your password or app password
                    $mail->SMTPSecure = 'tls';
                    $mail->Port = 587;

                    // Recipients
                    $mail->setFrom('uzukealucard@gmail.com');
                    $mail->addAddress($email, "$first_name $last_name");

                    // Content
                    $mail->isHTML(true);
                    $mail->Subject = 'Registration Successful';
                    $mail->Body = "
                    <html>
                    <head>
                        <style>
                            body {font-family: Arial, sans-serif; color: #333;}
                            .container {max-width: 600px; margin: auto; padding: 20px; border: 1px solid #ddd; border-radius: 5px;}
                            .header {background-color: #f8f8f8; padding: 10px; border-bottom: 1px solid #ddd;}
                            .footer {margin-top: 20px; font-size: 0.9em; color: #777;}
                        </style>
                    </head>
                    <body>
                        <div class='container'>
                            <div class='header'>
                                <h2>Welcome, $first_name $last_name!</h2>
                            </div>
                            <p>Thank you for registering at our site. We are thrilled to have you as a member.</p>
                            <p>Best Regards,<br>System Admin</p>
                            <div class='footer'>
                                <p>If you have any questions, feel free to <a href='mailto:systemadmin@example.com'>contact us</a>.</p>
                            </div>
                        </div>
                    </body>
                    </html>
                    ";
                    $mail->send();
                    $response['success'] = "Registered successfully!";
                } catch (Exception $e) {
                    $response['error'] = "Registered successfully but email could not be sent. Mailer Error: {$mail->ErrorInfo}";
                }
            } else {
                $response['error'] = "Error: " . $stmt->error;
            }
            $stmt->close();
        }
    }

    $conn->close();
    echo json_encode($response);
}
?>
