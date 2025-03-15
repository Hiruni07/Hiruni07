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

    // Fetch the membership details
    $query = "SELECT * FROM memberships WHERE membership_id = $membership_id";
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        $membership = $result->fetch_assoc();
    } else {
        echo "<script>alert('Membership package not found.'); window.location.href = 'membership_packages.php';</script>";
        exit();
    }
} else {
    echo "<script>alert('Invalid request.'); window.location.href = 'membership_packages.php';</script>";
    exit();
}

// Update logic if the form is submitted
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = $conn->real_escape_string($_POST['name']);
    $price = $conn->real_escape_string($_POST['price']);
    $duration = $conn->real_escape_string($_POST['duration']);
    $benefits = $conn->real_escape_string($_POST['benefits']);
    $promotions = $conn->real_escape_string($_POST['promotions']);

    // Update query
    $query = "UPDATE memberships 
              SET name = '$name', price = '$price', duration = '$duration', 
                  benefits = '$benefits', special_promotions = '$promotions' 
              WHERE membership_id = $membership_id";

    if ($conn->query($query)) {
        echo "<script>alert('Membership package updated successfully!'); window.location.href = 'membership_packages.php';</script>";
    } else {
        echo "<script>alert('Error: " . $conn->error . "');</script>";
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Membership Package</title>
</head>
<body>
    <h1>Edit Membership Package</h1>
    <form method="POST">
        <input type="text" name="name" value="<?php echo htmlspecialchars($membership['name']); ?>" required>
        <input type="number" step="0.01" name="price" value="<?php echo htmlspecialchars($membership['price']); ?>" required>
        <input type="number" name="duration" value="<?php echo htmlspecialchars($membership['duration']); ?>" required>
        <textarea name="benefits" required><?php echo htmlspecialchars($membership['benefits']); ?></textarea>
        <textarea name="promotions" required><?php echo htmlspecialchars($membership['special_promotions']); ?></textarea>
        <button type="submit">Update Membership Package</button>
    </form>
    <a href="membership_packages.php">Back to List</a>
</body>
</html>
