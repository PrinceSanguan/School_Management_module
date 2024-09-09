<?php
include "database/database.php";
require 'database/config.php';
?>

<head>
  <meta charset="UTF-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="stylesheet" href="asset/css/login.css" />
  <title>login</title>
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
      <h2>LOGIN</h2>
      
      <form action="" method="post">
        <input type="text" name="email" placeholder="Email" required><br>
        <input type="password" name="password" placeholder="Password" required><br>
        <div class="forgot-pass">
          <a href="" style="text-decoration: none">Forgot Password?</a><br>
        </div>
        <input type="submit" name="submit" value="Login">
      </form>
      <div class="bottom-links">
        <p>Don't have an account yet?</p><a href="signup.php" style="text-decoration: none">Sign Up</a>
      </div>
    </div>
  </div>


  <!-- Background Overlay -->
  <div id="popup-overlay" class="popup-overlay"></div>

  <!-- Pop-Up Dialog -->
  <div id="popup" class="popup">
    <h2>Login Failed</h2>
    <span id="popup-message"></span>
    <div>
    <button id="close-btn">Close</button>
    </div>
  </div>

  <script>
    // Function to show the popup with a specific message
function showPopup(message) {
    const popup = document.getElementById('popup');
    const overlay = document.getElementById('popup-overlay');
    const popupMessage = document.getElementById('popup-message');
    popupMessage.innerHTML = message;
    overlay.classList.add('show');
    popup.classList.add('show');
    overlay.style.display = 'block';
    popup.style.display = 'block';
    setTimeout(() => {
        overlay.style.opacity = '1';
        popup.style.opacity = '1';
    }, 10); // Small delay to ensure the display property takes effect before opacity transition
}

// Function to close the popup
function closePopup() {
    const popup = document.getElementById('popup');
    const overlay = document.getElementById('popup-overlay');
    popup.classList.remove('show');
    overlay.classList.remove('show');
    setTimeout(() => {
        popup.style.display = 'none';
        overlay.style.display = 'none';
    }, 10); // Match the transition duration in CSS
}

// Event listener for the close button
document.getElementById('close-btn').addEventListener('click', closePopup);

// Event listener for the overlay
document.getElementById('popup-overlay').addEventListener('click', closePopup);
  </script>

<?php 
  if(isset($_POST["submit"])){
    $useremail = $_POST["email"];
    $password = $_POST["password"];
    $sql = "SELECT * FROM users WHERE Email = '$useremail'";
    $result = $conn->query($sql);

    if($result->num_rows == 1){
      $user = $result->fetch_assoc();
      if($password == $user["Password"]){
        if($user['activation_token'] == NULL){
          if($user['activated'] == 1){
            if($user['Status'] == 'Teacher'){
              $_SESSION["login"] = true;
              $_SESSION["user"] = $user;
              header("Location: dashboard_compassED.php");
            } elseif($user['Status'] == 'Admin'){
              $_SESSION["login"] = true;
              $_SESSION["user"] = $user;
              header("Location: admin/account-approval.php");
            } elseif($user['Status'] == 'Parent'){
              $_SESSION["login"] = true;
              $_SESSION["user"] = $user;
              header("Location: parents/dashboard.php");
            }
          } else {
            echo "<script>showPopup('Account is not approved yet, please wait or contact the admin for account approval.');</script>";
          }
        } else {
          echo "<script>showPopup('Account is not activated yet, check your email for the link activation. <br><br> Didn\'t receive an email? <a href=\"resend-activation.php\">Resend</a>.');</script>";
        }
      } else {
        echo "<script>showPopup('Wrong Password.');</script>";
      }
    } else {
      echo "<script>showPopup('Email is not registered.');</script>";
    }
  }
  ?>

</body>
</html>