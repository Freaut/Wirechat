<?php
session_start();
include('../scripts/database.php');
$username = $_SESSION['Username'];
$server = $_SESSION['server'] ? $_SESSION['server'] : "Default";

if(isset($username)){
    $text = $_POST['text'];
    $stmt = $conn->prepare("SELECT * FROM Users WHERE Username=?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $res = $stmt->get_result();
    if($res->num_rows>0)
    {
        while($data = $res->fetch_assoc()) { 
            $pfp = "../" . $data['ProfilePicture'];
            $uid = $data["Id"];
        }
    }

    $username_color = "rgb(232, 232, 227)";
    if ($_SESSION["Rank"] == "Admin" || $_SESSION["Rank"] == "Owner"){
        $username_color = "rgb(255, 102, 102)";
    }

    $forbidden_words = array("lgbtq", "lgbt", "they/them");
    foreach($forbidden_words as $word)
    {
        if(strpos(strtolower($text), $word) !== false)
        {
            $text = str_replace($word, str_repeat("*", strlen($word)), strtolower($text));
        }
    }

    if (str_starts_with(strtolower($text), "/clear") && $_SESSION["Rank"] == "Admin") {
        file_put_contents("../pages/log_".$server.".html", "<div class='msgln' style='color: green;'><span class='chat-time'>".date("g:i A")."</span><b class='user-name' style='color: green;'>[Server]</b>: Chat has been cleared by <img width='24px' height='24px' style='border-radius: 50%' src='".$pfp."'/><a style='color: green;font-weight: bold;' class='user-name' href='user.php?uid=".$uid."'> ".$username. "</a>.<br></div>");
        return;
    }
    else if (str_starts_with(strtolower($text), "/clear") && $_SESSION["Rank"] == "Owner") {
        file_put_contents("../pages/log_".$server.".html", "<div class='msgln' style='color: green;'><span class='chat-time'>".date("g:i A")."</span><b class='user-name' style='color: green;'>[Server]</b>: Chat has been cleared by <img width='24px' height='24px' style='border-radius: 50%' src='".$pfp."'/><a style='color: green;font-weight: bold;' class='user-name' href='user.php?uid=".$uid."'> ".$username. "</a>.<br></div>");
        return;
    }
    else if (str_starts_with(strtolower($text), '/roll')) {
        $num = rand(1, 6);
        if (isset($_SESSION['lastroll'])) {
            if ($_SESSION['lastroll'] == $num && $num == 1) {
                $text = "rolled a " . $num . "... snake eyes!";
            } else if ($_SESSION['lastroll'] == $num) {
                $text = "rolled a " . $num . "... double!";
            } else {
                $text = "rolled a " . $num . ".";
            }
        } else {
            $text = "rolled a " . $num . ".";
        }
        $_SESSION['lastroll'] = $num;

        $text_message = "<div class='msgln' style='color: green;'><span class='chat-time'>".date("g:i A")."</span> <b class='user-name'>[Server]</b>: <img width='24px' height='24px' style='border-radius: 50%' src='".$pfp."'/> <a style='color: green;font-weight: bold;' class='user-name' href='user.php?uid=".$uid."'>".$username." </a>".$text."<br></div>";
    }
    else {
        $text_message = "<div class='msgln' style='color: rgb(232, 232, 227);'><span class='chat-time'>".date("g:i A")."</span> <img width='24px' height='24px' style='border-radius: 50%' src='".$pfp."'/> <a style='color: ".$username_color."; font-weight: bold;'class='user-name' href='user.php?uid=".$uid."'>".$username."</a>: ".stripslashes(htmlspecialchars($text))."<br></div>";
    }

    file_put_contents("../pages/log_".$server.".html", $text_message. "\n", FILE_APPEND | LOCK_EX);
    CheckToClearChat("../pages/log_".$server.".html");
    $stmt = $conn->prepare("UPDATE Users SET LastSeen='".date("Y-m-d H:i:s")."' WHERE Username=?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
}

function CheckToClearChat($filename) {
    $msglimit = 500;
    $file = file($filename);
    $count = count(file($filename));
    if ($count > $msglimit) {
        $file = array_slice($file, $count - $msglimit);
        file_put_contents($filename, $file);
    }
  }
?>