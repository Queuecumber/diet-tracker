<?php
include('utils/databaseSetup.php');

function genPassword($len)
{
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

    $randomString = '';
    for ($i = 0; $i < $len; $i++)
    {
        $randomString .= $characters[rand(0, strlen($characters) - 1)];
    }

    return $randomString;
}

function addUser($email, $name, $target)
{
    global $mysqli;

    $pw = genPassword(8);
    $hashPass = md5($pw);

    $query = "insert into user values('" . $email . "','" . $name . "','" . $hashPass . "'," . $target . ")";
    $res = $mysqli->query($query);

    if($res)
    {
        mail($email, 'Thanks for Joining!', 'Thanks for joining the diet tracker, '
        . 'your goal is to eat less than ' . $target . ' calories per day, good luck!'
        . 'Your password is ' . $pw . ' and can be changed after logging in.'
        . 'Please visit http://[host]/login.php to get started.');

        return true;
    }

    return false;
}

if($_SERVER['REQUEST_METHOD'] == 'POST')
{
    if(isset($_POST['email']))
    {
        $email = $_POST['email'];

        if(isset($_POST['name']))
        {
            $name = $_POST['name'];

            if(isset($POST['target']))
                $target = $_POST['target'];
            else
                $target = '2000';

            $errorMessage = '';

            if(addUser($email, $name, $target))
            {
                header("Location: login.php");
            }
            else
            {
                $errorMessage = 'A user with that email address already exists';
            }
        }
        else
        {
            $errorMessage = 'Please enter a name';
        }
    }
    else
    {
        $errorMessage = 'Please enter an email address';
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
        <title>Diet Tracker Register</title>
    </head>
    <body>
        <form action="register.php" method="POST">
            <label for="email">Email</label>
            <input type="text" name="email" requried/>
            <br/>
            <label for="name">Name</label>
            <input type="text" name="name" required/>
            <br/>
            <label for="target">Calorie Target</label>
            <input type="number" min="1000" max="3000" name="target" value="2000"/>
            <button type="submit">Register</button>
            <br/>
            <output name="response"><?= $errorMessage ?></output>
        </form>
    </body>
</html>
