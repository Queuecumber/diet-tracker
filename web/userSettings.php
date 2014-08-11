<?php

include('utils/checkUser.php');
include('utils/model.php');

$user = getUser($seshUser);

if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['new_password']))
{
    $newPass = $_POST['new_password'];
    $hashPass = md5($newPass);

    updateUserPassword($user['email'], $hashPass);
}

?>
<!DOCTYPE html>
<html>
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=0.9">

        <title>Account Settings</title>

        <link rel="stylesheet" href="lib/bootstrap-3.2.0-dist/css/bootstrap.min.css"/>
        <script src="lib/jquery-2.1.1.min.js"></script>
        <script src="lib/bootstrap-3.2.0-dist/js/bootstrap.min.js"></script>

        <style>

        main
        {
            padding: 10px;
        }

        .pwnomatch
        {
            display: none;
        }

        .glyphicon-beta:before
        {
            content: "\e0B8";
            font-size: 14pt;
            font-weight: bold;
        }

        .glyphicon-beta
        {
            width: 14px;
            height: 14px;
        }

        </style>

        <script>

        $(document).ready(function()
        {
            $('.form-changepw').submit(function ()
            {
                var pwInputs = $(this).find('input[type=password]');

                if($(pwInputs[0]).val() !== $(pwInputs[1]).val())
                {
                    $('.pwnomatch').show();
                    return false;
                }
                else
                {
                    return true;
                }
            });
        });

        </script>

    </head>
    <body>
        <header>
            <nav class="navbar navbar-default">
                <div class="container-fluid">
                    <div class="navbar-header">
                        <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#dt-navbar-collapse">
                            <span class="sr-only">Toggle navigation</span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                        </button>
                        <a class="navbar-brand">Diet Tracker</a>
                    </div>

                    <div class="collapse navbar-collapse" id="dt-navbar-collapse">
                        <ul class="nav navbar-nav">
                            <li><a href="index.php"><span class="glyphicon glyphicon-list"></span> Daily Report</a></li>
                            <li><a href="addMeal.php"><span class="glyphicon glyphicon-cutlery"></span> Add Meal</a></li>
                            <li><a href="addWeight.php"><span class="glyphicon glyphicon-inbox"></span> Record Weight</a></li>
                            <li><a href="addTarget.php"><span class="glyphicon glyphicon-screenshot"></span> Update Calorie Target</a></li>
                            <li><a href="history.php"><span class="glyphicon glyphicon-time"></span> History</a></li>
                            <li class="dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown"><span class="glyphicon glyphicon-user"></span> <?=$user['name']?><span class="caret"></span></a>
                                <ul class="dropdown-menu">
                                    <li class="active"><a href="userSettings.php"><span class="glyphicon glyphicon-cog"></span> Account Settings</a></li>
                                    <li><a href="https://github.com/Queuecumber/diet-tracker/issues"><span class="glyphicon glyphicon-beta"></span> Report Issue</a></li>
                                    <li class="divider"></li>
                                    <li><a href="logOff.php"><span class="glyphicon glyphicon-off"></span> Log Off</a></li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>
        </header>
        <main>
            <h1>Account Settings <small><?=$user['name']?></small></h1>
            <br/>
            <p>
                <strong>Email Address</strong> <?=$user['email']?>
            </p>
            <br/>
            <div class="row">
                <div class="col-xs-12 col-md-4">
                    <section class="panel-changepw panel panel-default">
                        <div class="panel-heading">Change Password</div>
                        <div class="panel-body">
                            <form action="userSettings.php" method="POST" class="form-changepw">
                                <input type="password" class="form-control" name="new_password" placeholder="Password"/> <br/>
                                <input type="password" class="form-control" placeholder="Verify Password"/> <br/>
                                <button type="submit" class="btn btn-default">Change</button>
                            </form>
                            <br/>
                            <div class="pwnomatch alert alert-danger"><strong>Oops!</strong> Passwords do not match</div>
                        </div>
                    </section>
                </div>
            </div>
        </main>
    </body>
</html>
