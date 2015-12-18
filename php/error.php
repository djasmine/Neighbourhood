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
    <title>Error Page</title>
</head>
<body>
<div class="jumbotron">
    <div class="container">
<?php include("../html/navbar_simple.html");
    echo "<h1>".$_SESSION["msg"]."</h1>";
?>
    <a href="../index.php" class="btn btn-primary btn-lg">main page</a>

    <script src="//cdn.bootcss.com/jquery/1.11.3/jquery.min.js"></script>
    <script src="//cdn.bootcss.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
    </div>
</div>
</body>