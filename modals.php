<!-- Edit Profile Modal -->
<div class="modal fade" id="editProfileModal" tabindex="-1" aria-labelledby="editProfileModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editProfileModalLabel">Important Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editProfileForm" action="modals-backend.php" method="post" onsubmit="return validateForm()">
                    <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($user_id); ?>">
                    <div class="mb-3">
                        <label for="first_name" class="form-label">First Name</label>
                        <input type="text" class="form-control border-dark border-2" id="first_name" name="first_name" value="<?php echo htmlspecialchars($user['first_name']); ?>" readonly>
                    </div>
                    <div class="mb-3">
                        <label for="last_name" class="form-label">Last Name</label>
                        <input type="text" class="form-control border-dark border-2" id="last_name" name="last_name" value="<?php echo htmlspecialchars($user['last_name']); ?>" readonly>
                    </div>
                    <div class="mb-3">
                        <label for="section" class="form-label">Section</label>
                        <input type="text" class="form-control border-dark border-2" id="section" name="section" placeholder="To Be Announced" value="<?php echo htmlspecialchars($user['section']); ?>" readonly>
                    </div>
                    <div class="mb-3">
                        <label for="lrn" class="form-label">LRN</label>
                        <input type="text" class="form-control border-dark border-2" id="lrn" name="lrn" value="<?php echo htmlspecialchars($user['lrn']); ?>" readonly>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control border-dark border-2" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" readonly>
                    </div>
                    <div class="modal-header">
                    <h5 class="modal-title" id="editProfileModalLabel">Edit Profile</h5>
                    </div>
                    <div class="mb-3">
                        <label for="parent_name" class="form-label">Parent Name</label>
                        <input type="text" class="form-control border-dark border-2" id="parent_name" name="parent_name" value="<?php echo htmlspecialchars($user['parent_name']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="parent_email" class="form-label">Parent Email</label>
                        <input type="email" class="form-control border-dark border-2" id="parent_email" name="parent_email" value="<?php echo htmlspecialchars($user['parent_email']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="parent_contact" class="form-label">Parent Contact</label>
                        <input type="number" class="form-control border-dark border-2" id="parent_contact" name="parent_contact" value="<?php echo htmlspecialchars($user['parent_contact']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password (Leave blank to keep unchanged)</label>
                        <input type="password" class="form-control border-dark border-2" placeholder="Password" id="password" name="password">
                    </div>
                    <div class="mb-3">
                        <label for="confirm_password" class="form-label">Confirm Password</label>
                        <input type="password" class="form-control border-black border-2" placeholder="Confirm Password" id="confirm_password" name="confirm_password">
                    </div>
                    <button type="submit" class="btn btn-primary">Save changes</button>
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Close</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<!-- Modal HTML -->
<div class="modal fade" id="subjectModal" tabindex="-1" aria-labelledby="subjectModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="subjectModalLabel">Subject Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Content for the modal will be included here -->
                <?php
                if (isset($_SESSION['user_id'])) {
                    include 'fetch-subject.php';
                } else {
                    echo '<p>You must be logged in to view subject details.</p>';
                }
                ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>



<script>
function validateForm() {
    var password = document.getElementById('password').value;
    var confirmPassword = document.getElementById('confirm_password').value;

    if (password !== confirmPassword) {
        alert("Passwords do not match.");
        return false; 
    }
    return true; 
}
</script>
