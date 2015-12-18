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
    <title>Sign in</title>
</head>
<body>
<?php include('../html/navbar_simple.html'); ?>
<div class="jumbotron">
    <div class="container">
        <h2>Please kindly input your information.</h2>
        <div class="bs-example" data-example-id="basic-forms">
            <form class="form-horizontal" action="main.php" method="post">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Email</label>
                    <div class="col-sm-4">
                        <input type="email" class="form-control" name="email" placeholder="Email">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">Password</label>
                    <div class="col-sm-4">
                        <input type="password" class="form-control" name="password" placeholder="Password">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">Name</label>
                    <div class="col-sm-4">
                        <input type="text" class="form-control" name="username" placeholder="Name">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">Address</label>
                    <!-- add address to hidden value-->
                    <input id="address" value="" type="hidden" class="form-control" name="address">
                    <div class="col-sm-4">
                        <input id="pac-input" class="controls" type="text" placeholder="Search Box">
                        <div id="map"></div>
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
                                    document.getElementById("address").value = input.value;
                                    document.getElementById("lat").value = place.geometry.location.lat();
                                    document.getElementById("lng").value = place.geometry.location.lng();
                                });
                                map.fitBounds(bounds);
                            });
                            // [END region_getplaces]
                        }
                    </script>
                </div>
                <!-- add latitude and longtitude to hidden value-->
                <input id="lat" value="" type="hidden" class="form-control" name="lat">
                <input id="lng" value="" type="hidden" class="form-control" name="lng">
                <div class="form-group">
                    <label class="col-sm-2 control-label">Description</label>
                    <div class="col-sm-4">
                        <textarea class="form-control" name="description" placeholder="Description" rows="3"></textarea>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label"></label>
                    <input type="submit" class="btn btn-primary">
                </div>
            </form>
        </div>
    </div>
</div>
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBHrZZCQYPQYXl1Hx7MYuayCZNd0hX8CFE&libraries=places&callback=initAutocomplete"
        async defer></script>
<script src="//cdn.bootcss.com/jquery/1.11.3/jquery.min.js"></script>
<script src="//cdn.bootcss.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
</body>
</html>