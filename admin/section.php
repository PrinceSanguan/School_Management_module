<?php
include "../database/database.php";

session_start();

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}


// Query to fetch sections
$query = "SELECT id, section FROM section";
$result = $conn->query($query);

if (!$result) {
    $_SESSION['error'] = 'Failed to fetch sections: ' . $conn->error;
    $conn->close();
    header("Location: /school-management/admin/section.php");
    exit();
}

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
  <style>
    /* Basic styling for the button */
.button {
    padding: 10px 20px;
    font-size: 16px;
    cursor: pointer;
}

/* The Modal (background) */
.modal {
    display: none; /* Hidden by default */
    position: fixed;
    z-index: 1; /* Sit on top */
    left: 0;
    top: 0;
    width: 100%; /* Full width */
    height: 100%; /* Full height */
    overflow: auto; /* Enable scroll if needed */
    background-color: rgb(0,0,0); /* Fallback color */
    background-color: rgba(0,0,0,0.4); /* Black w/ opacity */
}

/* Modal Content */
.modal-content {
    background-color: #fefefe;
    margin: 15% auto; /* 15% from the top and centered */
    padding: 20px;
    border: 1px solid #888;
    width: 80%; /* Could be more or less, depending on screen size */
}

/* The Close Button */
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
</head>
<body>
  <div class="navbar">
    <a href="../admin/account-approval.php">Accounts</a>
    <a href="../admin/section.php">Section</a>
    <a href="#">Settings</a>
    <a href="../controller/LogoutController/logOut.php">Logout</a>
  </div>

  <div class="container">

     <!-- Button to open the modal -->
     <button class="button" id="openModal">Add Section</button>


    <!-- Table to display sections -->
    <table>
        <thead>
            <tr>
                <th>Section</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['section']); ?></td>
                    <td>
                       <!-- Button to open the modal -->
                        <button id="openSubjectBtn" data-section-id="<?php echo $row['id']; ?>">Add Subject</button>
                        <button onclick="location.href='view_section.php?section_id=<?php echo $row['id']; ?>'">View</button>
                        <button onclick="location.href='../controller/AdminController/delete-section.php?section_id=<?php echo $row['id']; ?>'">Delete</button>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <?php
    $result->free();
    $conn->close();
    ?>

  </div>

    <!-- Add Section Modal -->
    <div class="modal" id="accountModal">
        <div class="modal-content">
            <button class="modal-close" id="closeModal">&times;</button>
            <h2>Add Section</h2>

            <form id="addSectionForm" method="post" action="../controller/AdminController/add-section.php">
                <input type="text" name="section" placeholder="Section" required>
                <button type="submit">Add Section</button>
            </form>
        </div>
    </div>
    <!-- Add Section Modal -->

    <!-- Add Subject Modal -->
    <div class="modal" id="addSubjectModal">
        <div class="modal-content">
            <button class="modal-close" id="closeModal">&times;</button>
            <h2>Add Subject</h2>
            <form id="addSubjectForm" action="../controller/AdminController/add-subject.php" method="post">
                <input type="hidden" name="section_id" id="sectionIdInput">
                <input type="text" name="subject" placeholder="Subject" required>
                <button type="submit">Add Subject</button>
            </form>
        </div>
    </div>
  <!-- Add Subject Modal -->



    <script>
      // Get the modal
      var modal = document.getElementById("accountModal");

      // Get the button that opens the modal
      var openModalButton = document.getElementById("openModal");

      // Get the <span> element that closes the modal
      var closeModalButton = document.getElementById("closeModal");

      // When the user clicks the button, open the modal 
      openModalButton.onclick = function() {
          modal.style.display = "block";
      }

      // When the user clicks on <span> (x), close the modal
      closeModalButton.onclick = function() {
          modal.style.display = "none";
      }

      // When the user clicks anywhere outside of the modal, close it
      window.onclick = function(event) {
          if (event.target == modal) {
              modal.style.display = "none";
          }
      }
    </script>

    <script>
      // Get modal elements
var modal = document.getElementById("addSubjectModal");
var closeModalButton = document.getElementById("closeModal");
var openSubjectButtons = document.querySelectorAll("#openSubjectBtn");
var sectionIdInput = document.getElementById("sectionIdInput");

// Function to open the modal and set the section_id
function openModal(sectionId) {
    sectionIdInput.value = sectionId;
    modal.style.display = "block";
}

// Attach event listeners to open subject buttons
openSubjectButtons.forEach(button => {
    button.addEventListener("click", function() {
        var sectionId = this.getAttribute("data-section-id");
        openModal(sectionId);
    });
});

// When the user clicks on <span> (x), close the modal
closeModalButton.onclick = function() {
    modal.style.display = "none";
}

// When the user clicks anywhere outside of the modal, close it
window.onclick = function(event) {
    if (event.target === modal) {
        modal.style.display = "none";
    }
}
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

