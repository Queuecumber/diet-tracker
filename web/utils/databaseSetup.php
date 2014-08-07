<?php

$dbHost = 'localhost';
$dbUser = 'mehrlich';
$dbPass = '';
$dbName = 'diet_tracker';

$mysqli = new mysqli($dbHost, $dbUser, $dbPass, $dbName);

function composeComplex($val)
{
    $op = $val[0];

    $res = '';

    switch($op)
    {
    case 'between':
        $op1 = $val[1];
        $op2 = $val[2];

        if(is_string($op1))
            $op1 = "'" . $op1 . "'";

        if(is_string($op2))
            $op2 = "'" . $op2 . "'";

            $res = 'between ' . $op1 . ' and ' . $op2;

        break;
    }

    return $res;
}

function querySymbolic($tableName, $params)
{
    global $mysqli;

    $query = "select * from " . $tableName . " where ";
    $op = '';
    foreach($params as $colname => $val)
    {
        if(is_string($val))
            $val = "'" . $val . "'";

        $clause = '';
        if(is_array($val))
        {
            $clause = composeComplex($val);
        }
        else
        {
            $clause = $colname . '=' . $val;
        }

        $query = $query . $op . $clause;
        $op = ' and ';
    }

    return $mysqli->query($query);
}

function extractSingle($res, $remove = [])
{
    if($res)
    {
        $nrows = $res->num_rows;

        if($nrows > 0)
        {
            $res->data_seek(0);
            $row = $res->fetch_assoc();

            foreach($remove as $col)
                unset($row[$col]);

            return $row;
        }
    }

    return $res;
}

function extractArray($res, $remove = [])
{
    if($res)
    {
        $arr = [];

        $nrows = $res->num_rows;
        for($i = 0; $i < $nrows; $i++)
        {
            $res->data_seek($i);
            $row = $res->fetch_assoc();

            foreach($remove as $col)
                unset($row[$col]);

            $arr[] = $row;
        }

        return $arr;
    }

    return $res;
}

?>
