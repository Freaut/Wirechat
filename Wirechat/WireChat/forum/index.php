<?php
// error_reporting(0);
session_start();
$username = $_SESSION['Username'];
if(empty($username)){
    header("location:../index.php");
}

include("../scripts/database.php");

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Bobbyhook</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css"></link>

    <script src="../javascript/navbar.js"></script>
    <link rel="stylesheet" href="../css/style.css">

</head>
<body>
    <div class="container sticky-top" style="padding-top: 1%">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title"><b style="color:white">Bobby</b><b style="color:lime">hook</b></h5>
                        <p class="card-text">...</p>
                    </div>
                </div>
            </div>
        </div>

        <?php
            $path = "../img";
            include("../scripts/main_logo.php");
        ?>
    </div>

    <div class="container" style="margin-top:1%">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-body">
                        <h6 class="card-text"><b style="">Announcements</b></h6>
                        <h6 class="card-text"><b style="">Discussions</b></h6>
                    </div>
                </div>
            </div>
        </div>

        <?php include("../scripts/footer.php"); ?>
    </div>

    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script type="text/javascript">
        $(document).ready(function(){
            if ( window.history.replaceState ) {
                window.history.replaceState( null, null, window.location.href );
            }
        });
    </script>
</body>
</html>
