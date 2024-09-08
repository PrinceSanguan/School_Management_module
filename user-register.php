<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="icon" href="images/logo.webp" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Anton&family=Baskervville+SC&family=Nerko+One&family=New+Amsterdam&display=swap" rel="stylesheet">
    <style>
      html, body {
        margin: 0;
        padding: 0;
        font-family: "New Amsterdam", sans-serif;
      }
      body {
          background-image: url('./images/background6.jpg'); /* Adjust path if needed */
          background-size: cover; /* Ensure the image covers the whole background */
          background-position: center; /* Center the image */
          background-repeat: no-repeat; /* Prevent repeating */
          background-attachment: fixed; /* Make sure the background stays fixed during scrolling */
      }
    </style>
</head>
<body>
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-5"> <!-- Adjusted column width -->
            <form action="user-register-backend.php" method="POST" class="border border-dark border-2 p-3" style="background-color: rgba(0, 10, 10, 0.8);" id="password-form">
                <h2 class="mb-3 text-center text-white">Register</h2>
                <div class="mb-2">
                    <label for="firstName" class="form-label text-white">First Name</label>
                    <input type="text" class="form-control border-dark border-2" id="firstName" name="first_name" value="<?php echo isset($_SESSION['first_name']) ? htmlspecialchars($_SESSION['first_name']) : ''; ?>" required>
                </div>
                <div class="mb-2">
                    <label for="lastName" class="form-label text-white">Last Name</label>
                    <input type="text" class="form-control border-dark border-2" id="lastName" name="last_name" value="<?php echo isset($_SESSION['last_name']) ? htmlspecialchars($_SESSION['last_name']) : ''; ?>" required>
                </div>
                <div class="mb-2">
                    <label for="email" class="form-label text-white">Email</label>
                    <input type="email" class="form-control border-dark border-2" id="email" name="email" value="<?php echo isset($_SESSION['email']) ? htmlspecialchars($_SESSION['email']) : ''; ?>" required>
                </div>
                <div class="mb-2">
                    <label for="password" class="form-label text-white">Password</label>
                    <input type="password" class="form-control border-dark border-2" id="password" name="password" required>
                </div>
                <div class="mb-2">
                    <label for="confirmPassword" class="form-label text-white">Confirm Password</label>
                    <input type="password" class="form-control border-dark border-2" id="confirmPassword" name="confirm_password" required>
                </div>
                <div class="mb-2">
                    <label for="contact" class="form-label text-white">Contact Number</label>
                    <input type="number" class="form-control border-dark border-2" id="contact" name="contact" value="<?php echo isset($_SESSION['contact']) ? htmlspecialchars($_SESSION['contact']) : ''; ?>" required>
                </div>
                <div class="mb-2">
                    <label for="address" class="form-label text-white">Address</label>
                    <input type="text" class="form-control border-dark border-2" id="address" name="address" value="<?php echo isset($_SESSION['address']) ? htmlspecialchars($_SESSION['address']) : ''; ?>" required>
                </div>
                <button type="submit" class="btn btn-primary btn-sm">Register</button> <!-- Smaller button -->
                <a href="user-login.php" class="btn btn-primary btn-sm">Login</a> <!-- Smaller button -->
                <a href="index.php" class="btn btn-primary btn-sm">Back</a> <!-- Smaller button -->
            </form>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"></script>
<script>
    document.getElementById('password-form').addEventListener('submit', function(event) {
        event.preventDefault(); // Prevent default form submission

        var password = document.getElementById('password').value;
        var confirmPassword = document.getElementById('confirmPassword').value;

        if (password !== confirmPassword) {
            Swal.fire({
                title: 'Error!',
                text: 'Passwords do not match.',
                icon: 'error'
            });
        } else {
            // Prepare FormData object
            var formData = new FormData(this);

            // Send data to the server
            fetch('user-register-backend.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json()) // Expect JSON response
            .then(data => {
                if (data.error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: data.error,
                        confirmButtonText: 'Okay'
                    });
                } else if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: data.success,
                        confirmButtonText: 'Okay'
                    }).then(() => {
                        window.location.href = 'user-login.php'; // Redirect on success
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'An error occurred. Please try again later.',
                    confirmButtonText: 'Okay'
                });
            });
        }
    });
</script>
</body>
</html>
