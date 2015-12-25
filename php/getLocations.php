<?php
    session_start();
    if (isset($_GET["type"])) {
        $type = $_GET["type"];
    }

    if (isset($_SESSION["id"])) {
        $id = $_SESSION["id"];
    } else {
        $_SESSION["msg"] = "out of session";
        exit;
    }
    $con = mysqli_connect("127.0.0.1:3306", "root", "", "Neighbourhood");
    if (!$con) {
        die("connection failed");
    }

    // deal with different type of name & locations needed
    if ($type == "hood") {
        $query = "select username, latitude, longitude
                    from user
                    where hoodid = (select hoodid
                                    from user
                                    where userid = ?)";
        $stmt = mysqli_prepare($con, $query);
        $stmt->bind_param("i", $id);
        $stmt->execute();

    } else if ($type == "block") {
        $query = "select username, latitude, longitude
                    from user
                    where blockid = (select blockid
                                    from user
                                    where userid = ?)";
        $stmt = mysqli_prepare($con, $query);
        $stmt->bind_param("i", $id);
        $stmt->execute();
    } else if ($type == "neighbor") {
        $query = "select username, latitude, longitude
                    from user, neighbor
                    where user.userid = neighbor.neighbor_id and neighbor.host_id = ?";
        $stmt = mysqli_prepare($con, $query);
        $stmt->bind_param("i", $id);
        $stmt->execute();
    } else {
        $query = "select username, latitude, longitude
                    from user, friendship
                    where (friendship.user1 = ? and user.userid = friendship.user2) or (friendship.user2 = ? and user.userid = friendship.user1)";
        $stmt = mysqli_prepare($con, $query);
        $stmt->bind_param("ii", $id, $id);
        $stmt->execute();
    }

    $result = $stmt->get_result();
    $locations = array();
    while ($row = $result->fetch_assoc()) {
        $locations[] = $row;
    }
    echo json_encode($locations);

    mysqli_close($con);
?>
