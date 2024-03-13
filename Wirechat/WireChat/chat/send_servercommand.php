<?php
    $servercommands = array("Change Chatroom", "ChangeServer");

    $fp = fsockopen("51.12.246.58", 6969, $errno, $errstr, 30);
    if (!$fp) {
        echo "$errstr ($errno)<br />\n";
    } else {
        fwrite($fp, "Webclient: " . $servercommands[$_POST['command']] . " " . $_POST['parameters']);
        while (!feof($fp)) {
            echo fgets($fp, 128);
        }
        fclose($fp);
    }

?>