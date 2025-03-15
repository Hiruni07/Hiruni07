<?php
// Enable full error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection
$host = "localhost";
$username = "root";
$password = "";
$database = "fitzone";

$conn = new mysqli($host, $username, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize messages
$success_message = "";
$error_message = "";

// Handle form submission (Insert or Update)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $class_name = $_POST["class_name"];
    $description = $_POST["description"];
    $trainer_id = intval($_POST["trainer_id"]);
    $schedule = $_POST["schedule"];
    $type = $_POST["type"];
    $spots_available = intval($_POST["spots_available"]);

    if (isset($_POST["class_id"]) && !empty($_POST["class_id"])) {
        // Update existing class
        $class_id = intval($_POST["class_id"]);
        $update_query = "UPDATE classes SET name=?, description=?, trainer_id=?, schedule=?, type=?, spots_available=? WHERE class_id=?";
        $stmt = $conn->prepare($update_query);
        $stmt->bind_param("ssissii", $class_name, $description, $trainer_id, $schedule, $type, $spots_available, $class_id);
        if ($stmt->execute()) {
            $success_message = "Class updated successfully!";
        } else {
            $error_message = "Failed to update class! Please try again.";
        }
    } else {
        // Insert new class
        $insert_query = "INSERT INTO classes (name, description, trainer_id, schedule, type, spots_available) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($insert_query);
        $stmt->bind_param("ssissi", $class_name, $description, $trainer_id, $schedule, $type, $spots_available);
        if ($stmt->execute()) {
            $success_message = "Class saved successfully!";
        } else {
            $error_message = "Failed to save class! Please try again.";
        }
    }
    $stmt->close();
}

// Fetch existing classes
$classes_query = "
    SELECT 
        c.class_id, 
        c.name AS class_name, 
        c.description, 
        t.name AS trainer_name, 
        c.schedule, 
        c.type, 
        c.spots_available 
    FROM 
        classes c
    LEFT JOIN 
        trainers t 
    ON 
        c.trainer_id = t.trainer_id";
$classes_result = $conn->query($classes_query);

