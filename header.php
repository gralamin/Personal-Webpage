<?php
require_once("settings.php");
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
        <meta name="description" content="Test site">
        <meta name="keywords" content="HTML, PHP, Test">
        <title>Glen Nelson - Computer Scientist</title>
        <link href="style/main.css" rel="stylesheet" type="text/css">
        <link href="style/lightbox.css" rel="stylesheet" type="text/css">
        <link href='http://fonts.googleapis.com/css?family=Copse' rel='stylesheet' type='text/css'>
    </head>
    <body>
        <div id="top-warning">
            This page uses CSS3 that is not implemented in most browsers yet.
            This page may not display properly.
            <input type='button' value='[X]' />.
        </div>
        <div id="top"></div>
        <div id="navbar">
            <div id="inner-navbar-block"></div>
            <div id="inner-navbar" class="vertical-center">
                <div class="non-float-left name vertical-center">
<?php
    $path = Settings::path_from_root;
    $title = Settings::site_title;
    echo ('<a href="' . $path . '">' . $title . '</a>');
?>
                </div>
                <div class="non-float-right">
<?php
    echo ('<a href="' . $path . '?p=work">Work</a>');
?>
                </div>
                <div class="non-float-right seperator">
                    <span class="header-seperator">|</span>
                </div>
                <div class="non-float-right">
<?php
    echo ('<a href="' . $path . '?p=about">About</a>');
?>
                </div>
                <div class="non-float-right seperator">
                    <span class="header-seperator">|</span>
                </div>
                <div class="non-float-right">
<?php
    echo ('<a href="' . $path . '?p=contact">Contact</a>');
?>
                </div>
                <div class="non-float-right seperator">
                    <span class="header-seperator">|</span>
                </div>
                <div class="clearboth"></div>
            </div>
        </div>
