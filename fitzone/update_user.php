<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "fitzone";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = intval($_POST['user_id']);
    $name = $_POST['name'];
    $email = $_POST['email'];
    $role = $_POST['role'];

    $sql = "UPDATE users SET name='$name', email='$email', role='$role' WHERE user_id=$user_id";

    if ($conn->query($sql) === TRUE) {
        echo "User updated successfully!";
    } else {
        echo "Error: " . $conn->error;
    }
} else {
    echo "Invalid request method.";
}

$conn->close();
?>
