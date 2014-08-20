<?php
include('utils/checkUser.php');
include('utils/model.php');

$meal_id = $_POST['meal_id'];

$nutrition = getNutritionForMeal($meal_id);

$labelUrl = "http://www.rufenacht.com/webapps/shopncook/nutritionFacts.jsp";

$nutritionData = [
    'isShort' => false,
    'quantityServingSize' => 1,
    'unitServingSize' => 'serving',
    'gramServingSize' => 1,
    'standardUnit' => 'g',
    'isPercent' => false
];

$nutritionData = array_merge($nutrition, $nutritionData);

$qs = http_build_query($nutritionData);

$res = file_get_contents($labelUrl . '?' . $qs);

echo $res;

?>
