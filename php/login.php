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
    <title>Log in</title>
</head>
<body>
<?php include('../html/navbar_simple.html'); ?>
<div class="jumbotron">
    <div class="container">
        <h2>Log in</h2>
            <form class="form-horizontal" action="main.php" method="post">
                <div class="form-group">
                    <label class="col-sm-2 control-label">ID</label>
                    <div class="col-sm-4">
                        <input type="number" class="form-control" name="id" placeholder="ID">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">Password</label>
                    <div class="col-sm-4">
                        <input type="password" class="form-control" name="pwd" placeholder="Password">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label"></label>
                    <input type="submit" class="btn btn-primary">
                </div>
            </form>
    </div>
</div>
<script src="//cdn.bootcss.com/jquery/1.11.3/jquery.min.js"></script>
<script src="//cdn.bootcss.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
</body>
</html>