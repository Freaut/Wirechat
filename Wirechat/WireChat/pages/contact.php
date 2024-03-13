<?php
error_reporting(0);
session_start();
$username= !empty($_SESSION['Username']) ? $_SESSION['Username'] : '';
if(empty($username)){
    header("location:../index.php");
}

$errors = array();
$success = '';
$errorMessage = '';

if (!empty($_POST)) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $message = $_POST['message'];

    if (empty($name)) {
        array_push($errors, 'Name is empty');
    }

    if (empty($email)) {
        array_push($errors, 'Email is empty');
    } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        array_push($errors, 'Email is invalid');
    }

    if (empty($message)) {
        array_push($errors, 'Message is empty');
    }

    if (empty($errors)) {
        $toEmail = 'freaut69@gmail.com';
        $emailSubject = 'Contact';
        $headers = ['From' => $email, 'Reply-To' => $email, 'Content-type' => 'text/html; charset=utf-8'];
        $bodyParagraphs = ["Name: {$name}", "Email: {$email}", "Message:", $message];
        $body = join(PHP_EOL, $bodyParagraphs);

        if (mail($toEmail, $emailSubject, $body, $headers)) 
            $success = 'Message sent successfully';
        } else {
            $errorMessage = 'Oops, something went wrong. Please try again later';
        }

    } else {
        $allErrors = join('<br/>', $errors);
        $errorMessage = "<p style='color: red;'>{$allErrors}</p>";
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

</head>
<body>

    <?php include("../scripts/navbar.php") ?>

    <!-- Create a contact form -->
    <div class="container">
        <?php
            $path = "../img";
            include("../scripts/main_logo.php");
        ?>

        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Contact Us</h5>
                        <p class="card-text">If you have any questions or concerns, please contact us.</p>
                        <form action="contact.php" method="post">
                            <div class="form-group">
                                <input autocomplete="off" type="text" id="name" class="form-control" name="name" placeholder="Enter your name">
                            </div>
                            <div class="form-group">
                                <input autocomplete="off" type="email" id="email" class="form-control" name="email" placeholder="Enter your email">
                            </div>
                            <div class="form-group">
                                <textarea class="form-control" id="message" name="message" rows="3" placeholder="Enter your message"></textarea>
                            </div>
                            <p class="err-msg">
                                <?php echo((!empty($errorMessage)) ? $errorMessage : '') ?>
                            </p>
                            <p style="color:lime">
                                <?php echo((!empty($success)) ? $success : '') ?>
                            </p>
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include("../scripts/footer.php"); ?>

</body>
</html>
