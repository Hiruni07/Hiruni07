<?php
// Database connection
$conn = new mysqli("localhost", "root", "", "fitzone");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $delete_id = $_POST["delete_id"] ?? null;

    if ($delete_id) {
        $stmt = $conn->prepare("DELETE FROM trainers WHERE trainer_id = ?");
        $stmt->bind_param("i", $delete_id);

        if ($stmt->execute()) {
            echo json_encode(["success" => true, "message" => "Trainer deleted successfully."]);
        } else {
            echo json_encode(["success" => false, "message" => "Failed to delete trainer."]);
        }

        $stmt->close();
    } else {
        echo json_encode(["success" => false, "message" => "Invalid trainer ID."]);
    }
}

$conn->close();
?>
