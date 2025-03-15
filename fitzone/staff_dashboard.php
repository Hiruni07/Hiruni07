<?php
// Start session
session_start();

// Database connection
$servername = "localhost";
$username = "root";
$password = ""; // Replace with your password
$dbname = "fitzone"; // Replace with your database name

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: staff_dashboard.php");
    exit();
}

// Fetch user role from the database
$user_id = $_SESSION['user_id'];
$sql = "SELECT role FROM users WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    // If no user is found, redirect to login
    header("Location: login.php");
    exit();
}

$row = $result->fetch_assoc();
$user_role = $row['role'];

// Check if the user is a staff member
if ($user_role !== 'staff') {
    echo "Access denied: You do not have permission to view this page.";
    exit();
}

// Proceed with the staff dashboard
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #121212;
            color: #ffffff;
            font-family: 'Arial', sans-serif;
        }

        .navbar {
            background-color: #1f1f1f;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.5);
        }

        .navbar-brand {
            color: #ffffff !important;
            font-weight: bold;
        }

        .nav-link {
            color: #b0b0b0 !important;
            transition: color 0.3s;
        }

        .nav-link:hover {
            color: #00d4ff !important;
        }

        .logout-btn {
            background-color: #d9534f;
            color: #ffffff;
            border: none;
            padding: 5px 10px;
            border-radius: 5px;
            transition: background-color 0.3s;
        }

        .logout-btn:hover {
            background-color: #c9302c;
        }

        .container {
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <!-- Navigation Bar -->
    <nav class="navbar navbar-expand-lg">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Staff Dashboard</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item"><a class="nav-link" href="staff_dashboard.php?page=appointment_management">Appointment Management</a></li>
                    <li class="nav-item"><a class="nav-link" href="staff_dashboard.php?page=classes_management">Classes Management</a></li>
                    <li class="nav-item"><a class="nav-link" href="staff_dashboard.php?page=trainers_management">Trainers Management</a></li>
                    <li class="nav-item"><a class="nav-link" href="staff_dashboard.php?page=memberships">Memberships</a></li>
                    <li class="nav-item"><a class="nav-link" href="staff_dashboard.php?page=user_queries">User Queries</a></li>
                </ul>
                 <!-- logout_button.php -->
                <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                <a class="nav-link" href="logout.php" title="Logout">
            <i class="fas fa-sign-out-alt"></i> Logout
        </a>
    </li>
</ul>

            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container">
        <?php
        $page = isset($_GET['page']) ? $_GET['page'] : 'appointment_management';
        switch ($page) {
            case 'appointment_management':
                include 'appointment_management.php';
                break;
            case 'classes_management':
                include 'classes_management.php';
                break;
            case 'trainers_management':
                include 'trainers_management.php';
                break;
            case 'memberships':
                include 'membership_management.php';
                break;
            case 'user_queries':
                include 'user_queries.php';
                break;
            default:
                echo "<h1>Welcome to the Staff Dashboard!</h1>";
                break;
        }
        ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
