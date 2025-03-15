<?php
// Database connection
$conn = new mysqli("localhost", "root", "", "fitzone");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the ID from the URL
if (isset($_GET['id'])) {
    $membership_id = intval($_GET['id']);

    // Delete query
    $query = "DELETE FROM memberships WHERE membership_id = $membership_id";

    if ($conn->query($query)) {
        echo "<script>alert('Membership package deleted successfully!'); window.location.href = 'membership_packages.php';</script>";
    } else {
        echo "<script>alert('Error: " . $conn->error . "'); window.location.href = 'membership_packages.php';</script>";
    }
} else {
    echo "<script>alert('Invalid request.'); window.location.href = 'membership_packages.php';</script>";
}

$conn->close();
?>
