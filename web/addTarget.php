<?php

include('utils/checkUser.php');
include('utils/model.php');

$user = getUser($seshUser);

if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['new_target']))
{
    changeTargetForUser($user['email'], $_POST['new_target']);
    $user = getUser($user['email']);
}

?>
<!DOCTYPE html>
<html>
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Calorie Target</title>

        <link rel="stylesheet" href="lib/bootstrap-3.2.0-dist/css/bootstrap.min.css"/>
        <script src="lib/jquery-2.1.1.min.js"></script>
        <script src="lib/bootstrap-3.2.0-dist/js/bootstrap.min.js"></script>

        <style>

        main
        {
            padding: 10px;
        }

        </style>

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
                            <li class="active"><a href="addTarget.php"><span class="glyphicon glyphicon-screenshot"></span> Update Calorie Target</a></li>
                            <li><a href="history.php"><span class="glyphicon glyphicon-time"></span> History</a></li>
                            <li class="dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown"><span class="glyphicon glyphicon-user"></span> <?=$user['name']?><span class="caret"></span></a>
                                <ul class="dropdown-menu">
                                    <li><a href="#"><span class="glyphicon glyphicon-cog"></span> Account Settings</a></li>
                                    <li><a href="logOff.php"><span class="glyphicon glyphicon-off"></span> Log Off</a></li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>
        </header>
        <main>
            <section>
                <h1>Update Your Daily Calorie Target</h1>
                <form action="addTarget.php" method="POST" class="form-inline">
                    <div class="input-group">
                        <input name="new_target" type="number" min="0" step="any" placeholder="Target (calories)" class="form-control" required/>
                        <span class="input-group-btn">
                            <button type="submit" class="btn btn-default">Change</button>
                        </span>
                </form>
            </section>
            <section>
                <strong>Current Target</strong> <?=$user['calorie_target']?> calories
            </section>
        </main>
    </body>
</html>
