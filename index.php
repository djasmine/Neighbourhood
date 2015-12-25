<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="//cdn.bootcss.com/bootstrap/3.3.5/css/bootstrap.min.css" rel="stylesheet">
    <title>NYU Neighbour</title>
</head>
<body>
    <?php
        session_start();
        session_unset();
    ?>
    <nav class="navbar navbar-inverse navbar-fixed-top">
        <div class="container">
            <div class="navbar-header">
                <a class="navbar-brand bold" href="index.php">NYU Neighbour</a>
            </div>
        </div>
    </nav>
    <div class="jumbotron">
        <div class="container">
            <h1>Welcome to NYU Neighbourhood</h1>
            <p>
                Find and connect your neighbourhood at NYU Neighbour!
            </p>
            <p>
                <a class="btn btn-primary btn-lg" href="php/sign_in.php">Sign in</a>
                <a class="btn btn-success btn-lg" href="php/login.php">Log in</a>
            </p>
        </div>
    </div>

    <script src="//cdn.bootcss.com/jquery/1.11.3/jquery.min.js"></script>
    <script src="//cdn.bootcss.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
</body>
</html>