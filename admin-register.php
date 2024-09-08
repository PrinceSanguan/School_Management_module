<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Register</title>
    <link rel="icon" href="images/logo.webp" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Anton&family=Baskervville+SC&family=Nerko+One&family=New+Amsterdam&display=swap" rel="stylesheet">
</head>
<style>
  html, body {
    margin: 0;
    padding: 0;
    font-family: "New Amsterdam", sans-serif;
}

body {
    background-image: url('./images/background1.jpg'); /* Adjust path if needed */
    background-size: cover; /* Ensure the image covers the whole background */
    background-position: center; /* Center the image */
    background-repeat: no-repeat; /* Prevent repeating */
    background-attachment: fixed; /* Make sure the background stays fixed during scrolling */
}
</style>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8 mt-5">
            <form action="admin-register-backend.php" method="POST" class="border border-dark border-2 p-4" style="background-color: rgba(0, 10, 10, 0.8);">
                    <h2 class="mb-3 text-center text-white">Admin Register</h2>
                    <?php
                        session_start();
                        if (isset($_SESSION['register_error']) && $_SESSION['register_error'] != "") {
                            echo '<div class="alert alert-danger" role="alert">' . htmlspecialchars($_SESSION['register_error']) . '</div>';
                            unset($_SESSION['register_error']);
                        }
                    ?>
                    <div class="mb-3">
                        <label for="email" class="form-label text-white">Email</label>
                        <input type="email" class="form-control border-dark border-2" id="email" name="email" value="<?php echo isset($_SESSION['register_email']) ? htmlspecialchars($_SESSION['register_email']) : ''; ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label text-white">Password</label>
                        <input type="password" class="form-control border-dark border-2" id="password" name="password" required>
                    </div>
                    <div class="mb-3">
                        <label for="confirmPassword" class="form-label text-white">Confirm Password</label>
                        <input type="password" class="form-control border-dark border-2" id="confirmPassword" name="confirm_password" required>
                    </div>
                    <div class="mb-3">
                        <label for="contact" class="form-label text-white">Contact Number</label>
                        <input type="text" class="form-control border-dark border-2" id="contact" name="contact" value="<?php echo isset($_SESSION['register_contact']) ? htmlspecialchars($_SESSION['register_contact']) : ''; ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="address" class="form-label text-white">Address</label>
                        <input type="text" class="form-control border-dark border-2" id="address" name="address" value="<?php echo isset($_SESSION['register_address']) ? htmlspecialchars($_SESSION['register_address']) : ''; ?>" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Register</button>
                    <a href="admin-login.php" class="btn btn-primary">Login</a>
                    <a href="index.php" class="btn btn-primary">Back</a>
                </form>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"></script>
</body>
<script>
     function clearForm() {
        document.querySelectorAll('input[type="text"], input[type="email"], input[type="password"]').forEach(input => {
            input.value = '';
        });
    }

    // Call the function on page load
    window.onload = function() {
        clearForm();
    };
</script>   
</html>
