<?php

function checkUserLogin($uname, $pw)
{
    $query = "select * from users where email='" . $uname . "' and password = '" . + $pw . "'";
    $res = $mysqli->query($query);

    if($res)
    {
        $nrow = $res->num_rows;

        if($nrow > 0)
        {
            return true;
        }
    }

    return false;
}

session_start();

if(isset(_SESSION['user']))
{
    header("Location: index.php");
}

$uname = $_POST['user'];
$pw = $_POST['pw'];

$errorMessage = '';

if(checkUserLogin($uname, $pw))
{
    session_start();
    $_SESSION['user'] = $uname;
    header("Location: index.php");
}
else
{
    $errorMessage = "Invalid email/password";
}

?>

<!DOCTYPE html>
<html>
    <head>
        <title>Diet Tracker Log In</title>
    </head>
    <body>
        <form action="login.php" method="POST">
            <label for="user">Email</label>
            <input type="text" name="user"/>

            <label for="pw">Password</label>
            <input type="password" name="pw">

            <button type="submit">Log In</button>
            <output for="user pw" value="<?= $errorMessage ?>"></output>
        </form>
    </body>
</html>
