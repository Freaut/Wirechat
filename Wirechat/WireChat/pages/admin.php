<!DOCTYPE html>
<html lang="en">
<head>
  <title>WireChat - Admin</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!--custom style-->
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css"></link>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"></script>
  <link rel="stylesheet" href="../css/style.css">

</head>
</html>
<body>
	
<?php include('../scripts/navbar.php'); ?>

<br><br>
<div class="content-box">
<?php
session_start();
error_reporting(E_ERROR | E_PARSE);
date_default_timezone_set("UTC");
include('../scripts/database.php');
include('../scripts/clean_input.php');
$username = legal_input($_SESSION['Username']);
$IsAdmin = false;

$stmt = $conn->prepare("SELECT * FROM Users WHERE Username=?");
$stmt->bind_param("s", $username);
$stmt->execute();
$res1 = $stmt->get_result();
if($res1->num_rows>0)
{
  while($data=$res1->fetch_assoc()){
    if (strtolower($data['Rank']) != "admin") {
		echo "<script>alert('You do not have permission to access this page.');</script>";
		echo "<script>window.location.href='../index.php';</script>";
		return;
    }
  }
}

function RandomString($length = 10) {
	$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	$charactersLength = strlen($characters);
	$randomString = '';
	for ($i = 0; $i < $length; $i++) {
		$randomString .= $characters[rand(0, $charactersLength - 1)];
	}
	return $randomString;
}


if(isset($_GET['inviter']) && isset($_GET['code']) && !empty($_GET['inviter']) && !empty($_GET['code']) && !empty($_GET['valid'])) {
	if (isset($_GET['infinite']))
		$infinite = "1"; // yes
	else
		$infinite = "0"; // no

	if (isset($_GET['valid']))
		$valid = $_GET['valid']; // yes
	else
		$valid = "0"; // no

	$stmt = $conn->prepare("INSERT INTO Invites (Inviter, Code, Infinite, Valid) VALUES (?, ?, ?, ?)");
	$stmt->bind_param("ssss", legal_input($_GET['inviter']), legal_input($_GET['code']), $infinite, $valid);
	$stmt->execute();
}

