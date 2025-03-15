<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "fitzone";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_GET['user_id'])) {
    $id = intval($_GET['user_id']);

    // Check if the user exists before trying to delete
    $checkSql = "SELECT * FROM users WHERE user_id = $id";
    $checkResult = $conn->query($checkSql);

    if ($checkResult->num_rows > 0) {
        // If the user exists, proceed to delete
        $sql = "DELETE FROM users WHERE user_id = $id";

        if ($conn->query($sql) === TRUE) {
            echo "User deleted successfully!";
        } else {
            echo "Error: " . $conn->error; // Detailed error message
        }
    } else {
        echo "User does not exist."; // Inform if the user doesn't exist
    }
} else {
    echo "Invalid request."; // If 'user_id' is not provided
}

$conn->close();
?>
