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
            <form action="Signup_compassED.php" method="post">
                <input type="text" name="fullname" placeholder="Enter Full Name"><br>
                <input type="text" name="email" placeholder="Enter Email" required><br>

                <input type="password" name="password" placeholder="Create Password" id="password" required><br>
                  <div class="password-requirements" id="password-requirements">
                  
                    <p>Password must contain:</p>
                    <ul>
                        <li>At least 8 characters</li>
                        <li>At least one number</li>
                        <li>At least one uppercase letter</li>
                        <li>At least one lowercase letter</li>
                    </ul>
                </div>
                <input type="password" name="cpassword" placeholder="Confrim password" required><br>
                <div class="radio-buttons">
                  <input class="radio-input" type="radio" value="Parent" name="Status" ID="radio1" checked="checked">
                  <label for="radio1" class="radio-label">Parent</label>
                  <input class="radio-input" type="radio" value="Teacher" name="Status" ID="radio2">
                  <label for="radio2" class="radio-label">Teacher</label>
                </div>
                <input type="submit" name="submit" value="Sign Up">
                
            </form>
            <div class="bottom-links">
                <p>Already have an account?</p><a href="login_compassED.php" style="text-decoration: none">Log in</a>
            </div>
        </div>

    </div>

  <!-- Background Overlay -->
  <div id="popup-overlay" class="popup-overlay"></div>

<!-- Pop-Up Dialog -->
<div id="popup" class="popup">
  <h2>Signup</h2>
  <span id="popup-message"></span>
  <div>
  <button id="close-btn">Close</button>
  </div>
</div>


  <script>
    document.getElementById('password').addEventListener('focus', function() {
        document.querySelector('.password-requirements').style.display = 'block';
    });

    document.getElementById('password').addEventListener('blur', function() {
        document.querySelector('.password-requirements').style.display = 'none';
    });

        // Function to show the popup with a specific message
function showPopup(message) {
    const popup = document.getElementById('popup');
    const overlay = document.getElementById('popup-overlay');
    const popupMessage = document.getElementById('popup-message');
    popupMessage.textContent = message;
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

</body>
</html>