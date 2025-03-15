<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Database connection details
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "fitzone";

// Create database connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Start session to get the logged-in user's ID
session_start();
if (!isset($_SESSION['user_id'])) {
    die("User not logged in. Please log in to view your dashboard.");
}

$user_id = $_SESSION['user_id']; // Assuming user_id is stored in session during login

// Fetch trainers from the database
$trainers_query = "SELECT trainer_id, name, proficiency, image FROM trainers";
$trainers_result = $conn->query($trainers_query);

// Check if query execution for trainers was successful
if (!$trainers_result) {
    die("Error fetching trainers: " . $conn->error);
}

// Fetch trainers into an array
$trainers = [];
if ($trainers_result->num_rows > 0) {
    while ($row = $trainers_result->fetch_assoc()) {
        $trainers[] = $row;
    }
} else {
    $trainers = []; // Empty array if no trainers found
}

// Fetch membership packages from the database
$memberships_query = "SELECT * FROM memberships";
$memberships_result = $conn->query($memberships_query);
$memberships = [];
if ($memberships_result && $memberships_result->num_rows > 0) {
    while ($row = $memberships_result->fetch_assoc()) {
        $memberships[] = $row;
    }
}

// Fetch classes from the database
$classes_query = "SELECT class_id, name AS class_name, description, type FROM classes";
$classes_result = $conn->query($classes_query);
$classes = [];
if ($classes_result && $classes_result->num_rows > 0) {
    while ($row = $classes_result->fetch_assoc()) {
        $classes[] = $row;
    }
}

// Fetch logged-in user's submitted queries from the database
// Ensure the database connection is established
if (!$conn) {
    die("Database connection failed: " . $conn->connect_error);
}

// Table name for queries
$query_table = "query";

// Fetch logged-in user's submitted queries
$sql = "SELECT id, subject, message, status, created_at FROM $query_table WHERE user_id = ? ORDER BY created_at DESC";
$stmt = $conn->prepare($sql);

if ($stmt === false) {
    die("SQL error: " . $conn->error);
}

// Bind the logged-in user's ID
$stmt->bind_param("i", $user_id);

// Execute the query
if (!$stmt->execute()) {
    die("Execution error: " . $stmt->error);
}

// Get the result
$result = $stmt->get_result();

$queries = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $queries[] = $row;
    }
}




// Close the database connection
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FitZone Fitness Center</title>
    
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- FontAwesome for Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">
    <!-- jQuery and Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <!-- Custom CSS -->
    <style>
        /* General Styling */
        body {
            font-family: Arial, sans-serif;
            background-color: #1f1f1f; /* Dark Background */
            color: #ffffff; /* White Text */
        }

        h2 {
            font-size: 2rem;
            font-weight: bold;
            text-align: center;
        }

        /* Navbar Styling */
        .navbar {
            background-color: #2a2a2a !important;
        }

        .navbar-brand, .nav-link {
            color: #ffffff !important;
            font-weight: bold;
        }

        .nav-link:hover {
            color: #ffa500 !important; /* Highlight */
        }

        section {
           scroll-margin-top: 80px; /* Adjust based on your fixed navbar height */
        }

        /* Section Titles */
        h2, h4, h5 {
            color: #ffa500; /* Accent Color */
        }

        /* Buttons */
        .btn-primary {
            background-color: #ffa500;
            border-color: #ffa500;
            font-weight: bold;
        }

        .btn-primary:hover {
            background-color: #e69500;
            border-color: #e69500;
        }
        
    
 
/* Membership Section Styling */
#membership {
    background-color: #1a1a1a;
    padding: 50px 20px;
}

.card {
    border-radius: 8px;
    box-shadow: 0 8px 15px rgba(0, 0, 0, 0.3);
    transition: transform 0.2s ease;
}

.card:hover {
    transform: scale(1.05);
}

form {
    max-width: 600px;
    margin: auto;
}

.btn-primary {
    background-color:rgb(255, 187, 51);
    border: none;
}

