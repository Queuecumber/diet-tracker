<?php

include('utils/checkUser.php');
include('utils/model.php');

$user = getUser($seshUser);

if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['meal_id']))
{
    $meal = getMeal($_POST['meal_id']);
    $mealInfo = getMealInfo($_POST['meal_id']);
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
            <form action="addMeal.php" method="POST">
                <input type="hidden" value="<?= $meal['meal_id'] ?>"/>

            </form>
        </main>
    </body>
</html>
