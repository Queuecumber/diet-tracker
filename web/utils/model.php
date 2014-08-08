<?php
include('databaseSetup.php');
include('scrape.php');
include('usdaExtract.php');

function getUser($email)
{
    $res = querySymbolic('user', ['email' => $email]);
    return extractSingle($res, ['password']);
}

function getMealsForUser($email, $date)
{
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
    return extractSearch($search);
}

function createMeal($email)
{
    global $mysqli;

    $dayString = date('Y-m-d H:i:s');

    insertSymbolic('meal', [
        'date' => $dayString,
        'amount' => 0,
        'user' => $email
    ]);

    $iid = $mysqli->insert_id;

    $res = querySymbolic('meal', [
        'meal_id' => $iid
    ]);

    return extractSingle($res);
}

function addFoodToMeal($meal_id, $ndb_no)
{
    $meal_id = intval($meal_id);

    $res = querySymbolic('food', [
        'ndb_no' => $ndb_no
    ]);

    $food = extractSingle($res);

    if(!$food)
    {
        $food = extractFood($ndb_no);
        insertSymbolic('food', $food);
    }

    $food_report = [
        'metric' => 'serving',
        'value' => 1,
        'calories' => 500,
        'meal'  => $meal_id,
        'food' => $ndb_no
    ];

    insertSymbolic('food_report', $food_report);

    $meal = extractSingle(querySymbolic('meal', ['meal_id' => $meal_id]));
    $meal['amount'] += $food_report['calories'];

    updateSymbolic('meal', ['meal_id' => $meal_id], ['amount' => $meal['amount']]);
}

?>
