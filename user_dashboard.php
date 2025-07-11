<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$name = $_SESSION['name'];

$conn = new mysqli("localhost", "root", "", "plantcare");

$servername = "localhost";
$username = "root";
$password = "";
$database = "plantcare";

$conn = new mysqli($servername, $username, $password, $database);

$user_id = $_SESSION['user_id'];
$name = $_SESSION['name'];

// Add Plant logic
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['common_plant'])) {
    $common_plant = $_POST['common_plant'];
    $scientific_name = $_POST['scientific_name'];
    $watering = $_POST['watering'];
    $soil_needed = $_POST['soil_needed'];
    $poisonous = $_POST['poisonous'];

    // Image Upload
    $image_name = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $image_name = basename($_FILES['image']['name']);
        $target = "uploads/" . $image_name;
        move_uploaded_file($_FILES['image']['tmp_name'], $target);
    }

    $stmt = $conn->prepare("INSERT INTO pending_plants (user_id, common_plant, scientific_name, watering, soil_needed, poisonous, image, status) VALUES (?, ?, ?, ?, ?, ?, ?, 'pending')");
    $stmt->bind_param("issssss", $user_id, $common_plant, $scientific_name, $watering, $soil_needed, $poisonous, $image_name);
    $stmt->execute();
    $stmt->close();
}



// Delete Plant
if (isset($_GET['delete'])) {
    $delete_id = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM pending_plants WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $delete_id, $user_id);
    $stmt->execute();
    $stmt->close();
}


// Fetch User's Plants
$plants = $conn->query("SELECT * FROM pending_plants WHERE user_id = $user_id");

?>
<style>
    body{
        margin:0;
        padding: 0;
        background-color: black;
        color:white;
    }
        #navbar{
        display: flex;
        justify-content: space-between;
        background-color: rgba(0, 0, 0, 0.677);
    }
    #homeimg{
        height: 40px;
        width: 40px;
    }
    .nav-toggle{
        display: none;
    }
    #logo{
        width:50px;
        height: 50px;
        border-radius: 50px;
    }
    #navbar ul{
        display: flex;
        width: 700px;
        justify-content: space-between;
        list-style: none;
    }

    #navbar ul li a{
        text-decoration: none;
        font-size: large;
        color: white;
    }
    #navbar ul li a:hover{
        text-decoration: underline;
    }
    @media screen and (max-width:751px){
        #navbar ul{
            display: flex;
        }
    } 
    @media screen and (max-width:750px){
        .section-2{
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            width:350px;
            height: 300px;

        }
        .section-1{
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            width:350px;
            height: 300px;

        }
        #navbar ul{
            display: none;
            color:white;
        }
        .nav-toggle{
            display:flex;
            flex-direction: column;
        }
        span{
            width:200px;
            background-color: rgba(0, 0, 0, 0.711);
            position:relative;
            top:50%;
            right:15px;
        }
        #homeimg{
            position: absolute;
            top:1%;
            right:4%;
        }
    }
    .button-group {
    display: flex;
    gap: 10px;
    margin-top: 10px;
    justify-content: center;
    flex-wrap: wrap;
}

</style>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
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
<div class="nav-toggle">
    <img src="hicon.png" alt="Home" id="homeimg">
    <span></span>
</div>
</nav>

<script>
let a = document.getElementById('homeimg');
let n = document.querySelector('ul');
a.addEventListener('click',()=>{
if(n.style.display == 'none'){
    n.style.display = 'block';
    document.querySelector('span').append(n)
}else{
    n.style.display = 'none';
}
})
</script>  
</body>
</html>

<!DOCTYPE html>
<html>
<head>
    <title>User Dashboard</title>
    <link rel="stylesheet" href="basic.css">
   <link rel="stylesheet" href="plantcare.css">
    
</head>
<body>


    
   

<div class="container">

    <!-- Add Plant Form -->
    <div class="section">
         <section class="add-section">
        <div class="section-1">
            <h1><strong>Welcome, <?php echo htmlspecialchars($name); ?></strong></h1>
            <h1>Do you want to add plant information?</h1>
            <p>Let's do it to give some neccessary information about your plant</p>
            <a href="http://localhost/plantcare/addplant.php"><button class="joinbtn">Add Plant</button></a>
        </div>        
    </section>
    </div>

   


    <!-- View Plants Table -->
<?php
// Fetch pending and approved plants separately
$pendingPlants = $conn->query("SELECT * FROM pending_plants WHERE user_id = $user_id AND status = 'pending'");
$approvedPlants = $conn->query("SELECT * FROM plantdetails WHERE user_id = $user_id");
?>

<!-- Approved Plants Section -->
<?php
$approvedPlants = [];
$approvedQuery = "SELECT * FROM plantdetails WHERE user_id = $user_id ";
$approvedResult = mysqli_query($conn, $approvedQuery);

if ($approvedResult && mysqli_num_rows($approvedResult) > 0) {
    while ($row = mysqli_fetch_assoc($approvedResult)) {
        $approvedPlants[] = $row;
    }
}

?>


