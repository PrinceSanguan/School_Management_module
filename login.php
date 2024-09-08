<?php
session_start();
$alert_type = $_SESSION['show_alert'] ?? '';
$error_message = $_SESSION['login_error'] ?? '';
unset($_SESSION['show_alert']);
unset($_SESSION['login_error']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="icon" href="images/logo.webp" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"></script>
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
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-section {
            background-color: rgba(255, 255, 255, 0.9);
            border-radius: 8px;
            display: flex;
            flex-direction: column; /* Make sure it stacks properly */
            overflow: hidden;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.5);
            padding: 20px;
            max-width: 600px; /* Limit maximum width for larger screens */
            width: 100%;
        }

        .login-image {
            padding: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            margin-bottom: 20px; /* Margin to separate from form */
        }

        .login-image img {
            max-width: 100%;
            height: auto;
        }

        .login-form {
            padding: 20px;
            width: 100%;
        }

        .form-label, .login-section h2 {
            color: #1B263B;
        }

        .form-control, .form-select {
            background-color: #415A77;
            border: 1px solid #1F7A8C;
            color: white;
            caret-color: white;
        }

        .form-control:focus, .form-select:focus {
            background-color: #415A77;
            border-color: #1F7A8C;
            color: white;
            caret-color: white;
            box-shadow: 0 0 0 0.2rem rgba(31, 122, 140, 0.25);
        }

        .form-control::placeholder, .form-select::placeholder {
            color: #AABACD;
        }

        .btn-primary {
            background-color: #1F7A8C;
            border: 1px solid #1F7A8C;
            width: 100%;
        }

        .btn-primary:hover {
            background-color: #1F6F7A;
            border-color: #1F6F7A;
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

        .text-center a:hover {
            color: #1F6F7A;
            text-decoration: underline;
        }

        /* Responsive styles */
        @media (min-width: 576px) {
            .login-section {
                flex-direction: row; /* Row layout for larger screens */
            }

            .login-image {
                border-right: 2px solid #1F7A8C;
                width: 50%;
            }

            .login-form {
                width: 50%;
            }
        }

        @media (max-width: 575.98px) {
            .login-section {
                flex-direction: column; /* Column layout for smaller screens */
            }
        }
    </style>
</head>

<body>
<div class="container">
        <div class="login-section">
            <div class="login-image">
                <img src="images/logo.webp" alt="Logo" class="img-fluid">
            </div>
            <div class="login-form">
                <form action="login-backend.php" method="POST">
                    <h2 class="mb-4 text-center">Login</h2>
                    <?php
                    if (isset($_SESSION['login_error']) && $_SESSION['login_error'] != "") {
                        echo '<div class="alert alert-danger" role="alert">' . htmlspecialchars($_SESSION['login_error']) . '</div>';
                        unset($_SESSION['login_error']);
                    }
                    ?>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" required autocomplete="off">
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <div class="mb-3">
                        <label for="user_type" class="form-label">User Type</label>
                        <select class="form-select" id="user_type" name="user_type" required>
                            <option value="student">Student</option>
                            <option value="admin">Admin</option>
                            <option value="teacher">Teacher</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <a href="password-reset-request.php">Forgot Password?</a>
                    </div>
                    <button type="submit" class="btn btn-primary">Login</button>
                    <div class="text-center mt-3">
                        <p>Do you want to get enrolled? <a href="register.php">Sign Up</a></p>
                        <p>Do you want to get back? <a href="index.php">Back</a></p>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
       document.addEventListener('DOMContentLoaded', function() {
        <?php if ($alert_type === 'success'): ?>
            Swal.fire({
                title: 'Success!',
                text: 'Login successful.',
                icon: 'success'
            });
        <?php elseif ($alert_type === 'error' && $error_message): ?>
            Swal.fire({
                title: 'Error!',
                text: '<?php echo addslashes($error_message); ?>',
                icon: 'error'
            });
        <?php endif; ?>
    });
    </script>
</body>

</html>
