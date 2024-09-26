<?php
include "../database/database.php";

session_start();

// Check if the user is logged in
if (!isset($_SESSION['userId']) || !isset($_SESSION['userRole'])) {
  $_SESSION['error'] = "Please log in to access this page.";
  header("Location: ../../login.php");
  exit();
}

// Check if the user is an admin or the verified user
if ($_SESSION['userRole'] !== 'admin' && $_SESSION['userId'] != $subject['userId']) {
  $_SESSION['error'] = "You do not have permission to access this page.";
  header("Location: ../../index.php");
  exit();
}

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Fetch data from the database
$query = "SELECT id, announcement, view, image_path FROM announcement";
$result = $conn->query($query);

if (!$result) {
    die("Query failed: " . $conn->error);
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
</head>
<body>

  <div class="navbar">
    <a href="../admin/account-approval.php">Accounts</a>
    <a href="../admin/section.php">Section</a>
    <a href="../admin/announcement.php" style="color:wheat">Announcement</a>
    <a href="../admin/registration.php">Assign Teacher</a>
    <a href="../admin/student-registration.php">Student Registration</a>
    <a href="../admin/calendar.php">Calendar</a>
    <a href="../admin/archive.php">Archive</a>
    <a href="../controller/LogoutController/logOut.php">Logout</a>
  </div>

  <div class="container">
    <button class="button" id="openModal" style="margin-bottom: 10px;">Add Announcement</button>

    <!---------------ADD MODAL---------------------------->
    <div class="modal" id="accountModal">
      <div class="modal-content">
        <button class="modal-close" id="closeModal">&times;</button>
        <h2>Add Announcement</h2>

        <!-- Form to add an announcement -->
        <form id="addAccountForm" method="post" action="../controller/AdminController/add-announcement.php" enctype="multipart/form-data">
          
          <!-- Select option to choose what to post -->
          <label for="postType">What do you want to post?</label>
          <select name="postType" id="postType">
            <option value="" disabled selected>Select an option</option>
            <option value="Image">Image</option>
            <option value="Text">Text</option>
          </select>

          <!-- Hidden image input field -->
          <div id="imageUpload" style="display: none;">
            <label>Upload Image</label>
            <input type="file" name="image" id="imageFile" accept=".png, .jpg, .jpeg">
          </div>

          <!-- Hidden textarea input field -->
          <div id="textInput" style="display: none;">
            <label>Announcement Text</label>
            <textarea name="announcement" rows="5" cols="65" placeholder="Place your announcement here"></textarea>
          </div>

          <!-- Select who can view the announcement -->
          <label for="viewers">Who can view this?</label>
          <select name="view" id="view">
            <option value="" disabled selected>Select a viewer</option>
            <option value="student">Student</option>
            <option value="teacher">Teacher</option>
            <option value="studentTeacher">Teacher and Student</option>
          </select>

          <!-- Submit button -->
          <button type="submit">Add Announcement</button>
        </form>
      </div>
    </div>
     <!---------------ADD MODAL---------------------------->
     <table>
  <thead>
    <tr>
      <th>Announcement</th>
      <th>Who can see it?</th>
      <th>Action</th>
    </tr>
  </thead>
  <tbody>
    <?php
    // Check if there are rows
    if ($result->num_rows > 0) {
        // Output data of each row
        while ($row = $result->fetch_assoc()) {
            // Ensure the 'id' key exists
            if (isset($row['id'])) {
                // Map view to readable format
                $viewText = '';
                switch ($row['view']) {
                    case 'student':
                        $viewText = 'Student';
                        break;
                    case 'teacher':
                        $viewText = 'Teacher';
                        break;
                    case 'studentTeacher':
                        $viewText = 'Teacher and Student';
                        break;
                }

                // Get announcement and image path
                $announcement = htmlspecialchars($row['announcement']);
                $imagePath = htmlspecialchars($row['image_path']);
                $viewText = htmlspecialchars($viewText);
                $id = urlencode($row['id']);

                ?>
                <tr>
                  <td>
                    <?php
                    // Display the announcement text or image based on what exists
                    if (!empty($announcement)) {
                        echo $announcement;
                    } elseif (!empty($imagePath)) {
                        echo '<img src="../../' . $imagePath . '" alt="Announcement Image" style="max-width: 100px; max-height: 100px;">';
                    } else {
                        echo 'No content available';
                    }
                    ?>
                  </td>
                  <td><?php echo $viewText; ?></td>
                  <td>
                    <button 
                      type="button" 
                      onclick="location.href='../controller/AdminController/delete-announcement.php?id=<?php echo $id; ?>'"
                      style="background-color: #f44336; color: white; border: none; padding: 10px 20px; text-align: center; text-decoration: none; display: inline-block; font-size: 16px; margin: 4px 2px; cursor: pointer; border-radius: 4px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);"
                    >
                      Delete
                    </button>
                  </td>
                </tr>
                <?php
            } else {
                // If 'id' is not present in the result
                echo '<tr><td colspan="3">Error: Missing ID for announcement</td></tr>';
            }
        }
    } else {
        echo '<tr><td colspan="3">No announcements found</td></tr>';
    }
    ?>
  </tbody>
</table>
  </div>
  
  <!-- JavaScript to handle showing/hiding fields -->
<script>
  document.getElementById('postType').addEventListener('change', function() {
    var postType = this.value;
    var imageUpload = document.getElementById('imageUpload');
    var textInput = document.getElementById('textInput');

    // Hide both initially
    imageUpload.style.display = 'none';
    textInput.style.display = 'none';

    // Show image upload if "Image" is selected
    if (postType === 'Image') {
      imageUpload.style.display = 'block';
    }
    // Show textarea if "Text" is selected
    else if (postType === 'Text') {
      textInput.style.display = 'block';
    }
  });
</script>
  
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

</body>
</html>