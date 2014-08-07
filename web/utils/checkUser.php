<?php

session_start();

if(!(isset($_SESSION['user'])))
{
    header("Location: login.php");
}
else
{
    $seshUser = $_SESSION['user'];
}

?>
