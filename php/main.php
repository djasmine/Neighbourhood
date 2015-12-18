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

    <title>Home</title>
</head>
<body>
<?php include('../html/navbar.html');
    if (isset($_POST["change_value"])) {
        #change some value
        $id = $_SESSION["id"];
        $email = $_POST["email"];
        $description = $_POST["description"];
        $con = mysqli_connect("127.0.0.1:3306", "root", "", "Neighbourhood");
        if (!$con) {
            die("connection failed");
        }
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

        $query = "select username, email, description, blockid, hoodid from `user` where userid=?";
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
        $blockid = $row["blockid"];
        $username = $row["username"];
        $hoodid = $row["hoodid"];
        $query = "update `user` set email=?, description=? where userid=?";
        $stmt = mysqli_prepare($con, $query);
        $stmt->bind_param("ssi", $email, $description, $id);
        $stmt->execute();
    } else {
        if (isset($_SESSION["id"])) {
            # click main
            $id = $_SESSION["id"];
            $con = mysqli_connect("127.0.0.1:3306", "root", "", "Neighbourhood");
            if (!$con) {
                die("connection failed");
            }
            $query = "select username, email, blockid, description, hoodid from `user` where userid = ?";
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

            echo "<tr><td>Name</td><td>".$username."</td><td>Can not change</td></tr>";
            echo "<tr><td>email</td><td>".$email."</td><td><input type='text' name='email'></td></tr>";
            echo "<tr><td>block</td><td>".$block."</td><td>Can not change</td></tr>";
            echo "<tr><td>hood</td><td>".$hood."</td><td>Can not change</td></tr>";
            echo "<tr><td>description</td><td>".$description."</td><td><input type='text' name='description'></td></tr>";
            echo "<tr><td></td><td></td><td><input class='btn btn-primary' type='submit' value='change'></td></tr>";
            echo "<input type='hidden' name='change_value' value='yes'>"

            ?>
            </tbody>
        </form>
    </table>
</div>
<script src="//cdn.bootcss.com/jquery/1.11.3/jquery.min.js"></script>
<script src="//cdn.bootcss.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
</body>
</html>
