<?php
error_reporting(0);
session_start();
$username= !empty($_SESSION['Username']) ? $_SESSION['Username'] : '';
if(empty($username))
    header("location:../index.php");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <title>WireChat</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!--bootstrap4 library linked-->
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"></script>

</head>
<body>

    <?php include("../scripts/navbar.php") ?>

    <div class="container">

        <?php
            $path = "../img";
            include("../scripts/main_logo.php");
        ?>

        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">About Us</h5>
                        <p class="card-text">
                            WireChat is a chat application that allows users to chat with each other.
                            It is available both as an executable file and in a web browser.

                            <br><br>
                            The purpose of this project was for me to both learn PHP and get a better understanding of backend development in general,
                            however is started out as a project for my CS class where I then later on was inspired to also create a web version for it.
                            <br><br>
                            The web project is still in development and is not yet fully functional, however the downloadable application is fully functional.
                            <br>WireChat allows invited users to chat with each other, either in a public main room, but it also allows users to create their own chat rooms.
                            <br>The chat rooms can be private or public, this fully depends on who you give the code out to, however this feature is not yet implemented in the web version.


                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <?php include("../scripts/footer.php"); ?>
</body>
</html>