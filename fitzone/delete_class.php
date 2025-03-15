<?php
// Database connection settings
$host = "localhost";
$username = "root";
$password = "";
$database = "fitzone";

// Connect to database
$conn = new mysqli($host, $username, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$response = array('success' => false);

if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    $delete_query = "DELETE FROM classes WHERE class_id = ?";
    $stmt = $conn->prepare($delete_query);
    $stmt->bind_param("i", $delete_id);
    
    if ($stmt->execute()) {
        $response['success'] = true;
    }
    $stmt->close();
}

echo json_encode($response);
$conn->close();
?>
