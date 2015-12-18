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
    <title>Read Message</title>
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
echo "<div class='jumbotron'>";
echo "<div class='container'>";
#insert a new message
if (isset($_POST["receiver_id"])) {
    $receiver = $_POST["receiver"];
    $receiver_id = [];
    if ($receiver == "FRIEND" || $receiver == "NEIGHBOR") {
        array_push($receiver_id, $_POST["receiver_id"]);
        array_push($receiver_id, $id);
    } else if ($receiver == "ALL_FRIENDS") {
        $query = "select user1 as uid from `friendship` where user2=? union select user2 as uid from `friendship` where user1=?";
        $stmt = mysqli_prepare($con, $query);
        $stmt->bind_param("ii", $id, $id);
        $stmt->execute();
        $res = $stmt->get_result();
        while ($row = $res->fetch_assoc()) {
            array_push($receiver_id, $row["uid"]);
        }
    } else if ($receiver == "BLOCK") {
        $query = "select userid as uid from `user` where blockid=(select blockid from `user` where userid=?)";
        $stmt = mysqli_prepare($con, $query);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $res = $stmt->get_result();
        while ($row = $res->fetch_assoc()) {
            array_push($receiver_id, $row["uid"]);
        }
    } else if ($receiver == "HOOD") {
        $query = "select userid as uid from `user` where hoodid=(select hoodid from `user` where userid=?)";
        $stmt = mysqli_prepare($con, $query);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $res = $stmt->get_result();
        while ($row = $res->fetch_assoc()) {
            array_push($receiver_id, $row["uid"]);
        }
    }
    if (count($receiver_id) == 1) {
        $_SESSION["msg"] = "You currently cannot send message to this group.";
        header("Location: error.php");
    }
    $query = "select max(mid) as max_mid from message";
    $stmt = mysqli_prepare($con, $query);
    $stmt->execute();
    $res = $stmt->get_result();
    $row = $res->fetch_assoc();
    $mid = $row["max_mid"] + 1;
    $content = $_POST["content"];
    $title = $_POST["title"];
    $m_date = date("Y-m-d H:i:s");
    $query = "insert into message(mid, m_number, author, send_time, title, content, location, receiver) values(?,0,?,?,?,?,null,?)";
    $stmt = mysqli_prepare($con, $query);
    $stmt->bind_param("iissss", $mid, $id, $m_date, $title, $content, $receiver);
    $stmt->execute();

    $arrlen = count($receiver_id);
    for ($i = 0; $i < $arrlen; $i++) {
        $query = "insert into feed(mid, userid, fstatus) values(?,?,'unread')";
        $stmt = mysqli_prepare($con, $query);
        $stmt->bind_param("ii", $mid, $receiver_id[$i]);
        $stmt->execute();
    }
} else if (isset($_POST["content"])) {
    #reply to a message
    $mid = $_POST["mid"];
    $content = $_POST["content"];
    $query = "select mid, title, receiver, max(m_number) as m_num from message where mid=? group by mid, title, receiver";
    $stmt = mysqli_prepare($con, $query);
    $stmt->bind_param("i", $mid);
    $stmt->execute();
    $res = $stmt->get_result();
    $row = $res->fetch_assoc();
    $m_number = $row["m_num"] + 1;
    $m_date = date("Y-m-d H:i:s");
    $receiver = $row["receiver"];
    $title = $row["title"];

    $query = "insert into message(mid, m_number, author, send_time, title, content, location, receiver) values(?,?,?,?,?,?,null,?)";
    $stmt = mysqli_prepare($con, $query);
    $stmt->bind_param("iiissss", $mid, $m_number, $id, $m_date, $title, $content, $receiver);
    $stmt->execute();

    $query = "update feed set fstatus='unread' where mid=?";
    $stmt = mysqli_prepare($con, $query);
    $stmt->bind_param("i", $mid);
    $stmt->execute();
} else {
    $mid = $_POST["mid"];
}

#web page
$query = "update feed set fstatus='read' where mid=? and userid=?";
$stmt = mysqli_prepare($con, $query);
$stmt->bind_param("ii", $mid, $id);
$stmt->execute();

$query1 = "select author, username, title, send_time, content, location, receiver from message, `user`".
    " where `user`.userid = author and mid = ? and m_number = 0";
$stmt1 = mysqli_prepare($con, $query1);
$stmt1->bind_param("i", $mid);
$stmt1->execute();
$res1 = $stmt1->get_result();
$row1 = $res1->fetch_assoc();
echo "<h1>".$row1["title"]."</h1>";
echo "<h4>Author: ".$row1["username"]."   ".$row1["send_time"]."   ".$row1["receiver"]."</h4>";
echo "<p>".$row1["content"]."</p>";
echo "</div></div>";

echo "<div class='container'>";
echo "<form class='form-horizontal' action='read_message.php' method='post'>";
echo "<div class='form-group'>";
echo "<label class='col-sm-2' control-label>You say:</label>";
echo "<div class='col-sm-8'>";
echo "<textarea class='form-control' name='content' placeholder='Say something...' rows=\"5\"></textarea>";
echo "</div>";
echo "<div class='col-sm-2'>";
echo "<input type='submit' class='btn btn-primary'>";
echo "</div>";
echo "</div>";
echo "<input type='hidden' name='mid' value=".$mid.">";
echo "</form>";

$query2 = "select m_number, author, username, title, send_time, content, location, receiver from message, `user`".
    " where `user`.userid = author and mid = ? and m_number > 0 order by m_number DESC";
$stmt2 = mysqli_prepare($con, $query2);
$stmt2->bind_param("i", $mid);
$stmt2->execute();
$res2 = $stmt2->get_result();
if ($res2->num_rows == 0) {
    echo "<h2>There are no replies for this message.</h2>";
} else {
    echo "<table class='table'>";
    echo "<thead><tr><th>User</th><th>Content</th><th>time</th></tr></thead>";
    echo "<tbody>";
    while ($row2 = $res2->fetch_assoc()) {
        echo "<tr><td>".$row2["username"]."</td><td>".$row2["content"]."</td><td>".$row2["send_time"]."</td></tr>";
    }
    echo "</tbody></table>";
}

echo "</div>";
?>
<script src="//cdn.bootcss.com/jquery/1.11.3/jquery.min.js"></script>
<script src="//cdn.bootcss.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
</body>
</html>