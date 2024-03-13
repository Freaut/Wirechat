<?php
date_default_timezone_set("UTC");

// by default, error messages are empty
$call_register=$set_username=$usernameErr=$passErr=$cpassErr=$emailErr=$inviteErr='';

extract($_POST);
include("clean_input.php");
if(isset($register))
{
  //Username Validation
  if(empty($username)){
    $usernameErr = "Username is Required"; 
  }
  else{
    $stmt = $conn->prepare("SELECT * FROM Users WHERE Username=?");
    $stmt->bind_param("s", legal_input($username));
    $stmt->execute();
    $result = $stmt->get_result();
    $num_lines = $result->num_rows;
    if ($num_lines > 0) {
      $usernameErr = "Username already exists";
    } else {
      $usernameErr = true;
    }
  }
  
  if (empty($email)){
    $emailErr = "Email is required";
  }
  else{
    $emailErr = true;
  }

  // password validation 
  if(empty($password)){
    $passErr = "Password is Required"; 
  } 
  else{
    /* password requirements */
    $uppercase = preg_match('@[A-Z]@', $password);
    $number    = preg_match('@[0-9]@', $password);
    if (!$uppercase || !$number || strlen($password) < 8) {
      $passErr="Password must be at least 8 characters in length and must include at least one upper case letter, one number, and one special character."; 
    }
    else{
      $passErr = true;
    } 
  }

  // cpassword validation 
  if(empty($cpassword)){
    $cpassErr = "Confirm Password is Required"; 
  } 
  else{
    if ($password == $cpassword){
      $cpassErr = true;
    }
    else{
      $cpassErr = "Password does not match";
    }
  }

  // invite validation 
  if(empty($invite)){
    $inviteErr = "Invite is Required"; 
  }
  else{
    $stmt = $conn->prepare("SELECT * FROM Invites WHERE Invite=?");
    $stmt->bind_param("s", legal_input($invite));
    $stmt->execute();
    $result = $stmt->get_result();
    $data = $result->fetch_assoc();
    $num_lines = $result->num_rows;

    if ($num_lines > 0) {
      $inviteErr = true;
      $inviter = $data['Inviter'];
      if ($data['Infinite'] == 0) {
        // prepare update statement
        $stmt = $conn->prepare("UPDATE Invites SET Valid=0 WHERE Invite=?");
        $stmt->bind_param("s", legal_input($invite));
        $stmt->execute();
      }
    } else {
      $inviteErr = "Invalid Invite";
    }

    // $sql_select_query = "SELECT * FROM Invites WHERE Code='". $invite ."' LIMIT 1";
    // $sql_select_result = $conn->query($sql_select_query);
    // if($sql_select_result->num_rows>0)
    // {
    //   while($data = $sql_select_result->fetch_assoc()) {
    //     $inviteErr = true;
    //     $inviter = $data['Inviter'];
    //     if ($data['Infinite'] == 0) {
    //       $sql_update_query = "UPDATE Invites SET Valid='0' WHERE Code='". $invite ."'";
    //       $sql_delete_result = $conn->query($sql_update_query);
    //     }
    //   }
    // } else {
    //   $inviteErr = "Invalid Invite"; 
    // }
  }

  // check all fields are valid or not
  if( $usernameErr == 1 && $passErr == 1 && $cpassErr == 1 && $emailErr == 1 && $inviteErr == 1)
  {
     // legal input values
     $username = legal_input($username);
     $password = legal_input(hash("sha256", $password));
     $email    = legal_input($email);
     // Sql Query to insert user data into database table
     $db = $conn;// database connection  
     $call_login = register($db,$username,$password, $email, $inviter);

  } else {
     $set_username=$username;
  }
}

// function to insert user data into database table
function register($db,$username,$password, $email, $inviter) {
  $data= [
    'Username' =>$username,
    'Password' =>$password,
    'Email'    =>$email,
    'Rank' => 'User',
    'Inviter' => $inviter
  ];

  $stmt = $db->prepare("INSERT INTO Users (Username, Password, Email, Rank, Inviter) VALUES (?, ?, ?, ?, ?)");
  $stmt->bind_param("sssss", $data['Username'], $data['Password'], $data['Email'], $data['Rank'], $data['Inviter']);
  $stmt->execute();
  $result = $stmt->get_result();
  if ($result){
    session_start();
    $_SESSION['Username']=$username;
    $_SESSION['Rank'] = $data["Rank"];
    // $pfpurl=$data['ProfilePicture'];
    header("location:pages/dashboard.php");
  }
}

function insert_data(array $data, string $tableName){
  global $db;

  $tableColumns = $userValues = ''; 
  $num = 0; 
  foreach($data as $column=>$value){ 
       $comma = ($num > 0)?', ':''; 
       $tableColumns .= $comma.$column; 
       $userValues  .= $comma."'".$value."'"; 
       $num++; 
  }
  $insertQuery="INSERT INTO ".$tableName."  (".$tableColumns.") VALUES (".$userValues.")";
  $insertResult=$db->query($insertQuery);
  if($insertResult){
      return true;
  }else{
      return "Error: " . $insertQuery . "<br>" . $db->error;
  }
}
?>
