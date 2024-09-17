<?php
require 'database/config.php';

?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="asset/css/index.css" />
  <link rel="icon" href="asset/images/logo.webp" type="image/x-icon">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@200&family=Oswald:wght@200..700&family=Poppins:wght@100;200;300;400;600;700&family=Roboto+Condensed:ital,wght@0,400;0,500;1,400&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Sharp:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
  <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
  <title>CompassED LMS</title>
</head>
<body>
  <nav class="navbar">
    <!-- LOGO -->
    <div class="logo">
      <a href="index.php"><img src="asset/images/logo.webp" alt=""></a>
    </div>

    <!-- NAVIGATION MENU -->
    <ul class="nav-links">

      <!-- USING CHECKBOX HACK -->
      <input type="checkbox" id="checkbox_toggle" />
      <label for="checkbox_toggle" class="hamburger">&#9776;</label>

      <!-- NAVIGATION MENUS -->
      <div class="menu">

        <li>
          <a href="index.php">Home</a>
        </li>
		
        <li class="about">
          <a href="about.php">About</a>
        </li>

        <li>
          <a href="calendar.php">Calendar</a>
        </li>

        <li>
          <a href="login.php">Login</a>
        </li>

      </div>
    </ul>
  </nav>

<!-- Landing Page -->
<section class="header">

<div class="text-box">
  <h1>Antipolo City SPED Center</h1>
  <p>Dedicated to providing a supportive and inclusive educational environment for <br>students with disabilities.</p>
  <a href="#cta" class="contact-us">Contact Us To Know More</a>
</div>

</section>

<!-- offers -->

<section class="offers">
  <h1>What We Offer</h1>
  <p>Discover what we offer: a vibrant community, top-notch education, and endless opportunities for growth and exploration. Join us to experience excellence in every aspect of school life.</p>

 <div class="row">
  <div class="offers-col">
    <h3>Safe Environment</h3>
    <p>At Antipolo City SPED Center, we prioritize creating a safe environment for our students, staff, and visitors. Our comprehensive safety program includes regular risk assessments, emergency preparedness drills, and a strong focus on health and well-being. We provide ongoing training for our staff and promote a culture of safety among our students to ensure a secure and nurturing learning atmosphere.</p>
  </div>
  <div class="offers-col">
    <h3>Specialized Teaching Staff</h3>
    <p>At Antipolo City SPED Center, we pride ourselves on having a team of specialized teaching staff dedicated to meeting the diverse needs of our students. Our educators are experts in their fields, bringing advanced knowledge, innovative teaching methods, and personalized attention to the classroom. They are committed to fostering an inclusive and supportive learning environment where every student can excel.</p>
  </div>
  <div class="offers-col">
    <h3>Inclusive Extracurricular Activities</h3>
    <p>At Antipolo City SPED Center, we offer a wide range of inclusive extracurricular activities designed to engage and inspire every student. Our programs cater to diverse interests and abilities, ensuring that all students have the opportunity to explore their passions and develop new skills.</p>
  </div>
 </div>
</section>

<!-- Contact US -->
<section class="cta" id="cta">
  <h1>Enroll For Our Specialized Teaching for Students with Disabilities</h1>
  <div class="row">
    <div class="cta-cols">

      <div>
      <i class='bx bxs-home'></i>
        <span> 
          <h5>C. Lawis Extension, Brgy. San Isidro, Antipolo City 1870</h5>
        </span>
      </div>

      <div>
      <i class='bx bxs-phone' ></i>
        <span>   
          <h5>631-48-43 Landline</h5>
          <p>Monday to Saturday, 9AM to 5PM</p>
        </span>
      </div>

      <div>
      <i class='bx bx-envelope' ></i> 
        <span>  
          <h5>antipolosped@gmail.com</h5>
          <p>Email us</p>
        </span>
      </div>
    </div>

    <div class="cta-cols">

      <form action="email.php" method="POST">
        <input type="text" name="name" placeholder="Enter your name" required>
        <input type="Email" name="email" placeholder="Enter email address" required>
        <input type="text" name="subject" placeholder="Enter your subject" required>
        <textarea rows="8" name="message" placeholder="Message" required></textarea>
        <button type="submit" class="contact-us">Send Message</button>
      </form>
    </div>

  </div>
</section>

<!-- location map -->

<section class="location">

<iframe src="https://www.google.com/maps/embed?pb=!1m14!1m8!1m3!1d15444.58602310674!2d121.1847307!3d14.5907264!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3397bf5ad18ae799%3A0x1a53df9ed8857c79!2sAntipolo%20City%20SPED%20Center!5e0!3m2!1sen!2sph!4v1715787011131!5m2!1sen!2sph" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>

</section>

<section class="footer">
<div class="row">
  <div class="footer-cols">

    <p>© 2024. Antipolo SPED Center. All rights reserved.</p>
  </div>
  <div class="footer-cols">
    <a href="https://www.facebook.com/DepEdTayoACSC500392/">
    <i class='bx bxl-facebook' ></i>
    </a>
    <a href="https://m.me/DepEdTayoACSC500392">
    <i class='bx bxl-messenger' ></i>
    </a>
  </div>
  </div>
</div>

</section>


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