<h2 style="text-align: center;">Your Approved Plants</h2>
<div class="plantcards">
    <?php if (count($approvedPlants) > 0): ?>
        <?php foreach ($approvedPlants as $plant): ?>
            <div class="card1">
                <h3>Name: <i><?php echo htmlspecialchars($plant['common_plant']);?></i></h3>
                <div><strong>Scientific Name: </strong><i><?php echo htmlspecialchars($plant['scientific_name']); ?></i></div>
                <div><strong>Watering: </strong><i><?php echo htmlspecialchars($plant['watering']); ?></i></div>
                <div><strong>Sunlight: </strong><i><?php echo htmlspecialchars($plant['sunlight']); ?></i></div>
                <div><strong>Poisonous: </strong><i><?php echo htmlspecialchars($plant['poisonous']); ?></i></div>
                <div><strong>Soil Needed: </strong><i><?php echo htmlspecialchars($plant['soil_needed']); ?></i></div>
                <img src="<?php echo htmlspecialchars($plant['image']); ?>" alt="image"  style="">
               
               <!-- Buttons wrapper -->
    <div class="button-group">
        <button class="joinbtn">
            <a href="reminders.php?plant_id=<?= $plant['plant_id'] ?>" style="color: white; text-decoration: none;">Reminder</a>
        </button>

        <a href="edit_plant.php?plant_id=<?= $plant['plant_id'] ?>" style="text-decoration: none;">
            <button class="joinbtn" style="color: white;">Edit</button>
        </a>

        <form action="delete_plant.php" method="POST" onsubmit="return confirm('Are you sure you want to delete this plant?');" style="display: inline;">
    <input type="hidden" name="delete_id" value="<?= $plant['plant_id'] ?>">
    <button type="submit" class="joinbtn" style="margin-left: 5px;">Delete</button>
</form>

    </div>
    
        
            </div>

                

           
         <?php endforeach; ?>
        <?php else: ?>
            <p>No approved plants  to display.</p>
        <?php endif; ?>
</div>

<!-- Pending Plants Section -->

<?php
// Connect to database
$servername = 'localhost';
$username = 'root';
$password = "";
$database = "plantcare"; 
$conn = mysqli_connect($servername, $username, $password, $database);
$plants = [];
if (!$conn) {
    die('Cannot connect to the database');
}

// 🌿 Handle deletion
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_id'])) {
    $delete_id = $_POST['delete_id'];
    $deleteQuery = "DELETE FROM pending_plants WHERE id = $delete_id";
    mysqli_query($conn, $deleteQuery);
}

// 🌿 Search box (if any)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['searchbox']) && !empty($_POST['searchbox'])) {
    $name = $_POST['searchbox'];
    $un = "SELECT * FROM `pending_plants` WHERE `common_plant` = '$name'";
    $result1 = mysqli_query($conn, $un);
    
    if ($result1 && mysqli_num_rows($result1) > 0) {
        $searchResult = mysqli_fetch_assoc($result1);
    } else {
        $searchResult = "No results found for '$name'.";
    }
}

// 🌿 Fetch all pending plants
$plantsQuery = "SELECT * FROM `pending_plants`";
$plantsResult = mysqli_query($conn, $plantsQuery);

if ($plantsResult && mysqli_num_rows($plantsResult) > 0) {
    while ($row = mysqli_fetch_assoc($plantsResult)) {
        $plants[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title></title>
    <link rel="stylesheet" href="basic.css">
    <link rel="stylesheet" href="plantcare.css">
</head>
<body>
<div>


 <h2 style="text-align: center;">Your Pending Plants</h2>

   <div class="plantcards">
        <?php if (count($plants) > 0): ?>
            <?php foreach ($plants as $plant): ?>
                <div class="card1">
                    
                      <h3>Name: <i><?php echo htmlspecialchars($plant['common_plant']);?></i> </h3>
                     <div><strong>Scientific Name: </strong><i> <?php echo htmlspecialchars($plant['scientific_name']); ?></i></div>
                     <div><strong>Watering: </strong> <i><?php echo htmlspecialchars($plant['watering']); ?></i></div>
                     <div><strong>Sunlight: </strong><i> <?php echo htmlspecialchars($plant['sunlight']); ?></i></div>
                     <div><strong>Poisonous: </strong> <i><?php echo htmlspecialchars($plant['poisonous']); ?></i></div>
                     <div><strong>Soil Needed: </strong><i> <?php echo htmlspecialchars($plant['soil_needed']); ?></i></div>
                     <img src="<?php echo htmlspecialchars($plant['image']); ?>" alt="image"  style="">
                     
                     <form method="POST" onsubmit="return confirm('Are you sure you want to delete this plant?');">
                    <input type="hidden" name="delete_id" value="<?php echo $plant['id']; ?>">
                    <button button class="joinbtn" type="submit" style="">Delete</button>
                </form>
            </div>

                </div>
                
            <?php endforeach; ?>
        <?php else: ?>
            <p>No other plants available to display.</p>
        <?php endif; ?>
    </div>

   
    
    <script>
        let a = document.getElementById('homeimg');
        let n = document.querySelector('ul');
        a.addEventListener('click', () => {
            if (n.style.display == 'none') {
                n.style.display = 'block';
                document.querySelector('span').append(n);
            } else {
                n.style.display = 'none';
            }
        });
    </script>
    


</div>


</body>
</html>
