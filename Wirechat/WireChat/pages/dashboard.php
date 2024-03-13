<?php
// error_reporting(0);
session_start();
$username= !empty($_SESSION['Username']) ? $_SESSION['Username'] : '';
if(empty($username)){
    header("location:../index.php");
}

include("../scripts/clean_input.php");

$server = "Default";
if (isset($_GET['server']))
{
    $server = legal_input($_GET['server']);
}
$_SESSION['server'] = $server;
$username = legal_input($username);

function RandomString($length = 10) {
	$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	$charactersLength = strlen($characters);
	$randomString = '';
	for ($i = 0; $i < $length; $i++) {
		$randomString .= $characters[rand(0, $charactersLength - 1)];
	}
	return $randomString;
}

include("../scripts/database.php");

if (!empty($_POST)){
    if (isset($_POST['room_name'])){
        $room_name = legal_input($_POST['room_name']);
        if (strlen($room_name) > 22){
            return;
        }
        $room_code = RandomString(6);
    
        $stmt = $conn->prepare("INSERT INTO Servers (Name, Code, Owner) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $room_name, $room_code, $username);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            $file = fopen("log_".$room_name.".html", "w");
            $stmt1 = $conn->prepare("SELECT * FROM Users WHERE Username=? LIMIT 1");
            $stmt1->bind_param("s", $username);
            $stmt1->execute();
            $res1 = $stmt1->get_result();
            $data = $res1->fetch_assoc();
            $servers = $data['Servers'];
            $servers = $servers . "|" . $room_name;
            $stmt2 = $conn->prepare("UPDATE Users SET Servers=? WHERE Username=?");
            $stmt2->bind_param("ss", $servers, $username);
            $stmt2->execute();

            header("location:dashboard.php?server=".$room_name);
        }
    }

    if (isset($_POST['room_code'])){
        $room_code = legal_input($_POST['room_code']);
        $stmt = $conn->prepare("SELECT * FROM Servers WHERE Code=?");
        $stmt->bind_param("s", $room_code);
        $stmt->execute();
        $res = $stmt->get_result();
        if ($stmt->affected_rows > 0){
            $data = $res->fetch_assoc();
            $room_name = $data['Name'];

            $stmt = $conn->prepare("SELECT Servers FROM Users WHERE Username=?");
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $res = $stmt->get_result();
            $data = $res->fetch_assoc();
            $servers = $data['Servers'];
            
            if (strpos($servers, $room_name) !== false) {
                header("location:dashboard.php?server=".$room_name);
            } else {
                $servers = $servers . "|" . $room_name;
                $stmt = $conn->prepare("UPDATE Users SET Servers=? WHERE Username=?");
                $stmt->bind_param("ss", $servers, $username);
                $stmt->execute();
                header("location:dashboard.php?server=".$room_name);
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <title>Bobbyhook</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!--bootstrap4 library linked-->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <link rel="stylesheet" href="css/style.css">

</head>
<body>

    <?php include("../scripts/navbar.php"); ?>

    <div class="container">

    <?php
        $path = "../img";
        include("../scripts/main_logo.php");
    ?>
        <div class="row">
            <div class="col-sm-4">
                <div class="dropdown">
                    <button class="btn btn-primary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <!-- Select Server -->
                        <?php echo "Server: " . $server; ?>
                    </button>
                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                        <?php
                            // Fetch servers from database

                            $stmt = $conn->prepare("SELECT * FROM Users WHERE Username=?");
                            $stmt->bind_param("s", $username);
                            $stmt->execute();
                            $result = $stmt->get_result();
                            $row = mysqli_fetch_assoc($result);
                            $servers = explode("|", $row['Servers']);
                            
                            foreach($servers as $server)
                            {
                                echo "<a class='dropdown-item' href='dashboard.php?server=".$server."'>$server</a>";
                            }
                        ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Bobbyhook</h5>
                        <p class="card-text">Enter a name for your chat room<br><br></p>
                        <form action="dashboard.php" method="post">
                            <div class="form-group">
                                <input autocomplete="off" type="text" id="room_name" class="form-control" name="room_name" placeholder="Enter room name">
                            </div>
                            <!--<a href="chat/create_chatroom.php" id="create_chatroom" class="btn btn-primary">Create Chat</a>-->
                            <!-- <a id="create_chatroom" class="btn btn-primary">Create Chat</a> -->
                            <button type="submit" class="btn btn-primary">Create Chatroom</button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-sm-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Join a chat room</h5>
                        <p class="card-text">Join a chat room and start chatting with your friends.</p>
                        <form action="dashboard.php" method="post">
                            <div class="form-group">
                                <input autocomplete="off" type="text" id="room_code" class="form-control" name="room_code" placeholder="Enter room code">
                            </div>
                            <button type="submit" class="btn btn-primary">Join Chatroom</button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-sm-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Settings</h5>
                        <p class="card-text">Change user settings, manage chatrooms and much more.<br><br><br></p>
                        <a href="settings.php" class="btn btn-primary">Settings</a>
                    </div>
                </div>
            </div>
            <div class="col-sm-12" style="padding-bottom: 5%">
                <?php
                    include("../chat/chat.php");
                ?>
            </div>
            <?php include("../scripts/footer.php"); ?>
        </div>
    </div>

    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script type="text/javascript">
        $(document).ready(function(){
            if ( window.history.replaceState ) {
                window.history.replaceState( null, null, window.location.href );
            }

            $("#create_chatroom").click(function(){
                var room_name = $("#room_name").val();
                $.post("dashboard.php", {room_name: room_name});
            });
        });
    </script>
</body>
</html>
