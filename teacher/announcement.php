<?php
include "../database/database.php";

session_start();

// Check if the user is logged in
if (!isset($_SESSION['userId']) || !isset($_SESSION['userRole'])) {
    $_SESSION['error'] = "Please log in to access this page.";
    header("Location: ../../login.php");
    exit();
}

// Check if the user is a teacher or the verified user
if ($_SESSION['userRole'] !== 'teacher' && $_SESSION['userId'] != $subject['userId']) {
    $_SESSION['error'] = "You do not have permission to access this page.";
    header("Location: ../../index.php");
    exit();
}

// Fetch announcements for teachers and studentTeachers
$query = "SELECT * FROM announcement WHERE view IN ('teacher', 'studentTeacher') ORDER BY id DESC";
$result = $conn->query($query);

$announcements = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $announcements[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../asset/css/account-approval.css">
    <title>Announcement</title>
    <style>
        .carousel-container {
            max-width: 1000px;
            margin: 50px auto;
            background-color: #0f3460;
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 0 20px rgba(0, 255, 0, 0.2);
        }
        .carousel {
            position: relative;
            overflow: hidden;
            height: 500px;
        }
        .carousel-item {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            opacity: 0;
            transition: opacity 0.5s ease-in-out;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 20px;
            box-sizing: border-box;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 255, 0, 0.1);
        }
        .carousel-item.active {
            opacity: 1;
        }
        .carousel-nav {
            text-align: center;
            margin-top: 20px;
        }
        .carousel-nav button {
            background-color: white;
            border: none;
            color: #1a1a2e;
            padding: 5px 10px;
            margin: 0 5px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .carousel-nav button:hover {
            background-color: #00cc00;
        }
    </style>
</head>
<body>
    <div class="navbar">
        <a href="../teacher/announcement.php" style="color: wheat;">Announcement</a>
        <a href="../teacher/assign_subject.php">Assigned Subject</a>
        <a href="../teacher/task.php">Task</a>
        <a href="../controller/LogoutController/logOut.php">Logout</a>
    </div>

    <div class="carousel-container">
    <div class="carousel">
        <?php foreach ($announcements as $index => $announcement): ?>
            <div class="carousel-item <?php echo $index === 0 ? 'active' : ''; ?>">
                <?php
                // Show text if available, otherwise show image
                if (!empty($announcement['announcement'])) {
                    echo '<p>' . htmlspecialchars($announcement['announcement']) . '</p>';
                } elseif (!empty($announcement['image_path'])) {
                    echo '<img style="height: 400px" src="../../' . htmlspecialchars($announcement['image_path']) . '" alt="Announcement Image" style="max-width: 300px; max-height: 300px;">';
                } else {
                    echo '<p>No content available</p>';
                }
                // Display the announcement creation date
                if (!empty($announcement['created_at'])) {
                    $formattedDate = date('F j, Y', strtotime($announcement['created_at']));
                    echo '<p class="announcement-date" style="margin-top: 10px;">Posted on: ' . $formattedDate . '</p>';
                }
                ?>
            </div>
        <?php endforeach; ?>
    </div>
    <div class="carousel-nav">
        <button id="prevBtn">Previous</button>
        <button id="nextBtn">Next</button>
    </div>
</div>


    <script>
        const carousel = document.querySelector('.carousel');
        const items = carousel.querySelectorAll('.carousel-item');
        const prevBtn = document.getElementById('prevBtn');
        const nextBtn = document.getElementById('nextBtn');
        let currentIndex = 0;

        function showItem(index) {
            items[currentIndex].classList.remove('active');
            items[index].classList.add('active');
            currentIndex = index;
        }

        function nextItem() {
            let index = (currentIndex + 1) % items.length;
            showItem(index);
        }

        function prevItem() {
            let index = (currentIndex - 1 + items.length) % items.length;
            showItem(index);
        }

        nextBtn.addEventListener('click', nextItem);
        prevBtn.addEventListener('click', prevItem);

        // Auto-rotate every 5 seconds
        // setInterval(nextItem, 5000);
    </script>

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