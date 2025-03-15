<?php
// Database connection
$conn = new mysqli("localhost", "root", "", "fitzone");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submission for adding or editing a trainer
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST["name"];
    $proficiency = $_POST["proficiency"];
    $experience_years = $_POST["experience_years"];
    $email = $_POST["email"];
    $trainer_id = isset($_POST["trainer_id"]) ? $_POST["trainer_id"] : null;
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

    // If editing, preserve old image if a new one isn't uploaded
    if (!$image_path && $trainer_id) {
        $result = $conn->query("SELECT image FROM trainers WHERE trainer_id = $trainer_id");
        if ($result && $row = $result->fetch_assoc()) {
            $image_path = $row["image"];
        }
    }

    // Check if email already exists
    if ($trainer_id) {
        $stmt = $conn->prepare("SELECT trainer_id FROM trainers WHERE email = ? AND trainer_id != ?");
        $stmt->bind_param("si", $email, $trainer_id);
    } else {
        $stmt = $conn->prepare("SELECT trainer_id FROM trainers WHERE email = ?");
        $stmt->bind_param("s", $email);
    }
    

    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        echo "<script>alert('Error: This email is already in use.'); window.history.back();</script>";
        exit();
    }
    $stmt->close();

    if ($trainer_id) {
        $stmt = $conn->prepare("UPDATE trainers SET name=?, proficiency=?, experience_years=?, email=?, image=? WHERE trainer_id=?");
        $stmt->bind_param("ssissi", $name, $proficiency, $experience_years, $email, $image_path, $trainer_id);
    } else {
        $stmt = $conn->prepare("INSERT INTO trainers (name, proficiency, experience_years, email, image) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("ssiss", $name, $proficiency, $experience_years, $email, $image_path);
    }

    $stmt->execute();
    $stmt->close();
    header("Location: trainers_management.php?message=Trainer saved successfully");
    exit();
}

// Handle deletion
if (isset($_GET["delete_id"])) {
    $delete_id = $_GET["delete_id"];
    $stmt = $conn->prepare("DELETE FROM trainers WHERE trainer_id = ?");
    $stmt->bind_param("i", $delete_id);
    if ($stmt->execute()) {
        $delete_message = "Trainer deleted successfully!";
    } else {
        $delete_message = "Error deleting trainer.";
    }
    $stmt->close();
    header("Location: trainers_management.php?message=" . urlencode($delete_message));
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trainers Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <style>
      body {
    background: linear-gradient(135deg, #121212, #1c1c1c); /* Dark and modern gradient */
    font-family: 'Poppins', sans-serif;
    color: #e0e0e0; /* Light gray text for contrast */
    margin: 0;
    padding: 0;
}

.container {
    background: #232323; /* Darker container background */
    color: #e0e0e0; /* Light text for readability */
    padding: 25px;
    border-radius: 15px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.6); /* Enhanced shadow for depth */
    margin-top: 30px;
}

h2, h3 {
    text-align: center;
    color: #ffffff; /* Pure white text for headings */
    font-weight: bold;
}

.message {
    margin-bottom: 15px;
    padding: 15px;
    border-radius: 10px;
    font-weight: 500;
}

.success-message {
    background: #28a745;
    color: #fff;
}

.error-message {
    background: #dc3545;
    color: #fff;
}

/* Form styling */
form .form-group {
    margin-bottom: 15px;
}

form label {
    font-weight: 500;
    color: #e0e0e0;
}

form input, form textarea, form select {
    width: 100%;
    padding: 12px;
    border: 1px solid #444;
    border-radius: 8px;
    background: #2f2f2f;
    font-size: 1rem;
    margin-top: 5px;
    color: #e0e0e0;
}

form input:focus, form textarea:focus, form select:focus {
    border-color: #6a11cb; /* Accent color */
    box-shadow: 0 0 5px rgba(106, 17, 203, 0.5);
    outline: none;
}

/* Buttons */
button {
    padding: 10px 20px;
    font-size: 1rem;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    transition: background-color 0.3s, box-shadow 0.3s;
}

button.btn-primary {
    background: #6a11cb;
    color: #fff;
}

button.btn-primary:hover {
    background: #4b089b;
    box-shadow: 0 4px 10px rgba(106, 17, 203, 0.5);
}

button.btn-reset {
    background: #6c757d;
    color: #fff;
}

button.btn-reset:hover {
    background: #5a6268;
}

/* Table Styling */
table {
    width: 100%;
    margin-top: 20px;
    border-collapse: collapse;
    font-size: 1rem;
    background: #ffffff; /* White background for better visibility */
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.4); /* Softer shadow */
}

thead {
    background: #444; /* Dark header */
    color: #ffffff; /* White text for header */
    text-transform: uppercase;
}

thead th {
    padding: 12px 15px;
    font-weight: 600;
    text-align: center;
}

tbody tr {
    border-bottom: 1px solid #ddd;
    transition: background-color 0.3s;
}

tbody tr:nth-child(odd) {
    background-color: #f9f9f9;
}

