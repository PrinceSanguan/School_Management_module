<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="stylesheet" href="style_1.css" />
  <title>Home</title>
  <link rel="icon" href="images/logo.webp" type="image/x-icon">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <link href="https://fonts.googleapis.com/css2?family=Anton&family=Baskervville+SC&family=Nerko+One&family=New+Amsterdam&display=swap" rel="stylesheet">
</head>

<body>
  <nav class="navbar">
    <div class="logo">
      <a href="mainpage_compassED.php"><img src="./images/logo.webp" alt=""></a>
    </div>
    <ul class="nav-links">
      <input type="checkbox" id="checkbox_toggle" />
      <label for="checkbox_toggle" class="hamburger">&#9776;</label>
      <div class="menu">
        <li><a href="#">Home</a></l>
        <li><a href="login.php">Login</a></li>
        <li><a href="about.php">About</a></li>
         <!--<li><a href="admin-login.php">Admin</a> -->
          <!-- DROPDOWN MENU -->
          <!--<ul class="dropdown">
            <li><a href="aboutus_compassED.php#Mission">Mission </a></li>
            <li><a href="aboutus_compassED.php#Vision">Vision</a></li>
            <li><a href="aboutus_compassED.php#Values">Core Values</a></li>
            <li><a href="aboutus_compassED.php#Institution">Institution</a></li>
          </ul>-->
        <!--<li><a href="teacher-login.php">Teacher</a></li>-->
        <!--<li><a href="user-login.php">User Login</a></li>-->
      </div>
    </ul>
  </nav>

<section class="header">

<div class="text-box">
  <h1>Antipolo City SPED Center</h1>
  <p>Dedicated to providing a supportive and inclusive educational environment for <br>students with disabilities.</p>
  <a href="#cta" class="contact-us">Contact Us To Know More</a>
</div>

</section>

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
    <form action="enroll-now-backend.php" method="POST">
          <input type="text" name="first_name" placeholder="Enter your first name" required>
          <input type="text" name="last_name" placeholder="Enter your last name" required>
          <input type="email" name="email" placeholder="Enter your email address" required>
          <input type="text" name="parent_name" placeholder="Enter parent's name" required>
          <input type="text" name="parent_email" placeholder="Enter parent's email" required>
          <input type="text" name="parent_contact" placeholder="Enter parent's contact number" required>
          <button type="submit" class="contact-us">Enroll Now</button>
    </form>
    </div>

  </div>
</section>

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

</body>

<script>
        document.addEventListener('DOMContentLoaded', function() {
            const urlParams = new URLSearchParams(window.location.search);
            const status = urlParams.get('status');
            if (status) {
              let message = '';
              let icon = '';
              let title = 'Enrollment Status'; // Default title for enrollment-related messages

              switch (status) {
                  case 'success':
                      message = 'Your enrollment has been successfully completed.';
                      icon = 'success';
                      title = 'Enrollment Successful'; // Title for successful enrollment
                      break;
                  case 'duplicate':
                      message = 'A record with the same email or parent email already exists.';
                      icon = 'error';
                      title = 'Duplicate Enrollment'; // Title for duplicate record
                      break;
                  case 'error':
                      message = 'An error occurred. Please try again.';
                      icon = 'error';
                      title = 'Enrollment Error'; // Title for general error
                      break;
              }

              Swal.fire({
                  icon: icon,
                  title: title,
                  text: message,
                  confirmButtonText: 'Okay'
              });
          }
        });
    </script>
</html>