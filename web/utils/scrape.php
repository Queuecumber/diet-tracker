<?php

function getTagContentsById($html, $tag, $id)
{
    preg_match_all("/<" . $tag . ".*?id=\"" . $id . "\".*?>(.*?)<\/" . $tag . ">/si", $html, $match);
    return $match[1];
}

function getTagContentsByClass($html, $tag, $class)
{
    preg_match_all("/<" . $tag . ".*?class=\"" . $class . "\".*?>(.*?)<\/" . $tag . ">/si", $html, $match);
    return $match[1];
}

function getTagAttributeByClass($html, $tag, $class, $attribute)
{
    $regexAfter = "/<" . $tag . ".*?class=\"" . $class . "\".*?" . $attribute . "=\"(.*?)\".*?>.*?<\/" . $tag . ">/si";
    preg_match_all($regexAfter, $html, $matchA);
    $matchAfter = $matchA[1];

    $regexBefore = "/<" . $tag . ".*?" . $attribute . "=\"(.*?)\".*?class=\"" . $class . "\".*?>.*?<\/" . $tag . ">/si";
    preg_match_all($regexBefore, $html, $matchB);
    $matchBefore = $matchB[1];

    return array_unique(array_merge($matchAfter, $matchBefore));
}

function getTagContents($html, $tag)
{
    preg_match_all("/<" . $tag . ".*?>(.*?)<\/" . $tag . ">/is", $html, $match);
    return $match[1];
}

?>
