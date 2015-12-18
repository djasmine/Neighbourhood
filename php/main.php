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
    <title>Home</title>
</head>
<body>
<?php include('../html/navbar.html');
    if (isset($_POST["change_value"])) {
        #change some value
        $id = $_SESSION["id"];
        $email = $_POST["email"];
        $description = $_POST["description"];
        $lat = $_POST["lat"];
        $lng = $_POST["lng"];
        $addr = $_POST["addr"];
        $con = mysqli_connect("127.0.0.1:3306", "root", "", "Neighbourhood");
        if (!$con) {
            die("connection failed");
        }
        #check email
        if ($email != "") {
            $query = "select userid frome `user` where email=?";
            $stmt = mysqli_prepare($con, $query);
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $res = $stmt->get_result();
            if ($res->num_rows > 0) {
                $_SESSION["msg"] = "This email is used by another user.";
                header("Location: error.php");
                exit;
            }
        }


        $query = "select username, email, description, blockid, hoodid, address, latitude, longitude from `user` where userid=?";
        $stmt = mysqli_prepare($con, $query);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $res = $stmt->get_result();
        $row = $res->fetch_assoc();
        if ($email == "") {
            $email = $row["email"];
        }
        if ($description == "") {
            $description = $row["description"];
        }
        if ($addr == "") {
            $addr = $row["address"];
            $lng = $row["longitude"];
            $lat = $row["latitude"];
            $blockid = $row["userid"];
            $hoodid = $row["hoodid"];
        } else {
            #check address range
            $query = "select blockid, hoodid from blocks where long_st<? and ?<=long_ed and lati_st<? and ?<=lati_ed";
            $stmt = mysqli_prepare($con, $query);
            $stmt->bind_param("dddd", $lng, $lng, $lat, $lat);
            $stmt->execute();
            $res = $stmt->get_result();
            if ($res->num_rows == 0) {
                $_SESSION["msg"] = "Address out of range";
                header("Location: error.php");
                exit;
            }
            $row = $res->fetch_assoc();
            $blockid = $row["blockid"];
            $hoodid = $row["hoodid"];

            $query = "delete from join_block where userid=?";
            $stmt = mysqli_prepare($con, $query);
            $stmt->bind_param("i", $id);
            $stmt->execute();

            $quert = "delete from agree_block where joiner_id=?";
            $stmt = mysqli_prepare($con, $query);
            $stmt->bind_param("i", $id);
            $stmt->execute();

            $query = "insert into join_block(userid, blockid) values(?,?)";
            $stmt = mysqli_prepare($con, $query);
            $stmt->bind_param("ii", $id, $blockid);
            $stmt->execute();

            $blockid = null;
            $hoodid = null;
        }
        $username = $row["username"];
        $query = "update `user` set email=?, description=?, latitude=?, longitude=?, address=?, blockid=?, hoodid=? where userid=?";
        $stmt = mysqli_prepare($con, $query);
        $stmt->bind_param("ssddsiii", $email, $description, $lat, $lng, $addr, $blockid, $hoodid, $id);
        $stmt->execute();
    } else {
        if (isset($_SESSION["id"])) {
            # click main
            $id = $_SESSION["id"];
            $con = mysqli_connect("127.0.0.1:3306", "root", "", "Neighbourhood");
            if (!$con) {
                die("connection failed");
            }
            $query = "select address, username, email, blockid, description, hoodid from `user` where userid = ?";
            $stmt = mysqli_prepare($con, $query);
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $res = $stmt->get_result();
            $row = $res->fetch_assoc();
            $username = $row["username"];
            $email = $row["email"];
            $blockid = $row["blockid"];
            $hoodid = $row["hoodid"];
            $description = $row["description"];
            $addr = $row["address"];
            $_SESSION["id"] = $id;
            $_SESSION["username"] = $username;
        } else {
            $_SESSION["msg"] = "out of session";
            header("Location: ../php/error.php");
        }
    }
?>
<div class="jumbotron">
    <div class="container">
        <h1>Hello <?php echo $username?>, this is your main page</h1>
    </div>
</div>
<div class="container">
    <table class="table">
        <form action="main.php" method="post">
            <thead><tr><td>Attribute</td><td>Value</td><td>Change to</td></tr></thead>
            <tbody>
            <?php
            $query = "select hoodname from hood where hoodid=?";
            $con = mysqli_connect("127.0.0.1:3306", "root", "", "Neighbourhood");
            $stmt = mysqli_prepare($con, $query);
            $stmt->bind_param("i", $hoodid);
            $stmt->execute();
            $res = $stmt->get_result();
            if ($res->num_rows != 0) {
                $row = $res->fetch_assoc();
                $hood = $row["hoodname"];
            } else {
                $hood = "not available";
            }

            $query = "select blockname from blocks where blockid=?";
            $stmt = mysqli_prepare($con, $query);
            $stmt->bind_param("i", $blockid);
            $stmt->execute();
            $res = $stmt->get_result();
            if ($res->num_rows != 0) {
                $row = $res->fetch_assoc();
                $block = $row["blockname"];
            } else {
                $block = "not available";
            }

            echo "<tr><td>Name</td><td>".$username."</td><td>-</td></tr>";
            echo "<tr><td>email</td><td>".$email."</td><td><input type='text' name='email'></td></tr>";
            echo "<tr><td>block</td><td>".$block."</td><td>-</td></tr>";
            echo "<tr><td>hood</td><td>".$hood."</td><td>-</td></tr>";
            echo "<tr><td>address</td>";
            echo "<td>".$addr."</td>";
            echo "<td><input id='pac-input' class='controls' type='text' placeholder='Search Box'><div id='map'></div></td></tr>";
            echo "<tr><td>description</td><td>".$description."</td><td><input type='text' name='description'></td></tr>";
            echo "<tr><td></td><td></td><td><input class='btn btn-primary' type='submit' value='change'></td></tr>";
            echo "<input type='hidden' name='change_value' value='yes'>";
            echo "<input type='hidden' id='lat' name='lat' value=''>";
            echo "<input type='hidden' id='lng' name='lng'>";
            echo "<input type='hidden' id='addr' name='addr' value=''>";
            ?>
            </tbody>
        </form>
    </table>
</div>
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
                document.getElementById("addr").value = input.value;
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
