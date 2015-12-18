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
    <title>Send Message</title>
</head>
<body>
<?php include('../html/navbar.html');
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

$receiver = $_POST["receiver"];
$receiver_id = $_POST["receiver_id"];
echo "<h2>Say something to your neighboorhoods.</h2>";
echo "<form class='form-horizontal' action='read_message.php' method='post'>";
echo "<input type='hidden' name='receiver_id' value=".$receiver_id.">";
echo "<input type='hidden' name='receiver' value=".$receiver.">";
echo "<div class='form-group'>";
echo "<label class='col-sm-2 control-label'>TO</label>";
echo "<div class='col-sm-10'>";
echo "<p class='form-control-static'>".$receiver."</p>";
echo "</div>";
echo "</div>";
echo "<div class='form-group'>";
echo "<label class='col-sm-2 control-label'>Title</label>";
echo "<div class='col-sm-10'>";
echo "<input type='text' name='title' placeholder='Title' class='form-control'>";
echo "</div>";
echo "</div>";
echo "<div class='form-group'>";
echo "<label class='col-sm-2 control-label'>Content</label>";
echo "<div class='col-sm-10'>";
echo "<textarea placeholder='Say something' name='content' class='form-control' rows='6'></textarea>";
echo "</div>";
echo "</div>";
echo "<div class='form-group'>";
echo "<div class='col-sm-11'></div>";
echo "<div class='col-sm-1'><input type='submit' class='btn btn-primary' value='send'></div>";
echo "</div>";
echo "</form>";

echo "</div></div>";
?>
<script src="//cdn.bootcss.com/jquery/1.11.3/jquery.min.js"></script>
<script src="//cdn.bootcss.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
</body>
</html>