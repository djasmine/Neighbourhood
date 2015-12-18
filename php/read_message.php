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
    <style>
        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
        }
        #map {
            height: 400px;
            width: 500px;
        }
        .controls {
            margin-top: 10px;
            border: 1px solid transparent;
            border-radius: 2px 0 0 2px;
            box-sizing: border-box;
            -moz-box-sizing: border-box;
            height: 32px;
            outline: none;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.3);
        }

        #pac-input {
            background-color: #fff;
            font-family: Roboto;
            font-size: 15px;
            font-weight: 300;
            margin-left: 12px;
            padding: 0 11px 0 13px;
            text-overflow: ellipsis;
            width: 300px;
        }

        #pac-input:focus {
            border-color: #4d90fe;
        }

        #type-selector label {
            font-family: Roboto;
            font-size: 13px;
            font-weight: 300;
        }

    </style>
    <title>Read Message</title>
</head>
<body>
<?php include('../html/navbar.html');

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
echo "<div class='jumbotron'>";
echo "<div class='container'>";
#insert a new message
if (isset($_POST["receiver_id"])) {
    #a new message
    $receiver = $_POST["receiver"];
    $receiver_id = [];
    if ($receiver == "FRIEND" || $receiver == "NEIGHBOR") {
        array_push($receiver_id, $_POST["receiver_id"]);
        array_push($receiver_id, $id);
    } else if ($receiver == "ALL_FRIEND") {
        $query = "select user1 as uid from `friendship` where user2=? union select user2 as uid from `friendship` where user1=?";
        $stmt = mysqli_prepare($con, $query);
        $stmt->bind_param("ii", $id, $id);
        $stmt->execute();
        $res = $stmt->get_result();
        while ($row = $res->fetch_assoc()) {
            array_push($receiver_id, $row["uid"]);
        }
    } else if ($receiver == "BLOCK") {
        $query = "select userid as uid from `user` where blockid=(select blockid from `user` where userid=?)";
        $stmt = mysqli_prepare($con, $query);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $res = $stmt->get_result();
        while ($row = $res->fetch_assoc()) {
            array_push($receiver_id, $row["uid"]);
        }
    } else if ($receiver == "HOOD") {
        $query = "select userid as uid from `user` where hoodid=(select hoodid from `user` where userid=?)";
        $stmt = mysqli_prepare($con, $query);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $res = $stmt->get_result();
        while ($row = $res->fetch_assoc()) {
            array_push($receiver_id, $row["uid"]);
        }
    }
    if (count($receiver_id) == 1) {
        $_SESSION["msg"] = "You currently cannot send message to this group.";
        header("Location: error.php");
    }
    $addr = $_POST["address"];
    $lng = $_POST["lng"];
    $lat = $_POST["lat"];

    $query = "select max(mid) as max_mid from message";
    $stmt = mysqli_prepare($con, $query);
    $stmt->execute();
    $res = $stmt->get_result();
    $row = $res->fetch_assoc();
    $mid = $row["max_mid"] + 1;
    $content = $_POST["content"];
    $title = $_POST["title"];
    $m_date = date("Y-m-d H:i:s");
    $query = "insert into message(mid, m_number, author, send_time, title, content, location, receiver, lat, lng)".
        " values(?,0,?,?,?,?,?,?,?,?)";
    $stmt = mysqli_prepare($con, $query);
    $stmt->bind_param("iisssssdd", $mid, $id, $m_date, $title, $content, $addr, $receiver, $lat, $lng);
    $stmt->execute();

    $arrlen = count($receiver_id);
    for ($i = 0; $i < $arrlen; $i++) {
        $query = "insert into feed(mid, userid, fstatus) values(?,?,'unread')";
        $stmt = mysqli_prepare($con, $query);
        $stmt->bind_param("ii", $mid, $receiver_id[$i]);
        $stmt->execute();
    }
} else if (isset($_POST["content"])) {
    #reply to a message
    $mid = $_POST["mid"];
    $content = $_POST["content"];
    $query = "select mid, title, receiver, max(m_number) as m_num from message where mid=? group by mid, title, receiver";
    $stmt = mysqli_prepare($con, $query);
    $stmt->bind_param("i", $mid);
    $stmt->execute();
    $res = $stmt->get_result();
    $row = $res->fetch_assoc();
    $m_number = $row["m_num"] + 1;
    $m_date = date("Y-m-d H:i:s");
    $receiver = $row["receiver"];
    $title = $row["title"];
    $location = $_POST["address"];
    $lat = $_POST["lat"];
    $lng = $_POST["lng"];

    $query = "insert into message(mid, m_number, author, send_time, title, content, location, receiver, lat, lng)".
        " values(?,?,?,?,?,?,?,?,?,?)";
    $stmt = mysqli_prepare($con, $query);
    $stmt->bind_param("iiisssssdd", $mid, $m_number, $id, $m_date, $title, $content, $location, $receiver, $lat, $lng);
    $stmt->execute();

    $query = "update feed set fstatus='unread' where mid=?";
    $stmt = mysqli_prepare($con, $query);
    $stmt->bind_param("i", $mid);
    $stmt->execute();
} else {
    $mid = $_POST["mid"];
}

