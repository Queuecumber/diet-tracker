<?php

// Native CSV parsing in PHP sucks and can't be used in real world scenarios. Borrowed this one from:
// http://us1.php.net/manual/en/function.str-getcsv.php#109948
function csvToArray($fileContent,$escape = '\\', $enclosure = '"', $delimiter = ',')
{
    $lines = array();
    $fields = array();

    if($escape == $enclosure)
    {
        $escape = '\\';
        $fileContent = str_replace(array('\\',$enclosure.$enclosure,"\r\n","\r"),
                    array('\\\\',$escape.$enclosure,"\\n","\\n"),$fileContent);
    }
    else
        $fileContent = str_replace(array("\r\n","\r"),array("\\n","\\n"),$fileContent);

    $nb = strlen($fileContent);
    $field = '';
    $inEnclosure = false;
    $previous = '';

    for($i = 0;$i<$nb; $i++)
    {
        $c = $fileContent[$i];
        if($c === $enclosure)
        {
            if($previous !== $escape)
                $inEnclosure ^= true;
            else
                $field .= $enclosure;
        }
        else if($c === $escape)
        {
            $next = $fileContent[$i+1];
            if($next != $enclosure && $next != $escape)
                $field .= $escape;
        }
        else if($c === $delimiter)
        {
            if($inEnclosure)
                $field .= $delimiter;
            else
            {
                //end of the field
                $fields[] = $field;
                $field = '';
            }
        }
        else if($c === "\n")
        {
            $fields[] = $field;
            $field = '';
            $lines[] = $fields;
            $fields = array();
        }
        else
            $field .= $c;
        $previous = $c;
    }
    //we add the last element
    if(true || $field !== '')
    {
        $fields[] = $field;
        $lines[] = $fields;
    }
    return $lines;
}


function extractSearch($search)
{
    $ndbUrl = "http://ndb.nal.usda.gov/ndb/foods";

    $encoded = urlencode($search);

    $queryString = $ndbUrl . "?qlookup=" . $encoded;

    $queryResults = file_get_contents($queryString);

    // Check to see if we had an exact match, if so we can get the food info directly
    $titleString = getTagContents($queryResults, 'title')[0];
    if($titleString == 'Foods List')
    {
        // Need to find the second <table> element
        $tableTags = getTagContents($queryResults, 'table');

        if(count($tableTags) >= 2) // No USDA matches found if there arent enough tables
        {
            $resultsTable = $tableTags[1];

            // Get the table body
            $tbody = getTagContents($resultsTable, 'tbody')[0];

            // Split into rows
            $rows = getTagContents($tbody, 'tr');

            // Make a food-like structure as the DB would return for each row
            $foods = [];
            foreach($rows as $row)
            {
                $foodDesc = [];

                $cells = getTagContents($row, 'td');

                $foodDesc['ndb_no'] = trim(strip_tags($cells[0]));
                $foodDesc['name'] = trim(strip_tags($cells[1]));

                $foods[] = $foodDesc;
            }

            return $foods;
        }
    }

    return false;
}

function extractFood($ndb_no)
{
    $findFoodByIdUrl = "http://ndb.nal.usda.gov/ndb/foods?qlookup=" . $ndb_no;

    $foodPage = file_get_contents($findFoodByIdUrl);

    // Extract the food name
    $header = getTagContentsById($foodPage, 'div', 'view-name')[0];
    $pieces = explode(",", strip_tags($header), 2);
    $name = trim($pieces[1]);

    $url = getTagAttributeByClass($foodPage, 'a', 'excel', 'href')[0];

    $url = str_replace("&amp;", "&", $url);

    $food = [
        'ndb_no' => intval($ndb_no),
        'name' => $name,
        'usda_report_url' => 'http://ndb.nal.usda.gov' . $url
    ];

    return $food;
}

function parseReport($reportString)
{
    $rows = csvToArray($reportString);
    return $rows;
}

function extractMetrics($food)
{
    $reportUrl = $food['usda_report_url'];

    $csvContent = file_get_contents($reportUrl);

    $csvArray = parseReport($csvContent);

    $metrics = [];
    for($i = 3; $i < count($csvArray[4]) - 1; $i++)
    {
        $metrics[] = trim(str_replace("\""," ",$csvArray[4][$i]));
    }

    return $metrics;
}

function extractCalories($food, $metric, $amount)
{
    $reportUrl = $food['usda_report_url'];
    $csvContent = file_get_contents($reportUrl);

    $csvArray = parseReport($csvContent);

    $colNo = 0;
    for($i = 3; $i < count($csvArray[4]) - 1; $i++)
    {
        $m = trim(str_replace("\""," ",$csvArray[4][$i]));

        if($metric == $m)
        {
            $colNo = $i;
            break;
        }
    }

    $rowNo = 0;
    for($i = 0; $i < count($csvArray); $i++)
    {
        if($csvArray[$i][0] == "Energy")
            $rowNo = $i;
    }

    $calPerServing = floatval($csvArray[$rowNo][$colNo]);

    return floatval($amount) * $calPerServing;
}

function extractNutrition($food, $metric)
{
    $allMetrics = extractMetrics($food);
    $metricCol = array_search($metric, $allMetrics) + 3;

    $reportUrl = $food['usda_report_url'];
    $csvContent = file_get_contents($reportUrl);

    $csvArray = parseReport($csvContent);

    $nutrition = [];
    for($i = 0; $i < count($csvArray); $i++)
    {
        switch($csvArray[$i][0])
        {
            case "Total lipid (fat)":
                $nutrition["totalFat"] = floatval($csvArray[$i][$metricCol]);
                break;

            case "Fatty acids, total saturated":
                $nutrition['saturatedFat'] = floatval($csvArray[$i][$metricCol]);
                break;

            case "Fatty acids, total trans":
                $nutrition["transFat"] = floatval($csvArray[$i][$metricCol]);
                break;

            case "Cholesterol":
                $nutrition["cholesterol"] = floatval($csvArray[$i][$metricCol]);
                break;

            case "Sodium, Na":
                $nutrition["sodium"] = floatval($csvArray[$i][$metricCol]);
                break;

            case "Carbohydrate, by difference":
                $nutrition["carb"] = floatval($csvArray[$i][$metricCol]);
                break;

            case "Fiber, total dietary":
                $nutrition["fiber"] = floatval($csvArray[$i][$metricCol]);
                break;

            case "Sugars, total":
                $nutrition["sugar"] = floatval($csvArray[$i][$metricCol]);
                break;

            case "Protein":
                $nutrition["protein"] = floatval($csvArray[$i][$metricCol]);
                break;

            case "Vitamin A, IU":
                $nutrition["vitA"] = floatval($csvArray[$i][$metricCol]);
                break;

            case "Vitamin C, total ascorbic acid":
                $nutrition["vitC"] = floatval($csvArray[$i][$metricCol]);
                break;

            case "Calcium, Ca":
                $nutrition["calcium"] = floatval($csvArray[$i][$metricCol]);
                break;

            case "Iron, Fe":
                $nutrition["iron"] = floatval($csvArray[$i][$metricCol]);
                break;
        }
    }

    $required = ["totalFat", "saturatedFat", "transFat", "cholesterol",
    "sodium", "carb", "fiber", "sugar", "protein", "vitA",
    "vitC", "calcium", "iron"];

    foreach($required as $rn)
    {
        if(!array_key_exists($rn, $nutrition))
        {
            $nutrition[$rn] = 0;
        }
    }

    return $nutrition;
}

?>
