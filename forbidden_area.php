<?php
// connect to database
require("connection.php");

// fetch all previous forbidden area records and store in arrays
$sql = "SELECT * FROM forbidden_area";

$result = mysqli_query($conn, $sql);
$areaids = array(); $areanames = array();

if(mysqli_num_rows($result) > 0){
	while ($row = mysqli_fetch_array($result)) {
		array_push($areaids, $row["id"]);
        array_push($areanames, $row["name"]);
    }
    $allareacoords = array();
    foreach($areaids as $x) {
        $areacoords = array();
        $sql = "SELECT * FROM forbidden_coordinates WHERE areaid = $x";
        
        $result = mysqli_query($conn, $sql);

        while ($row = mysqli_fetch_array($result)) {
            array_push($areacoords, $row["xcoord"]);
            array_push($areacoords, $row["ycoord"]);
        }
        array_push($allareacoords, $areacoords);
    }
}
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
    <script>
    if(performance.navigation.type == 2){
    location.reload(true);
    }
    </script>
    <link rel="shortcut icon" href="./images/pine-tree.png" />
    <link rel="stylesheet" href="./bootstrap-3.3.7/css/bootstrap.min.css">
    <style>
        .cell {cursor: crosshair;}
        area {cursor: not-allowed;}
    </style>
</head>
<body>
    <br>
    <div class="container">
        <form id="coordForm" method="POST" action="insert_forbidden_area.php">
            Coordinates: 
            <button class="submitCoords btn btn-success btn-block">Submit Coordinates</button>
        </form>
        <br>
        <div>
            <b>Current position:</b>
            <span class="mouse-cords"></span>
        </div>
    </div>
    <br>
    
    <!-- Image on which the forbidden area going to be marked -->
    <img class="map cell" id="mapImg" src="./images/campusMap.jpg" usemap="#mapTree" />

    <!-- Previously restricted area -->
    <map name="mapTree">
        <?php
        for($i=0; $i<count($areaids); $i++) {
            ?><area shape="poly" 
            coords="<?php
            for($j=0; $j<count($allareacoords[$i]); $j++) {
                echo $allareacoords[$i][$j].",";
            }
            ?>"
            alt="<?php echo "$areanames[$i]"; ?>" title="<?php echo "$areanames[$i]"; ?>"
            class="area">
            <?php
        }
        ?>
    </map>

    <p id="msg"></p>
</body>
</html>

<script type="text/javascript">
    $(document).ready(function(){
        $first = 0;

        // display X and Y coordinates
        $(".map").mousemove(function(event){
            var relX = event.pageX - $(this).offset().left;
            var relY = event.pageY - $(this).offset().top;
            var relBoxCoords = "(" + relX + "," + relY + ")";
            $(".mouse-cords").text(relBoxCoords);
        });
        
        // mark target location when user clicks
        $(".map").click(function (ev) {
            //$(".marker").remove();
            // get all user info from fields
            var coords = [];
            coords[0] = ev.pageX - $(this).offset().left;
            coords[1] = ev.pageY - $(this).offset().top;
            // display info on hover
            var title = "(" + coords[0] + ", " + coords[1] + ")";
            
            $("body").append(
                $('<img class="marker" src="./images/target.png" />').css({
                position: 'absolute',
                top: (ev.pageY - 8) + 'px',
                left: (ev.pageX - 8) + 'px'
                }).prop('title', title)
            );

            $("#coordForm .submitCoords:last").before(
                $('<input type="text" name="coords[]" value="'+coords[0]+'" required />').css({
                display: 'none'
                })
            );

            $("#coordForm .submitCoords:last").before(
                $('<input type="text" name="coords[]" value="'+coords[1]+'" required />').css({
                display: 'none'
                })
            );

            if(!$first){
                $("#coordForm .submitCoords:last").before(
                    $('<span>('+coords[0]+', '+coords[1]+') </span>').css({
                    //display: 'none'
                    })
                );
                $first++;
            }
            else{
                $("#coordForm .submitCoords:last").before(
                    $('<span>, ('+coords[0]+', '+coords[1]+') </span>').css({
                    //display: 'none'
                    })
                );
            }

        });

        /*$(".submitCoords").click(function (ev) {
            var formdata = $("form").serialize();
            $.ajax({
                type : "POST",
                url : "insert_forbidden_area.php",
                data : {coords : coords},
                dataType: 'JSON',
                success : function(feedback){
                    $("#msg").html(feedback);
                }
            });
        });*/
    });
    
</script>
