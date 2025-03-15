<?php
// Enable error reporting for debugging
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// Database connection details
$servername = "localhost";
$username = "root"; // Replace with your MySQL username
$password = ""; // Replace with your MySQL password
$dbname = "fitzone"; // Replace with your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize message and edit variables
$message = "";
$edit_data = null;

// Handle delete request via POST
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['delete_id'])) {
    $delete_id = intval($_POST['delete_id']);
    $stmt = $conn->prepare("DELETE FROM membership_registrations WHERE id = ?");
    $stmt->bind_param("i", $delete_id);
    if ($stmt->execute()) {
        $message = "Record deleted successfully.";
    } else {
        $message = "Failed to delete the record.";
    }
}

// Handle edit request via POST
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['load_edit'])) {
    $edit_id = intval($_POST['edit_id']);
    $stmt = $conn->prepare("SELECT * FROM membership_registrations WHERE id = ?");
    $stmt->bind_param("i", $edit_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $edit_data = $result->fetch_assoc();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST['edit_member'])) {
        $edit_id = intval($_POST['edit_id']);
        $full_name = trim($_POST['full_name']);
        $address = trim($_POST['address']);
        $age = intval($_POST['age']);
        $gender = trim($_POST['gender']);
        $phone = trim($_POST['phone']);
        $email = trim($_POST['email']);
        $package = trim($_POST['package']);
        $status = trim($_POST['status']);

        $stmt = $conn->prepare("UPDATE membership_registrations SET full_name = ?, address = ?, age = ?, gender = ?, phone = ?, email = ?, package = ?, status = ? WHERE id = ?");
        $stmt->bind_param("ssisssssi", $full_name, $address, $age, $gender, $phone, $email, $package, $status, $edit_id);
        if ($stmt->execute()) {
            $message = "Record updated successfully.";
        } else {
            $message = "Failed to update the record.";
        }
    } elseif (isset($_POST['add_member'])) {
        $full_name = trim($_POST['full_name']);
        $address = trim($_POST['address']);
        $age = intval($_POST['age']);
        $gender = trim($_POST['gender']);
        $phone = trim($_POST['phone']);
        $email = trim($_POST['email']);
        $package = trim($_POST['package']);
        $status = trim($_POST['status']);

        $stmt = $conn->prepare("INSERT INTO membership_registrations (full_name, address, age, gender, phone, email, package, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssisssss", $full_name, $address, $age, $gender, $phone, $email, $package, $status);
        if ($stmt->execute()) {
            $message = "Member added successfully.";
        } else {
            $message = "Failed to add member.";
        }
    }
}

// Fetch membership records
$sql = "SELECT * FROM membership_registrations";
$result = $conn->query($sql);

