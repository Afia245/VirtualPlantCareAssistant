<?php
session_start();
$servername = "localhost";
$username = "root";
$password = "";
$database = "plantcare";

$conn = new mysqli($servername, $username, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Approve plant (move to plantdetails)
if (isset($_GET['approve'])) {
    $id = $_GET['approve'];

    // Get plant data from pending_plants
    $stmt = $conn->prepare("SELECT * FROM pending_plants WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows === 1) {
        $row = $result->fetch_assoc();

        // Insert into plantdetails table
        $stmt2 = $conn->prepare("INSERT INTO plantdetails (user_id, common_plant, scientific_name, watering, sunlight, poisonous, soil_needed, last_watered, watering_frequency, image) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt2->bind_param(
            "isssssssss",
            $row['user_id'],
            $row['common_plant'],
            $row['scientific_name'],
            $row['watering'],
            $row['sunlight'],
            $row['poisonous'],
            $row['soil_needed'],
            $row['last_watered'],
            $row['watering_frequency'],
            $row['image']
        );
        if ($stmt2->execute()) {
            // Delete from pending_plants after successful insert
            $conn->query("DELETE FROM pending_plants WHERE id = $id");
        } else {
            echo "Insert failed: (" . $stmt2->errno . ") " . $stmt2->error;
        }
        $stmt2->close();
    } else {
        echo "Pending plant not found.";
    }
    $stmt->close();
}

// Delete plant from pending list
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $conn->query("DELETE FROM pending_plants WHERE id = $id");
}

// Get all pending plants
$result = $conn->query("SELECT * FROM pending_plants");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <style>
        body { background-color: #f0f0f0; font-family: Arial; }
        table { width: 90%; margin: 40px auto; border-collapse: collapse; }
        th, td { padding: 10px; border: 1px solid #ccc; text-align: center; }
        h2 { text-align: center; color: green; }
        .btn { padding: 5px 10px; margin: 2px; text-decoration: none; border-radius: 5px; }
        .approve { background-color: green; color: white; }
        .delete { background-color: red; color: white; }
    </style>
</head>
<body>
    <h1><strong>Welcome Admin!</strong></h1>
    <h2>Pending Plants - Admin Approval Panel</h2>
    <table>
        <tr>
            <th>ID</th>
            <th>Common Name</th>
            <th>Scientific Name</th>
            <th>Image</th>
            <th>Actions</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()) { ?>
        <tr>
            <td><?= $row['id'] ?></td>
            <td><?= htmlspecialchars($row['common_plant']) ?></td>
            <td><?= htmlspecialchars($row['scientific_name']) ?></td>
            <td><img src="<?= htmlspecialchars($row['image']) ?>" width="100"></td>
            <td>
                <a class="btn approve" href="?approve=<?= $row['id'] ?>">Approve</a>
                <a class="btn delete" href="?delete=<?= $row['id'] ?>" onclick="return confirm('Delete this plant?');">Delete</a>
            </td>
        </tr>
        <?php } ?>
    </table>
</body>
</html>
