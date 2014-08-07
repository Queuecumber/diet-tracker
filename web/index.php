<?php

include('utils/checkUser.php');
include('utils/model.php');

$user = getUser($seshUser);
$meals = getMealsForUser($user['email'], time())

?>

<!DOCTYPE html>
<html>
    <head>
        <title>Diet Tracker</title>
    </head>
    <body>
        <header>
            <h1> Welcome <?= $user['name'] ?> </h1>
            <a href="logOff.php">Log Off</a>
        </header>
        <main>
            <article>
                <h2> You have eaten <?= 0 ?> calories of your <?= $user['calorie_target'] ?> calories today </h2>
            </article>
            <article>
                <h2> Today's Meals </h2>
                <a href="addMeal.php">Add Meal</a>

            </article>
        </main>
        <footer>
        </footer>
    </body>
</html>
