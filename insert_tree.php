<?php

require("connection.php");

$info = $_POST["info"];
$xcoord = $info[0];
$ycoord = $info[1];
//$username = "John";
//$email = "john@example.com";
$username = $info[2];
$email = $info[3];

$sql = "INSERT INTO tree_location (xcoord, ycoord, username, email) VALUES($xcoord, $ycoord, '$username', '$email')";

if(mysqli_query($conn, $sql)){
	echo "<font color = green> Tree planted sucessfully </font>";
}
else{
	echo "<font color = red> Tree not planted </font>";
}

?>
