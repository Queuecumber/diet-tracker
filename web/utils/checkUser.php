<?php

session_start();

function tz_offset_to_name($offset)
{
    $abbrarray = timezone_abbreviations_list();
    foreach ($abbrarray as $abbr)
    {
        foreach ($abbr as $city)
        {
            if ($city['offset'] == $offset)
            {
                return $city['timezone_id'];
            }
        }
    }

    return FALSE;
}

if(!(isset($_SESSION['user'])))
{
    header("Location: login.php");
}
else
{
    $seshUser = $_SESSION['user'];
    $timezone = $_SESSION['zone'];
    $dst = filter_var($_SESSION['dst'], FILTER_VALIDATE_BOOLEAN);

    $tzName = timezone_name_from_abbr("", $timezone, $dst ? 1 : 0);
    date_default_timezone_set($tzName);
}

?>
