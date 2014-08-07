<?php
include('databaseSetup.php');

function getUser($email)
{
    $res = querySymbolic('user', ['email' => $email]);
    return extractSingle($res, ['password']);
}

function getMealsForUser($email, $date)
{
    global $mysqli;

    $dayString = date('Y-m-d', $date);
    $dayStart = $dayString . " 00:00:00";
    $dayEnd = $dayString . " 23:59:59";

    $res = querySymbolic('meal', [
        'email' => $email,
        'date' => ['between', $dayStart, $dayEnd]
    ]);

    return extractArray($res);
}


function getMeal($meal_id)
{
    $res = querySymbolic('meal', ['meal_id' => $meal_id]);
    return extractSingle($res);
}

function getMealInfo($meal_id)
{
    $res = querySymbolic([
        'meal',
        ['food_report', 'meal_id', 'meal'],
        ['food', 'food', 'ndb_no']
    ], ['meal_id' => $meal_id]);

    return extractArray($res);
}

function findFoods($search)
{
    
}

?>
