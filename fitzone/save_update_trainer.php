<?php
// Database connection
$conn = new mysqli("localhost", "root", "", "fitzone");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submission for adding or editing a trainer
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST["name"];
    $proficiency = $_POST["proficiency"];
    $experience_years = $_POST["experience_years"];
    $email = $_POST["email"];
    $trainer_id = isset($_POST["trainer_id"]) ? $_POST["trainer_id"] : null;
    $image_path = "";

    // Handle file upload
    if (isset($_FILES["image"]) && $_FILES["image"]["error"] == 0) {
        $upload_dir = "uploads/trainers/";
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        $image_path = $upload_dir . basename($_FILES["image"]["name"]);
        move_uploaded_file($_FILES["image"]["tmp_name"], $image_path);
    }

    // If editing, preserve old image if a new one isn't uploaded
    if (!$image_path && $trainer_id) {
        $result = $conn->query("SELECT image FROM trainers WHERE trainer_id = $trainer_id");
        if ($result && $row = $result->fetch_assoc()) {
            $image_path = $row["image"];
        }
    }

    // Check if email already exists
    if ($trainer_id) {
        $stmt = $conn->prepare("SELECT trainer_id FROM trainers WHERE email = ? AND trainer_id != ?");
        $stmt->bind_param("si", $email, $trainer_id);
    } else {
        $stmt = $conn->prepare("SELECT trainer_id FROM trainers WHERE email = ?");
        $stmt->bind_param("s", $email);
    }

    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        echo "<script>alert('Error: This email is already in use.'); window.history.back();</script>";
        exit();
    }
    $stmt->close();

    if ($trainer_id) {
        // Edit trainer
        $stmt = $conn->prepare("UPDATE trainers SET name=?, proficiency=?, experience_years=?, email=?, image=? WHERE trainer_id=?");
        $stmt->bind_param("ssissi", $name, $proficiency, $experience_years, $email, $image_path, $trainer_id);
    } else {
        // Add new trainer
        $stmt = $conn->prepare("INSERT INTO trainers (name, proficiency, experience_years, email, image) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("ssiss", $name, $proficiency, $experience_years, $email, $image_path);
    }

    $stmt->execute();
    $stmt->close();
    header("Location: trainers_management.php?message=Trainer saved successfully");
    exit();
}

// Handle deletion
if (isset($_GET["delete_id"])) {
    $delete_id = $_GET["delete_id"];
    $stmt = $conn->prepare("DELETE FROM trainers WHERE trainer_id = ?");
    $stmt->bind_param("i", $delete_id);
    if ($stmt->execute()) {
        $delete_message = "Trainer deleted successfully!";
    } else {
        $delete_message = "Error deleting trainer.";
    }
    $stmt->close();
    header("Location: trainers_management.php?message=" . urlencode($delete_message));
    exit();
}
?>
