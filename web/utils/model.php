<?php
include('databaseSetup.php');
include('scrape.php');
include('usdaExtract.php');

function getUser($email)
{
    $res = querySymbolic('user', ['email' => $email]);
    return extractSingle($res, ['password']);
}

function updateUserPassword($email, $hashpass)
{
    updateSymbolic('user',['email' => $email],['password' => $hashpass]);
}

function changeTargetForUser($email, $target)
{
    updateSymbolic('user', ['email' => $email], ['calorie_target' => $target]);
}

function getMealsForUser($email, $date)
{
    $dayString = date('Y-m-d', $date);
    $dayStart = $dayString . " 00:00:00";
    $dayEnd = $dayString . " 23:59:59";

    $gmStart = gmdate('Y-m-d H:i:s', strtotime($dayStart));
    $gmEnd = gmdate('Y-m-d H:i:s', strtotime($dayEnd));

    $res = querySymbolic('meal', [
        'user' => $email,
        'date' => ['between', $gmStart, $gmEnd]
    ]);

    $meals = extractArray($res);

    for($i = 0; $i < count($meals); $i++)
    {
        $ts = $meals[$i]['date'];

        $meals[$i]['date'] = date('Y-m-d H:i:s', strtotime($ts . ' UTC'));
    }

    return $meals;
}

function getUserHistory($email)
{
    $res = querySymbolic('meal', [ 'user' => $email ], 'order by date desc');
    $meals = extractArray($res);

    for($i = 0; $i < count($meals); $i++)
    {
        $ts = $meals[$i]['date'];

        $meals[$i]['date'] = date('Y-m-d H:i:s', strtotime($ts . ' UTC'));
    }

    $histories = [];
    foreach($meals as $m)
    {
        $day = explode(' ', $m['date'])[0];

        if(count($histories) != 0)
        {
            if(end($histories)['date'] != $day)
            {
                $histories[] = [
                    'date' => $day,
                    'calories' => floatval($m['amount'])
                ];
            }
            else
            {
                end($histories)['calories'] += floatval($m['amount']);
            }
        }
        else
        {
            $histories[] = [
                'date' => $day,
                'calories' => floatval($m['amount'])
            ];
        }
    }

    return $histories;
}

function getWeightsForUser($email)
{
    $res = querySymbolic('weight_measurement', [
        'user' => $email
    ], 'order by date desc');

    $weights = extractArray($res);

    for($i = 0; $i < count($weights); $i++)
    {
        $ts = $weights[$i]['date'];

        $weights[$i]['date'] = date('Y-m-d H:i:s', strtotime($ts . ' UTC'));
        $weights[$i]['amount'] = floatval($weights[$i]['amount']);
    }

    return $weights;
}

function addWeightForUser($email, $amount)
{
    $amount = floatval($amount);
    $dayString = gmdate('Y-m-d H:i:s');

    $weight = [
        'date' => $dayString,
        'amount' => $amount,
        'user' => $email
    ];

    insertSymbolic('weight_measurement', $weight);
}

function getFrequentFoods($email)
{
    $res = querySymbolic([
        'frequently_eats',
        ['food', 'food', 'ndb_no']
    ],
    ['user' => $email, 'count' => ['>', 1]], 'order by count desc');
    return extractArray($res);
}

function updateFrequentFoods($email, $ndb_no)
{
    $res = querySymbolic('frequently_eats', ['user' => $email, 'food' => $ndb_no]);
    $ff = extractSingle($res);

    if(!$ff)
    {
        $ff = [
            'user' => $email,
            'food' => $ndb_no,
            'count' => 1
        ];

        insertSymbolic('frequently_eats', $ff);
    }
    else
    {
        updateSymbolic('frequently_eats', [
            'user' => $email,
            'food' => $ndb_no
        ],[
            'count' => intval($ff['count']) + 1
        ]);
    }
}

