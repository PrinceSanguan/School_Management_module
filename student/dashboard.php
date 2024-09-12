<?php
include "../database/database.php";

session_start();

// Check if the user is a student
if (!isset($_SESSION['userRole']) || $_SESSION['userRole'] !== 'student') {
  $_SESSION['error'] = "You do not have permission to access this page!";
  header("Location: ../index.php");
  exit();
}

if (!$conn) {
  die("Connection failed: " . mysqli_connect_error());
}

// Fetch data based on user role
$userRole = $_SESSION['userRole'];
$query = "SELECT id, announcement, view FROM announcement WHERE view = '$userRole' OR view = 'studentTeacher'";
$result = $conn->query($query);

if (!$result) {
  die("Query failed: " . $conn->error);
}

// Store fetched announcements
$announcements = [];
while ($row = $result->fetch_assoc()) {
  $announcements[] = $row;
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="../asset/css/account-approval.css">
  <!-- Sweet Alert -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <title>Announcement</title>
  <style>


.carousel {
    position: relative;
    width: 80%;
    max-width: 800px;
    margin-top: 10px;
    margin: 0 auto; /* Center the carousel horizontally */
    overflow: hidden;
    border: 2px solid #ddd;
    border-radius: 10px;
    background-color: #fff;
    top: 190px;
}

.carousel-inner {
    display: flex;
    transition: transform 0.5s ease-in-out;
}

.carousel-item {
    min-width: 100%;
    box-sizing: border-box;
    padding: 20px;
    text-align: center;
}

.announcement-text {
    font-size: 1.5em;
    color: #333;
}

.carousel-control {
    position: absolute;
    top: 50%;
    width: 50px;
    height: 50px;
    background-color: rgba(0, 0, 0, 0.5);
    color: white;
    text-align: center;
    line-height: 50px;
    font-size: 2em;
    cursor: pointer;
    user-select: none;
    border-radius: 50%;
    z-index: 100;
}

.carousel-control.prev {
    left: 10px;
}

.carousel-control.next {
    right: 10px;
}
  </style>
</head>
<body>

  <div class="navbar">
    <a href="../student/dashboard.php">Announcement</a>
    <a href="../admin/section.php">Section</a>
    <a href="../admin/announcement.php">School Announcement</a>
    <a href="../controller/LogoutController/logOut.php">Logout</a>
  </div>



  <div class="carousel">
    <div class="carousel-inner">
      <?php foreach ($announcements as $index => $announcement): ?>
        <div class="carousel-item <?php echo $index === 0 ? 'active' : ''; ?>">
          <div class="announcement-text">Announcement</div>
          <div class="announcement"><?php echo htmlspecialchars($announcement['announcement']); ?></div>
        </div>
      <?php endforeach; ?>
    </div>
    <a class="carousel-control prev" onclick="prevSlide()">&#10094;</a>
    <a class="carousel-control next" onclick="nextSlide()">&#10095;</a>
  </div>

  </tbody>
  
  
  <script>
    document.getElementById('openModal').addEventListener('click', function() {
      document.getElementById('accountModal').style.display = 'flex';
    });

    document.getElementById('closeModal').addEventListener('click', function() {
      document.getElementById('accountModal').style.display = 'none';
    });

    window.addEventListener('click', function(event) {
      if (event.target === document.getElementById('accountModal')) {
        document.getElementById('accountModal').style.display = 'none';
      }
    });
  </script>

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

    <script>
      let currentIndex = 0;

function showSlide(index) {
    const slides = document.querySelectorAll('.carousel-item');
    if (index >= slides.length) {
        currentIndex = 0;
    } else if (index < 0) {
        currentIndex = slides.length - 1;
    } else {
        currentIndex = index;
    }

    const offset = -currentIndex * 100;
    document.querySelector('.carousel-inner').style.transform = `translateX(${offset}%)`;
}

function nextSlide() {
    showSlide(currentIndex + 1);
}

function prevSlide() {
    showSlide(currentIndex - 1);
}

// Initialize the carousel
showSlide(currentIndex);

// Optional: Automatically advance slides every 5 seconds
setInterval(nextSlide, 5000);
    </script>

</body>
</html>