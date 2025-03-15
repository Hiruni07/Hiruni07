<?php
// Enable error reporting for debugging
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// Database connection details
$servername = "localhost";
$username = "root"; // Replace with your MySQL username
$password = ""; // Replace with your MySQL password
$dbname = "fitzone"; // Replace with your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} else {
    //echo "Database connected successfully!<br>";
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and validate input
    $name = trim($_POST['name']); // Ensure this matches the form field name
    $address = trim($_POST['address']);
    $age = (int)trim($_POST['age']);
    $gender = trim($_POST['gender']);
    $phone = trim($_POST['phone']);
    $email = trim($_POST['email']);
    $password = password_hash(trim($_POST['password']), PASSWORD_BCRYPT); // Secure password storage
    $package = trim($_POST['package']);
    $status = 'pending'; // Default status

    try {
        // Prepare and execute the query
        $sql = "INSERT INTO membership_registrations 
                (full_name, address, age, gender, phone, email, password, package, status) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssissssss", $name, $address, $age, $gender, $phone, $email, $password, $package, $status);

        if ($stmt->execute()) {
            // JavaScript for success message
            echo "<script>
                    alert('Registration successful!');
                    // You can add any other code here to update content on the same page without redirecting.
                  </script>";
        } else {
            // JavaScript for failure message
            echo "<script>
                    alert('Error: Unable to process your registration.');
                  </script>";
        }
        $stmt->close();
    } catch (Exception $e) {
        // JavaScript for exception handling
        echo "<script>
                alert('An error occurred: " . addslashes($e->getMessage()) . "');
              </script>";
    }
}

// Close the database connection
$conn->close();
?>
