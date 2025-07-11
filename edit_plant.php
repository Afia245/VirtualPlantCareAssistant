<?php
session_start();
$servername = "localhost";
$username = "root";
$password = "";
$database = "plantcare";
$conn = new mysqli($servername, $username, $password, $database);

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Get plant_id from URL
if (!isset($_GET['plant_id'])) {
    die("Plant ID missing.");
}
$plant_id = intval($_GET['plant_id']);

// Handle update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $common_plant = $_POST['common_plant'];
    $scientific_name = $_POST['scientific_name'];
    $watering = $_POST['watering'];
    $sunlight = $_POST['sunlight'];
    $poisonous = $_POST['poisonous'];
    $soil_needed = $_POST['soil_needed'];
    $last_watered = $_POST['last_watered'] ?: null;  // allow empty
    $watering_frequency = intval($_POST['watering_frequency']);

    // Handle image upload only if a new file is uploaded
    $image_path = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = "uploads/";
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
        $tmp_name = $_FILES['image']['tmp_name'];
        $filename = basename($_FILES['image']['name']);
        $target_file = $upload_dir . time() . "_" . $filename;

        if (move_uploaded_file($tmp_name, $target_file)) {
            $image_path = $target_file;
        }
    }

    if ($image_path) {
        // Update including image
        $stmt = $conn->prepare("UPDATE plantdetails SET common_plant=?, scientific_name=?, watering=?, sunlight=?, poisonous=?, soil_needed=?, last_watered=?, watering_frequency=?, image=? WHERE plant_id=? AND user_id=?");
        $stmt->bind_param("sssssssisii", $common_plant, $scientific_name, $watering, $sunlight, $poisonous, $soil_needed, $last_watered, $watering_frequency, $image_path, $plant_id, $user_id);
    } else {
        // Update without changing image
        $stmt = $conn->prepare("UPDATE plantdetails SET common_plant=?, scientific_name=?, watering=?, sunlight=?, poisonous=?, soil_needed=?, last_watered=?, watering_frequency=? WHERE plant_id=? AND user_id=?");
        $stmt->bind_param("sssssssiii", $common_plant, $scientific_name, $watering, $sunlight, $poisonous, $soil_needed, $last_watered, $watering_frequency, $plant_id, $user_id);
    }
    
    $stmt->execute();
    $stmt->close();

    header("Location: user_dashboard.php");
    exit();
}

// Get existing data to show in form
$stmt = $conn->prepare("SELECT * FROM plantdetails WHERE plant_id=? AND user_id=?");
$stmt->bind_param("ii", $plant_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    die("Plant not found.");
}
$plant = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Plant</title>
    <style>
        body {
            font-family: Arial;
            background-color: #f0fff0;
            background: url(care.jpg)no-repeat center center/cover;
            
        }
        .form-container {
            width: 500px;
            margin: 40px auto;
            padding: 20px;
            background-color: #e6ffe6;
            border-radius: 8px;
            box-shadow: 0 0 10px #aaa;
        }
        input, textarea, select {
            width: 100%;
            padding: 10px;
            margin-top: 8px;
            margin-bottom: 16px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        label {
            font-weight: bold;
        }
        .btn {
            background-color: green;
            color: white;
            padding: 10px 16px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .btn:hover {
            background-color: darkgreen;
        }
        img {
            max-width: 100%;
            margin-bottom: 10px;
            border-radius: 5px;
        }
    </style>
</head>
<body>

<div class="form-container">
    <h2>Edit Plant Information</h2>
    <form method="POST" enctype="multipart/form-data">
        <label>Common Plant Name:</label>
        <input type="text" name="common_plant" value="<?= htmlspecialchars($plant['common_plant']) ?>" required>

        <label>Scientific Name:</label>
        <input type="text" name="scientific_name" value="<?= htmlspecialchars($plant['scientific_name']) ?>" required>

        <label>Watering Info:</label>
        <input type="text" name="watering" value="<?= htmlspecialchars($plant['watering']) ?>" required>

        <label>Sunlight Info:</label>
        <input type="text" name="sunlight" value="<?= htmlspecialchars($plant['sunlight']) ?>" required>

        <label>Poisonous (Yes/No):</label>
        <select name="poisonous" required>
            <option value="Yes" <?= $plant['poisonous'] == 'Yes' ? 'selected' : '' ?>>Yes</option>
            <option value="No" <?= $plant['poisonous'] == 'No' ? 'selected' : '' ?>>No</option>
        </select>

        <label>Soil Needed:</label>
        <input type="text" name="soil_needed" value="<?= htmlspecialchars($plant['soil_needed']) ?>" required>

        <label>Last Watered:</label>
        <input type="date" name="last_watered" value="<?= htmlspecialchars($plant['last_watered']) ?>">

        <label>Watering Frequency (days):</label>
        <input type="number" name="watering_frequency" min="1" value="<?= (int)$plant['watering_frequency'] ?>" required>

        <label>Current Image:</label>
        <?php if (!empty($plant['image'])): ?>
            <img src="<?= htmlspecialchars($plant['image']) ?>" alt="Plant Image">
        <?php else: ?>
            <p>No image available</p>
        <?php endif; ?>

        <label>Change Image (optional):</label>
        <input type="file" name="image" accept="image/*">

        <button class="btn" type="submit">Update Plant</button>
    </form>
</div>

</body>
</html>
