<?php

include('utils/checkUser.php');
include('utils/model.php');

$user = getUser($seshUser);
$meals = getMealsForUser($user['email'], time());
$weight = getWeightsForUser($user['email']);

$calTotal = 0;
foreach($meals as $m)
{
    $calTotal += floatval($m['amount']);
}

?>

<!DOCTYPE html>
<html>
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Daily Report</title>

        <link rel="stylesheet" href="lib/bootstrap-3.2.0-dist/css/bootstrap.min.css"/>
        <script src="lib/jquery-2.1.1.min.js"></script>
        <script src="lib/bootstrap-3.2.0-dist/js/bootstrap.min.js"></script>

        <style>

        main
        {
            padding: 10px;
        }

        .meal-report
        {
            display: inline-block;

            margin-top: 10px;
        }

        .meal-report table td
        {
            max-width: 300px;
            text-overflow: ellipsis;
        }

        .target-alert
        {
            display: inline-block;
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
                            <li class="active"><a href="index.php"><span class="glyphicon glyphicon-list"></span> Daily Report</a></li>
                            <li><a href="addMeal.php"><span class="glyphicon glyphicon-cutlery"></span> Add Meal</a></li>
                            <li><a href="addWeight.php"><span class="glyphicon glyphicon-inbox"></span> Record Weight</a></li>
                            <li><a href="addTarget.php"><span class="glyphicon glyphicon-screenshot"></span> Update Calorie Target</a></li>
                            <li><a href="#"><span class="glyphicon glyphicon-time"></span> History</a></li>
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
            <h1> Daily Report for <?= date("l, F jS") ?> </h1>
            <div class="target-alert alert <?php if($calTotal <= $user['calorie_target']) echo "alert-info"; else echo "alert-warning"; ?>">
                <strong><?= $calTotal ?> / <?= $user['calorie_target'] ?></strong> calories consumed today. <a href="addTarget.php" class="alert-link">Change your target</a>
            </div>
            </p>

            <?php

                foreach($meals as $m)
                {
                    $info = getMealInfo($m['meal_id']);

                    ?>

                    <section class="well meal-report">

                        <h2> <?= date("g:i a", strtotime($m['date'])) ?> </h2>

                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Servings</th>
                                    <th>Serving Size</th>
                                    <th>Calories</th>
                                </tr>
                            </thead>
                            <tbody>

                                <?php

                                foreach($info as $i)
                                {
                                    ?>

                                    <tr>
                                        <td><?= $i['name'] ?></td>
                                        <td><?= $i['value'] ?></td>
                                        <td><?= $i['metric'] ?></td>
                                        <td><?= $i['calories'] ?></td>
                                    </tr>

                                    <?php
                                }

                                ?>

                            </tbody>
                        </table>

                        <strong> Total Calories: </strong> <?= $m['amount'] ?>

                        <?php

                        ?>

                    </section>

                    <?php
                }

                if(count($meals) == 0)
                {
                    ?>

                    No meals recorded for today! <a href="addMeal.php">Add one to get started.</a>

                    <?php
                }

            ?>
        </main>
        <footer>
        </footer>
    </body>
</html>
