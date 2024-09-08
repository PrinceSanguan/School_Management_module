<?php
// Include PHPMailer autoload file
require 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Database connection
include 'db_connect.php';

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];

    // Check if email exists
    $sql = "SELECT id FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $userId = $user['id'];

        // Generate a unique token and expiration
        $token = bin2hex(random_bytes(50));
        $expires = date("Y-m-d H:i:s", strtotime('+1 hour'));

        // Store token in database
        $sql = "INSERT INTO password_resets (user_id, token, expires) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE token = VALUES(token), expires = VALUES(expires)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iss", $userId, $token, $expires);
        $stmt->execute();

        // Send password reset email
        $resetLink = "http://school-management.free.nf/reset-password.php?token=" . $token; // Use localhost for local testing
        //http://school-management.free.nf/reset-password.php
        //http://localhost/main/reset-password.php
        $mail = new PHPMailer(true);

        try {
            //Server settings
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com'; // Set the SMTP server to send through
            $mail->SMTPAuth = true;
            $mail->Username = 'uzukealucard@gmail.com'; // SMTP username
            $mail->Password = 'bokl qqcu lppr itnn'; // SMTP App Password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            //Recipients
            $mail->setFrom('uzukealucard@gmail.com', 'Password Reset');
            $mail->addAddress($email);

            // Content
            $mail->isHTML(true);
            $mail->Subject = 'Password Reset Request';
            $mail->Body    = "To reset your password, please click the following link: <a href='$resetLink'>$resetLink</a>";

            $mail->send();
            $success = 'A password reset link has been sent to your email address.';
        } catch (Exception $e) {
            $error = "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
    } else {
        $error = "No user found with this email.";
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Request Password Reset</title>
    <link rel="icon" href="images/logo.webp" type="image/x-icon">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="https://fonts.googleapis.com/css2?family=Anton&family=Baskervville+SC&family=Nerko+One&family=New+Amsterdam&display=swap" rel="stylesheet">
    <style>
        html, body {
            margin: 0;
            padding: 0;
            font-family: "New Amsterdam", sans-serif;
            background-color: #0D1B2A;
        }

        .container {
            align-items: center;
            justify-content: center;
        }


        .form-label, .register-section h2 {
            color: #1B263B; /* Darker text for contrast */
        }

        .form-control {
            background-color: #415A77;
            border: 1px solid #415A77;
            color: white;
        }

        .form-control:focus {
            background-color: #415A77;
            border-color: #1B263B;
            color: white;
        }
        .form-container{
            padding: 20px 20px;
            background-color: white;
        }

        .btn-primary {
            background-color: #1F7A8C;
            border: none;
            width: 100%;
        }

        .btn-primary:hover {
            background-color: #1F6F7A;
        }

        a {
            color: #1F7A8C;
        }

        a:hover {
            color: #1F6F7A;
            text-decoration: none;
        }

        .text-center a {
            color: #1F7A8C;
            text-decoration: none;
        }

        .form-select {
            background-color: #415A77;
            border: 1px solid #415A77;
            color: white;
        }   

        .form-select:focus {
            background-color: #415A77;
            border-color: #1B263B;
            color: white;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6 mt-5">
                <form action="" method="post" class="form-container mt-5">
                    <h2 class="text-center">Request Password Reset</h2>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email:</label>
                        <input type="email" name="email" class="form-control" required autocomplete="off">
                    </div>
                    <div class="d-flex">
                        <button type="submit" class="btn btn-primary">Send Request</button>
                        <a href="login.php" class="btn btn-primary ml-2">Back</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script>
        <?php if (!empty($success)): ?>
            Swal.fire({
            title: 'Success!',
            text: '<?php echo $success; ?>',
            icon: 'success',
            confirmButtonText: 'OK'
            }).then(function() {
                window.location.href = 'login.php';
            });
        <?php elseif (!empty($error)): ?>
            Swal.fire({
                title: 'Error!',
                text: '<?php echo $error; ?>',
                icon: 'error',
                confirmButtonText: 'OK'
            });
        <?php endif; ?>
    </script>
</body>
</html>
