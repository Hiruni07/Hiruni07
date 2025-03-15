<?php
// Database connection
$conn = new mysqli("localhost", "root", "", "fitzone");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Determine the status filter
$status_filter = isset($_GET['status']) ? $conn->real_escape_string($_GET['status']) : 'all';

// Base query for fetching data
$query = "SELECT query.id, query.user_id, query.subject, query.message, query.status, query.created_at, users.name AS user_name 
          FROM query 
          JOIN users ON query.user_id = users.user_id"; // Use the correct column name for the foreign key

// Add a filter if a specific status is selected
if ($status_filter !== 'all') {
    $query .= " WHERE query.status = ?";
}

// Prepare the SQL statement
$stmt = $conn->prepare($query);

if (!$stmt) {
    die("Failed to prepare statement: " . $conn->error); // Error message if preparation fails
}

// Bind the parameter if a filter is applied
if ($status_filter !== 'all') {
    $stmt->bind_param("s", $status_filter);
}

// Execute the statement
if (!$stmt->execute()) {
    die("Failed to execute statement: " . $stmt->error);
}

// Fetch results
$result = $stmt->get_result();

// Update status if admin selects a new status
if (isset($_POST['update_status'])) {
    $query_id = $_POST['query_id'];
    $new_status = $_POST['new_status'];

    $update_query = "UPDATE query SET status = ? WHERE id = ?";
    $update_stmt = $conn->prepare($update_query);
    $update_stmt->bind_param("si", $new_status, $query_id);
    $update_stmt->execute();

    // Reload the page to see changes
    header("Location: user_queries.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Queries Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <style>
        body {
            background-color: #1c1e21;
            color: #f5f5f5;
            font-family: Arial, sans-serif;
        }

        .container {
            max-width: 100%;
            margin: 20px auto;
            padding: 20px;
            background-color: #2a2d31;
            border-radius: 4px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
            overflow-x: auto;
        }

        .card {
            background-color: #3a3d42;
            border: none;
            border-radius: 8px;
        }

        .card-header {
            background-color: #44474d;
            border-bottom: 2px solid #575b63;
            font-size: 1.5rem;
            font-weight: bold;
            color: #ffffff;
            text-align: center;
        }

        .card-body {
            padding: 20px;
        }

        .form-label {
            color: #cccccc;
        }

        .form-select {
            background-color: #2a2d31;
            color: #f5f5f5;
            border: 1px solid #575b63;
            border-radius: 5px;
        }

        .form-select:focus {
            border-color: #00bcd4;
            box-shadow: 0 0 5px rgba(0, 188, 212, 0.5);
        }

        .table {
            width: 100%;
            color: #f5f5f5;
            background-color: #3a3d42;
            border-collapse: collapse;
        }

        .table thead {
            background-color: #44474d;
            color: #ffffff;
        }

        .table tbody tr:hover {
            background-color: #575b63;
            transition: background-color 0.3s;
        }

        .nav-link:hover {
            color: #00bcd4;
        }
    </style>
</head>
<body>
<div class="container mt-5">
    <div class="card">
        <div class="card-header text-center">Queries Management</div>
        <div class="card-body">
            <div class="mb-3">
                <label for="statusFilter" class="form-label">Filter by Status:</label>
                <select id="statusFilter" class="form-select" onchange="filterStatus(this.value)">
                    <option value="all" <?= $status_filter === 'all' ? 'selected' : '' ?>>All</option>
                    <option value="pending" <?= $status_filter === 'pending' ? 'selected' : '' ?>>Pending</option>
                    <option value="read" <?= $status_filter === 'read' ? 'selected' : '' ?>>Read</option>
                    <option value="closed" <?= $status_filter === 'closed' ? 'selected' : '' ?>>Closed</option>
                </select>
            </div>

            <table class="table table-bordered">
                <thead class="table-dark">
                    <tr>
                        <th>Query ID</th>
                        <th>User ID</th>
                        <th>User Name</th>
                        <th>Subject</th>
                        <th>Message</th>
                        <th>Status</th>
                        <th>Created At</th>
                        <th>Change Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($result && $result->num_rows > 0):
                        while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['id'] ?? 'N/A') ?></td>
                                <td><?= htmlspecialchars($row['user_id'] ?? 'N/A') ?></td>
                                <td><?= htmlspecialchars($row['user_name'] ?? 'N/A') ?></td>
                                <td><?= htmlspecialchars($row['subject'] ?? 'N/A') ?></td>
                                <td><?= nl2br(htmlspecialchars($row['message'] ?? 'N/A')) ?></td>
                                <td><?= htmlspecialchars($row['status'] ?? 'N/A') ?></td>
                                <td><?= htmlspecialchars($row['created_at'] ?? 'N/A') ?></td>
                                <td>
                                    <!-- Dropdown for changing status -->
                                    <form action="user_queries.php" method="POST">
                                        <input type="hidden" name="query_id" value="<?= htmlspecialchars($row['id']) ?>">
                                        <select name="new_status" class="form-select" required>
                                            <option value="pending" <?= $row['status'] === 'pending' ? 'selected' : '' ?>>Pending</option>
                                            <option value="read" <?= $row['status'] === 'read' ? 'selected' : '' ?>>Read</option>
                                            <option value="closed" <?= $row['status'] === 'closed' ? 'selected' : '' ?>>Closed</option>
                                        </select>
                                        <button type="submit" name="update_status" class="btn btn-warning mt-2">Update</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8">No queries found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    function filterStatus(status) {
        window.location.href = "user_queries.php?status=" + status;
    }
</script>
</body>
</html>

<?php
// Close statement and connection
$stmt->close();
$conn->close();
?>
