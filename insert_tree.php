<?php

require("connection.php");

$info = $_POST["info"];
$xcoord = $info[0];
$ycoord = $info[1];
$username = $info[2];
$email = $info[3];
$type = $info[4];

$sql = "INSERT INTO tree_location (type, xcoord, ycoord, username, email) VALUES('$type', $xcoord, $ycoord, '$username', '$email')";
if(filter_var($email,FILTER_VALIDATE_EMAIL))
{
	if(mysqli_query($conn, $sql)){
		//echo "<font color='green'> Congratulations! You have planted a tree. :) </font>";
		$message = "Congratulations, $username!\nYou have planted a $type tree.\nPlease provide your full name, residential addreess, tree named after and bank details (bank name and account number) in reply to this email.\n\nThanks!";
		mail($email,'Tree Plantation', $message, 'From: ned.trees@gmail.com');
	}
	else
		echo "<font color='red'> Tree not planted </font>";
}
else
	echo "<font color='red'> Please Enter Valid Email address </font>";

?>
