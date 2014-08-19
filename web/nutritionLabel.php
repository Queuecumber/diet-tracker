<?php

$meal_id = $_POST['meal_id'];

$labelUrl = "http://www.rufenacht.com/webapps/shopncook/nutritionFacts.jsp";

$nutritionData = [
    'isShort' => false,
    'quantityServingSize' => 1,
    'unitServingSize' => 'gram',
    'gramServingSize' => 1,
    'standardUnit' => 'g',
    'calories' => 100,
    'totalFat' => 1,
    'saturatedFat' => 2,
    'transFat' => 3,
    'cholesterol' => 4,
    'sodium' => 5,
    'carb' => 6,
    'fiber' => 7,
    'sugar' => 8,
    'protein' => 9,
    'isPercent' => false,
    'vitA' => 11,
    'vitC' => 22,
    'calcium' => 33,
    'iron' => 44,
];

$qs = http_build_query($nutritionData);

$res = file_get_contents($labelUrl . '?' . $qs);

echo $res;

?>