.btn-primary:hover {
    background-color:rgb(196, 162, 41);
}

/* Container for the appointment form */
.appointment-container {
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh; /* Full-screen height */
    background-color: #1f1f1f; /* Dark background */
    padding: 20px;
}

/* Appointment card styling */
.appointment-card {
    background-color: #2a2a2a; /* Slightly lighter than the background */
    border-radius: 15px;
    padding: 30px 40px;
    box-shadow: 0px 50px 50px rgba(0, 0, 0, 0.7); /* Soft shadow */
    max-width: 900px;
    width: 100%;
    text-align: center;
}



/* Heading styling */
.appointment-card h2 {
    color: #ffa500; /* Highlight color */
    margin-bottom: 20px;
    font-size: 1.8rem;
    text-shadow: 1px 1px 4px rgba(0, 0, 0, 0.5);
}

/* Form group styling */
.form-group {
    margin-bottom: 20px;
    text-align: left;
}

.form-group label {
    display: block;
    font-size: 1rem;
    color: #fff;
    margin-bottom: 8px;
    font-weight: bold;
}
/* Appointment Container Styling */
.appointment-container {
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh; /* Full-screen height */
    background-color: #1f1f1f; /* Dark background */
    padding: 20px;
}

/* Container for the appointment form */
.appointment-container {
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh; /* Full-screen height */
    background-color: #1f1f1f; /* Dark background */
    padding: 20px;
}

/* Appointment Card Styling */
.appointment-card {
    background-color: #2a2a2a; /* Slightly lighter than the background */
    border-radius: 15px;
    padding: 30px 40px;
    box-shadow: 0px 50px 50px rgba(0, 0, 0, 0.7); /* Soft shadow */
    max-width: 900px;
    width: 100%;
    text-align: center;
}

/* Heading Styling */
.appointment-card h2 {
    color: #ffa500; /* Highlight color */
    margin-bottom: 10px;
    font-size: 1.8rem;
    text-shadow: 1px 1px 4px rgba(0, 0, 0, 0.5);
    text-align: center; /* Horizontal centering */
    
}




/* Form Group Styling */
.form-group {
    margin-bottom: 20px;
    text-align: left;
}

.form-group label {
    display: block;
    font-size: 1rem;
    color: #fff;
    margin-bottom: 8px;
    font-weight: bold;
}

/* Input Field Styling */
.form-control {
    width: 100%;
    padding: 10px 15px;
    font-size: 1rem;
    border-radius: 8px;
    border: 1px solid #444;
    background-color: #1a1a1a; /* Dark input background */
    color: #fff; /* White text */
    box-shadow: inset 0px 2px 4px rgba(0, 0, 0, 0.6); /* Inner shadow */
    transition: all 0.3s ease;
}

.form-control::placeholder {
    color: #bbb; /* Light placeholder text */
}

.form-control:focus {
    outline: none;
    border-color: #ffa500; /* Accent border on focus */
    box-shadow: 0 0 10px rgba(255, 165, 0, 0.6); /* Glow effect */
}

/* Button Styling */
.btn-primary {
    width: 100%;
    padding: 10px 20px;
    font-size: 1rem;
    font-weight: bold;
    color: #fff;
    background-color: #ffa500; /* Highlight button */
    border: none;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0px 5px 15px rgba(255, 165, 0, 0.4); /* Button shadow */
}

.btn-primary:hover {
    background-color: #e69500; /* Slightly darker on hover */
    box-shadow: 0px 8px 20px rgba(255, 165, 0, 0.6); /* Intensify shadow */
    transform: translateY(-3px); /* Subtle lift on hover */
}





/* Query Section Container */
#query-section {
    background-color: #1a1a1a; /* Dark background to blend with the website */
    padding: 50px 20px;
    border-radius: 10px;
    box-shadow: 0px 8px 15px rgba(0, 0, 0, 0.4); /* Add depth */
    max-width: 600px;
    margin: 50px auto; /* Center align */
}

