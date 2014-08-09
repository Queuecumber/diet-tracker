<?php

include('utils/checkUser.php');
include('utils/model.php');

$user = getUser($seshUser);

if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['new_target']))
{
    changeTargetForUser($user['email'], $_POST['new_target']);
    $user = getUser($user['email']);
}

?>
<!DOCTYPE html>
<html>
    <head>
        <title>Calorie Target</title>
    </head>
    <body>
        <header>
            <h1>Change Daily Calorie Target</h1>
            <form action="addTarget.php" method="POST">
                <label for="new_target">Calories</label>
                <input name="new_target" type="number" required/>c
                <button type="submit">Change</button>
            </form>
        </header>
        <main>
            <strong>Current Target:</strong> <?=$user['calorie_target']?>
        </main>
        <footer>
            <a href="index.php">Done</a>
        </footer>
    </body>
</html>
