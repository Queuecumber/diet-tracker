<?php

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

        if(count($tableTags) > 2) // No USDA matches found
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
    $rows = str_getcsv($reportString, "\n");
    $csvArray = [];
    foreach($rows as $r)
    {
        $csvArray[] = str_getcsv($r);
    }

    return $csvArray;
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

?>
