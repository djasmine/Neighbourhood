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
    <title>Send Message</title>
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

$receiver = $_POST["receiver"];
$receiver_id = $_POST["receiver_id"];
echo "<h2>Say something to your neighboorhoods.</h2>";
echo "<form class='form-horizontal' action='read_message.php' method='post'>";
echo "<input type='hidden' name='receiver_id' value=".$receiver_id.">";
echo "<input type='hidden' name='receiver' value=".$receiver.">";
echo "<input type='hidden' id='lat' name='lat' value=''>";
echo "<input type='hidden' id='lng' name='lng'>";
echo "<input type='hidden' id='address' name='address' value=''>";
echo "<div class='form-group'>";
echo "<label class='col-sm-2 control-label'>TO</label>";
echo "<div class='col-sm-10'>";
echo "<p class='form-control-static'>".$receiver."</p>";
echo "</div>";
echo "</div>";
echo "<div class='form-group'>";
echo "<label class='col-sm-2 control-label'>Title</label>";
echo "<div class='col-sm-10'>";
echo "<input type='text' name='title' placeholder='Title' class='form-control'>";
echo "</div>";
echo "</div>";
echo "<div class='form-group'>";
echo "<label class='col-sm-2 control-label'>Content</label>";
echo "<div class='col-sm-10'>";
echo "<textarea placeholder='Say something' name='content' class='form-control' rows='6'></textarea>";
echo "</div>";
echo "</div>";
echo "<div class='form-group'>";
echo "<div class='col-sm-11'></div>";
echo "<div class='col-sm-1'><input type='submit' class='btn btn-primary' value='send'></div>";
echo "</div>";
echo "<div class='form-group'>";
echo "<div class='col-sm-3'></div>";
echo "<div class='col-sm-5'>";
echo "<input id='pac-input' class='controls' type='text' placeholder='Search Box'><div id='map'></div>";
echo "</div>";
echo "<div class='col-sm-4'></div>";
echo "</div>";
echo "</form>";

echo "</div></div>";
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