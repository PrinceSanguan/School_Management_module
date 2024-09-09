<?php
include "database/database.php";
require 'database/config.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="stylesheet" href="asset/css/signup.css" />
  <title>Signup</title>
  <link rel="icon" href="asset/images/logo.webp" type="image/x-icon">
</head>

<body>
<nav class="navbar">
    <!-- LOGO -->
    <div class="logo">
      <a href=""><img src="asset/images/logo.webp" alt=""></a>
    </div>

    <!-- NAVIGATION MENU -->
    <ul class="nav-links">
      <!-- USING CHECKBOX HACK -->
      <input type="checkbox" id="checkbox_toggle" />
      <label for="checkbox_toggle" class="hamburger">&#9776;</label>

      <!-- NAVIGATION MENUS -->
      <div class="menu">
        <li><a href="index.php">Home</a></li>
        <li class="about">
          <a href="about.php">About</a>
        </li>
        <li><a href="login.php">Login</a></li>
      </div>
    </ul>
  </nav>
  
    <div class="container">
        <div class="box">
        <div class="logoform">
      <img src="asset/images/logo.webp" alt="">
      </div>
        </div>

        <div class="box">
            <h2>Sign Up</h2>

            <form action="controller/SignupController/signup-form.php" method="post">
                <input type="text" name="firstName" placeholder="First Name"><br>
                <input type="text" name="lastName" placeholder="Last Name"><br>
                <input type="text" name="email" placeholder="Enter Email" required><br>

                <input type="password" name="password" placeholder="Create Password" id="password" required><br>
                <input type="password" name="cpassword" placeholder="Confrim password" required><br>
                <div class="radio-buttons">
                  <input class="radio-input" type="radio" value="Parent" name="userrole" ID="radio1" checked="checked">
                  <label for="radio1" class="radio-label">Parent</label>
                  <input class="radio-input" type="radio" value="Teacher" name="userrole" ID="radio2">
                  <label for="radio2" class="radio-label">Teacher</label>
                </div>
                <input type="submit" name="submit" value="Sign Up">
            </form>

            <div class="bottom-links">
                <p>Already have an account?</p><a href="login.php" style="text-decoration: none">Log in</a>
            </div>
        </div>

    </div>

<!---- Sweet Alert ---->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
  document.addEventListener('DOMContentLoaded', function () {
      // Check for error message
      <?php if (isset($_SESSION['error'])): ?>
          Swal.fire({
              icon: 'error',
              title: 'Oops...',
              text: '<?php echo $_SESSION['error']; ?>',
              confirmButtonText: 'Try Again'
          });
          <?php unset($_SESSION['error']); // Clear the session variable ?>
      <?php endif; ?>

      // Check for success message
      <?php if (isset($_SESSION['success'])): ?>
          Swal.fire({
              icon: 'success',
              title: 'Success!',
              text: '<?php echo $_SESSION['success']; ?>',
              confirmButtonText: 'OK'
          });
          <?php unset($_SESSION['success']); // Clear the session variable ?>
      <?php endif; ?>
  });
</script>


</body>
</html>