tbody tr:nth-child(even) {
    background-color: #ffffff;
}

tbody tr:hover {
    background-color: #eaeaea; /* Light gray hover effect */
}

td {
    text-align: center;
    padding: 10px 15px;
    color: #333; /* Dark text for contrast on white table */
}

.btn-sm {
    font-size: 0.9rem;
    padding: 6px 12px;
    border-radius: 6px;
}

.btn-warning {
    background: #f39c12;
    color: #fff;
}

.btn-warning:hover {
    background: #e67e22;
    box-shadow: 0 4px 10px rgba(243, 156, 18, 0.5);
}

.btn-danger {
    background: #e74c3c;
    color: #fff;
}

.btn-danger:hover {
    background: #c0392b;
    box-shadow: 0 4px 10px rgba(231, 76, 60, 0.5);
}

/* Specific styles as per your request */
.trainer-image {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    object-fit: cover;
}

.edit-btn, .delete-btn {
    padding: 5px 10px;
    border-radius: 5px;
    color: #fff;
    text-decoration: none;
}

.edit-btn {
    background-color: #007bff;
}

.delete-btn {
    background-color: #dc3545;
}

.edit-btn:hover, .delete-btn:hover {
    opacity: 0.8;
}

.btn-add {
    margin-bottom: 20px;
    margin-top: 20px; /* Adds a gap between Add Trainer button and other elements */
}

.alert {
    margin-top: 20px;
}

.btn-save-cancel {
    margin-right: 10px;
}

/* Responsive Design */
@media (max-width: 768px) {
    .container {
        padding: 15px;
    }

    table thead {
        display: none;
    }

    table tbody, table tr, table td {
        display: block;
        width: 100%;
    }

    table tbody tr {
        margin-bottom: 15px;
        border: 1px solid #ddd;
        border-radius: 10px;
        padding: 15px;
    }

    table td {
        text-align: left;
        padding: 10px 0;
        display: flex;
        justify-content: space-between;
    }

    table td::before {
        content: attr(data-label);
        font-weight: bold;
        text-transform: uppercase;
        color: #444;
    }
}
    
     /* CSS for Trainer Management Header */
     .header-title {
         color: white;
         font-weight: bold;
         text-align: center;
         margin-bottom: 30px;
        }

    </style>
</head>
<body>

<div class="container">
<h1 class="header-title">Trainers Management</h1>

    <?php if (isset($_GET['message'])): ?>
        <div class="alert alert-info"><?= htmlspecialchars($_GET['message']); ?></div>
    <?php endif; ?>

    <div id="trainerForm" style="display: none;">
    <h4 id="formTitle">Add Trainer</h4>
    <form id="addTrainerForm" enctype="multipart/form-data">
        <input type="hidden" name="trainer_id" id="trainer_id">
        <div class="mb-3">
            <label for="name" class="form-label">Name</label>
            <input type="text" class="form-control" id="name" name="name" required>
        </div>
        <div class="mb-3">
            <label for="proficiency" class="form-label">Proficiency</label>
            <input type="text" class="form-control" id="proficiency" name="proficiency" required>
        </div>
        <div class="mb-3">
            <label for="experience_years" class="form-label">Experience Years</label>
            <input type="number" class="form-control" id="experience_years" name="experience_years" required>
        </div>
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control" id="email" name="email" required>
        </div>
        <div class="mb-3">
            <label for="image" class="form-label">Image</label>
            <input type="file" class="form-control" id="image" name="image">
        </div>
        <button type="button" class="btn btn-primary" onclick="saveTrainer()">Save</button>
        <button type="button" class="btn btn-secondary" onclick="toggleForm()">Cancel</button>
    </form>
</div>

    <div>
        <button class="btn btn-success btn-add" onclick="toggleForm()">+ Add Trainer</button>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Image</th>
                    <th>Name</th>
                    <th>Proficiency</th>
                    <th>Experience Years</th>
                    <th>Email</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $result = $conn->query("SELECT * FROM trainers");
                while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row["trainer_id"]) ?></td>
                        <td><img src="<?= htmlspecialchars($row["image"] ?: 'uploads/trainers/default.jpg') ?>" class="trainer-image"></td>
                        <td><?= htmlspecialchars($row["name"]) ?></td>
                        <td><?= htmlspecialchars($row["proficiency"]) ?></td>
                        <td><?= htmlspecialchars($row["experience_years"]) ?></td>
                        <td><?= htmlspecialchars($row["email"]) ?></td>
                        <td>
                            <a href="javascript:void(0)" 
                               onclick="editTrainer(
                                   <?= $row['trainer_id'] ?>,
                                   '<?= htmlspecialchars($row['name']) ?>',
                                   '<?= htmlspecialchars($row['proficiency']) ?>',
                                   <?= $row['experience_years'] ?>,
                                   '<?= htmlspecialchars($row['email']) ?>',
                                   '<?= htmlspecialchars($row['image']) ?>'
                               )" 
                               class="edit-btn">Edit</a>
                               <a href="javascript:void(0)" 
                                    class="delete-btn" onclick="deleteTrainer(<?= $row['trainer_id'] ?>, this)">Delete</a>

                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
    function toggleForm() {
        const form = document.getElementById("trainerForm");
        form.style.display = form.style.display === "none" ? "block" : "none";
        document.getElementById("formTitle").textContent = "Add Trainer";
        document.getElementById("trainer_id").value = "";
        document.getElementById("name").value = "";
        document.getElementById("proficiency").value = "";
        document.getElementById("experience_years").value = "";
        document.getElementById("email").value = "";
        document.getElementById("image").value = "";
    }

    function editTrainer(id, name, proficiency, experienceYears, email, image) {
    // Populate the form with the trainer's current data
    toggleForm();
    document.getElementById("formTitle").textContent = "Edit Trainer";
    document.getElementById("trainer_id").value = id;
    document.getElementById("name").value = name;
    document.getElementById("proficiency").value = proficiency;
    document.getElementById("experience_years").value = experienceYears;
    document.getElementById("email").value = email;
}

