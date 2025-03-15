<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Database connection details
$servername = "localhost"; // Change this if needed
$username = "root";        // Your MySQL username
$password = "";            // Your MySQL password
$dbname = "fitzone";       // Your database name

// Create database connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch trainers from the database
$trainers_query = "SELECT name, proficiency, image FROM trainers";
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

// Check if query execution for memberships was successful
if (!$memberships_result) {
    die("Error fetching memberships: " . $conn->error);
}

// Fetch membership packages into an array
$memberships = [];
if ($memberships_result->num_rows > 0) {
    while ($row = $memberships_result->fetch_assoc()) {
        $memberships[] = $row;
    }
}

// Fetch classes from the database
$classes_query = "
    SELECT 
        c.class_id, 
        c.name AS class_name, 
        c.description, 
        c.type 
    FROM 
        classes c
";
$classes_result = $conn->query($classes_query);

// Check if query execution for classes was successful
if (!$classes_result) {
    die("Error fetching classes: " . $conn->error);
}

// Fetch classes into an array
$classes = [];
if ($classes_result->num_rows > 0) {
    while ($row = $classes_result->fetch_assoc()) {
        $classes[] = $row;
    }
} else {
    $classes = []; // Empty array if no classes found
}

// Close the database connection
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
        
        /* Home Section Styling */
#home {
    background-color: #1a1a1a; /* Dark Background */
    padding: 80px 0;
    color: white; /* Text color */
}

.moving-image {
    border: 0px solid #444; /* Border for images */
}

.carousel-inner img {
    height: 400px; /* Fixed image height */
    object-fit: cover; /* Maintain aspect ratio */
    border-radius: 10px;
}

.carousel-control-prev-icon,
.carousel-control-next-icon {
    background-color: #444; /* Icon Background */
    border-radius: 50%;
}
 
 /* Home Section Styling */
#home {
    background-color: #1a1a1a;
    padding: 0;
    position: relative;
    color: white;
    height: 100vh; /* Full viewport height */
    overflow: hidden; /* Prevent unwanted scroll */
}

/* Centered Text Styling */
.carousel-text {
    position: absolute;
    top: 50%; /* Move text 50% down */
    left: 50%; /* Move text 50% from the left */
    transform: translate(-50%, -50%); /* Adjust for center alignment */
    z-index: 10; /* Place text above carousel images */
    text-align: center; /* Center text content */
    color: #fff; /* White text color */
    text-shadow: 2px 2px 6px rgba(0, 0, 0, 0.7); /* Text shadow for clarity */
}

.carousel-text h2 {
    font-size: 3rem; /* Adjust heading size */
    margin-bottom: 1rem;
}

.carousel-text p {
    font-size: 1.2rem;
    margin-bottom: 1.5rem;
}

/* Carousel Image Styling */
.carousel-inner img {
    height: 100vh; /* Full height */
    object-fit: cover; /* Maintain aspect ratio */
    filter: brightness(50%); /* Darken background for contrast */
}

/* Blog Page Styling */
body {
    font-family: Arial, sans-serif;
    background-color: #111; /* Dark background */
    color: #ddd; /* Light text color */
}

h1 {
    font-weight: bold;
    text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.7);
}

.card {
    background-color: #222;
    color: #fff;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    border: none;
}

.card:hover {
    transform: translateY(-10px);
    box-shadow: 0px 10px 20px rgba(255, 193, 7, 0.5);
}

.card-title {
    color: #ffc107; /* Yellow color for titles */
}

.btn-warning {
    background-color: #ffc107;
    border: none;
    color: #333;
    font-weight: bold;
}

.btn-warning:hover {
    background-color: #ffca2c;
    color: #000;
}
 
 
/* Membership Section Styling */
#membership {
    background-color: #1a1a1a;
    padding: 100px 50px;
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

/* Input fields styling */
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

/* Button styling */
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
    background-color: #2a2a2a; /* Dark background */
    color: #ffffff; /* White text */
    padding: 40px 20px; /* Increase padding for larger footer */
    margin-top: 20px; /* Add space between content and footer */
    text-align: center; /* Center text content */
}

.footer a {
    color: #ffa500; /* Accent color for links */
    text-decoration: none; /* Remove underline */
}

.footer a:hover {
    color: #e69500; /* Darker orange on hover */
}

/* Additional Styling for Extended Footer */
.footer .footer-content {
    max-width: 1200px; /* Center content within the footer */
    margin: auto;
    text-align: left; /* Adjust text alignment as needed */
    line-height: 1.8; /* Increase line height for better readability */
}

