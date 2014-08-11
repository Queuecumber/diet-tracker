<?php

include('utils/checkUser.php');
include('utils/model.php');

$user = getUser($seshUser);
$frequentFoods = getFrequentFoods($user['email']);

if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_food']))
{
    $meal_id = $_POST['meal_id'];
    $food_id = $_POST['delete_food'];
    $metric = $_POST['delete_metric'];
    $value = $_POST['delete_value'];

    dropFoodFromMeal($food_id, $meal_id, $metric, $value);
}

if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['meal_id']))
{
    $meal = getMeal($_POST['meal_id']);
    $mealInfo = getMealInfo($_POST['meal_id']);

    if(count($mealInfo) == 0)
    {
        dropMeal($meal['meal_id'], $user['email']);
        unset($meal);
        unset($mealInfo);
    }
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
        <meta name="viewport" content="width=device-width, initial-scale=0.9">

        <title>Add Meal</title>

        <link rel="stylesheet" href="lib/bootstrap-3.2.0-dist/css/bootstrap.min.css"/>
        <script src="lib/jquery-2.1.1.min.js"></script>
        <script src="lib/bootstrap-3.2.0-dist/js/bootstrap.min.js"></script>

        <style>

        .find-foods
        {
            max-height: 700px;
        }

        .find-foods .panel-body
        {
            max-height: 600px;
            padding: 0px;
        }

        .search-area
        {
            padding: 10px;
        }

        .food-search-results-body
        {

            overflow-y: auto;
            max-height: 530px;

            padding-left: 10px;
            padding-right: 10px;
        }

        .food-search-results a.list-group-item
        {
            text-overflow: ellipsis;
            white-space: nowrap;
            overflow: hidden;
        }

        main
        {
            padding: 10px;
        }

        .glyphicon-beta
        {
            width: 14px;
            height: 14px;
            font-size: 14pt;
            text-align: center;
        }

        </style>

        <script>

        $(document).ready(function ()
        {
            $('.food-search-results').on('click', 'a.list-group-item', function ()
            {
                var targetForm = $(this).find('form');
                var foodToAdd = targetForm.find('input[name=food_to_add]').val();
                var mealId = targetForm.find('input[name=meal_id]').val();
                var foodName = $(this).text();

                var postData = {
                    food_to_add: foodToAdd
                };

                if(mealId)
                    postData['meal_id'] = mealId;

                $.post('addFood.php', postData, function(res)
                {
                    var html = $(res);
                    var f = html.filter('form');
                    f.find('button').hide();

                    $('#add-food-modal .modal-body').html(f);
                    $('#add-food-modal .modal-title').text(foodName);
                    $('#add-food-modal').modal({show: true});

                }, 'html');

            });

            $('#add-food-submit').on('click', function ()
            {
                // Can't use submit() here or required fields will be ignored
                $('#add-food-modal').find('form').find('[type=submit]').trigger('click');
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
                            <li class="active"><a href="addMeal.php"><span class="glyphicon glyphicon-cutlery"></span> Add Meal</a></li>
                            <li><a href="addWeight.php"><span class="glyphicon glyphicon-inbox"></span> Record Weight</a></li>
                            <li><a href="addTarget.php"><span class="glyphicon glyphicon-screenshot"></span> Update Calorie Target</a></li>
                            <li><a href="history.php"><span class="glyphicon glyphicon-time"></span> History</a></li>
                            <li class="dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown"><span class="glyphicon glyphicon-user"></span> <?=$user['name']?><span class="caret"></span></a>
                                <ul class="dropdown-menu">
                                    <li><a href="userSettings.php"><span class="glyphicon glyphicon-cog"></span> Account Settings</a></li>
                                    <li><a href="https://github.com/Queuecumber/diet-tracker/issues"><span class="glyphicon glyphicon-beta">&beta;</span> Report Issue</a></li>
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
            <h1> Add a New Meal </h1>

            <article class="col-sm-4">
                <aside class="panel panel-default find-foods">
                    <section class="panel-heading">
                        Find Foods
                    </section>
                    <div class="panel-body">
                        <section class="search-area">
                            <form action="addMeal.php" method="POST">

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


                                                <form>
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

                                        if(count($foods) == 0)
                                        {
                                            ?>

                                            No foods with that name were found

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
                    if(isset($mealInfo) && count($mealInfo) > 0)
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
                                        <td>
                                            <form class="form-delete-food-report" action="addMeal.php" method="POST">
                                                <button type="submit" class="delete-food-report btn btn-xs btn-danger">
                                                    <span class="glyphicon glyphicon-trash"></span>
                                                </button>
                                                <input type="hidden" name="meal_id" value="<?=$meal['meal_id']?>"/>
                                                <input type="hidden" name="delete_food" value="<?=$i['food']?>"/>
                                                <input type="hidden" name="delete_metric" value="<?=$i['metric']?>"/>
                                                <input type="hidden" name="delete_value" value="<?=$i['value']?>"/>
                                            </form>
                                        </td>
                                    </tr>

                                    <?php
                                }

                                ?>

                            </tbody>
                        </table>

                        <strong> Total Calories: </strong> <?= $meal['amount'] ?>

                        <?php
                    }
                    else
                    {
                        ?>

                        There are no foods in this meal, search for foods to get started!

                        <?php
                    }
                ?>
            </article>
        </main>
        <footer>
            <div class="modal fade" id="add-food-modal" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal"><span>&times;</span><span class="sr-only"Close</span></button>
                            <h4 class="modal-title"></h4>
                        </div>
                        <div class="modal-body">

                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                            <button typoe="button" class="btn btn-primary" id="add-food-submit">Add</button>
                        </div>
                    </div>
                </div>
            </div>
        </footer>
    </body>
</html>
