<?php

/* This file contains code to 'print' text with a given color. */

function print_color($string, $color, $span)
{
    $type = "span";
    if (!$span) {
        $type = "div";
    }
    print(_get_color($color, $type) . $string . "</" . $type . ">");
}


function _get_color($color, $type)
{
    $colorBegin = "<" . $type . " style='color:";
    $colorEnd = "'>";
    switch($color) {
    case ColorEnum::RED:
        return $colorBegin . "#F62817" . $colorEnd;
    case ColorEnum::BLUE:
        return $colorBegin . "#82CAFA" . $colorEnd;
    case ColorEnum::YELLOW:
        return $colorBegin . "#FFFF00" . $colorEnd;
    case ColorEnum::GREEN:
        return $colorBegin . "#00FF00" . $colorEnd;
    case ColorEnum::WHITE:
        return $colorBegin . "#FFFFFF" . $colorEnd;
    case ColorEnum::PURPLE:
        return $colorBegin . "#C45AEC" . $colorEnd;
    default:
        die("Invalid color passed in");
    }
}

abstract class ColorEnum
{
    const RED = 0;
    const BLUE = 1;
    const YELLOW = 2;
    const GREEN = 3;
    const WHITE = 4;
    const PURPLE = 5;
}

?>