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
    <title>Friend</title>
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
$query = "select userid, address, username, email from `user`, friendship where (userid = user1 and user2 = ?) or (userid = user2 and user1 = ?)";
$stmt = mysqli_prepare($con, $query);
$stmt->bind_param("ii", $id, $id);
$stmt->execute();
$res = $stmt->get_result();
if ($res->num_rows == 0) {
    echo "<h3>Sorry, you currently don't have any friends.</h3>";
} else {
    echo "<h3>Hey, here are your friends.</h3>";
    echo "<div class='container'>";
    echo "<form class='form-horizontal' action='send_message.php' method='post'>";
    echo "<input type='hidden' value='ALL_FRIEND' name='receiver'>";
    echo "<input type='hidden' value='' name='receiver_id'>";
    echo "<div class='col-sm-9'></div>";
    echo "<div class='col-sm-1'>";
    echo "<input class='btn btn-success' type='submit' value='send to all friends'>";
    echo "</div>";
    echo "</form>";
    echo "</div>";
    echo "<table class='table'>";
    echo "<thead><tr><th>name</th><th>email</th><th>address</th><th>send message</th></tr></thead>";
    echo "<tbody>";
    while ($row = $res->fetch_assoc()) {
        echo "<tr><th>".$row["username"]."</th><th>".$row["email"]."</th><th>".$row["address"]."</th>";
        echo "<form action='send_message.php' method='post'>";
        echo "<input type='hidden' name='receiver' value='FRIEND'>";
        echo "<input type='hidden' id='receiver_id' name='receiver_id' value=".$row["userid"].">";
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

    $.getJSON('getLocations.php', function(data) {
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