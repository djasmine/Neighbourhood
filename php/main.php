<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="http://libs.baidu.com/bootstrap/3.0.3/css/bootstrap.min.css" rel="stylesheet">
    <script src="http://libs.baidu.com/jquery/2.0.0/jquery.min.js"></script>
    <script src="http://libs.baidu.com/bootstrap/3.0.3/js/bootstrap.min.js"></script>
    <title>Home</title>
</head>
<body>
<?php include('../html/navbar.html');
    if ($_POST["id"] != null) {
        $id = $_POST["id"];
        $pwd = $_POST["pwd"];
        $con = mysqli_connect("127.0.0.1:3306", "root", "", "Neighbourhood");
        if (!$con) {
            die("connection failed");
        }
        $query = "select username, email, blockid, hoodid from `user` where userid = ? and password = ?";
        $stmt = mysqli_prepare($con, $query);
        $stmt->bind_param("is", $id, $pwd);
        $stmt->execute();
        $res = $stmt->get_result();
        if ($res->num_rows == 0) {
            $_SESSION["msg"] = "wrong user id or password";
            header("Location: ../php/error.php");
            exit;
        }
        $row = $res->fetch_assoc();
        $username = $row["username"];
        $email = $row["email"];
        $blockid = $row["blockid"];
        $hoodid = $row["hoodid"];
        $_SESSION["id"] = $id;
        $_SESSION["username"] = $username;
    } else {
        if ($_SESSION["id"] != null) {
            $con = mysqli_connect("127.0.0.1:3306", "root", "", "Neighbourhood");
            if (!$con) {
                die("connection failed");
            }
            $query = "select username, email, blockid, hoodid from `user` where userid = ?";
            $stmt = mysqli_prepare($con, $query);
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $res = $stmt->get_result();
            $row = $res->fetch_assoc();
            $username = $row["username"];
            $email = $row["email"];
            $blockid = $row["blockid"];
            $hoodid = $row["hoodid"];
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
</body>
</html>
