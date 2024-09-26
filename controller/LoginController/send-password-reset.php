<?php
date_default_timezone_set('Asia/Manila');

include "../../database/database.php";
require "../../database/config.php";
require '../../vendor/autoload.php'; // Assuming PHPMailer is located here

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if (isset($_POST['submit'])) {
    $email = $_POST['email'];

    // Query to find the user based on their email
    $stmt = $conn->prepare("SELECT id, email FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        // Fetch the user data
        $user = $result->fetch_assoc();
        $userId = $user['id'];

        // Generate a secure token (recommended instead of userId in the URL)
        $token = bin2hex(random_bytes(50)); // Generates a 100-character random token
        
        // Store the token in a password_resets table
        $expire_time = date("Y-m-d H:i:s", strtotime('+1 hour')); // Set expiration for the token
        $stmt = $conn->prepare("INSERT INTO password_resets (user_id, token, expire_at) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $userId, $token, $expire_time);
        $stmt->execute();

        // Prepare the password reset link
        $resetLink = "http://localhost:3000/reset-password.php?token=" . $token;

        // Send the email using PHPMailer
        $mail = new PHPMailer(true);
        try {
           // Configure PHPMailer
          $mail->isSMTP();
          $mail->Host = 'smtp.gmail.com'; // Assuming Gmail
          $mail->SMTPAuth = true;
          $mail->Username = '3870852@gmail.com'; // Replace with your Gmail address
          $mail->Password = 'zlfb cacx zpdu vhlo'; // Replace with your Gmail app password
          $mail->SMTPSecure = 'tls';
          $mail->Port = 587;                     // TCP port to connect to

            // Recipients
            $mail->setFrom('3870852@gmail.com', 'SPEED School');
            $mail->addAddress($email);                      // Add user's email

            // Content
            $mail->isHTML(true);                            // Set email format to HTML
            $mail->Subject = 'Reset your password';
            $mail->Body    = "Hi, <br><br> Click the link below to reset your password: <br>
                              <a href='$resetLink'>Reset Password</a><br><br>
                              If you did not request a password reset, please ignore this email.";

            $mail->send();
            $_SESSION['success'] = 'The reset password link is send to your email!';
              header("Location: ../../login.php");
        } catch (Exception $e) {
            echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
    } else {
        echo 'No account found with that email address.';
    }
}