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
$con = mysqli_connect("127.0.0.1:3306", "root", "", "Neighbourhood");
if (!$con) {
    die("connection failed");
}
if (isset($_POST["b_userid"])) {
    $b_userid = $_POST["f_userid"];
    $query = "update join_block set approve_num = approve_num + 1 where userid=?";
    $stmt = mysqli_prepare($con, $query);
    $stmt->bind_param("i", $b_userid);
    $stmt->execute();
}

$query = "select u2.userid, u2.username, u2.email, u1.blockid ".
    "from `user` as u1, `user` as u2, join_block where u1.blockid = join_block.blockid and ".
    "join_block.userid = u2.userid and u1.userid=?";
$stmt = mysqli_prepare($con, $query);
$stmt->bind_param("i", $id);
$stmt->execute();
$res = $stmt->get_result();
echo "<div class='jumbotron'>";
echo "<div class='container'>";
if ($res->num_rows == 0) {
    echo "<h2>Oh, you currently have no block reqeusts</h2>";
} else {
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