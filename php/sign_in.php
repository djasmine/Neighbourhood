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
    <title>Sign in</title>
</head>
<body>
<?php include('../html/navbar_simple.html'); ?>
<div class="jumbotron">
    <div class="container">
        <h2>Please kindly input your information.</h2>
        <div class="bs-example" data-example-id="basic-forms">
            <form class="form-horizontal" action="main.php" method="post">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Email</label>
                    <div class="col-sm-4">
                        <input type="email" class="form-control" name="email" placeholder="Email">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">Password</label>
                    <div class="col-sm-4">
                        <input type="password" class="form-control" name="password" placeholder="Password">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">Name</label>
                    <div class="col-sm-4">
                        <input type="text" class="form-control" name="username" placeholder="Name">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">Block</label>
                    <div class="col-sm-4">
                        <input type="number" class="form-control" name="block" placeholder="Block">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">Hood</label>
                    <div class="col-sm-4">
                        <input type="number" class="form-control" name="hood" placeholder="Hoods">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">Description</label>
                    <div class="col-sm-4">
                        <textarea class="form-control" name="description" placeholder="Description" rows="3"></textarea>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label"></label>
                    <input type="submit" class="btn btn-primary">
                </div>
            </form>
        </div>
    </div>
</div>
<script src="//cdn.bootcss.com/jquery/1.11.3/jquery.min.js"></script>
<script src="//cdn.bootcss.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
</body>
</html>