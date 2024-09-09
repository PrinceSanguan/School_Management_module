<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="stylesheet" href="asset/css/signup.css" />
  <title>Forgot Password</title>
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

  <div class="wrapper-forgotpw">
    <div class="container-forgotpw">
        <div class="title-forgotpw">
            <h2 class="title-fpw">Reset Password</h2>
            <p class="p-forgotpw">Enter the email address your account is registered with and we'll send you an instructions.</p>
        </div>
        <form action="send-password-reset.php" method="POST">
            <div class="input-forgotpw">
                <label for="" class="labeltitle">Enter Your Email</label>
                <input type="email" name="email" placeholder="Enter your email">
                <span class="icon-forgotpw">&#9993;</span>
            </div>
            <div class="input-forgotpw">
                <button class="forgot-submit" type="submit" name="submit">Reset my password</button>
            </div>
        </form>
    </div>
  </div>

</body>

</html>