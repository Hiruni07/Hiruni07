<?php
// Include the database connection
$servername = "localhost";
$username = "root"; // Default username
$password = ""; // Default password (update if needed)
$dbname = "fitzone"; // Database name

// Create connection
$conn = mysqli_connect($servername, $username, $password, $dbname);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Initialize filter
$status_filter = isset($_GET['status']) ? $_GET['status'] : 'all';

// Fetch filtered appointments
$query = "SELECT * FROM appointments";
if ($status_filter !== 'all') {
    $query .= " WHERE status = '" . mysqli_real_escape_string($conn, $status_filter) . "'";
}
$query .= " ORDER BY appointment_date, created_at";
$result = mysqli_query($conn, $query);

// Handle delete request
if (isset($_POST['delete'])) {
    $appointment_id = $_POST['appointment_id'];
    $delete_query = "DELETE FROM appointments WHERE appointment_id = ?";
    $stmt = $conn->prepare($delete_query);
    $stmt->bind_param("i", $appointment_id);
    if ($stmt->execute()) {
        echo "<script>alert('Appointment deleted successfully'); window.location.href = window.location.href;</script>";
    } else {
        echo "<script>alert('Failed to delete appointment'); window.location.href = window.location.href;</script>";
    }
    exit();
}

// Handle edit request
if (isset($_POST['edit'])) {
    $appointment_id = $_POST['appointment_id'];
    $fetch_query = "SELECT * FROM appointments WHERE appointment_id = ?";
    $stmt = $conn->prepare($fetch_query);
    $stmt->bind_param("i", $appointment_id);
    $stmt->execute();
    $result_edit = $stmt->get_result();
    $appointment = $result_edit->fetch_assoc();
}

// Handle update form submission
if (isset($_POST['update'])) {
    $appointment_id = $_POST['appointment_id'];
    $appointment_date = $_POST['appointment_date'];
    $status = $_POST['status'];
    $update_query = "UPDATE appointments SET appointment_date = ?, status = ? WHERE appointment_id = ?";
    $stmt = $conn->prepare($update_query);
    $stmt->bind_param("ssi", $appointment_date, $status, $appointment_id);
    if ($stmt->execute()) {
        echo "<script>alert('Appointment updated successfully'); window.location.href = window.location.href;</script>";
    } else {
        echo "<script>alert('Failed to update appointment'); window.location.href = window.location.href;</script>";
    }
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Appointment Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <style>
        .footer {
            background-color: #343a40;
            color: #fff;
            text-align: center;
            padding: 10px 0;
        }
    </style>
    <script>
        function filterByStatus(status) {
            const urlParams = new URLSearchParams(window.location.search);
            if (status === 'all') {
                urlParams.delete('status');
            } else {
                urlParams.set('status', status);
            }
            window.location.search = urlParams.toString();
        }
    </script>
</head>
<body>
<div class="container mt-4">
    <h2 class="text-center mb-4">Appointment Management</h2>

    <!-- Filter Dropdown -->
    <div class="mb-4">
        <label for="status" class="me-2">Filter by Status:</label>
        <select name="status" id="status" class="form-select w-auto d-inline" onchange="filterByStatus(this.value)">
            <option value="all" <?php if ($status_filter === 'all') echo 'selected'; ?>>All</option>
            <option value="pending" <?php if ($status_filter === 'pending') echo 'selected'; ?>>Pending</option>
            <option value="confirmed" <?php if ($status_filter === 'confirmed') echo 'selected'; ?>>Confirmed</option>
            <option value="completed" <?php if ($status_filter === 'completed') echo 'selected'; ?>>Completed</option>
            <option value="canceled" <?php if ($status_filter === 'canceled') echo 'selected'; ?>>Canceled</option>
        </select>
    </div>

    <!-- Update Form -->
    <?php if (isset($appointment)): ?>
        <div class="card mb-4">
            <div class="card-header">Update Appointment</div>
            <div class="card-body">
                <form method="POST">
                    <input type="hidden" name="appointment_id" value="<?php echo $appointment['appointment_id']; ?>">
                    <div class="mb-3">
                        <label for="appointment_date" class="form-label">Appointment Date</label>
                        <input type="datetime-local" id="appointment_date" name="appointment_date" class="form-control" value="<?php echo date('Y-m-d\TH:i', strtotime($appointment['appointment_date'])); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select id="status" name="status" class="form-select" required>
                            <option value="pending" <?php if ($appointment['status'] === 'pending') echo 'selected'; ?>>Pending</option>
                            <option value="confirmed" <?php if ($appointment['status'] === 'confirmed') echo 'selected'; ?>>Confirmed</option>
                            <option value="completed" <?php if ($appointment['status'] === 'completed') echo 'selected'; ?>>Completed</option>
                            <option value="canceled" <?php if ($appointment['status'] === 'canceled') echo 'selected'; ?>>Canceled</option>
                        </select>
                    </div>
                    <button type="submit" name="update" class="btn btn-primary">Update</button>
                </form>
            </div>
        </div>
    <?php endif; ?>

    <!-- Appointment Table -->
    <div class="table-responsive">
        <table class="table table-striped table-bordered">
            <thead class="table-dark">
                <tr>
                    <th>Appointment ID</th>
                    <th>User ID</th>
                    <th>Trainer ID</th>
                    <th>Class ID</th>
                    <th>Appointment Date</th>
                    <th>Status</th>
                    <th>Created At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (mysqli_num_rows($result) > 0): ?>
                    <?php while ($row = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td><?php echo $row['appointment_id']; ?></td>
                            <td><?php echo $row['id']; ?></td>
                            <td><?php echo $row['trainer_id'] ?? 'N/A'; ?></td>
                            <td><?php echo $row['class_id'] ?? 'N/A'; ?></td>
                            <td><?php echo $row['appointment_date']; ?></td>
                            <td>
                                <span class="badge bg-<?php echo $row['status'] === 'pending' ? 'warning' : ($row['status'] === 'confirmed' ? 'info' : ($row['status'] === 'completed' ? 'success' : 'danger')); ?>">
                                    <?php echo ucfirst($row['status']); ?>
                                </span>
                            </td>
                            <td><?php echo $row['created_at']; ?></td>
                            <td>
                                <form method="POST" class="d-inline">
                                    <input type="hidden" name="appointment_id" value="<?php echo $row['appointment_id']; ?>">
                                    <button type="submit" name="edit" class="btn btn-sm btn-warning">Edit</button>
                                </form>
                                <form method="POST" class="d-inline">
                                    <input type="hidden" name="appointment_id" value="<?php echo $row['appointment_id']; ?>">
                                    <button type="submit" name="delete" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8" class="text-center">No appointments found</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
