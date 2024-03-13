<?php
error_reporting(0);
session_start();
include('../scripts/clean_input.php');
$username= !empty($_SESSION['Username']) ? $_SESSION['Username'] : '';
$page = isset($_GET['page']) ? $_GET['page'] : "security";
$page = legal_input($page);
$username = legal_input($username);

if(empty($username))
    header("location:../index.php");

include('../scripts/database.php');
function RandomString($length = 10) {
	$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	$charactersLength = strlen($characters);
	$randomString = '';
	for ($i = 0; $i < $length; $i++) {
		$randomString .= $characters[rand(0, $charactersLength - 1)];
	}
	return $randomString;
}

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

if (isset($_GET['code']) && isset($_GET['action'])){
    $code = legal_input($_GET['code']);
    $action = legal_input($_GET['action']);

    $stmt = $conn->prepare("SELECT * FROM Servers WHERE Code=?");
    $stmt->bind_param("s", $code);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0){
        $data = $result->fetch_assoc();
        $room_name = $data['Name'];
        $room_owner = $data['Owner'];

        if ($room_owner == $username){
            if ($action == "delete"){
                $stmt1 = $conn->prepare("DELETE FROM Servers WHERE Code=?");
                $stmt1->bind_param("s", $code);
                $stmt1->execute();
                $res1 = $stmt1->get_result();
                if ($stmt1->affected_rows > 0){
                    $sql = "SELECT * FROM Users";
                    $res2 = $conn->query($sql);
                    while ($data_select = $res2->fetch_assoc()){
                        $servers = $data_select['Servers'];
                        $servers = explode("|", $servers);
                        $new_servers = "";
                        foreach ($servers as $server){
                            if ($server != $room_name){
                                $new_servers = $new_servers . "|" . $server;
                            }
                        }

                        $new_servers = substr($new_servers, 1);
                        $stmt = $conn->prepare("UPDATE Users SET Servers=? WHERE Username=?");
                        $stmt->bind_param("ss", $new_servers, $data_select['Username']);
                        $stmt->execute();
                    }

                    unlink("log_".$room_name.".html");
                    header("location:settings.php?page=manageservers");
                }
            }
        }
    }
}

// $bio_error new_bio
if (isset($_POST['new_bio'])){
    $new_bio = legal_input($_POST['new_bio']);
    $stmt = $conn->prepare("UPDATE Users SET About=? WHERE Username=?");
    $stmt->bind_param("ss", $new_bio, $username);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($res){
        header("location:settings.php?page=".$page);
    }
    else{
        $bio_error = "Error updating bio";
    }
}

if (isset($_FILES["newpfpimage"])){
    $pfp_dir = "../assets/pfp/";
    $target_pfp_file = $pfp_dir . basename($_FILES["newpfpimage"]["name"]);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_pfp_file,PATHINFO_EXTENSION));

    // ==== PFP SECTION ====
    if(!empty($_FILES["newpfpimage"]["tmp_name"])) {
    $check = getimagesize($_FILES["newpfpimage"]["tmp_name"]);
    if ($check !== false) {
        $uploadOk = 1;
    } else {
        echo "<div class='card-body' style='padding: 0.5rem;'><h5 style='color: red;'>File is not an image. ❌</h5></div>";
        $uploadOk = 0;
    }
    }
    else {
    echo "<div class='card-body' style='padding: 0.5rem;'><h5 style='color: red;'>Please select an image first. ❌</h5></div>";
    $uploadOk = 0;
    }

    redo:
    if (file_exists($target_pfp_file)) {
        $rand = rand(1,1000);
        $target_pfp_file = $pfp_dir . $rand . basename($_FILES["newpfpimage"]["name"]);
        goto redo;
    }

    if ($_FILES["newpfpimage"]["size"] > 5000000) {
        echo "<div class='card-body' style='padding: 0.5rem;'><h5 style='color: red;'>Sorry, your file is too large. (Max 5 MB) ❌</h5></div>";
        $uploadOk = 0;
    }

    if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
    echo "<div class='card-body' style='padding: 0.5rem;'><h5 style='color: red;'>Sorry, only JPG, JPEG, PNG & GIF files are allowed. ❌</h5></div>";
    $uploadOk = 0;
    }

    if ($uploadOk == 0) {
    echo "<div class='card-body' style='padding: 0.5rem;'><h5 style='color: red;'>Sorry, your file was not uploaded. ❌</h5></div>";
    } else {
        if (move_uploaded_file($_FILES["newpfpimage"]["tmp_name"], $target_pfp_file)) {
            echo "<div class='card-body' style='padding: 0.5rem;'><h5 style='color: green;'>The file ". htmlspecialchars( basename( $_FILES["newpfpimage"]["name"])). " has been uploaded. ✔</h5></div>";

            $stmt = $conn->prepare("SELECT ProfilePicture FROM Users WHERE Username=?");
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = mysqli_fetch_assoc($result);
            $oldpfpurl = $row['ProfilePicture'];
            if ($oldpfpurl != "assets/pfp/default.png") {
                unlink($oldpfpurl);
            }

            $pfpurl = substr($target_pfp_file, 3);
            $stmt = $conn->prepare("UPDATE Users SET ProfilePicture=? WHERE Username=?");
            $stmt->bind_param("ss", $pfpurl, $username);
            $stmt->execute();
            $res = $stmt->get_result();
            if (!(mysqli_affected_rows($stmt) > 0)) {
                echo "<div class='card-body' style='padding: 0.5rem;'><h5 style='color: red;'>Error updating profile picture. ❌</h5></div>";
            }
        } else {
            echo($target_pfp_file . " " . $_FILES["newpfpimage"]["tmp_name"]);
            echo "<div class='card-body' style='padding: 0.5rem;'><h5 style='color: red;'>Sorry, there was an error uploading your file. ❌ </h5></div>";
        }
    }
}
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
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css"></link>