/* Section Title */
#query-section h2 {
    color: #ffa500; /* Accent color for the title */
    text-align: center;
    font-size: 2rem;
    font-weight: bold;
    text-transform: uppercase;
    margin-bottom: 20px;
    text-shadow: 2px 2px 5px rgba(0, 0, 0, 0.7); /* Add shadow for emphasis */
}

/* Form Inputs */
#query-section .form-control {
    background-color: #2a2a2a; /* Dark input field */
    color: #fff; /* White text */
    border: 1px solid #ffa500; /* Accent color border */
    border-radius: 8px;
    padding: 10px;
    font-size: 1rem;
    transition: all 0.3s ease; /* Smooth focus animation */
}

#query-section .form-control:focus {
    background-color: #1a1a1a; /* Darker on focus */
    border-color: #e69500; /* Highlight border */
    box-shadow: 0 0 8px rgba(255, 165, 0, 0.8); /* Glow effect */
}

/* Submit Button */
#query-section .btn-primary {
    background-color: #ffa500;
    border: none;
    font-weight: bold;
    color: #111;
    padding: 12px;
    border-radius: 8px;
    text-transform: uppercase;
    font-size: 1rem;
    transition: all 0.3s ease; /* Smooth hover effect */
}


#query-section .btn-primary:hover {
    background-color: #e69500;
    box-shadow: 0px 5px 15px rgba(255, 165, 0, 0.6); /* Glow on hover */
    transform: translateY(-3px); /* Subtle hover lift */
}

/* Card Styling */
#query-section .card {
    background-color: #2a2a2a; /* Matches the theme */
    border: none;
    color: #fff;
    border-radius: 10px;
    box-shadow: 0px 8px 15px rgba(0, 0, 0, 0.3); /* Depth */
}

/* Form Labels */
#query-section .form-label {
    font-weight: bold;
    color: #ffa500;
}

/* Responsive Design */
@media (max-width: 768px) {
    #query-section {
        padding: 20px;
    }
    #query-section h2 {
        font-size: 1.5rem;
    }
    #query-section .btn-primary {
        font-size: 0.9rem;
    }
}


        /* Footer Styling */
        .footer {
            background-color: #2a2a2a;
            color: #ffffff;
        }

        .footer a {
            color: #ffa500;
        }

        .footer a:hover {
            color: #e69500;
        }

        /* Card Styling */
        .card {
            background-color: #2a2a2a;
            border: none;
            color: #ffffff;
            border-radius: 10px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.5);
            text-align: center;
        }

        .card h4 {
            color: #ffa500;
        }

        /* Trainers Images */
        .trainer-image {
            border: 3px solid #ffa500;
            padding: 5px;
            border-radius: 50%;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.5);
        }

        /* Change the color of the logout button when it's clicked (active) */
.navbar .navbar-brand:active, .navbar .navbar-brand:focus {
  background-color: #DAA520;  /* Dark Yellow */
  color: white;  /* Text color for better contrast */
  border-radius: 5px;  /* Optional: Adds rounded corners */
  padding: 5px 10px;  /* Optional: Adds padding for better appearance */
}

/* Optional: Change color when hover */
.navbar .navbar-brand:hover {
  background-color: #B8860B; /* Slightly darker yellow */
  color: white;
}
    </style>
</head>
<body>
  
   <!-- Navigation Bar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container d-flex justify-content-center">
        <a class="navbar-brand" href="#">FitZone Fitness Center</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav mx-auto">
                <li class="nav-item"><a class="nav-link" href="#trainers">Trainers</a></li>
                <li class="nav-item"><a class="nav-link" href="#membership_packages">Membership Packages</a></li>
                <li class="nav-item"><a class="nav-link" href="#appointments">Appointments</a></li>
                <li class="nav-item"><a class="nav-link" href="#query">Query</a></li>
            </ul>
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <!-- Logout Button (Left Top) -->
    <a class="navbar-brand" href="logout.php">
      <i class="fas fa-sign-out-alt"></i> Logout
    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<?php
    // Check if a success message exists in the session
    if (isset($_SESSION['query_success'])) {
        echo '<div class="alert alert-success" role="alert">' . $_SESSION['query_success'] . '</div>';
        unset($_SESSION['query_success']); // Clear the success message after displaying
    }

    // Check if an error message exists in the session
    if (isset($_SESSION['query_error'])) {
        echo '<div class="alert alert-danger" role="alert">' . $_SESSION['query_error'] . '</div>';
        unset($_SESSION['query_error']); // Clear the error message after displaying
    }
    ?>

    
  <!-- Trainers Section -->
