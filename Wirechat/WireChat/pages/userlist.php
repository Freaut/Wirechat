<!DOCTYPE html>
<html lang="en">
<head>
  <title>WireChat</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
  <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"></script>
  <link rel="stylesheet" href="../css/style.css">
</head>
<body>
  <?php
    error_reporting(0);
    session_start();
    include('../scripts/clean_input.php');
    $username = legal_input($_SESSION['Username']);
    if (empty($username)) {
      header("Location: ../index.php");
    }
    include('../scripts/navbar.php');
    include('../scripts/database.php');
  
    $stmt = $conn->prepare("SELECT * FROM Users WHERE Username=?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = $result->fetch_assoc();
    $rank = legal_input($data['Rank']);
    $_SESSION['Rank'] = $rank;

    if (empty($username)) {
      header("location:../index.php");
    }

    if (empty($rank)) {
      header("location:../index.php");
    }

    if (isset($_GET['generate'])){

      $stmt = $conn->prepare("SELECT * FROM Users WHERE Username=?");
      $stmt->bind_param("s", $username);
      $stmt->execute();
      $result = $stmt->get_result();
      $data = $result->fetch_assoc();
      if ($data['Rank'] != "Admin"){
        echo "<script>alert('You do not have permission to access this page.');</script>";
        echo "<script>window.location.href='../index.php';</script>";
        return;
      }

      $generateid = legal_input($_GET['id']);

      $code = RandomString(6) ."-". RandomString(6) ."-". RandomString(6) ."-". RandomString(6);
      $stmt = $conn->prepare("SELECT * FROM Users WHERE Id = ?");
      $stmt->bind_param("s", $generateid);
      $stmt->execute();
      $result = $stmt->get_result();
      $data = $result->fetch_assoc();
      $genusername = $data['Username'];

      $stmt = $conn->prepare("INSERT INTO Invites (Inviter, Code, Infinite, Valid) VALUES (?, ?, ?, ?)");
      $stmt->bind_param("ssii", $genusername, $code, 0, 1);
      $stmt->execute();
    }
    
    if (isset($_POST['search'])) {
      $search = legal_input($_POST['search']);
      $stmt = $conn->prepare("SELECT * FROM Users WHERE Username LIKE ? OR Id LIKE ?");
      $search = "%".$search."%";
      $stmt->bind_param("ss", $search, $search);
      $stmt->execute();
      $result = $stmt->get_result();
      $users = $result->fetch_all(MYSQLI_ASSOC);
    }
    else{
      $stmt = $conn->prepare("SELECT * FROM Users");
      $stmt->execute();
      $result = $stmt->get_result();
      $users = $result->fetch_all(MYSQLI_ASSOC);
    }

    ?>
    <div class="container">
      <?php
          $path = "../img";
          include("../scripts/main_logo.php");
      ?>

      <!-- Create a search box -->
      <div class="container">
        <div class="row">
          <div class="col-sm-5">
            <form action="userlist.php" method="post">
              <div class="input-group">
                <input autocomplete="off" id="search" type="text" class="form-control" placeholder="Search" name="search">
                <div class="input-group-append">
                  <button class="btn btn-outline-primary" type="submit">Search</button>
                </div>
              </div>
              <br>
            </form>
          </div>
        </div>
      </div>

      <div class='container'>
        <h2>Users</h2>
          <table class='table table-striped'>
          <thead>
          <tr>
            <th scope='col'>Uid</th>
            <th scope='col'>Username</th>
            <th scope='col'>Rank</th>
            <th scope='col'>Profile</th>
            <?php
              if ($rank == "Admin" || $rank == "Owner") {
                echo("<th scope='col'>Give Invite</th>");
              }
            ?>
          </tr>
        <tbody>
      </thead>
      <?php
      foreach ($users as $user) {
          echo("
              <tr>
              <td>" . $user['Id'] . "</td>
              <td>" . $user['Username'] . "</td>
              <td>" . $user['Rank'] . "</td>
              <td><a href='user.php?uid=" . $user['Id'] . "'>Profile</a></td>
          ");

          if ($rank == "Admin" || $rank == "Owner"){
            echo("<td><a href='userlist.php?generate=&id=".$user['Id']."'><input autocomplete='off' type='submit' value='Give invite' class='btn btn-success'></a></td>");
          }
          echo("</tr>");
        }
      ?>
      </tbody>
      </table>
      </div>
    </div>

    <?php include("../scripts/footer.php"); ?>
</body>
</html>
<?php

function RandomString($length = 10) {
	$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	$charactersLength = strlen($characters);
	$randomString = '';
	for ($i = 0; $i < $length; $i++) {
		$randomString .= $characters[rand(0, $charactersLength - 1)];
	}
	return $randomString;
}
?>