// Fetch packages for the dropdown
$packages_sql = "SELECT name FROM memberships";
$packages_result = $conn->query($packages_sql);
$packages = [];
if ($packages_result->num_rows > 0) {
    while ($package_row = $packages_result->fetch_assoc()) {
        $packages[] = $package_row['name'];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Membership Management</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color:rgb(58, 60, 62);
            margin: 0;
            padding: 0;
        }
        .container {
            width: 90%;
            margin: 20px auto;
            background: #ffffff;
            padding: 20px;
            box-shadow: 0px 4px 20px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
        }
        h1 {
    color: #333;
    text-align: center;
    font-size: 24px;
    margin-bottom: 20px;
    font-weight: bold; /* Added to make the text bold */
}

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table th, table td {
            padding: 15px;
            border: 1px solid #ddd;
            text-align: center;
            color: #555;
            font-size: 14px;
        }
        table th {
            background-color:rgb(42, 44, 46);
            color: white;
            text-transform: uppercase;
        }
        .actions button {
            padding: 8px 12px;
            margin: 2px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 12px;
        }
        .actions .edit {
            background-color: #28a745;
            color: white;
        }
        .actions .delete {
            background-color: #dc3545;
            color: white;
        }
        .add-form, .edit-form {
            margin-top: 20px;
            background: #f8f9fa;
            padding: 15px;
            border-radius: 10px;
        }
        .add-form h2, .edit-form h2 {
            color: #333;
            margin-bottom: 15px;
        }
        .add-form input, .edit-form input, .add-form select, .edit-form select {
            width: calc(100% - 20px);
            margin-bottom: 10px;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
        }
        .add-form button, .edit-form button {
            padding: 10px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .message {
            margin: 10px 0;
            padding: 15px;
            border-radius: 5px;
            font-size: 14px;
            color: white;
        }
        .message.success {
            background-color: #28a745;
        }
        .message.error {
            background-color: #dc3545;
        }
    </style>
</head>
<body>
<div class="container">
    <h1>Membership Management</h1>

    <?php if ($message): ?>
        <div class="message <?= strpos($message, 'successfully') !== false ? 'success' : 'error' ?>">
            <?= htmlspecialchars($message) ?>
        </div>
    <?php endif; ?>

    <!-- Add/Edit Member Form -->
    <form class="add-form" method="POST">
        <h2><?= $edit_data ? 'Edit Member' : 'Add Member' ?></h2>
        <input type="hidden" name="edit_id" value="<?= $edit_data['id'] ?? '' ?>">
        <input type="text" name="full_name" placeholder="Full Name" value="<?= htmlspecialchars($edit_data['full_name'] ?? '') ?>" required>
        <input type="text" name="address" placeholder="Address" value="<?= htmlspecialchars($edit_data['address'] ?? '') ?>" required>
        <input type="number" name="age" placeholder="Age" value="<?= htmlspecialchars($edit_data['age'] ?? '') ?>" required>
        <select name="gender" required>
            <option value="male" <?= (isset($edit_data['gender']) && $edit_data['gender'] === 'male') ? 'selected' : '' ?>>Male</option>
            <option value="female" <?= (isset($edit_data['gender']) && $edit_data['gender'] === 'female') ? 'selected' : '' ?>>Female</option>
            <option value="other" <?= (isset($edit_data['gender']) && $edit_data['gender'] === 'other') ? 'selected' : '' ?>>Other</option>
        </select>
        <input type="text" name="phone" placeholder="Phone" value="<?= htmlspecialchars($edit_data['phone'] ?? '') ?>" required>
        <input type="email" name="email" placeholder="Email" value="<?= htmlspecialchars($edit_data['email'] ?? '') ?>" required>
        <select name="package" required>
            <option value="">Select Package</option>
            <?php foreach ($packages as $package): ?>
                <option value="<?= htmlspecialchars($package) ?>" <?= (isset($edit_data['package']) && $edit_data['package'] === $package) ? 'selected' : '' ?>><?= htmlspecialchars($package) ?></option>
            <?php endforeach; ?>
        </select>
        <select name="status" required>
            <option value="pending" <?= (isset($edit_data['status']) && $edit_data['status'] === 'pending') ? 'selected' : '' ?>>Pending</option>
            <option value="accept" <?= (isset($edit_data['status']) && $edit_data['status'] === 'accept') ? 'selected' : '' ?>>Accept</option>
            <option value="cancel" <?= (isset($edit_data['status']) && $edit_data['status'] === 'cancel') ? 'selected' : '' ?>>Cancel</option>
        </select>
        <button type="submit" name="<?= $edit_data ? 'edit_member' : 'add_member' ?>">
            <?= $edit_data ? 'Update Member' : 'Add Member' ?>
        </button>
    </form>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Full Name</th>
                <th>Address</th>
                <th>Age</th>
                <th>Gender</th>
                <th>Phone</th>
                <th>Email</th>
                <th>Package</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['id']) ?></td>
                        <td><?= htmlspecialchars($row['full_name']) ?></td>
                        <td><?= htmlspecialchars($row['address']) ?></td>
                        <td><?= htmlspecialchars($row['age']) ?></td>
                        <td><?= htmlspecialchars($row['gender']) ?></td>
                        <td><?= htmlspecialchars($row['phone']) ?></td>
                        <td><?= htmlspecialchars($row['email']) ?></td>
                        <td><?= htmlspecialchars($row['package']) ?></td>
                        <td><?= htmlspecialchars($row['status']) ?></td>
                        <td class="actions">
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="edit_id" value="<?= $row['id'] ?>">
                                <button class="edit" type="submit" name="load_edit">Edit</button>
                            </form>
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="delete_id" value="<?= $row['id'] ?>">
                                <button class="delete" type="submit">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="10">No memberships found.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
</body>
</html>
