<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #121212;
            color: #ffffff;
            font-family: 'Arial', sans-serif;
        }

        .navbar {
            background-color:rgb(31, 31, 31);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.5);
        }

        .navbar-brand {
            color:#B8860B !important;
            font-weight: bold;
        }

        .nav-link {
            color:rgb(176, 176, 176) !important;
            transition: color 0.3s;
        }

        .nav-link:hover {
            color: #00d4ff !important;
        }
         
        .nav-link {
        color: #b0b0b0 !important;
        transition: color 0.3s;
    }

    .nav-link:hover {
        color: #00d4ff !important;
    }

    .nav-link i {
        margin-right: 5px; /* Space between icon and text */
    }

    /* Optional: Add custom style to logout button */
    .nav-item a {
        display: flex;
        align-items: center;
        padding: 8px 12px;
        border-radius: 5px;
    }
    
    .nav-item a:hover {
        background-color: #f8f9fa; /* Hover effect */
    }

    </style>
</head>
<body>
    <!-- Navigation Bar -->
    <nav class="navbar navbar-expand-lg">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Admin Dashboard</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item"><a class="nav-link" href="admin_dashboard.php?page=user_management">User Management</a></li>
                    <li class="nav-item"><a class="nav-link" href="admin_dashboard.php?page=appointment_management">Appointment Management</a></li>
                    <li class="nav-item"><a class="nav-link" href="admin_dashboard.php?page=classes_management">Classes Management</a></li>
                    <li class="nav-item"><a class="nav-link" href="admin_dashboard.php?page=trainers_management">Trainers Management</a></li>
                    <li class="nav-item"><a class="nav-link" href="admin_dashboard.php?page=memberships">Memberships</a></li>
                    <li class="nav-item"><a class="nav-link" href="admin_dashboard.php?page=membership_packages">Membership Packages</a></li>
                    <li class="nav-item"><a class="nav-link" href="admin_dashboard.php?page=user_queries">User Queries</a></li>
                </ul>

                  <!-- Logout Button in Navbar -->
                 <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link" href="logout.php" title="Logout">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </a>
                </li>
            </ul>
                
           
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container">
        <?php
        // Determine which page to load based on the 'page' parameter
        $page = isset($_GET['page']) ? $_GET['page'] : 'user_management';

        switch ($page) {
            case 'user_management':
                include 'user_management.php';
                break;
            case 'appointment_management':
                include 'appointment_management.php';
                break;
            case 'classes_management':
                include 'classes_management.php';
                break;
            case 'trainers_management':
                include 'trainers_management.php';
                break;
            case 'memberships':
                include 'membership_management.php';
                break;
            case 'membership_packages':
                include 'membership_packages.php';
                break;
            case 'user_queries':
                include 'user_queries.php';
                break;
           
        }
        ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
 
</body>
</html>
