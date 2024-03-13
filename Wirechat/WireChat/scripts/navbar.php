<?php
session_start();
include("database.php");
$username = $_SESSION['Username'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <title>Bobbyhook - Admin</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!--custom style-->
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css"></link>
  
  <script src="../javascript/navbar.js"></script>
  <link rel="stylesheet" href="../css/style.css">

</head>
<body>

    <nav class="navbar navbar-expand-md navbar-dark sticky-top navbar-main">
        <div class="container-fluid">
            <a class="navbar-brand" href="dashboard.php"><img src="../img/bobby.jpg" alt="logo" style="width:40px;"> Bobbyhook</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarResponsive">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarResponsive">
            <ul class="navbar-nav hoverable">
                <li class="nav-item">
                    <a class="nav-link" href="dashboard.php">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="about.php">About</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="contact.php">Contact</a>
                </li>
                <?php
                    if ($_SESSION["Rank"] == "Admin"){
                        echo('
                        <li class="nav-item">
                        <a class="nav-link" href="admin.php">Admin</a>
                        </li>
                        ');
                    }
                    ?>
                <li class="nav-item">
                    <a class="nav-link" href="userlist.php">Users</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="user.php">Profile</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="settings.php"><i class="fa-solid fa-gear"></i></a>
                </li>
            </ul>
            <ul class="navbar-nav ml-auto hoverable">
                <li class="nav-item">
                    <a class="nav-link" href="../scripts/logout.php">Logout</a>
                </li>
            </ul>
            </div>
        </div>
    </nav>
</body>
</html>