<?php

include('utils/checkUser.php');
include('utils/model.php');

$user = getUser($seshUser);
$weights = getWeightsForUser($user['email']);

if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['new_weight']))
{
    addWeightForUser($user['email'], $_POST['new_weight']);
    $weights = getWeightsForUser($user['email']);
}

?>
<!DOCTYPE html>
<html>
    <head>
        <title>Weight</title>
    </head>
    <body>
        <header>
            <h1>Record a new Weight</h1>
            <form action="addWeight.php" method="POST">
                <label for="new_weight">Weight</label>
                <input name="new_weight" type="number"/>lbs
                <button type="submit">Record</button>
            </form>
        </header>
        <main>
            <?php

            if(count($weights) > 0)
            {
                ?>
                <h2>Previous Weights</h2>

                <?php

                foreach($weights as $w)
                {
                    ?>

                    <strong><?= $w['amount'] ?>lbs</strong> at <?= $w['date'] ?> <br/>

                    <?php
                }

                ?>

                <?php
            }
            ?>
        </main>
        <footer>
            <a href="index.php">Done</a>
        </footer>
    </body>
</html>