// Fetch trainers for the dropdown
$trainers_query = "SELECT trainer_id, name FROM trainers";
$trainers_result = $conn->query($trainers_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Classes Management</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
       body {
    background: linear-gradient(135deg, #1e1e2f, #343a50);
    font-family: 'Poppins', sans-serif;
    color: #ddd;
    margin: 0;
    padding: 0;
}

.container {
    background: #2b2b40;
    color: #ddd;
    padding: 25px;
    border-radius: 15px;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.3);
    margin-top: 30px;
}

h2, h3 {
    text-align: center;
    color: #fff;
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
    color: #fff;
}

form input, form textarea, form select {
    width: 100%;
    padding: 12px;
    border: 1px solid #444;
    border-radius: 8px;
    background: #3e3e54;
    font-size: 1rem;
    margin-top: 5px;
    color: #ddd;
}

form input:focus, form textarea:focus, form select:focus {
    border-color: #5a9;
    box-shadow: 0 0 5px rgba(90, 153, 153, 0.5);
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
    background: #5a9;
    color: #fff;
}

button.btn-primary:hover {
    background: #4d8a7a;
    box-shadow: 0 4px 10px rgba(90, 153, 153, 0.5);
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
    background: #fff;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
}

thead {
    background: #444;
    color: #fff;
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
    background-color: #f1f1f1;
}

td {
    text-align: center;
    padding: 10px 15px;
    color: #333;
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

    </style>
</head>
<body>
<div class="container mt-5">
    <h2 class="text-center">Classes Management</h2>

    <!-- Display Success or Error Messages -->
    <?php if (!empty($success_message)): ?>
        <div class="message success-message"><?= htmlspecialchars($success_message) ?></div>
    <?php endif; ?>
    <?php if (!empty($error_message)): ?>
        <div class="message error-message"><?= htmlspecialchars($error_message) ?></div>
    <?php endif; ?>

    <form method="POST" class="mb-4">
        <input type="hidden" name="class_id" id="class_id">
        <div class="form-group">
            <label>Class Name</label>
            <input type="text" name="class_name" id="class_name" class="form-control" placeholder="Type class name" required>
        </div>
        <div class="form-group">
            <label for="description">Description</label>
            <textarea name="description" id="description" class="form-control" placeholder="Enter description" required></textarea>
        </div>

        <div class="form-group">
         <label>Trainer</label>
         <select name="trainer_id" id="trainer_id" class="form-control" required>
    <option value="">Select Trainer</option>
    <?php while ($trainer = $trainers_result->fetch_assoc()): ?>
        <option value="<?= $trainer['trainer_id'] ?>"><?= htmlspecialchars($trainer['name']) ?></option>
    <?php endwhile; ?>
</select>
</div>

        <div class="form-group">
            <label>Schedule</label>
            <input type="datetime-local" name="schedule" id="schedule" class="form-control" required>
        </div>
        <div class="form-group">
            <label>Class Type</label>
            <select name="type" id="type" class="form-control" required>
    <option value="cardio Training">Cardio Training</option>
    <option value="yoga & flexibility">Yoga & Flexibility</option>
    <option value="strength Training">Strength Training</option>
</select>
        </div>
        <div class="form-group">
            <label>Spots Available</label>
            <input type="number" name="spots_available" id="spots_available" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary">Save</button>
        <button type="reset" class="btn btn-reset" onclick="clearForm()">Reset</button>
    </form>

    <h3>Existing Classes</h3>
    <table class="table table-striped">
    <thead class="thead-dark">
        <tr>
            <th>Class Name</th>
            <th>Description</th>
            <th>Trainer</th>
            <th>Schedule</th>
            <th>Type</th>
            <th>Spots Available</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($class = $classes_result->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($class["class_name"]) ?></td>
                <td><?= htmlspecialchars($class["description"]) ?></td>
                <td><?= htmlspecialchars($class["trainer_name"]) ?></td>
                <td><?= htmlspecialchars($class["schedule"]) ?></td>
                <td><?= htmlspecialchars($class["type"]) ?></td>
                <td><?= htmlspecialchars($class["spots_available"]) ?></td>
                <td>
                    <button onclick="editClass(<?= htmlspecialchars(json_encode($class)) ?>)" class="btn btn-warning btn-sm">
                        <i class="fas fa-edit"></i> Edit
                    </button>
                    <!-- DELETE BUTTON -->
                    <button onclick="deleteClass(<?= $class['class_id'] ?>)" class="btn btn-danger btn-sm">
                    <i class="fas fa-trash"></i> Delete
                    </button>

                </td>
            </tr>
        <?php endwhile; ?>
    </tbody>
</table>
</div>

<script>
function editClass(classData) {
    console.log("Editing class:", classData); // Debug: Output class data
    if (typeof classData === 'string') {
        classData = JSON.parse(classData); // Only if classData might be a JSON string
    }

    document.getElementById('class_id').value = classData.class_id;
    document.getElementById('class_name').value = classData.class_name;
    document.getElementById('description').value = classData.description;
    document.getElementById('schedule').value = classData.schedule.replace(" ", "T");
    document.getElementById('type').value = classData.type;
    document.getElementById('spots_available').value = classData.spots_available;

    // Ensure the trainer dropdown selects the correct trainer
    var trainerSelect = document.getElementById('trainer_id');
    for (var i = 0; i < trainerSelect.options.length; i++) {
        trainerSelect.options[i].selected = false;
        if (parseInt(trainerSelect.options[i].value) === classData.trainer_id) {
            trainerSelect.options[i].selected = true;
            break; // Exit the loop once the correct trainer is selected
        }
    }
}

    function clearForm() {
        document.getElementById('class_id').value = '';
        document.getElementById('class_name').value = '';
        document.getElementById('description').value = '';
        document.getElementById('trainer_id').value = '';
        document.getElementById('schedule').value = '';
        document.getElementById('type').value = '';
        document.getElementById('spots_available').value = '';
    }

    function deleteClass(classId) {
    if (confirm('Are you sure you want to delete this class?')) {
        fetch(`delete_class.php?delete_id=${classId}`, {
            method: 'GET'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Class deleted successfully!');
                window.location.reload(); // Reload the section of the page or remove the row from the table
            } else {
                alert('Failed to delete class!');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred. Please try again.');
        });
    }
}

</script>
</body>
</html>
