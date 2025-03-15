<?php
// Database connection
$conn = new mysqli("localhost", "root", "", "fitzone");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submission for adding a new trainer
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Gather trainer data from form
    $name = $_POST["name"];
    $proficiency = $_POST["proficiency"];
    $experience_years = $_POST["experience_years"];
    $email = $_POST["email"];
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

    // Check if email already exists
    $stmt = $conn->prepare("SELECT trainer_id FROM trainers WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        echo "<script>alert('Error: This email is already in use.'); window.history.back();</script>";
        exit();
    }
    $stmt->close();

    // Insert new trainer into the database
    $stmt = $conn->prepare("INSERT INTO trainers (name, proficiency, experience_years, email, image) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("ssiss", $name, $proficiency, $experience_years, $email, $image_path);

    if ($stmt->execute()) {
        $trainer_id = $stmt->insert_id;
        // Return success response
        $response = [
            'success' => true,
            'trainer' => [
                'trainer_id' => $trainer_id,
                'name' => $name,
                'proficiency' => $proficiency,
                'experience_years' => $experience_years,
                'email' => $email,
                'image' => $image_path
            ]
        ];
        echo json_encode($response);
    } else {
        // Return failure response
        echo json_encode(['success' => false, 'message' => 'An error occurred while saving the trainer.']);
    }

    $stmt->close();
    exit();
}
?>
