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

// Handle delete request
if (isset($_GET['delete'])) {
    $userId = intval($_GET['delete']);
    $deleteQuery = "DELETE FROM users WHERE id = ?";
    $stmt = $conn->prepare($deleteQuery);
    $stmt->bind_param("i", $userId);
    
    if ($stmt->execute()) {
        $_SESSION['success'] = 'The account is already deleted!';
        header("Location: ../admin/account-approval.php");
        exit(); // Redirect to the page displaying the table
    } else {
        echo "Error deleting record: " . $conn->error;
    }
}

// Query to get user information including additional fields from studentLrn
$query = "
    SELECT u.id, u.userRole, CONCAT(u.firstName, ' ', u.lastName) AS fullName, 
           COALESCE(s.lrn, 'N/A') AS lrn, u.phone, u.email,
           COALESCE(s.parent, 'N/A') AS parent, 
           COALESCE(s.address, 'N/A') AS address, 
           COALESCE(s.number, 'N/A') AS number
    FROM users u
    LEFT JOIN studentLrn s ON u.id = s.user_id
";
$result = $conn->query($query);

if ($result->num_rows > 0) {
  $tableRows = '';
  while ($row = $result->fetch_assoc()) {
      $deleteButton = '';
      if ($row['userRole'] !== 'admin') {
          $deleteButton = '<a href="?delete=' . htmlspecialchars($row['id']) . '" onclick="return confirm(\'Are you sure you want to delete this user?\');"><button class="button">Delete</button></a>';
      }

      $tableRows .= '<tr>
          <td>' . htmlspecialchars($row['userRole']) . '</td>
          <td>' . htmlspecialchars($row['fullName']) . '</td>
          <td>' . htmlspecialchars($row['phone']) . '</td>
          <td>' . htmlspecialchars($row['email']) . '</td>
          <td>' . htmlspecialchars($row['lrn']) . '</td>
          <td>' . htmlspecialchars($row['parent']) . '</td>
          <td>' . htmlspecialchars($row['address']) . '</td>
          <td>' . htmlspecialchars($row['number']) . '</td>
          <td>
              <a href="edit-account.php?id=' . htmlspecialchars($row['id']) . '"><button class="button">Edit</button></a>
              ' . $deleteButton . '
          </td>
      </tr>';
  }
} else {
  $tableRows = '<tr><td colspan="9">No records found.</td></tr>';
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
  <title>Accounts</title>
</head>
<body>

  <div class="navbar">
    <a href="../admin/account-approval.php" style="color:wheat">Accounts</a>
    <a href="../admin/section.php">Section</a>
    <a href="../admin/announcement.php">Announcement</a>
    <a href="../admin/registration.php">Registration</a>
    <a href="../admin/student-registration.php">Student Registration</a>
    <a href="../admin/calendar.php">Calendar</a>
    <a href="../controller/LogoutController/logOut.php">Logout</a>
  </div>

  <div class="container">
    <button class="button" id="openModal">Add Account</button>
    
    <!---------------ADD MODAL---------------------------->
    <div class="modal" id="accountModal">
      <div class="modal-content">
        <button class="modal-close" id="closeModal">&times;</button>
        <h2>Add New Account</h2>

        <form id="addAccountForm" method="post" action="../controller/AdminController/add-account.php">
          <input type="text" name="firstName" placeholder="First Name" required>
          <input type="text" name="lastName" placeholder="Last Name" required>
          <input type="email" name="email" placeholder="Email" required>
          <input type="number" name="phone" placeholder="Phone" required min="0">
          <!-- User role selection -->
          <label for="userRole">Role:</label>
          <select id="userRole" name="userRole" required>
            <option value="" disabled selected>Select your role</option>
            <option value="student">Student</option>
            <option value="teacher">Teacher</option>
          </select>

          <!-- LRN input field, initially hidden -->
          <div id="lrnField">
            <input type="number" name="lrn" placeholder="Learner Reference Number" min="0">
            <input type="text" name="parent" placeholder="Parent Name">
            <input type="text" name="address" placeholder="Parent Address">
            <input type="number" name="number" placeholder="Parent Contact Number" min="0">
          </div>

          <button type="submit">Add Account</button>
        </form>
      </div>
    </div>
     <!---------------ADD MODAL---------------------------->

    <table>
      <thead>
        <tr>
          <th>User Role</th>
          <th>Full Name</th>
          <th>Phone</th>
          <th>Email</th>
          <th>LRN (Only Student)</th>
          <th>Parent (Only Student)</th>
          <th>Address (Only Student)</th>
          <th>Emergency Contact (Only Student)</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <?php echo $tableRows; ?>
      </tbody>
    </table>
  </div>
  
  <script>
    // JavaScript to toggle the visibility of the LRN field based on the user role selection
    document.getElementById('userRole').addEventListener('change', function() {
      var lrnField = document.getElementById('lrnField');
      if (this.value === 'student') {
        lrnField.style.display = 'block';
      } else {
        lrnField.style.display = 'none';
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