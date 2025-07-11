<?php
   



?> 

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Plant Care Assistant</title>
    <link rel="stylesheet" href="basic.css">
   
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
    <section class="primary-section">
        <div class="section-1">
            <h1>Plant Information And Care Tips</h1>
            <p>Are you curious about any plant and how to care for it? Look no further! Our website is your one-stop resource for all things plant care. Whether you’re looking to learn about specific plants, their care needs, or how to help them thrive, we’ve got you covered.</p>
            <a href="http://localhost/plantcare/plantcare.php"><button class="joinbtn">Click Here</button></a>
        </div>        
    </section>
    <section class="secondary-section">
        <div class="section-2">
            <h1>Your Ultimate Plant Care Guide! </h1>
            <p>Are you a plant lover but often forget when to water, fertilize, or repot your beloved greenery? We’re here to help! Our platform provides easy-to-use plant care reminders tailored specifically to your plants' needs. Whether you’re a seasoned gardener or a budding enthusiast, our goal is to ensure your plants thrive.</p>
            <a href="http://localhost/plantcare/reminders.php"><button class="joinbtn">Click Here</button></a>
        </div>        
    </section>
    
    <footer class="footer-section">
            <p>Copyright &#169 All rights are reserved</p>
        </div>
    </footer>
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
</body>c</html>`