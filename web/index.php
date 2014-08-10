<?php

include('utils/checkUser.php');
include('utils/model.php');

$user = getUser($seshUser);

if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_meal_id']))
{
    $deleteMealId = $_POST['delete_meal_id'];
    dropMeal($deleteMealId, $user['email']);
}

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

        .target-alert
        {
            display: inline-block;
        }

        .meal-controls
        {
            position: absolute;
            right: 30px;
            top: 10px;
        }

        </style>

        <script>

        $(document).ready(function ()
        {
            $('.meal-controls').find('.edit-meal').on('click', function ()
            {
                $(this).closest('section').find('.form-edit-meal').submit();
            });

            $('.meal-controls').find('.delete-meal').on('click', function ()
            {
                var deleteForm = $(this).closest('section').find('.form-delete-meal');
                var time = $(this).closest('h2').text();

                $('#delete-meal-modal').find('.modal-title').text('Delete Meal at ' + time);
                $('#delete-meal-modal').find('#delete-meal-submit').on('click', function ()
                {
                    deleteForm.submit();
                });

                $('#delete-meal-modal').modal({show: true});
            });

            $('#delete-meal-modal').on('hide.bs.modal', function ()
            {
                $('#delete-meal-modal').find('#delete-meal-submit').off();
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
                            <li class="active"><a href="index.php"><span class="glyphicon glyphicon-list"></span> Daily Report</a></li>
                            <li><a href="addMeal.php"><span class="glyphicon glyphicon-cutlery"></span> Add Meal</a></li>
                            <li><a href="addWeight.php"><span class="glyphicon glyphicon-inbox"></span> Record Weight</a></li>
                            <li><a href="addTarget.php"><span class="glyphicon glyphicon-screenshot"></span> Update Calorie Target</a></li>
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
            <h1> Daily Report for <?= date("l, F jS") ?> </h1>
            <div class="target-alert alert <?php if($calTotal <= $user['calorie_target']) echo "alert-info"; else echo "alert-warning"; ?>">
                <strong><?= $calTotal ?> / <?= $user['calorie_target'] ?></strong> calories consumed today. <a href="addTarget.php" class="alert-link">Change your target</a>
            </div>

            <div class="container-fluid">
                <div class="row">
                    <?php

                        $colCnt = 1;
                        foreach($meals as $m)
                        {
                            $info = getMealInfo($m['meal_id']);

                            ?>
                            <div class="col-xs-12 col-md-6 col-lg-4">
                                <section class="well">

                                    <h2>
                                        <?= date("g:i a", strtotime($m['date'])) ?>
                                        <span class="meal-controls">
                                            <button class="edit-meal btn btn-sm btn-default"><span class="glyphicon glyphicon-edit"></span></button>
                                            <button class="delete-meal btn btn-sm btn-danger"><span class="glyphicon glyphicon-trash"></span></button>
                                        </span>
                                    </h2>

                                    <table class="table table-condensed">
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

                                                <tr class="<?= $i['calories'] > 400 ? "danger" : "" ?>">
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

                                    <form action="addMeal.php" method="POST" class="form-edit-meal">
                                        <input type="hidden" name="meal_id" value="<?= $m['meal_id'] ?>"/>
                                    </form>

                                    <form action="index.php" method="POST" class="form-delete-meal">
                                        <input type="hidden" name="delete_meal_id" value="<?= $m['meal_id'] ?>"/>
                                    </form>

                                </section>
                            </div>

                            <?php

                            // Control row divisions on all screen sizes
                            if($colCnt % 3 == 0)
                            {
                                ?>

                                <div class="clearfix visible-lg-block"></div>

                                <?php
                            }

                            if($colCnt % 2 == 0)
                            {
                                ?>

                                <div class="clearfix visible-md-block"></div>

                                <?php
                            }

                            $colCnt++;
                        }

                        if(count($meals) == 0)
                        {
                            ?>

                            No meals recorded for today! <a href="addMeal.php">Add one to get started.</a>

                            <?php
                        }

                    ?>
                </div>
            </div>
        </main>
        <footer>
            <div class="modal fade" id="delete-meal-modal" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal"><span>&times;</span><span class="sr-only"Close</span></button>
                            <h4 class="modal-title"></h4>
                        </div>
                        <div class="modal-body">
                            Are you sure you want to delete this meal? This action can <strong>not</strong> be undone!
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                            <button typoe="button" class="btn btn-danger" id="delete-meal-submit">Delete</button>
                        </div>
                    </div>
                </div>
            </div>
        </footer>
    </body>
</html>