<section id="trainers" class="container my-5">
    <h2 class="text-center">Meet Our Trainers</h2>
    <div class="row text-center">
        <?php if (!empty($trainers)): ?>
            <?php foreach ($trainers as $trainer): ?>
                <div class="col-md-3 mb-4">
                    <!-- Display trainer image -->
                    <img 
                        src="<?= htmlspecialchars($trainer['image'] ?: 'uploads/trainers/default.jpg') ?>" 
                        class="img-fluid trainer-image" 
                        alt="<?= htmlspecialchars($trainer['name']) ?>"
                    >
                    <!-- Display trainer name and proficiency -->
                    <h5 class="mt-2"><?= htmlspecialchars($trainer['name']) ?></h5>
                    <p><?= htmlspecialchars($trainer['proficiency']) ?></p>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="text-center">No trainers available at the moment.</p>
        <?php endif; ?>
    </div>
</section>

    <!-- Membership Section -->
<section id="membership" class="container my-5">
    <h2 class="text-center text-white mb-4">Membership Packages</h2>
    
    <!-- Membership Packages -->
    <div class="row text-center">
        <?php
        // Loop through memberships and display them
        foreach ($memberships as $membership) {
            echo "
            <div class='col-md-4'>
                <div class='card bg-dark text-white p-3'>
                    <h4>" . htmlspecialchars($membership['name']) . "</h4>
                    <p>" . htmlspecialchars($membership['benefits']) . "</p>
                    <p><strong>Rs. " . number_format($membership['price'], 2) . "/month</strong></p>
                </div>
            </div>
            ";
        }
        ?>
    </div>


    <!-- Membership Registration Form -->
    <div class="mt-5">
        <h3 class="text-center text-white mb-4">Register for Membership</h3>
        <form method="POST" action="register.php" id="membershipForm" class="p-4 bg-dark text-white rounded">
        <div class="form-group">
        <label for="name">Full Name</label>
        <input type="text" name="name" id="name" class="form-control" required>
    </div>
    <div class="form-group">
        <label for="address">Address</label>
        <input type="text" name="address" id="address" class="form-control" required>
    </div>
    <div class="form-group">
        <label for="age">Age</label>
        <input type="number" name="age" id="age" class="form-control" required>
    </div>

            <div class="form-group">
            <label for="gender">Gender</label>
            <select name="gender" id="gender" class="form-control" required>
                <option value="" disabled selected>Select your gender</option>
                <option value="male">Male</option>
                <option value="female">Female</option>
                <option value="other">Other</option>
            </select>
            </div>

            <div class="form-group">
                <label for="phone">Phone Number</label>
                <input type="tel" name="phone" id="phone" class="form-control" placeholder="Enter your phone number" required>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" name="email" id="email" class="form-control" placeholder="Enter your email" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" name="password" id="password" class="form-control" placeholder="Create a password" required>
            </div>
            <div class="form-group">
                <label for="package">Choose Package</label>
                <select name="package" id="package" class="form-control">
                    <?php
                    // Dynamically populate the package options
                    foreach ($memberships as $membership) {
                        echo "<option value='" . htmlspecialchars($membership['name']) . "'> 
                                  " . htmlspecialchars($membership['name']) . " - Rs. " . number_format($membership['price'], 2) . "/month 
                              </option>";
                    }
                    ?>
                </select>
            </div>
            <button type="submit" class="btn btn-primary btn-block">Register</button>
        </form>
    </div>
