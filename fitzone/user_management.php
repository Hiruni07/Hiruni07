<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "fitzone";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Add user logic
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_user'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role'];

    $sql = "INSERT INTO users (name, email, password, role) VALUES ('$name', '$email', '$password', '$role')";
    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('User added successfully!');</script>";
    } else {
        echo "<script>alert('Error: " . $conn->error . "');</script>";
    }
}

if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $sql = "DELETE FROM users WHERE user_id = $user_id";
    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('User deleted successfully!'); window.location.href = 'user_management.php';</script>";
    } else {
        echo "<script>alert('Error: " . $conn->error . "');</script>";
    }
}

// Fetch users
$sql = "SELECT * FROM users";
$result = $conn->query($sql);
?>

<div class="container my-5">
    <!-- Add User Section -->
    <div class="card shadow-lg">
        <div class="card-header bg-primary text-white">
            <h3><i class="fas fa-user-plus"></i> Add New User</h3>
        </div>
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
        <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
        <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
        <div class="card-body">
            <form method="POST" action="">
                <div class="mb-3">
                    <label for="name" class="form-label">Name:</label>
                    <input type="text" class="form-control" id="name" name="name" placeholder="Enter user's name" required>
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Email:</label>
                    <input type="email" class="form-control" id="email" name="email" placeholder="Enter email address" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password:</label>
                    <input type="password" class="form-control" id="password" name="password" placeholder="Enter a secure password" required>
                </div>
                <div class="mb-3">
                    <label for="role" class="form-label">Role:</label>
                    <select class="form-select" id="role" name="role" required>
                        <option value="Admin">Admin</option>
                        <option value="Staff">Staff</option>
                        <option value="Customer">Customer</option>
                    </select>
                </div>
                <button type="submit" name="add_user" class="btn btn-primary"><i class="fas fa-save"></i> Add User</button>
            </form>
        </div>
    </div>

    <!-- User Management Table -->
    <div class="card shadow-lg mt-4">
        <div class="card-header bg-primary text-white">
            <h3><i class="fas fa-users"></i> User Management</h3>
        </div>
        <div class="card-body">
            <table class="table table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result->num_rows > 0): ?>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr user_id="user-row-<?= $row['user_id']; ?>">
                                <td><?= htmlspecialchars($row['name']); ?></td>
                                <td><?= htmlspecialchars($row['email']); ?></td>
                                <td><?= htmlspecialchars($row['role']); ?></td>
                                <td>
                                <a href="javascript:void(0);" onclick="deleteUser(<?= $row['user_id']; ?>)" class="btn btn-danger btn-sm" title="Delete User">
                                <i class="fas fa-trash-alt"></i>
                                </a>

                                    <button class="btn btn-warning btn-sm" title="Edit User" data-bs-toggle="modal" data-bs-target="#updateModal<?= $row['user_id']; ?>">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                </td>
                            </tr>

                            <!-- Update User Modal -->
                            <div class="modal fade" id="updateModal<?= $row['user_id']; ?>" tabindex="-1" aria-labelledby="updateModalLabel<?= $row['user_id']; ?>" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header bg-primary text-white">
                                            <h5 class="modal-title" id="updateModalLabel<?= $row['user_id']; ?>">Update User</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                        <form id="editForm<?= $row['user_id']; ?>" onsubmit="updateUser(event, <?= $row['user_id']; ?>)">
                                                <input type="hidden" name="user_id" value="<?= $row['user_id']; ?>">
                                                <div class="mb-3">
                                                    <label for="name<?= $row['user_id']; ?>" class="form-label">Name:</label>
                                                    <input type="text" class="form-control" id="name<?= $row['user_id']; ?>" name="name" value="<?= htmlspecialchars($row['name']); ?>" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="email<?= $row['user_id']; ?>" class="form-label">Email:</label>
                                                    <input type="email" class="form-control" id="email<?= $row['user_id']; ?>" name="email" value="<?= htmlspecialchars($row['email']); ?>" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="role<?= $row['user_id']; ?>" class="form-label">Role:</label>
                                                    <select class="form-select" id="role<?= $row['user_id']; ?>" name="role" required>
                                                        <option value="Admin" <?= $row['role'] == 'Admin' ? 'selected' : ''; ?>>Admin</option>
                                                        <option value="Staff" <?= $row['role'] == 'Staff' ? 'selected' : ''; ?>>Staff</option>
                                                        <option value="Customer" <?= $row['role'] == 'Customer' ? 'selected' : ''; ?>>Customer</option>
                                                    </select>
                                                </div>
                                                <button type="submit" name="update_user" class="btn btn-primary">
                                                    <i class="fas fa-save"></i> Save Changes
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" class="text-center">No users found</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    function deleteUser(userId) {
        if (confirm("Are you sure you want to delete this user?")) {
            fetch(`delete_user.php?user_id=${userId}`, {
                method: 'GET',
            })
            .then(response => response.text())
            .then(data => {
                alert(data); // Show success or error message
                // Optionally remove the user's row from the table
                const userRow = document.getElementById(`user-row-${userId}`);
                if (userRow) {
                    userRow.remove();
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Failed to delete the user.');
            });
        }
    }

    function updateUser(event, userId) {
    event.preventDefault(); // Prevent default form submission

    const form = document.getElementById(`editForm${userId}`);
    const formData = new FormData(form);

    fetch('update_user.php', {
        method: 'POST',
        body: formData,
    })
        .then(response => response.text())
        .then(data => {
            alert(data); // Show the server response as a pop-up

            // Update the table row dynamically if the update was successful
            if (data.includes("successfully")) {
                const userRow = document.getElementById(`user-row-${userId}`);
                if (userRow) {
                    userRow.cells[0].innerText = formData.get('name');
                    userRow.cells[1].innerText = formData.get('email');
                    userRow.cells[2].innerText = formData.get('role');
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Failed to update the user.');
        });
}

</script>



<?php
// Update user logic
if (isset($_POST['update_user'])) {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $email = $_POST['email'];
    $role = $_POST['role'];

    $sql = "UPDATE users SET name='$name', email='$email', role='$role' WHERE id=$id";
    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('User updated successfully!'); window.location.href = 'user_management.php';</script>";
    } else {
        echo "<script>alert('Error: " . $conn->error . "');</script>";
    }
}

// Close connection
$conn->close();
?>