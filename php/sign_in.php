<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="../bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <title>Sign in</title>
</head>
<body>
<?php include('../html/navbar_simple.html'); ?>
<div class="jumbotron">
    <div class="container">
        <h2>Please kindly input your information.</h2>
        <div class="bs-example" data-example-id="basic-forms">
            <form class="form-horizontal">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Email</label>
                    <div class="col-sm-4">
                        <input type="email" class="form-control" id="inputEmail3" placeholder="Email">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">Password</label>
                    <div class="col-sm-4">
                        <input type="password" class="form-control" id="inputName" placeholder="Password">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">Name</label>
                    <div class="col-sm-4">
                        <input type="text" class="form-control" id="inputName" placeholder="Name">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">Block</label>
                    <div class="col-sm-4">
                        <input type="number" class="form-control" id="inputBlock" placeholder="Block">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">Hood</label>
                    <div class="col-sm-4">
                        <input type="number" class="form-control" id="inputHood" placeholder="Hoods">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">Description</label>
                    <div class="col-sm-4">
                        <textarea class="form-control" id="inputDescription" placeholder="Description" rows="3"></textarea>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label"></label>
                    <input type="submit" class="btn btn-primary" href="#">
                </div>
            </form>
        </div>
    </div>
</div>
</body>
</html>