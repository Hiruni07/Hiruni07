<?php
// Database connection
$conn = new mysqli("localhost", "root", "", "fitzone");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Insert or update logic
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST['add_membership'])) {
        // Adding or updating membership
        if (!empty($_POST['edit_id'])) {
            // Updating an existing membership
            $edit_id = intval($_POST['edit_id']);
            $name = $conn->real_escape_string($_POST['name']);
            $price = $conn->real_escape_string($_POST['price']);
            $duration = $conn->real_escape_string($_POST['duration']);
            $benefits = $conn->real_escape_string($_POST['benefits']);
            $promotions = $conn->real_escape_string($_POST['promotions']);

            $query = "UPDATE memberships 
                      SET name = '$name', price = '$price', duration = '$duration', 
                          benefits = '$benefits', special_promotions = '$promotions' 
                      WHERE membership_id = $edit_id";

            if ($conn->query($query)) {
                echo "<script>alert('Membership package updated successfully!');</script>";
            } else {
                echo "<script>alert('Error updating membership: " . $conn->error . "');</script>";
            }
        } else {
            // Adding a new membership
            $name = $conn->real_escape_string($_POST['name']);
            $price = $conn->real_escape_string($_POST['price']);
            $duration = $conn->real_escape_string($_POST['duration']);
            $benefits = $conn->real_escape_string($_POST['benefits']);
            $promotions = $conn->real_escape_string($_POST['promotions']);

            $query = "INSERT INTO memberships (name, price, duration, benefits, special_promotions, created_at) 
                      VALUES ('$name', '$price', '$duration', '$benefits', '$promotions', NOW())";

            if ($conn->query($query)) {
                echo "<script>alert('Membership package added successfully!');</script>";
            } else {
                echo "<script>alert('Error adding membership: " . $conn->error . "');</script>";
            }
        }
    } elseif (isset($_POST['delete_id'])) {
        // Deleting a membership
        $delete_id = intval($_POST['delete_id']);
        $query = "DELETE FROM memberships WHERE membership_id = $delete_id";

        if ($conn->query($query)) {
            echo "<script>alert('Membership package deleted successfully!');</script>";
        } else {
            echo "<script>alert('Error deleting membership: " . $conn->error . "');</script>";
        }
    }
}

// Fetch membership packages
$query = "SELECT * FROM memberships";
$result = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Membership Packages</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* General body styles */
body {
    font-family: 'Arial', sans-serif;
    background: linear-gradient(135deg, #0f0f0f, #1c1c1c); /* Dark background gradient */
    margin: 0;
    padding: 0;
    color: #eaeaea; /* Light text for better contrast */
}

/* Container */
.container {
    width: 90%;
    margin: 40px auto;
    background-color: #252525; /* Dark gray container background */
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.4); /* Subtle shadow for the container */
    border-radius: 8px;
    padding: 20px;
}

/* Header styles */
h1 {
    text-align: center;
    font-size: 28px;
    font-weight: bold;
    color: #ffffff; /* White text for headers */
    margin-bottom: 20px;
}

/* Form styling */
form {
    display: flex;
    flex-wrap: wrap;
    gap: 15px;
    margin-bottom: 20px;
}

form input, form textarea, form button {
    font-size: 16px;
    padding: 10px;
    border-radius: 5px;
    border: 1px solid #444; /* Dark border for form elements */
    background-color: #333; /* Dark background for inputs */
    color: #eaeaea; /* Light text for inputs */
    width: 100%;
}

form input, form textarea {
    flex: 1 1 48%;
}

form textarea {
    height: 80px;
}

form button {
    background-color: #007bff;
    color: #fff;
    font-weight: bold;
    cursor: pointer;
    flex: 1 1 100%;
}

form button:hover {
    background-color: #0056b3;
}

/* Table styling */
.table {
    width: 100%;
    border-collapse: collapse;
    font-size: 16px;
    background-color: #ffffff; /* White background for table details */
    border-radius: 5px;
    overflow: hidden;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3); /* Subtle shadow for the table */
}

.table thead {
    background-color: #000; /* Black background for the header */
    color: #fff; /* White text for better readability */
}

.table th, .table td {
    text-align: left;
    padding: 12px;
    border: 1px solid #ddd;
}

.table tbody tr {
    background-color: #ffffff; /* White background for table rows */
}

.table tbody tr:nth-child(even) {
    background-color: #f9f9f9; /* Light background for alternating rows */
}

.table tbody tr:hover {
    background-color: #e1e1e1; /* Subtle hover effect */
}


/* Action button styles */
.icon-button {
    background: none;
    border: none;
    cursor: pointer;
    font-size: 18px;
    margin: 0 5px;
}

.icon-button.edit {
    color: #007bff;
}

.icon-button.delete {
    color: #dc3545;
}

.icon-button:hover {
    transform: scale(1.2);
}

/* Responsive styling */
@media (max-width: 768px) {
    form input, form textarea {
        flex: 1 1 100%;
    }

    form button {
        flex: 1 1 100%;
    }
}

    </style>
</head>
<body>
    <div class="container">
        <h1>Manage Membership Packages</h1>
        <!-- Add/Update Membership Form -->
        <form method="POST" id="membershipForm">
            <input type="hidden" name="edit_id" id="edit_id">
            <input type="text" name="name" id="name" placeholder="Name" required>
            <input type="number" step="0.01" name="price" id="price" placeholder="Price (Rs)" required>
            <input type="number" name="duration" id="duration" placeholder="Duration (Months)" required>
            <textarea name="benefits" id="benefits" placeholder="Benefits (e.g., Gym Access, Yoga Classes)" required></textarea>
            <textarea name="promotions" id="promotions" placeholder="Special Promotions (if any)" required></textarea>
            <button type="submit" name="add_membership" id="formButton">+ Add Membership Package</button>
        </form>
        <!-- Membership Table -->
        <table class="table">
            <thead>
                <tr>
                    <th>Id</th>
                    <th>Name</th>
                    <th>Price</th>
                    <th>Duration (Months)</th>
                    <th>Benefits</th>
                    <th>Promotions</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result->num_rows > 0) {
                    $counter = 1;
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $counter++ . "</td>";
                        echo "<td>" . htmlspecialchars($row['name']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['price']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['duration']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['benefits']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['special_promotions']) . "</td>";
                        echo "<td>
                                <button type='button' class='icon-button edit' onclick='editMembership(" . json_encode($row) . ")'>
                                    <i class='fas fa-edit'></i>
                                </button>
                                <button type='button' class='icon-button delete' onclick='confirmDelete(" . $row['membership_id'] . ")'>
                                    <i class='fas fa-trash'></i>
                                </button>
                              </td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='7'>No membership packages found.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <!-- Hidden Delete Form -->
    <form method="POST" id="deleteForm" style="display: none;">
        <input type="hidden" name="delete_id" id="delete_id">
    </form>

    <script>
        // Fill the form with membership details for editing
        function editMembership(data) {
            document.getElementById('edit_id').value = data.membership_id;
            document.getElementById('name').value = data.name;
            document.getElementById('price').value = data.price;
            document.getElementById('duration').value = data.duration;
            document.getElementById('benefits').value = data.benefits;
            document.getElementById('promotions').value = data.special_promotions;

            // Change button text to "Update"
            document.getElementById('formButton').textContent = "Update Membership Package";
        }

        // Confirm and delete a membership
        function confirmDelete(id) {
            if (confirm("Are you sure you want to delete this membership package?")) {
                document.getElementById('delete_id').value = id;
                document.getElementById('deleteForm').submit();
            }
        }
    </script>
</body>
</html>