.footer .social-icons {
    margin-top: 20px;
}

.footer .social-icons a {
    margin: 0 10px;
    font-size: 1.5rem;
    color: #ffa500;
    transition: color 0.3s ease;
}

.footer .social-icons a:hover {
    color: #e69500;
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
        
    </style>
</head>
<body>
    <!-- Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="#">FitZone Fitness Center</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse justify-content-center" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item"><a class="nav-link" href="#home">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="#classes">Classes</a></li>
                    <li class="nav-item"><a class="nav-link" href="#blog">Blog</a></li>
                    <li class="nav-item"><a class="nav-link" href="#trainers">Trainers</a></li>
                    <li class="nav-item"><a class="nav-link" href="#membership">Membership</a></li>
                    <li class="nav-item"><a class="nav-link" href="login.php"><i class="fas fa-user"></i> Login</a></li>
                </ul>
            </div>
        </div>
    </nav>
    
    <section id="home" class="container-fluid text-center my-5">
    <div class="carousel-wrapper">
        <!-- Centered Text -->
        <div class="carousel-text">
            <h2 class="display-4">Welcome to FitZone Fitness Center</h2>
            <p class="text-muted">Your ultimate fitness destination in Kurunegala! Explore personalized training, group classes, and much more.</p>
        </div>

        <!-- Bootstrap Carousel -->
        <div id="homeCarousel" class="carousel slide" data-ride="carousel">
            <div class="carousel-inner">
                <div class="carousel-item active">
                    <img src="https://static.vecteezy.com/system/resources/previews/022/653/988/non_2x/treadmill-in-modern-gym-toned-image-3d-rendering-generative-ai-free-photo.jpg" 
                         class="d-block w-100 rounded moving-image" alt="Gym Image 1">
                </div>
                <div class="carousel-item">
                    <img src="https://d.newsweek.com/en/full/1524142/cardio-workout.jpg" 
                         class="d-block w-100 rounded moving-image" alt="Gym Image 2">
                </div>
                <div class="carousel-item">
                    <img src="https://wallsdesk.com/wp-content/uploads/2016/10/Gym-for-desktop.jpg" 
                         class="d-block w-100 rounded moving-image" alt="Gym Image 3">
                </div>
            </div>
            <a class="carousel-control-prev" href="#homeCarousel" role="button" data-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="sr-only">Previous</span>
            </a>
            <a class="carousel-control-next" href="#homeCarousel" role="button" data-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="sr-only">Next</span>
            </a>
        </div>
    </div>
</section>


  <!-- Classes Section -->
  <section id="classes" class="container my-5">
        <h2 class="text-center">OUR CLASSES</h2>
        <div class="row text-center">
            <?php foreach ($classes as $class): ?>
                <div class="col-md-4">
                    <div class="card p-3">
                        <h4><?= htmlspecialchars($class['class_name']) ?></h4>
                        <p><?= htmlspecialchars($class['description']) ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

     <!-- Blog Page -->
<section id="blog" class="container my-5">
    <h1 class="text-center text-uppercase mb-4 text-warning">FitZone Blog</h1>
    <p class="text-center mb-4 text-muted">
        Explore workout routines, healthy recipes, and inspiring fitness success stories.
    </p>

    <!-- Blog Grid -->
    <div class="row">
        <!-- Blog Card 1 -->
        <div class="col-md-6 mb-4">
            <div class="card h-100 shadow-lg border-0 rounded-lg">
                <img src="https://images.pexels.com/photos/841130/pexels-photo-841130.jpeg?cs=srgb&dl=pexels-victorfreitas-841130.jpg&fm=.jpg" class="card-img-top rounded-top" alt="Workout Routine">
                <div class="card-body bg-dark text-light">
                    <h5 class="card-title text-warning">Workout Routine for Beginners</h5>
                    <p class="card-text">Kickstart your fitness journey with our beginner-friendly workout plan. Simple yet effective exercises to build strength and endurance. Follow this routine for quick results!</p>
                </div>
            </div>
        </div>
        
        <!-- Blog Card 2 -->
        <div class="col-md-6 mb-4">
            <div class="card h-100 shadow-lg border-0 rounded-lg">
                <img src="https://recipes.net/wp-content/uploads/2023/09/healthy-lunch-ideas-for-work-1694857624.jpg" class="card-img-top rounded-top" alt="Healthy Meal Plan">
                <div class="card-body bg-dark text-light">
                    <h5 class="card-title text-warning">Healthy Meal Plan</h5>
                    <p class="card-text">Achieve your fitness goals faster with our meal plan suggestions. Delicious recipes tailored for muscle gain and fat loss. Perfect for anyone looking to fuel their workouts!</p>
                </div>
            </div>
        </div>
        
        <!-- Blog Card 3 -->
        <div class="col-md-6 mb-4">
            <div class="card h-100 shadow-lg border-0 rounded-lg">
                <img src="https://img.freepik.com/premium-photo/male-instructor-leading-group-men-through-challenging-strength-training-class-with-heavy-w_1314467-172844.jpg" class="card-img-top rounded-top" alt="Strength Training">
                <div class="card-body bg-dark text-light">
                    <h5 class="card-title text-warning">Strength Training Basics</h5>
                    <p class="card-text">Learn the basics of strength training and improve your lifting form with our expert tips and guides. Start building strength safely and efficiently today!</p>
                </div>
            </div>
        </div>
        
        <!-- Blog Card 4 -->
        <div class="col-md-6 mb-4">
            <div class="card h-100 shadow-lg border-0 rounded-lg">
                <img src="https://www.houstonweightloss.com/wp-content/uploads/sites/30/2024/08/NP-side-by-side.jpg" class="card-img-top rounded-top" alt="Success Stories">
                <div class="card-body bg-dark text-light">
                    <h5 class="card-title text-warning">Success Stories</h5>
                    <p class="card-text">Get inspired by real-life transformations and success stories from FitZone members who achieved their fitness goals. See how dedication can lead to life-changing results!</p>
                </div>
            </div>
        </div>
    </div>
</section>

  <!-- Trainers Section -->
<section id="trainers" class="container my-5">
    <h2 class="text-center">MEET OUR TRAINERS</h2>
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
    <h2 class="text-center text-white mb-4">MEMBERSHIP PACKAGES</h2>
    
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
    <h3 class="text-center text-white mb-4">REGISTER FOR MEMBERSHIP</h3>
    <form method="POST" action="register.php" id="membershipForm" class="p-4 bg-dark text-white rounded">
        <div class="form-group">
            <label for="name">Full Name</label>
            <input type="text" name="name" id="name" class="form-control" placeholder="Enter your name" required>
        </div>
        <div class="form-group">
            <label for="address">Address</label>
            <input type="text" name="address" id="address" class="form-control" placeholder="Enter your address" required>
        </div>
        <div class="form-group">
            <label for="age">Age</label>
            <input type="number" name="age" id="age" class="form-control" placeholder="Enter your age" required min="1">
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
        <button type="button" class="btn btn-primary btn-block" onclick="redirectToLogin()">Register</button>
    </form>
</div>

<script>
    function redirectToLogin() {
        window.location.href = 'login.php'; // Adjust to your login page URL
    }
</script>


<!-- Appointment -->
<div class="appointment-container">
    <div class="appointment-card">
        <h2>Book an Appointment</h2>
        <form>
            <!-- Select Class -->
           <div class="form-group">
           <label for="class">Select Class</label>
           <select id="class" name="class" class="form-control">
             <option value="" disabled selected>Select a Class</option>
             <option value="cardio-training">Cardio Training</option>
             <option value="strength-training">Strength Training</option>
             <option value="yoga-flexibility">Yoga and Flexibility</option>
             <option value="yoga-flexibility">Fitness</option>
            </select>
           </div>
           
           <!-- Select Trainer -->
           <div class="form-group">
           <label for="trainer">Select Trainer</label>
           <select id="trainer" name="trainer" class="form-control">
             <option value="" disabled selected>Select a Trainer</option>
             <option value="john-doe">John Doe - Cardio Expert</option>
             <option value="jane-smith">Jane Smith - Strength Training</option>
             <option value="michael-brown">Michael Brown - Yoga Instructor</option>
             <option value="emily-davis">Emily Davis - Fitness Coach</option>
            </select>
           </div>

            <!-- Appointment Date -->
            <div class="form-group">
                <label for="appointment-date">Appointment Date</label>
                <input type="datetime-local" id="appointment-date" name="appointment-date" class="form-control">
            </div>

            <!-- Submit Button -->
            <button type="submit" class="btn btn-primary">Book Appointment</button>
        </form>
    </div>
</div>

<!-- Query -->
<div id="query-section" class="container my-5">
    <h2 class="text-center">Submit Your Query</h2>
    <form action="submit_query.php" method="POST" class="mt-4">
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

</body>
</html>
