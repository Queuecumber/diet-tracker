<?php

function extractSearch($search)
{
    $ndbUrl = "http://ndb.nal.usda.gov/ndb/foods";

    $encoded = urlencode($search);

    $queryString = $ndbUrl . "?qlookup=" . $encoded;

    $queryResults = file_get_contents($queryString);

    // Need to find the second <table> element
    $tableTags = getTagContents($queryResults, 'table');
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

?>
