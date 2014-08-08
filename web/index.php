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
        <title>Diet Tracker</title>
    </head>
    <body>
        <header>
            <h1> Welcome <?= $user['name'] ?> </h1>
            <a href="logOff.php">Log Off</a>
        </header>
        <main>
            <article>
                <h2> You have eaten <?= $calTotal ?> calories of your <?= $user['calorie_target'] ?> calories today </h2>
            </article>
            <article>
                <?php
                    if(count($weight) > 0)
                    {
                        ?>
                        <h2> Your last recorded weight was <?= $weight['amount'] ?>lbs </h2>
                        <a href="#">Update</a>
                        <?php
                    }
                    else
                    {
                        ?>
                        <a href="#">Please set your initial weight</a>
                        <?php
                    }
                ?>
            </article>
            <article>
                <h2> Today's Meals (<?= date("D, M jS") ?>)</h2>
                <a href="addMeal.php">Add Meal</a>

                <?php

                    foreach($meals as $m)
                    {
                        $info = getMealInfo($m['meal_id']);

                        ?>

                        <section>

                            <h3> <?= date("H:i:s", strtotime($m['date'])) ?> </h3>

                            <?php

                            foreach($info as $i)
                            {
                                ?>

                                <strong> <?= $i['name'] ?>: </strong> <?= $i['calories'] ?>c <br/>

                                <?php
                            }

                            ?>

                            <strong> Total Calories: </strong> <?= $m['amount'] ?>

                            <?php

                            ?>

                        </section>

                        <?php
                    }

                ?>

            </article>
        </main>
        <footer>
        </footer>
    </body>
</html>
