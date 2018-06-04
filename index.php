<?php
// connect to database
require("connection.php");

// fetch all previous marked trees records and store in arrays
$sql = "SELECT * FROM tree_location";

$result = mysqli_query($conn, $sql);
$oldxcoords = array(); $oldycoords = array();
$usernames = array(); $emails = array();

if(mysqli_num_rows($result) > 0){
	while ($row = mysqli_fetch_array($result)) {
		array_push($oldxcoords, $row["xcoord"]);
        array_push($oldycoords, $row["ycoord"]);
        array_push($usernames, $row["username"]);
        array_push($emails, $row["email"]);
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
    <link rel="shortcut icon" href="./images/pine-tree.png" />
    <style>
    .cell {cursor: cell;}
    </style>
</head>
<body>
    <div id="infoPanel">
        <div>
            <b>Username:</b>
            <input type="text" name="username" id="username" required/>
        </div>
        <div>
            <b>Email:</b>
            <input type="email" name="email" id="email" required/>
        </div>
        <div>
            <b>Current position:</b>
            <span class="mouse-cords"></span>
        </div>
        <div>
            <input type="radio" name="tree" value="pine"> <img class="marker" src="./images/pine-tree.png" title="pine tree" /><br>
            <input type="radio" name="tree" value="honeylocust"> <img class="marker" src="./images/honeylocust-tree.gif" title="honeylocust tree" /><br>
            <input type="radio" name="tree" value="neem"> <img class="marker" src="./images/neem-tree.gif" title="neem tree" />
        </div>
    </div>

    <img class="map cell" src="./images/campusMap.jpg" />

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
        var i = 0;

        for(i = 0; i < oldxcoords.length; i++){
            var user = "Username: " + usernames[i] + ", Email: " + emails[i];
            $("body").append(
                $('<img class="marker" src="./images/pine-tree.png" />').css({
                position: 'absolute',
                top: oldycoords[i] + 'px',
                left: oldxcoords[i] + 'px'
                }).prop('title', user)
            );
        }
        
        // display X and Y coordinates
        $(".map").mousemove(function(event){
            var relX = event.pageX;
            var relY = event.pageY;
            var relBoxCoords = "(" + relX + "," + relY + ")";
            $(".mouse-cords").text(relBoxCoords);
        });
        
        // mark tree when user clicks
        $(".map").click(function (ev) {
            //$(".marker").remove();
            var info = [];
            info[0] = ev.pageX - 13;
            info[1] = ev.pageY - 15;
            info[2] = $('#username').val();
            info[3] = $('#email').val();
            info[4] = $("input[type='radio']:checked").val();
            var user = "Username: " + info[2] + ", Email: " + info[3] + ", Tree: " + info[4];
            if(info[2] && info[3] && info[4] && (info[0] < 546 || info[0] > 753 || info[1] < 775 || info[1] > 936) ) {
                $("body").append(
                    $('<img class="marker" src="./images/pine-tree.png" />').css({
                    position: 'absolute',
                    top: (ev.pageY-15) + 'px',
                    left: (ev.pageX-13) + 'px'
                    }).prop('title', user)
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
</script>
