<?php

include('utils/checkUser.php');
include('utils/model.php');

$user = getUser($seshUser);

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
}

if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['food_to_add']))
{
    $newFood = $_POST['food_to_add'];

    if(!isset($meal))
    {
        $meal = createMeal($user['email']);
    }

    var_dump($meal);

    addFoodToMeal($meal['meal_id'], $newFood);
//    $mealInfo = getMealInfo($_POST['meal_id']);
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
                        <input type="text" name="food_to_search" value="<?= $food ?>"/>
                        <button type="submit">Search</button>
                    </form>
                </section>
                <section>
                    <?php
                        if(isset($foods))
                        {
                            foreach($foods as $f)
                            {
                                ?>

                                <form action="addMeal.php" method="POST">
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
                        foreach($mealInfo as $info)
                        {
                            ?>

                            <strong> <?= $info['name'] ?>: </strong> <?= $info['calories'] ?>c <br/>

                            <?php
                        }
                    }
                ?>
            </article>
        </main>
    </body>
</html>
