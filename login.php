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
  <link rel="stylesheet" href="asset/css/login.css" />
  <link rel="icon" href="images/logo.webp" type="image/x-icon">
  <title>Login</title>
  <style>

  </style>
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
      <h2>LOGIN</h2>
      <form action="login_compassED.php" method="post">
        <input type="text" name="email" placeholder="Email" required><br>
        <input type="password" name="password" placeholder="Password" required><br>
        <div class="forgot-pass">
          <a href="forgot-password.php" style="text-decoration: none">Forgot Password?</a><br>
        </div>
        <input type="submit" name="submit" value="Login">
      </form>
      <div class="bottom-links">
        <p>Don't have an account yet?</p><a href="signup.php" style="text-decoration: none">Sign Up</a>
      </div>
    </div>
  </div>

<!---- Sweet Alert ---->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
  document.addEventListener('DOMContentLoaded', function () {
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
  });
</script>

</body>
</html>