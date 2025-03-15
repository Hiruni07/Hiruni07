<?php
$conn = new mysqli("localhost", "root", "", "fitzone");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $trainer_id = $_POST["trainer_id"];
    $name = $_POST["name"];
    $proficiency = $_POST["proficiency"];
    $experience_years = $_POST["experience_years"];
    $email = $_POST["email"];
    $image_path = "";

    // Handle file upload if a new image is uploaded
    if (isset($_FILES["image"]) && $_FILES["image"]["error"] == 0) {
        $upload_dir = "uploads/trainers/";
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        $image_path = $upload_dir . basename($_FILES["image"]["name"]);
        move_uploaded_file($_FILES["image"]["tmp_name"], $image_path);
    }

    // If no new image is uploaded, keep the current image
    if (!$image_path) {
        $result = $conn->query("SELECT image FROM trainers WHERE trainer_id = $trainer_id");
        if ($result && $row = $result->fetch_assoc()) {
            $image_path = $row["image"];
        }
    }

    // Update trainer information in the database
    $stmt = $conn->prepare("UPDATE trainers SET name = ?, proficiency = ?, experience_years = ?, email = ?, image = ? WHERE trainer_id = ?");
    $stmt->bind_param("ssissi", $name, $proficiency, $experience_years, $email, $image_path, $trainer_id);

    if ($stmt->execute()) {
        echo json_encode([
            "success" => true,
            "trainer" => [
                "trainer_id" => $trainer_id,
                "name" => $name,
                "proficiency" => $proficiency,
                "experience_years" => $experience_years,
                "email" => $email,
                "image" => $image_path
            ]
        ]);
    } else {
        echo json_encode(["success" => false, "message" => "Error updating trainer."]);
    }

    $stmt->close();
    $conn->close();
}
?>
