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
    $findTagWithClassRegex = "/<" . $tag . "[^>]+?class=\"" . $class . "\".*?>/si";
    preg_match_all($findTagWithClassRegex, $html, $matchTags);

    $extractAttributeNameRegex = "/" . $attribute . "=\"(.*?)\"/si";
    $attrMatches = [];
    foreach($matchTags[0] as $m)
    {
        preg_match_all($extractAttributeNameRegex, $m, $matchAttr);
        $attrMatches = array_merge($attrMatches, $matchAttr[1]);
    }

    return $attrMatches;
}

function getTagContents($html, $tag)
{
    preg_match_all("/<" . $tag . ".*?>(.*?)<\/" . $tag . ">/is", $html, $match);
    return $match[1];
}

?>
