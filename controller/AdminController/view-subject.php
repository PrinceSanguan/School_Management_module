<?php
// Use an absolute path to include the database connection
include __DIR__ . '/../../database/database.php'; // Adjust path as necessary

session_start();

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$section_id = intval($_GET['section_id'] ?? 0);

// Fetch subjects associated with the section
$sql = "SELECT * FROM subject WHERE section_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $section_id);
$stmt->execute();
$result = $stmt->get_result();

$subjects = [];
while ($row = $result->fetch_assoc()) {
    $subjects[] = $row;
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="../../asset/css/account-approval.css">
  <!-- Sweet Alert -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <title>View Subject</title>
</head>
<style>
        .modal {
            display: none; /* Hide the modal initially */
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgb(0,0,0);
            background-color: rgba(0,0,0,0.4);
        }
        .modal-content {
            background-color: #fefefe;
            margin: 15% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
        }
        .modal-close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }
        .modal-close:hover,
        .modal-close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }
    </style>
<body>

<div class="navbar">
    <a href="../../admin/account-approval.php">Accounts</a>
    <a href="../../admin/section.php">Section</a>
    <a href="../../admin/announcement.php">Announcement</a>
    <a href="../../admin/registration.php">Assign Teacher</a>
    <a href="../../admin/student-registration.php">Assign Student</a>
    <a href="../../admin/calendar.php">Calendar</a>
    <a href="../../admin/archive.php">Archive</a>
    <a href="../../controller/LogoutController/logOut.php">Logout</a>
  </div>
  <br>

  <table>
    <thead>
      <tr>
        <th>Subjects</th>
        <th>Action</th>
      </tr>
    </thead>
<img src="../../admin/" alt="">
    <tbody>
      <?php foreach ($subjects as $subject): ?>
        <tr>
          <td><?php echo htmlspecialchars($subject['subject']); ?></td>
          <td>
          <button 
              class="add-module-btn" 
              data-subject-id="<?php echo $subject['id']; ?>" 
              style="background-color: #2196F3; color: white; border: none; padding: 10px 20px; text-align: center; text-decoration: none; display: inline-block; font-size: 16px; margin: 4px 2px; cursor: pointer; border-radius: 4px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);"
          >
              Add Module
          </button>
              <!-- View Module Button -->
              <button 
                  class="view-module-btn" 
                  data-subject-id="<?php echo $subject['id']; ?>" 
                  style="background-color: #2196F3; color: white; border: none; padding: 10px 20px; text-align: center; text-decoration: none; display: inline-block; font-size: 16px; margin: 4px 2px; cursor: pointer; border-radius: 4px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);"
              >
                  View Module
              </button>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>

  <!-- Add Module Modal -->
<div class="modal" id="addModuleModal">
    <div class="modal-content">
        <button class="modal-close" id="closeModuleModal">&times;</button>
        <h2>Add Module</h2>
        <form id="addModuleForm" method="post" action="../AdminController/add-module.php" enctype="multipart/form-data">
            <input type="text" name="subject_id" id="subjectIdInput">
            <label for="weekSelect">Select Week:</label>
            <select name="week" id="weekSelect" required>
                <option value="week1">Week 1</option>
                <option value="week2">Week 2</option>
                <option value="week3">Week 3</option>
                <option value="week4">Week 4</option>
            </select>
            <br><br>
            <label for="pdfFiles">Upload PDFs:</label>
            <input type="file" name="pdfFiles[]" id="pdfFiles" accept=".pdf" multiple required>
            <br><br>
            <button type="submit">Submit</button>
        </form>
    </div>
</div>
<!-- Add Module Modal -->

  <!-- View Module Modal -->
  <div class="modal" id="viewModuleModal">
    <div class="modal-content">
    <button class="modal-close" id="closeModuleModal">&times;</button>
      <h2>Module Details</h2>
      <div id="moduleContent">
        <!-- Content will be dynamically loaded here -->
      </div>
    </div>
  </div>
  <!-- View Module Modal -->

<!-- Add this JavaScript to handle the modal and form submission -->
<script>
        document.addEventListener('DOMContentLoaded', function () {
            // Open Modal
            document.querySelectorAll('.add-module-btn').forEach(button => {
                button.addEventListener('click', function () {
                    // Set the subject ID in the modal form
                    document.getElementById('subjectIdInput').value = this.getAttribute('data-subject-id');
                    // Show the modal
                    document.getElementById('addModuleModal').style.display = 'block';
                });
            });

            // Close Modal
            document.getElementById('closeModuleModal').addEventListener('click', function () {
                document.getElementById('addModuleModal').style.display = 'none';
            });

            // Close Modal when clicking outside of the modal content
            document.getElementById('addModuleModal').addEventListener('click', function (event) {
                if (event.target === this) {
                    this.style.display = 'none';
                }
            });
        });
    </script>
<!-- Add this JavaScript to handle the modal and form submission -->

  <!-- JavaScript to handle modal -->
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      const modal = document.getElementById('viewModuleModal');

      // Open Modal
      document.querySelectorAll('.view-module-btn').forEach(button => {
        button.addEventListener('click', function () {
          const subjectId = this.getAttribute('data-subject-id');
          fetchModuleDetails(subjectId);
          modal.style.display = 'block';
        });
      });

      // Close Modal when clicking outside of the modal content
      window.addEventListener('click', function (event) {
        if (event.target === modal) {
          modal.style.display = 'none';
        }
      });

      // Function to fetch module details
      function fetchModuleDetails(subjectId) {
        fetch(`../AdminController/view-subject-detail.php?subject_id=${subjectId}`)
          .then(response => response.json())
          .then(data => {
            const moduleContent = document.getElementById('moduleContent');
            moduleContent.innerHTML = '';

            const weeks = ['week1', 'week2', 'week3', 'week4'];

            weeks.forEach(week => {
              if (data[week] && data[week].length > 0) {
                const section = document.createElement('div');
                section.className = 'week-section';
                section.innerHTML = `<h3>${week.charAt(0).toUpperCase() + week.slice(1)}</h3>`;

                data[week].forEach(file => {
                  const fileLink = document.createElement('a');
                  fileLink.href = file;
                  fileLink.textContent = file.split('/').pop();
                  fileLink.target = '_blank';
                  section.appendChild(fileLink);
                  section.appendChild(document.createElement('br'));
                });

                moduleContent.appendChild(section);
              }
            });
          })
          .catch(error => {
            console.error('Error fetching module details:', error);
          });
      }
    });
  </script>
   <!-- JavaScript to handle modal -->
  
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