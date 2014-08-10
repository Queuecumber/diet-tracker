<?php
include('utils/databaseSetup.php');

function checkUserLogin($uname, $pw)
{
    global $mysqli;

    $hashPass = md5($pw);

    $query = "select * from user where email='" . $uname . "' and password='" . $hashPass . "'";
    $res = $mysqli->query($query);

    if($res)
    {
        $nrow = $res->num_rows;

        if($nrow > 0)
        {
            return true;
        }
    }

    return false;
}

session_start();

if(isset($_SESSION['user']))
{
    header("Location: index.php");
}

if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['user']) && isset($_POST['pw']))
{
    $uname = $_POST['user'];
    $pw = $_POST['pw'];

    $errorMessage = '';

    if(checkUserLogin($uname, $pw))
    {
        session_start();
        $_SESSION['user'] = $uname;
        header("Location: index.php");
    }
    else
    {
        $errorMessage = "Invalid email/password";
    }
}
else
{
    $errorMessage = '';
}

?>

<!DOCTYPE html>
<html>
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Diet Tracker Log In</title>

        <link rel="stylesheet" href="lib/bootstrap-3.2.0-dist/css/bootstrap.min.css"/>
        <script src="lib/jquery-2.1.1.min.js"></script>
        <script src="lib/bootstrap-3.2.0-dist/js/bootstrap.min.js"></script>

        <style>

        .form-signin
        {
            max-width: 330px;
            padding: 15px;
            margin: 0 auto;
        }

        .form-signin .form-control
        {
            position: relative;
            height: auto;
            -webkit-box-sizing: border-box;
               -moz-box-sizing: border-box;
                    box-sizing: border-box;
            padding: 10px;
            font-size: 16px;
        }

        .form-signin .form-control:focus
        {
            z-index: 2;
        }

        .form-signin input[type="email"]
        {
            margin-top:30px;
            margin-bottom: -1px;
            border-bottom-right-radius: 0;
            border-bottom-left-radius: 0;
        }

        .form-signin input[type="password"]
        {
            margin-bottom: 30px;
            border-top-left-radius: 0;
            border-top-right-radius: 0;
        }

        .container
        {
            margin-top: 100px;
            max-width: 400px;
            text-align: center;
        }

        .container .alert
        {
            margin-top: 25px;
        }

        </style>

    </head>
    <body>
        <section class="container">

            <h1>Diet Tracker Log In</h1>

            <form action="login.php" method="POST" class="form-signin">
                <input type="email" name="user" class="form-control" placeholder="Email" required/>
                <input type="password" name="pw" class="form-control" placeholder="Password" required>
                <button type="submit" class="btn btn-lg btn-default btn-block">Log In</button>
            </form>
            <a href="register.php">Register</a> <br/>
            <?php

            if($errorMessage != '')
            {
                ?>

                <div class="alert alert-danger"><strong>Oops!</strong> <?= $errorMessage ?></div>

                <?php
            }
            ?>
        </section>
    </body>
</html>
