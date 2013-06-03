<?php
require_once("settings.php");
?>
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
