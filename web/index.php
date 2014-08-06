<?php

include('utils/databaseSetup.php');
include('utils/checkUser.php');

?>

<!DOCTYPE html>
<html>
    <head>
        <title>Diet Tracker</title>
    </head>
    <body>
        <h1> Welcome <?= $user ?> </h1>
        <form action="logOff.php" method="POST">
            <button type="submit">Log Off</button>
        </form>
    </body>
</html>
