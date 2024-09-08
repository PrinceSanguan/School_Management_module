<?php
require 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

include 'db_connect.php';

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$error = "";
$passwordreset = "";

$token = isset($_GET['token']) ? $_GET['token'] : '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $token = $_POST['token'];
    $newPassword = $_POST['new_password'];
    $sql = "SELECT user_id, expires FROM password_resets WHERE token = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $reset = $result->fetch_assoc();
        $userId = $reset['user_id'];
        $expires = $reset['expires'];

        if (new DateTime() < new DateTime($expires)) {

            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            $sql = "UPDATE users SET password = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("si", $hashedPassword, $userId);
            $stmt->execute();

            $sql = "DELETE FROM password_resets WHERE token = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $token);
            $stmt->execute();

            $passwordreset = "Your password has been reset successfully.";
        } else {
            $error = "This token has expired.";
        }
    } else {
        $error = "Invalid token.";
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
    <title>Reset Password</title>
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
        <div class="col-md-6">
            <form method="post" action="" class="border border-dark border-2 p-4 form-container mt-5" id="resetPasswordForm">
                <h2 class="text-center">Reset Password</h2>
                <?php
                        session_start();
                        if ($passwordreset) {
                            $_SESSION['passwordreset'] = $passwordreset;
                            echo '<script type="text/javascript">var resetPasswordSuccess = true;</script>';
                        }
                        if ($error) {
                            $_SESSION['error'] = $error;
                            echo '<script type="text/javascript">var resetPasswordSuccess = false;</script>';
                        }
                    ?>
                <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
                <div class="mb-3">
                    <label for="new_password" class="form-label">New Password</label>
                    <input type="password" class="form-control border-dark border-2" name="new_password" id="new_password" required>
                </div>
                <div class="mb-3">
                    <label for="confirm_password" class="form-label">Confirm Password</label>
                    <input type="password" class="form-control border-dark border-2" name="confirm_password" id="confirm_password" required>
                </div>
                <button type="submit" class="btn btn-primary">Reset Password</button>
            </form>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
    if (typeof resetPasswordSuccess !== 'undefined') {
        if (resetPasswordSuccess) {
            Swal.fire({
                icon: 'success',
                title: 'Password Reset',
                text: 'The password has been successfully reset.',
            }).then(() => {
                window.close();
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'An error occurred while resetting the password.',
            });
        }
    }
    document.getElementById('resetPasswordForm').addEventListener('submit', function(event) {
        event.preventDefault();

        var newPassword = document.getElementById('new_password').value;
        var confirmPassword = document.getElementById('confirm_password').value;

        if (newPassword !== confirmPassword) {
            Swal.fire({
                icon: 'error',
                title: 'Passwords do not match',
                text: 'Please ensure both password fields match.',
            });
        } else {
            Swal.fire({
                icon: 'success',
                title: 'Passwords match',
                text: 'Your password will be reset!',
                showCancelButton: true,
                confirmButtonText: 'Reset Password',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    event.target.submit();
                }
            });
        }
    });
});

</script>
</body>
</html>