function getMeal($meal_id)
{
    $res = querySymbolic('meal', ['meal_id' => $meal_id]);
    $m = extractSingle($res);

    $m['date'] = date('Y-m-d H:i:s', strtotime($m['date'] . ' UTC'));

    return $m;
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

function findFoodByName($name)
{
    $name = str_replace("'", "&#39;", $name);
    $res = querySymbolic('food', ['name' => $name]);
    return extractSingle($res);
}

function createMeal($email)
{
    global $mysqli;

    $dayString = gmdate('Y-m-d H:i:s');

    insertSymbolic('meal', [
        'date' => $dayString,
        'amount' => 0,
        'user' => $email
    ]);

    $iid = $mysqli->insert_id;

    $res = querySymbolic('meal', [
        'meal_id' => $iid
    ]);


    $m = extractSingle($res);

    $m['date'] = date('Y-m-d H:i:s', strtotime($m['date'] . ' UTC'));

    return $m;
}

function dropMeal($meal_id, $email)
{
    deleteSymbolic('meal', [
        'meal_id' => $meal_id,
        'user' => $email
    ]);
}

function getFood($ndb_no)
{
    $res = querySymbolic('food', [
        'ndb_no' => $ndb_no
    ]);

    $food = extractSingle($res);

    if(!$food)
    {
        $food = extractFood($ndb_no);
        insertSymbolic('food', $food);
    }

    return $food;
}

function getFoodMetrics($ndb_no)
{
    $res = querySymbolic('food', [
        'ndb_no' => $ndb_no
    ]);

    $food = extractSingle($res);

    return extractMetrics($food);
}

function addFoodToMeal($meal_id, $ndb_no, $amount, $metric)
{
    $meal_id = intval($meal_id);
    $res = querySymbolic('food', [
        'ndb_no' => $ndb_no
    ]);

    $food = extractSingle($res);

    $cals = extractCalories($food, $metric, $amount);

    $food_report = [
        'metric' => $metric,
        'value' => $amount,
        'calories' => $cals,
        'meal'  => $meal_id,
        'food' => $ndb_no
    ];

    insertSymbolic('food_report', $food_report);

    $meal = extractSingle(querySymbolic('meal', ['meal_id' => $meal_id]));
    $meal['amount'] += $food_report['calories'];

    updateSymbolic('meal', ['meal_id' => $meal_id], ['amount' => $meal['amount']]);
}

function dropFoodFromMeal($food_id, $meal_id, $metric, $value)
{
    $res = querySymbolic('food_report', [
        'meal' => $meal_id,
        'food' => $food_id,
        'metric' => $metric,
        'value' => $value
    ], 'limit 1');

    $fr = extractSingle($res);

    deleteSymbolic('food_report', [
        'meal' => $meal_id,
        'food' => $food_id,
        'metric' => $metric,
        'value' => $value
    ], 'limit 1');

    $res = querySymbolic('meal', [
        'meal_id' => $meal_id
    ]);

    $meal = extractSingle($res);

    updateSymbolic('meal', [
        'meal_id' => $meal_id
    ], [
        'amount' => $meal['amount'] - $fr['calories']
    ]);
}

function getNutritionForMeal($meal_id)
{
    $res = querySymbolic([
        'meal',
        ['food_report', 'meal_id', 'meal'],
        ['food', 'food', 'ndb_no']
    ], ['meal_id' => $meal_id]);

    $minfo = extractArray($res);

    $scaleNutrition = function (&$nval, $nkey, $ss)
    {
        $nval *= $ss;
    };

    $nutrition = extractNutrition($minfo[0], $minfo[0]['metric']);
    array_walk($nutrition, $scaleNutrition, floatval($minfo[0]['value']));

    for($i = 1; $i < count($minfo); $i++)
    {
        $n = extractNutrition($minfo[$i], $minfo[$i]['metric']);
        array_walk($n, $scaleNutrition, floatval($minfo[$i]['value']));

        foreach($nutrition as $name => $v)
        {
            $nutrition[$name] = $v + $n[$name];
        }
    }

    $nutrition['calories'] = floatval($minfo[0]['amount']);

    return $nutrition;
}

?>
