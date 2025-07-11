<?php
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$database = "plantcare";

$conn = mysqli_connect($servername, $username, $password, $database);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
$error="";
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['uname']) && isset($_POST['upass'])) {
        $name = trim($_POST['uname']);
        $pass = trim($_POST['upass']);

        $stmt = $conn->prepare("SELECT * FROM userdetails WHERE  LOWER(Name) = LOWER(?)");

        $stmt->bind_param("s", $name);
        $stmt->execute();
        $result = $stmt->get_result();

        

        if ($result->num_rows == 1) {
            $row = $result->fetch_assoc();

            if ($pass == $row['user_pass']) { 
                $_SESSION['loggedin'] = true;
                $_SESSION['name'] = $row['Name'];
                $_SESSION['role'] = $row['role'];
                $_SESSION['user_id'] = $row['user_id'];

                if ($row['role'] === 'admin') {

                  header("Refresh: 1; URL= admin_dashboard.php");
                    exit();
                } else {
                   header("Refresh: 1; URL=user_dashboard.php");
                    exit();
                }
            } else {
                echo "<script>alert('Incorrect password! Please try again.');</script>";
            }
        } else {
            echo "<script>alert('No such user found! Please check your username.');</script>";
        }

        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            background-color: black;
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
        .logincontainer {
            width: 400px;
            height: 270px;
            border: 6px solid green;
            display: block;
            margin-left: auto;
            margin-top: 150px;
            margin-right: 100px;
            text-align: center;
            border-radius: 12px;
            color: green;
        }
        form {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }
        input {
            width: 250px;
            height: 30px;
            margin: 10px;
            text-align: center;
            border-radius: 12px;
            color: green;
            border: 2px solid green;
        }
        a {
            color: green;
        }
        body::before {
            content: "";
            position: absolute;
            background: url(screen.jpg) no-repeat center center/cover;
            top: 0;
            bottom: 0;
            height: 100%;
            width: 100%;
            z-index: -1;
            opacity: 0.9;
        }
    </style>
</head>
<body>

<nav id="navbar">
    <img src="logo.jpg" alt="not loaded" id="logo">
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

<div class="logincontainer">
    <h1>Login</h1>
    <form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
        <input type="text" name="uname" id="uname" placeholder="Enter user name" required>
        <input type="password" name="upass" id="upass" placeholder="Enter password" required>
       
        <input type="submit" value="Login">
       
         <a href="signup.php">Register</a>
    </form>
</div>

</body>
</html>