</head>
<body>

<?php
    include("../scripts/navbar.php");
?>

<!-- Create container on the left side for settings menu -->
<div class="container-fluid" style="padding: 2rem">
    <div class="row">
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Settings</h5>
                    <div class="list-group" style="padding-bottom: 25%">
                        <a class="list-group-item list-group-item-action active rounded-top"><b>User Settings</b></a>
                        <a href="settings.php?page=security" <?php echo($page == "security" ? "style='color: dodgerblue'" : ""); ?> class="list-group-item list-group-item-action">Security</a>
                        <a href="settings.php?page=editprofile" <?php echo($page == "editprofile" ? "style='color: dodgerblue'" : ""); ?> class="list-group-item list-group-item-action">Edit Profile</a>
                        <a href="settings.php?page=manageinvites" <?php echo($page == "manageinvites" ? "style='color: dodgerblue'" : ""); ?> class="list-group-item list-group-item-action">Manage Invites</a>
                        
                        <hr class="hr hr-blurry"/>
                        <a class="list-group-item list-group-item-action active rounded-top"><b>Server Settings</b></a>
                        <a href="settings.php?page=manageservers" <?php echo($page == "manageservers" ? "style='color: dodgerblue'" : ""); ?> class="list-group-item list-group-item-action">Manage Servers</a>
                        <a href="settings.php?page=serversettings" <?php echo($page == "serversettings" ? "style='color: dodgerblue'" : ""); ?> class="list-group-item list-group-item-action">Create & Join Servers</a>
                    
                        <hr class="hr hr-blurry"/>
                        <a class="list-group-item list-group-item-action active rounded-top"><b>Other</b></a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Create container on the right side for settings page -->
        <div class="col-md-9">
            <div class="card">
                <div class="card-body">
                <?php
            if ($page == "security"){ ?>
                <h5 class="card-title">Change Password</h5>
                <p class="card-text">
                    <form action="changeprofilepicture.php" method="post" enctype="multipart/form-data">
                        <div class="form-group">
                            <input autocomplete="off" type="text" id="password" class="form-control" name="password" placeholder="Password">
                        </div>
                        <div class="form-group">
                            <input autocomplete="off" type="text" id="new_password" class="form-control" name="new_password" placeholder="New password">
                        </div>
                        <div class="form-group">
                            <input autocomplete="off" type="text" id="confirm_new_password" class="form-control" name="confirm_new_password" placeholder="Confirm new password">
                        </div>
                        <button type="submit" class="btn btn-primary">Change Password</button>
                    </form>
                </p> <?php
            }
            elseif ($page == "editprofile"){ ?>
                <h5 class="card-title">Change Profile Picture</h5>
                <p class="card-text">
                    <form action="settings.php" method="post" enctype="multipart/form-data">
                        <div class="form-group">
                            <label for="profilepicture">Profile Picture</label>
                            <input autocomplete="off" type="file" class="form-control-file" id="profilepicture" name="newpfpimage">
                        </div>
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </form>
                </p>
                <br>
                <h5 class="card-title">Change Profile Information</h5>
                <p class="card-text">
                    <form action="settings.php" method="post" enctype="multipart/form-data">
                        <div class="form-group">
                            <textarea class="form-control" id="new_bio" name="new_bio" rows="4" placeholder="About me"></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Update</button>
                        <p class="text-danger">
                            <?php echo($bio_error ? $bio_error : ""); ?>
                        </p>
                    </form>
                </p>

                <?php
            }
            elseif ($page == "manageservers"){
                $stmt = $conn->prepare("SELECT * FROM Users WHERE Username = ?");
                $stmt->bind_param("s", $_SESSION["Username"]);
                $stmt->execute();
                $result = $stmt->get_result();
                if($result)
                {
                    while($data = $result->fetch_assoc()) { 
                        $servers = explode("|", $data['Servers']);
                    }
                }?>
                <div class="row">
                    <div class="col">
                        <h4>Manage Servers</h4>
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <div class="table-responsive">
                            <div class="table-data">
                                <table class="table">
                                    <tr>
                                        <th>Name</th>
                                        <th>Code</th>
                                        <th>Owner</th>
                                        <th>Delete</th>
                                    </tr> <?php
                        foreach($servers as $server){ ?>
                            <tr>
                                <td><?php echo $server; ?></td> <?php
                                $stmt = $conn->prepare("SELECT * FROM Servers WHERE Name = ?");
                                $stmt->bind_param("s", $server);
                                $stmt->execute();
                                $result = $stmt->get_result();
                                if($result->num_rows>0)
                                {
                                    while($data = $result->fetch_assoc()) { 
                                        $code = $data['Code'];
                                        $owner = $data['Owner'];
                                    }
                                } ?>
                                <td><input autocomplete="off" readonly class="form-control" style="width: 100%" value="<?php echo $code; ?>"></td>
                                <td><input autocomplete="off" readonly class="form-control" style="width: 100%" value="<?php echo $owner; ?>"></td>

                                </a></td>
                                <?php
                                if ($server != "Default"){
                                    ?>
                                    <td><a
                                        href='<?php echo($owner == $username ? "settings.php?page=3&code=".$code."&action=delete" : "") ?>'
                                        class="text-danger delete">
                                        <i
                                            class='<?php echo($owner == $username ? "far fa-trash-alt" : "") ?>'>
                                        </i>
                                    </a></td>
                                    <?php
                                }
                                ?>
                            </tr>
                            <?php
                        } ?>
                        </table>
                    </div>
                </div>
                </div>
                </div>
            </div>
        </div>
            <?php
            }
            else if ($page == "manageinvites"){ ?>
                <div class="row">
                    <div class="col">
                        <h4>Manage Invites</h4>
                    </div>
                </div>
                <div class="row">
                <div class="col">
                <div class="table-responsive">
                <div class="table-data">
                    <table class="table">
                        <tr>
                            <th>Code</th>
                            <th>Registered User</th>
                            <th>Infinite</th>
                            <th>Valid</th>
                        </tr> <?php
                        $stmt = $conn->prepare("SELECT * FROM Invites WHERE Inviter = ?");
                        $stmt->bind_param("s", $username);
                        $stmt->execute();
                        $result = $stmt->get_result();
                        if($result->num_rows > 0)
                        {
                            while($invites_data = $result->fetch_assoc()) {
                                if (!empty($invites_data['RegisteredUser'])){
                                    $stmt = $conn->prepare("SELECT * FROM Users WHERE Username = ?");
                                    $stmt->bind_param("s", $invites_data['RegisteredUser']);
                                    $stmt->execute();
                                    $sequel_result = $stmt->get_result();
                                    if($sequel_result->num_rows > 0) {
                                        while($sequel_data = $sequel_result->fetch_assoc()) {
                                            $registered_user = '<a href="user.php?uid='.$sequel_data['Id'].'" class="text-primary">'.$invites_data['RegisteredUser'].'</a>';
                                        }
                                    }
                                    else {
                                        $registered_user = "Unknown";
                                    }  
                                }
                                else {
                                    $registered_user = "-";
                                } ?>
                                <tr>
                                    <td><input autocomplete="off" readonly class="form-control" style="width: 100%" value="<?php echo $invites_data['Code']; ?>"></td>
                                    <td><?php echo($registered_user); ?></td>
                                    <td><?php echo $invites_data['Infinite'] == 1 ? "Yes ✔": "No ❌"; ?></td>
                                    <td><?php echo $invites_data['Valid'] == 1 ? "Yes ✔": "No ❌"; ?></td>
                                </tr>
                                <?php
                            } ?>
                            </tbody>
                            </table> <?php
                        }
                        else {
                            echo "You have no invites";
                        } ?>
                </div>
                </div>
                </div>
                </div> <?php                                                
            }
            else if($page == "serversettings"){
                ?>
                <div class="col-sm-6">
                <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Create Chatroom</h5>
                    <p class="card-text">Enter a name for your chat room<br></p>
                    <form action="settings.php" method="post">
                        <div class="form-group">
                            <input autocomplete="off" type="text" id="room_name" class="form-control" name="room_name" placeholder="Room name">
                        </div>
                        <button type="submit" class="btn btn-primary">Create Chatroom</button>
                    </form>
                </div>
                </div>
                </div>
                <br>
                <div class="col-sm-6">
                <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Join chat room</h5>
                    <p class="card-text">Join a chat room and start chatting with your friends.</p>
                    <form action="settings.php" method="post">
                        <div class="form-group">
                            <input autocomplete="off" type="text" id="room_code" class="form-control" name="room_code" placeholder="Room code">
                        </div>
                        <button type="submit" class="btn btn-primary">Join Chatroom</button>
                    </form>
                </div>
                </div>
                </div>
            </div>
            <?php
            }
            ?>
            </div>
            </div>
        </div>
    </div>
</div>

    <?php include("../scripts/footer.php"); ?>

    <script type="text/javascript">
        $(document).ready(function(){
            if ( window.history.replaceState ) {
                window.history.replaceState( null, null, window.location.href );
            }
        });
    </script>
</body>
</html>
