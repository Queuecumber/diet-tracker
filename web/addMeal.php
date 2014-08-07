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
                <form action="addMeal.php" method="POST">
                    <label for="food_to_search">Food Name</label>
                    <input type="text" name="food_to_search" value="<?= $food ?>"/>
                    <button type="submit">Search</button>
                </form>

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
