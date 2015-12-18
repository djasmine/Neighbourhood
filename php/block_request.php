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
    <title>Block Request</title>
</head>
<body>
<?php include('../html/navbar.html');
$id = $_SESSION["id"];
echo "<div class='jumbotron'>";
echo "<div class='container'>";
$con = mysqli_connect("127.0.0.1:3306", "root", "", "Neighbourhood");
if (!$con) {
    die("connection failed");
}
if (isset($_POST["b_userid"])) {
    $b_userid = $_POST["b_userid"];
    $query = "select blockid, approve_num from join_block where userid=?";
    $stmt = mysqli_prepare($con, $query);
    $stmt->bind_param("i", $b_userid);
    $stmt->execute();
    $res = $stmt->get_result();
    $row = $res->fetch_assoc();
    $app_num = $row["approve_num"];
    $blockid = $row["blockid"];

    #total user num
    $query = "select blockid, count(blockid) as cnt from `user` where blockid=? group by blockid";
    $stmt = mysqli_prepare($con, $query);
    $stmt->bind_param("i", $blockid);
    $stmt->execute();
    $res = $stmt->get_result();
    $row = $res->fetch_assoc();
    $total_num_of_block = $row["cnt"];

    if ($app_num == 2 || $app_num == $total_num_of_block - 1) {
        $query = "delete from join_block where userid=?";
        $stmt = mysqli_prepare($con, $query);
        $stmt->bind_param("i", $b_userid);
        $stmt->execute();

        $query = "select blockid, hoodid from `user` where userid=?";
        $stmt = mysqli_prepare($con, $query);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $res = $stmt->get_result();
        $row = $res->fetch_assoc();
        $blockid = $row["blockid"];
        $hoodid = $row["hoodid"];

        $query = "update `user` set blockid=?, hoodid=? where userid=?";
        $stmt = mysqli_prepare($con, $query);
        $stmt->bind_param("iii", $blockid, $hoodid, $b_userid);
        $stmt->execute();
    } else {
        $query = "update join_block set approve_num = approve_num + 1 where userid=?";
        $stmt = mysqli_prepare($con, $query);
        $stmt->bind_param("i", $b_userid);
        $stmt->execute();
    }
    $query = "insert into agree_block(host_id, joiner_id) values(?,?)";
    $stmt = mysqli_prepare($con, $query);
    $stmt->bind_param("ii", $id, $b_userid);
    $stmt->execute();
}

$query = "select u2.userid, u2.username, u2.email, u1.blockid ".
    "from `user` as u1, `user` as u2, join_block where u1.blockid = join_block.blockid and ".
    "join_block.userid = u2.userid and u1.userid=? and u2.userid not in ".
    "(select joiner_id from agree_block where host_id=?)";
$stmt = mysqli_prepare($con, $query);
$stmt->bind_param("ii", $id, $id);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows == 0) {
    echo "<h2>Oh, you currently have no block reqeusts</h2>";
} else {
    echo "<h2>They want to join your block.</h2>";
    echo "<table class='table'>";
    echo "<thead><tr><td>Name</td><td>Email</td><td>Accept</td></tr></thead>";
    echo "<tbody>";
    while ($row = $res->fetch_assoc()) {
        echo "<tr><td>".$row["username"]."</td><td>".$row["email"]."</td>";
        echo "<form action='block_request.php' method='post'>";
        echo "<input type='hidden' name='b_userid' value=".$row["userid"].">";
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