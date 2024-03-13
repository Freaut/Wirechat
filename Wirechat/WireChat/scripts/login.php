<?php
// by default, error messages are empty
$call_login=$set_username=$usernameErr=$passErr='';

extract($_POST);
include("clean_input.php");
if(isset($login))
{
 
  //Username Validation
  if(empty($username))
    $usernameErr = "Username is Required"; 
  else
    $usernameErr = true;

  // password validation 
  if(empty($password))
    $passErr = "Password is Required"; 
  else
    $passErr = true;

  // check all fields are valid or not
  if($usernameErr == 1 && $passErr == 1)
  {
    //legal input values
    $username = legal_input($username);
    $password = legal_input(hash("sha256", $password));

    //  Sql Query to insert user data into database table
    $db = $conn;// database connection  
    $call_login = login($db, $username, $password);

  } else {
    $set_username = $username;
  }
}

// function to check valid login data into database table
function login($db, $username, $password){
    // checking valid user
    $stmt = $db->prepare("SELECT * FROM Users WHERE Username=? AND Password=?");
    $stmt->bind_param("ss", legal_input($username), legal_input($password));
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $num_lines = $result->num_rows;

    if ($num_lines > 0) {
        // if user exists
        $_SESSION['Username'] = $user['Username'];
        $_SESSION['Rank'] = $user['Rank'];
        $pfpurl = $user['ProfilePicture'];
        header("location:pages/dashboard.php");
    } else {
        return "Invalid Username or Password";
    }
}
?>