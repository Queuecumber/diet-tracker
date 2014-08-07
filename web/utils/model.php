<?php

function getUser($email)
{
    global $mysqli;

    $query = "select * from user where email='" . $email . "'";
    $res = $mysqli->query($query);

    if($res)
    {
        $nrows = $res->num_rows;

        if($nrows > 0)
        {
            $res->data_seek(0);
            $row = $res->fetch_assoc();

            unset($row['password']);

            return $row;
        }
    }

    return $res;
}

function getMealsForUser($email, $date)
{
    global $mysqli;

    $dayString = date('Y-m-d', $date);
    $dayStart = $dayString . " 00:00:00";
    $dayEnd = $dayString . " 23:59:59";

    $query = "select * from meal where user='" . $email . "' and date between '" . $dayStart . "' and '" . $dayEnd . "'";
    $res = $mysqli->query($query);

    if($res)
    {
        $mealArray = array();

        $nrows = $res->num_rows;
        for($i = 0; $i < $nrows; $i++)
        {
            $res->data_seek($i);
            $mealArray[] = $res->fetch_assoc();
        }

        return $mealArray;
    }

    return $res;
}

?>
