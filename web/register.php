<?php
include('utils/databaseSetup.php');

// This page won't work without a suitable PHP email setup

function genPassword($len)
{
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

    $randomString = '';
    for ($i = 0; $i < $len; $i++)
    {
        $randomString .= $characters[rand(0, strlen($characters) - 1)];
    }

    return $randomString;
}

function addUser($email, $name, $target)
{
    global $mysqli;

    $pw = genPassword(8);
    $hashPass = md5($pw);

    $query = "insert into user values('" . $email . "','" . $name . "','" . $hashPass . "'," . $target . ")";
    $res = $mysqli->query($query);

    if($res)
    {
        mail($email, 'Thanks for Joining!', 'Thanks for joining the diet tracker, '
        . 'your goal is to eat less than ' . $target . ' calories per day, good luck!'
        . 'Your password is ' . $pw . ' and can be changed after logging in.'
        . 'Please visit http://[host]/login.php to get started.');

        return true;
    }

    return false;
}

if($_SERVER['REQUEST_METHOD'] == 'POST')
{
    if(isset($_POST['email']))
    {
        $email = $_POST['email'];

        if(isset($_POST['name']))
        {
            $name = $_POST['name'];

            if(isset($_POST['target']) && $_POST['target'] != "")
                $target = $_POST['target'];
            else
                $target = '2000';

            $errorMessage = '';

            if(addUser($email, $name, $target))
            {
                header("Location: login.php");
            }
            else
            {
                $errorMessage = 'A user with that email address already exists';
            }
        }
        else
        {
            $errorMessage = 'Please enter a name';
        }
    }
    else
    {
        $errorMessage = 'Please enter an email address';
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
        <meta name="viewport" content="width=device-width, initial-scale=0.9">

        <title>Diet Tracker Registration</title>

        <link rel="stylesheet" href="lib/bootstrap-3.2.0-dist/css/bootstrap.min.css"/>
        <script src="lib/jquery-2.1.1.min.js"></script>
        <script src="lib/bootstrap-3.2.0-dist/js/bootstrap.min.js"></script>

        <style>

        main
        {
            padding: 10px;
        }

        .container
        {
            margin-top: 100px;
            max-width: 500px;
            text-align: center;
        }

        .container .alert
        {
            margin-top: 25px;
        }

        </style>

    </head>
    <body>
        <main>
            <section class="container">
                <h1>Diet Tracker Registration</h1>

                Your password will be emailed to the address you provide <br/> <br/>

                <form action="register.php" method="POST">
                    <input type="email" name="email" class="form-control" placeholder="Email" requried/> <br/>
                    <input type="text" name="name" class="form-control" placeholder="Name" required/> <br/>
                    <input type="number" min="1000" max="3000" name="target" class="form-control" placeholder="Calorie Target (optional)"/> <br/>
                    <button type="submit" class="btn btn-default btn-lg btn-block">Register</button>
                </form>
                <?php

                if($errorMessage != '')
                {
                    ?>

                    <div class="alert alert-danger"><strong>Oops!</strong> <?= $errorMessage ?></div>

                    <?php
                }
                ?>
            </section>
        </main>
    </body>
</html>