</section>

<!-- Appointment Form -->

<form id="appointmentForm">
<h2>Book an Appointment</h2>
    <!-- Select Class -->
    <div class="form-group">
        <label for="class">Select Class</label>
        <select id="class" name="class_id" class="form-control" required>
            <option value="" disabled selected>Select a Class</option>
            <?php foreach ($classes as $class): ?>
                <option value="<?= $class['class_id'] ?>"><?= htmlspecialchars($class['class_name']) ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <!-- Select Trainer -->
    <div class="form-group">
        <label for="trainer">Select Trainer</label>
        <select id="trainer" name="trainer_id" class="form-control" required>
            <option value="" disabled selected>Select a Trainer</option>
            <?php if (!empty($trainers)): ?>
                <?php foreach ($trainers as $trainer): ?>
                    <option value="<?= htmlspecialchars($trainer['trainer_id']) ?>">
                        <?= htmlspecialchars($trainer['name']) ?>
                    </option>
                <?php endforeach; ?>
            <?php else: ?>
                <option value="" disabled>No trainers available</option>
            <?php endif; ?>
        </select>
    </div>

    <!-- Appointment Date -->
    <div class="form-group">
        <label for="appointment-date">Appointment Date</label>
        <input type="datetime-local" id="appointment-date" name="appointment_date" class="form-control" required>
    </div>

    <!-- Submit Button -->
    <button type="submit" class="btn btn-primary">Book Appointment</button>
</form>


<!-- Query Section -->
<div id="query-section" class="container my-5">
    <h2 class="text-center">Submit Your Query</h2>
    <form id="queryForm" class="mt-4">
        <div class="card p-4">
            <div class="mb-3">
                <label for="subject" class="form-label">Subject</label>
                <input type="text" id="subject" name="subject" class="form-control" placeholder="Enter the subject" required>
            </div>
            <div class="mb-3">
                <label for="message" class="form-label">Message</label>
                <textarea id="message" name="message" rows="5" class="form-control" placeholder="Enter your message" required></textarea>
            </div>
            <button type="submit" class="btn btn-primary w-100">Submit Query</button>
        </div>
    </form>
</div>

<script>
    document.getElementById("queryForm").addEventListener("submit", function (event) {
    event.preventDefault();

    var formData = new FormData(this);

    fetch("submit_query.php", {
        method: "POST",
        body: formData,
    })
        .then((response) => {
            if (!response.ok) {
                throw new Error(`HTTP error! Status: ${response.status}`);
            }
            return response.json();
        })
        .then((data) => {
            if (data.success) {
                alert(data.success);
            } else if (data.error) {
                alert(data.error);
            }
        })
        .catch((error) => {
            console.error("Fetch error:", error);
            alert("An unexpected error occurred. Check the console for details.");
        });
});

</script>


<!-- Footer -->
<footer class="footer py-4">
    <div class="container text-center">
        <p>FitZone Fitness Center | Kurunegala, Sri Lanka</p>
        <p>© 2024 FitZone. All Rights Reserved.</p>
        <p>
            <a href="#" class="mr-2"><i class="fab fa-facebook-f"></i></a>
            <a href="#" class="mr-2"><i class="fab fa-instagram"></i></a>
            <a href="#" class="mr-2"><i class="fab fa-twitter"></i></a>
            <a href="#" class="mr-2"><i class="fab fa-youtube"></i></a>
        </p>
    </div>
</footer>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<script>
    document.getElementById('appointmentForm').addEventListener('submit', function(event) {
        event.preventDefault(); // Prevent default form submission

        // Create a FormData object to send form data
        const formData = new FormData(this);

        // Send the AJAX request
        fetch('book_appointment.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            // Check response and show appropriate message
            if (data.success) {
                alert('Appointment booked successfully!');
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An unexpected error occurred. Please try again.');
        });
    });

    
</script>

</body>
</html>