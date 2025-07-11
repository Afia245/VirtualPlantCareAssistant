<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$servername = "localhost";
$username = "root";
$password = "";
$database = "plantcare";

$conn = new mysqli($servername, $username, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_id'])) {
    $delete_id = intval($_POST['delete_id']);
    $user_id = $_SESSION['user_id'];

    // Prepare and execute the delete query
    $stmt = $conn->prepare("DELETE FROM plantdetails WHERE plant_id = ? AND user_id = ?");
    if ($stmt) {
        $stmt->bind_param("ii", $delete_id, $user_id);
        $stmt->execute();
        $stmt->close();
    }

    // Redirect to user dashboard after deletion
    header("Location: user_dashboard.php");
    exit();
} else {
    echo "Invalid request.";
}
?>
