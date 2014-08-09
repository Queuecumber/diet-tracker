<?php
include('utils/databaseSetup.php');

function checkUserLogin($uname, $pw)
{
    global $mysqli;

    $hashPass = md5($pw);

    $query = "select * from user where email='" . $uname . "' and password='" . $hashPass . "'";
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

if(isset($_SESSION['user']))
{
    header("Location: index.php");
}

if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['user']) && isset($_POST['pw']))
{
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
}
else
{
    $errorMessage = '';
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
            <input type="text" name="user" required/>
            <br/>
            <label for="pw">Password</label>
            <input type="password" name="pw" required>
            <br/>
            <button type="submit">Log In</button>
            <br/>
            <output name="response" for="user pw"><?= $errorMessage ?></output>
        </form>
        <a href="register.php">Register</a>
    </body>
</html>
