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
    </head>
    <body>
        <header>
            <h1>Add New Meal</h1>
        </header>
        <main>
            <article>
                <section>
                    <form action="addMeal.php" method="POST">
                        <label for="food_to_search">Food Name</label>
                        <input type="text" name="food_to_search" value="<?= $food ?>" list="frequent-foods" required/>

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

                        <button type="submit">Search</button>

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
                <section>
                    <?php
                        if(isset($foods))
                        {
                            foreach($foods as $f)
                            {
                                ?>

                                <form action="addFood.php" method="POST">
                                    <button type="submit">Add</button>
                                    <label for="food_to_add"><?= $f['name'] ?> </label>
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

                                <?php
                            }
                        }
                    ?>
                </section>
            </article>
            <article>
                <?php
                    if(isset($mealInfo))
                    {
                        ?>

                        <section>

                            <?php

                            foreach($mealInfo as $info)
                            {
                                ?>

                                <strong> <?= $info['name'] ?>: </strong> <?= $info['calories'] ?>c <br/>

                                <?php
                            }

                            ?>

                        </section>
                        <section>
                            <strong> Total Calories: </strong> <?= $meal['amount'] ?>
                        </section>

                        <?php
                    }
                ?>
            </article>
        </main>
        <footer>
            <a href="index.php">Done</a>
        </footer>
    </body>
</html>
