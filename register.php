<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
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

    .register-section {
        background-color: rgba(255, 255, 255, 0.9);
        border-radius: 8px;
        box-shadow: 0 0 20px rgba(0, 0, 0, 0.5);
        max-width: 800px;
        width: 100%;
        margin: auto;
        display: flex;
        flex-direction: column;
    }

    .register-image img {
        width: 100%;
        height: auto; /* Adjusted for responsive image scaling */
    }

    .register-form {
        padding: 40px;
    }

    .form-label, .register-section h2 {
        color: #1B263B;
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

    .form-check-label {
        padding-left: 10px;
    }

    .divider {
        border: 0;
        height: 1px;
        background: #1F7A8C;
        margin: 20px 0;
    }

    /* Responsive adjustments */
    @media (min-width: 768px) {
        .register-section {
            flex-direction: row;
        }

        .register-image {
            width: 50%;
            border-right: 2px solid #1F7A8C; /* Divider line */
        }

        .register-form {
            width: 50%;
        }
    }

    @media (max-width: 767.98px) {
        .register-image {
            border-right: none; /* Remove border for smaller screens */
        }

        .register-form {
            padding: 20px;
        }
    }
    </style>
</head>

<body>
<div class="container">
        <div class="row justify-content-center align-items-center min-vh-100">
            <div class="col-lg-8 col-md-10 col-sm-12">
                <div class="register-section">
                    <div class="register-image d-flex justify-content-center align-items-center p-3">
                        <img src="images/logo.webp" alt="Logo" class="img-fluid">
                    </div>
                    <div class="register-form">
                        <form action="register-backend.php" method="POST">
                            <h2 class="mb-4 text-center">Register</h2>
                            <div class="mb-3">
                                <label for="first_name" class="form-label">First Name</label>
                                <input type="text" class="form-control" id="first_name" name="first_name" required>
                            </div>
                            <div class="mb-3">
                                <label for="last_name" class="form-label">Last Name</label>
                                <input type="text" class="form-control" id="last_name" name="last_name" required>
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                            <div class="mb-3">
                                <label for="lrn" class="form-label">LRN</label>
                                <input type="text" class="form-control" id="lrn" name="lrn" required>
                            </div>
                            <div class="mb-3">
                                <label for="parent_name" class="form-label">Parent Name</label>
                                <input type="text" class="form-control" id="parent_name" name="parent_name" required>
                            </div>
                            <div class="mb-3">
                                <label for="parent_email" class="form-label">Parent Email</label>
                                <input type="email" class="form-control" id="parent_email" name="parent_email" required>
                            </div>
                            <div class="mb-3">
                                <label for="parent_contact" class="form-label">Parent Contact</label>
                                <input type="text" class="form-control" id="parent_contact" name="parent_contact" required>
                            </div>
                            <div class="mb-3 form-check">
                                <input type="checkbox" class="form-check-input border-black" id="terms" required>
                                <label class="form-check-label" for="terms">
                                    I agree to the <a href="termsandconditions.php" target="_blank">Terms and Conditions</a>
                                </label>
                            </div>

                            <button type="submit" class="btn btn-primary">Register</button>
                            <div class="text-center mt-3">
                                <p>Do you want to get back? <a href="login.php">Back</a></p>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            <?php
            if (isset($_SESSION['register_error']) && $_SESSION['register_error'] != "") {
                echo "Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: '" . addslashes($_SESSION['register_error']) . "'
                });";
                unset($_SESSION['register_error']);
            }
            if (isset($_SESSION['register_success']) && $_SESSION['register_success'] != "") {
                echo "Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: '" . addslashes($_SESSION['register_success']) . "'
                }).then(function() {
                    window.location.href = 'login.php';
                });";
                unset($_SESSION['register_success']);
            }
            ?>
        });
    </script>
</body>
</html>