if(isset($_GET['delete'])) {
	$id 	= $_GET['delete'];
	$stmt = $conn->prepare("DELETE FROM Invites WHERE ID=?");
	$stmt->bind_param("s", $id);
	$stmt->execute();
}
if(isset($_GET['update'])) {
	$id 	= $_GET['delete'];
	$stmt = $conn->prepare("DELETE FROM Invites WHERE Id=?");
	$stmt->bind_param("s", legal_input($id));
	$stmt->execute();
}
if(isset($_GET['regenerate'])) {
	$id 		= $_GET['regenerate'];
	$stmt 		= $conn->prepare("SELECT Code FROM Invites WHERE Id=?");
	$stmt->bind_param("s", legal_input($id));
	$stmt->execute();
	$res 		= $stmt->get_result();
	$editData 	= $res->fetch_assoc();

	$code 		= $editData['Code'];
	$newcode 	= RandomString(6) ."-". RandomString(6) ."-". RandomString(6) ."-". RandomString(6);
	$stmt 		= $conn->prepare("UPDATE Invites SET Code=? WHERE Id=?");
	$stmt->bind_param("ss", $newcode, $id);
	$stmt->execute();
}
if(isset($_GET['add'])) {
	if(isset($_GET['edit'])) { 
		$editId		= $_GET['edit'];
		$stmt 		= $conn->prepare("SELECT * FROM Invites WHERE Id=?");
		$stmt->bind_param("s", legal_input($editId));
		$stmt->execute();
		$res 		= $stmt->get_result();
   		$editData	= $res->fetch_assoc();
   		$inviter	= $editData['Inviter'];
   		$code		= $editData['Code'];
   		$infinite	= $editData['Infinite'];
   		$valid		= $editData['Valid'];
   		$idAttr		= "updateForm";
	} else {
		//$inviter='';
		$inviter 	= legal_input($_SESSION['Username']);
		$code 		= RandomString(6) ."-". RandomString(6) ."-". RandomString(6) ."-". RandomString(6);
		$infinite 	= '';
		$valid 		= '1';
		$editId 	= '';
		$idAttr 	= "adminForm";
	}
	?>
<div class="card">
	<div class="row">
		<div class="col">
			<h3>Edit Key</h3>
		</div>
		<div class="col text-right">
			<a href="admin.php" class="btn btn-secondary content-link"> Back</a>
		</div>
	</div>
<br>
<form id="<?php echo $idAttr; ?>" rel="<?php echo $editId; ?>" name="Invites">
<div class="row">
	<div class="col">
		<div class="form-group">
			<label>Inviter</label>
			<input type="text" placeholder="Inviter" class="form-control" name="inviter" value="<?php echo $inviter; ?>">
		</div>
	</div>
</div>
<div class="row">
	<div class="col">
		<div class="form-group">
			<label>Code</label>
			<input type="text" placeholder="Code" class="form-control" name="code" value="<?php echo $code; ?>">
		</div>
	</div>
</div>
<div class="row">
	<div class="col">
		<div class="form-group">
			<input type="checkbox" placeholder="Infinite" name="infinite" <?php if ($infinite == 0) echo "checked='checked'"; ?>>
			<label>Infinite</label>
		</div>
	</div>
</div>
<div class="row">
	<div class="col">
		<div class="form-group">
			<input type="checkbox" placeholder="Valid" name="valid" <?php if ($valid == 0) echo "checked='checked'"; ?>>
			<label>Valid</label>
		</div>
	</div>
</div>
<div class="row">
	<div class="col">
		<div class="form-group">
			<button class="btn btn-secondary">Save</button>
		</div>
	</div>
</div>
</form>
<?php } else if(isset($_GET['view'])) {
	$id			= $_GET['view'];
	$stmt 		= $conn->prepare("SELECT * FROM Invites WHERE Id=?");
	$stmt->bind_param("s", legal_input($id));
	$stmt->execute();
	$res 		= $stmt->get_result();
   	$viewData	= $res->fetch_assoc();
   	$ID			= $viewData['Id'];
   	$inviter	= $viewData['Inviter'];
   	$code		= $viewData['Code'];
   	$infinite	= $viewData['Infinite'];
   	$valid		= $viewData['Valid'];
?>
<div class="row">
	<div class="col">
	</div>
	<div class="col text-right">
		<a href="admin.php" class="btn btn-secondary content-link">Back</a>
	</div>
</div>
<br>
<div class="table-responsive">
	<table class="table">
		<tr>
			<th>ID:</th><td><?php echo $id; ?></td>
		</tr>
		<tr>
			<th>Code:</th><td><input type="text" readonly class="form-control" style="width: 100%" value="<?php echo $code; ?>"></td>
		</tr>
		<tr>
			<th>Inviter:</th><td><?php echo $inviter; ?></td>
		</tr>
		<tr>
			<th>Infinite:</th><td><?php echo $infinite == 1 ? "Yes ✔": "No ❌"; ?></td>
		</tr>
		<tr>
			<th>Valid:</th><td><?php echo $valid == 1 ? "Yes ✔": "No ❌"; ?></td>
		</tr>
	</table>
</div>
   <?php

 }else {?>


<!-----=================table content start=================-->
	
	<div class="row">
		<div class="col">
			<h4>Invites</h4>
		</div>
		<div class="col text-right">
			<a href="admin.php?add" class="btn btn-secondary content-link"> Add New</a>
		</div>
	</div>
	<br>
	<div class="row">
		<div class="col">
	<div class="table-responsive">
	<div class="table-data">
		<table class="table">
			<tr>
				<th>ID</th>
				<th>Inviter</th>
				<th>Code</th>
				<th>Infinite</th>
				<th>Valid</th>
				<th>View</th>
				<th>Edit</th>
				<th>Delete</th>
				<th>Regenerate Code</th>
			</tr>
			<?php
  $sql1="SELECT * FROM Invites ORDER BY Id";
  $res1= $conn->query($sql1);
  if($res1->num_rows>0)
  {
   	while($data=$res1->fetch_assoc()) {
   	?>
   	<tr>
   		<td><?php echo $data['Id']; ?></td>
   		<td><?php echo $data['Inviter']; ?></td>
   		<td><input readonly class="form-control" style="width: 100%" value="<?php echo $data['Code']; ?>"></td>
   		<td><?php echo $data['Infinite'] == 1 ? "Yes ✔": "No ❌"; ?></td>
   		<td><?php echo $data['Valid'] == 1 ? "Yes ✔": "No ❌"; ?></td>

   		</a></td>
   		<td><a href="admin.php?view=<?php echo $data['Id']; ?>" class="text-secondary content-link"><i class='far fa-eye'></i></a></td>
        <td><a href="admin.php?add=&edit=<?php echo $data['Id']; ?>" class="text-success content-link"><i class=' far fa-edit'></i></a></td>
        <td><a href="admin.php?delete=<?php echo $data['Id']; ?>" class="text-danger delete"><i class='far fa-trash-alt'></i></a></td>
		<td><a href="admin.php?regenerate=<?php echo $data['Id']; ?>" class="btn btn-primary">Regenerate Key</a></td> 
   	</tr>
   	<?php
   }
}else{

?>
<tr>
	<td colspan="6">No Key Data</td>
</tr>
<?php } ?>
			
		</table>
	</div>
</div>
</div>
</div>
	<!-----==================table content end===================-->
<?php } ?>

</div>

<?php include("../scripts/footer.php"); ?>

</body>