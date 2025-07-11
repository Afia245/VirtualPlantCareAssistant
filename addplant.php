<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    die("User not logged in");
}
$user_id = $_SESSION['user_id'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Plant</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            background-color: ;
            color: white;
        }
        #navbar {
            display: flex;
            justify-content: space-between;
            background-color: rgba(0, 0, 0, 0.677);
        }
        #homeimg {
            height: 40px;
            width: 40px;
        }
        .nav-toggle {
            display: none;
        }
        #logo {
            width: 50px;
            height: 50px;
            border-radius: 50px;
        }
        #navbar ul {
            display: flex;
            width: 700px;
            justify-content: space-between;
            list-style: none;
        }
        #navbar ul li a {
            text-decoration: none;
            font-size: large;
            color: white;
        }
        #navbar ul li a:hover {
            text-decoration: underline;
        }
        @media screen and (max-width:750px) {
            .section-1, .section-2 {
                display: flex;
                flex-direction: column;
                justify-content: center;
                align-items: center;
                width: 350px;
                height: 300px;
            }
            #navbar ul {
                display: none;
                color: white;
            }
            .nav-toggle {
                display: flex;
                flex-direction: column;
            }
            span {
                width: 200px;
                background-color: rgba(0, 0, 0, 0.711);
                position: relative;
                top: 50%;
                right: 15px;
            }
            #homeimg {
                position: absolute;
                top: 1%;
                right: 4%;
            }
        }
        body{
            color:green;
        }
        h2 {
            color: black;
            font-size:30px;
        }
        .add-container {
            width: 600px;
            height: 700px;
            border: 6px solid green;
            display: block;
            margin: 55px auto 0 auto;
            text-align: center;
            border-radius: 12px;
            background-color: ;
        }
        form {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }
        input {
            width:350px;
            height: 30px;
            margin: 10px;
            text-align: center;
            border-radius: 12px;
            color:green;
            border: 2px solid;
        }
        .addbtn {
            width: 120px;
            background-color: black;
            height: 40px;
            color: white;
            font-size: medium;
            border-radius: 10px;
            border: 2px solid white;
        }
        .addbtn:hover {
            background-color: rgb(9, 158, 24);
        }
        body::before {
            content: "";
            position: absolute;
            background: url(plantcare.jpg) no-repeat center center/cover;
            top: 0;
            left: 0;
            height: 100%;
            width: 100%;
            z-index: -1;
            opacity: 0.9;
        }
    </style>
</head>
<body>
<nav id="navbar">
    <img src="logo.jpg" alt="Logo" id="logo">
    <ul>
        <li><a href="home.php">Home</a></li>
        <li><a href="plantcare.php">Plant Info</a></li>
        <li><a href="reminders.php">Reminders</a></li>
        <li><a href="http://localhost/plantcare/login.php">User Login</a></li>
        <li><a href="http://localhost/plantcare/adminlogin.php">Admin Panel</a></li>
        <li><a href="logout.php">Log out</a></li>
    </ul>
    <div class="nav-toggle">
        <img src="hicon.png" alt="Home" id="homeimg">
        <span></span>
    </div>
</nav>

<div class="add-container">   
    <h2>Add New Plant</h2>
    <form action="" method="POST" enctype="multipart/form-data">
        <input type="text" name="common_plant" placeholder="Common Plant Name" required>
        <input type="text" name="scientific_name" placeholder="Scientific Name" required>
        <input type="text" name="watering" placeholder="Watering Info" required>
        <input type="text" name="sunlight" placeholder="Sunlight Info" required>
        <input type="text" name="poisonous" placeholder="Poisonous (Yes/No)" required>
        <input type="text" name="soil_needed" placeholder="Soil Needed" required>
        
       <input type="date" id="last_watered" name="last_watered">
 
       <input type="number" id="watering_frequency" name="watering_frequency" min="1" value="7">

        <label style="margin-top:10px;">Upload Image:</label>
        <input type="file" name="image" accept="image/*" required>
        <input class="addbtn" type="submit" value="Add Plant">
    </form>
</div>  

<?php
//session_start(); // Session start kortei hobe

// Check login
if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('Please login first.'); window.location.href='login.php';</script>";
    exit();
}
$user_id = $_SESSION['user_id']; // Set user_id

$servername = "localhost";
$username = "root";
$password = "";
$database = "plantcare";
$conn = mysqli_connect($servername, $username, $password, $database);

if (!$conn) {
    die('Cannot connect to the database');
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $common = $_POST['common_plant'];
    $scientific = $_POST['scientific_name'];
    $watering = $_POST['watering'];
    $sunlight = $_POST['sunlight'];
    $poisonous = $_POST['poisonous'];
    $soil = $_POST['soil_needed'];
    $last_watered = $_POST['last_watered'];
    $watering_frequency = $_POST['watering_frequency'];
    
    $image = $_FILES['image']['name'];
    $imageTmp = $_FILES['image']['tmp_name'];
    $imagePath = "uploads/" . basename($image);

    if (move_uploaded_file($imageTmp, $imagePath)) {
        $status = 'pending';

        // ✅ Correct SQL with proper comma and bind values
        $sql = "INSERT INTO pending_plants (user_id, common_plant, scientific_name, watering, sunlight, poisonous, soil_needed, last_watered, watering_frequency, image)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("isssssssss", $user_id, $common, $scientific, $watering, $sunlight, $poisonous, $soil, $last_watered, $watering_frequency, $imagePath);

        if ($stmt->execute()) {
            echo "<script>alert('Plant added successfully and is pending admin approval.'); window.location.href='user_dashboard.php';</script>";
        } else {
            echo "Error executing query: " . $stmt->error;
        }
    } else {
        echo "Failed to upload image.";
    }
}
?>



</body>
</html>
