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
$con = mysqli_connect("127.0.0.1:3306", "root", "", "Neighbourhood");
if (!$con) {
    die("connection failed");
}
else if (isset($_POST["description"])) {
    # new user
    $description = $_POST["description"];
    $username = $_POST["username"];
    $pwd = $_POST["password"];
    $blockid = $_POST["block"];
    $hoodid = $_POST["hood"];
    $email = $_POST["email"];
    #check block right
    $query0 = "select blockname from blocks where blockid=? and hoodid=?";
    $stmt0 = mysqli_prepare($con, $query0);
    $stmt0->bind_param("ii", $blockid, $hoodid);
    $stmt0->execute();
    $res0 = $stmt0->get_result();
    if ($res0->num_rows == 0) {
        $_SESSION["msg"] = "wrong block id or hood id";
        header("Location: ../php/error.php");
        exit;
    }
    # check email
    $query = "select userid from `user` where email=?";
    $stmt = mysqli_prepare($con, $query);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($res->num_rows > 0) {
        $_SESSION["msg"] = "Your email has been used in this site.";
        header("Location: error.php");
        exit;
    }

    $query = "select max(userid) as uid from `user`";
    $stmt = mysqli_prepare($con, $query);
    $stmt->execute();
    $res = $stmt->get_result();
    $row = $res->fetch_assoc();
    $id = $row["uid"] + 1;
    $query1 = "insert into `user`(userid, username, email, description, blockid, hoodid, password) values(?,?,?,?,null,null,?)";
    $stmt1 = mysqli_prepare($con, $query1);
    $stmt1->bind_param("issss", $id, $username, $email, $description, $pwd);
    $stmt1->execute();
    $query2 = "insert into join_block(userid, blockid, approve_num) values(?,?,0)";
    $stmt2 = mysqli_prepare($con, $query2);
    $stmt2->bind_param("ii", $id, $blockid);
    $stmt2->execute();
    $_SESSION["id"] = $id;
    $hoodid = -1;
    $blockid = -1;
}
else if (isset($_POST["email"])) {
    # log in
    $email = $_POST["email"];
    $pwd = $_POST["pwd"];
    $query = "select userid, username, blockid, description, hoodid from `user` where email=? and password=?";
    $stmt = mysqli_prepare($con, $query);
    $stmt->bind_param("ss", $email, $pwd);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($res->num_rows == 0) {
        $_SESSION["msg"] = "wrong user id or password";
        header("Location: ../php/error.php");
        exit;
    }
    $row = $res->fetch_assoc();
    $username = $row["username"];
    $blockid = $row["blockid"];
    $hoodid = $row["hoodid"];
    $description = $row["description"];
    $id = $row["userid"];
    $_SESSION["id"] = $id;
    $_SESSION["username"] = $username;
} else {
    $id = $_SESSION["id"];
}

$pattern = "%";
if (isset($_POST["keyword"])) {
    $pattern = "%".$_POST["keyword"]."%";
}

$query1 = "select mid, max(send_time) as t from feed natural join ".
    "(select distinct mid, send_time from message where content like ?) as z where userid=? group by mid order by t DESC";
$stmt1 = mysqli_prepare($con, $query1);
$stmt1->bind_param("si", $pattern, $id);
$stmt1->execute();
$res1 = $stmt1->get_result();
if ($res1->num_rows == 0) {
    echo "<h3>Sorry, there are no messages for you.</h3>";
} else {
    echo "<h3>Hey, here are messages for you.</h3><br>";
    echo "<form class='form-horizontal' action='message.php' method='post'>";
    echo "<label class='col-sm-2 control-label'>You can search:</label>";
    echo "<div class='col-sm-8'>";
    echo "<input type='text' class='form-control' name='keyword' placeholder='key word'>";
    echo "</div>";
    echo "<div class='col-sm-2'>";
    echo "<input type='submit' class='btn btn-primary' value='search'>";
    echo "</div>";
    echo "</form>";
    echo "</div></div>";

    echo "<div class='container'>";
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
echo "</div>";
?>
<script src="//cdn.bootcss.com/jquery/1.11.3/jquery.min.js"></script>
<script src="//cdn.bootcss.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
</body>
</html>