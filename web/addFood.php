<?php

include('utils/checkUser.php');
include('utils/model.php');

$user = getUser($seshUser);

if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['meal_id']))
{
    $meal = getMeal($_POST['meal_id']);
}

if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['food_to_add']))
{
    $newFood = $_POST['food_to_add'];
    $food = getFood($newFood);
    $metrics = getFoodMetrics($food['ndb_no']);
}

?>
<!DOCTYPE html>
<html>
    <head>
        <title>Amount of Food</title>
    </head>
    <body>
        <h1>Add <?= $food['name'] ?> to Meal</h1>
        <form action="addMeal.php" method="POST">
            <label for="amount">Amount</label>
            <input type="number" name="amount" step="any" min="0" required/>
            <select name="metric">
                <?php

                foreach($metrics as $m)
                {
                    ?>

                    <option value="<?=$m?>"><?=$m?></option>

                    <?php
                }

                ?>
            </select>
            <button type="submit">Add</button>

            <?php

            if(isset($meal))
            {
                ?>

                <input type="hidden" name="meal_id" value="<?= $meal['meal_id'] ?>"/>

                <?php
            }

            ?>

            <input type="hidden" name="food_to_add" value="<?= $food['ndb_no'] ?>"/>
        </form>
    </body>
</html>
