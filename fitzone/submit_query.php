<?php
session_start();
header('Content-Type: application/json');

try {
    // Simulate error for testing
    // throw new Exception("Simulated error");

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode(["error" => "Invalid request method."]);
        exit();
    }

    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "fitzone";

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        echo json_encode(["error" => "Database connection failed."]);
        exit();
    }

    $subject = $_POST['subject'] ?? '';
    $message = $_POST['message'] ?? '';
    $user_id = $_SESSION['user_id'] ?? null;

    if (!$user_id) {
        echo json_encode(["error" => "User not logged in."]);
        exit();
    }

    $stmt = $conn->prepare("INSERT INTO query (user_id, subject, message, status) VALUES (?, ?, ?, 'Pending')");
    $stmt->bind_param("iss", $user_id, $subject, $message);

    if ($stmt->execute()) {
        echo json_encode(["success" => "Query submitted successfully!"]);
    } else {
        echo json_encode(["error" => "Failed to save query."]);
    }

    $stmt->close();
    $conn->close();
} catch (Exception $e) {
    echo json_encode(["error" => "Unexpected error: " . $e->getMessage()]);
}
?>
