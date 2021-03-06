<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="//cdn.bootcss.com/bootstrap/3.3.5/css/bootstrap.min.css" rel="stylesheet">
    <title>Neighbour</title>
</head>
<body>
<?php include('../html/navbar.html');
echo "<div class='jumbotron'>";
echo "<div class='container'>";
if (isset($_SESSION["id"])) {
    $id = $_SESSION["id"];
} else {
    $_SESSION["msg"] = "out of session";
    header("Location: error.php");
    exit;
}
$con = mysqli_connect("127.0.0.1:3306", "root", "", "Neighbourhood");
if (!$con) {
    die("connection failed");
}
$query = "select userid, address, username, email from `user`, neighbor where userid = neighbor_id and host_id = ?";
$stmt = mysqli_prepare($con, $query);
$stmt->bind_param("i", $id);
$stmt->execute();
$res = $stmt->get_result();
if ($res->num_rows == 0) {
    echo "<h3>Sorry, you currently don't have any neighbors.</h3>";
} else {
    echo "<h3>Hey, here are your neighbors.</h3>";
    echo "<table class='table'>";
    echo "<thead><tr><th>name</th><th>email</th><th>address</th><th>send message</th></tr></thead>";
    echo "<tbody>";
    while ($row = $res->fetch_assoc()) {
        echo "<tr><th>".$row["username"]."</th><th>".$row["email"]."</th><th>".$row["address"]."</th>";
        echo "<form action='send_message.php' method='post'>";
        echo "<input type='hidden' name='receiver' value='NEIGHBOR'>";
        echo "<input type='hidden' name='receiver_id' value=".$row["userid"].">";
        echo "<th><input type='submit' class='btn btn-primary' value='send'></th>";
        echo "</form>";
        echo "</tr>";
    }
    echo "</tbody></table>";
}
echo "</div></div>";
?>
<div class="container"><div id="googleMap" style="width:75%;height:500px;margin-bottom: 80px"></div></div>
<script src="http://maps.googleapis.com/maps/api/js"></script>
<script src="//cdn.bootcss.com/jquery/1.11.3/jquery.min.js"></script>
<script>

    var locations = [];

    $.getJSON('getLocations.php?type=neighbor', function(data) {
        $.each(data, function(idx, tuple) {
            var loc = new google.maps.LatLng(tuple.latitude, tuple.longitude);
            var temp = {name: tuple.username, loc: loc};
            locations.push(temp);
        });

        var mapProp = {
            center:locations[0].loc,
            zoom:12,
            mapTypeId:google.maps.MapTypeId.ROADMAP
        };

        var map = new google.maps.Map(document.getElementById("googleMap"),mapProp);

        locations.forEach(function(location) {

            var marker = new google.maps.Marker({
                position:location.loc,
            });

            marker.setMap(map);

            var infowindow = new google.maps.InfoWindow({
                content:location.name
            });

            infowindow.open(map,marker);
        });
        console.log(locations);
    });

</script>
<script src="//cdn.bootcss.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
</body>
</html>