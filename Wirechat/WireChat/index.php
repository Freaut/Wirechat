<?php

session_start();
$username = !empty($_SESSION['Username']) ? $_SESSION['Username'] : '';
if(!empty($username))
    header("location:pages/dashboard.php");

include('scripts/database.php');
include('scripts/login.php');
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
  <link rel="stylesheet" href="css/style.css">

</head>
<body>

<div class="container-fluid">
 <div class="row">
   <div class="col-sm-4">
   </div>
   <div class="col-sm-4">
    <!--====registration form====-->
    <div class="registration-form">
      <h4 class="text-center">WireChat Login</h4>
      <p class="err-msg text-center"><?php echo $call_login; ?></p>

      <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post">
       
        <!--// Username//-->
        <div class="form-group">
            <label>Username:</label>
            <input type="text" class="form-control"  placeholder="Enter Username" name="username" value="<?php echo $set_username; ?>">
            <p class="err-msg">
            <?php if($usernameErr != 1){ echo $usernameErr; } ?>
            </p>
        </div>
        
        <!--//Password//-->
        <div class="form-group">
            <label>Password:</label>
            <input type="password" class="form-control"  placeholder="Enter Password" name="password">
            <p class="err-msg">
            <?php if($passErr != 1){ echo $passErr; } ?>
            </p>
        </div>

        <button type="submit" class="btn btn-success" name="login">Login</button>
        <a href="register.php" class="btn btn-primary" name="register">Register</a>
      </form>
    </div>
   </div>
   <div class="col-sm-4">
   </div>
 </div>
</div>
</body>
</html>