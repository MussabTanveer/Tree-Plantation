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
        
    </style>
</head>
<body>
    <br>
    <div class="container">

    <?php
    if(!empty($_GET['area'])) {
        $area = $_GET['area'];

        // Delete area query
        $sql = "DELETE FROM forbidden_area WHERE id = $area";

        if(mysqli_query($conn, $sql)) {
            // Delete area coordinates query
            $sql = "DELETE FROM forbidden_coordinates WHERE areaid = $area";

            if (mysqli_query($conn, $sql)) {
                echo "<h3 style='color:green'> Forbidden area coordinates have been deleted. </h3>";
            }
            else {
                echo "<h3 style='color:red'> Forbidden area coordinates have not been deleted. </h3>";
                //echo "Error: " . $sql . "<br>" . mysqli_error($conn);
            }

            echo "<h3 style='color:green'> Forbidden area has been deleted. </h3>";
        }
        else {
            echo "<h3 style='color:red'> Forbidden area has not been deleted. </h3>";
            //echo "Error: " . $sql . "<br>" . mysqli_error($conn);
        }
    }
    ?>
    </div>
</body>
</html>