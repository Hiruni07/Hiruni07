<?php
// Start the session at the very beginning
session_start();

// Database configuration
$host = "localhost";
$user = "root";
$password = ""; // Default password for XAMPP
$dbname = "fitzone";

// Create database connection
$conn = new mysqli($host, $user, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$error_message = ""; // Initialize error message variable
$success_message = ""; // Initialize success message variable

// Handle POST requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Determine if the request is for login or registration
    if (isset($_POST['login'])) {
        // **Login Process**

        // Retrieve and sanitize input data
        $email = trim($_POST['email']);
        $password = trim($_POST['password']);

        // Basic validation
        if (empty($email) || empty($password)) {
            $error_message = "Please fill in all required fields.";
        } else {
            // Prepare and execute login query
            $stmt = $conn->prepare("SELECT user_id, email, password, name, role FROM users WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows > 0) {
                $stmt->bind_result($db_id, $db_email, $db_password, $db_name, $db_role);
                $stmt->fetch();

                // Verify the password
                if (password_verify($password, $db_password)) {
                    // Set session variables
                    $_SESSION['user_id'] = $db_id;
                    $_SESSION['user_email'] = $db_email;
                    $_SESSION['user_name'] = $db_name;
                    $_SESSION['role'] = $db_role;

                    // Redirect based on role
                    switch ($db_role) {
                        case 'admin':
                            header("Location: admin_dashboard.php");
                            break;
                        case 'staff':
                            header("Location: staff_dashboard.php");
                            break;
                        case 'customer':
                        default:
                            header("Location: customer_dashboard.php");
                            break;
                    }
                    exit();
                } else {
                    $error_message = "Invalid email or password.";
                }
            } else {
                $error_message = "Invalid email or password.";
            }

            $stmt->close();
        }
    } elseif (isset($_POST['register'])) {
        // **Registration Process**

        // Retrieve and sanitize input data
        $name = trim($_POST['name']);
        $email = trim($_POST['email']);
        $password = trim($_POST['password']);

        // Basic validation
        if (empty($name) || empty($email) || empty($password)) {
            $error_message = "Please fill in all required fields.";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error_message = "Please enter a valid email address.";
        } else {
            // Check if the email already exists
            $stmt = $conn->prepare("SELECT user_id FROM users WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows > 0) {
                $error_message = "An account with this email already exists.";
            } else {
                // Hash the password
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                // Set role to 'customer'
                $role = 'customer';

                // Insert the new user into the database
                $stmt_insert = $conn->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
                $stmt_insert->bind_param("ssss", $name, $email, $hashed_password, $role);

                if ($stmt_insert->execute()) {
                    $success_message = "Registration successful! You can now log in.";
                    
                    // Optionally, automatically log the user in after registration
                    /*
                    $_SESSION['user_id'] = $stmt_insert->insert_id;
                    $_SESSION['user_email'] = $email;
                    $_SESSION['user_name'] = $name;
                    $_SESSION['role'] = $role;
                    header("Location: customer_dashboard.php");
                    exit();
                    */
                } else {
                    $error_message = "Registration failed. Please try again later.";
                }

                $stmt_insert->close();
            }

            $stmt->close();
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <!-- (Existing head content remains unchanged) -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FitZone - Login/Register</title>
    <!-- Add Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Add FontAwesome for icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">

    <style>
        /* (Existing styles remain unchanged) */
        body {
            background-image: url('https://wallpaperset.com/w/full/6/d/6/45480.jpg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            height: 100vh;
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Roboto', sans-serif;
        }

        .form-container {
            background: rgba(255, 255, 255, 0.9);
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.3);
            max-width: 400px;
            width: 100%;
            transition: all 0.5s ease;
        }

        .form-title {
            color: #333;
            font-weight: bold;
            font-size: 1.8rem;
            text-align: center;
            margin-bottom: 20px;
        }

        .custom-input {
            border-radius: 5px;
            padding: 10px;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
        }

        .custom-input:focus {
            border-color:rgb(255, 106, 0);
            box-shadow: 0 0 8px rgba(255, 140, 0, 0.8);
            outline: none;
        }

        .btn-primary, .btn-success {
            background-color: #FFA500; /* Orange-like yellow color */
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: 5px;
            font-weight: bold;
            font-size: 1rem;
            width: 100%;
            transition: background-color 0.3s ease, transform 0.2s ease;
        }

        .btn-primary:hover, .btn-success:hover {
            background-color:rgb(255, 123, 0); /* Slightly lighter yellow on hover */
            transform: translateY(-2px);
        }

        .link-text {
            color: #007bff;
            font-weight: bold;
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .link-text:hover {
            color: #0056b3;
        }

        #register-form {
            display: none;
        }
        
        .alert {
            margin-bottom: 15px;
        }
        
    </style>
    <script>
        // JavaScript to toggle forms with animations
        function showRegisterForm() {
            document.getElementById('login-form').style.display = 'none';
            document.getElementById('register-form').style.display = 'block';
        }

        function showLoginForm() {
            document.getElementById('register-form').style.display = 'none';
            document.getElementById('login-form').style.display = 'block';
        }

        // Optionally, show the appropriate form based on server messages
        window.onload = function() {
            <?php if (isset($_POST['register']) && $error_message != "") { ?>
                showRegisterForm();
            <?php } ?>
        };
    </script>
</head>
<body>
<div class="form-container">
    <!-- Display Success or Error Messages -->
    <?php if (!empty($error_message)): ?>
        <div class="alert alert-danger" role="alert">
            <?php echo htmlspecialchars($error_message); ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($success_message)): ?>
        <div class="alert alert-success" role="alert">
            <?php echo htmlspecialchars($success_message); ?>
        </div>
    <?php endif; ?>

    <!-- Login Form -->
    <div id="login-form">
        <h1 class="form-title">Login</h1>
        <form method="POST" action="">
            <div class="mb-3">
                <label for="login-email" class="form-label">Email</label>
                <input type="email" id="login-email" name="email" class="form-control custom-input" required>
            </div>
            <div class="mb-3">
                <label for="login-password" class="form-label">Password</label>
                <input type="password" id="login-password" name="password" class="form-control custom-input" required>
            </div>
            <input type="hidden" name="login" value="1">
            <div class="d-grid">
                <button type="submit" class="btn btn-primary">Login</button>
            </div>
        </form>
        <div class="text-center mt-3">
            <p>Don't have an account? <a href="javascript:void(0);" class="link-text" onclick="showRegisterForm()">Register here</a>.</p>
        </div>
    </div>

    <!-- Register Form -->
    <div id="register-form">
        <h1 class="form-title">Register</h1>
        <form method="POST" action="">
            <div class="mb-3">
                <label for="register-name" class="form-label">Name</label>
                <input type="text" id="register-name" name="name" class="form-control custom-input" required>
            </div>
            <div class="mb-3">
                <label for="register-email" class="form-label">Email</label>
                <input type="email" id="register-email" name="email" class="form-control custom-input" required>
            </div>
            <div class="mb-3">
                <label for="register-password" class="form-label">Password</label>
                <input type="password" id="register-password" name="password" class="form-control custom-input" required>
            </div>
            <input type="hidden" name="register" value="1">
            <div class="d-grid">
                <button type="submit" class="btn btn-success">Register</button>
            </div>
        </form>
        <div class="text-center mt-3">
            <p>Already have an account? <a href="javascript:void(0);" class="link-text" onclick="showLoginForm()">Login here</a>.</p>
        </div>
    </div>
</div>

<!-- Add Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

