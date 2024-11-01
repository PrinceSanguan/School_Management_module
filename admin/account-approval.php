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
  // Update the query to set is_archived to 1 instead of deleting the record
  $archiveQuery = "UPDATE users SET is_archived = 1 WHERE id = ?";
  $stmt = $conn->prepare($archiveQuery);
  $stmt->bind_param("i", $userId);
  
  if ($stmt->execute()) {
      $_SESSION['success'] = 'The account has been archived!';
      header("Location: ../admin/account-approval.php");
      exit(); // Redirect to the page displaying the table
  } else {
      echo "Error archiving record: " . $conn->error;
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
    WHERE u.is_archived = 0
";
$result = $conn->query($query);

if ($result->num_rows > 0) {
    $tableRows = '';
    while ($row = $result->fetch_assoc()) {
        $deleteButton = '';
        if ($row['userRole'] !== 'admin') {
            $deleteButton = '<a href="?delete=' . htmlspecialchars($row['id']) . '" onclick="return confirm(\'Are you sure you want to delete this user?\');"><button class="buttons">Delete</button></a>';
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
            <button class="button edit-btn" data-id="' . htmlspecialchars($row['id']) . '">Edit</button>
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

   <!-- Include DataTables CSS -->
   <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">

<!-- Include jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Include DataTables JS -->
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
  <title>Accounts</title>

</head>
<body>

  <div class="navbar">
    <a href="../admin/account-approval.php" style="color:wheat">Accounts</a>
    <a href="../admin/section.php">Section</a>
    <a href="../admin/announcement.php">Announcement</a>
    <a href="../admin/registration.php">Assign Teacher</a>
    <a href="../admin/student-registration.php">Assign Student</a>
    <a href="../admin/calendar.php">Calendar</a>
    <a href="../admin/archive.php">Archive</a>
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

<!---------------EDIT MODAL---------------------------->
<div id="editModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h2>Edit User</h2>
        <form id="editForm" method="post" action="edit-account.php">
            <label for="firstName">First Name:</label>
            <input type="text" id="firstName" name="firstName" required><br><br>

            <label for="lastName">Last Name:</label>
            <input type="text" id="lastName" name="lastName" required><br><br>

            <label for="phone">Phone:</label>
            <input type="text" id="phone" name="phone" required><br><br>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required><br><br>

            <input type="hidden" id="userId" name="userId">
            
            <button type="button" id="updateBtn">Update</button>
        </form>
    </div>
</div>
<!--------------- EDIT MODAL--------------------------->

     <table id="myTable">
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
  $(document).ready(function() {
      $('#myTable').DataTable({
          "lengthChange": false, // Disable length menu
          "searching": true,     // Enable the search box
          "paging": true         // Keep pagination enabled (optional)
      });
  });
</script>
  
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById("editModal");
    const closeModal = document.querySelector(".close");

    // Add event listener for all Edit buttons
    document.querySelectorAll('.edit-btn').forEach(button => {
        button.addEventListener('click', function() {
            const userId = this.getAttribute('data-id');  // Get user ID from data-id attribute

            // Fetch user data based on user ID
            fetch(`edit-account.php?id=${userId}`)
                .then(response => response.json())
                .then(data => {
                    // Populate the form with the fetched user details
                    document.getElementById('firstName').value = data.firstName;
                    document.getElementById('lastName').value = data.lastName;
                    document.getElementById('phone').value = data.phone;
                    document.getElementById('email').value = data.email;
                    document.getElementById('userId').value = userId; // Set the userId in the hidden input

                    // Show the modal
                    modal.style.display = "flex";
                })
                .catch(error => console.error('Error fetching user data:', error));
        });
    });

    // Close the modal when the 'x' is clicked
    closeModal.addEventListener('click', function() {
        modal.style.display = "none";
    });

    // Close the modal when clicking outside the modal content
    window.onclick = function(event) {
        if (event.target === modal) {
            modal.style.display = "none";
        }
    };

    // Add event listener for the Update button
    document.getElementById('updateBtn').addEventListener('click', function() {
        const form = document.getElementById('editForm');
        const formData = new FormData(form);

        // Send the form data using fetch
        fetch(form.action, {
            method: 'POST',
            body: formData
        })
        .then(response => {
            if (response.ok) {
                // Redirect to the account approval page after successful update
                window.location.href = '../admin/account-approval.php'; // Adjust as necessary
            } else {
                console.error('Error updating user data:', response.statusText);
            }
        })
        .catch(error => console.error('Error:', error));
    });
});
</script>

</body>
</html>