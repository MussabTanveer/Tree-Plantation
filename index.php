<?php
// connect to database
require("connection.php");

// fetch all previous marked trees records and store in arrays
$sql = "SELECT * FROM tree_location";

$result = mysqli_query($conn, $sql);
$types = array(); $oldxcoords = array(); $oldycoords = array();
$usernames = array(); $emails = array();

if(mysqli_num_rows($result) > 0){
	while ($row = mysqli_fetch_array($result)) {
		array_push($oldxcoords, $row["xcoord"]);
        array_push($oldycoords, $row["ycoord"]);
        array_push($usernames, $row["username"]);
        array_push($emails, $row["email"]);
        array_push($types, $row["type"]);
	}
}

// fetch all forbidden area records and store in arrays
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
//print_r($areaids);echo "<br>";
//print_r($areanames);echo "<br>";
//print_r($allareacoords);echo "<br>";

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
        .cell {cursor: cell;}
        area {cursor: not-allowed;}
    </style>
</head>
<body>
    <br>
    <div class="container">
        <div id="infoPanel" class="form-inline">
            <div class="input-group">
                <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
                <input type="text" name="username" class="form-control" id="username" placeholder="Enter username" required/>
            </div>
            <div class="input-group">
                <span class="input-group-addon"><i class="glyphicon glyphicon-envelope"></i></span>
                <input type="email" name="email" class="form-control" id="email" placeholder="Enter email" required/>
            </div>
            <div class="form-group">
                <b>Select tree: </b>
                <label class="radio-inline"><input type="radio" name="tree" value="pine"> <img class="marker" src="./images/pine-tree.png" title="pine tree" /></label>
                <label class="radio-inline"><input type="radio" name="tree" value="honeylocust"> <img class="marker" src="./images/honeylocust-tree.gif" title="honeylocust tree" /></label>
                <label class="radio-inline"><input type="radio" name="tree" value="neem"> <img class="marker" src="./images/neem-tree.gif" title="neem tree" /></label>
            </div>
            <div class="form-group">
                <b>Current position:</b>
                <span class="mouse-cords"></span>
            </div>
        </div>
        <br>
        <!-- Toggle images 
        <button class="btn btn-success" id="toggle" onclick="toggle();">Show Coordinates <span class="glyphicon glyphicon-eye-open"></span></button>
        <br>
        -->
    </div>
    <br>
    
    <!-- Image on which the trees are going to be placed -->
    <img class="map cell" id="mapImg" src="./images/campusMap.jpg" usemap="#mapTree" style="width:1280px; height:1024px;" />
    
    <!-- Restricted area -->
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
        // display all previous marked trees
        var oldxcoords = <?php echo json_encode($oldxcoords); ?>;
        var oldycoords = <?php echo json_encode($oldycoords); ?>;
        var usernames = <?php echo json_encode($usernames); ?>;
        var emails = <?php echo json_encode($emails); ?>;
        var types = <?php echo json_encode($types); ?>;
        var i = 0;

        for(i = 0; i < oldxcoords.length; i++){
            var title = "Username: " + usernames[i] + ", Email: " + emails[i] + ", Tree: " + types[i];
            if(types[i] == "pine"){
                var src = "./images/pine-tree.png"
            }
            else if(types[i] == "honeylocust"){
                var src = "./images/honeylocust-tree.gif"
            }
            else if(types[i] == "neem"){
                var src = "./images/neem-tree.gif"
            }
            oldycoords[i] = parseInt(oldycoords[i]) + $(".map").offset().top;
            oldxcoords[i] = parseInt(oldxcoords[i]) + $(".map").offset().left;
            $("body").append(
                $('<img class="marker" src="'+src+'" />').css({
                position: 'absolute',
                top: oldycoords[i] + 'px',
                left: oldxcoords[i] + 'px'
                }).prop('title', title)
            );
        }
        
        // display X and Y coordinates
        $(".map").mousemove(function(event){
            var relX = event.pageX - $(this).offset().left;
            var relY = event.pageY - $(this).offset().top;
            var relBoxCoords = "(" + relX + "," + relY + ")";
            $(".mouse-cords").text(relBoxCoords);
        });
        
        // mark tree when user clicks
        $(".map").click(function (ev) {
            //$(".marker").remove();
            // get all user info from fields
            var info = [];
            info[0] = ev.pageX - $(this).offset().left - 15;
            info[1] = ev.pageY - $(this).offset().top - 30;
            info[2] = $('#username').val();
            info[3] = $('#email').val();
            info[4] = $("input[type='radio']:checked").val();
            // set img src a/c to type of tree to be placed
            if(info[4] == "pine"){
                var src = "./images/pine-tree.png"
            }
            else if(info[4] == "honeylocust"){
                var src = "./images/honeylocust-tree.gif"
            }
            else if(info[4] == "neem"){
                var src = "./images/neem-tree.gif"
            }
            // display info on hover
            var title = "Username: " + info[2] + ", Email: " + info[3] + ", Tree: " + info[4];
            
            // check whether user has filled all info in fields
            if(info[2] && info[3] && info[4]) {
                $("body").append(
                    $('<img class="marker" src="'+src+'" />').css({
                    position: 'absolute',
                    top: (ev.pageY - 30) + 'px',
                    left: (ev.pageX - 15) + 'px'
                    }).prop('title', title)
                );

                $.ajax({
                    type : "POST",
                    url : "insert_tree.php",
                    data : {info : info},
                    dataType: 'JSON',
                    success : function(feedback){
                        $("#msg").html(feedback);
                    }
                });
            }
        });
    });
    
    // Toggle map images
    /*
    var t = 0;
    function toggle(){
        if(t == 0)
        {
            document.getElementById("mapImg").src = "./images/campusMap2.jpg";
            document.getElementById("toggle").innerHTML = 'Hide Coordinates <span class="glyphicon glyphicon-eye-close"></span>';
            t = 1;
        }
        else
        {
            document.getElementById("mapImg").src = "./images/campusMap.jpg";
            document.getElementById("toggle").innerHTML = 'Show Coordinates <span class="glyphicon glyphicon-eye-open"></span>';
            t = 0;
        }
    }
    */
</script>