function saveTrainer() {
    const form = document.getElementById("addTrainerForm");
    const formData = new FormData(form);

    const xhr = new XMLHttpRequest();
    xhr.open("POST", "edit_trainer.php", true);

    xhr.onload = function () {
        if (xhr.status === 200) {
            const response = JSON.parse(xhr.responseText);
            if (response.success) {
                // Update the corresponding row in the table
                const tableRows = document.querySelectorAll("table tbody tr");
                tableRows.forEach(row => {
                    if (row.children[0].textContent == response.trainer.trainer_id) {
                        row.children[2].textContent = response.trainer.name;
                        row.children[3].textContent = response.trainer.proficiency;
                        row.children[4].textContent = response.trainer.experience_years;
                        row.children[5].textContent = response.trainer.email;
                        if (response.trainer.image) {
                            row.children[1].children[0].src = response.trainer.image;
                        }
                    }
                });
                alert("Trainer updated successfully!");
                toggleForm();
            } else {
                alert(response.message);
            }
        } else {
            alert("An error occurred. Please try again.");
        }
    };

    xhr.send(formData);
}


    function deleteTrainer(trainerId, element) {
    if (!confirm("Are you sure you want to delete this trainer?")) {
        return;
    }

    const xhr = new XMLHttpRequest();
    xhr.open("POST", "delete_trainer.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

    xhr.onload = function() {
        if (xhr.status === 200) {
            const response = JSON.parse(xhr.responseText);
            if (response.success) {
                const row = element.closest("tr");
                row.parentNode.removeChild(row); // Remove the row from the table
                alert(response.message);
            } else {
                alert(response.message);
            }
        } else {
            alert("An error occurred. Please try again.");
        }
    };

    xhr.send("delete_id=" + trainerId);
}

function addTrainer() {
    const form = document.getElementById("addTrainerForm");
    const formData = new FormData(form);

    const xhr = new XMLHttpRequest();
    xhr.open("POST", "add_trainer.php", true);

    xhr.onload = function() {
        if (xhr.status === 200) {
            const response = JSON.parse(xhr.responseText);
            if (response.success) {
                // Update the table dynamically
                const tableBody = document.querySelector("table tbody");
                const newRow = document.createElement("tr");

                newRow.innerHTML = `
                    <td>${response.trainer.trainer_id}</td>
                    <td><img src="${response.trainer.image}" class="trainer-image"></td>
                    <td>${response.trainer.name}</td>
                    <td>${response.trainer.proficiency}</td>
                    <td>${response.trainer.experience_years}</td>
                    <td>${response.trainer.email}</td>
                    <td>
                        <a href="javascript:void(0)" 
                           class="edit-btn" 
                           onclick="editTrainer(${response.trainer.trainer_id}, '${response.trainer.name}', '${response.trainer.proficiency}', ${response.trainer.experience_years}, '${response.trainer.email}', '${response.trainer.image}')">
                           Edit
                        </a>
                        <a href="javascript:void(0)" 
                           class="delete-btn" 
                           onclick="deleteTrainer(${response.trainer.trainer_id}, this)">
                           Delete
                        </a>
                    </td>
                `;

                tableBody.appendChild(newRow);
                alert("Trainer added successfully!");
                toggleForm(); // Close the form
            } else {
                alert(response.message);
            }
        } else {
            alert("An error occurred. Please try again.");
        }
    };

    xhr.send(formData);
}

function saveTrainer() {
    const form = document.getElementById("addTrainerForm");
    const formData = new FormData(form);

    const xhr = new XMLHttpRequest();
    xhr.open("POST", "trainers_management.php", true); // Post to the same script that handles both add and edit

    xhr.onload = function () {
        if (xhr.status === 200) {
            const response = xhr.responseText; // You can handle the response here if you want feedback
            alert("Trainer saved successfully!");
            toggleForm(); // Close the form after saving
            location.reload(); // Reload the page to update the list of trainers
        } else {
            alert("An error occurred. Please try again.");
        }
    };

    xhr.send(formData); // Send the form data to the server
}


</script>

</body>
</html>