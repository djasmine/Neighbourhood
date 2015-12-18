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
    <title>Message</title>
</head>
<body>
<?php include('../html/navbar.html');
echo "<div class='jumbotron'>";
echo "<div class='container'>";
$id = $_SESSION["id"];
$con = mysqli_connect("127.0.0.1:3306", "root", "", "Neighbourhood");
if (!$con) {
    die("connection failed");
}
$query1 = "select mid, max(send_time) as t from feed natural join message where userid=? group by mid order by t DESC";
$stmt1 = mysqli_prepare($con, $query1);
$stmt1->bind_param("i", $id);
$stmt1->execute();
$res1 = $stmt1->get_result();
if ($res1->num_rows == 0) {
    echo "<h3>Sorry, there are no messages for you.</h3>";
} else {
    echo "<h3>Hey, here are messages for you.</h3>";
    echo "<table class='table'>";
    echo "<thead><tr><th>title</th><th>author</th><th>publish time</th><th>recent reply time</th><th>type</th>".
        "<th>read status</th><th>read</th></tr></thead>";
    echo "<tbody>";
    while ($row = $res1->fetch_assoc()) {
        $mid = $row["mid"];
        $last_time = $row["t"];
        $query = "select author, send_time, title, receiver, fstatus, username from message natural join feed, `user` ".
            "where m_number=0 and mid=? and feed.userid=? and `user`.userid=author";
        $stmt = mysqli_prepare($con, $query);
        $stmt->bind_param("ii", $mid, $id);
        $stmt->execute();
        $res = $stmt->get_result();
        $msg_row = $res->fetch_assoc();
        echo "<form action='read_message.php' method='post'>";
        echo "<tr><th>".$msg_row["title"]."</th>";
        echo "<th>".$msg_row["username"]."</th>";
        echo "<th>".$msg_row["send_time"]."</th>";
        echo "<th>".$last_time."</th>";
        echo "<th>".$msg_row["receiver"]."</th>";
        echo "<th>".$msg_row["fstatus"]."</th>";
        echo "<th><input type='hidden' name='mid' value='".$mid."'>";
        echo "<input type='submit' class='btn btn-primary' value='read'></th>";
        echo "</tr></form>";
    }
    echo "</tbody></table>";
}

?>
<script src="//cdn.bootcss.com/jquery/1.11.3/jquery.min.js"></script>
<script src="//cdn.bootcss.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
</body>
</html>