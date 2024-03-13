<?php
error_reporting(!E_ERROR | !E_PARSE);
try {
  // 
  //$conn = new mysqli("127.0.0.1", "root", "8UYG87ygd8G7YF4W7", "WireChat");
  $conn = new mysqli("127.0.0.1", "root", "", "WireChat");

  if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
  }
}
catch (exception $e) {
  die("<h1>Database login error</h1><h3>Contact an admin!<br>- Thank you</h3>");
}
?>
