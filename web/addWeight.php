<?php

include('utils/checkUser.php');
include('utils/model.php');

$user = getUser($seshUser);
$weights = getWeightsForUser($user['email']);

if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['new_weight']))
{
    addWeightForUser($user['email'], $_POST['new_weight']);
    $weights = getWeightsForUser($user['email']);
}

?>
<!DOCTYPE html>
<html>
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=0.9">

        <title>Weight</title>

        <link rel="stylesheet" href="lib/bootstrap-3.2.0-dist/css/bootstrap.min.css"/>
        <script src="lib/jquery-2.1.1.min.js"></script>
        <script src="lib/bootstrap-3.2.0-dist/js/bootstrap.min.js"></script>

        <style>

        main
        {
            padding: 10px;
        }

        .previous-weights
        {
            width: 300px;
            margin-top: 10px;
        }

        .weight-record
        {
            margin-top: 5px;
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
                            <li class="active"><a href="addWeight.php"><span class="glyphicon glyphicon-inbox"></span> Record Weight</a></li>
                            <li><a href="addTarget.php"><span class="glyphicon glyphicon-screenshot"></span> Update Calorie Target</a></li>
                            <li><a href="history.php"><span class="glyphicon glyphicon-time"></span> History</a></li>
                            <li class="dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown"><span class="glyphicon glyphicon-user"></span> <?=$user['name']?><span class="caret"></span></a>
                                <ul class="dropdown-menu">
                                    <li><a href="userSettings.php"><span class="glyphicon glyphicon-cog"></span> Account Settings</a></li>
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
                <h1>Record a New Weight</h1>
                <form action="addWeight.php" method="POST" class="form-inline">
                    <div class="input-group">
                        <input name="new_weight" type="number" class="form-control" placeholder="Weight (lbs)" min="0" step="any" required/>
                        <span class="input-group-btn">
                            <button type="submit" class="btn btn-default">Record</button>
                        </span>
                    </div>
                </form>
            </section>
            <section class="previous-weights">
                <?php

                if(count($weights) > 0)
                {
                    ?>
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h3 class="panel-title">Previous Weights</h3>
                        </div>
                        <div class="panel-body">
                            <?php

                            foreach($weights as $w)
                            {
                                ?>

                                <div class="weight-record">
                                    <strong><?= $w['amount'] ?>lbs</strong> on <?= date("l, F jS Y" ,strtotime($w['date'] . " UTC")) ?>
                                </div>

                                <?php
                            }

                            ?>
                        </div>
                    </div>

                    <?php
                }
                ?>
            </section>
        </main>
    </body>
</html>
