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

function composeJoins($desc)
{
    $initialTable = $desc[0];
    $clause = $initialTable;

    for($i = 1; $i < count($desc); $i++)
    {
        $clause = $clause . " inner join " . $desc[$i][0];

        if(count($desc[$i]) > 2)
        {
            $clause = $clause . " on " . $desc[$i][1] . '=' . $desc[$i][2];
        }
        else
        {
            $clause = $clause . " using (" . $desc[$i][1] . ")";
        }
    }

    return $clause;
}

function querySymbolic($tableName, $params)
{
    global $mysqli;

    if(is_array($tableName))
        $tableName = composeJoins($tableName);

    $query = "select * from " . $tableName . " where ";
    $op = '';
    foreach($params as $colname => $val)
    {
        if(is_string($val))
            $val = "'" . $val . "'";

        $clause = '';
        if(is_array($val))
        {
            $clause = $colname . ' ' . composeComplex($val);
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

function insertSymbolic($table, $values)
{
    global $mysqli;

    $query = "insert into " . $table . " ";
    $cols = '(';
    $vals = '(';

    $sep = '';
    foreach($values as $c => $v)
    {
        if(is_string($v))
            $v = "'" . $v . "'";

        $cols = $cols . $sep . $c;
        $vals = $vals . $sep . $v;

        $sep = ', ';
    }

    $cols = $cols . ')';
    $vals = $vals . ')';

    $query = $query . $cols . " values " . $vals;

    return $mysqli->query($query);
}

function updateSymbolic($table, $where, $values)
{
    global $mysqli;

    $query = "update " . $table . " set ";

    $sep = '';
    foreach($values as $c => $v)
    {
        if(is_string($v))
            $v = "'" . $v . "'";

        $query = $query . $sep . $c . '=' . $v;
        $sep = ',';
    }

    $query = $query . ' where ';

    $sep = '';
    foreach($where as $c => $v)
    {
        if(is_string($v))
            $v = "'" . $v . "'";

        $clause = '';
        if(is_array($v))
        {
            $clause = $c . ' ' . composeComplex($v);
        }
        else
        {
            $clause = $c . '=' . $v;
        }

        $query = $query . $sep . $clause;
        $sep = ' and ';
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

    return false;
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

    return false;
}

?>