#web page
$query = "update feed set fstatus='read' where mid=? and userid=?";
$stmt = mysqli_prepare($con, $query);
$stmt->bind_param("ii", $mid, $id);
$stmt->execute();

$query1 = "select author, username, title, send_time, content, location, receiver from message, `user`".
    " where `user`.userid = author and mid = ? and m_number = 0";
$stmt1 = mysqli_prepare($con, $query1);
$stmt1->bind_param("i", $mid);
$stmt1->execute();
$res1 = $stmt1->get_result();
$row1 = $res1->fetch_assoc();
echo "<h1>".$row1["title"]."</h1>";
echo "<h4>Author: ".$row1["username"]."  Time: ".$row1["send_time"]."  TO: ".$row1["receiver"]." @".$row1["location"]."</h4>";
echo "<p>".$row1["content"]."</p>";
echo "</div></div>";

echo "<div class='container'>";
echo "<form class='form-horizontal' action='read_message.php' method='post'>";
echo "<div class='form-group'>";
echo "<label class='col-sm-1' control-label>You say:</label>";
echo "<div class='col-sm-6'>";
echo "<input id='pac-input' class='controls' type='text' placeholder='Search Box'><div id='map'></div>";
echo "</div>";
echo "<div class='col-sm-3'>";
echo "<textarea class='form-control' name='content' placeholder='Say something...' rows=\"18\"></textarea>";
echo "</div>";
echo "<div class='col-sm-1'>";
echo "<input type='submit' class='btn btn-primary'>";
echo "</div>";
echo "</div>";
echo "<input type='hidden' name='mid' value=".$mid.">";
echo "<input type='hidden' id='lat' name='lat' value=''>";
echo "<input type='hidden' id='lng' name='lng' value=''>";
echo "<input type='hidden' id='address' name='address' value=''>";
echo "</form>";

$query2 = "select m_number, author, username, title, send_time, content, location, receiver from message, `user`".
    " where `user`.userid = author and mid = ? and m_number > 0 order by m_number DESC";
$stmt2 = mysqli_prepare($con, $query2);
$stmt2->bind_param("i", $mid);
$stmt2->execute();
$res2 = $stmt2->get_result();
if ($res2->num_rows == 0) {
    echo "<h2>There are no replies for this message.</h2>";
} else {
    echo "<table class='table'>";
    echo "<thead><tr><th>User</th><th>Content</th><th>time</th><th>location</th></tr></thead>";
    echo "<tbody>";
    while ($row2 = $res2->fetch_assoc()) {
        echo "<tr><td>".$row2["username"]."</td><td>".$row2["content"]."</td><td>".$row2["send_time"]."</td><td>".$row2["location"]."</td></tr>";
    }
    echo "</tbody></table>";
}

echo "</div>";
?>
<script>
    function initAutocomplete() {
        var map = new google.maps.Map(document.getElementById('map'), {
            center: {lat: 40.693931, lng: -73.986483},
            zoom: 12,
            mapTypeId: google.maps.MapTypeId.ROADMAP
        });

        // Create the search box and link it to the UI element.
        var input = document.getElementById('pac-input');
        var searchBox = new google.maps.places.SearchBox(input);
        map.controls[google.maps.ControlPosition.TOP_LEFT].push(input);

        // Bias the SearchBox results towards current map's viewport.
        map.addListener('bounds_changed', function() {
            searchBox.setBounds(map.getBounds());
        });

        var markers = [];
        // [START region_getplaces]
        // Listen for the event fired when the user selects a prediction and retrieve
        // more details for that place.
        searchBox.addListener('places_changed', function() {
            var places = searchBox.getPlaces();

            if (places.length == 0) {
                return;
            }

            // Clear out the old markers.
            markers.forEach(function(marker) {
                marker.setMap(null);
            });
            markers = [];

            // For each place, get the icon, name and location.
            var bounds = new google.maps.LatLngBounds();

            places.forEach(function(place) {
                var icon = {
                    url: place.icon,
                    size: new google.maps.Size(71, 71),
                    origin: new google.maps.Point(0, 0),
                    anchor: new google.maps.Point(17, 34),
                    scaledSize: new google.maps.Size(25, 25)
                };

                // Create a marker for each place.
                markers.push(new google.maps.Marker({
                    map: map,
                    icon: icon,
                    title: place.name,
                    position: place.geometry.location
                }));

                if (place.geometry.viewport) {
                    // Only geocodes have viewport.
                    bounds.union(place.geometry.viewport);
                } else {
                    bounds.extend(place.geometry.location);
                }
                document.getElementById("address").value = input.value;
                document.getElementById("lat").value = place.geometry.location.lat();
                document.getElementById("lng").value = place.geometry.location.lng();
            });
            map.fitBounds(bounds);
        });
        // [END region_getplaces]
    }
</script>
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBHrZZCQYPQYXl1Hx7MYuayCZNd0hX8CFE&libraries=places&callback=initAutocomplete"
        async defer></script>
<script src="//cdn.bootcss.com/jquery/1.11.3/jquery.min.js"></script>
<script src="//cdn.bootcss.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
</body>
</html>