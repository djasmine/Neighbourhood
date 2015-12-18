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
    <title>Friend Request</title>
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
if (isset($_POST["f_userid"])) {
    $f_userid = $_POST["f_userid"];
    $query = "delete from friend_request where user1=? and user2=?";
    $stmt = mysqli_prepare($con, $query);
    $stmt->bind_param("ii", $f_userid, $id);
    $stmt->execute();

    $query = "insert into friendship(user1, user2) values(?,?)";
    $stmt = mysqli_prepare($con, $query);
    $stmt->bind_param("ii", $f_userid, $id);
    $stmt->execute();
}

$query = "select userid, username, email from `user`, friend_request where user2=? and user1=userid";
$stmt = mysqli_prepare($con, $query);
$stmt->bind_param("i", $id);
$stmt->execute();
$res = $stmt->get_result();
echo "<div class='jumbotron'>";
echo "<div class='container'>";
if ($res->num_rows == 0) {
    echo "<h2>Oh, you currently have no friend reqeusts</h2>";
} else {
    echo "<h2>They want to be your friends.</h2>";
    echo "<table class='table'>";
    echo "<thead><tr><td>Name</td><td>Email</td><td>Accept</td></tr></thead>";
    echo "<tbody>";
    while ($row = $res->fetch_assoc()) {
        echo "<tr><td>".$row["username"]."</td><td>".$row["email"]."</td>";
        echo "<form action='friend_request.php' method='post'>";
        echo "<input type='hidden' name='f_userid' value=".$row["userid"].">";
        echo "<td><input type='submit' class='btn btn-primary' value='accept'></td>";
        echo "</form></tr>";
    }
    echo "</tbody>";
    echo "</table>";
}
echo "</div></div>"
?>
<script src="//cdn.bootcss.com/jquery/1.11.3/jquery.min.js"></script>
<script src="//cdn.bootcss.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
</body>
</html>