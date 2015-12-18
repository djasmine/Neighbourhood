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
    <title>Hood</title>
</head>
<body>
<?php
    include('../html/navbar.html');
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

    if (isset($_POST["friend_id"])) {
        #solve the add friend
        $friend_id = $_POST["friend_id"];
        $query = "insert into friend_request(user1, user2) values(?,?)";
        $stmt = mysqli_prepare($con, $query);
        $stmt->bind_param("ii", $id, $friend_id);
        $stmt->execute();
    } else if (isset($_POST["neighbor_id"])) {
        #solve the add neighbor
        $neighbor_id = $_POST["neighbor_id"];
        $query = "insert into neighbor(host_id, neighbor_id) values(?,?)";
        $stmt = mysqli_prepare($con, $query);
        $stmt->bind_param("ii", $id, $neighbor_id);
        $stmt->execute();
    }

    $query0 = "select hoodid from `user` where userid=?";
    $stmt0 = mysqli_prepare($con, $query0);
    $stmt0->bind_param("i", $id);
    $stmt0->execute();
    $res0 = $stmt0->get_result();
    $row = $res0->fetch_assoc();
    if ($row["hoodid"] == null) {
        echo "<h3>Because you are not approve to join the hood, we can not list the people living in the hood.</h3>";
    }
    else {
        $hoodid = $row["hoodid"];
        $query1 = "select userid, username, email from `user` where hoodid=?";
        $stmt1 = mysqli_prepare($con, $query1);
        $stmt1->bind_param("i", $hoodid);
        $stmt1->execute();
        $res1 = $stmt1->get_result();
        if ($res1->num_rows > 1) {
            echo "<h3>Hey, here are people living in your hood</h3>";
            echo "<div class='container'>";
            echo "<form class='form-horizontal' action='send_message.php' method='post'>";
            echo "<input type='hidden' value='HOOD' name='receiver'>";
            echo "<input type='hidden' value='' name='receiver_id'>";
            echo "<div class='col-sm-9'></div>";
            echo "<div class='col-sm-1'>";
            echo "<input class='btn btn-success' type='submit' value='send to entire hood'>";
            echo "</div>";
            echo "</form>";
            echo "</div>";
            echo "<table class='table'>";
            echo "<thead><tr><th>name</th><th>email</th><th>add friend</th><th>add neighbor</th></tr></thead>";
            echo "<tbody>";
            while ($row = $res1->fetch_assoc()) {
                if ($row["userid"] != $id) {
                    echo "<tr><th>" . $row["username"] . "</th><th>" . $row["email"] . "</th>";
                    # add friend button
                    $query = "select user1 from friendship where (user1=? and user2=?) or (user1=? and user2=?)";
                    $stmt = mysqli_prepare($con, $query);
                    $stmt->bind_param("iiii", $id, $row["userid"], $row["userid"], $id);
                    $stmt->execute();
                    $res = $stmt->get_result();

                    if ($res->num_rows == 0) {
                        echo "<form action='hood.php' method='post'>";
                        echo "<input type='hidden' name='friend_id' value=".$row["userid"].">";
                        echo "<th><input type='submit' value='add' class='btn btn-primary'></th>";
                        echo "</form>";
                    } else {
                        echo "<th>-</th>";
                    }

                    #add neighbor button
                    $query = "select host_id from neighbor where host_id=? and neighbor_id=?";
                    $stmt = mysqli_prepare($con, $query);
                    $stmt->bind_param("ii", $id, $row["userid"]);
                    $stmt->execute();
                    $res = $stmt->get_result();
                    if ($res->num_rows == 0) {
                        echo "<form action='hood.php' method='post'>";
                        echo "<input type='hidden' name='neighbor_id' value=".$row["userid"].">";
                        echo "<th><input type='submit' value='add' class='btn btn-primary'></th>";
                        echo "</form>";
                    } else {
                        echo "<th>-</th>";
                    }

                    echo "</tr>";
                }
            }
            echo "</tbody></table>";
        }
        else {
            echo "<h3>Sorry, we did not find anyone living in your hood.</h3>";
        }
    }
    echo "</div></div>";
?>
<script src="//cdn.bootcss.com/jquery/1.11.3/jquery.min.js"></script>
<script src="//cdn.bootcss.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
</body>
</html>