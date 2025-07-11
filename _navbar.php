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
    <li><a href="PlantCareAssistant/home.php">Home</a></li>
    <li><a href="http://localhost/PlantCareAssistant/plantcare.php">Plant Info</a></li>
    <li><a href="http://localhost/PlantCareAssistant/reminders.php">Reminders</a></li>
    <li><a href="http://localhost/plantcare/login.php">User Login</a></li>
        <li><a href="http://localhost/plantcare/adminlogin.php">Admin Panel</a></li>
    <li><a href="http://localhost/PlantCareAssistant/logout.php">Logout</a></li>
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