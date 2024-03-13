<!DOCTYPE html>
<html lang="en">
<head>
  <title>WireChat - Admin</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"></script>
  <link rel="stylesheet" href="../css/style.css">

</head>
<body>
<?php
  session_start();
  $username = $_SESSION['Username'];
  include('../scripts/database.php');
  include('../scripts/clean_input.php');
  include("../scripts/navbar.php");

  $username = legal_input($username);
  if (empty($username)) {
    header("location:../index.php");
  }

  if (isset($_GET['uid'])) {
    $finduser = legal_input($_GET['uid']);
  }
  else{
    $stmt = $conn->prepare("SELECT * FROM Users WHERE Username=?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = $result->fetch_assoc();
    $finduser = legal_input($data['Id']);
  }

  if (isset($_GET['action'])){
    $action = legal_input($_GET['action']);
  }

  $stmt = $conn->prepare("SELECT * FROM Users WHERE Id=?");
  $stmt->bind_param("s", $finduser);
  $stmt->execute();
  $result = $stmt->get_result();
  if($result->num_rows > 0)
  {
    while($data = $result->fetch_assoc()) {
      if ($_SESSION["Rank"] == "Admin" && $action == "promote"){
        $stmt = $conn->prepare("UPDATE Users SET Rank='Admin' WHERE Id=?");
        $stmt->bind_param("s", $finduser);
        $stmt->execute();
        $result = $stmt->get_result();
        if($result){
          return true;
        } else{
            return "Error: " . $sql_update_query . "<br>" . $db->error;
        }
      }
  ?>
    <h1 class='text-center'></h1>
      <div class="center">
        <div class="card">
          <div class="card-body">
            <div class="col text-center">
            <h3 class="card-title"><?php echo $data['Username']; ?></h3>
            <img width="72px" height="72px" style="margin: 8px; border-radius: 50%; overflow: hidden;" src="<?php echo "../" . $data['ProfilePicture']; ?>"/>
            <p class="card-text">
              <b>UID:</b> <?php echo $data['Id']; ?> <br>
              <b>Registered:</b> <?php echo(date("d/M/Y", strtotime($data['RegistrationDate']))); ?> <br>
              <b>Invited by:</b> <?php

              $stmt = $conn->prepare("SELECT * FROM Users WHERE Username=?");
              $stmt->bind_param("s", $data['Inviter']);
              $stmt->execute();
              $result = $stmt->get_result();
              if($result->num_rows > 0)
              {
                while($inviter = $result->fetch_assoc()) {
                  echo("<td><a href='user.php?uid=" . $inviter['Id'] . "'> " . $data["Inviter"] . "</a></td>");
                }

              // echo($data['Inviter']);
              }
              ?> <br>
              <b>Last seen:</b> <?php
                $lastlogin = $data['LastSeen'];
                $now = date("Y-m-d H:i:s", time());
                $diff = abs(strtotime($now) - strtotime($lastlogin));
                $years = floor($diff / (365*60*60*24));
                $months = floor(($diff - $years * 365*60*60*24) / (30*60*60*24));
                $days = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));
                $hours = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24 - $days*60*60*24) / (60*60));
                $minutes = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24 - $days*60*60*24 - $hours*60*60)/ 60);
                $seconds = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24 - $days*60*60*24 - $hours*60*60 - $minutes*60));
                
                if      ($years > 0)    echo $years . " years and" . $months . " months ago";
                elseif  ($months > 0)   echo $months . " months and " . $days . " days ago";
                elseif  ($days > 0)     echo $days . " days and " . $hours . " hours ago";
                elseif  ($hours > 0)    echo $hours . " hours and " . $minutes . " minutes ago";
                elseif  ($minutes > 0)  echo $minutes . " minutes and " . $seconds . " seconds ago";
                else    echo "Just now ";

              ?> <br>
                <?php
                if ($_SESSION["Rank"] == "Admin") {
                    echo("<b>Email:</b> " . $data['Email'] . "<br>");
                    if ($data['Rank'] != "Admin"){
                      echo("<a href='user.php?uid=" . $data['Id'] . "&action=promote" . " ' class='btn btn-primary'>Promote to Admin</a>");
                    }
                  } ?>
                  <br><b>About</b> <br>
                  <?php echo $data['About']; ?>
            </p>
            <?php
            if ($_SESSION["Username"] == $data["Username"]){
              echo('<a href="settings.php?page=manageinvites"><button class="btn btn-primary" style="important; color: white;" class="btn btn-secondary"> Manage Invites</button></a>');
            }
            ?>
            <button onclick="window.history.go(-1); return false;" style="background-color: #049a2a!important; color: white;" class="btn btn-secondary"> Back</button><br><br>
          </div>
        </div>
      </div>
    <?php
    }
  } else {
    echo "User not found";
  }

  include("../scripts/footer.php");
?>
</body>
</html>