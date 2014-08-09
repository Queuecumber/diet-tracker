<?php

include('utils/checkUser.php');
include('utils/model.php');

$user = getUser($seshUser);
$frequentFoods = getFrequentFoods($user['email']);

if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['meal_id']))
{
    $meal = getMeal($_POST['meal_id']);
    $mealInfo = getMealInfo($_POST['meal_id']);
}

$food = '';
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['food_to_search']))
{
    $food = $_POST['food_to_search'];
    $foods = findFoods($food);

    if(!$foods)
    {
        $singleFoodMatch = findFoodByName($food);

        if($singleFoodMatch)
            $foods = [$singleFoodMatch];
        else
            $foods = [];
    }
}

if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['food_to_add']))
{
    $newFood = $_POST['food_to_add'];
    $amount = $_POST['amount'];
    $metric = $_POST['metric'];

    if(!isset($meal))
    {
        $meal = createMeal($user['email']);
    }

    addFoodToMeal($meal['meal_id'], $newFood, $amount, $metric);
    updateFrequentFoods($user['email'], $newFood);

    $meal = getMeal($meal['meal_id']);
    $mealInfo = getMealInfo($meal['meal_id']);
    $frequentFoods = getFrequentFoods($user['email']);
}

?>
<!DOCTYPE html>
<html>
    <head>
        <title>Add Meal</title>

        <link rel="stylesheet" href="lib/bootstrap-3.2.0-dist/css/bootstrap.min.css"/>
        <script src="lib/jquery-2.1.1.min.js"></script>
        <script src="lib/bootstrap-3.2.0-dist/js/bootstrap.min.js"></script>

        <style>

        .find-foods
        {
            margin-left: 10px;

            height: 700px;

            position: relative;
        }

        .find-foods .panel-body
        {
            height: 100%;
        }

        .food-search-results-body
        {
            margin-top: 10px;
            overflow-y: auto;
            height: 86%;
        }

        .food-search-results a.list-group-item
        {
            text-overflow: ellipsis;
            white-space: nowrap;
            overflow: hidden;
        }

        </style>

        <script>

        $(document).ready(function ()
        {
            $('.food-search-results').on('click', 'a.list-group-item', function ()
            {
                $(this).find('form').submit();
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
                            <li><a href="index.php">Daily Report</a></li>
                            <li class="active"><a href="addMeal.php">Add Meal</a></li>
                            <li><a href="addWeight.php">Record Weight</a></li>
                            <li><a href="addTarget.php">Update Calorie Target</a></li>
                            <li><a href="#">History</a></li>
                            <li class="dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown"><span class="glyphicon glyphicon-user"></span> <?=$user['name']?><span class="caret"></span></a>
                                <ul class="dropdown-menu">
                                    <li><a href="#">Account Settings</a></li>
                                    <li><a href="logOff.php">Log Off</a></li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>
        </header>
        <main>
            <h1> Add New Meal </h1>

            <article class="col-md-4">
                <aside class="panel panel-default find-foods">
                    <section class="panel-heading">
                        Find Foods
                    </section>
                    <div class="panel-body">
                        <section>
                            <form action="addMeal.php" method="POST" class="form-inline">

                                <div class="input-group">
                                    <input type="text" class="form-control" placeholder="Name" name="food_to_search" value="<?= $food ?>" list="frequent-foods" required/>
                                    <span class="input-group-btn">
                                        <button type="submit" class="btn btn-default">Search</button>
                                    </span>
                                </div>

                                <datalist id="frequent-foods">
                                    <?php

                                    foreach($frequentFoods as $ff)
                                    {
                                        ?>

                                        <option value="<?=$ff['name']?>"></option>

                                        <?php
                                    }

                                    ?>
                                </datalist>

                                <?php

                                if(isset($meal))
                                {
                                    ?>

                                    <input type="hidden" name="meal_id" value="<?= $meal['meal_id'] ?>"/>

                                    <?php
                                }

                                ?>

                            </form>
                        </section>
                        <section class="food-search-results-body">
                            <?php
                                if(isset($foods))
                                {
                                    ?>

                                    <div class="list-group food-search-results">

                                        <?php

                                        foreach($foods as $f)
                                        {
                                            ?>

                                            <a href="#" class="list-group-item">

                                                <span class="glyphicon glyphicon-chevron-right"></span>
                                                <?=$f['name']?>


                                                <form action="addFood.php" method="POST">
                                                    <input type="hidden" name="food_to_add" value="<?= $f['ndb_no'] ?>"/>

                                                    <?php

                                                    if(isset($meal))
                                                    {
                                                        ?>

                                                        <input type="hidden" name="meal_id" value="<?= $meal['meal_id'] ?>"/>

                                                        <?php
                                                    }

                                                    ?>
                                                </form>
                                            </a>

                                            <?php
                                        }

                                        ?>

                                    </div>

                                    <?php
                                }
                            ?>
                        </section>
                    </div>
                </aside>
            </article>
            <article class="current-meal col-md-8">
                <h2> Current Meal </h2>
                <?php
                    if(isset($mealInfo))
                    {
                        ?>

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

                                foreach($mealInfo as $i)
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

                        <strong> Total Calories: </strong> <?= $meal['amount'] ?>

                        <?php
                    }
                ?>
            </article>
        </main>
    </body>
</html>
