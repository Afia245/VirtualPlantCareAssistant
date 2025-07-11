<?php
session_start();

$servername = 'localhost';
$username = 'root';
$password = '';
$database = 'plantcare';
$conn = mysqli_connect($servername, $username, $password, $database);

if (!$conn) {
    die('Cannot connect to the database');
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['uname'];
    $mail = $_POST['umail'];
    $upass = $_POST['upass'];
    $cpass = $_POST['cpass'];
    $role = 'user'; // default role

    // Check if username exists
    $uniquesql = "SELECT * FROM userdetails WHERE Name = '$username'";
    $uniquesqlresult = mysqli_query($conn, $uniquesql);
    $noofrows = mysqli_num_rows($uniquesqlresult);

    if ($noofrows > 0) {
        echo '<script>alert("Username is already taken");</script>';
    } elseif (!preg_match("/^[a-zA-Z0-9]{3,}$/", $username)) {
        echo '<script>alert("Username must be at least 4 characters long.");</script>';
    } elseif (!filter_var($mail, FILTER_VALIDATE_EMAIL)) {
        echo '<script>alert("Invalid email format.");</script>';
    } elseif (!preg_match("/^(?=.*[A-Za-z])(?=.*\d)(?=.*[!@#$%^&*]).{8,}$/", $upass)) {
        echo '<script>alert("Password must be at least 8 characters long, contain a letter, number, and special character.");</script>';
    } elseif ($upass !== $cpass) {
        echo '<script>alert("Your passwords do not match.");</script>';
    } else {
        $sql = "INSERT INTO userdetails (Name, Mail, user_pass, role) VALUES ('$username', '$mail', '$upass', '$role')";
        $result = mysqli_query($conn, $sql);

        if (!$result) {
            echo '<script>alert("Your form was not submitted.");</script>';
        } else {
            $_SESSION['username'] = $username;
            echo '<script>alert("Signup successful! Redirecting to dashboard.");</script>';
            echo '<script>window.location.href="login.php";</script>';
            exit();
        }
    }
}
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
           <li><a href="http://localhost/plantcare/login.php">User Login</a></li>
           <li><a href="http://localhost/plantcare/adminlogin.php">Admin Panel</a></li>
            <li><a href="http://localhost/plantcare/logout.php">Log out</a></li>
           
        </ul>
<div class="nav-toggle">
    <img src="hicon.png" alt="Home" id="homeimg">
    <span></span>
</div>
</nav>
<style>
        body{
            color:green;
        }
        .Signupcontainer{
            width:400px;
            height:370px;
            border:2px solid;
            display:block;
            margin-left:auto;
            margin-top:150px;
            margin-right: 100px;
            text-align: center;
            border-radius: 12px;
            border:6px solid ;
        }
        form{
            display:flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }
        input{
            width:350px;
            height: 30px;
            margin: 10px;
            text-align: center;
            border-radius: 12px;
            color:green;
            border: 2px solid;
        }
        body::before{
            content:"";
            position: absolute;
            background: url(screen.jpg)no-repeat center center/cover;
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
    <div class="Signupcontainer">
        <h1>Signup</h1>
        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
            <input type="text" name="uname" id="uname" placeholder="Enter user name">
            <input type="mail" name="umail" id="umail" placeholder="Enter user mail">
            <input type="password" name="upass" id="upass" placeholder="Enter password">
            <input type="password" name="cpass" id="cpass" placeholder="Confirm password">
            <input type="submit" value="Signup">
        </form>
    </div>
</body>
</html>


