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

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Mark as watered
if (isset($_GET['watered'])) {
    $plant_id = intval($_GET['watered']);
    // Update last_watered to today
    $stmt = $conn->prepare("UPDATE plantdetails SET last_watered = CURDATE() WHERE plant_id = ? AND user_id = ?");
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }
    $stmt->bind_param("ii", $plant_id, $user_id);
    $stmt->execute();
    $stmt->close();

    header("Location: reminders.php?plant_id=$plant_id");
    exit();
}

// Get plant_id from URL to show reminder for that specific plant
$plant_id = isset($_GET['plant_id']) ? intval($_GET['plant_id']) : 0;
if ($plant_id === 0) {
    die("No plant selected.");
}

// Prepare statement to fetch plant data
$stmt = $conn->prepare("SELECT * FROM plantdetails WHERE plant_id = ? AND user_id = ?");
if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}
$stmt->bind_param("ii", $plant_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

$date_today = new DateTime();

// Handle manual reminder update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_reminder'])) {
    $plant_id = intval($_POST['plant_id']);
    $last_watered = $_POST['last_watered'];
    $watering_frequency = intval($_POST['watering_frequency']);

    $stmt = $conn->prepare("UPDATE plantdetails SET last_watered = ?, watering_frequency = ? WHERE plant_id = ? AND user_id = ?");
    $stmt->bind_param("siii", $last_watered, $watering_frequency, $plant_id, $user_id);
    $stmt->execute();
    $stmt->close();

    header("Location: reminders.php?plant_id=$plant_id");
    exit();
}


?>

<!DOCTYPE html>
<html>
<head>
    <title>Plant Reminder</title>
    <link rel="stylesheet" href="basic.css">
    <link rel="stylesheet" href="plantcare.css">
    <style>
        body {
            font-family: Arial;
            color: green;
            background: url('care.jpg') no-repeat center center/cover;
            background-attachment: fixed;
            margin: 0;
            padding: 0;
        }
        nav {
            background-color: rgba(0, 0, 0, 0.7);
            padding: 10px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        nav img {
            height: 50px;
            border-radius: 25px;
        }
        nav ul {
            list-style: none;
            display: flex;
            gap: 20px;
        }
        nav ul li a {
            color: white;
            text-decoration: none;
            font-size: large;
        }
        nav ul li a:hover {
            text-decoration: underline;
        }
        h2 {
            text-align: center;
            margin-top: 30px;
            color: #004d00;
        }
        table {
            border-collapse: collapse;
            width: 90%;
            margin: 30px auto;
            background: rgba(255, 255, 255, 0.9);
        }
        th, td {
            padding: 12px 15px;
            border: 1px solid #ccc;
            text-align: center;
        }
        th {
            background-color: #c8e6c9;
        }
        .button-link {
            background-color: green;
            color: white;
            padding: 6px 12px;
            text-decoration: none;
            border-radius: 5px;
        }
        .button-link:hover {
            background-color: darkgreen;
        }
    </style>
</head>
<body>

<nav id="navbar">

<img src="logo.jpg" alt="not loaded" id="logo">
<ul>
    <li><a href="http://localhost/plantcare/home.php">Home</a></li>
    <li><a href="http://localhost/plantcare/plantcare.php">Plant Info</a></li>
    <li><a href="http://localhost/plantcare/reminders.php">Reminders</a></li>
    <li><a href="user_dashboard.php">Dashboard</a></li>
    <li><a href="logout.php">Logout</a></li>
</ul>

</nav>

<h2>Reminder for Your Plant</h2>

<table>
    <thead>
        <tr>
            <th>Plant Name</th>
            <th>Last Watered</th>
            <th>Watering Frequency</th>
            <th>Next Watering Date</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
    <?php if ($result->num_rows === 1): 
        $plant = $result->fetch_assoc();
        $last_watered = $plant['last_watered'];
        $frequency = (int)$plant['watering_frequency'];
        $last_date = $last_watered ? new DateTime($last_watered) : null;
        $next_date = $last_date ? (clone $last_date)->modify("+$frequency days") : null;

        if (!$last_date || !$next_date) {
            $status = "No watering data";
        } elseif ($next_date <= $date_today) {
            $status = "<span style='color:red;'>Needs Watering Today!</span>";
        } else {
            $days_left = $date_today->diff($next_date)->days;
            $status = "<span style='color:green;'>Next in $days_left day(s)</span>";
        }
    ?>
        <tr>
            <td><?= htmlspecialchars($plant['common_plant']) ?></td>
            <td><?= $last_watered ?: 'N/A' ?></td>
            <td><?= $frequency ?> days</td>
            <td><?= $next_date ? $next_date->format('Y-m-d') : 'N/A' ?></td>
            <td><?= $status ?></td>
            <td>
                <?php if ($next_date && $next_date <= $date_today): ?>
                    <a class="button-link" href="reminders.php?watered=<?= $plant['plant_id'] ?>&plant_id=<?= $plant['plant_id'] ?>" onclick="return confirm('Mark as watered today?')">Mark as Watered</a>
                <?php else: ?>
                    ---
                <?php endif; ?>
            </td>
        </tr>
    <?php else: ?>
        <tr><td colspan="6">Plant not found or unauthorized access.</td></tr>
    <?php endif; ?>
    </tbody>
</table>
<?php if ($result->num_rows === 1): ?>
    <div style="width: 50%; margin: 20px auto; background: rgba(255,255,255,0.9); padding: 20px; border-radius: 10px;">
        <h3 style="text-align:center; color:#004d00;">Set New Reminder</h3>
        <form method="post" action="">
            <input type="hidden" name="plant_id" value="<?= $plant['plant_id'] ?>">

            <label for="last_watered">Last Watered Date:</label><br>
            <input type="date" name="last_watered" value="<?= $plant['last_watered'] ?>" required><br><br>

            <label for="watering_frequency">Watering Frequency (days):</label><br>
            <input type="number" name="watering_frequency" value="<?= $plant['watering_frequency'] ?>" min="1" required><br><br>

            <input type="submit" name="update_reminder" value="Update Reminder" class="button-link">
        </form>
    </div>
<?php endif; ?>


</body>
</html>
