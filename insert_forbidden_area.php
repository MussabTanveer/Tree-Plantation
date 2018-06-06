<?php
// connect to database
require("connection.php");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Tree Plantation</title>
    <script src="./script/jquery/jquery-3.2.1.min.js"></script>
    <script src="./bootstrap-3.3.7/js/bootstrap.min.js"></script>
    <link rel="shortcut icon" href="./images/pine-tree.png" />
    <link rel="stylesheet" href="./bootstrap-3.3.7/css/bootstrap.min.css">
    <style>
        .cell {cursor: crosshair;}
    </style>
</head>
<body>
    <br>
    <div class="container">

<?php
$coords=array();
foreach ($_POST['coords'] as $coord)
{
    array_push($coords,$coord);	
}
//print_r($coords);

$sql = "INSERT INTO forbidden_area (name) VALUES('Forbidden Area')";

if(mysqli_query($conn, $sql)) {
    $last_id = mysqli_insert_id($conn);

    $count = 0;
    $xy = array();
    $sqlmulti = "";
    foreach($coords as $c)
    {
        if($count < 2){
            array_push($xy, $c);
            $count++;
        }
        else{
            $sqlmulti .= "INSERT INTO forbidden_coordinates (areaid, xcoord, ycoord) VALUES($last_id, $xy[0], $xy[1]);";
            $count = 0;
            $xy = array();
            array_push($xy, $c);
            $count++;
        }
    }
    // For very last coord record
    $sqlmulti .= "INSERT INTO forbidden_coordinates (areaid, xcoord, ycoord) VALUES($last_id, $xy[0], $xy[1]);";

    if (mysqli_multi_query($conn, $sqlmulti)) {
        echo "<h3 style='color:green'> Forbidden area coordinates have been inserted. </h3>";
    }
    else {
        echo "<h3 style='color:red'> Forbidden area coordinates have not been inserted. </h3>";
        //echo "Error: " . $sqlmulti . "<br>" . mysqli_error($conn);
    }

    echo "<h3 style='color:green'> Forbidden area has been marked. </h3>";
}
else {
    echo "<h3 style='color:red'> Forbidden area has not been marked </h3>";
    //echo "Error: " . $sql . "<br>" . mysqli_error($conn);
}

?>
    </div>
</body>
</html>